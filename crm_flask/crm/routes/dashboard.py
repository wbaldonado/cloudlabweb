from flask import Blueprint, render_template, request
from flask_login import current_user
from sqlalchemy import or_

from crm.auth_utils import admin_required
from crm.extensions import db
from crm.models import (
    Activity, Company, Contact, Deal, EmailMessage, User, pipeline_stats,
)
from crm.presenter import activity_dict, company_dict, contact_dict, deal_dict

dashboard_bp = Blueprint('dashboard', __name__)


@dashboard_bp.route('/')
@admin_required
def index():
    if not _tables_ready():
        return render_template('setup.html', missing=_missing_tables())

    search = request.args.get('q', '').strip()
    stage = request.args.get('stage', '').strip()
    q = Contact.query
    if search:
        term = f'%{search}%'
        q = q.outerjoin(Company).filter(
            or_(
                Contact.first_name.like(term),
                Contact.last_name.like(term),
                Contact.email.like(term),
                Contact.phone.like(term),
                Contact.job_title.like(term),
                Company.name.like(term),
            )
        )
    if stage:
        q = q.filter(Contact.lifecycle_stage == stage)

    contacts = q.order_by(Contact.created_at.desc()).paginate(page=request.args.get('page', 1, type=int), per_page=10)
    pipe = pipeline_stats()
    open_stages = ['prospecting', 'qualified', 'proposal', 'negotiation']
    open_deals = Deal.query.filter(Deal.stage.in_(open_stages)).all()

    stats = {
        'contacts': Contact.query.count(),
        'companies': Company.query.count(),
        'pending_activities': Activity.query.filter(Activity.status != 'completed').count(),
        'email_replies': EmailMessage.query.filter_by(direction='inbound').filter(EmailMessage.read_at.is_(None)).count(),
        **pipe,
    }

    return render_template(
        'dashboard.html',
        stats=stats,
        contacts=contacts,
        companies=Company.query.order_by(Company.name).all(),
        deals=Deal.query.order_by(Deal.updated_at.desc()).all(),
        open_deals=open_deals,
        activities=Activity.query.order_by(Activity.due_date).all(),
        companies_list=Company.query.order_by(Company.name).all(),
        contacts_list=Contact.query.order_by(Contact.first_name).all(),
        users=User.query.order_by(User.name).all(),
        recent_replies=EmailMessage.query.filter_by(direction='inbound').order_by(EmailMessage.created_at.desc()).limit(5).all(),
        search=search,
        stage_filter=stage,
        user=current_user,
        contact_dict=contact_dict,
        company_dict=company_dict,
        deal_dict=deal_dict,
        activity_dict=activity_dict,
    )


def _tables_ready() -> bool:
    return len(_missing_tables()) == 0


def _missing_tables() -> list:
    from sqlalchemy import inspect
    insp = inspect(db.engine)
    required = ['crm_companies', 'crm_contacts', 'crm_deals']
    return [t for t in required if t not in insp.get_table_names()]
