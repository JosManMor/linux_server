#!/bin/bash
# ================================================
# crear_usuarios_web.sh
# Genera 5 usuarios como registros en BD
# ================================================

set -euo pipefail

DB_HOST="${DB_HOST:-localhost}"
DB_NAME="${DB_NAME:-mi_sitio_db}"
DB_USER="${DB_USER:-root}"
DB_PASS="${DB_PASS:-root}"
LOG="${LOG:-./credenciales_usuarios_db.log}"
MYSQL_BIN="${MYSQL_BIN:-}"

if [ -z "$MYSQL_BIN" ]; then
  if [ -x "/opt/lampp/bin/mysql" ]; then
    MYSQL_BIN="/opt/lampp/bin/mysql"
  else
    MYSQL_BIN="mysql"
  fi
fi

MYSQL_CMD=("$MYSQL_BIN" -h "$DB_HOST" -u "$DB_USER" "-p$DB_PASS" "$DB_NAME")

echo "=== Creación de usuarios BD $(date) ===" >> "$LOG"

# Bucle for: crear usuarios webcontent1 al webcontent5 en BD
for i in $(seq 1 5); do
    USUARIO="user$i"
    PASS="password$i"

    EXISTE=$(
      "${MYSQL_CMD[@]}" -Nse "SELECT COUNT(*) FROM usuarios WHERE usuario='${USUARIO}';"
    )

    if [ "$EXISTE" -gt 0 ]; then
        echo "[SKIP] $USUARIO ya existe en BD"
    else
        "${MYSQL_CMD[@]}" -e "
          INSERT INTO usuarios (usuario, password)
          VALUES ('${USUARIO}', '${PASS}');
        "
        printf "%-15s | %s\n" "$USUARIO" "$PASS" >> "$LOG"
        echo "[OK] Usuario BD creado: $USUARIO"
    fi
done