#!/usr/bin/env bash
set -euo pipefail

SRC_DIR="/home/manuel/Desktop/Telematica/linux/firewall_server/separado/mi_sitio"
DEST_DIR="/opt/lampp/htdocs/mi_sitio"

if [[ ! -d "$SRC_DIR" ]]; then
  echo "Error: no existe el directorio fuente: $SRC_DIR" >&2
  exit 1
fi

if [[ ! -d "$DEST_DIR" ]]; then
  echo "Error: no existe el directorio destino: $DEST_DIR" >&2
  exit 1
fi

echo "[1/3] Compilando CSS de Tailwind..."
cd "$SRC_DIR"
npm run build:css

echo "[2/3] Sincronizando archivos al servidor local..."
sudo rsync -av --delete \
  --exclude='.git' \
  --exclude='node_modules' \
  --exclude='package-lock.json' \
  --exclude='tailwind.config.js' \
  --exclude='package.json' \
  "$SRC_DIR/" "$DEST_DIR/"

echo "[3/3] Ajustando permisos de lectura para Apache..."
sudo find "$DEST_DIR" -type d -exec chmod 755 {} \;
sudo find "$DEST_DIR" -type f -exec chmod 644 {} \;

echo "Actualizacion completada en: $DEST_DIR"
