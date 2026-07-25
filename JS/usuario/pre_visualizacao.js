function alterar_PreVisualizacao() {
    let titulo = $('#nome').val();
    let descricao = $('#descricao').val();
    let nivel = $('#nivel option:selected').text();
    let tipo = $('#tipo option:selected').text();

    if (titulo.trim() == '') {
        titulo = 'Nome do Projeto';
    }
    $('#preview_title').html(titulo);
    if (descricao.trim() == '') {
        descricao = "Aqui aparecerá a descrição do projeto informada no formulário, permitindo uma visão geral antes da publicação.";
    } 
    $('#preview_description').html(descricao);

    $('#preview_nivel').html(nivel);
    $('#preview_type').html(tipo);
}

$(':input').on('input', () => {
    alterar_PreVisualizacao();
});