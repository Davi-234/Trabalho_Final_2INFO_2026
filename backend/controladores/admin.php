<?php
require_once __DIR__ . '/../seguranca/sessao.php';
iniciarSessaoSegura();
header('Content-Type: application/json; charset=utf-8');

function responderAdmin(int $status, array $dados): void
{
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

if (!isset($_SESSION['usuario_id']) || !is_int($_SESSION['usuario_id'])) {
    responderAdmin(401, ['erro' => 'Faça login para acessar o painel.']);
}

try {
    require_once __DIR__ . '/../dao/dao.php';

    $usuario = (new UsuarioDAO())->get($_SESSION['usuario_id']);
    if ($usuario === null || $usuario->getTipo() !== NivelUsuario::ADM) {
        responderAdmin(403, ['erro' => 'Acesso permitido somente a administradores.']);
    }

    $denunciaDAO = new DenunciaDAO();
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $acao = $_GET['acao'] ?? 'listar';
        if ($acao === 'detalhe') {
            $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
            if ($id === false || $id < 1) {
                responderAdmin(400, ['erro' => 'Identificador de denúncia inválido.']);
            }
            $denuncia = $denunciaDAO->buscarParaAdmin($id);
            if ($denuncia === null) {
                responderAdmin(404, ['erro' => 'Denúncia não encontrada.']);
            }
            responderAdmin(200, [
                'denuncia' => $denuncia,
                'csrf_token' => obterTokenCsrf(),
            ]);
        }

        if ($acao !== 'listar') {
            responderAdmin(400, ['erro' => 'Ação inválida.']);
        }
        $tipo = $_GET['tipo'] ?? '';
        if (!is_string($tipo) || !in_array($tipo, ['', 'projeto', 'comentario', 'usuario'], true)) {
            responderAdmin(400, ['erro' => 'Categoria de denúncia inválida.']);
        }
        responderAdmin(200, [
            'denuncias' => $denunciaDAO->listarParaAdmin($tipo === '' ? null : $tipo),
            'csrf_token' => obterTokenCsrf(),
            'administrador' => $usuario->getNome(),
        ]);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        responderAdmin(405, ['erro' => 'Método não permitido.']);
    }
    if (!tokenCsrfValido($_POST['csrf_token'] ?? null)) {
        responderAdmin(400, ['erro' => 'A sessão expirou. Atualize a página e tente novamente.']);
    }

    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    $acao = $_POST['acao'] ?? null;
    if ($id === false || $id < 1 || !in_array($acao, ['concordar', 'ignorar'], true)) {
        responderAdmin(400, ['erro' => 'Dados da decisão inválidos.']);
    }

    $atualizada = $denunciaDAO->decidirComoAdmin($id, $acao === 'concordar');
    if (!$atualizada) {
        responderAdmin(404, ['erro' => 'A denúncia já foi analisada ou não existe.']);
    }
    responderAdmin(200, ['sucesso' => true, 'mensagem' => 'Denúncia analisada.']);
} catch (Throwable $erro) {
    error_log($erro->getMessage());
    responderAdmin(500, ['erro' => 'Não foi possível concluir a operação administrativa.']);
}
