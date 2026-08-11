projetos = [];

function criarCardsProjeto() {
    ordenar();
    const listaProjetos = $("#projetos");
    listaProjetos.empty();

    projetos.forEach((projeto) => {
        const article = document.createElement("article");
        article.className = "projeto";

        article.innerHTML = `
        <header>
            <h3>Nome do Projeto</h3>
            <p>Visibilidade: Público</p>
          </header>

          <section>
            <p>
              Descrição do projeto. Aqui ficará um resumo do que foi
              desenvolvido e quais tecnologias foram utilizadas.
            </p>
          </section>

          <!-- Ações do projeto -->
          <footer>
            <nav>
              <ul>
                <li><a href="../global/projeto.html">Ver</a></li>
                <li><a href="projeto.html">Editar</a></li>
                <li><a href="#">Excluir</a></li>
              </ul>
            </nav>
          </footer>
    `;

        listaProjetos.append(article);
    });
}
