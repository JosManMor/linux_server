CREATE DATABASE mi_sitio_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE USER 'sitio_user'@'localhost'
  IDENTIFIED BY 'ClaveSegura2024!';

GRANT ALL PRIVILEGES ON mi_sitio_db.*
  TO 'sitio_user'@'localhost';

FLUSH PRIVILEGES;
EXIT;
