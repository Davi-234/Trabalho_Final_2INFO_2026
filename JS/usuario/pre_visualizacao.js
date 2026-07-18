function alterar_PreVisualizacao() {
    let titulo = $('#nome').val();
    let descricao = $('#descricao').val();

    $('#preview_title').html(titulo);
    $('#preview_description').html(descricao);
}

setInterval(function() {
    alterar_PreVisualizacao()
}, 100);