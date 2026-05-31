# CloudLab Latam - Laravel CMS

A Laravel-based Content Management System for CloudLab Latam website with blog and project management features.

## Features

- **Blog Management**: Create, edit, and manage blog posts with featured images
- **Project Management**: Showcase projects with images, descriptions, and links
- **Admin Panel**: Secure admin section for content management
- **User Authentication**: Admin login system with role-based access
- **Responsive Design**: Modern, mobile-friendly interface

## Requirements

- PHP >= 8.0
- MySQL >= 5.7
- Composer
- Node.js & NPM (for assets)

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd cloudlablatam1
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Update `.env` file with your database credentials**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cloudlablatam
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Create storage link**
   
   **For Windows (Run PowerShell/CMD as Administrator):**
   ```bash
   # Option 1: Use the batch file (run as Administrator)
   create-storage-link.bat
   
   # Option 2: Use PowerShell script (run as Administrator)
   powershell -ExecutionPolicy Bypass -File create-storage-link.ps1
   
   # Option 3: Manual command in PowerShell (as Administrator)
   New-Item -ItemType SymbolicLink -Path "public\storage" -Target "$(Resolve-Path storage\app\public)"
   ```
   
   **For Linux/Mac:**
   ```bash
   php artisan storage:link
   ```
   
   **Note:** On Windows, `php artisan storage:link` may fail because `symlink()` requires Administrator privileges. Use one of the Windows options above instead.

7. **Start the development server**
   ```bash
   php artisan serve
   ```

## Default Admin Credentials

After running the seeder, you can login with:

- **Email**: admin@cloudlablatam.com
- **Password**: password

**Important**: Change the default password after first login!

## Admin Panel Access

- Admin Login: `http://localhost:8000/admin/login`
- Admin Dashboard: `http://localhost:8000/admin/dashboard`

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Admin controllers
│   │   ├── BlogController.php
│   │   ├── ProjectController.php
│   │   └── HomeController.php
│   └── Middleware/
│       └── AdminMiddleware.php
├── Models/
│   ├── Blog.php
│   ├── Project.php
│   └── User.php
database/
├── migrations/              # Database migrations
└── seeders/                 # Database seeders
resources/
└── views/
    ├── admin/               # Admin views
    ├── blogs/               # Blog views
    ├── projects/            # Project views
    └── layouts/             # Layout templates
```

## Features Overview

### Blog Management
- Create, edit, and delete blog posts
- Featured image upload
- Excerpt and full content
- Publish/draft status
- SEO-friendly slugs

### Project Management
- Create, edit, and delete projects
- Project image upload
- Project URL and GitHub URL
- Technologies tags
- Featured project option
- Publish/draft status

### Admin Features
- Secure admin authentication
- Dashboard with statistics
- Full CRUD operations for blogs and projects
- Image upload and management

## Routes

### Frontend Routes
- `/` - Home page
- `/blogs` - Blog listing
- `/blogs/{slug}` - Blog post detail
- `/projects` - Project listing
- `/projects/{slug}` - Project detail

### Admin Routes
- `/admin/login` - Admin login
- `/admin/dashboard` - Admin dashboard
- `/admin/blogs` - Blog management
- `/admin/projects` - Project management

## Database Schema

### Users Table
- id
- name
- email
- password
- is_admin (boolean)
- timestamps

### Blogs Table
- id
- title
- slug
- excerpt
- content
- featured_image
- is_published
- user_id
- timestamps

### Projects Table
- id
- title
- slug
- description
- content
- image
- url
- github_url
- technologies
- is_featured
- is_published
- user_id
- timestamps

## Security Notes

- Change default admin password immediately
- Use strong passwords in production
- Keep Laravel and dependencies updated
- Configure proper file permissions
- Use HTTPS in production

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support, email info@cloudlablatam.tech or create an issue in the repository.
