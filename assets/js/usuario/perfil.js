const formularioPerfil = document.getElementById("perfil-form");
const endpointPerfil = "../../backend/controladores/perfil.php";
const mensagemPerfil = document.getElementById("mensagem-perfil");
const inputImagemPerfil = document.getElementById("imagem-perfil");
const imagemAvatarPerfil = document.getElementById("avatar-imagem");
const inicialAvatarPerfil = document.getElementById("avatar-inicial");
const mensagemImagemPerfil = document.getElementById("mensagem-imagem");
let fotoPerfilAtual = null;

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
        const inicial = (dados.usuario.email || "?").trim().charAt(0).toUpperCase() || "?";
        inicialAvatarPerfil.textContent = inicial;
        fotoPerfilAtual = dados.usuario.foto_perfil;
        if (fotoPerfilAtual) {
            exibirFotoPerfil(`../../${fotoPerfilAtual}`);
        }
    } catch (erro) {
        mostrarMensagemPerfil(erro.message, true);
    }
}

function exibirFotoPerfil(src) {
    imagemAvatarPerfil.onload = () => {
        imagemAvatarPerfil.hidden = false;
        inicialAvatarPerfil.hidden = true;
    };
    imagemAvatarPerfil.onerror = () => {
        imagemAvatarPerfil.hidden = true;
        inicialAvatarPerfil.hidden = false;
    };
    imagemAvatarPerfil.src = src;
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

document.getElementById("editar-imagem").addEventListener("click", () => inputImagemPerfil.click());

inputImagemPerfil.addEventListener("change", async () => {
    const arquivo = inputImagemPerfil.files[0];
    if (!arquivo) {
        return;
    }
    if (!['image/jpeg', 'image/png', 'image/webp'].includes(arquivo.type) || arquivo.size > 5 * 1024 * 1024) {
        mensagemImagemPerfil.textContent = "Escolha uma imagem JPG, PNG ou WebP de até 5 MB.";
        inputImagemPerfil.value = "";
        return;
    }

    const fotoAnterior = fotoPerfilAtual;
    const urlTemporaria = URL.createObjectURL(arquivo);
    exibirFotoPerfil(urlTemporaria);
    mensagemImagemPerfil.textContent = "Enviando imagem...";

    const dados = new FormData();
    dados.set("acao", "imagem");
    dados.set("csrf_token", formularioPerfil.elements.namedItem("csrf_token").value);
    dados.set("imagem", arquivo);

    try {
        const resposta = await fetch(endpointPerfil, { method: "POST", body: dados });
        const resultado = await resposta.json();
        if (!resposta.ok) {
            throw new Error(resultado.mensagem || "Não foi possível atualizar a imagem.");
        }
        fotoPerfilAtual = resultado.foto_perfil;
        exibirFotoPerfil(`../../${fotoPerfilAtual}?v=${Date.now()}`);
        mensagemImagemPerfil.textContent = resultado.mensagem;
    } catch (erro) {
        mensagemImagemPerfil.textContent = erro.message;
        if (fotoAnterior) {
            exibirFotoPerfil(`../../${fotoAnterior}`);
        } else {
            imagemAvatarPerfil.hidden = true;
            inicialAvatarPerfil.hidden = false;
        }
    } finally {
        URL.revokeObjectURL(urlTemporaria);
        inputImagemPerfil.value = "";
    }
});

document.addEventListener("DOMContentLoaded", carregarPerfil);