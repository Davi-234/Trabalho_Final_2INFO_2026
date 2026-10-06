<?php
include "dao.php";
include "model.php";
class UsuarioService
{
    private UsuarioDAO $usuarioDAO;

    public function __construct(UsuarioDAO $usuarioDAO)
    {
        $this->usuarioDAO = $usuarioDAO;
    }

    public function add(Usuario $usuario): void
    {
        $this->usuarioDAO->add($usuario);
    }

    public function get(string $email): ?Usuario
    {
        return $this->usuarioDAO->get($email);
    }

    public function update(
        string $email,
        Usuario $usuario
    ): void {
        $this->usuarioDAO->update($email, [
            'nome' => $usuario->getNome(),
            'email' => $usuario->getEmail(),
            'senha' => $usuario->getSenha(),
        ]);
    }

    public function remove(Usuario $usuario): void
    {
        $this->usuarioDAO->remove($usuario->getEmail());
    }
}

class ProjetoService
{
    private ProjetoDAO $projetoDAO;

    public function __construct(ProjetoDAO $projetoDAO)
    {
        $this->projetoDAO = $projetoDAO;
    }

    public function getAll(): array
    {
        return [];
    }

    public function add(Projeto $projeto): void
    {
        $this->projetoDAO->add($projeto);
    }

    public function get(string $link_repositorio): ?Projeto
    {
        return $this->projetoDAO->get($link_repositorio);
    }

    public function update(
        string $link_repositorio,
        Projeto $projeto
    ): void {
        $this->projetoDAO->update($link_repositorio, $projeto);
    }

    public function remove(Projeto $projeto): void
    {
        $this->projetoDAO->remove($projeto);
    }
}

class ComentarioService
{
    private ComentarioDAO $comentarioDAO;

    public function __construct(ComentarioDAO $comentarioDAO)
    {
        $this->comentarioDAO = $comentarioDAO;
    }

    public function getAll(): array
    {
        return [];
    }

    public function add(Comentario $comentario): void
    {
        $this->comentarioDAO->add($comentario);
    }

    public function get(int $id): Comentario
    {
        return $this->comentarioDAO->get($id);
    }

    public function update(
        int $id,
        Comentario $comentario
    ): void {
        $this->comentarioDAO->update($id, $comentario);
    }

    public function remove(Comentario $comentario): void
    {
        $this->comentarioDAO->remove($comentario);
    }
}

class DenunciaService
{
    private DenunciaDAO $denunciaDAO;

    public function __construct(DenunciaDAO $denunciaDAO)
    {
        $this->denunciaDAO = $denunciaDAO;
    }

    public function getAll(): array
    {
        return [];
    }

    public function add(Denuncia $denuncia): void
    {
        $this->denunciaDAO->add($denuncia);
    }

    public function get(int $id): ?Denuncia
    {
        return null;
    }

    public function update(
        int $id,
        Denuncia $denuncia
    ): void {
        $this->denunciaDAO->update($id, $denuncia);
    }

    public function remove(Denuncia $denuncia): void
    {
        $this->denunciaDAO->remove($denuncia);
    }
}

?>