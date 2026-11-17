const endpointProjetos = "../../backend/controladores/cadastrarProjetos.php";
const tokenCsrf = document.querySelector('meta[name="csrf-token"]').content;

async function carregarProjetos() {
    const lista = document.getElementById("lista-projetos");
    lista.replaceChildren();

    try {
        const resposta = await fetch(`${endpointProjetos}?acao=listar`);
        const dados = await resposta.json();
        if (!resposta.ok) {
            throw new Error(dados.mensagem || "Não foi possível carregar seus projetos.");
        }

        if (dados.projetos.length === 0) {
            const vazio = document.createElement("p");
            vazio.textContent = "Você ainda não cadastrou projetos.";
            lista.append(vazio);
            return;
        }

        dados.projetos.forEach((projeto) => {
            const artigo = document.createElement("article");
            artigo.className = "projeto";

            const cabecalho = document.createElement("header");
            const titulo = document.createElement("h3");
            titulo.textContent = projeto.nome;
            const visibilidade = document.createElement("span");
            visibilidade.className = `visibilidade ${projeto.visibilidade ? "publico" : "privado"}`;
            visibilidade.textContent = projeto.visibilidade ? "Público" : "Privado";
            cabecalho.append(titulo, visibilidade);

            const conteudo = document.createElement("section");
            const descricao = document.createElement("p");
            descricao.textContent = projeto.descricao;
            conteudo.append(descricao);

            const rodape = document.createElement("footer");
            const navegacao = document.createElement("nav");
            const acoes = document.createElement("ul");
            const editar = document.createElement("a");
            editar.className = "btn";
            editar.href = `projeto.php?id=${encodeURIComponent(projeto.id)}`;
            editar.textContent = "Editar";
            const excluir = document.createElement("button");
            excluir.type = "button";
            excluir.className = "btn btn-excluir";
            excluir.dataset.id = projeto.id;
            excluir.textContent = "Excluir";
            const itemEditar = document.createElement("li");
            const itemExcluir = document.createElement("li");
            itemEditar.append(editar);
            itemExcluir.append(excluir);
            acoes.append(itemEditar, itemExcluir);
            navegacao.append(acoes);
            rodape.append(navegacao);
            artigo.append(cabecalho, conteudo, rodape);
            lista.append(artigo);
        });
    } catch (erro) {
        const mensagem = document.createElement("p");
        mensagem.textContent = erro.message;
        lista.append(mensagem);
    }
}

document.getElementById("lista-projetos").addEventListener("click", async (evento) => {
    const botao = evento.target.closest(".btn-excluir");
    if (!botao || !window.confirm("Deseja realmente excluir este projeto?")) {
        return;
    }

    const dados = new FormData();
    dados.set("acao", "excluir");
    dados.set("id", botao.dataset.id);
    dados.set("csrf_token", tokenCsrf);

    try {
        const resposta = await fetch(endpointProjetos, { method: "POST", body: dados });
        const resultado = await resposta.json();
        if (!resposta.ok) {
            throw new Error(resultado.mensagem || "Não foi possível excluir o projeto.");
        }
        await carregarProjetos();
    } catch (erro) {
        window.alert(erro.message);
    }
});

document.addEventListener("DOMContentLoaded", carregarProjetos);
