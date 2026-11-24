const formularioRecuperacao = document.getElementById("recuperar-form");
const mensagemRecuperacao = document.getElementById("mensagem-recuperacao");

formularioRecuperacao.addEventListener("submit", async (evento) => {
    evento.preventDefault();
    const botao = formularioRecuperacao.querySelector("button[type='submit']");
    botao.disabled = true;

    try {
        const resposta = await fetch("../../backend/controladores/solicitarRecuperacaoSenha.php", {
            method: "POST",
            body: new FormData(formularioRecuperacao)
        });
        const dados = await resposta.json();
        if (!resposta.ok) {
            throw new Error(dados.mensagem || "Não foi possível solicitar a recuperação.");
        }
        mensagemRecuperacao.textContent = dados.mensagem;
        formularioRecuperacao.reset();
    } catch (erro) {
        mensagemRecuperacao.textContent = erro.message;
    } finally {
        botao.disabled = false;
    }
});