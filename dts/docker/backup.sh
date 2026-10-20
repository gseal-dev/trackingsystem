#!/bin/bash
# Backup of the database + uploaded PDFs. Meant for Synology Task Scheduler (run as root).
# Edit the two folders below if yours are different.
set -euo pipefail

PROJECT_DIR="/volume1/docker/trackingsystem"   # where docker-compose.yml lives
BACKUP_DIR="/volume1/backup/trackingsystem"    # where backups are written
KEEP_DAYS=14                                   # older backups are deleted

STAMP="$(date +%Y-%m-%d_%H%M%S)"
DB_FILE="$BACKUP_DIR/db_$STAMP.sql.gz"
FILES_FILE="$BACKUP_DIR/files_$STAMP.tar.gz"
mkdir -p "$BACKUP_DIR"

# Everything is written to a temporary ".part" file first and only renamed when it is complete,
# so a failed run can never damage or replace a good backup.
trap 'rm -f "$DB_FILE.part" "$FILES_FILE.part"' EXIT

# 1) database (consistent snapshot, the site keeps running)
docker exec tracking_db sh -c 'exec mariadb-dump -uroot -p"$MARIADB_ROOT_PASSWORD" --single-transaction --routines "$MARIADB_DATABASE"' \
    | gzip > "$DB_FILE.part"

# refuse to keep an empty/broken dump
if [ "$(gzip -dc "$DB_FILE.part" | wc -c)" -lt 1000 ]; then
    echo "ERROR: database dump looks empty - nothing was saved" >&2
    exit 1
fi
mv "$DB_FILE.part" "$DB_FILE"

# 2) uploaded PDFs
tar -czf "$FILES_FILE.part" -C "$PROJECT_DIR/data" storage/app
mv "$FILES_FILE.part" "$FILES_FILE"

# 3) delete backups older than KEEP_DAYS
find "$BACKUP_DIR" -type f \( -name 'db_*.sql.gz' -o -name 'files_*.tar.gz' \) -mtime +"$KEEP_DAYS" -delete

echo "Backup finished: $STAMP"
