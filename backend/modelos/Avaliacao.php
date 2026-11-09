<?php

require_once __DIR__ . "/Projeto.php";
require_once __DIR__ . "/Usuario.php";

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
