const projetosPorPagina = 10;
let paginaAtual = 1;
let requisicaoAtual;
let temporizadorBusca;

const listaProjetos = document.getElementById('projetos');
const paginacao = document.getElementById('paginacao');
const campoBusca = document.getElementById('palavra-chave');
const campoOrdenacao = document.getElementById('ordenar');
const campoInverter = document.getElementById('inverter');
const campoNivel = document.getElementById('nivel');
const campoTecnologias = document.getElementById('tecnologias');

async function carregarPagina(pagina = 1) {
    paginaAtual = pagina;
    requisicaoAtual?.abort();
    requisicaoAtual = new AbortController();

    const parametros = new URLSearchParams({
        q: campoBusca.value.trim(),
        nivel: campoNivel.value,
        tecnologias: campoTecnologias.value.trim(),
        pagina: String(paginaAtual),
        ordenar: campoOrdenacao.value,
        inverter: campoInverter.checked ? '1' : '0'
    });

    listaProjetos.textContent = 'Carregando projetos...';
    paginacao.replaceChildren();

    try {
        const resposta = await fetch(`../../backend/controladores/projetos.php?${parametros}`, {
            signal: requisicaoAtual.signal
        });

        if (!resposta.ok) {
            throw new Error('Falha ao carregar projetos.');
        }

        const dados = await resposta.json();
        renderizarProjetos(dados.projetos);
        renderizarPaginacao(dados.pagina, dados.totalPaginas);
    } catch (erro) {
        if (erro.name === 'AbortError') {
            return;
        }

        listaProjetos.textContent = 'Não foi possível carregar os projetos.';
    }
}

function renderizarProjetos(projetos) {
    listaProjetos.replaceChildren();

    if (projetos.length === 0) {
        listaProjetos.textContent = 'Nenhum projeto encontrado.';
        return;
    }

    projetos.forEach((projeto) => {
        const artigo = document.createElement('article');
        artigo.className = 'projeto';

        const cabecalho = document.createElement('header');
        const titulo = document.createElement('h3');
        const link = document.createElement('a');
        link.className = 'titulo';
        link.href = `projeto.html?id=${encodeURIComponent(projeto.id)}`;
        link.textContent = projeto.nome;
        titulo.append(link);
        cabecalho.append(titulo);

        const secao = document.createElement('section');
        const descricao = document.createElement('p');
        descricao.className = 'descricao';
        descricao.textContent = projeto.descricao;
        secao.append(descricao);

        const rodape = document.createElement('footer');
        const metricas = document.createElement('ul');
        metricas.append(
            criarMetrica('Visualizações', projeto.views),
            criarMetrica('Comentários', projeto.comentarios)
        );

        const avaliacao = document.createElement('li');
        avaliacao.className = 'avaliacao';
        const estrelasLista = document.createElement('ul');
        estrelasLista.style.gap = '1px';

        const legenda = document.createElement('li');
        legenda.textContent = `Avaliação: ${projeto.avaliacao}/5`;
        estrelasLista.append(legenda);

        const estrelasAtivas = Math.round(Number(projeto.avaliacao));
        for (let estrela = 1; estrela <= 5; estrela++) {
            const item = document.createElement('li');
            item.className = `estrela-moderna${estrela <= estrelasAtivas ? '' : ' desativada'}`;
            estrelasLista.append(item);
        }

        avaliacao.append(estrelasLista);
        metricas.append(avaliacao);
        rodape.append(metricas);

        artigo.append(cabecalho, secao, rodape);
        listaProjetos.append(artigo);
    });
}

function criarMetrica(rotulo, valor) {
    const item = document.createElement('li');
    item.textContent = `${rotulo}: ${valor}`;
    return item;
}

function renderizarPaginacao(pagina, totalPaginas) {
    paginacao.replaceChildren();

    if (totalPaginas <= 1) {
        return;
    }

    adicionarBotaoPagina('Anterior', pagina - 1, pagina === 1);

    const primeiraPagina = Math.max(1, pagina - 2);
    const ultimaPagina = Math.min(totalPaginas, pagina + 2);
    for (let numero = primeiraPagina; numero <= ultimaPagina; numero++) {
        adicionarBotaoPagina(String(numero), numero, false, numero === pagina);
    }

    adicionarBotaoPagina('Próxima', pagina + 1, pagina === totalPaginas);
}

function adicionarBotaoPagina(rotulo, pagina, desabilitado, ativo = false) {
    const item = document.createElement('li');
    item.className = 'page-item';
    if (desabilitado) item.classList.add('disabled');
    if (ativo) item.classList.add('active');

    const link = document.createElement('a');
    link.className = 'page-link';
    link.href = '#';
    link.textContent = rotulo;
    if (ativo) link.setAttribute('aria-current', 'page');

    link.addEventListener('click', (evento) => {
        evento.preventDefault();
        if (!desabilitado && !ativo) carregarPagina(pagina);
    });

    item.append(link);
    paginacao.append(item);
}

campoBusca.addEventListener('input', () => {
    window.clearTimeout(temporizadorBusca);
    temporizadorBusca = window.setTimeout(() => carregarPagina(1), 300);
});

document.querySelector('#buscador form').addEventListener('submit', (evento) => {
    evento.preventDefault();
    window.clearTimeout(temporizadorBusca);
    carregarPagina(1);
});

document.querySelector('#filtros form').addEventListener('submit', (evento) => {
    evento.preventDefault();
    carregarPagina(1);
});

campoOrdenacao.addEventListener('change', () => carregarPagina(1));
campoInverter.addEventListener('change', () => carregarPagina(1));

carregarPagina();