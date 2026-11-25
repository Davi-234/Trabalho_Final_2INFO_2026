<?php

require_once __DIR__ . '/../modelos/model.php';
require_once __DIR__ . '/../configuracao/conexao.php';

    /**
     * Cadastra um usuário, armazenando a senha como hash.
     * Retorna false se não for possível preparar ou executar o INSERT.
     */
class UsuarioDAO {
    public function add(Usuario $usuario): bool
    {
        global $conn;

        $stmt = $conn->prepare(
            'INSERT INTO usuario
            (nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo)
            VALUES (?, ?, ?, ?, ?, ?, ?)'
        );

        if (!$stmt) {
            return false;
        }

        $nome = $usuario->getNome();
        $email = $usuario->getEmail();
        $senha = password_hash($usuario->getSenha(), PASSWORD_DEFAULT);
        $github = $usuario->getPerfilGithub();
        $linkedin = $usuario->getPerfilLinkedin();
        $lattes = $usuario->getPerfilLattes();
        $tipo = $usuario->getTipo()->value;

        $stmt->bind_param(
            'sssssss',
            $nome,
            $email,
            $senha,
            $github,
            $linkedin,
            $lattes,
            $tipo
        );

        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    /**
     * Busca um usuário pelo e-mail (string) ou pelo ID (int).
     * Retorna null quando não encontra uma conta ou a consulta falha.
     */
    public function get(string|int $identificador): ?Usuario
    {
        global $conn;

        $stmt = is_string($identificador)
            ? $conn->prepare('SELECT * FROM usuario WHERE email = ?')
            : $conn->prepare('SELECT * FROM usuario WHERE id = ?');

        if (!$stmt) {
            return null;
        }

        $tipo = is_string($identificador) ? 's' : 'i';
        $stmt->bind_param($tipo, $identificador);

        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        }

        $dados = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$dados) {
            return null;
        }

        $usuario = new Usuario(
            $dados['nome'],
            $dados['email'],
            $dados['senha_hash'],
            $dados['link_github'],
            $dados['link_linkedin'],
            $dados['perfil_lattes'],
            (int) $dados['id'],
            $dados['foto_perfil'] ?? null
        );

        $usuario->setTipo(NivelUsuario::from($dados['tipo']));

