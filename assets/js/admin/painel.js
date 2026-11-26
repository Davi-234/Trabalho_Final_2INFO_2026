const adminApi = '../../backend/controladores/admin.php';

function mostrarMensagem(elemento, mensagem) {
    if (elemento) {
        elemento.textContent = mensagem;
    }
}

function escaparHtml(valor) {
    const elemento = document.createElement('span');
    elemento.textContent = valor ?? '';
    return elemento.innerHTML;
}

async function respostaAdmin(url, opcoes = {}) {
    const resposta = await fetch(url, { credentials: 'same-origin', ...opcoes });
    const dados = await resposta.json();
    if (!resposta.ok) {
        if (resposta.status === 401 || resposta.status === 403) {
            window.location.href = 'adm.html';
        }
        throw new Error(dados.erro || dados.mensagem || 'Não foi possível concluir a operação.');
    }
    return dados;
}

const formularioLogin = document.querySelector('#login-box form');
if (formularioLogin) {
    formularioLogin.addEventListener('submit', async evento => {
        evento.preventDefault();
        if (!formularioLogin.reportValidity()) return;

        const mensagem = document.getElementById('mensagem-login-admin');
        mostrarMensagem(mensagem, '');
        try {
            const resposta = await fetch('../../backend/controladores/loginAdmin.php', {
                method: 'POST',
                body: new FormData(formularioLogin),
                credentials: 'same-origin'
            });
            const dados = await resposta.json();
            if (!resposta.ok || !dados.sucesso) {
                throw new Error(dados.mensagem || 'Não foi possível entrar.');
            }
            window.location.href = 'analise_denuncias.html';
        } catch (erro) {
            mostrarMensagem(mensagem, erro.message);
        }
    });
}

const listaDenuncias = document.getElementById('lista-denuncias');
if (listaDenuncias) {
    let tipoAtual = '';
    let tokenCsrf = '';
    const mensagem = document.getElementById('mensagem-admin');
    const rotulos = {
        projeto: 'Projeto',
        comentario: 'Comentário',
        usuario: 'Usuário'
    };

    async function carregarDenuncias() {
        mostrarMensagem(mensagem, 'Carregando denúncias...');
        try {
            const query = new URLSearchParams({ acao: 'listar', tipo: tipoAtual });
            const dados = await respostaAdmin(`${adminApi}?${query}`);
            tokenCsrf = dados.csrf_token;
            document.getElementById('csrf-logout').value = tokenCsrf;
            document.getElementById('nome-administrador').textContent = dados.administrador;
            document.getElementById('titulo_denuncias').textContent = tipoAtual
                ? `Denúncias de ${rotulos[tipoAtual].toLowerCase()}`
                : 'Denúncias pendentes';

            if (dados.denuncias.length === 0) {
                listaDenuncias.innerHTML = '';
                mostrarMensagem(mensagem, 'Não há denúncias pendentes nesta categoria.');
                return;
            }

            mostrarMensagem(mensagem, '');
            listaDenuncias.innerHTML = dados.denuncias.map(denuncia => `
                <article class="denuncia-admin-card">
                    <h3>${escaparHtml(rotulos[denuncia.tipo])}: ${escaparHtml(denuncia.alvo)}</h3>
                    <p><strong>Denunciante:</strong> ${escaparHtml(denuncia.denunciante)}</p>
                    <p><strong>Autor do conteúdo:</strong> ${escaparHtml(denuncia.autor_alvo)}</p>
                    <p><strong>Motivo:</strong> ${escaparHtml(denuncia.motivo)}</p>
                    <p><strong>Data:</strong> ${escaparHtml(new Date(denuncia.data.replace(' ', 'T')).toLocaleString('pt-BR'))}</p>
                    <div class="acoes-denuncia">
                        <a href="denuncia_descricao.html?id=${encodeURIComponent(denuncia.id)}">Ver detalhes</a>
                        <button type="button" data-acao="concordar" data-id="${denuncia.id}">Concordar e remover</button>
                        <button type="button" class="acao-secundaria" data-acao="ignorar" data-id="${denuncia.id}">Ignorar denúncia</button>
                    </div>
                </article>
            `).join('');
        } catch (erro) {
            mostrarMensagem(mensagem, erro.message);
        }
    }

    async function decidir(id, acao) {
        const confirmacao = acao === 'concordar'
            ? 'Isso removerá permanentemente o conteúdo denunciado. Se for um usuário, a conta, seus projetos e comentários também serão removidos. Continuar?'
            : 'A denúncia será encerrada sem remover o conteúdo. Continuar?';
        if (!window.confirm(confirmacao)) return;

        const dados = new FormData();
        dados.set('csrf_token', tokenCsrf);
        dados.set('id', id);
        dados.set('acao', acao);
        try {
            await respostaAdmin(adminApi, { method: 'POST', body: dados });
            await carregarDenuncias();
        } catch (erro) {
            mostrarMensagem(mensagem, erro.message);
        }
    }

    document.querySelectorAll('.filtro-denuncia').forEach(botao => {
        botao.addEventListener('click', () => {
            document.querySelector('.filtro-denuncia.ativo')?.classList.remove('ativo');
            botao.classList.add('ativo');
            tipoAtual = botao.dataset.tipo;
            carregarDenuncias();
        });
    });

    listaDenuncias.addEventListener('click', evento => {
        const botao = evento.target.closest('button[data-acao]');
        if (botao) decidir(botao.dataset.id, botao.dataset.acao);
    });

    carregarDenuncias();
}

