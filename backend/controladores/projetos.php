<?php
    require_once __DIR__ . "/../persistencia/dao.php";
    require_once __DIR__ . "/../servicos/service.php";
    
    function todosProjetos(): array
    {
        $projetoService = new ProjetoService(new ProjetoDAO());
        return $projetoService->getAll();
    }
?>