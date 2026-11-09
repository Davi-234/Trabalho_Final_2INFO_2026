<?php

function iniciarSessaoSegura(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

function exigirUsuarioAutenticado(): void
{
    iniciarSessaoSegura();

    if (!isset($_SESSION['usuario_id']) || !is_int($_SESSION['usuario_id'])) {
        header('Location: ../entrada/login.html', true, 302);
        exit;
    }
}

function obterTokenCsrf(): string
{
    iniciarSessaoSegura();

    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function tokenCsrfValido(mixed $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}