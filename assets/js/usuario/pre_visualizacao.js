function alterar_PreVisualizacao(input_selecionado) {
  if (input_selecionado === "status" || input_selecionado === "tags" || input_selecionado === "tecnologias" || input_selecionado === "link") {
    return;
  }

  if (input_selecionado === "titulo" || input_selecionado === "descricao") {
    let titulo_ou_descricao_valor =
      document.getElementById(input_selecionado).value;

    if (titulo_ou_descricao_valor.trim() === "") {
      if (input_selecionado === "titulo") {
        titulo_ou_descricao_valor = "Nome do Projeto";
      }

      if (input_selecionado === "descricao") {
        titulo_ou_descricao_valor =
          "Aqui aparecerá a descrição do projeto informada no formulário, permitindo uma visão geral antes da publicação.";
      }
    }

    let preview_titulo_ou_descricao_id = "preview_" + input_selecionado;
    let element = document.getElementById(preview_titulo_ou_descricao_id);
    element.innerHTML = titulo_ou_descricao_valor;
  }
  if (input_selecionado !== "titulo" && input_selecionado !== "descricao") {
    let select_id_aux = "#" + input_selecionado + " option:selected";
    let select_option_text = $(select_id_aux).text();

    let select_preview_id_aux = "preview_" + input_selecionado;
    let element = document.getElementById(select_preview_id_aux);
    element.innerHTML = select_option_text;
  }
}

// O evento é de mundança dos inputs, valendo para qualquer alteração, e para quaisquer inputs
$(":input").on("input", function () {
  // Função responsável pela pré-visualização
  alterar_PreVisualizacao(this.id);
  // Remove a mensagem de erro de campo vazio, após ocorrer uma mundança no valor do input
  $($(this).attr("mensagem_correspondente")).removeClass("visivel");
  // Retira a tela que diz o número de inputs vazios, após ocorrer a alteração de algum dos inputs
  apagar_elemento("tela_falta_dados");
});
