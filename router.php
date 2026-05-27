<?php
// router.php — Point d'entrée unique pour Railway avec PHP built-in server
// Gère les URLs propres et redirige vers les bons fichiers

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Servir les fichiers statiques normalement
if ($uri !== '/' && file_exists(__DIR__ . $uri) && !is_dir(__DIR__ . $uri)) {
    return false;
}

// Mapping des URLs
$routes = [
    '/'            => '/index.php',
    '/classement'  => '/classement.php',
    '/equipes'     => '/equipes.php',
    '/rejoindre'   => '/rejoindre.php',
    '/regles'      => '/regles.php',
    '/login'       => '/login.php',
    '/logout'      => '/logout.php',
    '/player'      => '/player/dashboard.php',
    '/admin'       => '/admin/index.php',
    '/admin/actions' => '/admin/actions.php',
];

// Chercher la route exacte ou avec .php
$file = null;
if (array_key_exists($uri, $routes)) {
    $file = __DIR__ . $routes[$uri];
} elseif (file_exists(__DIR__ . $uri . '.php')) {
    $file = __DIR__ . $uri . '.php';
} elseif (file_exists(__DIR__ . $uri)) {
    $file = __DIR__ . $uri;
} else {
    // 404
    http_response_code(404);
    require_once __DIR__ . '/includes/config.php';
    $pageTitle = 'Page introuvable';
    include __DIR__ . '/includes/header.php';
    echo '<div class="page-hero"><h1>404</h1><div class="divider-gold"></div><p>Cette page n\'existe pas.</p></div>';
    echo '<div style="text-align:center; margin-top:2rem"><a href="/" class="btn btn-outline">← Retour à l\'accueil</a></div>';
    include __DIR__ . '/includes/footer.php';
    return;
}

require $file;
