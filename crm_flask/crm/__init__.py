import click
from flask import Flask
from flask_wtf.csrf import CSRFProtect

from crm.config import Config
from crm.extensions import db, login_manager


def create_app(config_class=Config):
    app = Flask(__name__, template_folder='templates', static_folder='static')
    app.config.from_object(config_class)

    db.init_app(app)
    login_manager.init_app(app)
    CSRFProtect(app)

    from crm.routes.auth import auth_bp
    from crm.routes.dashboard import dashboard_bp
    from crm.routes.api import api_bp

    app.register_blueprint(auth_bp, url_prefix='/admin/crm')
    app.register_blueprint(dashboard_bp, url_prefix='/admin/crm')
    app.register_blueprint(api_bp, url_prefix='/admin/crm')

    @app.cli.command('fetch-inbox')
    def fetch_inbox_cmd():
        """Poll IMAP for CRM replies (run via cron every 10 min)."""
        from crm.services.imap_service import fetch_inbox
        with app.app_context():
            n = fetch_inbox()
            click.echo(f'Processed {n} reply(ies).')

    @app.context_processor
    def inject_globals():
        return {
            'laravel_admin_url': app.config.get('LARAVEL_ADMIN_URL', '/admin'),
        }

    return app
