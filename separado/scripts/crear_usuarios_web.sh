#!/bin/bash
# ================================================
# crear_usuarios_web.sh
# Genera 5 usuarios con acceso limitado a uploads
# del sitio propio en /var/www/mi_sitio/uploads/
# ================================================

UPLOAD_BASE="/var/www/mi_sitio/uploads"
LOG="/root/credenciales_web.log"
GRUPO="webupload"

# Crear grupo compartido si no existe
getent group "$GRUPO" &>/dev/null || sudo groupadd "$GRUPO"

# Ajustar directorio base con SGID
sudo chown www-data:"$GRUPO" "$UPLOAD_BASE"
sudo chmod 2775 "$UPLOAD_BASE"

echo "=== Creación de usuarios $(date) ===" | \
  sudo tee -a "$LOG" >/dev/null

# Bucle for: crear usuarios webcontent1 al webcontent5
for i in $(seq 1 5); do
    USER="webcontent$i"
    PASS=$(openssl rand -base64 14)
    USER_DIR="$UPLOAD_BASE/$USER"

    # Crear usuario sin shell interactivo
    if id "$USER" &>/dev/null; then
        echo "[SKIP] $USER ya existe"
    else
        sudo useradd \
            --create-home \
            --shell /usr/sbin/nologin \
            --groups "$GRUPO" \
            --comment "Subidor de contenido web $i" \
            "$USER"

        echo "$USER:$PASS" | sudo chpasswd

        # Directorio privado de uploads
        sudo mkdir -p "$USER_DIR"
        sudo chown "$USER":"$GRUPO" "$USER_DIR"
        sudo chmod 750 "$USER_DIR"

        # Guardar credencial en log seguro
        printf "%-15s | %s\n" "$USER" "$PASS" | \
          sudo tee -a "$LOG" >/dev/null

        echo "[OK] Creado: $USER → $USER_DIR"
    fi
done

# Proteger log de credenciales
sudo chmod 600 "$LOG"
sudo chown root:root "$LOG"

echo "
--- Usuarios del grupo $GRUPO ---"
getent group "$GRUPO"