const detalheDenuncia = document.getElementById('denuncia');
if (detalheDenuncia) {
    const mensagem = document.getElementById('mensagem-admin');
    const id = new URLSearchParams(window.location.search).get('id');
    let tokenCsrf = '';
    const rotulos = {
        projeto: 'projeto',
        comentario: 'comentário',
        usuario: 'usuário'
    };

    async function carregarDetalhe() {
        if (!id || !/^\d+$/.test(id)) {
            mostrarMensagem(mensagem, 'Identificador de denúncia inválido.');
            return;
        }
        try {
            const query = new URLSearchParams({ acao: 'detalhe', id });
            const dados = await respostaAdmin(`${adminApi}?${query}`);
            const denuncia = dados.denuncia;
            tokenCsrf = dados.csrf_token;
            document.getElementById('csrf-logout').value = tokenCsrf;
            detalheDenuncia.dataset.id = denuncia.id;
            document.getElementById('tipo-denuncia').textContent = rotulos[denuncia.tipo] || denuncia.tipo;
            document.getElementById('nome-denunciante').textContent = denuncia.denunciante;
            document.getElementById('alvo-denuncia').textContent = denuncia.alvo;
            document.getElementById('autor-alvo').textContent = denuncia.autor_alvo;
            document.getElementById('motivo-denuncia').textContent = denuncia.motivo;
            document.getElementById('detalhes-alvo').textContent = denuncia.detalhes_alvo || 'Conteúdo não disponível.';
            document.getElementById('data-denuncia').textContent =
                new Date(denuncia.data.replace(' ', 'T')).toLocaleString('pt-BR');
            if (denuncia.status === 'removida') {
                detalheDenuncia.querySelectorAll('button[data-acao]').forEach(botao => {
                    botao.disabled = true;
                });
                mostrarMensagem(mensagem, 'Esta denúncia já foi encerrada.');
            }
        } catch (erro) {
            mostrarMensagem(mensagem, erro.message);
        }
    }

    detalheDenuncia.addEventListener('click', async evento => {
        const botao = evento.target.closest('button[data-acao]');
        if (!botao || !detalheDenuncia.dataset.id) return;

        const acao = botao.dataset.acao;
        const aviso = acao === 'concordar'
            ? 'O conteúdo ou a conta denunciada será removido permanentemente. Continuar?'
            : 'A denúncia será encerrada sem remover o conteúdo. Continuar?';
        if (!window.confirm(aviso)) return;

        const dados = new FormData();
        dados.set('csrf_token', tokenCsrf);
        dados.set('id', detalheDenuncia.dataset.id);
        dados.set('acao', acao);
        try {
            await respostaAdmin(adminApi, { method: 'POST', body: dados });
            window.location.href = 'analise_denuncias.html';
        } catch (erro) {
            mostrarMensagem(mensagem, erro.message);
        }
    });

    carregarDetalhe();
}
