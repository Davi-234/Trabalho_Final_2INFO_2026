<?php

include "model.php";
require_once 'conexao.php';

class UsuarioDAO
{
    public function add(Usuario $usuario): bool
    {
        if ($stmt = $conn->prepare('INSERT INTO usuario (nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo)  VALUES (?, ?, SHA2(?, 256), ?, ?, ?, ?);')) {
            $stmt->bind_param("sssssss", $usuario->getNome(), $usuario->getEmail(), $usuario->getSenha(), $usuario->getPerfilGithub(), $usuario->getPerfilLinkedin(), $usuario->getPerfilLattes(), 'comum');
            $stmt->execute();
            return true;
        } else {
            return false;
        }
    }

    public function get(string|int $identificador): ?Usuario
    {
        $stmt = (gettype($identificador) === "string")
            ? $conn->prepare('SELECT * FROM usuario WHERE email = ?')
            : $conn->prepare('SELECT * FROM usuario WHERE id = ?');

        $stmt->bind_param(["i", $identificador]);
        $stmt->execute();
        $user = $stmt->get_result();

        if ($user === false) {
            return null;
        }

        $usuario_buscado = new Usuario(
            $user['nome'],
            $user['email'],
            $user['senha_hash'],
            $user['link_github'],
            $user['link_linkedin'],
            $user['perfil_lattes'],
            (int) $user['id']
        );

        $usuario_buscado->setTipo($user['tipo']);

        $stmt->close();
        $conn->close();

        return $usuario_buscado;
    }

    public function update(
        string|int $identificador,
        array $atributo_valor
    ): bool {
        // $atributo_valor é vetor, cuja chave é o nome do atributo, que recebe nessa posição o valor do atributo; podendo ser enviado mais de um atributo da tabela para ser atualizado.
        $sql = "UPDATE usuario SET ";

        foreach ($atributo_valor as [$attr, $valor]) {
            $sql .= "$attr = $valor";
        }

        $sql .= (gettype($identificador) === "string") ? "WHERE email = :email" : "WHERE id = :id";

        $conn->execute($sql);

        return true;
    }

    public function remove(string|int $identificador): void
    {
        $stmt = (gettype($identificador) === "string")
            ? $conn->prepare('DELETE FROM usuario WHERE email = ?')
            : $conn->prepare('DELETE FROM usuario WHERE id = ?');

        $stmt->execute([$identificador]);
    }
}


class ProjetoDAO
{
    public function add(Projeto $projeto): bool
    {
        if ($stmt = $conn->prepare('INSERT INTO usuario (visibilidade, data_criacao, descricao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id)  VALUES (?, ?, SHA2(?, 256), ?, ?, ?, ?);')) {
            $stmt->bind_param("bsssssssisi", $projeto->getVisibilidade(), $projeto->getDataCriacao(), $projeto->getDescricao(), $projeto->getTitulo(), $projeto->getStatus(), $projeto->getNivel(), $projeto->getImagemProjeto(), $projeto->getViews(), $projeto->getLinkRepositorio(), $projeto->getDono()->getId());
            $stmt->execute();
            return true;
        } else {
            return false;
        }
    }

    public function get(int $identificador): ?Projeto
    {
        
        $stmt = $conn->prepare('SELECT * FROM projeto WHERE id = ?');

        $stmt->bind_param(["i", $identificador]);
        $stmt->execute();
        $project = $stmt->get_result();

        if ($project === false) {
            return null;
        }

        $projeto_buscado = new Projeto(
            $project['nome'],
            $project['descricao'],
            $project['link_repositorio'],
            $project['visibilidade'],
            UsuarioDAO.get(intval($project["usuario_id"])),
            $project['data_criacao'],
            $project['status'],
            $project['nivel'],
            $project['imagem_projeto'],
            $project['tecnologias'],
            $project['tags'],
            (int) $project['id']
        );

        $projeto_buscado->setTipo($project['tipo']);

        $stmt->close();
        $conn->close();

        return $projeto_buscado;
    }

    public function update(string|int $identificador, Projeto $projeto): void {}

    public function remove(Projeto $projeto): void {}
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
