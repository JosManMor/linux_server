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
    'expires'  => time() - 3600,
    'path'     => '/',
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
      'expires'  => time() - 3600,
      'path'     => '/',
      'httponly' => true,
      'samesite' => 'Strict',
    ]);
    header('Location: index.php');
    exit;
  }

  if ($accion === 'publicar') {
    $titulo    = trim($_POST['titulo'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');

    if ($titulo === '' || $contenido === '') {
      $error = 'Título y contenido son obligatorios.';
    } else {
      $stmtInsert = $pdo->prepare(
        'INSERT INTO articulos (usuario_id, titulo, contenido) VALUES (:usuario_id, :titulo, :contenido)'
      );
      $stmtInsert->execute([
        ':usuario_id' => (int) $usuarioActual['id'],
        ':titulo'     => $titulo,
        ':contenido'  => $contenido,
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

$totalArticulos = count($articulos);

$articulos7dias = 0;
$ahora = new DateTime();
foreach ($articulos as $art) {
  $fechaArt = new DateTime($art['fecha']);
  if ($ahora->diff($fechaArt)->days <= 7) {
    $articulos7dias++;
  }
}

$ultimaFecha = $totalArticulos > 0
  ? (new DateTime($articulos[0]['fecha']))->format('d/m/Y')
  : '—';

$partes   = explode('.', $usuarioActual['usuario']);
$iniciales = '';
foreach ($partes as $p) {
  $iniciales .= mb_strtoupper(mb_substr($p, 0, 1));
}
$iniciales = mb_substr($iniciales, 0, 2) ?: 'U';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?> – Mi contenido</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

  <header class="bg-white border-b border-gray-200 px-6 py-3">
    <div class="max-w-2xl mx-auto flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
          <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
            <path d="M10 2L17 6v8l-7 4-7-4V6l7-4z" stroke="#1d4ed8" stroke-width="1.5" stroke-linejoin="round"/>
          </svg>
        </div>
        <span class="text-sm font-medium text-gray-900">
          <?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?>
        </span>
      </div>

      <div class="flex items-center gap-3">
        <div class="flex items-center gap-2">
          <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center text-xs font-medium text-blue-700">
            <?= htmlspecialchars($iniciales, ENT_QUOTES, 'UTF-8') ?>
          </div>
          <span class="text-sm text-gray-600 hidden sm:block">
            <?= htmlspecialchars($usuarioActual['usuario'], ENT_QUOTES, 'UTF-8') ?>
          </span>
        </div>

        <form method="post" action="contenido.php">
          <input type="hidden" name="accion" value="logout">
          <button type="submit"
            class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors">
            Cerrar sesión
          </button>
        </form>
      </div>
    </div>
  </header>

  <main class="max-w-2xl mx-auto px-4 py-8 space-y-6">

    <a href="index.php"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
      <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
        <path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Volver al acceso
    </a>

    <div class="grid grid-cols-3 gap-3">
      <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
        <p class="text-2xl font-medium text-gray-900"><?= $totalArticulos ?></p>
        <p class="text-xs text-gray-500 mt-0.5">artículos</p>
      </div>
      <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
        <p class="text-2xl font-medium text-gray-900"><?= $articulos7dias ?></p>
        <p class="text-xs text-gray-500 mt-0.5">esta semana</p>
      </div>
      <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
        <p class="text-sm font-medium text-gray-900 leading-tight mt-1"><?= htmlspecialchars($ultimaFecha, ENT_QUOTES, 'UTF-8') ?></p>
        <p class="text-xs text-gray-500 mt-0.5">último artículo</p>
      </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-6">
      <h2 class="text-sm font-medium text-gray-900 mb-4">Publicar artículo</h2>

      <?php if ($creado): ?>
        <div class="flex items-center gap-2 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-3 py-2 mb-4">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none" class="shrink-0">
            <path d="M3 8l3.5 3.5L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Artículo publicado correctamente.
        </div>
      <?php endif; ?>

      <?php if ($error !== ''): ?>
        <div class="flex items-center gap-2 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-3 py-2 mb-4">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none" class="shrink-0">
            <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/>
            <path d="M8 5v3M8 10.5v.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
          </svg>
          <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endif; ?>

      <form method="post" action="contenido.php" class="space-y-4">
        <input type="hidden" name="accion" value="publicar">

        <div>
          <label for="titulo" class="block text-xs text-gray-500 mb-1">Título</label>
          <input id="titulo" name="titulo" type="text" maxlength="200" required
            class="w-full text-sm px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
            placeholder="Escribe un título...">
        </div>

        <div>
          <label for="contenido" class="block text-xs text-gray-500 mb-1">Contenido</label>
          <textarea id="contenido" name="contenido" rows="5" required
            oninput="document.getElementById('chars').textContent = this.value.length + ' caracteres'"
            class="w-full text-sm px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 resize-y"
            placeholder="Escribe el contenido del artículo..."></textarea>
          <p id="chars" class="text-xs text-gray-400 text-right mt-1">0 caracteres</p>
        </div>

        <button type="submit"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-100 text-blue-800 text-sm font-medium hover:bg-blue-200 transition-colors">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
            <path d="M13.5 2.5l-6 11-2-4.5-4.5-2 11-6z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
          </svg>
          Publicar artículo
        </button>
      </form>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-6">
      <h2 class="text-sm font-medium text-gray-900 mb-4">Mis artículos</h2>

      <?php if ($totalArticulos === 0): ?>
        <div class="text-center py-10 text-sm text-gray-400">
          <svg width="32" height="32" viewBox="0 0 32 32" fill="none" class="mx-auto mb-3 text-gray-300">
            <rect x="6" y="4" width="20" height="24" rx="3" stroke="currentColor" stroke-width="1.5"/>
            <path d="M11 10h10M11 15h10M11 20h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          No has publicado ningún artículo todavía.
        </div>
      <?php else: ?>
        <div class="space-y-3">
          <?php foreach ($articulos as $art): ?>
            <article class="bg-gray-50 border border-gray-200 rounded-lg p-4">
              <h3 class="text-sm font-medium text-gray-900 mb-1">
                <?= htmlspecialchars($art['titulo'], ENT_QUOTES, 'UTF-8') ?>
              </h3>
              <p class="text-sm text-gray-600 leading-relaxed mb-3">
                <?= nl2br(htmlspecialchars($art['contenido'], ENT_QUOTES, 'UTF-8')) ?>
              </p>
              <time class="inline-flex items-center gap-1.5 text-xs text-gray-400">
                <svg width="12" height="12" viewBox="0 0 16 16" fill="none">
                  <rect x="2" y="3" width="12" height="11" rx="2" stroke="currentColor" stroke-width="1.3"/>
                  <path d="M5 1v2M11 1v2M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                </svg>
                <?= htmlspecialchars($art['fecha'], ENT_QUOTES, 'UTF-8') ?>
              </time>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

  </main>
</body>
</html>