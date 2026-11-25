<?php
require_once __DIR__ . "/../dao/dao.php";
require_once __DIR__ . "/../modelos/model.php";

class UsuarioService
{
    private UsuarioDAO $usuarioDAO;

    /**
     * Recebe o DAO que executará as consultas de usuário no banco.
     */
    public function __construct(UsuarioDAO $usuarioDAO)
    {
        $this->usuarioDAO = $usuarioDAO;
    }

    /**
     * Normaliza e valida os dados de uma conta antes de pedir ao DAO para cadastrá-la.
     * Lança InvalidArgumentException para dados inválidos e RuntimeException se falhar ao salvar.
     */
    public function add(Usuario $usuario): void
    {
        // Remove espaços acidentais antes de validar e salvar os dados.
        $usuario->setNome(trim($usuario->getNome()));
        $usuario->setEmail(trim($usuario->getEmail()));
        $this->validarUsuario($usuario);

        if (!$this->usuarioDAO->add($usuario)) {
            throw new RuntimeException('Não foi possível realizar o cadastro.');
        }
    }

    /**
     * Confere as credenciais e retorna o usuário autenticado ou null.
     * Também migra para password_hash uma senha válida que ainda use o hash legado.
     */
    public function login(string $email, string $senha): ?Usuario
    {
        $email = trim($email);
        // null significa que os dados não autenticam nenhuma conta.
        if ($email === '' || $senha === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
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

        // Aceita hashes antigos só para migrá-los para password_hash no login.
        $hashLegado = hash('sha256', $senha);
        if (
            !preg_match('/\A[a-f0-9]{64}\z/i', $hashArmazenado) ||
            !hash_equals(strtolower($hashArmazenado), $hashLegado)
        ) {
            return null;
        }

        $novoHash = password_hash($senha, PASSWORD_DEFAULT);
        if (!$this->usuarioDAO->updatePasswordHash($usuario->getId(), $novoHash)) {
            throw new RuntimeException('Não foi possível atualizar a senha da conta.');
        }

        $usuario->setSenha($novoHash);
        return $usuario;
    }

    /**
     * Cria um token temporário de recuperação para o e-mail informado.
     * Retorna o token para envio ao usuário, ou null quando o e-mail não é válido/não existe.
     */
    public function gerarTokenRecuperacaoSenha(string $email): ?string
    {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $usuario = $this->usuarioDAO->get($email);
        if ($usuario === null) {
            return null;
        }

        $token = bin2hex(random_bytes(32));
        // No banco fica somente o hash; o token original é enviado ao usuário.
        $tokenHash = hash('sha256', $token);
        $expiraEm = new DateTimeImmutable('+1 hour');
        if (!$this->usuarioDAO->criarTokenRecuperacao($usuario->getId(), $tokenHash, $expiraEm)) {
            throw new RuntimeException('Não foi possível gerar a recuperação de senha.');
        }

        return $token;
    }

    /**
     * Valida o token e a nova senha e pede ao DAO para atualizar a conta.
     * Lança InvalidArgumentException se a senha ou o token não forem aceitos.
     */
    public function redefinirSenha(string $token, string $novaSenha): void
    {
        // Valida antes de gastar uma consulta ao banco.
        $this->validarSenha($novaSenha);
        if (!preg_match('/\A[a-f0-9]{64}\z/', $token)) {
            throw new \InvalidArgumentException('O link de recuperação é inválido ou expirou.');
        }

        $tokenHash = hash('sha256', $token);
        $passwordHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        if (!$this->usuarioDAO->redefinirSenha($tokenHash, $passwordHash)) {
            throw new InvalidArgumentException('O link de recuperação é inválido ou expirou.');
        }
    }

    /**
     * Aplica as regras do cadastro: campos, limites, e-mail, links, senha e unicidade.
     * É privada porque só deve ser usada pelo fluxo de cadastro desta classe.
     */
    private function validarUsuario(Usuario $usuario): void
    {
        $nome = $usuario->getNome();
        $email = $usuario->getEmail();
        if ($nome === '' || strlen($nome) > 100 || $email === '' || strlen($email) > 255 || $usuario->getSenha() === '') {
            throw new InvalidArgumentException('Informe nome, e-mail e senha válidos.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('O e-mail informado é inválido.');
        }

        // Reaproveita a mesma regra de URL no cadastro e na edição do perfil.
        $this->validarLinkPerfil('GitHub', $usuario->getPerfilGithub());
        $this->validarLinkPerfil('LinkedIn', $usuario->getPerfilLinkedin());
        $this->validarSenha($usuario->getSenha(), false);

        if ($this->usuarioDAO->get($email) !== null) {
            throw new InvalidArgumentException('Este endereço de e-mail já está cadastrado.');
        }
    }

    /**
     * Busca uma conta por e-mail (string) ou ID (int), rejeitando identificadores vazios.
     * Retorna null quando o identificador é inválido ou o DAO não encontra usuário.
     */
    public function get(string|int $identificador): ?Usuario
    {
        // Evita consultar o banco com identificadores vazios ou impossíveis.
        if ((is_string($identificador) && trim($identificador) === '') || (is_int($identificador) && $identificador < 1)) {
            return null;
        }
        return $this->usuarioDAO->get($identificador);
    }

    /**
     * Valida o caminho gerado para a foto e salva-o na conta indicada pelo ID.
     * Lança InvalidArgumentException para caminho inválido ou RuntimeException se o DAO falhar.
     */
    public function atualizarFotoPerfil(int $id, string $caminho): void
    {
        // Só aceita caminhos com o nome aleatório e extensões gerados pelo controller.
        if (
            $id < 1 ||
            !preg_match('/\Aassets\/images\/perfil_[a-f0-9]{32}\.(jpg|png|webp)\z/', $caminho)
        ) {
            throw new InvalidArgumentException('Imagem de perfil inválida.');
        }
        if (!$this->usuarioDAO->atualizarFotoPerfil($id, $caminho)) {
            throw new RuntimeException('Não foi possível salvar a imagem do perfil.');
        }
    }

    /**
     * Valida e atualiza os dados editáveis do perfil e, opcionalmente, a senha.
     * Impede usar o e-mail de outra conta e lança exceções em caso de erro.
     */
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

        if ($id < 1 || $nome === '' || strlen($nome) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
            throw new InvalidArgumentException('Informe um nome e e-mail válidos.');
        }

        foreach (['GitHub' => $linkGithub, 'LinkedIn' => $linkLinkedin] as $nomeCampo => $link) {
            $this->validarLinkPerfil($nomeCampo, $link);
        }

        $usuarioAtual = $this->usuarioDAO->get($id);
        if ($usuarioAtual === null) {
            throw new RuntimeException('Conta não encontrada.');
        }

        // E-mail pode continuar igual para esta conta, mas não pertencer a outra.
        $usuarioComEmail = $this->usuarioDAO->get($email);
        if ($usuarioComEmail !== null && $usuarioComEmail->getId() !== $id) {
            throw new InvalidArgumentException('Este endereço de e-mail já está cadastrado.');
        }

        // A senha só entra nos dados de atualização quando foi preenchida.
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
            throw new RuntimeException('Não foi possível atualizar o perfil.');
        }
    }

