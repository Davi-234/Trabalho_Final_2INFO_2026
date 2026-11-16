<?php

require_once __DIR__ . "/../modelos/model.php";
require_once __DIR__ . "/../configuracao/conexao.php";

class UsuarioDAO
{
    public function add(Usuario $usuario): bool
    {
        global $conn;

        if ($stmt = $conn->prepare('INSERT INTO usuario (nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo) VALUES (?, ?, ?, ?, ?, ?, ?);')) {
            $nome = $usuario->getNome();
            $email = $usuario->getEmail();
            $senha = password_hash($usuario->getSenha(), PASSWORD_DEFAULT);
            $perfilGithub = $usuario->getPerfilGithub();
            $perfilLinkedin = $usuario->getPerfilLinkedin();
            $perfilLattes = $usuario->getPerfilLattes();
            $tipo = 'comum';

            $stmt->bind_param(
                'sssssss',
                $nome,
                $email,
                $senha,
                $perfilGithub,
                $perfilLinkedin,
                $perfilLattes,
                $tipo
            );
            $executado = $stmt->execute();
            $stmt->close();
            return $executado;
        }

        return false;
    }

    public function get(string|int $identificador): ?Usuario
    {
        global $conn;

        $stmt = (is_string($identificador))
            ? $conn->prepare('SELECT * FROM usuario WHERE email = ?')
            : $conn->prepare('SELECT * FROM usuario WHERE id = ?');

        if ($stmt === false) {
            return null;
        }

        $tipoIdentificador = is_string($identificador) ? 's' : 'i';
        $stmt->bind_param($tipoIdentificador, $identificador);
        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        }
        $user = $stmt->get_result();

        if ($user === false || $user->num_rows === 0) {
            $stmt->close();
            return null;
        }

        $dados = $user->fetch_assoc();

        $usuario_buscado = new Usuario(
            $dados['nome'],
            $dados['email'],
            $dados['senha_hash'],
            $dados['link_github'],
            $dados['link_linkedin'],
            $dados['perfil_lattes'],
            (int) $dados['id']
        );

        $usuario_buscado->setTipo(NivelUsuario::from($dados['tipo']));

        $stmt->close();

        return $usuario_buscado;
    }

    public function updatePasswordHash(int $id, string $passwordHash): bool
    {
        global $conn;

        $stmt = $conn->prepare('UPDATE usuario SET senha_hash = ? WHERE id = ?');
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param('si', $passwordHash, $id);
        $executado = $stmt->execute();
        $stmt->close();

        return $executado;
    }

    public function update(
        string|int $identificador,
        array $atributo_valor
    ): bool {
        global $conn;

        $colunasPermitidas = [
            'nome' => 'nome',
            'email' => 'email',
            'senha' => 'senha_hash',
            'senha_hash' => 'senha_hash',
            'link_github' => 'link_github',
            'link_linkedin' => 'link_linkedin',
            'perfil_lattes' => 'perfil_lattes',
            'tipo' => 'tipo',
        ];
        $atualizacoes = [];
        $valores = [];

        foreach ($atributo_valor as $atributo => $valor) {
            if (!isset($colunasPermitidas[$atributo])) {
                continue;
            }

            if ($atributo === 'senha') {
                $valor = password_hash((string) $valor, PASSWORD_DEFAULT);
            }

            $atualizacoes[] = $colunasPermitidas[$atributo] . ' = ?';
            $valores[] = $valor;
        }

        if ($atualizacoes === []) {
            return false;
        }

        $colunaIdentificador = is_string($identificador) ? 'email' : 'id';
        $sql = 'UPDATE usuario SET ' . implode(', ', $atualizacoes) . " WHERE $colunaIdentificador = ?";
        $valores[] = $identificador;
        $tipos = str_repeat('s', count($atualizacoes)) . (is_string($identificador) ? 's' : 'i');
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            return false;
        }

        $parametros = [$tipos];
        foreach ($valores as $indice => $_) {
            $parametros[] = &$valores[$indice];
        }

        $vinculado = call_user_func_array([$stmt, 'bind_param'], $parametros);
        $executado = $vinculado && $stmt->execute();
        $stmt->close();

        return $executado;
    }

    public function remove(string|int $identificador): bool
    {
        global $conn;

        $stmt = (is_string($identificador))
            ? $conn->prepare('DELETE FROM usuario WHERE email = ?')
            : $conn->prepare('DELETE FROM usuario WHERE id = ?');

        if ($stmt === false) {
            return false;
        }

        $tipoIdentificador = is_string($identificador) ? 's' : 'i';
        $stmt->bind_param($tipoIdentificador, $identificador);
        $executado = $stmt->execute();
        $removido = $executado && $stmt->affected_rows === 1;
        $stmt->close();
        return $removido;
    }
}


