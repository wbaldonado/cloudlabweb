from flask import Blueprint, flash, redirect, render_template, request, url_for
from flask_login import login_user, logout_user

from crm.auth_utils import verify_laravel_password
from crm.extensions import db
from crm.models import User

auth_bp = Blueprint('auth', __name__)


@auth_bp.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        email = request.form.get('email', '').strip()
        password = request.form.get('password', '')
        user = User.query.filter_by(email=email, is_admin=True).first()
        if user and verify_laravel_password(password, user.password):
            login_user(user)
            return redirect(request.args.get('next') or url_for('dashboard.index'))
        flash('Credenciales inválidas o sin permisos de admin.', 'danger')
    return render_template('login.html')


@auth_bp.route('/logout', methods=['POST'])
def logout():
    logout_user()
    return redirect(url_for('auth.login'))
