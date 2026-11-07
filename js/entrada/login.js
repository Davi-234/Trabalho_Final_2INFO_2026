const form = document.getElementById("form-login");

form.addEventListener("submit", async event => {
    event.preventDefault();

    if (!form.reportValidity()) {
        return;
    }

    try {
        const response = await fetch('../../admin/backend_entidades_sistema/login.php', {
            method: 'POST',
            body: new FormData(form)
        });
        const data = await response.json();

        if (!response.ok || !data.sucesso) {
            alert('Erro no login: ' + data.mensagem);
            return;
        }

        alert(data.mensagem);
        window.location.href = "../usuario/home.php";
    } catch (error) {
        console.error('Erro na requisição:', error);
        alert('Ocorreu um problema ao tentar conectar com o servidor. Tente novamente mais tarde.');
    }
});