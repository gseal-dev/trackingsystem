# Document Tracking System

## First Time Setup

# Clone the project
1. git clone https://github.com/YourUsername/Document-Tracking-System.git
2. cd Document-Tracking-System/dts

# Install dependencies
3. composer install

# Copy environment file
4. cp .env.example .env

# Configure your database in .env file
# Then run the setup command
5. php artisan project:setup --fresh
## Manual Setup (Alternative)

```bash
php artisan migrate
php artisan db:seed
php artisan key:generate
php artisan storage:link
php artisan serve
```

This will automatically create the default roles, departments, and document statuses.