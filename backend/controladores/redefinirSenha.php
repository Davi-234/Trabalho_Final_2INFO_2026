<?php
header('Content-Type: application/json; charset=utf-8');

function responderRedefinicaoSenha(int $status, string $mensagem): void
{
	http_response_code($status);
	echo json_encode(['sucesso' => $status < 400, 'mensagem' => $mensagem], JSON_UNESCAPED_UNICODE);
	exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
	responderRedefinicaoSenha(405, 'Método não permitido.');
}

$token = $_POST['token'] ?? null;
$senha = $_POST['senha'] ?? null;
$confirmacao = $_POST['confirmar_senha'] ?? null;
if (!is_string($token) || !is_string($senha) || !is_string($confirmacao)) {
	responderRedefinicaoSenha(422, 'Preencha todos os campos.');
}
if ($senha !== $confirmacao) {
	responderRedefinicaoSenha(422, 'As senhas não coincidem.');
}

try {
	require_once __DIR__ . '/../servicos/service.php';
	(new UsuarioService(new UsuarioDAO()))->redefinirSenha($token, $senha);
	responderRedefinicaoSenha(200, 'Senha redefinida. Você já pode entrar com a nova senha.');
} catch (InvalidArgumentException $erro) {
	responderRedefinicaoSenha(422, $erro->getMessage());
} catch (Throwable $erro) {
	error_log($erro->getMessage());
	responderRedefinicaoSenha(500, 'Não foi possível redefinir a senha. Tente novamente.');
}