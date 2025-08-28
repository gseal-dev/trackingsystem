# Document Tracking System

## First Time Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Copy environment file: `cp .env.example .env`
4. Configure your database credentials in `.env`
5. Run setup command: `php artisan project:setup --fresh`
6. Start the server: `php artisan serve`

## Manual Setup (Alternative)

```bash
php artisan migrate
php artisan db:seed
php artisan key:generate
php artisan storage:link
php artisan serve
```

This will automatically create the default roles, departments, and document statuses.