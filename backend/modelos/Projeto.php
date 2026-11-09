<?php

require_once __DIR__ . "/Enumeracoes.php";
require_once __DIR__ . "/Denunciado.php";
require_once __DIR__ . "/Usuario.php";
require_once __DIR__ . "/Tag.php";
require_once __DIR__ . "/Tecnologia.php";

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
