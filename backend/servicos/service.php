<?php
require_once __DIR__ . "/../dao/dao.php";
require_once __DIR__ . "/../modelos/model.php";

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

    public function gerarTokenRecuperacaoSenha(string $email): ?string
    {
        $usuario = $this->usuarioDAO->get(trim($email));
        if ($usuario === null) {
            return null;
        }

        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiraEm = new DateTimeImmutable('+1 hour');
        if (!$this->usuarioDAO->criarTokenRecuperacao($usuario->getId(), $tokenHash, $expiraEm)) {
            throw new \RuntimeException('Não foi possível gerar a recuperação de senha.');
        }

        return $token;
    }

    public function redefinirSenha(string $token, string $novaSenha): void
    {
        $this->validarSenha($novaSenha);
        if (!preg_match('/\A[a-f0-9]{64}\z/', $token)) {
            throw new \InvalidArgumentException('O link de recuperação é inválido ou expirou.');
        }

        $tokenHash = hash('sha256', $token);
        $passwordHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        if (!$this->usuarioDAO->redefinirSenha($tokenHash, $passwordHash)) {
            throw new \InvalidArgumentException('O link de recuperação é inválido ou expirou.');
        }
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
        $this->validarSenha($senha, false);

        if ($this->get($usuario->getEmail()) !== null) {
            throw new \InvalidArgumentException("Este endereço de e-mail já está cadastrado.");
        }
    }

    public function get(string|int $identificador): ?Usuario
    {
        return $this->usuarioDAO->get($identificador);
    }

    public function atualizarFotoPerfil(int $id, string $caminho): void
    {
        if (!$this->usuarioDAO->atualizarFotoPerfil($id, $caminho)) {
            throw new \RuntimeException('Não foi possível salvar a imagem do perfil.');
        }
    }

    public function atualizarPerfil(
        int $id,
        string $nome,
        string $email,
        ?string $linkGithub,
        ?string $linkLinkedin,
        ?string $novaSenha = null
    ): void {
        $nome = trim($nome);
        $email = trim($email);
        $linkGithub = $linkGithub !== null ? trim($linkGithub) : null;
        $linkLinkedin = $linkLinkedin !== null ? trim($linkLinkedin) : null;

        if ($nome === '' || strlen($nome) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
            throw new \InvalidArgumentException('Informe um nome e e-mail válidos.');
        }

        foreach (['GitHub' => $linkGithub, 'LinkedIn' => $linkLinkedin] as $nomeCampo => $link) {
            if ($link !== null && $link !== '' && (strlen($link) > 255 || !filter_var($link, FILTER_VALIDATE_URL))) {
                throw new \InvalidArgumentException("O link de {$nomeCampo} é inválido.");
            }
        }

        $usuarioAtual = $this->usuarioDAO->get($id);
        if ($usuarioAtual === null) {
            throw new \RuntimeException('Conta não encontrada.');
        }

        $usuarioComEmail = $this->usuarioDAO->get($email);
        if ($usuarioComEmail !== null && $usuarioComEmail->getId() !== $id) {
            throw new \InvalidArgumentException('Este endereço de e-mail já está cadastrado.');
        }

        $dados = [
            'nome' => $nome,
            'email' => $email,
            'link_github' => $linkGithub ?: null,
            'link_linkedin' => $linkLinkedin ?: null,
        ];
        if ($novaSenha !== null && $novaSenha !== '') {
            $this->validarSenha($novaSenha, true);
            $dados['senha'] = $novaSenha;
        }

        if (!$this->usuarioDAO->update($id, $dados)) {
            throw new \RuntimeException('Não foi possível atualizar o perfil.');
        }
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
        if (!$this->usuarioDAO->remove($usuario->getId())) {
            throw new \RuntimeException('Não foi possível excluir a conta.');
        }
    }

    private function validarSenha(string $senha, bool $nova = true): void
    {
        if (strlen($senha) < 8 || !preg_match('/[A-Z]/', $senha) || !preg_match('/[a-z]/', $senha) || !preg_match('/[0-9]/', $senha) || !preg_match('/[\W_]/', $senha)) {
            $mensagem = $nova
                ? 'A nova senha não atende aos requisitos mínimos de segurança.'
                : 'A senha não atende aos requisitos mínimos de segurança.';
            throw new \InvalidArgumentException($mensagem);
        }
    }
}

class ProjetoService
{
    private ProjetoDAO $projetoDAO;

