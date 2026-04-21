<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

$pdo = conectar();
$cookieName = 'usuario_id';
$error = '';
$creado = false;

if (!isset($_COOKIE[$cookieName]) || !ctype_digit($_COOKIE[$cookieName])) {
  header('Location: index.php?error=auth');
  exit;
}

$stmtUser = $pdo->prepare('SELECT id, usuario FROM usuarios WHERE id = :id LIMIT 1');
$stmtUser->execute([':id' => (int) $_COOKIE[$cookieName]]);
$usuarioActual = $stmtUser->fetch();

if (!$usuarioActual) {
  setcookie($cookieName, '', [
    'expires' => time() - 3600,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Strict',
  ]);
  header('Location: index.php?error=auth');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $accion = $_POST['accion'] ?? '';

  if ($accion === 'logout') {
    setcookie($cookieName, '', [
      'expires' => time() - 3600,
      'path' => '/',
      'httponly' => true,
      'samesite' => 'Strict',
    ]);
    header('Location: index.php');
    exit;
  }

  if ($accion === 'publicar') {
    $titulo = trim($_POST['titulo'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');

    if ($titulo === '' || $contenido === '') {
      $error = 'Titulo y contenido son obligatorios.';
    } else {
      $stmtInsert = $pdo->prepare(
        'INSERT INTO articulos (usuario_id, titulo, contenido) VALUES (:usuario_id, :titulo, :contenido)'
      );
      $stmtInsert->execute([
        ':usuario_id' => (int) $usuarioActual['id'],
        ':titulo' => $titulo,
        ':contenido' => $contenido,
      ]);
      $creado = true;
    }
  }
}

$stmt = $pdo->prepare(
  'SELECT titulo, contenido, fecha
   FROM articulos
   WHERE usuario_id = :usuario_id
   ORDER BY fecha DESC
   LIMIT 20'
);
$stmt->execute([':usuario_id' => (int) $usuarioActual['id']]);
$articulos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= SITE_NAME ?> - Mi contenido</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header>
    <h1><?= SITE_NAME ?></h1>
    <p>Usuario: <strong><?= htmlspecialchars($usuarioActual['usuario'], ENT_QUOTES, 'UTF-8') ?></strong></p>
  </header>

  <main>
    <p><a href="index.php">Volver al acceso</a></p>

    <form method="post" action="contenido.php">
      <input type="hidden" name="accion" value="logout">
      <button type="submit">Cerrar sesion</button>
    </form>

    <hr>

    <section>
      <h2>Publicar articulo</h2>

      <?php if ($creado): ?>
        <p>Articulo publicado correctamente.</p>
      <?php endif; ?>

      <?php if ($error !== ''): ?>
        <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>

      <form method="post" action="contenido.php">
        <input type="hidden" name="accion" value="publicar">
        <p>
          <label for="titulo">Titulo</label><br>
          <input id="titulo" name="titulo" type="text" maxlength="200" required>
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

    <section>
      <h2>Mis articulos</h2>

      <?php if (count($articulos) === 0): ?>
        <p>No hay articulos para este usuario.</p>
      <?php endif; ?>

      <?php foreach ($articulos as $art): ?>
        <article>
          <h3><?= htmlspecialchars($art['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
          <p><?= nl2br(htmlspecialchars($art['contenido'], ENT_QUOTES, 'UTF-8')) ?></p>
          <time><?= htmlspecialchars($art['fecha'], ENT_QUOTES, 'UTF-8') ?></time>
        </article>
      <?php endforeach; ?>
    </section>
  </main>
</body>
</html>
