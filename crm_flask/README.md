# CloudLab Latam — Flask CRM

Python **Flask** CRM with the **same features** as the Laravel CRM admin module. Uses the **same MySQL database** and **same admin users** (Laravel `users` table + bcrypt passwords).

## Features (parity with Laravel)

| Feature | Status |
|---------|--------|
| Dashboard stats (contacts, companies, pipeline, activities) | ✅ |
| CRUD contacts, companies, deals, activities (modals + AJAX) | ✅ |
| Send email to contact (SMTP / Hostinger) | ✅ |
| IMAP reply polling + admin notification | ✅ |
| CSV import (companies / contacts) | ✅ |
| Email campaigns | 🔜 Use Laravel CRM campaigns until Flask port (same DB) |

## Quick start (local)

```bash
cd crm_flask
python -m venv venv
venv\Scripts\activate          # Windows
pip install -r requirements.txt
copy .env.example .env         # edit DB_* to match Laravel
python run.py
```

Open: **http://127.0.0.1:5001/admin/crm**

Login: same as Laravel admin (`admin@cloudlablatam.com` / `password`)

## Production (Hostinger)

### Option A — Subdomain (recommended)
- `crm.cloudlablatam.tech` → Python app (Passenger, Gunicorn, or VPS)
- Point Laravel admin link to `https://crm.cloudlablatam.tech/admin/crm`

### Option B — Reverse proxy
Nginx/Apache proxy `/crm-app/` → Flask on port 5001

### Cron (reply detection)
```
*/10 * * * * cd /path/to/crm_flask && /path/to/venv/bin/flask fetch-inbox >> /dev/null 2>&1
```

Or:
```
*/10 * * * * cd /path/to/crm_flask && /path/to/venv/bin/python -c "from crm import create_app; from crm.services.imap_service import fetch_inbox; app=create_app(); app.app_context().push(); fetch_inbox()"
```

## Laravel integration

In Laravel `.env`:
```env
CRM_FLASK_URL=http://127.0.0.1:5001/admin/crm
```

Admin sidebar **CRM Dashboard** opens the Flask app (same data, upgraded stack).

## Environment variables

See `.env.example` — mirrors Laravel `MAIL_*` and `CRM_IMAP_*`.

## Project structure

```
crm_flask/
  run.py                 # Entry point
  crm/
    __init__.py          # App factory
    models.py            # SQLAlchemy (shared tables)
    presenter.py         # JSON for AJAX (same as CrmPresenter)
    routes/
      auth.py            # Login (Laravel users)
      dashboard.py       # Main CRM UI
      api.py             # CRUD + email + import
    services/
      email_service.py   # SMTP outbound
      imap_service.py    # Free reply polling
      csv_importer.py
    templates/
    static/              # crm-dashboard.css/js (from Laravel)
```

## Commands

```bash
flask fetch-inbox      # Poll IMAP for lead replies
```

## Database

Uses existing tables — no separate DB. Run Laravel migrations / SQL scripts as before:
- `crm_companies`, `crm_contacts`, `crm_deals`, `crm_activities`, `crm_email_messages`

## Why Flask?

- Independent deploy and scaling
- Easier Python ecosystem for email/ML/automation later
- Same UX and data as Laravel CRM