    public function __construct(ProjetoDAO $projetoDAO)
    {
        $this->projetoDAO = $projetoDAO;
    }

    public function listarPublicos(
        string $busca,
        string $nivel,
        string $tecnologias,
        int $pagina,
        string $ordenar,
        bool $inverter
    ): array
    {
        return $this->projetoDAO->listarPublicos($busca, $nivel, $tecnologias, $pagina, $ordenar, $inverter);
    }

    public function add(Projeto $projeto): void
    {
        if (!$this->projetoDAO->add($projeto)) {
            throw new RuntimeException('Não foi possível salvar o projeto.');
        }
    }

    public function get(string|int $identificador): ?Projeto
    {
        return $this->projetoDAO->get($identificador);
    }

    public function listarDoUsuario(int $usuarioId): array
    {
        return $this->projetoDAO->listarDoUsuario($usuarioId);
    }

    public function getDoUsuario(int|string $projetoId): ?Usuario
    {
        return $this->projetoDAO->get($projetoId)->getDono();
    }

    public function update(
        string|int $identificador,
        array $attr_val
    ): void {
        if (!$this->projetoDAO->update($identificador, $attr_val)) {
            throw new RuntimeException('Não foi possível atualizar o projeto.');
        }
    }

    public function remove(Projeto $projeto): void
    {
        $this->projetoDAO->remove($projeto->getId());
    }
}

class ComentarioService
{
    private ComentarioDAO $comentarioDAO;

    public function __construct(ComentarioDAO $comentarioDAO)
    {
        $this->comentarioDAO = $comentarioDAO;
    }

    public function add(Comentario $comentario): void
    {
        if (!$comentario->getConteudo()) return;
        if (!($this->comentarioDAO->add($comentario))) {
            throw new RuntimeException('Não foi possível adicionar o comentário.');
        }
    }

    public function get(int $id): Comentario
    {
        return $this->comentarioDAO->get($id);
    }

    public function update(
        int $id,
        Comentario $comentario
    ): void {
        if (!$comentario->getConteudo()) return;
        if (!($this->comentarioDAO->update($id, $comentario))) {
            throw new RuntimeException('Não foi possível atualizar o comentário.');
        }
    }

    public function remove(Comentario $comentario): void
    {
        $this->comentarioDAO->remove($comentario->getId());
    }
}

class DenunciaService
{
    private DenunciaDAO $denunciaDAO;

    public function __construct(DenunciaDAO $denunciaDAO)
    {
        $this->denunciaDAO = $denunciaDAO;
    }

    public function add(Denuncia $denuncia): void
    {
        if (!$denuncia->getMotivo()) return;
        if (!($this->denunciaDAO->add($denuncia))) {
            throw new RuntimeException('Não foi possível denunciar.');
        }
    }

    public function get(int $id): ?Denuncia
    {
        if ($id <= 0) return null;
        return $this->denunciaDAO->get($id);
    }

    public function update(
        int $id,
        Denuncia $denuncia
    ): void {
        if (!$denuncia->getMotivo()) return;
        if (!($this->denunciaDAO->update($id, $denuncia))) {
            throw new RuntimeException('Não foi possível atualizar essa denúncia.');
        }
    }

    public function remove(Denuncia $denuncia): void
    {
        $this->denunciaDAO->remove($denuncia->getId());
    }
}

class AvaliacaoService {
    private AvaliacaoDAO $avaliacaoDAO;

    public function __construct(AvaliacaoDAO $avaliacaoDAO)
    {
        $this->avaliacaoDAO = $avaliacaoDAO;
    }

    public function add(Avaliacao $avaliacao): void
    {
        if ($avaliacao->getNota() < 0) return;
        if (!($this->avaliacaoDAO->add($avaliacao))) {
            throw new RuntimeException('Não foi possível avaliar esse projeto.');
        }
    }

    public function get(int $id): ?Avaliacao
    {
        if ($id <= 0) return null;
        $saida = $this->avaliacaoDAO->get($id);
        return $saida;
    }

    public function update(
        int $id,
        Avaliacao $avaliacao
    ): void {
        if ($avaliacao->getNota() < 0) return;
        if (!($this->avaliacaoDAO->update($id, $avaliacao))) {
            throw new RuntimeException('Não foi possível atualizar essa avaliação.');
        }
    }

    public function remove(Avaliacao $avaliacao): void
    {
        $this->avaliacaoDAO->remove($avaliacao->getId());
    }
}

?>