    /**
     * Exclui a conta correspondente ao objeto recebido.
     * Lança RuntimeException se o ID não for válido ou se o DAO não conseguir excluir.
     */
    public function remove(Usuario $usuario): void
    {
        // O DAO devolve false quando a exclusão não pôde ser concluída.
        if ($usuario->getId() < 1 || !$this->usuarioDAO->remove($usuario->getId())) {
            throw new RuntimeException('Não foi possível excluir a conta.');
        }
    }

    /**
     * Aceita link opcional vazio; se preenchido, exige URL HTTP/HTTPS com tamanho permitido.
     */
    private function validarLinkPerfil(string $nomeCampo, ?string $link): void
    {
        if ($link === null || trim($link) === '') {
            return;
        }

        $partes = parse_url($link);
        // Links sociais devem ser URLs completas e usar apenas HTTP ou HTTPS.
        if (
            strlen($link) > 255 ||
            filter_var($link, FILTER_VALIDATE_URL) === false ||
            !is_array($partes) ||
            !in_array(strtolower($partes['scheme'] ?? ''), ['http', 'https'], true)
        ) {
            throw new InvalidArgumentException("O link de {$nomeCampo} é inválido.");
        }
    }

    /**
     * Exige no mínimo 8 caracteres, maiúscula, minúscula, número e símbolo.
     * A flag $nova escolhe a mensagem adequada para senha nova ou cadastro.
     */
    private function validarSenha(string $senha, bool $nova = true): void
    {
        // Mantém a política de senha igual no cadastro, perfil e recuperação.
        if (strlen($senha) < 8 || !preg_match('/[A-Z]/', $senha) || !preg_match('/[a-z]/', $senha) || !preg_match('/[0-9]/', $senha) || !preg_match('/[\W_]/', $senha)) {
            $mensagem = $nova
                ? 'A nova senha não atende aos requisitos mínimos de segurança.'
                : 'A senha não atende aos requisitos mínimos de segurança.';
            throw new InvalidArgumentException($mensagem);
        }
    }
}

