# Project Signal Intelligence (PSI)

> Platform that helps users discover companies showing signals of opportunity — expansion, hiring, investment, vendor registration, and digital transformation.

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.4)
- **Frontend**: React 19 + Inertia.js + shadcn/ui
- **Database**: MySQL 8
- **Cache & Queue**: Redis 7
- **Queue Monitoring**: Laravel Horizon
- **Email (Development)**: Mailpit
- **Reverse Proxy**: External Nginx Proxy Manager

## Prerequisites

- Docker & Docker Compose
- External `web-gateway` Docker network (for Nginx Proxy Manager)

## Quick Start

### 1. Clone & Configure

```bash
cp .env.example .env
# Generate a new app key (inside the container after first build)
```

### 2. Start All Services

```bash
docker compose up -d
```

This starts 4 containers:

| Container | Purpose | Port |
|---|---|---|
| `web-prosignal` | Laravel + Nginx + Horizon | 80 (internal) |
| `mysql-prosignal` | MySQL 8 Database | 3306 (internal) |
| `redis-prosignal` | Redis Cache & Queue | 6379 (internal) |
| `mailpit-prosignal` | Development SMTP | 8025 (UI) |

### 3. Run Migrations & Seed

```bash
docker exec web-prosignal php artisan migrate
docker exec web-prosignal php artisan db:seed
```

### 4. Access the Application

- **Application**: http://prosignal.local (via Nginx Proxy Manager)
- **Mailpit UI**: http://localhost:8025
- **Horizon**: http://prosignal.local/horizon (Super Admin / Admin only)

### 5. Default Super Admin

| Field | Value |
|---|---|
| Email | `superadmin@prosignal.local` |
| Password | `password` |

## Roles & Permissions

| Role | Access Level |
|---|---|
| Super Admin | Full access (bypasses all permission checks) |
| Admin | System management |
| Member | Application user (default for new registrations) |

## Development

### Useful Commands

```bash
# View logs
docker compose logs -f web

# Run artisan commands
docker exec web-prosignal php artisan <command>

# Queue monitoring
docker exec web-prosignal php artisan horizon:status

# Run backup
docker exec web-prosignal php artisan backup:run --only-db

# Check activity log
docker exec web-prosignal php artisan tinker --execute="Spatie\Activitylog\Models\Activity::latest()->take(10)->get(['description', 'causer_type', 'created_at'])"
```

## Project Structure

```
project-signal-finder/
├── app/
│   ├── Http/Controllers/Auth/     # Authentication controllers
│   ├── Http/Middleware/           # Inertia middleware
│   ├── Jobs/                     # Queue jobs
│   ├── Models/                   # Eloquent models
│   └── Providers/                # Service providers
├── database/
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Roles, permissions, super admin
├── docker/
│   ├── nginx/nginx.conf          # Nginx configuration
│   └── php/
│       ├── Dockerfile            # Multi-stage Docker build
│       └── supervisord.conf      # Process manager config
├── resources/
│   ├── css/app.css               # Design system (Tailwind + shadcn theme)
│   ├── js/
│   │   ├── Components/           # React components
│   │   │   ├── ui/               # shadcn/ui base components
│   │   │   ├── Header.jsx        # App header with user menu
│   │   │   └── Sidebar.jsx       # Navigation sidebar
│   │   ├── Layouts/              # Page layouts
│   │   ├── Pages/                # Inertia pages
│   │   │   ├── Auth/             # Login, Register, Password reset
│   │   │   └── Dashboard.jsx     # Main dashboard
│   │   └── lib/utils.js          # shadcn utility (cn function)
│   └── views/app.blade.php       # Inertia root template
├── routes/
│   ├── web.php                   # Web routes
│   └── console.php               # Scheduled tasks
├── docker-compose.yml
└── .env.example
```
# prosignal
