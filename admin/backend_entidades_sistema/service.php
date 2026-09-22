<?php
include "dao.php";
include "model.php";
class UsuarioService
{
    private UsuarioDAO $usuarioDAO;

    public function __construct(UsuarioDAO $usuarioDAO)
    {
        return;
    }

    public function add(Usuario $usuario): void
    {
    }

    public function get(string $email): ?Usuario
    {
        return null;
    }

    public function update(
        string $email,
        Usuario $usuario
    ): void {
    }

    public function remove(Usuario $usuario): void
    {
    }
}

class ProjetoService
{
    private ProjetoDAO $projetoDAO;

    public function __construct(ProjetoDAO $projetoDAO)
    {
        return;
    }

    public function getAll(): array
    {
        return [];
    }

    public function add(Projeto $projeto): void
    {
    }

    public function get(string $link_repositorio): ?Projeto
    {
        return null;
    }

    public function update(
        string $link_repositorio,
        Projeto $projeto
    ): void {
    }

    public function remove(Projeto $projeto): void
    {
    }
}

class ComentarioService
{
    private ComentarioDAO $comentarioDAO;

    public function __construct(ComentarioDAO $comentarioDAO)
    {
        return;
    }

    public function getAll(): array
    {
        return [];
    }

    public function add(Comentario $comentario): void
    {
    }

    public function get(int $id): Comentario
    {
        
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

class DenunciaService
{
    private DenunciaDAO $denunciaDAO;

    public function __construct(DenunciaDAO $denunciaDAO)
    {
        return;
    }

    public function getAll(): array
    {
        return [];
    }

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