        return $usuario;
    }

    /**
     * Atualiza o caminho da foto de perfil de um usuário.
     * Retorna se o UPDATE foi executado com sucesso.
     */
    public function atualizarFotoPerfil(int $id, string $caminho): bool
    {
        global $conn;

        $stmt = $conn->prepare('UPDATE usuario SET foto_perfil = ? WHERE id = ?');
        if ($stmt === false) {
            return false;
        }
        $stmt->bind_param('si', $caminho, $id);
        $executado = $stmt->execute();
        $stmt->close();
        return $executado;
    }

    /**
     * Substitui o hash de senha de uma conta identificada pelo ID.
     * Usado na migração de hashes antigos e na atualização de senha.
     */
    public function updatePasswordHash(int $id, string $passwordHash): bool
    {
        global $conn;

        $stmt = $conn->prepare(
            'UPDATE usuario SET senha_hash = ? WHERE id = ?'
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('si', $passwordHash, $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    /**
     * Remove tokens antigos e registra um novo token de recuperação.
     * Usa transação para não deixar a tabela parcialmente alterada.
     */
    public function criarTokenRecuperacao(int $usuarioId, string $tokenHash, DateTimeImmutable $expiraEm): bool
    {
        global $conn;

        $conn->begin_transaction();
        $stmt = $conn->prepare('DELETE FROM recuperacao_senha WHERE usuario_id = ? OR expira_em <= CURRENT_TIMESTAMP');
        if ($stmt === false) {
            $conn->rollback();
            return false;
        }
        $stmt->bind_param('i', $usuarioId);
        $executado = $stmt->execute();
        $stmt->close();
        if (!$executado) {
            $conn->rollback();
            return false;
        }

        $expiracao = $expiraEm->format('Y-m-d H:i:s');
        $stmt = $conn->prepare('INSERT INTO recuperacao_senha (usuario_id, token_hash, expira_em) VALUES (?, ?, ?)');
        if ($stmt === false) {
            $conn->rollback();
            return false;
        }
        $stmt->bind_param('iss', $usuarioId, $tokenHash, $expiracao);
        $executado = $stmt->execute();
        $stmt->close();
        if (!$executado) {
            $conn->rollback();
            return false;
        }

        $conn->commit();
        return true;
    }

    /**
     * Apaga o token de recuperação que corresponda ao hash informado.
     */
    public function removerTokenRecuperacao(string $tokenHash): void
    {
        global $conn;

        $stmt = $conn->prepare('DELETE FROM recuperacao_senha WHERE token_hash = ?');
        if ($stmt === false) {
            return;
        }
        $stmt->bind_param('s', $tokenHash);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Valida e consome um token, atualiza a senha e remove tokens da conta.
     * As operações são confirmadas juntas ou desfeitas pela transação.
     */
    public function redefinirSenha(string $tokenHash, string $passwordHash): bool
    {
        global $conn;

        $conn->begin_transaction();
        $stmt = $conn->prepare(
            'SELECT usuario_id FROM recuperacao_senha WHERE token_hash = ? AND expira_em > CURRENT_TIMESTAMP FOR UPDATE'
        );
        if ($stmt === false) {
            $conn->rollback();
            return false;
        }
        $stmt->bind_param('s', $tokenHash);
        $executado = $stmt->execute();
        $resultado = $executado ? $stmt->get_result()->fetch_assoc() : null;
        $stmt->close();
        if ($resultado === null) {
            $conn->rollback();
            return false;
        }

        $usuarioId = (int) $resultado['usuario_id'];
        $stmt = $conn->prepare('UPDATE usuario SET senha_hash = ? WHERE id = ?');
        if ($stmt === false) {
            $conn->rollback();
            return false;
        }
        $stmt->bind_param('si', $passwordHash, $usuarioId);
        $executado = $stmt->execute();
        $stmt->close();
        if (!$executado) {
            $conn->rollback();
            return false;
        }

        $stmt = $conn->prepare('DELETE FROM recuperacao_senha WHERE usuario_id = ?');
        if ($stmt === false) {
            $conn->rollback();
            return false;
        }
        $stmt->bind_param('i', $usuarioId);
        $executado = $stmt->execute();
        $stmt->close();
        if (!$executado) {
            $conn->rollback();
            return false;
        }

        $conn->commit();
        return true;
    }

    /**
     * Atualiza apenas os campos de usuário incluídos na lista permitida.
     * O identificador pode ser o e-mail (string) ou o ID (int).
     */
    public function update(
        string|int $identificador,
        array $atributo_valor
    ): bool {
        global $conn;

        $permitidas = [
            'nome' => 'nome',
            'email' => 'email',
            'senha' => 'senha_hash',
            'senha_hash' => 'senha_hash',
            'link_github' => 'link_github',
            'link_linkedin' => 'link_linkedin',
            'perfil_lattes' => 'perfil_lattes',
            'tipo' => 'tipo'
        ];

        $sets = [];
        $valores = [];
        $tipos = '';

        foreach ($atributo_valor as $atributo => $valor) {
            if (!isset($permitidas[$atributo])) {
                continue;
            }

            if ($atributo === 'senha') {
                $valor = password_hash((string) $valor, PASSWORD_DEFAULT);
            }

            if ($valor instanceof BackedEnum) {
                $valor = $valor->value;
            }

            $sets[] = $permitidas[$atributo] . ' = ?';
            $valores[] = $valor;
            $tipos .= 's';
        }

        if (!$sets) {
            return false;
        }

        $coluna = is_string($identificador) ? 'email' : 'id';
        $tipos .= is_string($identificador) ? 's' : 'i';
        $valores[] = $identificador;

        $stmt = $conn->prepare(
            'UPDATE usuario SET ' . implode(', ', $sets) . " WHERE $coluna = ?"
        );

        if (!$stmt) {
            return false;
        }

        $bind = [$tipos];
        foreach ($valores as $i => &$valor) {
            $bind[] = &$valor;
        }

        $okBind = call_user_func_array([$stmt, 'bind_param'], $bind);
        $ok = $okBind && $stmt->execute();
        $stmt->close();

        return $ok;
    }

    /**
     * Exclui um usuário pelo e-mail (string) ou pelo ID (int).
     * Retorna se o comando DELETE foi executado.
     */
    public function remove(string|int $identificador): bool
    {
        global $conn;

        $coluna = is_string($identificador) ? 'email' : 'id';
        $tipo = is_string($identificador) ? 's' : 'i';

        $stmt = $conn->prepare("DELETE FROM usuario WHERE $coluna = ?");
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param($tipo, $identificador);
        $removido = $stmt->execute();
        $stmt->close();
        return $removido;
    }
}

class ProjetoDAO
{
    /**
     * Lista projetos públicos filtrados, ordenados e paginados.
     * Retorna projetos e metadados de paginação no formato usado pelo controller.
     */
    public function listarPublicos(
        string $busca,
        string $nivel,
        string $tecnologias,
        int $pagina,
        string $ordenar,
        bool $inverter
    ): array {
        global $conn;

        $condicoes = ['p.visibilidade = 1'];
        $valores = [];
        $tipos = '';

        if ($busca !== '') {
            $condicoes[] = '(p.nome LIKE ? OR p.descricao LIKE ?)';
            $termoBusca = '%' . $busca . '%';
            $valores[] = $termoBusca;
            $valores[] = $termoBusca;
            $tipos .= 'ss';
        }
        if ($nivel !== '') {
            $condicoes[] = 'p.nivel = ?';
            $valores[] = $nivel;
            $tipos .= 's';
        }
        if ($tecnologias !== '') {
            $condicoes[] = 'EXISTS (
                SELECT 1
                FROM projeto_tecnologia pt
                INNER JOIN tecnologia t ON t.id = pt.tecnologias_id
                WHERE pt.projeto_id = p.id AND t.nome LIKE ?
            )';
            $valores[] = '%' . $tecnologias . '%';
            $tipos .= 's';
        }

        $where = implode(' AND ', $condicoes);
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM projeto p WHERE $where");
        if (!$stmt) {
            throw new RuntimeException('Não foi possível preparar a busca de projetos.');
        }
        $this->bindValores($stmt, $tipos, $valores);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException('Não foi possível contar os projetos.');
        }
        $total = (int) $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();

        $porPagina = 10;
        $totalPaginas = max(1, (int) ceil($total / $porPagina));
        $pagina = min($pagina, $totalPaginas);
        $offset = ($pagina - 1) * $porPagina;

        $colunasOrdenacao = [
            'recentes' => 'p.data_criacao',
            'views' => 'p.views',
            'avaliacao' => 'COALESCE(a.avaliacao, 0)',
            'comentarios' => 'COALESCE(c.comentarios, 0)',
        ];
        if (!isset($colunasOrdenacao[$ordenar])) {
            throw new InvalidArgumentException('Critério de ordenação inválido.');
        }
        $direcao = $inverter ? 'ASC' : 'DESC';
        $valoresPagina = [...$valores, $porPagina, $offset];
        $tiposPagina = $tipos . 'ii';

        $sql = "SELECT
                    p.id,
                    p.nome,
                    p.descricao,
                    p.views,
                    COALESCE(c.comentarios, 0) AS comentarios,
                    COALESCE(a.avaliacao, 0) AS avaliacao
                FROM projeto p
                LEFT JOIN (
                    SELECT projeto_id, COUNT(*) AS comentarios
                    FROM comentario
                    GROUP BY projeto_id
                ) c ON c.projeto_id = p.id
                LEFT JOIN (
                    SELECT projeto_id, AVG(nota) / 2 AS avaliacao
                    FROM avaliacao
                    GROUP BY projeto_id
                ) a ON a.projeto_id = p.id
                WHERE $where
                ORDER BY {$colunasOrdenacao[$ordenar]} $direcao, p.id DESC
                LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException('Não foi possível preparar a listagem de projetos.');
        }
        $this->bindValores($stmt, $tiposPagina, $valoresPagina);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException('Não foi possível carregar os projetos.');
        }

        $projetos = [];
        $resultado = $stmt->get_result();
        while ($linha = $resultado->fetch_assoc()) {
            $projetos[] = [
                'id' => (int) $linha['id'],
                'nome' => $linha['nome'],
                'descricao' => $linha['descricao'],
                'views' => (int) $linha['views'],
                'comentarios' => (int) $linha['comentarios'],
                'avaliacao' => round((float) $linha['avaliacao'], 1),
            ];
        }
        $stmt->close();

        return [
            'projetos' => $projetos,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
        ];
    }

    /**
     * Lista os dados resumidos dos projetos pertencentes ao usuário informado.
     */
    public function listarDoUsuario(int $usuarioId): array
    {
        global $conn;

        $stmt = $conn->prepare(
            'SELECT id, nome, descricao, visibilidade, views
             FROM projeto
             WHERE usuario_id = ?
             ORDER BY data_criacao DESC, id DESC'
        );
        if (!$stmt) {
            throw new RuntimeException('Não foi possível preparar a listagem dos projetos.');
        }
        $stmt->bind_param('i', $usuarioId);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException('Não foi possível carregar os projetos da conta.');
        }

        $projetos = [];
        $resultado = $stmt->get_result();
        while ($linha = $resultado->fetch_assoc()) {
            $projetos[] = [
                'id' => (int) $linha['id'],
                'nome' => $linha['nome'],
                'descricao' => $linha['descricao'],
                'visibilidade' => (bool) $linha['visibilidade'],
                'views' => (int) $linha['views'],
            ];
        }
        $stmt->close();
        return $projetos;
    }

    /**
     * Busca o projeto pelo link apenas se ele estiver público.
     * Retorna o objeto completo ou null se não existir/não estiver visível.
     */
    public function getPublico(string $linkRepositorio): ?Projeto
    {
        global $conn;

        $stmt = $conn->prepare(
            'SELECT id FROM projeto WHERE link_repositorio = ? AND visibilidade = 1'
        );
        if (!$stmt) {
            throw new RuntimeException('Não foi possível buscar o projeto.');
        }
        $stmt->bind_param('s', $linkRepositorio);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException('Não foi possível buscar o projeto.');
        }
        $linha = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$linha) {
            return null;
        }

        $projeto = $this->get((int) $linha['id']);
        return $projeto !== null && $projeto->isPublico() ? $projeto : null;
    }

    /**
     * Busca um projeto somente quando seu ID e o ID do dono correspondem.
     */
    public function getDoUsuario(int $projetoId, int $usuarioId): ?Projeto
    {
        global $conn;

        $stmt = $conn->prepare(
            'SELECT id FROM projeto WHERE id = ? AND usuario_id = ?'
        );
        if (!$stmt) {
            throw new RuntimeException('Não foi possível buscar o projeto.');
        }
        $stmt->bind_param('ii', $projetoId, $usuarioId);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException('Não foi possível buscar o projeto.');
        }
        $linha = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $linha ? $this->get((int) $linha['id']) : null;
    }

    /**
     * Exclui um projeto apenas se pertencer ao usuário informado.
     * Retorna false se nenhum projeto correspondente foi removido.
     */
    public function removeDoUsuario(int $projetoId, int $usuarioId): bool
    {
        global $conn;

        $stmt = $conn->prepare(
            'DELETE FROM projeto WHERE id = ? AND usuario_id = ?'
        );
        if (!$stmt) {
            throw new RuntimeException('Não foi possível preparar a exclusão do projeto.');
        }
        $stmt->bind_param('ii', $projetoId, $usuarioId);
        $executado = $stmt->execute();
        $removido = $executado && $stmt->affected_rows > 0;
        $stmt->close();
        return $removido;
    }

    /**
     * Atualiza campos e relacionamentos de um projeto pertencente ao usuário.
     */
    public function updateDoUsuario(int $projetoId, int $usuarioId, array $valores): bool
    {
        return $this->atualizar($projetoId, $valores, $usuarioId);
    }

    /**
     * Vincula dinamicamente valores a um statement usando os tipos do mysqli.
     * Recebe os valores por referência porque bind_param exige essa forma.
     */
    private function bindValores(mysqli_stmt $stmt, string $tipos, array &$valores): void
    {
        if ($tipos === '') {
            return;
        }

        $argumentos = [$tipos];
        foreach ($valores as $indice => &$valor) {
            $argumentos[] = &$valor;
        }
        if (!call_user_func_array([$stmt, 'bind_param'], $argumentos)) {
            $stmt->close();
            throw new RuntimeException('Não foi possível vincular os filtros da busca.');
        }
    }

    private function salvarRelacionamentos(
        int $projetoId,
        array $itens,
        string $tabela,
        string $tabelaRelacao,
        string $colunaRelacao
    ): bool {
        global $conn;

        $stmtItem = $conn->prepare(
            "INSERT INTO $tabela (nome)
             VALUES (?)
             ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)"
        );

        $stmtRelacao = $conn->prepare(
            "INSERT IGNORE INTO $tabelaRelacao (projeto_id, $colunaRelacao)
             VALUES (?, ?)"
        );

        if (!$stmtItem || !$stmtRelacao) {
            if ($stmtItem) $stmtItem->close();
            if ($stmtRelacao) $stmtRelacao->close();
            return false;
        }

        foreach ($itens as $item) {
            if (!is_object($item) || !method_exists($item, 'getNome')) {
                $stmtItem->close();
                $stmtRelacao->close();
                return false;
            }

            $nome = $item->getNome();
            $stmtItem->bind_param('s', $nome);

            if (!$stmtItem->execute()) {
                $stmtItem->close();
                $stmtRelacao->close();
                return false;
            }

            // LAST_INSERT_ID(id) devolve o ID existente em caso de duplicidade.
            $itemId = (int) $conn->insert_id;

            $stmtRelacao->bind_param('ii', $projetoId, $itemId);

            if (!$stmtRelacao->execute()) {
                $stmtItem->close();
                $stmtRelacao->close();
                return false;
            }
        }

        $stmtItem->close();
        $stmtRelacao->close();

        return true;
    }

    /**
     * Insere um projeto e suas tags/tecnologias em uma única transação.
     * Retorna false e desfaz tudo se qualquer parte da operação falhar.
     */
    public function add(Projeto $projeto): bool
    {
        global $conn;

        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare(
                'INSERT INTO projeto
                (visibilidade, data_criacao, descricao, nome, status, nivel,
                 imagem_projeto, views, link_repositorio, usuario_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );

            if (!$stmt) {
                throw new RuntimeException($conn->error);
            }

            $visibilidade = $projeto->isPublico() ? 1 : 0;
            $data = $projeto->getDataCriacao()->format('Y-m-d H:i:s');
            $descricao = $projeto->getDescricao();
            $nome = $projeto->getTitulo();
            $status = $projeto->getStatus()->value;
            $nivel = $projeto->getNivel()->value;
            $imagem = $projeto->getImagemProjeto();
            $views = $projeto->getViews();
            $link = $projeto->getLinkRepositorio();
            $usuarioId = $projeto->getDono()->getId();

            $stmt->bind_param(
                'issssssisi',
                $visibilidade,
                $data,
                $descricao,
                $nome,
                $status,
                $nivel,
                $imagem,
                $views,
                $link,
                $usuarioId
            );

            if (!$stmt->execute()) {
                throw new RuntimeException($stmt->error);
            }

            $projetoId = (int) $conn->insert_id;
            $stmt->close();

            if (!$this->salvarRelacionamentos(
                $projetoId,
                $projeto->getTags(),
                'tag',
                'projeto_tag',
                'tag_id'
            )) {
                throw new RuntimeException('Falha ao salvar tags.');
            }

            if (!$this->salvarRelacionamentos(
                $projetoId,
                $projeto->getTecnologias(),
                'tecnologia',
                'projeto_tecnologia',
                'tecnologias_id'
            )) {
                throw new RuntimeException('Falha ao salvar tecnologias.');
            }

            $conn->commit();
            return true;
        } catch (Throwable $e) {
            $conn->rollback();
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Busca um projeto por link (string) ou ID (int), carregando dono e relações.
     * Retorna null quando o projeto ou algum dado essencial não é encontrado.
     */
    public function get(string|int $identificador): ?Projeto
    {
        global $conn;

        $stmt = is_string($identificador)
            ? $conn->prepare('SELECT * FROM projeto WHERE link_repositorio = ?')
            : $conn->prepare('SELECT * FROM projeto WHERE id = ?');

        if (!$stmt) {
            return null;
        }

        $tipo = is_string($identificador) ? 's' : 'i';
        $stmt->bind_param($tipo, $identificador);

        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        }

        $dados = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$dados) {
            return null;
        }

        $id = (int) $dados['id'];
        $dono = (new UsuarioDAO())->get((int) $dados['usuario_id']);

        if (!$dono) {
            return null;
        }

        $tags = [];
        $stmt = $conn->prepare(
            'SELECT t.id, t.nome
             FROM tag t
             INNER JOIN projeto_tag pt ON pt.tag_id = t.id
             WHERE pt.projeto_id = ?'
        );

        if (!$stmt) return null;

        $stmt->bind_param('i', $id);
        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        }

        $resultado = $stmt->get_result();
        while ($linha = $resultado->fetch_assoc()) {
            $tags[] = new Tag($linha['nome'], (int) $linha['id']);
        }
        $stmt->close();

        $tecnologias = [];
        $stmt = $conn->prepare(
            'SELECT t.id, t.nome
             FROM tecnologia t
             INNER JOIN projeto_tecnologia pt
                 ON pt.tecnologias_id = t.id
             WHERE pt.projeto_id = ?'
        );

        if (!$stmt) return null;

        $stmt->bind_param('i', $id);
        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        }

        $resultado = $stmt->get_result();
        while ($linha = $resultado->fetch_assoc()) {
            $tecnologias[] = new Tecnologia(
                $linha['nome'],
                (int) $linha['id']
            );
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
            $id
        );

        $projeto->setViews((int) $dados['views']);

        if (!empty($dados['data_atualizacao'])) {
            $projeto->setDataAtualizacao(
                new DateTime($dados['data_atualizacao'])
            );
        }

        return $projeto;
    }

    /**
     * Atualiza campos e relações do projeto sem exigir uma verificação de dono.
     * Para alterações de usuário, prefira updateDoUsuario().
     */
    public function update(string|int $identificador, array $valores): bool
    {
        return $this->atualizar($identificador, $valores, null);
    }

    /**
     * Executa a atualização comum; quando recebe usuário, restringe a operação ao dono.
     * Campos e relações são gravados dentro da mesma transação.
     */
    private function atualizar(string|int $identificador, array $valores, ?int $usuarioId): bool
    {
        global $conn;

        $permitidas = [
            'visibilidade' => 'visibilidade',
            'descricao' => 'descricao',
            'nome' => 'nome',
            'titulo' => 'nome',
            'status' => 'status',
            'nivel' => 'nivel',
            'imagem_projeto' => 'imagem_projeto',
            'views' => 'views',
            'link_repositorio' => 'link_repositorio'
        ];

        $sets = [];
        $params = [];
        $tipos = '';
        $tags = null;
        $tecnologias = null;

        foreach ($valores as $atributo => $valor) {
            if ($atributo === 'tags') {
                $tags = $valor;
                continue;
            }

            if ($atributo === 'tecnologias') {
                $tecnologias = $valor;
                continue;
            }

            if (!isset($permitidas[$atributo])) {
                continue;
            }

            if ($valor instanceof BackedEnum) {
                $valor = $valor->value;
            }

            if ($atributo === 'visibilidade' || $atributo === 'views') {
                $valor = (int) $valor;
                $tipos .= 'i';
            } else {
                $tipos .= 's';
            }

            $sets[] = $permitidas[$atributo] . ' = ?';
            $params[] = $valor;
        }

        if (!$sets && $tags === null && $tecnologias === null) {
            return false;
        }

        $colunaId = is_string($identificador) ? 'link_repositorio' : 'id';
        $tipoId = is_string($identificador) ? 's' : 'i';
        $condicaoUsuario = $usuarioId === null ? '' : ' AND usuario_id = ?';

        $conn->begin_transaction();

        try {
            if ($sets) {
                $params[] = $identificador;
                $tipos .= $tipoId;
                if ($usuarioId !== null) {
                    $params[] = $usuarioId;
                    $tipos .= 'i';
                }

                $stmt = $conn->prepare(
                    'UPDATE projeto SET ' . implode(', ', $sets) .
                    " WHERE $colunaId = ?$condicaoUsuario"
                );

                if (!$stmt) {
                    throw new RuntimeException($conn->error);
                }

                $bind = [$tipos];
                foreach ($params as $i => &$valor) {
                    $bind[] = &$valor;
                }

                if (!call_user_func_array([$stmt, 'bind_param'], $bind)
                    || !$stmt->execute()) {
                    throw new RuntimeException($stmt->error);
                }

                $stmt->close();
            }

            $stmt = $conn->prepare(
                "SELECT id FROM projeto WHERE $colunaId = ?$condicaoUsuario"
            );

            if (!$stmt) throw new RuntimeException($conn->error);

            if ($usuarioId === null) {
                $stmt->bind_param($tipoId, $identificador);
            } else {
                $stmt->bind_param($tipoId . 'i', $identificador, $usuarioId);
            }
            if (!$stmt->execute()) {
                throw new RuntimeException($stmt->error);
            }

            $linha = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$linha) {
                throw new RuntimeException('Projeto não encontrado.');
            }

            $projetoId = (int) $linha['id'];

            if ($tags !== null) {
                $stmt = $conn->prepare(
                    'DELETE FROM projeto_tag WHERE projeto_id = ?'
                );
                if (!$stmt) throw new RuntimeException($conn->error);
                $stmt->bind_param('i', $projetoId);
                if (!$stmt->execute()) throw new RuntimeException($stmt->error);
                $stmt->close();

                if (!$this->salvarRelacionamentos(
                    $projetoId, $tags, 'tag', 'projeto_tag', 'tag_id'
                )) {
                    throw new RuntimeException('Falha ao atualizar tags.');
                }
            }

            if ($tecnologias !== null) {
                $stmt = $conn->prepare(
                    'DELETE FROM projeto_tecnologia WHERE projeto_id = ?'
                );
                if (!$stmt) throw new RuntimeException($conn->error);
                $stmt->bind_param('i', $projetoId);
                if (!$stmt->execute()) throw new RuntimeException($stmt->error);
                $stmt->close();

                if (!$this->salvarRelacionamentos(
                    $projetoId,
                    $tecnologias,
                    'tecnologia',
                    'projeto_tecnologia',
                    'tecnologias_id'
                )) {
                    throw new RuntimeException('Falha ao atualizar tecnologias.');
                }
            }

            $conn->commit();
            return true;
        } catch (Throwable $e) {
            $conn->rollback();
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Exclui um projeto pelo link (string) ou pelo ID (int).
     * Este método não verifica o dono; use removeDoUsuario em operações de usuário.
     */
    public function remove(string|int $identificador): void
    {
        global $conn;

        $stmt = is_string($identificador)
            ? $conn->prepare('DELETE FROM projeto WHERE link_repositorio = ?')
            : $conn->prepare('DELETE FROM projeto WHERE id = ?');

        if (!$stmt) return;

        $tipo = is_string($identificador) ? 's' : 'i';
        $stmt->bind_param($tipo, $identificador);
        $stmt->execute();
        $stmt->close();
    }
}

    /**
     * Insere um comentário com autor, projeto e, se existir, comentário pai.
     * Retorna se o INSERT foi executado.
     */

