<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

// Ejemplo: leer artículos de la BD
$pdo = conectar();
$stmt = $pdo->query("SELECT titulo, contenido, fecha
                       FROM articulos
                       ORDER BY fecha DESC
                       LIMIT 5");
$articulos = $stmt->fetchAll();
?>
<!DOCTYPEhtml>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= SITE_NAME ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header><h1><?= SITE_NAME ?></h1></header>
  <main>
    <?php foreach ($articulos as $art): ?>
      <article>
        <h2><?= htmlspecialchars($art['titulo']) ?></h2>
        <p><?= htmlspecialchars($art['contenido']) ?></p>
        <time><?= $art['fecha'] ?></time>
      </article>
    <?php endforeach; ?>
  </main>
</body>
</html>
