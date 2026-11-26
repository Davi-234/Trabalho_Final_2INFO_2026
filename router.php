<?php

// O servidor PHP embutido não aplica regras do .htaccess. Bloqueie arquivos
// ocultos antes de deixar o servidor atender arquivos estáticos do projeto.
$caminho = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
if (preg_match('~(?:^|/)\.[^/]*~', $caminho)) {
    http_response_code(404);
    exit;
}

return false;
