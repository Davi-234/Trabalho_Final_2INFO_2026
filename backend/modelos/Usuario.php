<?php

require_once __DIR__ . "/Enumeracoes.php";
require_once __DIR__ . "/Denunciado.php";

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
