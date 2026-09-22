<?php
include "model.php";
class UsuarioDAO
{
    public function add(Usuario $usuario): void
    {
    }

    public function get(string|int $identificador): ?Usuario
    {
        return null;
    }

    public function getAll(): array
    {
        return [];
    }

    public function update(
        string|int $identificador,
        Usuario $usuario
    ): void {
    }

    public function remove(Usuario $usuario): void
    {
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

    public function getAll(): array
    {
        return [];
    }

    public function update(
        string|int $identificador,
        Projeto $projeto
    ): void {
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

    public function getAll(): array
    {
        return [];
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

    public function getAll(): array
    {
        return [];
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