<?php
require_once __DIR__ . '/../../backend/seguranca/sessao.php';
exigirUsuarioAutenticado();
$csrfToken = obterTokenCsrf();
?>
<!doctype html>
<html lang="en">

<head>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet" />
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
  <title>Home</title>
  <link rel="stylesheet" href="../../assets/css/utilidades/estilo_principal.css">
  <link rel="stylesheet" href="../../assets/css/utilidades/header.css">
  <link rel="stylesheet" href="../../assets/css/utilidades/footer.css">
  <link rel="stylesheet" href="../../assets/css/utilidades/formularios.css">
  <link rel="stylesheet" href="../../assets/css/usuario/home.css" />
  <link rel="stylesheet" href="../../assets/css/utilidades/tela_modal.css">
  <link rel="icon" type="image/png" href="../../assets/images/logo.png">
  <style>
    .tela_excluir {
      width: auto;
      height: auto;
      align-items: stretch;
    }

    #logout-form button {
      padding: 0;
      border: 0;
      background: none;
      color: inherit;
      font: inherit;
      cursor: pointer;
    }
  </style>
</head>

<body>
  <!-- ================= HEADER ================= -->
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

    <!-- Navegação principal -->
    <nav>
      <ul>
        <li><a href="../global/explorar.html">Explorar Projetos</a></li>
        <li>
          <form id="logout-form" action="../../backend/controladores/logout.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit">Sair</button>
          </form>
        </li>
        <li>
          <!-- Foto de perfil (placeholder para CSS) -->
          <a href="#">
            <div id="perfil-icone"></div>
          </a>
        </li>
      </ul>
    </nav>
  </header>

  <!-- ================= MAIN ================= -->
  <main>
    <!-- Área do usuário -->
    <section id="usuario">
      <article id="boas-vindas">
        <h2>Bem-vindo ao seu painel</h2>
        <p>
          Gerencie seus projetos, acompanhe avaliações e evolua como
          desenvolvedor.
        </p>
      </article>

      <!-- Ações do usuário -->
      <section id="acoes-usuario">
        <article>
          <h3>Adicionar novo projeto</h3>
          <a href="projeto.php">Criar Projeto</a>
        </article>

        <article>
          <h3>Editar perfil</h3>
          <a href="perfil_usuario_editar.php">Configurações do Perfil</a>
        </article>
      </section>
    </section>

    <!-- Projetos do usuário -->
    <section id="meus-projetos">
      <header>
        <h2>Meus Projetos</h2>
        <p>Projetos que você publicou na plataforma</p>
      </header>
      <section id="lista-projetos"></section>
    </section>
  </main>
  <!-- FOOTER -->
  <footer id="footer">
    DevShare - Sempre abertos à comunidade
  </footer>
</body>
<script src="../../assets/js/usuario/card.js"></script>
</html>