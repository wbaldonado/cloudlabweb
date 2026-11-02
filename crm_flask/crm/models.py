from datetime import datetime, date
from decimal import Decimal

from flask_login import UserMixin
from sqlalchemy import func

from crm.extensions import db


class User(UserMixin, db.Model):
    __tablename__ = 'users'

    id = db.Column(db.BigInteger, primary_key=True)
    name = db.Column(db.String(255), nullable=False)
    email = db.Column(db.String(255), unique=True, nullable=False)
    password = db.Column(db.String(255), nullable=False)
    is_admin = db.Column(db.Boolean, default=False)
    created_at = db.Column(db.DateTime)
    updated_at = db.Column(db.DateTime)

    def is_admin_user(self) -> bool:
        return bool(self.is_admin)


class Company(db.Model):
    __tablename__ = 'crm_companies'

    id = db.Column(db.BigInteger, primary_key=True)
    name = db.Column(db.String(255), nullable=False)
    industry = db.Column(db.String(255))
    website = db.Column(db.String(255))
    description = db.Column(db.Text)
    phone = db.Column(db.String(255))
    address = db.Column(db.String(255))
    city = db.Column(db.String(255))
    state = db.Column(db.String(255))
    country = db.Column(db.String(255))
    zip_code = db.Column(db.String(255))
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    contacts = db.relationship('Contact', backref='company', lazy='dynamic')
    deals = db.relationship('Deal', backref='company', lazy='dynamic')


class Contact(db.Model):
    __tablename__ = 'crm_contacts'

    id = db.Column(db.BigInteger, primary_key=True)
    first_name = db.Column(db.String(255), nullable=False)
    last_name = db.Column(db.String(255), nullable=False)
    email = db.Column(db.String(255))
    phone = db.Column(db.String(255))
    job_title = db.Column(db.String(255))
    lifecycle_stage = db.Column(db.Enum('lead', 'mql', 'sql', 'customer'), default='lead')
    lead_source = db.Column(db.String(255))
    lead_score = db.Column(db.Integer, default=0)
    capture_method = db.Column(db.String(255))
    owner_id = db.Column(db.BigInteger, db.ForeignKey('users.id'))
    assigned_to = db.Column(db.BigInteger, db.ForeignKey('users.id'))
    company_id = db.Column(db.BigInteger, db.ForeignKey('crm_companies.id'))
    notes = db.Column(db.Text)
    captured_at = db.Column(db.DateTime)
    converted_at = db.Column(db.DateTime)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    owner = db.relationship('User', foreign_keys=[owner_id])
    deals = db.relationship('Deal', backref='contact', lazy='dynamic')

    @property
    def full_name(self) -> str:
        return f'{self.first_name} {self.last_name}'.strip()


class Deal(db.Model):
    __tablename__ = 'crm_deals'

    id = db.Column(db.BigInteger, primary_key=True)
    title = db.Column(db.String(255), nullable=False)
    amount = db.Column(db.Numeric(15, 2), default=0)
    stage = db.Column(
        db.Enum('prospecting', 'qualified', 'proposal', 'negotiation', 'won', 'lost'),
        default='prospecting',
    )
    contact_id = db.Column(db.BigInteger, db.ForeignKey('crm_contacts.id'))
    company_id = db.Column(db.BigInteger, db.ForeignKey('crm_companies.id'))
    owner_id = db.Column(db.BigInteger, db.ForeignKey('users.id'))
    expected_close_date = db.Column(db.Date)
    description = db.Column(db.Text)
    probability = db.Column(db.Integer, default=0)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    owner = db.relationship('User', foreign_keys=[owner_id])

    def weighted_value(self) -> float:
        return float(self.amount or 0) * (int(self.probability or 0) / 100)


class Activity(db.Model):
    __tablename__ = 'crm_activities'

    id = db.Column(db.BigInteger, primary_key=True)
    title = db.Column(db.String(255), nullable=False)
    type = db.Column(db.Enum('email', 'meeting', 'task', 'call'), default='task')
    status = db.Column(
        db.Enum('not_started', 'in_progress', 'blocked', 'completed'),
        default='not_started',
    )
    due_date = db.Column(db.Date)
    contact_id = db.Column(db.BigInteger, db.ForeignKey('crm_contacts.id'))
    company_id = db.Column(db.BigInteger, db.ForeignKey('crm_companies.id'))
    deal_id = db.Column(db.BigInteger, db.ForeignKey('crm_deals.id'))
    owner_id = db.Column(db.BigInteger, db.ForeignKey('users.id'))
    notes = db.Column(db.Text)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    contact = db.relationship('Contact', foreign_keys=[contact_id])
    company = db.relationship('Company', foreign_keys=[company_id])


class EmailMessage(db.Model):
    __tablename__ = 'crm_email_messages'

    id = db.Column(db.BigInteger, primary_key=True)
    contact_id = db.Column(db.BigInteger, db.ForeignKey('crm_contacts.id'))
    user_id = db.Column(db.BigInteger, db.ForeignKey('users.id'))
    direction = db.Column(db.Enum('outbound', 'inbound'), nullable=False)
    subject = db.Column(db.String(255), nullable=False)
    body = db.Column(db.Text, nullable=False)
    from_email = db.Column(db.String(255), nullable=False)
    to_email = db.Column(db.String(255), nullable=False)
    message_id = db.Column(db.String(255), unique=True)
    in_reply_to = db.Column(db.String(255))
    imap_uid = db.Column(db.String(255), unique=True)
    sent_at = db.Column(db.DateTime)
    read_at = db.Column(db.DateTime)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    contact = db.relationship('Contact', foreign_keys=[contact_id])


class Campaign(db.Model):
    __tablename__ = 'crm_campaigns'

    id = db.Column(db.BigInteger, primary_key=True)
    name = db.Column(db.String(255), nullable=False)
    subject = db.Column(db.String(255), nullable=False)
    html_body = db.Column(db.Text, nullable=False)
    text_body = db.Column(db.Text)
    status = db.Column(db.String(32), default='draft')
    from_name = db.Column(db.String(255))
    from_email = db.Column(db.String(255))
    cta_label = db.Column(db.String(255))
    cta_url = db.Column(db.String(255))
    created_by = db.Column(db.BigInteger, db.ForeignKey('users.id'))
    sent_at = db.Column(db.DateTime)
    recipients_count = db.Column(db.Integer, default=0)
    sent_count = db.Column(db.Integer, default=0)
    failed_count = db.Column(db.Integer, default=0)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)


def pipeline_stats():
    open_stages = ['prospecting', 'qualified', 'proposal', 'negotiation']
    total = db.session.query(func.coalesce(func.sum(Deal.amount), 0)).filter(
        Deal.stage.in_(open_stages)
    ).scalar()
    weighted = db.session.query(
        func.coalesce(func.sum(Deal.amount * Deal.probability / 100), 0)
    ).filter(Deal.stage.in_(open_stages)).scalar()
    won = db.session.query(func.coalesce(func.sum(Deal.amount), 0)).filter(
        Deal.stage == 'won'
    ).scalar()
    return {
        'pipeline_total': float(total or 0),
        'pipeline_weighted': float(weighted or 0),
        'total_payments': float(won or 0),
    }
