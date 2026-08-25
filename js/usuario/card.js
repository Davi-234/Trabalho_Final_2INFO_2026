const projetos = [
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
        nome: "Gerenciamento de Acólitos",
        visibilidade: "Público",
        descricao: "O projeto visa uma organização dos membros do acolitato."
    },
    {
        id: 4,
        nome: "Gerenciamento de Acólitos",
        visibilidade: "Público",
        descricao: "O projeto visa uma organização dos membros do acolitato."
    },
    {
        id: 5,
        nome: "Gerenciamento de Acólitos",
        visibilidade: "Público",
        descricao: "O projeto visa uma organização dos membros do acolitato."
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

                <span class="visibilidade ${projeto.visibilidade.toLowerCase()}">
                    ${projeto.visibilidade}
                </span>
            </header>

            <section>
                <p>${projeto.descricao}</p>
            </section>

            <footer>
                <nav>
                    <ul>
                        <li>
                            <a href="../global/projeto.html?id=${projeto.id}">
                                Ver
                            </a>
                        </li>

                        <li>
                            <a href="projeto.html?id=${projeto.id}">
                                Editar
                            </a>
                        </li>

                        <li>
                            <button class="btn-excluir">
                                Excluir
                            </button>
                        </li>
                    </ul>
                </nav>
            </footer>
        `;

        listaProjetos.append(article);
    });
}