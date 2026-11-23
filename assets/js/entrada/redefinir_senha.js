const formularioRedefinicao = document.getElementById("redefinir-form");
const mensagemRedefinicao = document.getElementById("mensagem-redefinicao");
const campoSenhaRedefinicao = document.getElementById("senha");
const campoConfirmacaoRedefinicao = document.getElementById("confirmar_senha");
const erroSenhas = document.getElementById("erro-senhas");
const tokenRecuperacao = new URLSearchParams(window.location.search).get("token") || "";
formularioRedefinicao.elements.namedItem("token").value = tokenRecuperacao;

function atualizarRequisitosSenha() {
    const requisitos = validar_senha(campoSenhaRedefinicao.value).requisitos_senha;
    document.querySelectorAll("#requisitos-senha [data-regra]").forEach((item) => {
        const valido = requisitos[item.dataset.regra];
        item.classList.toggle("valido", valido);
        item.setAttribute("aria-label", `${item.textContent}: ${valido ? "atendido" : "pendente"}`);
    });
    return Object.values(requisitos).every(Boolean);
}

function validarConfirmacaoSenha() {
    const confere = campoSenhaRedefinicao.value === campoConfirmacaoRedefinicao.value;
    campoConfirmacaoRedefinicao.setCustomValidity(confere ? "" : "As senhas não coincidem.");
    erroSenhas.textContent = confere || campoConfirmacaoRedefinicao.value === ""
        ? ""
        : "As senhas não coincidem.";
    return confere;
}

campoSenhaRedefinicao.addEventListener("input", () => {
    atualizarRequisitosSenha();
    validarConfirmacaoSenha();
});
campoConfirmacaoRedefinicao.addEventListener("input", validarConfirmacaoSenha);

if (!/^[a-f0-9]{64}$/.test(tokenRecuperacao)) {
    mensagemRedefinicao.textContent = "O link de recuperação é inválido ou expirou.";
    formularioRedefinicao.querySelectorAll("input, button").forEach((campo) => {
        campo.disabled = true;
    });
}

formularioRedefinicao.addEventListener("submit", async (evento) => {
    evento.preventDefault();
    if (!atualizarRequisitosSenha()) {
        erroSenhas.textContent = "A senha deve atender a todos os requisitos acima.";
        campoSenhaRedefinicao.focus();
        return;
    }
    if (!validarConfirmacaoSenha()) {
        campoConfirmacaoRedefinicao.focus();
        return;
    }

    const botao = formularioRedefinicao.querySelector("button[type='submit']");
    botao.disabled = true;

    try {
        const resposta = await fetch("../../backend/controladores/redefinirSenha.php", {
            method: "POST",
            body: new FormData(formularioRedefinicao)
        });
        const dados = await resposta.json();
        if (!resposta.ok) {
            throw new Error(dados.mensagem || "Não foi possível redefinir a senha.");
        }
        mensagemRedefinicao.textContent = dados.mensagem;
        formularioRedefinicao.reset();
    } catch (erro) {
        mensagemRedefinicao.textContent = erro.message;
    } finally {
        botao.disabled = false;
    }
});