$("#editar_dados").on("click", function () {
  const modal_window = $(`
    <div id="tela_modal">
        <h1>Editar dados</h1>
        <fieldset>
            <form id="form_uptade" name="form_uptade" action="#" method="get">
              <legend>Dados:</legend>
              <label for="name_uptade">Nome:</label>
              <input type="text" id="name_uptade" name="name_uptade">
              <label for="link_git_uptade">GitHub:</label>
              <input type="text" id="link_git_uptade" name="link_git_uptade">
              <label for="linkedin_uptade">Linkedin:</label>
              <input type="text" id="linkedin_uptade" name="linkedin_uptade">
              <label for="email_uptade">E-mail:</label>
              <input type="text" id="email_uptade" name="email_uptade">
            </form>
        </fieldset>
        <div>
            <button type="submit" id="confirmar_alteracoes" class="btn">Confirmar Alterações</button>
            <button type="" id="sair_edicao_dados" class="btn" onclick="$('#tela_modal').remove(); $('body').removeClass('esconder');">Sair da edição</button>
        </div>
    </div>
    `);
  $("body").append(modal_window);
  $("body").addClass("esconder");
});

// =================================== Prenche os inputs com os dados do usuário ============================================= //