<?php
require_once __DIR__ . '/sessao.php';
iniciarSessaoSegura();

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    !tokenCsrfValido($_POST['csrf_token'] ?? null)
) {
    http_response_code(400);
    exit('Solicitação inválida.');
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $params['path'],
        'domain' => $params['domain'],
        'secure' => $params['secure'],
        'httponly' => $params['httponly'],
        'samesite' => $params['samesite'] ?? 'Lax',
    ]);
}

session_destroy();
header('Location: ../../pages/entrada/login.html', true, 303);
exit;