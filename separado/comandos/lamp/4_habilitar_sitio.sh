sudo a2ensite mi_sitio.conf
sudo a2dissite 000-default.conf  # deshabilitar default
sudo a2enmod rewrite headers
sudo systemctl reload apache2

# Agregar hostname local para pruebas
echo "127.0.0.1 mi_sitio.local" | sudo tee -a /etc/hosts
