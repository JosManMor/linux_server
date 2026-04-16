# Servidor de Contenido Dinámico con Firewall Moderno

## Descripción

Implementación de un servidor web basado en la pila LAMP para alojar un
CMS (como WordPress o un sitio propio), con automatización de usuarios y
configuración de seguridad mediante firewall.

## Instrucciones principales

- Las instrucciones completas paso a paso están en [sitio.html](sitio.html).
- Esta guía resume el proyecto y apunta a los archivos separados en [separado](separado).

## Tecnologías utilizadas

- Linux\
- Apache\
- MySQL\
- PHP\
- UFW

## Instalación de la pila LAMP

- Instalación base LAMP: [separado/comandos/lamp/1_instalar_lamp.sh](separado/comandos/lamp/1_instalar_lamp.sh)
- Creación de base de datos y usuario: [separado/comandos/lamp/2_crear_bd_usuario.sql](separado/comandos/lamp/2_crear_bd_usuario.sql)
- Preparación de directorios del sitio: [separado/comandos/lamp/3_preparar_sitio.sh](separado/comandos/lamp/3_preparar_sitio.sh)
- Habilitar sitio y módulos Apache: [separado/comandos/lamp/4_habilitar_sitio.sh](separado/comandos/lamp/4_habilitar_sitio.sh)
- VirtualHost Apache: [separado/apache/mi_sitio.conf](separado/apache/mi_sitio.conf)

## Configuración del CMS

- Estructura de sitio PHP propio: [separado/mi_sitio](separado/mi_sitio)
- Configuración de conexión: [separado/mi_sitio/includes/config.php](separado/mi_sitio/includes/config.php)
- Conexión PDO: [separado/mi_sitio/includes/db.php](separado/mi_sitio/includes/db.php)
- Página principal: [separado/mi_sitio/public/index.php](separado/mi_sitio/public/index.php)
- Reglas de Apache en aplicación: [separado/mi_sitio/public/.htaccess](separado/mi_sitio/public/.htaccess)
- SQL de tabla de contenido: [separado/sql/crear_tabla_articulos.sql](separado/sql/crear_tabla_articulos.sql)

## Automatización: creación de usuarios

- Script de creación de usuarios: [separado/scripts/crear_usuarios_web.sh](separado/scripts/crear_usuarios_web.sh)
- Script de ejecución y verificación: [separado/scripts/ejecutar_y_verificar.sh](separado/scripts/ejecutar_y_verificar.sh)

## Configuración de Seguridad con UFW

- Política base y activación: [separado/comandos/ufw/1_base.sh](separado/comandos/ufw/1_base.sh)
- Perfil de aplicación UFW: [separado/comandos/ufw/2_perfil_app.conf](separado/comandos/ufw/2_perfil_app.conf)
- Comandos para registrar perfil: [separado/comandos/ufw/2_perfil_app_comandos.sh](separado/comandos/ufw/2_perfil_app_comandos.sh)
- Límites anti fuerza bruta y bloqueos: [separado/comandos/ufw/3_limit_y_bloqueos.sh](separado/comandos/ufw/3_limit_y_bloqueos.sh)
- Monitoreo de logs: [separado/comandos/ufw/4_logs_tiempo_real.sh](separado/comandos/ufw/4_logs_tiempo_real.sh)

## Buenas prácticas

- Deshabilitar root en SSH\
- Usar llaves SSH\
- Mantener sistema actualizado\
- Revisar logs

## Resultado esperado

- Servidor LAMP funcional\
- CMS accesible\
- 5 usuarios creados\
- Firewall activo
