<?php

enum StatusDenuncia
{
    case EM_ANALISE;
    case REJEITADA;
    case ACEITA;
}

enum NivelProjeto
{
    case INICIANTE;
    case INTERMEDIARIO;
    case AVANCADO;
}

enum NivelUsuario
{
    case ADM;
    case COMUM;
}

enum StatusProjeto
{
    case ANDAMENTO;
    case PAUSADO;
    case CONCLUIDO;
}

abstract class Denunciado
{
    public abstract function toString(): string;
}

class Usuario extends Denunciado
{
    private string $nome;
    private string $email;
    private string $senha;
    private string $perfil_github;
    private string $perfil_lattes;
    private string $perfil_linkedin;
    private NivelUsuario $tipo;

    public function __construct(
        string $nome,
        string $email,
        string $senha,
        string $perfil_github = "",
        string $perfil_lattes = "",
        string $perfil_linkedin = ""
    ) {
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->perfil_github = $perfil_github;
        $this->perfil_lattes = $perfil_lattes;
        $this->perfil_linkedin = $perfil_linkedin;
        $this->tipo = NivelUsuario::COMUM;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setSenha(string $senha): void
    {
        $this->senha = $senha;
    }

    public function getSenha(): string
    {
        return $this->senha;
    }

    public function setPerfilGithub(string $perfil_github): void
    {
        $this->perfil_github = $perfil_github;
    }

    public function getPerfilGithub(): string
    {
        return $this->perfil_github;
    }

    public function setPerfilLattes(string $perfil_lattes): void
    {
        $this->perfil_lattes = $perfil_lattes;
    }

    public function getPerfilLattes(): string
    {
        return $this->perfil_lattes;
    }

    public function setPerfilLinkedin(string $perfil_linkedin): void
    {
        $this->perfil_linkedin = $perfil_linkedin;
    }

    public function getPerfilLinkedin(): string
    {
        return $this->perfil_linkedin;
    }

    public function setTipo(NivelUsuario $tipo): void
    {
        $this->tipo = $tipo;
    }

    public function getTipo(): NivelUsuario
    {
        return $this->tipo;
    }

    public function toString(): string
    {
        return "Usuário " . $this->nome .
               ", email: " . $this->email;
    }
}

class Projeto extends Denunciado
{
    private Usuario $dono;
    private string $titulo;
    private string $descricao;
    private int $views;
    private int $curtidas;
    private string $tecnologias;
    private string $tags;
    private bool $visibilidade;
    private DateTime $data_criacao;
    private DateTime $data_update;
    private string $link_repositorio;
    private StatusProjeto $status;
    private NivelProjeto $nivel;

    public function __construct(
        string $titulo,
        string $descricao,
        string $link_repositorio,
        bool $visibilidade,
        Usuario $dono,
        DateTime $data_criacao,
        StatusProjeto $status,
        NivelProjeto $nivel,
        string $tecnologias = "",
        string $tags = ""
    ) {
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->link_repositorio = $link_repositorio;
        $this->visibilidade = $visibilidade;
        $this->dono = $dono;
        $this->data_criacao = $data_criacao;
        $this->data_update = $data_criacao;
        $this->status = $status;
        $this->nivel = $nivel;

        $this->views = 0;
        $this->curtidas = 0;
        $this->tecnologias = $tecnologias;
        $this->tags = $tags;
    }

    public function setDono(Usuario $dono): void
    {
        $this->dono = $dono;
    }

    public function getDono(): Usuario
    {
        return $this->dono;
    }

