"""IMAP inbox polling — same logic as Laravel CrmImapInbox (free, no API)."""

import imaplib
import email
import re
from datetime import datetime
from email.header import decode_header

from flask import current_app

from crm.extensions import db
from crm.models import Activity, Contact, EmailMessage

def fetch_inbox() -> int:
    if not current_app.config.get('CRM_IMAP_ENABLED'):
        return 0

    cfg = current_app.config
    host = cfg['CRM_IMAP_HOST']
    port = cfg['CRM_IMAP_PORT']
    folder = cfg.get('CRM_IMAP_FOLDER', 'INBOX')
    mail = imaplib.IMAP4_SSL(host, port)
    mail.login(cfg['CRM_IMAP_USERNAME'], cfg['CRM_IMAP_PASSWORD'])
    mail.select(folder)
    _, data = mail.search(None, 'UNSEEN')
    processed = 0

    for num in data[0].split():
        _, msg_data = mail.fetch(num, '(RFC822)')
        raw = msg_data[0][1]
        msg = email.message_from_bytes(raw)
        uid = num.decode()
        imap_uid = f'{folder}:{uid}'

        if EmailMessage.query.filter_by(imap_uid=imap_uid).first():
            continue

        from_email = _extract_email(msg.get('From', ''))
        subject = _decode_header(msg.get('Subject', ''))
        in_reply_to = (msg.get('In-Reply-To') or '').strip()
        message_id = (msg.get('Message-ID') or '').strip()
        body = _get_body(msg)

        own = {cfg['MAIL_FROM_ADDRESS'].lower(), (cfg.get('CRM_IMAP_USERNAME') or '').lower()}
        if from_email in own:
            mail.store(num, '+FLAGS', '\\Seen')
            continue

        contact = _resolve_contact(from_email, in_reply_to)
        if not contact:
            continue

        inbound = EmailMessage(
            contact_id=contact.id,
            direction='inbound',
            subject=subject[:255],
            body=body[:20000],
            from_email=from_email,
            to_email=cfg['MAIL_FROM_ADDRESS'],
            message_id=message_id or None,
            in_reply_to=in_reply_to or None,
            imap_uid=imap_uid,
            sent_at=datetime.utcnow(),
        )
        db.session.add(inbound)
        db.session.add(Activity(
            title=f'Respuesta: {subject[:100]}',
            type='email',
            status='not_started',
            contact_id=contact.id,
            company_id=contact.company_id,
            notes=body[:1000],
            due_date=datetime.utcnow().date(),
        ))
        db.session.commit()
        _notify_admin(inbound, contact)
        mail.store(num, '+FLAGS', '\\Seen')
        processed += 1

    mail.logout()
    return processed


def _notify_admin(inbound: EmailMessage, contact: Contact):
    import smtplib
    from email.mime.text import MIMEText

    cfg = current_app.config
    notify = cfg.get('CRM_NOTIFY_EMAIL')
    if not notify:
        return
    body = (
        f"Nueva respuesta CRM\n\n"
        f"Contacto: {contact.full_name}\n"
        f"Email: {inbound.from_email}\n"
        f"Asunto: {inbound.subject}\n\n"
        f"{inbound.body[:2000]}"
    )
    msg = MIMEText(body, 'plain', 'utf-8')
    msg['Subject'] = f'[CRM] Respuesta de {contact.full_name}'
    msg['From'] = cfg['MAIL_FROM_ADDRESS']
    msg['To'] = notify
    with smtplib.SMTP(cfg['MAIL_HOST'], cfg['MAIL_PORT']) as server:
        if cfg.get('MAIL_USE_TLS'):
            server.starttls()
        if cfg.get('MAIL_USERNAME'):
            server.login(cfg['MAIL_USERNAME'], cfg['MAIL_PASSWORD'])
        server.sendmail(cfg['MAIL_FROM_ADDRESS'], [notify], msg.as_string())


def _resolve_contact(from_email: str, in_reply_to: str | None) -> Contact | None:
    if in_reply_to:
        parent = EmailMessage.query.filter(
            (EmailMessage.message_id == in_reply_to)
            | (EmailMessage.message_id == in_reply_to.strip('<>'))
        ).first()
        if parent and parent.contact:
            return parent.contact
    return Contact.query.filter_by(email=from_email).first()


def _extract_email(raw: str) -> str:
    m = re.search(r'<([^>]+)>', raw)
    return (m.group(1) if m else raw).strip().lower()


def _decode_header(value: str) -> str:
    parts = decode_header(value)
    out = []
    for text, enc in parts:
        if isinstance(text, bytes):
            out.append(text.decode(enc or 'utf-8', errors='replace'))
        else:
            out.append(text)
    return ''.join(out)


def _get_body(msg) -> str:
    if msg.is_multipart():
        for part in msg.walk():
            if part.get_content_type() == 'text/plain':
                payload = part.get_payload(decode=True)
                if payload:
                    return payload.decode(part.get_content_charset() or 'utf-8', errors='replace')
        for part in msg.walk():
            if part.get_content_type() == 'text/html':
                payload = part.get_payload(decode=True)
                if payload:
                    return payload.decode(part.get_content_charset() or 'utf-8', errors='replace')
    payload = msg.get_payload(decode=True)
    if payload:
        return payload.decode(msg.get_content_charset() or 'utf-8', errors='replace')
    return ''
