projetos = [
  {
    id: 1,
    nome: "DevShare",
    visibilidade: "Público",
    descricao: "Rede social para compartilhamento de projetos."
  },
  {
    id: 2,
    nome: "MenteBoa",
    visibilidade: "Privado",
    descricao: "Aplicação desenvolvida em Java."
  },
  {
    id: 3,
    nome: "Gerenciamento de acolitos",
    visibilidade: "publico",
    descricao: "O projeto visa uma organização dos membros do acolitato"
  },
  {
    id: 3,
    nome: "Gerenciamento de acolitos",
    visibilidade: "publico",
    descricao: "O projeto visa uma organização dos membros do acolitato"
  },
  {
    id: 3,
    nome: "Gerenciamento de acolitos",
    visibilidade: "publico",
    descricao: "O projeto visa uma organização dos membros do acolitato"
  }
];

function carregarProjetos() {
  const listaProjetos = $("#lista-projetos");
  projetos.forEach((projeto) => {
    const article = document.createElement("article");
    article.className = "projeto";

    article.innerHTML = `
        <header>
            <h3>${projeto.nome}</h3>
            <p>Visibilidade: ${projeto.visibilidade}</p>
          </header>

          <section>
            <p>
              ${projeto.descricao}
            </p>
          </section>

          <!-- Ações do projeto -->
          <footer>
            <nav>
              <ul>
                <li><a href="../global/projeto.html?id=${projeto.id}">Ver</a></li>
                <li><a href="projeto.html?id=${projeto.id}">Editar</a></li>
                <li><a href="#">Excluir</a></li>
              </ul>
            </nav>
          </footer>
    `;

    listaProjetos.append(article);
  });
}