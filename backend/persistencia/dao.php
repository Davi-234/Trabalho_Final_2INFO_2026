<?php

require_once __DIR__ . '/../modelos/model.php';
require_once __DIR__ . '/../configuracao/conexao.php';


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
            (int) $dados['id']
        );

        $usuario->setTipo(NivelUsuario::from($dados['tipo']));

        return $usuario;
    }

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

    public function update(string|int $identificador, array $atributo_valor): bool
    {
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

    public function remove(string|int $identificador): bool
    {
        global $conn;

        $coluna = is_string($identificador) ? 'email' : 'id';
        $tipo = is_string($identificador) ? 's' : 'i';

        $stmt = $conn->prepare("DELETE FROM usuario WHERE $coluna = ?");
        if (!$stmt) {
            return;
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param($tipo, $identificador);
        $stmt->execute();
        $stmt->close();
        return $removido;
    }
}

class ProjetoDAO {
    /*
     * Insere cada tag/tecnologia se ainda não existir e cria a associação N:N.
     * $tabela, $tabelaRelacao e $colunaRelacao são nomes constantes definidos
     * internamente, nunca valores recebidos diretamente do usuário.
     */
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

    public function update(string|int $identificador, array $valores): bool
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

        $conn->begin_transaction();

        try {
            if ($sets) {
                $params[] = $identificador;
                $tipos .= $tipoId;

                $stmt = $conn->prepare(
                    'UPDATE projeto SET ' . implode(', ', $sets) .
                    " WHERE $colunaId = ?"
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
                "SELECT id FROM projeto WHERE $colunaId = ?"
            );

            if (!$stmt) throw new RuntimeException($conn->error);

            $stmt->bind_param($tipoId, $identificador);
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

        // Carrega apenas os dados do pai, sem percorrer seus filhos.
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

    public function get(int $id): ?Comentario
    {
        return $this->buscarComentario($id);
    }

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

class DenunciaDAO {
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