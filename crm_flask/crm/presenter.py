from flask import url_for

from crm.models import Activity, Company, Contact, Deal


STAGE_LABELS = {
    'lead': ('Nuevo', 'crm-badge--new'),
    'mql': ('Contactado', 'crm-badge--contacted'),
    'sql': ('Calificado', 'crm-badge--qualified'),
    'customer': ('Cliente', 'crm-badge--customer'),
}

DEAL_STAGES = {
    'prospecting': ('Prospección', 'crm-badge--prospecting'),
    'qualified': ('Calificación', 'crm-badge--qualified'),
    'proposal': ('Propuesta', 'crm-badge--proposal'),
    'negotiation': ('Negociación', 'crm-badge--negotiation'),
    'won': ('Ganada', 'crm-badge--won'),
    'lost': ('Perdida', 'crm-badge--lost'),
}

ACTIVITY_TYPES = {'email': 'Correo', 'meeting': 'Reunión', 'call': 'Llamada', 'task': 'Tarea'}
ACTIVITY_STATUS = {
    'not_started': 'No iniciada',
    'in_progress': 'En progreso',
    'blocked': 'Bloqueada',
    'completed': 'Completada',
}


def contact_dict(c: Contact) -> dict:
    label, css = STAGE_LABELS.get(c.lifecycle_stage or 'lead', ('', 'crm-badge--new'))
    return {
        'id': c.id,
        'first_name': c.first_name,
        'last_name': c.last_name,
        'full_name': c.full_name,
        'email': c.email,
        'phone': c.phone,
        'job_title': c.job_title,
        'lifecycle_stage': c.lifecycle_stage,
        'company_id': c.company_id,
        'company': c.company.name if c.company else None,
        'owner_id': c.owner_id,
        'notes': c.notes,
        'stage_label': label,
        'stage_class': css,
        'update_url': url_for('api.update_contact', contact_id=c.id),
        'destroy_url': url_for('api.destroy_contact', contact_id=c.id),
        'send_email_url': url_for('api.send_contact_email', contact_id=c.id) if c.email else None,
    }


def company_dict(co: Company) -> dict:
    meta = f"{co.industry or 'Sin industria'} · {co.country or '—'}"
    return {
        'id': co.id,
        'name': co.name,
        'industry': co.industry,
        'website': co.website,
        'phone': co.phone,
        'address': co.address,
        'city': co.city,
        'state': co.state,
        'country': co.country,
        'zip_code': co.zip_code,
        'description': co.description,
        'meta': meta.strip(' ·'),
        'update_url': url_for('api.update_company', company_id=co.id),
        'destroy_url': url_for('api.destroy_company', company_id=co.id),
    }


def deal_dict(d: Deal) -> dict:
    label, css = DEAL_STAGES.get(d.stage or 'prospecting', ('', ''))
    return {
        'id': d.id,
        'title': d.title,
        'amount': float(d.amount or 0),
        'stage': d.stage,
        'contact_id': d.contact_id,
        'company_id': d.company_id,
        'owner_id': d.owner_id,
        'expected_close_date': d.expected_close_date.isoformat() if d.expected_close_date else None,
        'description': d.description,
        'probability': int(d.probability or 0),
        'stage_label': label,
        'stage_class': css,
        'company': d.company.name if d.company else None,
        'contact': d.contact.full_name if d.contact else None,
        'weighted': d.weighted_value(),
        'update_url': url_for('api.update_deal', deal_id=d.id),
        'destroy_url': url_for('api.destroy_deal', deal_id=d.id),
    }


def activity_dict(a: Activity) -> dict:
    return {
        'id': a.id,
        'title': a.title,
        'type': a.type,
        'status': a.status,
        'due_date': a.due_date.isoformat() if a.due_date else None,
        'contact_id': a.contact_id,
        'company_id': a.company_id,
        'deal_id': a.deal_id,
        'owner_id': a.owner_id,
        'notes': a.notes,
        'type_label': ACTIVITY_TYPES.get(a.type, 'Tarea'),
        'status_label': ACTIVITY_STATUS.get(a.status, ''),
        'meta': (a.company.name if a.company else None) or (a.contact.full_name if a.contact else '—'),
        'update_url': url_for('api.update_activity', activity_id=a.id),
        'destroy_url': url_for('api.destroy_activity', activity_id=a.id),
    }
