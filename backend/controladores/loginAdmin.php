<?php
require_once __DIR__ . '/../seguranca/sessao.php';
iniciarSessaoSegura();
header('Content-Type: application/json; charset=utf-8');

function responderLoginAdmin(int $status, bool $sucesso, string $mensagem): void
{
    http_response_code($status);
    echo json_encode(
        ['sucesso' => $sucesso, 'mensagem' => $mensagem],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderLoginAdmin(405, false, 'Método não permitido.');
}

$email = $_POST['email'] ?? null;
$senha = $_POST['senha'] ?? null;
if (!is_string($email) || !is_string($senha) || trim($email) === '' || $senha === '') {
    responderLoginAdmin(400, false, 'Informe e-mail e senha.');
}

try {
    require_once __DIR__ . '/../servicos/service.php';

    $usuario = (new UsuarioService(new UsuarioDAO()))->login($email, $senha);
    if ($usuario === null || $usuario->getTipo() !== NivelUsuario::ADM) {
        responderLoginAdmin(401, false, 'E-mail ou senha de administrador inválidos.');
    }

    session_regenerate_id(true);
    $_SESSION['usuario_id'] = $usuario->getId();
    $_SESSION['usuario_nome'] = $usuario->getNome();
    $_SESSION['usuario_tipo'] = $usuario->getTipo()->value;

    responderLoginAdmin(200, true, 'Login de administrador realizado.');
} catch (Throwable $erro) {
    error_log($erro->getMessage());
    responderLoginAdmin(500, false, 'Não foi possível concluir o login. Tente novamente.');
}