class ProjetoService
{
    private ProjetoDAO $projetoDAO;

    /**
     * Recebe o DAO que executará as consultas de projeto no banco.
     */
    public function __construct(ProjetoDAO $projetoDAO)
    {
        $this->projetoDAO = $projetoDAO;
    }

    /**
     * Valida filtros e ordenação antes de solicitar ao DAO a lista pública paginada.
     * Retorna os projetos e os metadados de paginação.
     */
    public function listarPublicos(
        string $busca,
        string $nivel,
        string $tecnologias,
        int $pagina,
        string $ordenar,
        bool $inverter
    ): array
    {
        // Lista fechada: nenhum texto recebido do usuário vira coluna SQL.
        $ordenacoesPermitidas = ['recentes', 'views', 'avaliacao', 'comentarios'];
        if (
            strlen($busca) > 200 ||
            strlen($tecnologias) > 200 ||
            $pagina < 1 ||
            ($nivel !== '' && NivelProjeto::tryFrom($nivel) === null) ||
            !in_array($ordenar, $ordenacoesPermitidas, true)
        ) {
            throw new InvalidArgumentException('Filtros de busca inválidos.');
        }

        return $this->projetoDAO->listarPublicos($busca, $nivel, $tecnologias, $pagina, $ordenar, $inverter);
    }

    /**
     * Valida os campos e relações do projeto e pede ao DAO para cadastrá-lo.
     * Lança RuntimeException se o DAO não conseguir salvar.
     */
    public function add(Projeto $projeto): void
    {
        // Regras de dados ficam no service; o DAO cuida apenas de persistir.
        $this->validarProjeto($projeto);
        if (!$this->projetoDAO->add($projeto)) {
            throw new RuntimeException('Não foi possível salvar o projeto.');
        }
    }
    /**
     * Busca por link apenas projetos públicos.
     * Retorna null para link vazio, longo demais ou sem projeto público correspondente.
     */
    public function get(string $linkRepositorio): ?Projeto
    {
        // A busca por link é pública; projetos privados não são retornados aqui.
        if ($linkRepositorio === '' || strlen($linkRepositorio) > 255) {
            return null;
        }
        return $this->projetoDAO->getPublico($linkRepositorio);
    }

    /**
     * Lista os projetos pertencentes ao usuário indicado pelo ID.
     * Rejeita IDs inválidos antes de chamar o DAO.
     */
    public function listarDoUsuario(int $usuarioId): array
    {
        $this->validarId($usuarioId, 'Conta');
        return $this->projetoDAO->listarDoUsuario($usuarioId);
    }

    /**
     * Busca um projeto somente se projeto e usuário forem os IDs informados.
     * Retorna null se não existir ou não pertencer à conta.
     */
    public function getDoUsuario(int $projetoId, int $usuarioId): ?Projeto
    {
        $this->validarId($projetoId, 'Projeto');
        $this->validarId($usuarioId, 'Conta');
        return $this->projetoDAO->getDoUsuario($projetoId, $usuarioId);
    }

    /**
     * Exclui somente o projeto pertencente à conta informada.
     * Retorna false se nenhum projeto correspondente foi removido.
     */
    public function removeDoUsuario(int $projetoId, int $usuarioId): bool
    {
        // O DAO retorna false quando não existe projeto com esse dono.
        $this->validarId($projetoId, 'Projeto');
        $this->validarId($usuarioId, 'Conta');
        return $this->projetoDAO->removeDoUsuario($projetoId, $usuarioId);
    }

