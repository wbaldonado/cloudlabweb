"""WSGI entry point for Gunicorn / Passenger."""
from crm import create_app

app = create_app()
