let projetos = [
    {
        id: 1,
        nome: "DevShare",
        visibilidade: "Público",
        descricao: "Rede social para compartilhamento de projetos.",
        comentarios: 32,
        curtidas: 120,
        avaliacao: 4,
        views: 540
    },
    {
        id: 2,
        nome: "MenteBoa",
        visibilidade: "Privado",
        descricao: "Aplicação desenvolvida em Java.",
        comentarios: 15,
        curtidas: 80,
        avaliacao: 5,
        views: 300
    },
    {
        id: 3,
        nome: "Gerenciamento de Acólitos",
        visibilidade: "Público",
        descricao: "O projeto visa uma organização dos membros do acolitato.",
        comentarios: 50,
        curtidas: 200,
        avaliacao: 4,
        views: 700
    }
];


/*
 * =========================================================
 * ORDENAÇÃO
 * =========================================================
 */

function ordenar(lista) {

    let valor = $("#ordenar").val();

    switch (valor) {

        case "comentarios":
            lista.sort((a, b) => b.comentarios - a.comentarios);
            break;

        case "curtidas":
            lista.sort((a, b) => b.curtidas - a.curtidas);
            break;

        case "avaliacao":
            lista.sort((a, b) => b.avaliacao - a.avaliacao);
            break;

        case "views":
            lista.sort((a, b) => b.views - a.views);
            break;

        case "recentes":
            break;
    }

    if ($("#inverter").prop("checked")) {
        lista.reverse();
    }

    return lista;
}



/*
 * =========================================================
 * BUSCA
 * =========================================================
 */

function obterProjetosPublicos() {

    return projetos.filter((projeto) => {
        return projeto.visibilidade.toLowerCase() === "público";
    });
}


function buscar() {

    const palavra = $("#palavra-chave").val().trim().toLowerCase();

    const projetosPublicos = obterProjetosPublicos();

    const resultados = projetosPublicos.filter((projeto) => {

        const nome = projeto.nome.toLowerCase();
        const descricao = projeto.descricao.toLowerCase();

        return nome.includes(palavra) ||
               descricao.includes(palavra);
    });

    carregarProjetos(resultados);
}

/*
 * =========================================================
 * CRIAÇÃO DOS CARDS
 * =========================================================
 */

function carregarProjetos(lista) {

    const listaProjetos = $("#projetos");

    listaProjetos.empty();

    lista.forEach((projeto) => {

        const article = document.createElement("article");

        article.className = "projeto";

        article.innerHTML = `
            <header>
                <h3>
                    <a href="projeto.html?id=${projeto.id}" class="titulo">
                        ${projeto.nome}
                    </a>
                </h3>
            </header>

            <section>
                <p class="descricao">
                    ${projeto.descricao}
                </p>
            </section>

            <footer>
                <ul>
                    <li class="Curtidas">
                        Curtidas: ${projeto.curtidas}
                    </li>

                    <li class="viws">
                        Visualizações: ${projeto.views}
                    </li>

                    <li class="avaliacao">
                        Avaliação: ${projeto.avaliacao}
                    </li>

                    <li class="comentarios">
                        Comentários: ${projeto.comentarios}
                    </li>
                </ul>

                <div class="status">
                    Status:
                    <span>Em Andamento</span>
                </div>
            </footer>
        `;

        listaProjetos.append(article);
    });
}


/*
 * =========================================================
 * FORMULÁRIO DE BUSCA
 * =========================================================
 */

$("#buscador form").on("submit", function(event) {

    event.preventDefault();

    buscar();

});


/*
 * =========================================================
 * FORMULÁRIO DE FILTROS
 * =========================================================
 */

$("#filtros form").on("submit", function(event) {

    event.preventDefault();

    let resultados = obterProjetosPublicos();

    resultados = ordenar(resultados);

    carregarProjetos(resultados);

});


/*
 * =========================================================
 * CARREGAR OS PROJETOS QUANDO A PÁGINA ABRIR
 * =========================================================
 */

$(document).ready(function() {
    carregarProjetos(obterProjetosPublicos());
});