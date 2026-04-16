# limit SSH: bloquea IP con más de 6 intentos en 30s
sudo ufw limit ssh

# limit HTTP y HTTPS
sudo ufw limit 80/tcp
sudo ufw limit 443/tcp

# Bloquear MariaDB al exterior (solo localhost)
sudo ufw deny 3306/tcp

# Habilitar logging detallado
sudo ufw logging medium

# Revisar intentos bloqueados
sudo grep "BLOCK" /var/log/ufw.log | tail -20

# Estado final numerado
sudo ufw status numbered
