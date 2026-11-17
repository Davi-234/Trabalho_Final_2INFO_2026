const formularioProjeto = document.getElementById("form");
const mensagemProjeto = document.getElementById("mensagem-projeto");
const endpointProjeto = "../../backend/controladores/cadastrarProjetos.php";
const parametrosProjeto = new URLSearchParams(window.location.search);
const idProjeto = parametrosProjeto.get("id");

function mostrarMensagemProjeto(texto, erro = false) {
    mensagemProjeto.textContent = texto;
    mensagemProjeto.classList.toggle("erro", erro);
}

async function carregarProjetoParaEdicao() {
    if (!idProjeto) {
        return;
    }

    try {
        const resposta = await fetch(`${endpointProjeto}?id=${encodeURIComponent(idProjeto)}`);
        const dados = await resposta.json();
        if (!resposta.ok) {
            throw new Error(dados.mensagem || "Não foi possível carregar o projeto.");
        }

        const projeto = dados.projeto;
        formularioProjeto.elements.namedItem("titulo").value = projeto.titulo;
        formularioProjeto.elements.namedItem("descricao").value = projeto.descricao;
        formularioProjeto.elements.namedItem("link").value = projeto.link;
        formularioProjeto.elements.namedItem("nivel").value = projeto.nivel;
        formularioProjeto.elements.namedItem("status").value = projeto.status;
        formularioProjeto.elements.namedItem("tecnologias").value = projeto.tecnologias.join(", ");
        formularioProjeto.elements.namedItem("tags").value = projeto.tags.join(", ");
        formularioProjeto.querySelector(`input[name="visibilidade"][value="${projeto.visibilidade}"]`).checked = true;
        document.getElementById("projeto_id").value = projeto.id;
        document.querySelector("#intro h2").textContent = "Editar Projeto";
        document.getElementById("btn_submit").textContent = "Salvar Alterações";
        alterar_PreVisualizacao("titulo");
        alterar_PreVisualizacao("descricao");
        alterar_PreVisualizacao("nivel");
    } catch (erro) {
        mostrarMensagemProjeto(erro.message, true);
    }
}

formularioProjeto.addEventListener("submit", async (evento) => {
    evento.preventDefault();
    mostrarMensagemProjeto("");

    const dados = new FormData(formularioProjeto);
    dados.set("acao", idProjeto ? "editar" : "criar");
    if (idProjeto) {
        dados.set("id", idProjeto);
    }

    const botaoSalvar = document.getElementById("btn_submit");
    botaoSalvar.disabled = true;

    try {
        const resposta = await fetch(endpointProjeto, { method: "POST", body: dados });
        const resultado = await resposta.json();
        if (!resposta.ok) {
            throw new Error(resultado.mensagem || "Não foi possível salvar o projeto.");
        }
        window.location.href = "home.php";
    } catch (erro) {
        mostrarMensagemProjeto(erro.message, true);
    } finally {
        botaoSalvar.disabled = false;
    }
});

document.addEventListener("DOMContentLoaded", carregarProjetoParaEdicao);