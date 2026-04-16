# Dar permisos de ejecución
chmod +x crear_usuarios_web.sh

# Ejecutar
sudo bash crear_usuarios_web.sh

# Verificar usuarios creados
getent passwd | grep webcontent

# Ver directorios de upload generados
ls -la /var/www/mi_sitio/uploads/

# Leer credenciales (solo root)
sudo cat /root/credenciales_web.log
