const formularioPerfil = document.getElementById("perfil-form");
const endpointPerfil = "../../backend/controladores/perfil.php";
const mensagemPerfil = document.getElementById("mensagem-perfil");

function mostrarMensagemPerfil(texto, erro = false) {
    mensagemPerfil.textContent = texto;
    mensagemPerfil.classList.toggle("erro", erro);
}

async function carregarPerfil() {
    try {
        const resposta = await fetch(endpointPerfil);
        const dados = await resposta.json();
        if (!resposta.ok) {
            throw new Error(dados.mensagem || "Não foi possível carregar o perfil.");
        }

        formularioPerfil.elements.namedItem("nome").value = dados.usuario.nome;
        formularioPerfil.elements.namedItem("email").value = dados.usuario.email;
        formularioPerfil.elements.namedItem("link_github").value = dados.usuario.link_github || "";
        formularioPerfil.elements.namedItem("link_linkedin").value = dados.usuario.link_linkedin || "";
    } catch (erro) {
        mostrarMensagemPerfil(erro.message, true);
    }
}

async function enviarPerfil(acao) {
    const dados = new FormData(formularioPerfil);
    dados.set("acao", acao);
    const resposta = await fetch(endpointPerfil, { method: "POST", body: dados });
    const resultado = await resposta.json();
    if (!resposta.ok) {
        throw new Error(resultado.mensagem || "Não foi possível concluir a operação.");
    }
    return resultado;
}

formularioPerfil.addEventListener("submit", async (evento) => {
    evento.preventDefault();
    const novaSenha = formularioPerfil.elements.namedItem("nova_senha").value;
    const confirmarSenha = formularioPerfil.elements.namedItem("confirmar_senha").value;
    if (novaSenha !== confirmarSenha) {
        mostrarMensagemPerfil("A confirmação da nova senha não confere.", true);
        return;
    }

    const botaoSalvar = document.getElementById("salvar_perfil");
    botaoSalvar.disabled = true;
    try {
        const resultado = await enviarPerfil("editar");
        mostrarMensagemPerfil(resultado.mensagem);
        formularioPerfil.elements.namedItem("senha_atual").value = "";
        formularioPerfil.elements.namedItem("nova_senha").value = "";
        formularioPerfil.elements.namedItem("confirmar_senha").value = "";
    } catch (erro) {
        mostrarMensagemPerfil(erro.message, true);
    } finally {
        botaoSalvar.disabled = false;
    }
});

document.getElementById("excluir_conta").addEventListener("click", async () => {
    if (!window.confirm("A exclusão da conta é permanente. Deseja continuar?")) {
        return;
    }

    const botaoExcluir = document.getElementById("excluir_conta");
    botaoExcluir.disabled = true;
    try {
        const resultado = await enviarPerfil("excluir");
        window.alert(resultado.mensagem);
        window.location.href = "../entrada/login.html";
    } catch (erro) {
        mostrarMensagemPerfil(erro.message, true);
    } finally {
        botaoExcluir.disabled = false;
    }
});

document.addEventListener("DOMContentLoaded", carregarPerfil);