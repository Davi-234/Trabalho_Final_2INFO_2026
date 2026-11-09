let paginaAtual = 1;

const projetosPorPagina = 10;

/*
=========================================================
PROJETOS PÚBLICOS
=========================================================
*/

function obterProjetosPublicos() {

    return projetos.filter(function (projeto) {

        return projeto.visibilidade.toLowerCase() === "público";

    });

}

/*
=========================================================
BUSCA
=========================================================
*/

function buscar() {

    const palavra = $("#palavra-chave")
        .val()
        .trim()
        .toLowerCase();


    // Primeiro pegamos somente os projetos públicos

    const projetosPublicos = obterProjetosPublicos();


    // Se o campo estiver vazio,
    // mostra todos os projetos públicos

    if (palavra === "") {

        paginaAtual = 1;

        carregarProjetos(projetosPublicos);

        criarPaginacao(projetosPublicos);

        return;
    }


    /*
    Divide a frase em palavras.

    Exemplo:

    "sistema java web"

    vira:

    ["sistema", "java", "web"]
    */

    const palavras = palavra.split(/\s+/);


    const resultados = projetosPublicos.filter(function (projeto) {

        const texto = (
            projeto.nome + " " +
            projeto.descricao
        ).toLowerCase();


        /*
        every() verifica se TODAS as palavras
        estão presentes no projeto.

        Exemplo:

        "sistema java"

        O projeto precisa conter:
        sistema
        E
        java

        As palavras não precisam estar juntas.
        */

        return palavras.every(function (palavra) {

            return texto.includes(palavra);

        });

    });


    paginaAtual = 1;

    carregarProjetos(resultados);

    criarPaginacao(resultados);

}


/*
=========================================================
CRIAÇÃO DOS CARDS
=========================================================
*/

function carregarProjetos(lista) {

    const listaProjetos = $("#projetos");

    listaProjetos.empty();


    /*
    Calculamos de onde até onde
    devemos pegar os projetos.

    Página 1:

    inicio = 0
    fim = 10

    Página 2:

    inicio = 10
    fim = 20
    */

    const inicio = (paginaAtual - 1) * projetosPorPagina;

    const fim = inicio + projetosPorPagina;


    /*
    slice() pega somente os projetos
    pertencentes à página atual.
    */

    const projetosDaPagina = lista.slice(inicio, fim);


    /*
    Agora criamos os cards somente
    dos projetos dessa página.
    */

    projetosDaPagina.forEach(function (projeto) {

        const article = document.createElement("article");

        article.className = "projeto";


        /*
        Criando as estrelas
        */

        let estrelas = "";

        for (let i = 1; i <= 5; i++) {

            estrelas += `
                <li class="estrela-moderna 
                    ${i <= projeto.avaliacao ? "" : "desativada"}">
                </li>
            `;

        }


        article.innerHTML = `

            <header>

                <h3>

                    <a
                        href="projeto.html?id=${projeto.id}"
                        class="titulo"
                    >
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


                    <li class="views">
                        Visualizações: ${projeto.views}
                    </li>


                    <li class="avaliacao">

                        <ul style="gap:1px;">

                            <li>
                                Avaliação:
                            </li>

                            ${estrelas}

                        </ul>

                    </li>


                    <li class="comentarios">
                        Comentários: ${projeto.comentarios}
                    </li>

                </ul>

            </footer>

        `;


        listaProjetos.append(article);

    });

}


/*
=========================================================
PAGINAÇÃO
=========================================================
*/

function criarPaginacao(lista) {

    const paginacao = $("#paginacao");

    paginacao.empty();

    const totalPaginas = Math.ceil(
        lista.length / projetosPorPagina
    );

    if (totalPaginas <= 1) {

        return;

    }


    /*
    =========================
    BOTÃO ANTERIOR
    =========================
    */

    const anterior = $(`
        <li class="page-item">
            <a
                class="page-link"
                href="#"
                aria-label="Previous"
            >
                &laquo;
            </a>
        </li>
    `);


    /*
    Quando clicar em anterior
    */

    anterior.on("click", function (event) {

        event.preventDefault();


        if (paginaAtual > 1) {

            paginaAtual--;

            carregarProjetos(lista);

            criarPaginacao(lista);

        }

    });


    /*
    Se já estamos na primeira página,
    desabilitamos o botão.
    */

    if (paginaAtual === 1) {

        anterior.addClass("disabled");

    }


    paginacao.append(anterior);


    /*
    =========================
    NÚMEROS DAS PÁGINAS
    =========================
    */

    for (let i = 1; i <= totalPaginas; i++) {

        const item = $(`
            <li class="page-item">

                <a
                    class="page-link"
                    href="#"
                >
                    ${i}
                </a>

            </li>
        `);


        /*
        Destaca a página atual
        */

        if (i === paginaAtual) {

            item.addClass("active");

        }


        /*
        Quando clicar no número
        */

        item.on("click", function (event) {

            event.preventDefault();

            paginaAtual = i;

            carregarProjetos(lista);

            criarPaginacao(lista);

        });


        paginacao.append(item);

    }


    /*
    =========================
    BOTÃO PRÓXIMO
    =========================
    */

    const proximo = $(`
        <li class="page-item">

            <a
                class="page-link"
                href="#"
                aria-label="Next"
            >
                &raquo;
            </a>

        </li>
    `);


    proximo.on("click", function (event) {

        event.preventDefault();


        if (paginaAtual < totalPaginas) {

            paginaAtual++;

            carregarProjetos(lista);

            criarPaginacao(lista);

        }

    });


    /*
    Se estamos na última página,
    desabilitamos o botão.
    */

    if (paginaAtual === totalPaginas) {

        proximo.addClass("disabled");

    }


    paginacao.append(proximo);

}


/*
=========================================================
BUSCA ENQUANTO DIGITA
=========================================================
*/

$("#palavra-chave").on("input", function () {

    buscar();

});


/*
=========================================================
FORMULÁRIO DE BUSCA
=========================================================
*/

$("#buscador form").on("submit", function (event) {

    event.preventDefault();

    buscar();

});


/*
=========================================================
ORDENAÇÃO
=========================================================
*/

$("#ordenar").on("change", function () {

    paginaAtual = 1;


    let resultados = obterProjetosPublicos();


    /*
    Ordena a lista.
    */

    ordenar(resultados);


    carregarProjetos(resultados);

    criarPaginacao(resultados);

});


/*
=========================================================
INVERTER
=========================================================
*/

$("#inverter").on("change", function () {

    paginaAtual = 1;


    let resultados = obterProjetosPublicos();


    ordenar(resultados);


    carregarProjetos(resultados);

    criarPaginacao(resultados);

});


/*
=========================================================
CARREGAMENTO INICIAL
=========================================================
*/

$(document).ready(function () {

    const projetosPublicos = obterProjetosPublicos();


    carregarProjetos(projetosPublicos);

    criarPaginacao(projetosPublicos);

});
