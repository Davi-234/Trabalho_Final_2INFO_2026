<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

function responderLogin(int $status, bool $sucesso, string $mensagem): void
{
    http_response_code($status);
    echo json_encode(
        ['sucesso' => $sucesso, 'mensagem' => $mensagem],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}   

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderLogin(405, false, 'Método não permitido.');
}

$email = $_POST['email'] ?? null;
$senha = $_POST['senha'] ?? null;
if (!is_string($email) || !is_string($senha) || trim($email) === '' || $senha === '') {
    responderLogin(400, false, 'Informe um e-mail e uma senha válidos.');
}

try {
    require_once __DIR__ . '/service.php';

    $usuarioService = new UsuarioService(new UsuarioDAO());
    $usuario = $usuarioService->login($email, $senha);

    if ($usuario === null) {
        responderLogin(401, false, 'E-mail ou senha inválidos.');
    }

    session_regenerate_id(true);
    $_SESSION['usuario_id'] = $usuario->getId();
    $_SESSION['usuario_nome'] = $usuario->getNome();

    responderLogin(200, true, 'Login realizado com sucesso.');
    
} catch (Throwable $erro) {
    error_log($erro->getMessage());
    responderLogin(500, false, 'Não foi possível concluir o login. Tente novamente.');
}

?>