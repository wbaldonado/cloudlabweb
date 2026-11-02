import smtplib
import uuid
from datetime import datetime
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

from flask import current_app

from crm.extensions import db
from crm.models import Activity, Contact, EmailMessage


def make_message_id() -> str:
    domain = current_app.config['CRM_MESSAGE_ID_DOMAIN']
    return f'<crm-{uuid.uuid4()}@{domain}>'


def send_contact_email(contact: Contact, user_id: int, subject: str, body: str) -> EmailMessage:
    cfg = current_app.config
    msg = MIMEMultipart('alternative')
    msg['Subject'] = subject
    msg['From'] = f"{cfg['MAIL_FROM_NAME']} <{cfg['MAIL_FROM_ADDRESS']}>"
    msg['To'] = contact.email
    message_id = make_message_id()
    msg['Message-ID'] = message_id
    msg.attach(MIMEText(body.replace('\n', '<br>'), 'html', 'utf-8'))
    msg.attach(MIMEText(body, 'plain', 'utf-8'))

    with smtplib.SMTP(cfg['MAIL_HOST'], cfg['MAIL_PORT']) as server:
        if cfg.get('MAIL_USE_TLS'):
            server.starttls()
        if cfg.get('MAIL_USERNAME') and cfg.get('MAIL_PASSWORD'):
            server.login(cfg['MAIL_USERNAME'], cfg['MAIL_PASSWORD'])
        server.sendmail(cfg['MAIL_FROM_ADDRESS'], [contact.email], msg.as_string())

    record = EmailMessage(
        contact_id=contact.id,
        user_id=user_id,
        direction='outbound',
        subject=subject,
        body=body,
        from_email=cfg['MAIL_FROM_ADDRESS'],
        to_email=contact.email,
        message_id=message_id,
        sent_at=datetime.utcnow(),
    )
    db.session.add(record)
    db.session.add(Activity(
        title=f'Email enviado: {subject[:100]}',
        type='email',
        status='completed',
        contact_id=contact.id,
        company_id=contact.company_id,
        owner_id=user_id,
        notes=body[:1000],
        due_date=datetime.utcnow().date(),
    ))
    db.session.commit()
    return record
