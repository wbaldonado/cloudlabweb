from datetime import datetime

from flask import Blueprint, jsonify, request, send_file
from flask_login import current_user
import io

from crm.auth_utils import admin_required, validation_error, wants_json
from crm.extensions import db
from crm.models import Activity, Company, Contact, Deal
from crm.presenter import activity_dict, company_dict, contact_dict, deal_dict
from crm.services.csv_importer import import_companies, import_contacts, parse_csv, MAX_ROWS
from crm.services.email_service import send_contact_email as dispatch_email

api_bp = Blueprint('api', __name__)


def _json_ok(message: str, **data):
    return jsonify({'success': True, 'message': message, **data})


# --- Companies ---
@api_bp.route('/companies', methods=['POST'])
@admin_required
def store_company():
    data = request.form
    name = (data.get('name') or '').strip()
    if not name:
        return validation_error({'name': ['Nombre requerido.']})
    company = Company(name=name, **{f: data.get(f) or None for f in (
        'industry', 'website', 'phone', 'address', 'city', 'state', 'country', 'zip_code', 'description'
    )})
    db.session.add(company)
    db.session.commit()
    return _json_ok('Empresa creada correctamente.', company=company_dict(company))


@api_bp.route('/companies/<int:company_id>', methods=['POST'])
@admin_required
def update_company(company_id):
    company = db.session.get(Company, company_id) or abort404()
    if request.form.get('_method') == 'PUT' or request.method == 'PUT':
        data = request.form
        company.name = (data.get('name') or company.name).strip()
        for f in ('industry', 'website', 'phone', 'address', 'city', 'state', 'country', 'zip_code', 'description'):
            if f in data:
                setattr(company, f, data.get(f) or None)
        db.session.commit()
        return _json_ok('Empresa actualizada correctamente.', company=company_dict(company))
    if request.form.get('_method') == 'DELETE':
        return destroy_company(company_id)
    return validation_error({'_method': ['Método no válido.']})


@api_bp.route('/companies/<int:company_id>', methods=['DELETE'])
@admin_required
def destroy_company(company_id):
    company = db.session.get(Company, company_id) or abort404()
    db.session.delete(company)
    db.session.commit()
    return _json_ok('Empresa eliminada.')


# --- Contacts ---
@api_bp.route('/contacts', methods=['POST'])
@admin_required
def store_contact():
    data = request.form
    err = _validate_contact(data)
    if err:
        return validation_error(err)
    contact = Contact(
        first_name=data['first_name'].strip(),
        last_name=data['last_name'].strip(),
        email=data.get('email') or None,
        phone=data.get('phone') or None,
        job_title=data.get('job_title') or None,
        lifecycle_stage=data.get('lifecycle_stage', 'lead'),
        company_id=data.get('company_id') or None,
        owner_id=data.get('owner_id') or None,
        notes=data.get('notes') or None,
        captured_at=datetime.utcnow() if data.get('lifecycle_stage', 'lead') == 'lead' else None,
    )
    db.session.add(contact)
    db.session.commit()
    return _json_ok('Contacto creado correctamente.', contact=contact_dict(contact))


@api_bp.route('/contacts/<int:contact_id>', methods=['POST'])
@admin_required
def update_contact(contact_id):
    contact = db.session.get(Contact, contact_id) or abort404()
    if request.form.get('_method') == 'DELETE':
        return destroy_contact(contact_id)
    if request.form.get('_method') != 'PUT':
        return validation_error({'_method': ['Use PUT.']})
    data = request.form
    err = _validate_contact(data)
    if err:
        return validation_error(err)
    for f in ('first_name', 'last_name', 'email', 'phone', 'job_title', 'lifecycle_stage', 'company_id', 'owner_id', 'notes'):
        if f in data:
            setattr(contact, f, data.get(f) or None)
    db.session.commit()
    return _json_ok('Contacto actualizado correctamente.', contact=contact_dict(contact))


@api_bp.route('/contacts/<int:contact_id>', methods=['DELETE'])
@admin_required
def destroy_contact(contact_id):
    contact = db.session.get(Contact, contact_id) or abort404()
    db.session.delete(contact)
    db.session.commit()
    return _json_ok('Contacto eliminado.')


@api_bp.route('/contacts/<int:contact_id>/email', methods=['POST'])
@admin_required
def send_contact_email(contact_id):
    contact = db.session.get(Contact, contact_id) or abort404()
    if not contact.email:
        return jsonify({'success': False, 'message': 'Este contacto no tiene email.'}), 422
    subject = (request.form.get('subject') or '').strip()
    body = (request.form.get('body') or '').strip()
    if not subject or not body:
        return validation_error({'subject': ['Requerido.'], 'body': ['Requerido.']})
    try:
        dispatch_email(contact, current_user.id, subject, body)
    except Exception as e:
        return jsonify({'success': False, 'message': f'Error SMTP: {e}'}), 500
    return _json_ok(f'Correo enviado a {contact.email}')


# --- Deals ---
@api_bp.route('/deals', methods=['POST'])
@admin_required
def store_deal():
    data = request.form
    title = (data.get('title') or '').strip()
    if not title:
        return validation_error({'title': ['Título requerido.']})
    deal = Deal(
        title=title,
        amount=data.get('amount') or 0,
        stage=data.get('stage', 'prospecting'),
        probability=data.get('probability') or 0,
        company_id=data.get('company_id') or None,
        contact_id=data.get('contact_id') or None,
        description=data.get('description') or None,
        expected_close_date=data.get('expected_close_date') or None,
    )
    db.session.add(deal)
    db.session.commit()
    return _json_ok('Oportunidad creada correctamente.', deal=deal_dict(deal))


