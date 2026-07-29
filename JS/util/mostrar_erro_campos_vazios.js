function mostrar_erro(input) {
  $(input.attr("mensagem_correspondente")).addClass("visivel");
}

function apagar_erros(input) {
  $(input.attr("mensagem_correspondente")).removeClass("visivel");
}
