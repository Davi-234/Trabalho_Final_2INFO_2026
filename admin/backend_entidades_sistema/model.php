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

abstract class Denunciado {}

class Usuario extends Denunciado
{
    private string $nome;
    private string $email;
    private string $senha;
    private ?string $perfil_github;
    private ?string $perfil_lattes;
    private ?string $perfil_linkedin;
    private NivelUsuario $tipo;
    private array $projetos;

    public function __construct(
        string $nome,
        string $email,
        string $senha
    ) {
        return;
    }

    public function setNome(string $nome): void
    {
    }

    public function getNome(): string
    {
        return "";
    }

    public function setEmail(string $email): void
    {
    }

    public function getEmail(): string
    {
        return "";
    }

    public function setSenha(string $senha): void
    {
    }

    public function getSenha(): string
    {
        return "";
    }

    public function setPerfilGithub(?string $perfil_github): void
    {
    }

    public function getPerfilGithub(): ?string
    {
        return "";
    }

    public function setPerfilLattes(?string $perfil_lattes): void
    {
    }

    public function getPerfilLattes(): ?string
    {
        return "";
    }

    public function setPerfilLinkedin(?string $perfil_linkedin): void
    {
    }

    public function getPerfilLinkedin(): ?string
    {
        return "";
    }

    public function setTipo(NivelUsuario $tipo): void
    {
    }

    public function getTipo(): NivelUsuario
    {
        return $this->tipo;
    }

    public function setProjetos(array $projetos): void
    {
    }

    public function getProjetos(): array
    {
        return [];
    }

    public function toString(): ?string
    {
        return "";
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
        Usuario $dono
    ) {
        return;
    }

    public function setDono(Usuario $dono): void
    {
    }

    public function getDono(): Usuario
    {
        return $this->dono;
    }

    public function setTitulo(string $titulo): void
    {
    }

    public function getTitulo(): string
    {
        return "";
    }

    public function setDescricao(string $descricao): void
    {
    }

    public function getDescricao(): string
    {
        return "";
    }

    public function setViews(int $views): void
    {
    }

    public function getViews(): int
    {
        return 0;
    }

    public function setCurtidas(int $curtidas): void
    {
    }

    public function getCurtidas(): int
    {
        return 0;
    }

    public function setTecnologias(string $tecnologias): void
    {
    }

    public function getTecnologias(): string
    {
        return "";
    }

    public function setTags(string $tags): void
    {
    }

    public function getTags(): string
    {
        return "";
    }

    public function setVisibilidade(bool $visibilidade): void
    {
    }

    public function getVisibilidade(): bool
    {
        return false;
    }

    public function setDataCriacao(DateTime $data_criacao): void
    {
    }

    public function getDataCriacao(): DateTime
    {
        return new DateTime();
    }

    public function setDataUpdate(DateTime $data_update): void
    {
    }

    public function getDataUpdate(): DateTime
    {
        return new DateTime();
    }

    public function setLinkRepositorio(string $link_repositorio): void
    {
    }

    public function getLinkRepositorio(): string
    {
        return "";
    }

    public function setStatus(StatusProjeto $status): void
    {
    }

    public function getStatus(): StatusProjeto
    {
        return $this->status;
    }

    public function setNivel(NivelProjeto $nivel): void
    {
    }

    public function getNivel(): NivelProjeto
    {
        return $this->nivel;
    }

    public function toString(): ?string
    {
        return "";
    }
}

class Comentario extends Denunciado {
        private Usuario $dono;
        private string $conteudo;
        private DateTime $data_postagem;
        private array $filhos;

        public function __construct(Usuario $dono, string $conteudo)
        {
            
        }

        public function setDono(Usuario $dono): void 
        {

        }

        public function getDono(): Usuario
        {
            return new Usuario("","","");
        }

        public function setConteudo(string $conteudo): void
        {

        }

        public function getConteudo(): string
        {
            return "";
        }

        public function setDataPostagem(DateTime $data_postagem): void
        {

        }

        public function getDataPostagem(): DateTime
        {
            return new DateTime('2026-12-25 20:00:00');
        }

        public function setFilhos(array $filhos): void
        {

        }

        public function getFilhos(): array
        {
            return [];
        }

        public function toString(): string {
            return "";
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
        return;
    }

    public function setMotivo(string $motivo): void
    {
    }

    public function getMotivo(): string
    {
        return "";
    }

    public function setData(DateTime $data): void
    {
    }

    public function getData(): DateTime
    {
        return new DateTime();
    }

    public function setStatus(StatusDenuncia $status): void
    {
    }

    public function getStatus(): StatusDenuncia
    {
        return $this->status;
    }

    public function setDenunciante(Usuario $denunciante): void
    {
    }

    public function getDenunciante(): Usuario
    {
        return $this->denunciante;
    }

    public function setDenunciado(Denunciado $denunciado): void
    {
    }

    public function getDenunciado(): Denunciado
    {
        return $this->denunciado;
    }

    public function toString(): ?string
    {
        return "";
    }
}