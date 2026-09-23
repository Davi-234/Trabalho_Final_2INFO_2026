<?php

include "model.php";
include "conexao.php";

class UsuarioDAO
{
    public function add(Usuario $usuario): void
    {
        $stmt = $pdo->prepare(
            'INSERT INTO usuario 
            (nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo) 
            VALUES (:nome, :email, SHA2(:senha_hash, 256), :link_github, :link_linkedin, :perfil_lattes, :tipo)'
        );

        $stmt->execute([
            'nome' => $usuario->getNome(),
            'email' => $usuario->getEmail(),
            'senha_hash' => $usuario->getSenha(),
            'link_github' => $usuario->getPerfilGithub(),
            'link_linkedin' => $usuario->getPerfilLinkedin(),
            'perfil_lattes' => $usuario->getPerfilLattes(),
            'tipo' => 'COMUM'
        ]);

        $usuario->setId((int) $pdo->lastInsertId());
    }

    public function get(string|int $identificador): ?Usuario
    {
        $stmt = (gettype($identificador) === "string")
            ? $pdo->prepare('SELECT * FROM usuario WHERE email = ?')
            : $pdo->prepare('SELECT * FROM usuario WHERE id = ?');

        $stmt->execute([$identificador]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

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

        return $usuario_buscado;
    }

    public function update(
        string|int $identificador,
        Usuario $usuario
    ): void {
        $stmt = $pdo->prepare(
            'UPDATE usuario SET 
                nome = :nome,
                email = :email,
                senha_hash = SHA2(:senha_hash, 256),
                link_github = :link_github,
                link_linkedin = :link_linkedin,
                perfil_lattes = :perfil_lattes,
                tipo = :tipo
            WHERE id = :id'
        );

        $stmt->execute([
            'nome' => $usuario->getNome(),
            'email' => $usuario->getEmail(),
            'senha_hash' => $usuario->getSenha(),
            'link_github' => $usuario->getPerfilGithub(),
            'link_linkedin' => $usuario->getPerfilLinkedin(),
            'perfil_lattes' => $usuario->getPerfilLattes(),
            'tipo' => $usuario->getTipo(),
            'id' => $identificador
        ]);
    }

    public function remove(string|int $identificador): void
    {
        $stmt = (gettype($identificador) === "string")
            ? $pdo->prepare('DELETE FROM usuario WHERE email = ?')
            : $pdo->prepare('DELETE FROM usuario WHERE id = ?');

        $stmt->execute([$identificador]);
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