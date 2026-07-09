#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT_DIR"

BACKUP_DIR="$ROOT_DIR/storage/backups"
mkdir -p "$BACKUP_DIR"
TIMESTAMP="$(date +%F_%H-%M-%S)"

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD:-dev123}"
LARAVEL_DB="${DB_DATABASE:-laravel}"
MOODLE_DB="${MOODLE_DB_NAME:-moodle}"

MYSQL_CMD=(mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD")
MYSQLDUMP_CMD=(mysqldump -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD")

backup_db() {
  local db_name="$1"
  local backup_file="$BACKUP_DIR/${db_name}_${TIMESTAMP}.sql"
  echo "Sauvegarde de la base $db_name vers $backup_file"
  "${MYSQLDUMP_CMD[@]}" --add-drop-table --routines --events "$db_name" > "$backup_file"
}

backup_db "$LARAVEL_DB"
backup_db "$MOODLE_DB"

echo "Réinitialisation de la base Laravel..."
php artisan migrate:fresh --seed --force

echo "Nettoyage de la base Moodle tout en conservant la configuration et l'administrateur..."
cat > /tmp/reset_moodle.sql <<'SQL'
USE moodle;
SET FOREIGN_KEY_CHECKS = 0;

-- Conserver la configuration système et l'administrateur.
DELETE FROM mdl_user WHERE username <> 'admin' AND id <> 2;
DELETE FROM mdl_role_assignments WHERE userid <> (SELECT id FROM mdl_user WHERE username = 'admin' LIMIT 1);

SET @keep_tables := 'mdl_config,mdl_config_plugins,mdl_role,mdl_capabilities,mdl_context,mdl_user,mdl_role_assignments';

SET @sql = '';
SELECT GROUP_CONCAT(CONCAT('DELETE FROM ', table_name, ';') SEPARATOR '\n')
INTO @sql
FROM information_schema.tables
WHERE table_schema = DATABASE()
  AND table_type = 'BASE TABLE'
  AND table_name NOT IN ('mdl_config','mdl_config_plugins','mdl_role','mdl_capabilities','mdl_context','mdl_user','mdl_role_assignments');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET FOREIGN_KEY_CHECKS = 1;
SQL

mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" < /tmp/reset_moodle.sql
rm -f /tmp/reset_moodle.sql

echo "Nettoyage terminé."
echo "Vérification rapide :"
mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "USE moodle; SELECT 'mdl_user' AS table_name, COUNT(*) AS rows FROM mdl_user; SELECT 'mdl_course' AS table_name, COUNT(*) AS rows FROM mdl_course; SELECT 'mdl_config' AS table_name, COUNT(*) AS rows FROM mdl_config;"