class ComentarioDAO {
    public function add(Comentario $comentario): bool
    {
        global $conn;

        $stmt = $conn->prepare(
            'INSERT INTO comentario
            (descricao, data, usuario_id, projeto_id, comentario_pai_id)
            VALUES (?, ?, ?, ?, ?)'
        );

        if (!$stmt) return false;

        $conteudo = $comentario->getConteudo();
        $data = $comentario->getDataPostagem()->format('Y-m-d H:i:s');
        $usuarioId = $comentario->getDono()->getId();
        $projetoId = $comentario->getProjeto()->getId();
        $pai = $comentario->getPai();
        $paiId = $pai ? $pai->getId() : null;

        $stmt->bind_param(
            'ssiii',
            $conteudo,
            $data,
            $usuarioId,
            $projetoId,
            $paiId
        );

        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    /**
     * Carrega comentário, autor, projeto e pai; opcionalmente carrega respostas diretas.
     * A opção evita recursão infinita ao carregar o pai ou cada resposta.
     */
    private function buscarComentario(int $id, bool $carregarFilhos = true): ?Comentario
    {
        global $conn;

        $stmt = $conn->prepare('SELECT * FROM comentario WHERE id = ?');
        if (!$stmt) return null;

        $stmt->bind_param('i', $id);
        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        }

        $dados = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$dados) return null;

