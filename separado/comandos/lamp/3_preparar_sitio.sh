# Crear directorio del sitio
sudo mkdir -p /var/www/mi_sitio/{public,uploads,includes,assets}
sudo chown -R www-data:www-data /var/www/mi_sitio
sudo chmod -R 755 /var/www/mi_sitio

# Crear VirtualHost
sudo nano /etc/apache2/sites-available/mi_sitio.conf
