<?php
header('Content-Type: application/json; charset=utf-8');

function responderSolicitacaoRecuperacao(int $status, string $mensagem): void
{
	http_response_code($status);
	echo json_encode(['sucesso' => $status < 400, 'mensagem' => $mensagem], JSON_UNESCAPED_UNICODE);
	exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
	responderSolicitacaoRecuperacao(405, 'Método não permitido.');
}

$email = $_POST['email'] ?? null;
if (!is_string($email) || !filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
	responderSolicitacaoRecuperacao(422, 'Informe um endereço de e-mail válido.');
}

try {
	require_once __DIR__ . '/../configuracao/ambiente.php';
	carregarAmbiente();
} catch (Throwable $erroAmbiente) {
	error_log($erroAmbiente->getMessage());
	responderSolicitacaoRecuperacao(503, 'Não foi possível carregar as configurações. Confira o arquivo .env na raiz do projeto.');
}

$baseUrl = rtrim((string) getenv('DEVSHARE_BASE_URL'), '/');
$remetente = (string) getenv('DEVSHARE_MAIL_FROM');
$clientId = (string) getenv('SENDPULSE_CLIENT_ID');
$clientSecret = (string) getenv('SENDPULSE_CLIENT_SECRET');
$partesUrl = parse_url($baseUrl);
if (
	$baseUrl === '' || !is_array($partesUrl) ||
	!in_array($partesUrl['scheme'] ?? '', ['https', 'http'], true) ||
	empty($partesUrl['host']) || !filter_var($remetente, FILTER_VALIDATE_EMAIL) ||
	$clientId === '' || $clientSecret === ''
) {
	error_log('Recuperação de senha não configurada: revise URL, remetente e credenciais SendPulse no .env.');
	responderSolicitacaoRecuperacao(503, 'As configurações de envio estão incompletas. Confira o arquivo .env.');
}

try {
	require_once __DIR__ . '/../servicos/service.php';
	$usuarioDAO = new UsuarioDAO();
	$usuarioService = new UsuarioService($usuarioDAO);
	$usuario = $usuarioService->get(trim($email));
	if ($usuario === null) {
		responderSolicitacaoRecuperacao(404, 'E-mail não cadastrado.');
	}

	$token = $usuarioService->gerarTokenRecuperacaoSenha(trim($email));
	if ($token === null) {
		responderSolicitacaoRecuperacao(500, 'Não foi possível gerar o link de recuperação.');
	}

	$link = $baseUrl . '/pages/entrada/redefinir_senha.html?token=' . rawurlencode($token);
	try {
		require_once __DIR__ . '/../servicos/enviarEmail.php';
		(new SendPulseEmailService())->enviarRedefinicaoSenha(trim($email), $link);
	} catch (Throwable $erroEnvio) {
		$usuarioDAO->removerTokenRecuperacao(hash('sha256', $token));
		error_log($erroEnvio->getMessage());
		responderSolicitacaoRecuperacao(503, 'A conta foi localizada, mas não foi possível enviar o e-mail.');
	}

	responderSolicitacaoRecuperacao(200, 'E-mail de recuperação enviado. Verifique sua caixa de entrada e a pasta de spam.');
} catch (mysqli_sql_exception $erroBanco) {
	error_log('Falha no banco durante a recuperação de senha: ' . $erroBanco->getMessage());
	$codigoBanco = (int) $erroBanco->getCode();
	$mensagensBanco = [
		1044 => 'O usuário MySQL não tem permissão para acessar o banco dev_share.',
		1045 => 'O MySQL recusou o usuário ou a senha. Confira MYSQL_USER e MYSQL_PASSWORD no .env.',
		1049 => 'O banco dev_share não existe. Importe o script de criação do banco MySQL.',
		1146 => 'Uma tabela necessária não existe. Importe o script MySQL e a atualização de recuperação de senha.',
		2002 => 'Não foi possível encontrar o servidor MySQL. Confira MYSQL_HOST e se o serviço MySQL está iniciado.',
		2003 => 'Não foi possível conectar ao servidor MySQL. Confira MYSQL_HOST e se o serviço MySQL está iniciado.',
	];
	$mensagemBanco = $mensagensBanco[$codigoBanco]
		?? 'Não foi possível concluir uma consulta ao banco MySQL (código ' . $codigoBanco . '). Confira as configurações MYSQL_* e as tabelas do banco.';
	responderSolicitacaoRecuperacao(500, $mensagemBanco);
} catch (Throwable $erro) {
	error_log($erro->getMessage());
	responderSolicitacaoRecuperacao(500, 'Não foi possível concluir a solicitação.');
}