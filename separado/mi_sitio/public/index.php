<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

$pdo = conectar();

$error = '';
$cookieName = 'usuario_id';
$usuarioLogueado = null;

if (isset($_COOKIE[$cookieName]) && ctype_digit($_COOKIE[$cookieName])) {
  $stmtCookieUser = $pdo->prepare('SELECT id, usuario FROM usuarios WHERE id = :id LIMIT 1');
  $stmtCookieUser->execute([':id' => (int) $_COOKIE[$cookieName]]);
  $usuarioLogueado = $stmtCookieUser->fetch();

  if (!$usuarioLogueado) {
    setcookie($cookieName, '', [
      'expires' => time() - 3600,
      'path' => '/',
      'httponly' => true,
      'samesite' => 'Strict',
    ]);
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $accion = $_POST['accion'] ?? '';

  if ($accion === 'login') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($usuario === '' || $password === '') {
      $error = 'Usuario y contrasena son obligatorios.';
    } else {
      $stmtUser = $pdo->prepare('SELECT id, usuario, password FROM usuarios WHERE usuario = :usuario LIMIT 1');
      $stmtUser->execute([':usuario' => $usuario]);
      $user = $stmtUser->fetch();

      if ($user && hash_equals($user['password'], $password)) {
        setcookie($cookieName, (string) $user['id'], [
          'expires' => time() + 86400,
          'path' => '/',
          'httponly' => true,
          'samesite' => 'Strict',
        ]);
        header('Location: contenido.php');
        exit;
      } else {
        $error = 'Credenciales invalidas.';
      }
    }
  }

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
}

if (isset($_GET['error']) && $_GET['error'] === 'auth') {
  $error = 'Debes iniciar sesion para acceder al contenido.';
}
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
      <h2>Acceso</h2>

      <?php if ($usuarioLogueado): ?>
        <p>Sesion activa: <strong><?= htmlspecialchars($usuarioLogueado['usuario'], ENT_QUOTES, 'UTF-8') ?></strong></p>
        <p><a href="contenido.php">Ir a mi contenido</a></p>
        <form method="post" action="index.php">
          <input type="hidden" name="accion" value="logout">
          <button type="submit">Cerrar sesion</button>
        </form>
      <?php else: ?>
        <form method="post" action="index.php">
          <input type="hidden" name="accion" value="login">
          <p>
            <label for="usuario">Usuario</label><br>
            <input id="usuario" name="usuario" type="text" maxlength="50" required>
          </p>
          <p>
            <label for="password">Contrasena</label><br>
            <input id="password" name="password" type="password" required>
          </p>
          <p>
            <button type="submit">Iniciar sesion</button>
          </p>
        </form>
      <?php endif; ?>
    </section>

    <hr>
  </main>
</body>
</html>
