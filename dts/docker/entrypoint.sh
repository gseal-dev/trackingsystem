#!/bin/sh
# Runs every time the "app" container starts, before nginx / PHP-FPM.
set -eu

cd /var/www/html

# Run artisan as the web user so files it creates are writable by PHP-FPM later.
artisan() { su-exec www-data php artisan "$@"; }

echo "==> Preparing storage folders"
mkdir -p storage/app/public/documents \
         storage/framework/cache/data storage/framework/sessions storage/framework/views \
         storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
# nginx must be able to read the uploaded PDFs
chmod 755 storage storage/app storage/app/public storage/app/public/documents

echo "==> Checking settings"
# The database settings are shared with the MariaDB container (MARIADB_*).
export DB_DATABASE="${DB_DATABASE:-${MARIADB_DATABASE:-}}"
export DB_USERNAME="${DB_USERNAME:-${MARIADB_USER:-}}"
export DB_PASSWORD="${DB_PASSWORD:-${MARIADB_PASSWORD:-}}"
if [ -z "$DB_DATABASE" ] || [ -z "$DB_USERNAME" ] || [ -z "$DB_PASSWORD" ]; then
    echo "ERROR: MARIADB_DATABASE / MARIADB_USER / MARIADB_PASSWORD are not set. Check tracking.env." >&2
    exit 1
fi

# Application key: use APP_KEY from tracking.env, otherwise create one once and keep it.
if [ -z "${APP_KEY:-}" ]; then
    KEYFILE=storage/.app_key
    if [ ! -s "$KEYFILE" ]; then
        echo "==> Generating a new application key (saved in data/storage/.app_key)"
        artisan key:generate --show | tail -n 1 > "$KEYFILE"
        chown www-data:www-data "$KEYFILE"
        chmod 600 "$KEYFILE"
    fi
    APP_KEY="$(cat "$KEYFILE")"
    export APP_KEY
fi

echo "==> Linking public storage (uploaded files)"
# same link that "php artisan storage:link" makes, without needing Laravel to run
ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage

echo "==> Waiting for the database"
tries=0
until php -r '
    try {
        new PDO(
            "mysql:host=" . getenv("DB_HOST") . ";port=" . (getenv("DB_PORT") ?: "3306") . ";dbname=" . getenv("DB_DATABASE"),
            getenv("DB_USERNAME"), getenv("DB_PASSWORD"), [PDO::ATTR_TIMEOUT => 5]
        );
    } catch (Throwable $e) {
        fwrite(STDERR, "    " . $e->getMessage() . "\n");
        exit(1);
    }'
do
    tries=$((tries + 1))
    if [ "$tries" -ge 20 ]; then
        echo "ERROR: the database did not become reachable after 20 tries (see the message above)." >&2
        echo "       'Access denied' = the passwords in tracking.env do not match the existing database." >&2
        exit 1
    fi
    echo "    not ready yet (try $tries/20), waiting 3 seconds..."
    sleep 3
done

echo "==> Updating database tables"
artisan migrate --force

# Default roles, departments and the Admin account - ONLY on the very first start.
# (Running the seeder again would reset the Admin password and department names.)
db_scalar() {
    QUERY="$1" php -r '
        $pdo = new PDO(
            "mysql:host=" . getenv("DB_HOST") . ";port=" . (getenv("DB_PORT") ?: "3306") . ";dbname=" . getenv("DB_DATABASE"),
            getenv("DB_USERNAME"), getenv("DB_PASSWORD"), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        echo $pdo->query(getenv("QUERY"))->fetchColumn();'
}
if ROLE_COUNT="$(db_scalar 'SELECT COUNT(*) FROM roles' 2>/dev/null)"; then
    if [ "$ROLE_COUNT" = "0" ]; then
        echo "==> First start: creating default roles, departments and the Admin account"
        artisan db:seed --force
    fi
else
    # If we cannot tell, do NOT seed - better to skip than to overwrite real data.
    echo "WARNING: could not check whether the database already has data - skipping the default data." >&2
fi

echo "==> Optimizing for production"
artisan config:clear >/dev/null
artisan route:clear  >/dev/null
artisan view:clear   >/dev/null
artisan config:cache
artisan route:cache
artisan view:cache

echo "==> Starting web server"
exec "$@"
