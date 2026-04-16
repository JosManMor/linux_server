CREATE TABLE articulos (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    titulo    VARCHAR(200) NOT NULL,
    contenido TEXT,
    autor     VARCHAR(50),
    fecha     DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insertar artículo de prueba
INSERT INTO articulos (titulo, contenido, autor)
VALUES (
    'Bienvenido a mi sitio',
    'Este es el primer artículo del servidor LAMP propio.',
    'admin'
);