@api_bp.route('/deals/<int:deal_id>', methods=['POST'])
@admin_required
def update_deal(deal_id):
    deal = db.session.get(Deal, deal_id) or abort404()
    if request.form.get('_method') == 'DELETE':
        return destroy_deal(deal_id)
    if request.form.get('_method') != 'PUT':
        return validation_error({'_method': ['Use PUT.']})
    data = request.form
    deal.title = (data.get('title') or deal.title).strip()
    for f in ('amount', 'stage', 'probability', 'company_id', 'contact_id', 'description', 'expected_close_date'):
        if f in data:
            setattr(deal, f, data.get(f) or None)
    db.session.commit()
    return _json_ok('Oportunidad actualizada correctamente.', deal=deal_dict(deal))


@api_bp.route('/deals/<int:deal_id>', methods=['DELETE'])
@admin_required
def destroy_deal(deal_id):
    deal = db.session.get(Deal, deal_id) or abort404()
    db.session.delete(deal)
    db.session.commit()
    return _json_ok('Oportunidad eliminada.')


# --- Activities ---
@api_bp.route('/activities', methods=['POST'])
@admin_required
def store_activity():
    data = request.form
    title = (data.get('title') or '').strip()
    if not title:
        return validation_error({'title': ['Título requerido.']})
    activity = Activity(
        title=title,
        type=data.get('type', 'task'),
        status=data.get('status', 'not_started'),
        due_date=data.get('due_date') or None,
        company_id=data.get('company_id') or None,
        contact_id=data.get('contact_id') or None,
        notes=data.get('notes') or None,
    )
    db.session.add(activity)
    db.session.commit()
    return _json_ok('Actividad creada correctamente.', activity=activity_dict(activity))


@api_bp.route('/activities/<int:activity_id>', methods=['POST'])
@admin_required
def update_activity(activity_id):
    activity = db.session.get(Activity, activity_id) or abort404()
    if request.form.get('_method') == 'DELETE':
        return destroy_activity(activity_id)
    if request.form.get('_method') != 'PUT':
        return validation_error({'_method': ['Use PUT.']})
    data = request.form
    for f in ('title', 'type', 'status', 'due_date', 'company_id', 'contact_id', 'notes'):
        if f in data:
            setattr(activity, f, data.get(f) or None)
    db.session.commit()
    return _json_ok('Actividad actualizada correctamente.', activity=activity_dict(activity))


@api_bp.route('/activities/<int:activity_id>', methods=['DELETE'])
@admin_required
def destroy_activity(activity_id):
    activity = db.session.get(Activity, activity_id) or abort404()
    db.session.delete(activity)
    db.session.commit()
    return _json_ok('Actividad eliminada.')


# --- CSV Import ---
@api_bp.route('/import', methods=['POST'])
@admin_required
def import_csv():
    itype = request.form.get('type')
    if itype not in ('companies', 'contacts'):
        return validation_error({'type': ['Tipo inválido.']})
    f = request.files.get('csv_file')
    if not f:
        return validation_error({'csv_file': ['Archivo requerido.']})
    rows = parse_csv(f)
    if rows is None:
        return jsonify({'success': False, 'message': 'No se pudo leer el CSV.'}), 422
    if len(rows) > MAX_ROWS:
        return jsonify({'success': False, 'message': f'Máximo {MAX_ROWS} filas.'}), 422
    if itype == 'companies':
        result = import_companies(rows, request.form.get('with_contacts') == '1', current_user.id)
    else:
        result = import_contacts(rows, current_user.id)
    return _json_ok(f"Importación completada: {result}")


@api_bp.route('/import/template/<type>')
@admin_required
def import_template(type):
    if type == 'companies':
        content = 'name,industry,website,phone,address,city,state,country,zip_code,description\nAcme Corp,Technology,https://acme.com,+57 300 000 0000,Calle 100,Bogota,Cundinamarca,Colombia,110111,Cliente potencial\n'
        name = 'crm-empresas-plantilla.csv'
    elif type == 'contacts':
        content = 'first_name,last_name,email,phone,job_title,lifecycle_stage,lead_source,company_name,notes\nJuan,Perez,juan@acme.com,+57 300 111 2233,CEO,lead,referral,Acme Corp,Importado\n'
        name = 'crm-contactos-plantilla.csv'
    else:
        return abort404()
    return send_file(io.BytesIO(content.encode('utf-8-sig')), mimetype='text/csv', as_attachment=True, download_name=name)


def _validate_contact(data) -> dict | None:
    errors = {}
    if not (data.get('first_name') or '').strip():
        errors['first_name'] = ['Nombre requerido.']
    if not (data.get('last_name') or '').strip():
        errors['last_name'] = ['Apellido requerido.']
    stage = data.get('lifecycle_stage', 'lead')
    if stage not in ('lead', 'mql', 'sql', 'customer'):
        errors['lifecycle_stage'] = ['Etapa inválida.']
    return errors or None


def abort404():
    from flask import abort
    abort(404)
