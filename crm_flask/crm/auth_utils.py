import bcrypt
from flask import current_app
from flask_login import current_user

from crm.extensions import db, login_manager
from crm.models import User


@login_manager.user_loader
def load_user(user_id: str):
    return db.session.get(User, int(user_id))


def verify_laravel_password(plain: str, hashed: str) -> bool:
    if not plain or not hashed:
        return False
    try:
        h = hashed.encode('utf-8')
        if hashed.startswith('$2y$'):
            h = ('$2b$' + hashed[4:]).encode('utf-8')
        return bcrypt.checkpw(plain.encode('utf-8'), h)
    except Exception:
        return False


def admin_required(view):
    from functools import wraps
    from flask import abort, redirect, url_for
    from flask_login import login_required

    @wraps(view)
    @login_required
    def wrapped(*args, **kwargs):
        if not current_user.is_authenticated or not current_user.is_admin_user():
            abort(403)
        return view(*args, **kwargs)

    return wrapped


def wants_json():
    from flask import request
    return (
        request.accept_mimetypes.best == 'application/json'
        or request.headers.get('X-Requested-With') == 'XMLHttpRequest'
    )


def json_or_redirect(success_message: str, redirect_endpoint='dashboard.index', **context):
    from flask import jsonify, redirect, request, url_for

    if wants_json():
        return jsonify({'success': True, 'message': success_message, **context})
    return redirect(url_for(redirect_endpoint))


def validation_error(errors: dict):
    from flask import jsonify
    return jsonify({'success': False, 'errors': errors}), 422