    /**
     * Valida e atualiza projeto e relações somente para o dono informado.
     * Compara também o dono do objeto e lança RuntimeException se o DAO falhar.
     */
    public function updateDoUsuario(int $projetoId, int $usuarioId, Projeto $projeto): void
    {
        $this->validarId($projetoId, 'Projeto');
        $this->validarId($usuarioId, 'Conta');
        $this->validarProjeto($projeto);

        if ($projeto->getDono()->getId() !== $usuarioId) {
            throw new InvalidArgumentException('O dono do projeto não corresponde à conta autenticada.');
        }

        // Converte o objeto de domínio para os campos que o DAO sabe atualizar.
        $valores = [
            'visibilidade' => $projeto->isPublico(),
            'descricao' => $projeto->getDescricao(),
            'nome' => $projeto->getTitulo(),
            'status' => $projeto->getStatus(),
            'nivel' => $projeto->getNivel(),
            'imagem_projeto' => $projeto->getImagemProjeto(),
            'link_repositorio' => $projeto->getLinkRepositorio(),
            'tags' => $projeto->getTags(),
            'tecnologias' => $projeto->getTecnologias(),
        ];

        if (!$this->projetoDAO->updateDoUsuario($projetoId, $usuarioId, $valores)) {
            throw new RuntimeException('Não foi possível atualizar o projeto.');
        }
    }

    /**
     * Confere os campos, URL, caminho da imagem, tags e tecnologias do projeto.
     * Normaliza título, descrição e link; dados inválidos geram InvalidArgumentException.
     */
    private function validarProjeto(Projeto $projeto): void
    {
        $this->validarId($projeto->getDono()->getId(), 'Conta');

        // Normaliza textos para evitar salvar espaços no início ou no fim.
        $titulo = trim($projeto->getTitulo());
        $descricao = trim($projeto->getDescricao());
        $link = trim($projeto->getLinkRepositorio());
        $projeto->setTitulo($titulo);
        $projeto->setDescricao($descricao);
        $projeto->setLinkRepositorio($link);
        $partesLink = parse_url($link);
        if (
            $titulo === '' || strlen($titulo) > 200 ||
            $descricao === '' || strlen($descricao) > 500 ||
            strlen($link) > 255 ||
            filter_var($link, FILTER_VALIDATE_URL) === false ||
            !is_array($partesLink) ||
            !in_array(strtolower($partesLink['scheme'] ?? ''), ['http', 'https'], true)
        ) {
            throw new InvalidArgumentException('Confira o título, a descrição e o link do projeto.');
        }

        $imagem = $projeto->getImagemProjeto();
        if (
            $imagem !== null &&
            (
                !str_starts_with($imagem, 'assets/images/') ||
                strlen($imagem) > 300 ||
                str_contains($imagem, '..') ||
                str_contains($imagem, '\\')
            )
        ) {
            throw new InvalidArgumentException('A imagem do projeto é inválida.');
        }

        // Cada item precisa ser do tipo esperado e respeitar os limites do banco.
        foreach ([
            'tags' => [$projeto->getTags(), Tag::class, 30],
            'tecnologias' => [$projeto->getTecnologias(), Tecnologia::class, 50],
        ] as $campo => [$itens, $classe, $limite]) {
            if ($itens === [] || count($itens) > 20) {
                throw new InvalidArgumentException('Informe tags e tecnologias válidas.');
            }

            foreach ($itens as $item) {
                if (!($item instanceof $classe) || trim($item->getNome()) === '' || strlen($item->getNome()) > $limite) {
                    throw new InvalidArgumentException("Há um item inválido em {$campo}.");
                }
            }
        }
    }

    /**
     * Garante que IDs de conta e projeto sejam positivos.
     * O nome da entidade é usado para montar a mensagem do erro.
     */
    private function validarId(int $id, string $entidade): void
    {
        if ($id < 1) {
            throw new InvalidArgumentException("Identificador de {$entidade} inválido.");
        }
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

    public function get(int $id): ?Comentario
    {
        if ($id <= 0) return null;
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