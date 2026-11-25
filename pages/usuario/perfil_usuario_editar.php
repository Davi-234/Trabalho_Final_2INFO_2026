<?php
require_once __DIR__ . '/../../backend/seguranca/sessao.php';
exigirUsuarioAutenticado();
$csrfToken = obterTokenCsrf();
?>
<!-- Essa página aparece para o usuário dono do perfil -->
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    <title>Perfil</title>
    <link rel="stylesheet" href="../../assets/css/utilidades/tela_modal.css">
    <link rel="stylesheet" href="../../assets/css/usuario/perfil_estilos/perfil_usuario.css">
    <link rel="stylesheet" href="../../assets/css/utilidades/estrela.css">
    <link rel="stylesheet" href="../../assets/css/utilidades/projetos.css">
    <link rel="stylesheet" href="../../assets/css/utilidades/estilo_principal.css">
    <link rel="stylesheet" href="../../assets/css/utilidades/header.css">
    <link rel="stylesheet" href="../../assets/css/utilidades/formularios.css">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
</head>

<body>
    <header>
        <!-- Logo + Nome do Projeto -->
        <div id="logo-container">
            <h1>
                <span>
                    <img src="../../assets/images/logo.png" alt="Project community logo" id="logo" />
                </span>
                DevShare
            </h1>
            <p>Compartilhe, avalie e evolua projetos com a comunidade</p>
        </div>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="../global/explorar.html">Explorar</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <form id="perfil-form" action="../../backend/controladores/perfil.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <p id="mensagem-perfil" role="status" aria-live="polite"></p>

            <section id="foto-perfil">
                <div id="avatar-circulo">
                    <span id="avatar-inicial" aria-hidden="true">?</span>
                    <img id="avatar-imagem" alt="Foto de perfil" hidden>
                </div>
                <input id="imagem-perfil" type="file" accept="image/jpeg,image/png,image/webp" hidden>
                <button type="button" class="btn" id="editar-imagem">Editar imagem</button>
                <p id="mensagem-imagem" role="status" aria-live="polite"></p>
            </section>

            <section id="dados_pessoais_perfil">
                <fieldset>
                    <legend>Dados pessoais</legend>
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" maxlength="100" required>
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" maxlength="255" required>
                </fieldset>
            </section>

            <section id="dados_contato_perfil">
                <fieldset>
                    <legend>Contatos</legend>
                    <label for="link_github">GitHub:</label>
                    <input type="url" id="link_github" name="link_github" maxlength="255">
                    <label for="link_linkedin">LinkedIn:</label>
                    <input type="url" id="link_linkedin" name="link_linkedin" maxlength="255">
                </fieldset>
            </section>

            <section>
                <fieldset>
                    <legend>Confirmação de segurança</legend>
                    <label for="senha_atual">Senha atual:</label>
                    <input type="password" id="senha_atual" name="senha_atual" autocomplete="current-password" required>
                    <label for="nova_senha">Nova senha (opcional):</label>
                    <input type="password" id="nova_senha" name="nova_senha" autocomplete="new-password">
                    <label for="confirmar_senha">Confirmar nova senha:</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" autocomplete="new-password">
                </fieldset>
            </section>

            <section id="btns_alterar">
                <button type="submit" class="btn" id="salvar_perfil">Salvar alterações</button>
                <button type="button" class="btn" id="excluir_conta">Excluir conta</button>
            </section>
        </form>
    </main>

    <!-- FOOTER -->
    <footer id="footer">
        DevShare — compartilhe ideias, encontre parceiros e transforme projetos em realidade.
    </footer>
</body>
<script src="../../assets/js/usuario/perfil.js"></script>

</html>