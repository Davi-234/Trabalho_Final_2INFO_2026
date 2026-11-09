<?php
header('Content-Type: application/json; charset=utf-8');

function Cadastro(int $status, bool $sucesso, string $mensagem): void
{
	http_response_code($status);
	echo json_encode(
		['sucesso' => $sucesso, 'mensagem' => $mensagem],
		JSON_UNESCAPED_UNICODE
	);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	Cadastro(405, false, 'Método não permitido.');
}

$nome = $_POST['nome'] ?? null;
$email = $_POST['email'] ?? null;
$senha = $_POST['senha'] ?? null;
$linkGithub = $_POST['link_github'] ?? null;
$linkLinkedin = $_POST['link_linkedin'] ?? null;

if (!is_string($nome) || !is_string($email) || !is_string($senha)) {
	Cadastro(400, false, 'Dados obrigatórios do cadastro não foram enviados.');
}

if (
	($linkGithub !== null && !is_string($linkGithub)) ||
	($linkLinkedin !== null && !is_string($linkLinkedin))
) {
	Cadastro(400, false, 'Os links informados são inválidos.');
}

try {
	require_once __DIR__ . '/../servicos/service.php';

	$usuario = new Usuario(
		trim($nome),
		trim($email),
		$senha,
		$linkGithub ?: null,
		$linkLinkedin ?: null
	);

	$usuarioService = new UsuarioService(new UsuarioDAO());
	$usuarioService->add($usuario);

	Cadastro(201, true, 'Cadastro realizado com sucesso.');
} catch (InvalidArgumentException $erro) {
	Cadastro(422, false, $erro->getMessage());
} catch (Throwable $erro) {
	error_log($erro->getMessage());
	Cadastro(500, false, 'Não foi possível concluir o cadastro. Tente novamente.');
}
