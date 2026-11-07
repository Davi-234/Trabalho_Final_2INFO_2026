<?php
enum StatusDenuncia: string
{
    case PENDENTE = 'pendente';
    case REMOVIDA = 'removida';
    case ANALISANDO = 'analisando';
}


enum NivelProjeto: string
{
    case INICIANTE = 'iniciante';
    case INTERMEDIARIO = 'intermediario';
    case AVANCADO = 'avancado';
}


enum NivelUsuario: string
{
    case ADM = 'adm';
    case COMUM = 'comum';
}


enum StatusProjeto: string
{
    case ANDAMENTO = 'em_desenvolvimento';
    case PAUSADO = 'pausado';
    case CONCLUIDO = 'completo';
}


abstract class Denunciado
{
    public abstract function toString(): string;
}


class Usuario extends Denunciado
{
    private ?int $id;
    private string $nome;
    private string $email;
    private string $senha;
    private ?string $perfil_github;
    private ?string $perfil_linkedin;
    private ?string $perfil_lattes;
    private NivelUsuario $tipo;

    public function __construct(
        string $nome,
        string $email,
        string $senha,
        ?string $perfil_github = null,
        ?string $perfil_linkedin = null,
        ?string $perfil_lattes = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->perfil_github = $perfil_github;
        $this->perfil_linkedin = $perfil_linkedin;
        $this->perfil_lattes = $perfil_lattes;
        $this->tipo = NivelUsuario::COMUM;
    }

    public function getId(): int
    {
        return $this->id;
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


    public function setPerfilGithub(?string $perfil_github): void
    {
        $this->perfil_github = $perfil_github;
    }


    public function getPerfilGithub(): ?string
    {
        return $this->perfil_github;
    }


    public function setPerfilLinkedin(?string $perfil_linkedin): void
    {
        $this->perfil_linkedin = $perfil_linkedin;
    }


    public function getPerfilLinkedin(): ?string
    {
        return $this->perfil_linkedin;
    }


    public function setPerfilLattes(?string $perfil_lattes): void
    {
        $this->perfil_lattes = $perfil_lattes;
    }


    public function getPerfilLattes(): ?string
    {
        return $this->perfil_lattes;
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
        return "Usuário " .
            $this->nome .
            ", email: " .
            $this->email;
    }
}



class Tag
{
    private ?int $id;
    private string $nome;


    public function __construct(
        string $nome,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->nome = $nome;
    }


    


    public function getId(): int
    {
        return $this->id;
    }


    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }


    public function getNome(): string
    {
        return $this->nome;
    }
}



class Tecnologia
{
    private ?int $id;
    private string $nome;


    public function __construct(
        string $nome,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->nome = $nome;
    }


    


    public function getId(): int
    {
        return $this->id;
    }


    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }


    public function getNome(): string
    {
        return $this->nome;
    }
}



class Projeto extends Denunciado
{
    private ?int $id;
    private Usuario $dono;
    private string $titulo;
    private string $descricao;
    private int $views;
    private array $tecnologias;
    private array $tags;
    private bool $visibilidade;
    private DateTime $data_criacao;
    private DateTime $data_atualizacao;
    private ?string $imagem_projeto;
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
        ?string $imagem_projeto = null,
        array $tecnologias = [],
        array $tags = [],
        ?int $id = null
    ) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->link_repositorio = $link_repositorio;
        $this->visibilidade = $visibilidade;
        $this->dono = $dono;
        $this->data_criacao = $data_criacao;
        $this->data_atualizacao = $data_criacao;
        $this->status = $status;
        $this->nivel = $nivel;
        $this->imagem_projeto = $imagem_projeto;

        $this->views = 0;
        $this->tecnologias = $tecnologias;
        $this->tags = $tags;
    }


    


    public function getId(): int
    {
        return $this->id;
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
        if ($this->views > 0) {
            $this->views--;
        }
    }


    public function setViews(int $views): void
    {
        $this->views = $views;
    }


    public function getViews(): int
    {
        return $this->views;
    }


    public function setTecnologias(array $tecnologias): void
    {
        $this->tecnologias = $tecnologias;
    }


    public function getTecnologias(): array
    {
        return $this->tecnologias;
    }


    public function adicionarTecnologia(Tecnologia $tecnologia): void
    {
        $this->tecnologias[] = $tecnologia;
    }


    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }


    public function getTags(): array
    {
        return $this->tags;
    }


    public function adicionarTag(Tag $tag): void
    {
        $this->tags[] = $tag;
    }


    public function tornarPublico(): void
    {
        $this->visibilidade = true;
    }


    public function tornarPrivado(): void
    {
        $this->visibilidade = false;
    }


    public function isPublico(): bool
    {
        return $this->visibilidade;
    }


    public function setDataCriacao(DateTime $data_criacao): void
    {
        $this->data_criacao = $data_criacao;
    }


    public function getDataCriacao(): DateTime
    {
        return $this->data_criacao;
    }


    public function setDataAtualizacao(DateTime $data_atualizacao): void
    {
        $this->data_atualizacao = $data_atualizacao;
    }


    public function getDataAtualizacao(): DateTime
    {
        return $this->data_atualizacao;
    }


    public function atualizarDataAtualizacao(): void
    {
        $this->data_atualizacao = new DateTime();
    }


    public function setImagemProjeto(?string $imagem_projeto): void
    {
        $this->imagem_projeto = $imagem_projeto;
    }


    public function getImagemProjeto(): ?string
    {
        return $this->imagem_projeto;
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


    public function toString(): string
    {
        return "Projeto: " .
            $this->titulo .
            ", dono: " .
            $this->dono->getNome() .
            ", descrição: " .
            $this->descricao .
            ", status: " .
            $this->status->name .
            ", nível: " .
            $this->nivel->name;
    }
}



