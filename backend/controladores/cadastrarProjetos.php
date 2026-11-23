<?php
require_once __DIR__ . '/../seguranca/sessao.php';
require_once __DIR__ . '/../dao/dao.php';
require_once __DIR__ . '/../servicos/service.php';

header('Content-Type: application/json; charset=utf-8');

function responderProjeto(int $status, array $dados): never
{
	http_response_code($status);
	echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
	exit;
}

function limparImagemProjeto(?string $imagem): void
{
	if ($imagem === null || !preg_match('/\Aassets\/images\/[a-f0-9]{32}\.(jpg|png|webp|gif)\z/', $imagem)) {
		return;
	}

	$arquivo = dirname(__DIR__, 2) . '/' . $imagem;
	if (is_file($arquivo)) {
		unlink($arquivo);
	}
}

iniciarSessaoSegura();
if (!isset($_SESSION['usuario_id']) || !is_int($_SESSION['usuario_id'])) {
	responderProjeto(401, ['sucesso' => false, 'mensagem' => 'É necessário entrar na conta.']);
}

$usuarioId = $_SESSION['usuario_id'];
$projetoDAO = new ProjetoDAO();
$projetoService = new ProjetoService($projetoDAO);
$metodo = $_SERVER['REQUEST_METHOD'] ?? '';
$novoArquivoProjeto = null;

