let paginaAtual = 1;

const projetosPorPagina = 10;


/*
=========================================================
PROJETOS
=========================================================
*/

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
    },
    {
        id: 4,
        nome: "Sistema de Biblioteca",
        visibilidade: "Público",
        descricao: "Sistema web para gerenciamento de livros, usuários e empréstimos.",
        comentarios: 42,
        curtidas: 175,
        avaliacao: 5,
        views: 920
    },
    {
        id: 5,
        nome: "Controle Financeiro",
        visibilidade: "Público",
        descricao: "Aplicação para controle de gastos pessoais desenvolvida com Java.",
        comentarios: 27,
        curtidas: 143,
        avaliacao: 4,
        views: 610
    },
    {
        id: 6,
        nome: "Sistema Escolar",
        visibilidade: "Privado",
        descricao: "Sistema web para gerenciamento de alunos, professores e notas.",
        comentarios: 18,
        curtidas: 91,
        avaliacao: 4,
        views: 350
    },
    {
        id: 7,
        nome: "Agenda de Compromissos",
        visibilidade: "Público",
        descricao: "Aplicação web para organização de compromissos e tarefas.",
        comentarios: 36,
        curtidas: 156,
        avaliacao: 3,
        views: 480
    },
    {
        id: 8,
        nome: "Gerenciador de Tarefas",
        visibilidade: "Público",
        descricao: "Sistema desenvolvido em Java para gerenciamento de tarefas e usuários.",
        comentarios: 61,
        curtidas: 280,
        avaliacao: 5,
        views: 1300
    },
    {
        id: 9,
        nome: "Biblioteca Digital",
        visibilidade: "Público",
        descricao: "Plataforma web para leitura e organização de livros digitais.",
        comentarios: 23,
        curtidas: 110,
        avaliacao: 4,
        views: 580
    },
    {
        id: 10,
        nome: "API de Usuários",
        visibilidade: "Privado",
        descricao: "API REST desenvolvida em Java para cadastro e autenticação de usuários.",
        comentarios: 12,
        curtidas: 65,
        avaliacao: 5,
        views: 240
    },
    {
        id: 11,
        nome: "Sistema de Vendas",
        visibilidade: "Público",
        descricao: "Sistema para controle de produtos, clientes, vendas e estoque.",
        comentarios: 48,
        curtidas: 215,
        avaliacao: 4,
        views: 850
    },
    {
        id: 12,
        nome: "Controle de Estoque",
        visibilidade: "Público",
        descricao: "Aplicação para gerenciamento de produtos e controle de estoque.",
        comentarios: 34,
        curtidas: 189,
        avaliacao: 3,
        views: 730
    },
    {
        id: 13,
        nome: "Portal de Notícias",
        visibilidade: "Público",
        descricao: "Site desenvolvido para publicação e organização de notícias.",
        comentarios: 75,
        curtidas: 320,
        avaliacao: 5,
        views: 2100
    },
    {
        id: 14,
        nome: "Chat Online",
        visibilidade: "Público",
        descricao: "Aplicação web para comunicação entre usuários em tempo real.",
        comentarios: 52,
        curtidas: 250,
        avaliacao: 4,
        views: 1450
    },
    {
        id: 15,
        nome: "Sistema de Biblioteca Escolar",
        visibilidade: "Público",
        descricao: "Aplicação para gerenciamento de livros, alunos e empréstimos escolares.",
        comentarios: 29,
        curtidas: 134,
        avaliacao: 4,
        views: 670
    },
    {
        id: 16,
        nome: "Gerenciamento de Projetos",
        visibilidade: "Público",
        descricao: "Ferramenta web para organização de equipes, tarefas e projetos.",
        comentarios: 84,
        curtidas: 410,
        avaliacao: 5,
        views: 2800
    },
    {
        id: 17,
        nome: "Sistema de Restaurante",
        visibilidade: "Privado",
        descricao: "Sistema para gerenciamento de pedidos, mesas, clientes e funcionários.",
        comentarios: 21,
        curtidas: 87,
        avaliacao: 4,
        views: 390
    },
    {
        id: 18,
        nome: "Aplicação de Notas",
        visibilidade: "Público",
        descricao: "Sistema simples para criação e organização de anotações pessoais.",
        comentarios: 17,
        curtidas: 76,
        avaliacao: 3,
        views: 310
    },
    {
        id: 19,
        nome: "Rede Social Acadêmica",
        visibilidade: "Público",
        descricao: "Plataforma para estudantes compartilharem projetos, trabalhos e conhecimentos.",
        comentarios: 67,
        curtidas: 350,
        avaliacao: 5,
        views: 1900
    },
    {
        id: 20,
        nome: "Sistema de Presença",
        visibilidade: "Público",
        descricao: "Aplicação para controle de presença e frequência de alunos.",
        comentarios: 31,
        curtidas: 128,
        avaliacao: 4,
        views: 540
    }
];


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


                <div class="status">

                    Status:
                    <span>
                        Em Andamento
                    </span>

                </div>

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