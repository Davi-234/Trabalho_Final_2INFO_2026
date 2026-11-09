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
