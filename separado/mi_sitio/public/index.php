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
      'expires'  => time() - 3600,
      'path'     => '/',
      'httponly' => true,
      'samesite' => 'Strict',
    ]);
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $accion = $_POST['accion'] ?? '';

  if ($accion === 'login') {
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($usuario === '' || $password === '') {
      $error = 'Usuario y contraseña son obligatorios.';
    } else {
      $stmtUser = $pdo->prepare(
        'SELECT id, usuario, password FROM usuarios WHERE usuario = :usuario LIMIT 1'
      );
      $stmtUser->execute([':usuario' => $usuario]);
      $user = $stmtUser->fetch();

      if ($user && hash_equals($user['password'], $password)) {
        setcookie($cookieName, (string) $user['id'], [
          'expires'  => time() + 86400,
          'path'     => '/',
          'httponly' => true,
          'samesite' => 'Strict',
        ]);
        header('Location: contenido.php');
        exit;
      } else {
        $error = 'Credenciales inválidas.';
      }
    }
  }

  if ($accion === 'logout') {
    setcookie($cookieName, '', [
      'expires'  => time() - 3600,
      'path'     => '/',
      'httponly' => true,
      'samesite' => 'Strict',
    ]);
    header('Location: index.php');
    exit;
  }
}

if (isset($_GET['error']) && $_GET['error'] === 'auth') {
  $error = 'Debes iniciar sesión para acceder al contenido.';
}

// Iniciales para el avatar
$iniciales = '';
if ($usuarioLogueado) {
  $partes = explode('.', $usuarioLogueado['usuario']);
  foreach ($partes as $p) {
    $iniciales .= mb_strtoupper(mb_substr($p, 0, 1));
  }
  $iniciales = mb_substr($iniciales, 0, 2);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center px-4 py-10">

  <div class="mb-6 flex flex-col items-center gap-2">
    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
      <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
        <path d="M10 2L17 6v8l-7 4-7-4V6l7-4z" stroke="#1d4ed8" stroke-width="1.5" stroke-linejoin="round"/>
      </svg>
    </div>
    <h1 class="text-lg font-medium text-gray-900"><?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="text-sm text-gray-500">Panel de acceso</p>
  </div>

  <?php if ($usuarioLogueado): ?>

    <div class="bg-white border border-gray-200 rounded-xl p-6 w-full max-w-sm">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-sm font-medium text-blue-700 shrink-0">
          <?= htmlspecialchars($iniciales ?: 'U', ENT_QUOTES, 'UTF-8') ?>
        </div>
        <div>
          <p class="text-sm font-medium text-gray-900">
            <?= htmlspecialchars($usuarioLogueado['usuario'], ENT_QUOTES, 'UTF-8') ?>
          </p>
          <p class="text-xs text-gray-500">Sesión activa</p>
        </div>
      </div>

      <a href="contenido.php"
         class="flex items-center justify-center gap-2 w-full py-2 rounded-lg bg-green-100 text-green-800 text-sm font-medium mb-2 hover:bg-green-200 transition-colors">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
          <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Ir a mi contenido
      </a>

      <form method="post" action="index.php">
        <input type="hidden" name="accion" value="logout">
        <button type="submit"
          class="w-full py-2 rounded-lg border border-gray-200 text-sm text-gray-500 hover:bg-gray-50 transition-colors">
          Cerrar sesión
        </button>
      </form>
    </div>

  <?php else: ?>

    <div class="bg-white border border-gray-200 rounded-xl p-6 w-full max-w-sm">
      <h2 class="text-sm font-medium text-gray-900 mb-4">Iniciar sesión</h2>

      <?php if ($error !== ''): ?>
        <div class="flex items-center gap-2 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-3 py-2 mb-4">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none" class="shrink-0">
            <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/>
            <path d="M8 5v3M8 10.5v.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
          </svg>
          <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endif; ?>

      <form method="post" action="index.php" class="space-y-4">
        <input type="hidden" name="accion" value="login">

        <div>
          <label for="usuario" class="block text-xs text-gray-500 mb-1">Usuario</label>
          <input id="usuario" name="usuario" type="text" maxlength="50" required
            class="w-full text-sm px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
            placeholder="tu_usuario">
        </div>

        <div>
          <label for="password" class="block text-xs text-gray-500 mb-1">Contraseña</label>
          <input id="password" name="password" type="password" required
            class="w-full text-sm px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
            placeholder="••••••••">
        </div>

        <button type="submit"
          class="w-full py-2 rounded-lg bg-blue-100 text-blue-800 text-sm font-medium hover:bg-blue-200 transition-colors">
          Iniciar sesión
        </button>
      </form>
    </div>

  <?php endif; ?>

</body>
</html>