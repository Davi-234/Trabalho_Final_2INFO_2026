<?php

include "model.php";
require_once 'conexao.php';

class UsuarioDAO
{
    public function add(Usuario $usuario): bool
    {
        global $conn;

        if ($stmt = $conn->prepare('INSERT INTO usuario (nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo) VALUES (?, ?, SHA2(?, 256), ?, ?, ?, ?);')) {
            $nome = $usuario->getNome();
            $email = $usuario->getEmail();
            $senha = $usuario->getSenha();
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
            $stmt->execute();
            return true;
        }

        return false;
    }

    public function get(string|int $identificador): ?Usuario
    {
        global $conn;

        $stmt = (gettype($identificador) === 'string')
            ? $conn->prepare('SELECT * FROM usuario WHERE email = ?')
            : $conn->prepare('SELECT * FROM usuario WHERE id = ?');

        if ($stmt === false) {
            return null;
        }

        $stmt->bind_param('s', $identificador);
        $stmt->execute();
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

        $usuario_buscado->setTipo($dados['tipo']);

        $stmt->close();

        return $usuario_buscado;
    }

    public function update(
        string|int $identificador,
        array $atributo_valor
    ): bool {
        global $conn;

        // $atributo_valor é vetor, cuja chave é o nome do atributo, que recebe nessa posição o valor do atributo; podendo ser enviado mais de um atributo da tabela para ser atualizado.
        $stmt = $conn->prepare(
            'UPDATE usuario SET 
                nome = ?,
                email = ?,
                senha_hash = SHA2(?, 256),
                link_github = ?,
                link_linkedin = ?,
                perfil_lattes = ?,
                tipo = ?
            WHERE id = ?'
        );

        if ($stmt === false) {
            return false;
        }

        $nome = $atributo_valor['nome'] ?? null;
        $email = $atributo_valor['email'] ?? null;
        $senha = $atributo_valor['senha_hash'] ?? null;
        $linkGithub = $atributo_valor['link_github'] ?? null;
        $linkLinkedin = $atributo_valor['link_linkedin'] ?? null;
        $perfilLattes = $atributo_valor['perfil_lattes'] ?? null;
        $tipo = $atributo_valor['tipo'] ?? null;

        $stmt->bind_param(
            'sssssssi',
            $nome,
            $email,
            $senha,
            $linkGithub,
            $linkLinkedin,
            $perfilLattes,
            $tipo,
            $identificador
        );

        $stmt->execute();
        $stmt->close();

        return true;
    }

    public function remove(string|int $identificador): void
    {
        global $conn;

        $stmt = (gettype($identificador) === 'string')
            ? $conn->prepare('DELETE FROM usuario WHERE email = ?')
            : $conn->prepare('DELETE FROM usuario WHERE id = ?');

        if ($stmt === false) {
            return;
        }

        $stmt->bind_param('s', $identificador);
        $stmt->execute();
        $stmt->close();
    }
}


class ProjetoDAO
{
    public function add(Projeto $projeto): void
    {
        
    }

    public function get(string|int $identificador): ?Projeto
    {
        
        return null;
    }

    public function update(string|int $identificador, Projeto $projeto): void {
        
    }

    public function remove(Projeto $projeto): void
    {
        
    }
}


class ComentarioDAO
{
    public function add(Comentario $comentario): void
    {
        
    }

    public function get(int $id): ?Comentario
    {
        
        return null;
    }

    public function update(
        int $id,
        Comentario $comentario
    ): void {
        
    }

    public function remove(Comentario $comentario): void
    {
        
    }
}


class DenunciaDAO
{
    public function add(Denuncia $denuncia): void
    {
        
    }

    public function get(int $id): ?Denuncia
    {
        
        return null;
    }

    public function update(
        int $id,
        Denuncia $denuncia
    ): void {
        
    }

    public function remove(Denuncia $denuncia): void
    {
        
    }
}
?>