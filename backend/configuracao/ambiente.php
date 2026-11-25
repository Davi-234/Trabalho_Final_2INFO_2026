<?php

/**
 * Lê as configurações do arquivo .env na raiz e as disponibiliza no PHP.
 *
 * Cada linha deve seguir o formato NOME=valor. Valores entre aspas são
 * aceitos, assim como linhas vazias e comentários iniciados por #.
 */
function carregarAmbiente(): void
{
    static $carregado = false;
    if ($carregado) {
        return;
    }

    $arquivo = dirname(__DIR__, 2) . '/.env';
    if (!is_file($arquivo)) {
        throw new RuntimeException('Arquivo .env não encontrado na raiz do projeto. Copie .env.example para .env.');
    }
    if (!is_readable($arquivo)) {
        throw new RuntimeException('O arquivo .env não pode ser lido. Verifique as permissões.');
    }

    $linhas = file($arquivo, FILE_IGNORE_NEW_LINES);
    if ($linhas === false) {
        throw new RuntimeException('Não foi possível ler o arquivo .env.');
    }

    $nomesEncontrados = [];
    foreach ($linhas as $indice => $linha) {
        $linha = trim($linha);
        if ($linha === '' || str_starts_with($linha, '#')) {
            continue;
        }

        if (!preg_match('/\A([A-Z_][A-Z0-9_]*)\s*=\s*(.*)\z/', $linha, $partes)) {
            throw new RuntimeException('Configuração inválida no arquivo .env, linha ' . ($indice + 1) . '.');
        }

        $nome = $partes[1];
        $valor = trim($partes[2]);
        if (isset($nomesEncontrados[$nome])) {
            throw new RuntimeException('A configuração ' . $nome . ' aparece mais de uma vez no arquivo .env.');
        }
        $nomesEncontrados[$nome] = true;

        // Remove aspas que envolvem o valor e avisa sobre aspas sem par.
        $comecaComAspa = str_starts_with($valor, '"') || str_starts_with($valor, "'");
        $terminaComAspa = str_ends_with($valor, '"') || str_ends_with($valor, "'");
        if ($comecaComAspa || $terminaComAspa) {
            if (
                strlen($valor) < 2 ||
                $valor[0] !== $valor[strlen($valor) - 1] ||
                !in_array($valor[0], ['"', "'"], true)
            ) {
                throw new RuntimeException('Aspas inválidas na configuração ' . $nome . ' do arquivo .env.');
            }
            $valor = substr($valor, 1, -1);
        }

        // putenv torna o valor disponível para getenv() no restante da aplicação.
        if (!putenv($nome . '=' . $valor)) {
            throw new RuntimeException('Não foi possível carregar a configuração ' . $nome . ' do arquivo .env.');
        }
        $_ENV[$nome] = $valor;
        $_SERVER[$nome] = $valor;
    }

    $carregado = true;
}
