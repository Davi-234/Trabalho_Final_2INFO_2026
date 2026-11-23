<?php
require_once __DIR__ . '/../seguranca/sessao.php';
require_once __DIR__ . '/../servicos/service.php';

header('Content-Type: application/json; charset=utf-8');

function responderPerfil(int $status, array $dados): never
{
	http_response_code($status);
	echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
	exit;
}

function limparFotoPerfilLocal(?string $foto): void
{
	if ($foto === null || !preg_match('/\Aassets\/images\/perfil_[a-f0-9]{32}\.(jpg|png|webp)\z/', $foto)) {
		return;
	}

	$arquivo = dirname(__DIR__, 2) . '/' . $foto;
	if (is_file($arquivo)) {
		unlink($arquivo);
	}
}

iniciarSessaoSegura();
if (!isset($_SESSION['usuario_id']) || !is_int($_SESSION['usuario_id'])) {
	responderPerfil(401, ['sucesso' => false, 'mensagem' => 'É necessário entrar na conta.']);
}

$usuarioId = $_SESSION['usuario_id'];
$usuarioService = new UsuarioService(new UsuarioDAO());
$metodo = $_SERVER['REQUEST_METHOD'] ?? '';

try {
	if ($metodo === 'GET') {
		$usuario = $usuarioService->get($usuarioId);
		if ($usuario === null) {
			responderPerfil(404, ['sucesso' => false, 'mensagem' => 'Conta não encontrada.']);
		}

		responderPerfil(200, ['usuario' => [
			'nome' => $usuario->getNome(),
			'email' => $usuario->getEmail(),
			'link_github' => $usuario->getPerfilGithub(),
			'link_linkedin' => $usuario->getPerfilLinkedin(),
			'foto_perfil' => $usuario->getFotoPerfil(),
		]]);
	}

	if ($metodo !== 'POST') {
		responderPerfil(405, ['sucesso' => false, 'mensagem' => 'Método não permitido.']);
	}

	if (!tokenCsrfValido($_POST['csrf_token'] ?? null)) {
		responderPerfil(403, ['sucesso' => false, 'mensagem' => 'Token de segurança inválido. Atualize a página e tente novamente.']);
	}

	$acao = $_POST['acao'] ?? '';
	if ($acao === 'imagem') {
		$usuarioAtual = $usuarioService->get($usuarioId);
		if ($usuarioAtual === null) {
			responderPerfil(404, ['sucesso' => false, 'mensagem' => 'Conta não encontrada.']);
		}

		$arquivo = $_FILES['imagem'] ?? null;
		if (!is_array($arquivo) || ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
			responderPerfil(422, ['sucesso' => false, 'mensagem' => 'Selecione uma imagem válida.']);
		}
		if (($arquivo['size'] ?? 0) > 5 * 1024 * 1024 || !is_uploaded_file($arquivo['tmp_name'] ?? '')) {
			responderPerfil(422, ['sucesso' => false, 'mensagem' => 'A imagem deve ter até 5 MB.']);
		}

		$informacoesImagem = @getimagesize($arquivo['tmp_name']);
		$extensoesPermitidas = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
		$mime = is_array($informacoesImagem) ? ($informacoesImagem['mime'] ?? '') : '';
		if (!isset($extensoesPermitidas[$mime])) {
			responderPerfil(422, ['sucesso' => false, 'mensagem' => 'Use uma imagem JPG, PNG ou WebP.']);
		}

		$nomeArquivo = 'perfil_' . bin2hex(random_bytes(16)) . '.' . $extensoesPermitidas[$mime];
		$caminhoRelativo = 'assets/images/' . $nomeArquivo;
		$caminhoAbsoluto = dirname(__DIR__, 2) . '/' . $caminhoRelativo;
		if (!move_uploaded_file($arquivo['tmp_name'], $caminhoAbsoluto)) {
			responderPerfil(500, ['sucesso' => false, 'mensagem' => 'Não foi possível armazenar a imagem.']);
		}

		try {
			$usuarioService->atualizarFotoPerfil($usuarioId, $caminhoRelativo);
		} catch (Throwable $erro) {
			unlink($caminhoAbsoluto);
			throw $erro;
		}

		limparFotoPerfilLocal($usuarioAtual->getFotoPerfil());
		responderPerfil(200, [
			'sucesso' => true,
			'mensagem' => 'Imagem do perfil atualizada.',
			'foto_perfil' => $caminhoRelativo,
		]);
	}

	$senhaAtual = $_POST['senha_atual'] ?? null;
	if (!is_string($senhaAtual) || $senhaAtual === '') {
		responderPerfil(422, ['sucesso' => false, 'mensagem' => 'Informe sua senha atual para confirmar a alteração.']);
	}

	$usuario = $usuarioService->get($usuarioId);
	if ($usuario === null || $usuarioService->login($usuario->getEmail(), $senhaAtual) === null) {
		responderPerfil(403, ['sucesso' => false, 'mensagem' => 'A senha atual está incorreta.']);
	}

	if ($acao === 'excluir') {
		$usuarioService->remove($usuario);
		$_SESSION = [];
		if (ini_get('session.use_cookies')) {
			$parametros = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $parametros['path'], $parametros['domain'], $parametros['secure'], $parametros['httponly']);
		}
		session_destroy();
		responderPerfil(200, ['sucesso' => true, 'mensagem' => 'Conta excluída.']);
	}

	if ($acao !== 'editar') {
		responderPerfil(400, ['sucesso' => false, 'mensagem' => 'Ação inválida.']);
	}

	$nome = $_POST['nome'] ?? null;
	$email = $_POST['email'] ?? null;
	$linkGithub = $_POST['link_github'] ?? null;
	$linkLinkedin = $_POST['link_linkedin'] ?? null;
	$novaSenha = $_POST['nova_senha'] ?? null;
	$confirmacaoSenha = $_POST['confirmar_senha'] ?? null;
	if (
		!is_string($nome) || !is_string($email) ||
		($linkGithub !== null && !is_string($linkGithub)) ||
		($linkLinkedin !== null && !is_string($linkLinkedin)) ||
		($novaSenha !== null && !is_string($novaSenha)) ||
		($confirmacaoSenha !== null && !is_string($confirmacaoSenha))
	) {
		responderPerfil(422, ['sucesso' => false, 'mensagem' => 'Dados do perfil inválidos.']);
	}
	if (($novaSenha ?? '') !== ($confirmacaoSenha ?? '')) {
		responderPerfil(422, ['sucesso' => false, 'mensagem' => 'A confirmação da nova senha não confere.']);
	}

	$usuarioService->atualizarPerfil(
		$usuarioId,
		$nome,
		$email,
		$linkGithub ?: null,
		$linkLinkedin ?: null,
		$novaSenha ?: null
	);
	$_SESSION['usuario_nome'] = trim($nome);
	responderPerfil(200, ['sucesso' => true, 'mensagem' => 'Perfil atualizado.']);
} catch (InvalidArgumentException $erro) {
	responderPerfil(422, ['sucesso' => false, 'mensagem' => $erro->getMessage()]);
} catch (mysqli_sql_exception $erro) {
	error_log($erro->getMessage());
	if ((int) $erro->getCode() === 1451) {
		responderPerfil(409, ['sucesso' => false, 'mensagem' => 'A conta possui denúncias vinculadas e não pode ser excluída por aqui.']);
	}
	if ((int) $erro->getCode() === 1062) {
		responderPerfil(409, ['sucesso' => false, 'mensagem' => 'E-mail ou link social já está em uso.']);
	}
	responderPerfil(500, ['sucesso' => false, 'mensagem' => 'Não foi possível concluir a operação.']);
} catch (Throwable $erro) {
	error_log($erro->getMessage());
	responderPerfil(500, ['sucesso' => false, 'mensagem' => 'Não foi possível concluir a operação.']);
}