        $dono = (new UsuarioDAO())->get((int) $dados['usuario_id']);
        $projeto = (new ProjetoDAO())->get((int) $dados['projeto_id']);

        if (!$dono || !$projeto) return null;

        $pai = null;
        if ($dados['comentario_pai_id'] !== null) {
            $pai = $this->buscarComentario(
                (int) $dados['comentario_pai_id'],
                false
            );
        }

        $comentario = new Comentario(
            $dono,
            $dados['descricao'],
            $projeto,
            $pai,
            new DateTime($dados['data']),
            (int) $dados['id']
        );

        if ($carregarFilhos) {
            $stmt = $conn->prepare(
                'SELECT id FROM comentario
                 WHERE comentario_pai_id = ?
                 ORDER BY data ASC'
            );

            if ($stmt) {
                $stmt->bind_param('i', $id);
                if ($stmt->execute()) {
                    $resultado = $stmt->get_result();

                    while ($filho = $resultado->fetch_assoc()) {
                        $objetoFilho = $this->buscarComentario(
                            (int) $filho['id'],
                            false
                        );

                        if ($objetoFilho) {
                            $comentario->adicionarFilho($objetoFilho);
                        }
                    }
                }
                $stmt->close();
            }
        }

        return $comentario;
    }

    /**
     * Busca um comentário pelo ID usando o carregador de relações completo.
     */
    public function get(int $id): ?Comentario
    {
        return $this->buscarComentario($id);
    }

    /**
     * Atualiza o conteúdo e o comentário pai de um comentário existente.
     */
    public function update(int $id, Comentario $comentario): bool
    {
        global $conn;

        $stmt = $conn->prepare(
            'UPDATE comentario
             SET descricao = ?, comentario_pai_id = ?
             WHERE id = ?'
        );

        if (!$stmt) return false;

        $conteudo = $comentario->getConteudo();
        $pai = $comentario->getPai();
        $paiId = $pai ? $pai->getId() : null;

        $stmt->bind_param('sii', $conteudo, $paiId, $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    /**
     * Exclui um comentário pelo ID; converte a string recebida para inteiro.
     */
    public function remove(int|string $identificador): void
    {
        global $conn;

        $id = (int) $identificador;
        $stmt = $conn->prepare('DELETE FROM comentario WHERE id = ?');

        if (!$stmt) return;

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
}
    /**
     * Insere uma denúncia e identifica se o alvo é projeto, usuário ou comentário.
     * Preenche a coluna de relacionamento correspondente ao tipo do alvo.
     */
class DenunciaDAO {
    private function consultarPainelAdmin(?int $id, ?string $tipo): array
    {
        global $conn;

        $sql = <<<'SQL'
                SELECT
                    d.id,
                    d.motivo,
                    d.data,
                    d.status,
                    d.tipo,
                    COALESCE(denunciante.nome, 'Usuário removido') AS denunciante,
                    CASE d.tipo
                        WHEN 'projeto' THEN COALESCE(projeto.nome, 'Projeto removido')
                        WHEN 'comentario' THEN COALESCE(comentario_projeto.nome, 'Projeto removido')
                        WHEN 'usuario' THEN COALESCE(usuario_denunciado.nome, 'Usuário removido')
                    END AS alvo,
                    CASE d.tipo
                        WHEN 'projeto' THEN COALESCE(dono_projeto.nome, 'Usuário removido')
                        WHEN 'comentario' THEN COALESCE(dono_comentario.nome, 'Usuário removido')
                        WHEN 'usuario' THEN COALESCE(usuario_denunciado.nome, 'Usuário removido')
                    END AS autor_alvo,
                    CASE d.tipo
                        WHEN 'projeto' THEN projeto.descricao
                        WHEN 'comentario' THEN comentario.descricao
                        WHEN 'usuario' THEN usuario_denunciado.email
                    END AS detalhes_alvo
                FROM denuncia d
                LEFT JOIN usuario denunciante ON denunciante.id = d.denunciante_id
                LEFT JOIN projeto ON projeto.id = d.projeto_denunciado_id
                LEFT JOIN usuario dono_projeto ON dono_projeto.id = projeto.usuario_id
                LEFT JOIN comentario ON comentario.id = d.comentario_denunciado_id
                LEFT JOIN projeto comentario_projeto ON comentario_projeto.id = comentario.projeto_id
                LEFT JOIN usuario dono_comentario ON dono_comentario.id = comentario.usuario_id
                LEFT JOIN usuario usuario_denunciado ON usuario_denunciado.id = d.usuario_denunciado_id
                WHERE 1 = 1
                SQL;
        if ($id !== null) {
            $sql .= ' AND d.id = ?';
        } else {
            $sql .= " AND d.status <> 'removida'";
            if ($tipo !== null) {
                $sql .= ' AND d.tipo = ?';
            }
        }

        $sql .= ' ORDER BY d.data DESC, d.id DESC';
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException('Não foi possível preparar a consulta das denúncias.');
        }

        if ($id !== null) {
            $stmt->bind_param('i', $id);
        } elseif ($tipo !== null) {
            $stmt->bind_param('s', $tipo);
        }
        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException('Não foi possível carregar as denúncias.');
        }

        $linhas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $linhas;
    }

    public function listarParaAdmin(?string $tipo = null): array
    {
        return $this->consultarPainelAdmin(null, $tipo);
    }

    public function buscarParaAdmin(int $id): ?array
    {
        return $this->consultarPainelAdmin($id, null)[0] ?? null;
    }

    private function executarAdmin(string $sql, string $tipos, mixed ...$valores): void
    {
        global $conn;

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException('Não foi possível preparar a ação de moderação.');
        }
        $argumentos = [$tipos];
        foreach ($valores as &$valor) {
            $argumentos[] = &$valor;
        }
        unset($valor);
        if (!call_user_func_array([$stmt, 'bind_param'], $argumentos)) {
            $stmt->close();
            throw new RuntimeException('Não foi possível vincular os dados da ação administrativa.');
        }
        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException('Não foi possível concluir a ação de moderação.');
        }
        $stmt->close();
    }

    public function decidirComoAdmin(int $id, bool $removerAlvo): bool
    {
        global $conn;

        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare(
                'SELECT tipo, status, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id
                 FROM denuncia WHERE id = ? FOR UPDATE'
            );
            if (!$stmt) {
                throw new RuntimeException('Não foi possível localizar a denúncia.');
            }
            $stmt->bind_param('i', $id);
            if (!$stmt->execute()) {
                $stmt->close();
                throw new RuntimeException('Não foi possível localizar a denúncia.');
            }
            $denuncia = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$denuncia || $denuncia['status'] === 'removida') {
                $conn->rollback();
                return false;
            }

            if (!$removerAlvo) {
                $this->executarAdmin(
                    "UPDATE denuncia SET status = 'removida' WHERE id = ?",
                    'i',
                    $id
                );
            } else {
                $colunaAlvo = match ($denuncia['tipo']) {
                    'projeto' => 'projeto_denunciado_id',
                    'comentario' => 'comentario_denunciado_id',
                    'usuario' => 'usuario_denunciado_id',
                    default => null,
                };
                if ($colunaAlvo === null || $denuncia[$colunaAlvo] === null) {
                    $conn->rollback();
                    return false;
                }

                switch ($denuncia['tipo']) {
                    case 'projeto':
                        $projetoId = (int) $denuncia['projeto_denunciado_id'];
                        $this->executarAdmin(
                            "UPDATE denuncia SET status = 'removida', projeto_denunciado_id = NULL
                             WHERE projeto_denunciado_id = ?",
                            'i',
                            $projetoId
                        );
                        $this->executarAdmin(
                            "UPDATE denuncia d
                             JOIN comentario c ON c.id = d.comentario_denunciado_id
                             SET d.status = 'removida', d.comentario_denunciado_id = NULL
                             WHERE c.projeto_id = ?",
                            'i',
                            $projetoId
                        );
                        $this->executarAdmin(
                            'UPDATE comentario resposta
                             JOIN comentario alvo ON alvo.id = resposta.comentario_pai_id
                             SET resposta.comentario_pai_id = NULL
                             WHERE alvo.projeto_id = ?',
                            'i',
                            $projetoId
                        );
                        $this->executarAdmin('DELETE FROM projeto WHERE id = ?', 'i', $projetoId);
                        break;

                    case 'comentario':
                        $comentarioId = (int) $denuncia['comentario_denunciado_id'];
                        $this->executarAdmin(
                            "UPDATE denuncia SET status = 'removida', comentario_denunciado_id = NULL
                             WHERE comentario_denunciado_id = ?",
                            'i',
                            $comentarioId
                        );
                        $this->executarAdmin(
                            'UPDATE comentario SET comentario_pai_id = NULL WHERE comentario_pai_id = ?',
                            'i',
                            $comentarioId
                        );
                        $this->executarAdmin('DELETE FROM comentario WHERE id = ?', 'i', $comentarioId);
                        break;

                    case 'usuario':
                        $usuarioId = (int) $denuncia['usuario_denunciado_id'];
                        $this->executarAdmin(
                            "UPDATE denuncia d
                             JOIN projeto p ON p.id = d.projeto_denunciado_id
                             SET d.status = 'removida', d.projeto_denunciado_id = NULL
                             WHERE p.usuario_id = ?",
                            'i',
                            $usuarioId
                        );
                        $this->executarAdmin(
                            "UPDATE denuncia d
                             JOIN comentario c ON c.id = d.comentario_denunciado_id
                             LEFT JOIN projeto p ON p.id = c.projeto_id
                             SET d.status = 'removida', d.comentario_denunciado_id = NULL
                             WHERE c.usuario_id = ? OR p.usuario_id = ?",
                            'ii',
                            $usuarioId,
                            $usuarioId
                        );
                        $this->executarAdmin(
                            "UPDATE denuncia
                             SET status = 'removida', usuario_denunciado_id = NULL
                             WHERE usuario_denunciado_id = ?",
                            'i',
                            $usuarioId
                        );
                        $this->executarAdmin(
                            'UPDATE denuncia SET denunciante_id = NULL WHERE denunciante_id = ?',
                            'i',
                            $usuarioId
                        );
                        $this->executarAdmin(
                            'UPDATE comentario resposta
                             JOIN comentario alvo ON alvo.id = resposta.comentario_pai_id
                             LEFT JOIN projeto p ON p.id = alvo.projeto_id
                             SET resposta.comentario_pai_id = NULL
                             WHERE alvo.usuario_id = ? OR p.usuario_id = ?',
                            'ii',
                            $usuarioId,
                            $usuarioId
                        );
                        $this->executarAdmin('DELETE FROM usuario WHERE id = ?', 'i', $usuarioId);
                        break;

                    default:
                        throw new RuntimeException('O tipo de denúncia não é válido.');
                }
            }

            $conn->commit();
            return true;
        } catch (Throwable $erro) {
            $conn->rollback();
            throw $erro;
        }
    }

    public function add(Denuncia $denuncia): bool
    {
        global $conn;

        $stmt = $conn->prepare(
            'INSERT INTO denuncia
            (motivo, data, status, tipo, denunciante_id,
             comentario_denunciado_id, projeto_denunciado_id,
             usuario_denunciado_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );

        if (!$stmt) return false;

        $motivo = $denuncia->getMotivo();
        $data = $denuncia->getData()->format('Y-m-d H:i:s');
        $status = $denuncia->getStatus()->value;

        $denunciante = $denuncia->getDenunciante();
        $denuncianteId = $denunciante ? $denunciante->getId() : null;

        $denunciado = $denuncia->getDenunciado();
        $tipo = null;
        $comentarioId = null;
        $projetoId = null;
        $usuarioId = null;

        if ($denunciado instanceof Projeto) {
            $tipo = 'projeto';
            $projetoId = $denunciado->getId();
        } elseif ($denunciado instanceof Usuario) {
            $tipo = 'usuario';
            $usuarioId = $denunciado->getId();
        } elseif ($denunciado instanceof Comentario) {
            $tipo = 'comentario';
            $comentarioId = $denunciado->getId();
        } else {
            $stmt->close();
            return false;
        }

        $stmt->bind_param(
            'ssssiiii',
            $motivo,
            $data,
            $status,
            $tipo,
            $denuncianteId,
            $comentarioId,
            $projetoId,
            $usuarioId
        );

        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    /**
     * Busca uma denúncia e carrega denunciante e entidade denunciada.
     */
    public function get(int $id): ?Denuncia
    {
        global $conn;

        $stmt = $conn->prepare('SELECT * FROM denuncia WHERE id = ?');
        if (!$stmt) return null;

        $stmt->bind_param('i', $id);
        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        }

        $dados = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$dados) return null;

        $denunciante = null;
        if ($dados['denunciante_id'] !== null) {
            $denunciante = (new UsuarioDAO())->get(
                (int) $dados['denunciante_id']
            );
            if (!$denunciante) return null;
        }

        $denunciado = null;

        switch ($dados['tipo']) {
            case 'projeto':
                if ($dados['projeto_denunciado_id'] !== null) {
                    $denunciado = (new ProjetoDAO())->get(
                        (int) $dados['projeto_denunciado_id']
                    );
                }
                break;

            case 'usuario':
                if ($dados['usuario_denunciado_id'] !== null) {
                    $denunciado = (new UsuarioDAO())->get(
                        (int) $dados['usuario_denunciado_id']
                    );
                }
                break;

            case 'comentario':
                if ($dados['comentario_denunciado_id'] !== null) {
                    $denunciado = (new ComentarioDAO())->get(
                        (int) $dados['comentario_denunciado_id']
                    );
                }
                break;
        }

        if (!$denunciado) return null;

        return new Denuncia(
            $dados['motivo'],
            $denunciante,
            $denunciado,
            new DateTime($dados['data']),
            StatusDenuncia::from($dados['status']),
            (int) $dados['id']
        );
    }

    /**
     * Atualiza o motivo e o status de uma denúncia existente.
     */
    public function update(int $id, Denuncia $denuncia): bool
    {
        global $conn;

        $stmt = $conn->prepare(
            'UPDATE denuncia SET motivo = ?, status = ? WHERE id = ?'
        );

        if (!$stmt) return false;

        $motivo = $denuncia->getMotivo();
        $status = $denuncia->getStatus()->value;

        $stmt->bind_param('ssi', $motivo, $status, $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    /**
     * Exclui uma denúncia pelo ID, aceitando string por compatibilidade.
     */
    public function remove(int|string $identificador): void
    {
        global $conn;

        $id = (int) $identificador;
        $stmt = $conn->prepare('DELETE FROM denuncia WHERE id = ?');

        if (!$stmt) return;

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
}

    /**
     * Insere uma avaliação ligada a um projeto e a um usuário.
     */
class AvaliacaoDAO {
    public function add(Avaliacao $avaliacao): bool
    {
        global $conn;

        $stmt = $conn->prepare(
            'INSERT INTO avaliacao (nota, data_criacao, projeto_id, usuario_id)
             VALUES (?, ?, ?, ?)'
        );

        if (!$stmt) return false;

        $nota = $avaliacao->getNota();
        $data = $avaliacao->getDataCriacao()->format('Y-m-d H:i:s');
        $projetoId = $avaliacao->getProjeto()->getId();
        $usuarioId = $avaliacao->getUsuario()->getId();

        $stmt->bind_param('isii', $nota, $data, $projetoId, $usuarioId);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    /**
     * Busca uma avaliação e carrega o projeto e o usuário associados.
     */
    public function get(int $id): ?Avaliacao
    {
        global $conn;

        $stmt = $conn->prepare('SELECT * FROM avaliacao WHERE id = ?');
        if (!$stmt) return null;

        $stmt->bind_param('i', $id);
        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        }

        $dados = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$dados) return null;

        $projeto = (new ProjetoDAO())->get((int) $dados['projeto_id']);
        $usuario = (new UsuarioDAO())->get((int) $dados['usuario_id']);

        if (!$projeto || !$usuario) return null;

        return new Avaliacao(
            (int) $dados['nota'],
            $projeto,
            $usuario,
            new DateTime($dados['data_criacao']),
            (int) $dados['id']
        );
    }

    /**
     * Atualiza a nota da avaliação identificada pelo ID.
     */
    public function update(int $id, Avaliacao $avaliacao): bool
    {
        global $conn;

        $stmt = $conn->prepare(
            'UPDATE avaliacao SET nota = ? WHERE id = ?'
        );

        if (!$stmt) return false;

        $nota = $avaliacao->getNota();
        $stmt->bind_param('ii', $nota, $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    /**
     * Exclui uma avaliação pelo ID, aceitando string por compatibilidade.
     */
    public function remove(int|string $identificador): void
    {
        global $conn;

        $id = (int) $identificador;
        $stmt = $conn->prepare('DELETE FROM avaliacao WHERE id = ?');

        if (!$stmt) return;

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
}