class ProjetoDAO
{

    public function add(Projeto $projeto): bool
    {
        global $conn;

        $stmt = $conn->prepare(
            'INSERT INTO projeto (visibilidade, data_criacao, descricao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        if ($stmt === false) {
            return false;
        }

        $visibilidade = $projeto->isPublico() ? 1 : 0;
        $dataCriacao = $projeto->getDataCriacao()->format('Y-m-d H:i:s');
        $descricao = $projeto->getDescricao();
        $nome = $projeto->getTitulo();
        $status = $projeto->getStatus()->value;
        $nivel = $projeto->getNivel()->value;
        $imagem = $projeto->getImagemProjeto();
        $views = $projeto->getViews();
        $linkRepositorio = $projeto->getLinkRepositorio();
        $usuarioId = $projeto->getDono()->getId();
        $stmt->bind_param(
            'issssssisi',
            $visibilidade,
            $dataCriacao,
            $descricao,
            $nome,
            $status,
            $nivel,
            $imagem,
            $views,
            $linkRepositorio,
            $usuarioId
        );
        $conn->begin_transaction();
        $executado = $stmt->execute();
        $projetoId = (int) $conn->insert_id;
        $stmt->close();

        if (!$executado || !$this->salvarRelacoes($projetoId, $projeto)) {
            $conn->rollback();
            return false;
        }

        $conn->commit();

        return true;
    }

    public function get(string|int $identificador): ?Projeto
    {
        global $conn;
        $stmt = is_string($identificador)
            ? $conn->prepare('SELECT * FROM projeto WHERE link_repositorio = ?')
            : $conn->prepare('SELECT * FROM projeto WHERE id = ?');
        if ($stmt === false) {
            return null;
        }

        $tipoIdentificador = is_string($identificador) ? 's' : 'i';
        $stmt->bind_param($tipoIdentificador, $identificador);
        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        }
        $resultado = $stmt->get_result();
        $dados = $resultado === false ? null : $resultado->fetch_assoc();
        $stmt->close();

        if ($dados === null) {
            return null;
        }

        $dono = (new UsuarioDAO())->get((int) $dados['usuario_id']);
        if ($dono === null) {
            return null;
        }

