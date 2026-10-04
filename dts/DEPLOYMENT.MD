This document provides step-by-step instructions for deploying, updating, and maintaining the Document Tracking System using Docker Desktop / Docker Engine.

---

## Prerequisites

- **Docker & Docker Compose** installed on the host machine.
- Project root mapped to `/var/www/html/trackingsystem/dts` inside containers.
- Ports `8081` (Nginx/App) and `3306` (MariaDB) open on host/firewall.

---

## 1. Initial Deployment Setup

### Step 1: Environment Configuration
Copy the sample environment file and generate the application encryption key:

```bash
# Copy environment template
cp .env.example .env

# Configure environment variables (DB host, passwords, app URL)
nano .env
```

### Step 1: Ensure key settings in .env match your container network:

```base
APP_NAME=TrackingSystem
APP_ENV=production
APP_DEBUG=false
APP_URL=[http://192.168.100.204:8081](http://192.168.100.204:8081)

LOG_LEVEL=info

DB_CONNECTION=mysql
DB_HOST=lemp_mariadb
DB_PORT=3306
DB_DATABASE=tracking_system
DB_USERNAME=tracker
DB_PASSWORD=StrongPassword123!

QUEUE_CONNECTION=sync
CACHE_STORE=file
```
### Step 2: Build & Boot Containers
Start the containers in detached mode:

```bash
docker compose up -d --build
```

### Step 3: Install PHP Dependencies & Build Assets
Run Composer and NPM asset build commands inside the app container:

```bash
# Install PHP production dependencies
docker exec -it lemp_php composer install --no-dev --optimize-autoloader

# Generate Application Key
docker exec -it lemp_php php artisan key:generate --force

# Install Node dependencies
sudo docker run --rm -v /DATA/lemp-stack/html/trackingsystem/dts:/app -w /app node:lts-slim npm install

# Build production assets (Vite / Tailwind)
sudo docker run --rm -v /DATA/lemp-stack/html/trackingsystem/dts:/app -w /app node:lts-slim npm run build
```

### Step 4: Storage Permissions & Link Creation
Ensure the log directories exist and grant write access to www-data on storage and cache folders:

```bash
# Create log directory if missing
docker exec -it lemp_php mkdir -p /var/www/html/trackingsystem/dts/storage/logs

# Fix storage & cache permissions
docker exec -it lemp_php chmod -R 777 /var/www/html/trackingsystem/dts/storage /var/www/html/trackingsystem/dts/bootstrap/cache

# Create public storage symlink
docker exec -it lemp_php php artisan storage:link
```
### Step 5: Database Migrations & Seeding
Run database migrations and seed default data:

```bash
# Execute database migrations
docker exec -it lemp_php php artisan migrate --force

# Seed the database
docker exec -it lemp_php php artisan db:seed --force
```

### Step 6: Production Caching
Optimize application performance by caching configurations, routes, and views:

```bash
docker exec -it lemp_php php artisan config:cache
docker exec -it lemp_php php artisan route:cache
docker exec -it lemp_php php artisan view:cache
```

## 2. Maintenance & Troubleshooting Commands

### Viewing Application & Container Logs

```bash
# View Laravel application log
docker exec -it lemp_php tail -n 50 /var/www/html/trackingsystem/dts/storage/logs/laravel.log

# View PHP container logs
docker logs lemp_php --tail 50

# View Nginx container logs
docker logs lemp_nginx --tail 50
```

### Re-Seeding or Resetting the Database

If you need to wipe and completely reset the database with fresh seeds:

    ⚠️ Warning: This will delete all existing table data!

```bash
docker exec -it lemp_php php artisan migrate:fresh --seed --force
```

### Clearing Application Cache

Run this whenever .env changes or after pulling code updates:

```bash
docker exec -it lemp_php php artisan config:clear
docker exec -it lemp_php php artisan cache:clear
docker exec -it lemp_php php artisan route:clear
docker exec -it lemp_php php artisan view:clear

# Re-apply production caches
docker exec -it lemp_php php artisan config:cache
docker exec -it lemp_php php artisan route:cache
docker exec -it lemp_php php artisan view:cache
```

## 3. Routine Deployment Script (Updates)

When pulling new code changes to production, run this sequence:

```bash
git pull origin main
docker exec -it lemp_php composer install --no-dev --optimize-autoloader
docker exec -it lemp_php npm run build
docker exec -it lemp_php php artisan migrate --force
docker exec -it lemp_php php artisan config:clear
docker exec -it lemp_php php artisan config:cache
docker exec -it lemp_php php artisan view:cache
```