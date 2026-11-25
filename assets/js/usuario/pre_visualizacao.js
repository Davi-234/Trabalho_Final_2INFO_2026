function alterar_PreVisualizacao(input_selecionado) {
  if (!input_selecionado) {
    return;
  }

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
    element.textContent = titulo_ou_descricao_valor;
  }
  if (input_selecionado !== "titulo" && input_selecionado !== "descricao") {
    let select_id_aux = "#" + input_selecionado + " option:selected";
    let select_option_text = $(select_id_aux).text();

    let select_preview_id_aux = "preview_" + input_selecionado;
    let element = document.getElementById(select_preview_id_aux);
    if (!element) {
      return;
    }
    element.textContent = select_option_text;
  }
}

$(":input").on("input", function () {
  alterar_PreVisualizacao(this.id);
  $($(this).attr("mensagem_correspondente")).removeClass("visivel");
  apagar_elemento("tela_falta_dados");
});