try {
	if ($metodo === 'GET') {
		if (($_GET['acao'] ?? '') === 'listar') {
			responderProjeto(200, ['projetos' => $projetoService->listarDoUsuario($usuarioId)]);
		}

		$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
		if ($id === false || $id === null || $id < 1) {
			responderProjeto(400, ['sucesso' => false, 'mensagem' => 'Identificador do projeto inválido.']);
		}

		$projeto = $projetoService->getDoUsuario($id, $usuarioId);
		if ($projeto === null) {
			responderProjeto(404, ['sucesso' => false, 'mensagem' => 'Projeto não encontrado.']);
		}

		responderProjeto(200, ['projeto' => [
			'id' => $projeto->getId(),
			'titulo' => $projeto->getTitulo(),
			'descricao' => $projeto->getDescricao(),
			'link' => $projeto->getLinkRepositorio(),
			'visibilidade' => $projeto->isPublico() ? 'publico' : 'privado',
			'nivel' => $projeto->getNivel()->value,
			'status' => $projeto->getStatus()->value,
			'imagem' => $projeto->getImagemProjeto(),
			'tecnologias' => array_map(static fn(Tecnologia $item): string => $item->getNome(), $projeto->getTecnologias()),
			'tags' => array_map(static fn(Tag $item): string => $item->getNome(), $projeto->getTags()),
		]]);
	}

	if ($metodo !== 'POST') {
		responderProjeto(405, ['sucesso' => false, 'mensagem' => 'Método não permitido.']);
	}

	if (!tokenCsrfValido($_POST['csrf_token'] ?? null)) {
		responderProjeto(403, ['sucesso' => false, 'mensagem' => 'Token de segurança inválido. Atualize a página e tente novamente.']);
	}

	$acao = $_POST['acao'] ?? '';
	$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
	if ($acao === 'excluir') {
		if ($id === false || $id === null || $id < 1) {
			responderProjeto(400, ['sucesso' => false, 'mensagem' => 'Identificador do projeto inválido.']);
		}
		$projetoRemovido = $projetoService->getDoUsuario($id, $usuarioId);
		if ($projetoRemovido === null) {
			responderProjeto(404, ['sucesso' => false, 'mensagem' => 'Projeto não encontrado ou não pertence à sua conta.']);
		}
		if (!$projetoService->removeDoUsuario($id, $usuarioId)) {
			responderProjeto(404, ['sucesso' => false, 'mensagem' => 'Projeto não encontrado ou não pertence à sua conta.']);
		}
		limparImagemProjeto($projetoRemovido->getImagemProjeto());
		responderProjeto(200, ['sucesso' => true, 'mensagem' => 'Projeto excluído.']);
	}

	if (!in_array($acao, ['criar', 'editar'], true)) {
		responderProjeto(400, ['sucesso' => false, 'mensagem' => 'Ação inválida.']);
	}
	if ($acao === 'editar' && ($id === false || $id === null || $id < 1)) {
		responderProjeto(400, ['sucesso' => false, 'mensagem' => 'Identificador do projeto inválido.']);
	}

	$titulo = $_POST['titulo'] ?? null;
	$descricao = $_POST['descricao'] ?? null;
	$link = $_POST['link'] ?? null;
	$nivelValor = $_POST['nivel'] ?? null;
	$statusValor = $_POST['status'] ?? null;
	$visibilidadeValor = $_POST['visibilidade'] ?? null;
	if (!is_string($titulo) || !is_string($descricao) || !is_string($link) || !is_string($nivelValor) || !is_string($statusValor) || !is_string($visibilidadeValor)) {
		responderProjeto(422, ['sucesso' => false, 'mensagem' => 'Preencha todos os campos obrigatórios.']);
	}

	$titulo = trim($titulo);
	$descricao = trim($descricao);
	$link = trim($link);
	if ($titulo === '' || strlen($titulo) > 200 || $descricao === '' || strlen($descricao) > 500 || !filter_var($link, FILTER_VALIDATE_URL)) {
		responderProjeto(422, ['sucesso' => false, 'mensagem' => 'Confira o título, a descrição e o link do projeto.']);
	}

	try {
		$nivel = NivelProjeto::from($nivelValor);
		$status = StatusProjeto::from($statusValor);
	} catch (ValueError) {
		responderProjeto(422, ['sucesso' => false, 'mensagem' => 'Nível ou status inválido.']);
	}
	if (!in_array($visibilidadeValor, ['publico', 'privado'], true)) {
		responderProjeto(422, ['sucesso' => false, 'mensagem' => 'Visibilidade inválida.']);
	}

	$separarItens = static function (mixed $valor): array {
		if (!is_string($valor)) {
			return [];
		}
		$itens = preg_split('/[,;]+/', $valor, -1, PREG_SPLIT_NO_EMPTY) ?: [];
		$itens = array_map(static fn(string $item): string => trim($item), $itens);
		$itens = array_filter($itens, static fn(string $item): bool => $item !== '');
		return array_slice(array_values(array_unique($itens)), 0, 20);
	};
	$tecnologias = array_map(static fn(string $nome): Tecnologia => new Tecnologia($nome), $separarItens($_POST['tecnologias'] ?? ''));
	$tags = array_map(static fn(string $nome): Tag => new Tag($nome), $separarItens($_POST['tags'] ?? ''));
	if ($tecnologias === [] || $tags === [] || array_filter($tecnologias, static fn(Tecnologia $item): bool => strlen($item->getNome()) > 50) || array_filter($tags, static fn(Tag $item): bool => strlen($item->getNome()) > 30)) {
		responderProjeto(422, ['sucesso' => false, 'mensagem' => 'Informe tecnologias e tags válidas (até 50 e 30 caracteres, respectivamente).']);
	}

	$usuario = (new UsuarioDAO())->get($usuarioId);
	if ($usuario === null) {
		responderProjeto(401, ['sucesso' => false, 'mensagem' => 'Conta não encontrada.']);
	}

	$projetoAtual = null;
	if ($acao === 'editar') {
		$projetoAtual = $projetoService->getDoUsuario($id, $usuarioId);
		if ($projetoAtual === null) {
			responderProjeto(404, ['sucesso' => false, 'mensagem' => 'Projeto não encontrado ou não pertence à sua conta.']);
		}
	}

	$imagemAnterior = $projetoAtual?->getImagemProjeto();
	$imagem = $imagemAnterior;
	if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
		$arquivo = $_FILES['imagem'];
		if ($arquivo['error'] !== UPLOAD_ERR_OK || $arquivo['size'] > 5 * 1024 * 1024 || !is_uploaded_file($arquivo['tmp_name'])) {
			responderProjeto(422, ['sucesso' => false, 'mensagem' => 'A imagem deve ter até 5 MB.']);
		}
		$imagemInfo = @getimagesize($arquivo['tmp_name']);
		$extensoes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
		$mime = is_array($imagemInfo) ? ($imagemInfo['mime'] ?? '') : '';
		if (!isset($extensoes[$mime])) {
			responderProjeto(422, ['sucesso' => false, 'mensagem' => 'Envie uma imagem JPG, PNG, WebP ou GIF válida.']);
		}
		$nomeArquivo = bin2hex(random_bytes(16)) . '.' . $extensoes[$mime];
		$destino = dirname(__DIR__, 2) . '/assets/images/' . $nomeArquivo;
		if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
			responderProjeto(500, ['sucesso' => false, 'mensagem' => 'Não foi possível armazenar a imagem.']);
		}
		$imagem = 'assets/images/' . $nomeArquivo;
		$novoArquivoProjeto = $imagem;
	}

	$projeto = new Projeto(
		$titulo,
		$descricao,
		$link,
		$visibilidadeValor === 'publico',
		$usuario,
		$projetoAtual?->getDataCriacao() ?? new DateTime(),
		$status,
		$nivel,
		$imagem,
		$tecnologias,
		$tags,
		$id > 0 ? $id : null
	);

	if ($acao === 'criar') {
		$projetoService->add($projeto);
		responderProjeto(201, ['sucesso' => true, 'mensagem' => 'Projeto criado.']);
	}

	$projetoService->update($id, $projeto);
	if ($imagemAnterior !== $imagem) {
		limparImagemProjeto($imagemAnterior);
	}
	responderProjeto(200, ['sucesso' => true, 'mensagem' => 'Projeto atualizado.']);
} catch (mysqli_sql_exception $erro) {
	error_log($erro->getMessage());
	limparImagemProjeto($novoArquivoProjeto);
	$status = (int) $erro->getCode() === 1451 ? 409 : 500;
	$mensagem = $status === 409
		? 'Este projeto possui denúncias vinculadas e não pode ser excluído.'
		: 'Não foi possível salvar a alteração do projeto.';
	responderProjeto($status, ['sucesso' => false, 'mensagem' => $mensagem]);
} catch (Throwable $erro) {
	error_log($erro->getMessage());
	limparImagemProjeto($novoArquivoProjeto);
	responderProjeto(500, ['sucesso' => false, 'mensagem' => 'Não foi possível concluir a operação.']);
}
