# Conexiones bloqueadas
sudo tail -f /var/log/ufw.log

# Accesos al sitio
sudo tail -f \
  /var/log/apache2/mi_sitio_access.log

# Errores PHP/Apache
sudo tail -f \
  /var/log/apache2/mi_sitio_error.log