class Avaliacao
{
    private ?int $id;
    private int $nota;
    private DateTime $data_criacao;
    private Projeto $projeto;
    private Usuario $usuario;


    public function __construct(
        int $nota,
        Projeto $projeto,
        Usuario $usuario,
        ?DateTime $data_criacao = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->setNota($nota);
        $this->projeto = $projeto;
        $this->usuario = $usuario;
        $this->data_criacao = $data_criacao ?? new DateTime();
    }


    


    public function getId(): int
    {
        return $this->id;
    }


    public function setNota(int $nota): void
    {
        if ($nota < 0 || $nota > 10) {
            throw new InvalidArgumentException(
                "A nota deve estar entre 0 e 10."
            );
        }

        $this->nota = $nota;
    }


    public function getNota(): int
    {
        return $this->nota;
    }


    public function setDataCriacao(DateTime $data_criacao): void
    {
        $this->data_criacao = $data_criacao;
    }


    public function getDataCriacao(): DateTime
    {
        return $this->data_criacao;
    }


    public function setProjeto(Projeto $projeto): void
    {
        $this->projeto = $projeto;
    }


    public function getProjeto(): Projeto
    {
        return $this->projeto;
    }


    public function setUsuario(Usuario $usuario): void
    {
        $this->usuario = $usuario;
    }


    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }
}



class Comentario extends Denunciado
{
    private ?int $id;
    private Usuario $dono;
    private string $conteudo;
    private DateTime $data_postagem;
    private array $filhos;
    private ?Comentario $pai;
    private Projeto $projeto;


    public function __construct(
        Usuario $dono,
        string $conteudo,
        Projeto $projeto,
        ?Comentario $pai = null,
        ?DateTime $data_postagem = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->dono = $dono;
        $this->conteudo = $conteudo;
        $this->projeto = $projeto;
        $this->pai = $pai;
        $this->data_postagem = $data_postagem ?? new DateTime();
        $this->filhos = [];
    }


    


    public function getId(): int
    {
        return $this->id;
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


    public function adicionarFilho(Comentario $comentario): void
    {
        $this->filhos[] = $comentario;
    }


    public function setPai(?Comentario $pai): void
    {
        $this->pai = $pai;
    }


    public function getPai(): ?Comentario
    {
        return $this->pai;
    }


    public function setProjeto(Projeto $projeto): void
    {
        $this->projeto = $projeto;
    }


    public function getProjeto(): Projeto
    {
        return $this->projeto;
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
    private ?int $id;
    private string $motivo;
    private DateTime $data;
    private StatusDenuncia $status;
    private ?Usuario $denunciante;
    private Denunciado $denunciado;


    public function __construct(
        string $motivo,
        ?Usuario $denunciante,
        Denunciado $denunciado,
        ?DateTime $data = null,
        ?StatusDenuncia $status = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->motivo = $motivo;
        $this->denunciante = $denunciante;
        $this->denunciado = $denunciado;
        $this->data = $data ?? new DateTime();
        $this->status = $status ?? StatusDenuncia::ANALISANDO;
    }


    


    public function getId(): int
    {
        return $this->id;
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


    public function setDenunciante(?Usuario $denunciante): void
    {
        $this->denunciante = $denunciante;
    }


    public function getDenunciante(): ?Usuario
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
        $denunciante = $this->denunciante !== null
            ? $this->denunciante->toString()
            : "um usuário não identificado";

        return "A denúncia foi feita pelo " .
            $denunciante .
            ", direcionada ao " .
            $this->denunciado->toString() .
            ", em " .
            $this->data->format("d/m/Y H:i:s") .
            "; em decorrência do motivo: " .
            $this->motivo;
    }
}
?>