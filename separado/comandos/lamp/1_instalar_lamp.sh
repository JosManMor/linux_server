# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Apache2
sudo apt install apache2 -y
sudo systemctl enable --now apache2

# Instalar MariaDB
sudo apt install mariadb-server mariadb-client -y
sudo systemctl enable --now mariadb
sudo mysql_secure_installation

# Instalar PHP y módulos para sitio propio
sudo apt install php libapache2-mod-php php-mysql \
  php-curl php-gd php-mbstring php-xml php-zip -y

# Verificar versiones
apache2 -v && mysql --version && php -v
