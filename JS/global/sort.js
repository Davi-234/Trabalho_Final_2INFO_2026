/*
Objeto javascript dos projetos = {
id: ,
titulo:,
descricao:,
curtidas:,
views:,
avaliacao:,
comentarios:
}
*/

// Quardará dados especificos do banco de dados, já com os filtros aplicados
projetos = [];
projetos.push({
  id: 1,
  titulo: "Titulo",
  descricao: "lorem",
  curtidas: 4566,
  views: 434,
  avaliacao: 3,
  comentarios: 32,
});
projetos.push({
  id: 1,
  titulo: "Titulo",
  descricao: "lorem",
  curtidas: 4566,
  views: 434,
  avaliacao: 5,
  comentarios: 32,
});
projetos.push({
  id: 1,
  titulo: "Titulo",
  descricao: "lorem",
  curtidas: 4566,
  views: 434,
  avaliacao: 5,
  comentarios: 37,
});

function preencherProjetos() {
  projetos = []; //Incialmente limpa o vetor

  //Faz as requicições ao banco de dados

  // Os dados serão do banco de dados
}

function ordenar() {
  let valor = $("#ordenar").val();

  switch (valor) {
    case "comentarios":
      projetos.sort((a, b) => b.comentarios - a.comentarios);
      break;
    case "curtidas":
      projetos.sort((a, b) => b.curtidas - a.curtidas);
      break;
    case "avaliacao":
      projetos.sort((a, b) => b.avaliacao - a.avaliacao);
      break;
    case "views":
      projetos.sort((a, b) => b.views - a.views);
      break;
    case "recentes":
      break;
  }

  if ($("#inverter").prop("checked")) {
    projetos.reverse();
    
  }
}

function criarCardsProjeto() {
  ordenar();
  const listaProjetos = $("#projetos");
  listaProjetos.empty();

  projetos.forEach((projeto) => {
    const article = document.createElement("article");
    article.className = "projeto";

    // Gera as estrelas
    let estrelas = "";

    for (let i = 1; i <= 5; i++) {
      estrelas += `<li class="estrela-moderna ${i <= projeto.avaliacao ? "" : "desativada"}"></li>`;
    }

    article.innerHTML = `
        <header>
            <h3>
                <a href="projeto.html?id=${projeto.id}" class="titulo">
                    ${projeto.titulo}
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
                        <li>Avaliação:</li>
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

$("#ordenar").on("change", criarCardsProjeto);
$("#inverter").on("change", criarCardsProjeto);
