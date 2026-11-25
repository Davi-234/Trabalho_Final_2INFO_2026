<?php
require_once __DIR__ . "/../dao/dao.php";
require_once __DIR__ . "/../servicos/service.php";

header('Content-Type: application/json; charset=utf-8');

try {
    // Limita tamanho e tipos dos filtros antes de encaminhá-los ao service.
    $busca = $_GET['q'] ?? '';
    $busca = is_string($busca) ? trim(substr($busca, 0, 200)) : '';

    $nivel = $_GET['nivel'] ?? '';
    $nivel = is_string($nivel) ? $nivel : '';

    $tecnologias = $_GET['tecnologias'] ?? '';
    $tecnologias = is_string($tecnologias) ? trim(substr($tecnologias, 0, 200)) : '';

    $pagina = filter_var($_GET['pagina'] ?? 1, FILTER_VALIDATE_INT);
    $pagina = $pagina !== false && $pagina > 0 ? $pagina : 1;

    $ordenar = $_GET['ordenar'] ?? 'avaliacao';
    $ordenar = is_string($ordenar) ? $ordenar : 'avaliacao';
    $inverter = ($_GET['inverter'] ?? '') === '1';

    $projetoService = new ProjetoService(new ProjetoDAO());
    $resultado = $projetoService->listarPublicos($busca, $nivel, $tecnologias, $pagina, $ordenar, $inverter);

    // O service/DAO já entrega os dados no formato consumido pela tela de busca.
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
} catch (InvalidArgumentException $erro) {
    // Filtros inválidos são erro do pedido, não falha interna do servidor.
    http_response_code(400);
    echo json_encode(['erro' => $erro->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $erro) {
    http_response_code(500);
    echo json_encode(['erro' => 'Não foi possível carregar os projetos.'], JSON_UNESCAPED_UNICODE);
}
?>