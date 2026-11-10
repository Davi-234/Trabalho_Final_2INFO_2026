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

    public function remove(string|int $identificador): void
    {
        global $conn;

        $stmt = (is_string($identificador))
            ? $conn->prepare('DELETE FROM usuario WHERE email = ?')
            : $conn->prepare('DELETE FROM usuario WHERE id = ?');

        if ($stmt === false) {
            return;
        }

        $tipoIdentificador = is_string($identificador) ? 's' : 'i';
        $stmt->bind_param($tipoIdentificador, $identificador);
        $stmt->execute();
        $stmt->close();
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
        $executado = $stmt->execute();
        $stmt->close();

        return $executado;
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

    public function update(string|int $identificador, Projeto $projeto): void
    {
        global $conn;

        $colunaIdentificador = is_string($identificador) ? 'link_repositorio' : 'id';
        $tipoIdentificador = is_string($identificador) ? 's' : 'i';
        $stmt = $conn->prepare(
            "UPDATE projeto SET visibilidade = ?, descricao = ?, nome = ?, status = ?, nivel = ?, imagem_projeto = ?, views = ?, link_repositorio = ?, usuario_id = ? WHERE $colunaIdentificador = ?"
        );
        if ($stmt === false) {
            return;
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
        $stmt->execute();
        $stmt->close();
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
