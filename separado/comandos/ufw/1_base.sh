# Política por defecto: denegar todo lo entrante
sudo ufw default deny incoming
sudo ufw default allow outgoing

# SSH primero (para no perder acceso remoto)
sudo ufw allow OpenSSH

# Perfiles de aplicación Apache
sudo ufw allow 'Apache Full'   # HTTP 80 + HTTPS 443

# Activar UFW
sudo ufw enable

# Verificar
sudo ufw status verbose
