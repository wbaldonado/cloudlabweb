import csv
import io
from datetime import datetime

from crm.extensions import db
from crm.models import Company, Contact

MAX_ROWS = 5000


def parse_csv(file_storage) -> list[dict] | None:
    raw = file_storage.read()
    for encoding in ('utf-8-sig', 'utf-8', 'latin-1'):
        try:
            text = raw.decode(encoding)
            break
        except UnicodeDecodeError:
            continue
    else:
        return None

    delimiter = ';' if text.count(';') > text.count(',') else ','
    reader = csv.DictReader(io.StringIO(text), delimiter=delimiter)
    rows = [dict(r) for r in reader if any(v and str(v).strip() for v in r.values())]
    return rows


def import_companies(rows: list[dict], with_contacts: bool, owner_id: int) -> dict:
    created = updated = skipped = 0
    for row in rows:
        name = (row.get('name') or '').strip()
        if not name:
            skipped += 1
            continue
        company = Company.query.filter_by(name=name).first()
        if not company:
            company = Company(name=name)
            created += 1
        else:
            updated += 1
        for field in ('industry', 'website', 'phone', 'address', 'city', 'state', 'country', 'zip_code', 'description'):
            if row.get(field):
                setattr(company, field, row[field].strip())
        db.session.add(company)
        db.session.flush()

        if with_contacts and row.get('contact_email'):
            email = row['contact_email'].strip()
            contact = Contact.query.filter_by(email=email).first()
            if not contact:
                contact = Contact(
                    first_name=row.get('contact_first_name', 'Contacto').strip() or 'Contacto',
                    last_name=row.get('contact_last_name', '').strip() or 'Importado',
                    email=email,
                    lifecycle_stage='lead',
                    company_id=company.id,
                    owner_id=owner_id,
                    captured_at=datetime.utcnow(),
                )
                db.session.add(contact)
    db.session.commit()
    return {'created': created, 'updated': updated, 'skipped': skipped}


def import_contacts(rows: list[dict], owner_id: int) -> dict:
    created = skipped = 0
    for row in rows:
        first = (row.get('first_name') or '').strip()
        last = (row.get('last_name') or '').strip()
        if not first or not last:
            skipped += 1
            continue
        email = (row.get('email') or '').strip() or None
        if email and Contact.query.filter_by(email=email).first():
            skipped += 1
            continue
        company_id = None
        cname = (row.get('company_name') or '').strip()
        if cname:
            company = Company.query.filter_by(name=cname).first()
            if not company:
                company = Company(name=cname)
                db.session.add(company)
                db.session.flush()
            company_id = company.id
        stage = (row.get('lifecycle_stage') or 'lead').strip()
        if stage not in ('lead', 'mql', 'sql', 'customer'):
            stage = 'lead'
        contact = Contact(
            first_name=first,
            last_name=last,
            email=email,
            phone=(row.get('phone') or '').strip() or None,
            job_title=(row.get('job_title') or '').strip() or None,
            lifecycle_stage=stage,
            lead_source=(row.get('lead_source') or '').strip() or None,
            company_id=company_id,
            owner_id=owner_id,
            notes=(row.get('notes') or '').strip() or None,
            captured_at=datetime.utcnow() if stage == 'lead' else None,
        )
        db.session.add(contact)
        created += 1
    db.session.commit()
    return {'created': created, 'skipped': skipped}