        $idProjeto = (int) $dados['id'];
        $tags = [];
        $stmt = $conn->prepare(
            'SELECT tag.id, tag.nome FROM tag INNER JOIN projeto_tag ON projeto_tag.tag_id = tag.id WHERE projeto_tag.projeto_id = ?'
        );
        if ($stmt === false) {
            return null;
        }
        $stmt->bind_param('i', $idProjeto);
        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        }
        $resultadoTags = $stmt->get_result();
        while ($tag = $resultadoTags->fetch_assoc()) {
            $tags[] = new Tag($tag['nome'], (int) $tag['id']);
        }
        $stmt->close();

        $tecnologias = [];
        $stmt = $conn->prepare(
            'SELECT tecnologia.id, tecnologia.nome FROM tecnologia INNER JOIN projeto_tecnologia ON projeto_tecnologia.tecnologias_id = tecnologia.id WHERE projeto_tecnologia.projeto_id = ?'
        );
        if ($stmt === false) {
            return null;
        }
        $stmt->bind_param('i', $idProjeto);
        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        }
        $resultadoTecnologias = $stmt->get_result();
        while ($tecnologia = $resultadoTecnologias->fetch_assoc()) {
            $tecnologias[] = new Tecnologia($tecnologia['nome'], (int) $tecnologia['id']);
        }
        $stmt->close();

        $projeto = new Projeto(
            $dados['nome'],
            $dados['descricao'],
            $dados['link_repositorio'],
            (bool) $dados['visibilidade'],
            $dono,
            new DateTime($dados['data_criacao']),
            StatusProjeto::from($dados['status']),
            NivelProjeto::from($dados['nivel']),
            $dados['imagem_projeto'],
            $tecnologias,
            $tags,
            $idProjeto
        );
        $projeto->setViews((int) $dados['views']);

        return $projeto;
    }

    public function update(string|int $identificador, Projeto $projeto): bool
    {
        global $conn;

        $colunaIdentificador = is_string($identificador) ? 'link_repositorio' : 'id';
        $tipoIdentificador = is_string($identificador) ? 's' : 'i';
        $stmt = $conn->prepare(
            "UPDATE projeto SET visibilidade = ?, descricao = ?, nome = ?, status = ?, nivel = ?, imagem_projeto = ?, views = ?, link_repositorio = ?, usuario_id = ? WHERE $colunaIdentificador = ?"
        );
        if ($stmt === false) {
            return false;
        }

        $visibilidade = $projeto->isPublico() ? 1 : 0;
        $descricao = $projeto->getDescricao();
        $nome = $projeto->getTitulo();
        $status = $projeto->getStatus()->value;
        $nivel = $projeto->getNivel()->value;
        $imagem = $projeto->getImagemProjeto();
        $views = $projeto->getViews();
        $linkRepositorio = $projeto->getLinkRepositorio();
        $usuarioId = $projeto->getDono()->getId();
        $tipos = 'isssssisi' . $tipoIdentificador;
        $stmt->bind_param(
            $tipos,
            $visibilidade,
            $descricao,
            $nome,
            $status,
            $nivel,
            $imagem,
            $views,
            $linkRepositorio,
            $usuarioId,
            $identificador
        );
        $conn->begin_transaction();
        $executado = $stmt->execute();
        $stmt->close();

        if (!$executado) {
            $conn->rollback();
            return false;
        }

        $resultado = is_string($identificador)
            ? $conn->prepare('SELECT id FROM projeto WHERE link_repositorio = ?')
            : $conn->prepare('SELECT id FROM projeto WHERE id = ?');
        if ($resultado === false) {
            $conn->rollback();
            return false;
        }
        $resultado->bind_param($tipoIdentificador, $identificador);
        if (!$resultado->execute()) {
            $resultado->close();
            $conn->rollback();
            return false;
        }
        $dados = $resultado->get_result()->fetch_assoc();
        $resultado->close();
        if ($dados === null || !$this->salvarRelacoes((int) $dados['id'], $projeto)) {
            $conn->rollback();
            return false;
        }

        $conn->commit();
        return true;
    }

    public function remove(Projeto $projeto): void
    {
        global $conn;

        $stmt = $conn->prepare('DELETE FROM projeto WHERE link_repositorio = ?');
        if ($stmt === false) {
            return;
        }

        $linkRepositorio = $projeto->getLinkRepositorio();
        $stmt->bind_param('s', $linkRepositorio);
        $stmt->execute();
        $stmt->close();
    }

    public function listarDoUsuario(int $usuarioId): array
    {
        global $conn;

        $stmt = $conn->prepare(
            'SELECT id, nome, descricao, visibilidade, status, nivel FROM projeto WHERE usuario_id = ? ORDER BY data_criacao DESC, id DESC'
        );
        if ($stmt === false) {
            throw new RuntimeException('Não foi possível preparar a lista de projetos.');
        }

        $stmt->bind_param('i', $usuarioId);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException('Não foi possível consultar os projetos.');
        }

        $projetos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $projetos;
    }

    public function getDoUsuario(int $projetoId, int $usuarioId): ?Projeto
    {
        global $conn;

        $stmt = $conn->prepare('SELECT id FROM projeto WHERE id = ? AND usuario_id = ?');
        if ($stmt === false) {
            return null;
        }
        $stmt->bind_param('ii', $projetoId, $usuarioId);
        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        }
        $existe = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        return $existe ? $this->get($projetoId) : null;
    }

    public function removeDoUsuario(int $projetoId, int $usuarioId): bool
    {
        global $conn;

        $stmt = $conn->prepare('DELETE FROM projeto WHERE id = ? AND usuario_id = ?');
        if ($stmt === false) {
            return false;
        }
        $stmt->bind_param('ii', $projetoId, $usuarioId);
        $executado = $stmt->execute();
        $removido = $executado && $stmt->affected_rows === 1;
        $stmt->close();
        return $removido;
    }

    private function salvarRelacoes(int $projetoId, Projeto $projeto): bool
    {
        global $conn;

        foreach ([
            ['itens' => $projeto->getTags(), 'tabela' => 'tag', 'ligacao' => 'projeto_tag', 'coluna' => 'tag_id'],
            ['itens' => $projeto->getTecnologias(), 'tabela' => 'tecnologia', 'ligacao' => 'projeto_tecnologia', 'coluna' => 'tecnologias_id'],
        ] as $relacao) {
            $stmtExcluir = $conn->prepare("DELETE FROM {$relacao['ligacao']} WHERE projeto_id = ?");
            if ($stmtExcluir === false) {
                return false;
            }
            $stmtExcluir->bind_param('i', $projetoId);
            $executado = $stmtExcluir->execute();
            $stmtExcluir->close();
            if (!$executado) {
                return false;
            }

            foreach ($relacao['itens'] as $item) {
                $nome = trim($item->getNome());
                if ($nome === '') {
                    continue;
                }

                $stmtItem = $conn->prepare(
                    "INSERT INTO {$relacao['tabela']} (nome) VALUES (?) ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)"
                );
                if ($stmtItem === false) {
                    return false;
                }
                $stmtItem->bind_param('s', $nome);
                $executado = $stmtItem->execute();
                $itemId = (int) $conn->insert_id;
                $stmtItem->close();
                if (!$executado || $itemId < 1) {
                    return false;
                }

                $stmtLigacao = $conn->prepare(
                    "INSERT INTO {$relacao['ligacao']} (projeto_id, {$relacao['coluna']}) VALUES (?, ?)"
                );
                if ($stmtLigacao === false) {
                    return false;
                }
                $stmtLigacao->bind_param('ii', $projetoId, $itemId);
                $executado = $stmtLigacao->execute();
                $stmtLigacao->close();
                if (!$executado) {
                    return false;
                }
            }
        }

        return true;
    }

    public function listarPublicos(
        string $busca,
        string $nivel,
        string $tecnologias,
        int $pagina,
        string $ordenar,
        bool $inverter
    ): array
    {
        global $conn;

        $termos = preg_split('/\s+/', trim($busca), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $termos = array_slice($termos, 0, 8);
        $condicoes = ['p.visibilidade = 1'];
        $valoresBusca = [];

        if (in_array($nivel, ['iniciante', 'intermediario', 'avancado'], true)) {
            $condicoes[] = 'p.nivel = ?';
            $valoresBusca[] = $nivel;
        }

        foreach ($termos as $termo) {
            $condicoes[] = '(p.nome LIKE ? OR p.descricao LIKE ?)';
            $padrao = '%' . $termo . '%';
            $valoresBusca[] = $padrao;
            $valoresBusca[] = $padrao;
        }

        $termosTecnologia = preg_split('/[,;]+/', trim($tecnologias), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $termosTecnologia = array_slice($termosTecnologia, 0, 5);
        foreach ($termosTecnologia as $termoTecnologia) {
            $condicoes[] = 'EXISTS (
                SELECT 1
                FROM projeto_tecnologia AS pt
                INNER JOIN tecnologia AS t ON t.id = pt.tecnologias_id
                WHERE pt.projeto_id = p.id AND t.nome LIKE ?
            )';
            $valoresBusca[] = '%' . trim(substr($termoTecnologia, 0, 50)) . '%';
        }

        $where = implode(' AND ', $condicoes);
        $stmtTotal = $conn->prepare("SELECT COUNT(*) AS total FROM projeto AS p WHERE $where");
        if ($stmtTotal === false) {
            throw new RuntimeException('Não foi possível preparar a busca de projetos.');
        }

        if (!$this->bindParametros($stmtTotal, str_repeat('s', count($valoresBusca)), $valoresBusca) || !$stmtTotal->execute()) {
            $stmtTotal->close();
            throw new RuntimeException('Não foi possível consultar os projetos.');
        }

        $total = (int) $stmtTotal->get_result()->fetch_assoc()['total'];
        $stmtTotal->close();

        $porPagina = 10;
        $totalPaginas = max(1, (int) ceil($total / $porPagina));
        $pagina = min(max(1, $pagina), $totalPaginas);
        $offset = ($pagina - 1) * $porPagina;
        $direcao = $inverter ? 'ASC' : 'DESC';

        $ordenacoes = [
            'avaliacao' => "COALESCE(a.media_nota, 0) $direcao, COALESCE(a.total_avaliacoes, 0) $direcao",
            'recentes' => "p.data_criacao $direcao",
            'views' => "p.views $direcao",
            'comentarios' => "COALESCE(c.total_comentarios, 0) $direcao",
        ];
        $orderBy = $ordenacoes[$ordenar] ?? $ordenacoes['avaliacao'];

        $sql = "SELECT
                    p.id,
                    p.nome,
                    p.descricao,
                    p.views,
                    COALESCE(ROUND(a.media_nota / 2, 1), 0) AS avaliacao,
                    COALESCE(c.total_comentarios, 0) AS comentarios
                FROM projeto AS p
                LEFT JOIN (
                    SELECT projeto_id, AVG(nota) AS media_nota, COUNT(*) AS total_avaliacoes
                    FROM avaliacao
                    GROUP BY projeto_id
                ) AS a ON a.projeto_id = p.id
                LEFT JOIN (
                    SELECT projeto_id, COUNT(*) AS total_comentarios
                    FROM comentario
                    GROUP BY projeto_id
                ) AS c ON c.projeto_id = p.id
                WHERE $where
                ORDER BY $orderBy, p.id DESC
                LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new RuntimeException('Não foi possível preparar a listagem de projetos.');
        }

        $valores = array_merge($valoresBusca, [$porPagina, $offset]);
        if (!$this->bindParametros($stmt, str_repeat('s', count($valoresBusca)) . 'ii', $valores) || !$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException('Não foi possível listar os projetos.');
        }

        $projetos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return [
            'projetos' => $projetos,
            'total' => $total,
            'pagina' => $pagina,
            'porPagina' => $porPagina,
            'totalPaginas' => $totalPaginas,
        ];
    }

    private function bindParametros(mysqli_stmt $stmt, string $tipos, array &$valores): bool
    {
        if ($tipos === '') {
            return true;
        }

        $parametros = [$tipos];
        foreach ($valores as $indice => $_) {
            $parametros[] = &$valores[$indice];
        }

        return call_user_func_array([$stmt, 'bind_param'], $parametros);
    }
}


class ComentarioDAO
{
    public function add(Comentario $comentario): void {}

    public function get(int $id): ?Comentario
    {
        return null;
    }

    public function update(
        int $id,
        Comentario $comentario
    ): void {}

    public function remove(Comentario $comentario): void {}
}


class DenunciaDAO
{
    public function add(Denuncia $denuncia): void {}

    public function get(int $id): ?Denuncia
    {

        return null;
    }

    public function update(
        int $id,
        Denuncia $denuncia
    ): void {}

    public function remove(Denuncia $denuncia): void {}
}
