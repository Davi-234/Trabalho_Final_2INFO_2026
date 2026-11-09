<?php

require_once __DIR__ . "/Enumeracoes.php";
require_once __DIR__ . "/Denunciado.php";
require_once __DIR__ . "/Usuario.php";

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
