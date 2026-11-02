import os
from pathlib import Path

from dotenv import load_dotenv

BASE_DIR = Path(__file__).resolve().parent.parent
load_dotenv(BASE_DIR / '.env')


class Config:
    SECRET_KEY = os.getenv('SECRET_KEY', 'dev-change-me')
    CRM_BASE_URL = os.getenv('CRM_BASE_URL', 'http://127.0.0.1:5001').rstrip('/')
    LARAVEL_ADMIN_URL = os.getenv('LARAVEL_ADMIN_URL', '/admin').rstrip('/')

    SQLALCHEMY_DATABASE_URI = (
        f"mysql+pymysql://{os.getenv('DB_USERNAME', 'root')}:"
        f"{os.getenv('DB_PASSWORD', '')}@"
        f"{os.getenv('DB_HOST', '127.0.0.1')}:"
        f"{os.getenv('DB_PORT', '3306')}/"
        f"{os.getenv('DB_DATABASE', 'db_cloudlab')}?charset=utf8mb4"
    )
    SQLALCHEMY_TRACK_MODIFICATIONS = False
    SQLALCHEMY_ENGINE_OPTIONS = {'pool_pre_ping': True}

    MAIL_HOST = os.getenv('MAIL_HOST', 'smtp.hostinger.com')
    MAIL_PORT = int(os.getenv('MAIL_PORT', 587))
    MAIL_USERNAME = os.getenv('MAIL_USERNAME')
    MAIL_PASSWORD = os.getenv('MAIL_PASSWORD')
    MAIL_FROM_ADDRESS = os.getenv('MAIL_FROM_ADDRESS', 'info@cloudlablatam.com')
    MAIL_FROM_NAME = os.getenv('MAIL_FROM_NAME', 'CloudLab Latam')
    MAIL_USE_TLS = os.getenv('MAIL_ENCRYPTION', 'tls') == 'tls'

    CRM_IMAP_ENABLED = os.getenv('CRM_IMAP_ENABLED', 'false').lower() == 'true'
    CRM_IMAP_HOST = os.getenv('CRM_IMAP_HOST', 'imap.hostinger.com')
    CRM_IMAP_PORT = int(os.getenv('CRM_IMAP_PORT', 993))
    CRM_IMAP_USERNAME = os.getenv('CRM_IMAP_USERNAME')
    CRM_IMAP_PASSWORD = os.getenv('CRM_IMAP_PASSWORD')
    CRM_IMAP_FOLDER = os.getenv('CRM_IMAP_FOLDER', 'INBOX')
    CRM_NOTIFY_EMAIL = os.getenv('CRM_NOTIFY_EMAIL', MAIL_FROM_ADDRESS)
    CRM_MESSAGE_ID_DOMAIN = os.getenv('CRM_MESSAGE_ID_DOMAIN', 'cloudlablatam.tech')
