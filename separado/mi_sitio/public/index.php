<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

$pdo = conectar();

$error = '';
$creado = isset($_GET['ok']) && $_GET['ok'] === '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    $autor = trim($_POST['autor'] ?? '');

    if ($titulo === '' || $contenido === '') {
        $error = 'Titulo y contenido son obligatorios.';
    } else {
        $stmtInsert = $pdo->prepare(
            'INSERT INTO articulos (titulo, contenido, autor) VALUES (:titulo, :contenido, :autor)'
        );
        $stmtInsert->execute([
            ':titulo' => $titulo,
            ':contenido' => $contenido,
            ':autor' => $autor !== '' ? $autor : null,
        ]);

        header('Location: index.php?ok=1');
        exit;
    }
}

$stmt = $pdo->query("SELECT titulo, contenido, autor, fecha
                       FROM articulos
                       ORDER BY fecha DESC
                       LIMIT 10");
$articulos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= SITE_NAME ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header><h1><?= SITE_NAME ?></h1></header>
  <main>
    <section>
      <h2>Publicar articulo</h2>

      <?php if ($creado): ?>
        <p>Articulo publicado correctamente.</p>
      <?php endif; ?>

      <?php if ($error !== ''): ?>
        <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>

      <form method="post" action="index.php">
        <p>
          <label for="titulo">Titulo</label><br>
          <input id="titulo" name="titulo" type="text" maxlength="200" required>
        </p>

        <p>
          <label for="autor">Autor (opcional)</label><br>
          <input id="autor" name="autor" type="text" maxlength="50">
        </p>

        <p>
          <label for="contenido">Contenido</label><br>
          <textarea id="contenido" name="contenido" rows="6" required></textarea>
        </p>

        <p>
          <button type="submit">Publicar articulo</button>
        </p>
      </form>
    </section>

    <hr>
    <h2>Ultimos articulos</h2>

    <?php foreach ($articulos as $art): ?>
      <article>
        <h3><?= htmlspecialchars($art['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
        <p><?= nl2br(htmlspecialchars($art['contenido'], ENT_QUOTES, 'UTF-8')) ?></p>
        <?php if (!empty($art['autor'])): ?>
          <p><strong>Autor:</strong> <?= htmlspecialchars($art['autor'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <time><?= htmlspecialchars($art['fecha'], ENT_QUOTES, 'UTF-8') ?></time>
      </article>
    <?php endforeach; ?>
  </main>
</body>
</html>
