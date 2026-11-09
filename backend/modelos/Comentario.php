<?php

require_once __DIR__ . "/Denunciado.php";
require_once __DIR__ . "/Usuario.php";
require_once __DIR__ . "/Projeto.php";

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
