<?php
require_once __DIR__ . "/dao.php";
require_once __DIR__ . "/model.php";

class UsuarioService
{
    private UsuarioDAO $usuarioDAO;

    public function __construct(UsuarioDAO $usuarioDAO)
    {
        $this->usuarioDAO = $usuarioDAO;
    }

    public function add(Usuario $usuario): void
    {
        $this->validarUsuario($usuario);

        if (!$this->usuarioDAO->add($usuario)) {
            throw new \RuntimeException("Não foi possível realizar o cadastro.");
        }
    }

    public function login(string $email, string $senha): ?Usuario
    {
        $email = trim($email);
        if ($email === '' || $senha === '') {
            return null;
        }

        $usuario = $this->usuarioDAO->get($email);

        if ($usuario === null) {
            return null;
        }

        $hashArmazenado = $usuario->getSenha();
        if (password_verify($senha, $hashArmazenado)) {
            return $usuario;
        }

        $hashLegado = hash('sha256', $senha);
        if (
            !preg_match('/\A[a-f0-9]{64}\z/i', $hashArmazenado) ||
            !hash_equals(strtolower($hashArmazenado), $hashLegado)
        ) {
            return null;
        }

        $novoHash = password_hash($senha, PASSWORD_DEFAULT);
        if (!$this->usuarioDAO->updatePasswordHash($usuario->getId(), $novoHash)) {
            throw new \RuntimeException("Não foi possível atualizar a senha da conta.");
        }

        $usuario->setSenha($novoHash);
        return $usuario;
    }

    private function validarUsuario(Usuario $usuario): void
    {
        if (trim($usuario->getNome()) === '' || empty($usuario->getEmail()) || empty($usuario->getSenha())) {
            throw new \InvalidArgumentException("Campos obrigatórios não preenchidos.");
        }

        if (!filter_var($usuario->getEmail(), FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("O e-mail informado é inválido.");
        }

        $senha = $usuario->getSenha();
        if (strlen($senha) < 8 || !preg_match('/[A-Z]/', $senha) || !preg_match('/[a-z]/', $senha) || !preg_match('/[0-9]/', $senha) || !preg_match('/[\W_]/', $senha)) {
            throw new \InvalidArgumentException("A senha não atende aos requisitos mínimos de segurança.");
        }

        if ($this->get($usuario->getEmail()) !== null) {
            throw new \InvalidArgumentException("Este endereço de e-mail já está cadastrado.");
        }
    }

    public function get(string $email): ?Usuario
    {
        return $this->usuarioDAO->get($email);
    }

    public function update(string $email, Usuario $usuario): void
    {
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