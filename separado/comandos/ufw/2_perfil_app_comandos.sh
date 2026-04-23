# Crear perfil UFW del sitio
sudo nano /etc/ufw/applications.d/mi-sitio-web

# Registrar y aplicar el perfil
sudo ufw app update 'Mi Sitio Web'
sudo ufw allow 'Mi Sitio Web'
sudo ufw app info 'Mi Sitio Web'
