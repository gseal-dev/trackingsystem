#!/bin/bash
# Backup of the database + uploaded PDFs. Meant for Synology Task Scheduler (run as root).
# Edit the two folders below if yours are different.
set -euo pipefail

PROJECT_DIR="/volume1/docker/trackingsystem"   # where docker-compose.yml lives
BACKUP_DIR="/volume1/backup/trackingsystem"    # where backups are written
KEEP_DAYS=14                                   # older backups are deleted

STAMP="$(date +%Y-%m-%d_%H%M)"
mkdir -p "$BACKUP_DIR"

# 1) database (consistent snapshot, the site keeps running)
docker exec tracking_db sh -c 'exec mariadb-dump -uroot -p"$MARIADB_ROOT_PASSWORD" --single-transaction --routines "$MARIADB_DATABASE"' \
    | gzip > "$BACKUP_DIR/db_$STAMP.sql.gz"

# refuse to keep an empty/broken dump
if [ "$(gzip -dc "$BACKUP_DIR/db_$STAMP.sql.gz" | wc -c)" -lt 1000 ]; then
    echo "ERROR: database dump looks empty" >&2
    rm -f "$BACKUP_DIR/db_$STAMP.sql.gz"
    exit 1
fi

# 2) uploaded PDFs
tar -czf "$BACKUP_DIR/files_$STAMP.tar.gz" -C "$PROJECT_DIR/data" storage/app

# 3) delete backups older than KEEP_DAYS
find "$BACKUP_DIR" -type f \( -name 'db_*.sql.gz' -o -name 'files_*.tar.gz' \) -mtime +"$KEEP_DAYS" -delete

echo "Backup finished: $STAMP"
