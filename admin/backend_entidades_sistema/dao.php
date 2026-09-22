<?php
include "model.php";
include "conexao.php";
class UsuarioDAO
{
    public function add(Usuario $usuario): void
    {
        $stmt = $pdo->prepare('INSERT INTO usuario (nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo) VALUES (:nome,:email,:senha_hash,:link_github,:link_linkedin, :perfil_lattes, :tipo)');
        $stmt->execute([
            'nome' => $usuario.getNome(),
            'email' => $usuario.getEmail(),
            'senha_hash' => 'sha2('.$usuario.getSenha().',255)',
            'link_github' => $usuario.getEmail(),
            'link_linkedin' => $usuario.getEmail(),
            'perfil_lattes' => $usuario.getEmail(),
            "tipo" => 'COMUM'
        ]);
    }

    public function get(string|int $identificador): ?Usuario
    {
        return null;
    }

    public function update(
        string|int $identificador,
        Usuario $usuario
    ): void {}

    public function remove(Usuario $usuario): void {}
}

class ProjetoDAO
{
    public function add(Projeto $projeto): void {}

    public function get(string|int $identificador): ?Projeto
    {
        return null;
    }

    public function update(
        string|int $identificador,
        Projeto $projeto
    ): void {}

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