    public function setTitulo(string $titulo): void
    {
        $this->titulo = $titulo;
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function setDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function aumentarViews(): void
    {
        $this->views++;
    }

    public function decrementarViews(): void
    {
        $this->views--;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function aumentarCurtidas(): void
    {
        $this->curtidas++;
    }

    public function decrementarCurtidas(): void
    {
        $this->curtidas--;
    }

    public function getCurtidas(): int
    {
        return $this->curtidas;
    }

    public function setTecnologias(string $tecnologias): void
    {
        $this->tecnologias = $tecnologias;
    }

    public function getTecnologias(): string
    {
        return $this->tecnologias;
    }

    public function setTags(string $tags): void
    {
        $this->tags = $tags;
    }

    public function getTags(): string
    {
        return $this->tags;
    }

    public function tornarPublico(): void
    {
        $this->visibilidade = true;
    }

    public function tornarPrivado(): void
    {
        $this->visibilidade = false;
    }

    public function setDataCriacao(DateTime $data_criacao): void
    {
        $this->data_criacao = $data_criacao;
    }

    public function getDataCriacao(): DateTime
    {
        return $this->data_criacao;
    }

    public function atualizar_DataUpdate(): void
    {
        $this->data_update = new DateTime();
    }

    public function getDataUpdate(): DateTime
    {
        return $this->data_update;
    }

    public function setLinkRepositorio(string $link_repositorio): void
    {
        $this->link_repositorio = $link_repositorio;
    }

    public function getLinkRepositorio(): string
    {
        return $this->link_repositorio;
    }

    public function setStatus(StatusProjeto $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): StatusProjeto
    {
        return $this->status;
    }

    public function setNivel(NivelProjeto $nivel): void
    {
        $this->nivel = $nivel;
    }

    public function getNivel(): NivelProjeto
    {
        return $this->nivel;
    }

    public function isPublico(): bool
    {
        return $this->visibilidade;
    }

    public function toString(): string
    {
        return "Projeto: " . $this->titulo .
               ", dono: " . $this->dono->getNome() .
               ", descrição: " . $this->descricao .
               ", status: " . $this->status->name .
               ", nível: " . $this->nivel->name;
    }
}

class Comentario extends Denunciado
{
    private Usuario $dono;
    private string $conteudo;
    private DateTime $data_postagem;
    private array $filhos;
    private Projeto $projeto;

    public function __construct(
        Usuario $dono,
        string $conteudo,
        Projeto $projeto
    ) {
        $this->dono = $dono;
        $this->conteudo = $conteudo;
        $this->projeto = $projeto;
        $this->data_postagem = new DateTime();
        $this->filhos = [];
    }

    public function setDono(Usuario $dono): void
    {
        $this->dono = $dono;
    }

    public function getDono(): Usuario
    {
        return $this->dono;
    }

    public function setConteudo(string $conteudo): void
    {
        $this->conteudo = $conteudo;
    }

    public function getConteudo(): string
    {
        return $this->conteudo;
    }

    public function setDataPostagem(DateTime $data_postagem): void
    {
        $this->data_postagem = $data_postagem;
    }

    public function getDataPostagem(): DateTime
    {
        return $this->data_postagem;
    }

    public function setFilhos(array $filhos): void
    {
        $this->filhos = $filhos;
    }

    public function getFilhos(): array
    {
        return $this->filhos;
    }

    public function setProjeto(Projeto $projeto): void
    {
        $this->projeto = $projeto;
    }

    public function getProjeto(): Projeto
    {
        return $this->projeto;
    }

    public function adicionarFilho(Comentario $comentario): void
    {
        $this->filhos[] = $comentario;
    }

    public function toString(): string
    {
        return "Comentário de " .
               $this->dono->getNome() .
               ": " .
               $this->conteudo .
               ", publicado em " .
               $this->data_postagem->format("d/m/Y H:i:s");
    }
}

class Denuncia
{
    private string $motivo;
    private DateTime $data;
    private StatusDenuncia $status;
    private Usuario $denunciante;
    private Denunciado $denunciado;

    public function __construct(
        string $motivo,
        Usuario $denunciante,
        Denunciado $denunciado
    ) {
        $this->motivo = $motivo;
        $this->denunciante = $denunciante;
        $this->denunciado = $denunciado;
        $this->data = new DateTime();
        $this->status = StatusDenuncia::EM_ANALISE;
    }

    public function setMotivo(string $motivo): void
    {
        $this->motivo = $motivo;
    }

    public function getMotivo(): string
    {
        return $this->motivo;
    }

    public function setData(DateTime $data): void
    {
        $this->data = $data;
    }

    public function getData(): DateTime
    {
        return $this->data;
    }

    public function setStatus(StatusDenuncia $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): StatusDenuncia
    {
        return $this->status;
    }

    public function setDenunciante(Usuario $denunciante): void
    {
        $this->denunciante = $denunciante;
    }

    public function getDenunciante(): Usuario
    {
        return $this->denunciante;
    }

    public function setDenunciado(Denunciado $denunciado): void
    {
        $this->denunciado = $denunciado;
    }

    public function getDenunciado(): Denunciado
    {
        return $this->denunciado;
    }

    public function toString(): string
    {
        return "A denúncia foi feita pelo " .
            $this->denunciante->toString() .
            ", direcionada ao " .
            $this->denunciado->toString() .
            ", em " .
            $this->data->format("d/m/Y H:i:s") .
            "; em decorrência do motivo: " .
            $this->motivo;
    }
}
?>