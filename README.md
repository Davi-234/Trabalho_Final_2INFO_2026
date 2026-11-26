# Trabalho Final — IFMG Campus Formiga

## Descrição

Este projeto foi desenvolvido como trabalho final do 2º ano do curso técnico em Informática do IFMG – Campus Formiga, pelos alunos Davi Leandro e João Marcos Vital.

A proposta consiste na construção de uma aplicação web que integra, de forma prática, conhecimentos adquiridos nas disciplinas de Desenvolvimento Web I, Banco de Dados I e Análise e Projeto de Sistemas. A iniciativa parte da identificação de uma necessidade recorrente entre estudantes e desenvolvedores: a dificuldade em encontrar, de forma rápida e confiável, repositórios e conteúdos relevantes sobre temas específicos.

Como solução, foi idealizada uma plataforma com características de rede social, na qual usuários podem se cadastrar e compartilhar links de repositórios, sites e outros materiais digitais. Além disso, o sistema permite a interação entre os usuários por meio de comentários e avaliações, promovendo não apenas a divulgação, mas também a curadoria colaborativa dos conteúdos publicados.

Dessa forma, o projeto busca centralizar informações úteis em um único ambiente, reduzindo o tempo de busca e incentivando a troca de conhecimento, ao mesmo tempo em que aplica, em um contexto prático, conceitos fundamentais da formação técnica dos desenvolvedores envolvidos.

## Organização

- `assets/`: arquivos estáticos, separados em CSS, JavaScript e imagens.
- `pages/`: telas da aplicação, organizadas por área: administração, entrada, páginas globais e usuário.
- `backend/`: código PHP separado em `controladores/`, `servicos/`, `persistencia/`, `configuracao/`, `modelos/`, `seguranca/` e `utilidades/`.
- `documentacao/`: requisitos, casos de uso e arquivos do banco de dados.
- `index.html` e `404.html`: páginas servidas a partir da raiz do projeto.

## Execução local com MySQL

Use Docker, Docker Compose e PHP com a extensão `mysqli`. O MySQL será executado em um contêiner na porta `3307`, sem substituir nem apagar o MariaDB instalado no computador.

1. Instale Docker e Compose no Ubuntu: `sudo apt update` e depois `sudo apt install docker.io docker-compose-v2`.
2. Inicie o serviço: `sudo systemctl enable --now docker`.
3. Copie `.env.example` para `.env` e preencha as senhas. Use um valor próprio para `MYSQL_ROOT_PASSWORD` e outro para `MYSQL_PASSWORD`; deixe `MYSQL_HOST="127.0.0.1"` e `MYSQL_PORT="3307"`.
4. Na raiz do projeto, inicie o MySQL:

```sh
sudo docker compose up -d
```

O contêiner mantém os dados em um volume Docker separado; não remova esse volume se quiser preservá-los.

5. Importe `documentacao/BancoDeDados/scripts/criação_BancoDeDados_Mysql.sql` para o MySQL do contêiner. Você pode conectar pelo MySQL Workbench em `127.0.0.1`, porta `3307`, usando o usuário `root` e `MYSQL_ROOT_PASSWORD` do `.env`. O banco MySQL do contêiner começa vazio; isso não altera o conteúdo do MariaDB.
6. Inicie a aplicação a partir da raiz:

```sh
php -S localhost:8000 router.php
```

O roteador bloqueia o acesso HTTP aos arquivos ocultos, como `.env`. Use-o sempre ao iniciar o servidor PHP embutido. Para parar o MySQL sem apagar seus dados, use `sudo docker compose stop`.

Para acessar o painel em `pages/admin/adm.html`, a conta precisa ter `tipo = 'adm'` na tabela `usuario`. Você pode promover uma conta existente no MySQL:

```sql
UPDATE usuario SET tipo = 'adm' WHERE email = 'seu-email@exemplo.com';
```

O painel permite filtrar denúncias por categoria, consultar detalhes, encerrar denúncias sem remover o conteúdo ou concordar com a denúncia e remover o conteúdo. Remover uma conta também remove seus projetos e comentários.

## Recuperação de senha

As configurações de e-mail ficam no arquivo `.env` na raiz do projeto. Configure `DEVSHARE_BASE_URL`, `DEVSHARE_MAIL_FROM`, `DEVSHARE_MAIL_FROM_NAME`, `SENDPULSE_CLIENT_ID` e `SENDPULSE_CLIENT_SECRET`. O arquivo `.env.example` mostra o formato; não coloque credenciais reais nele.

O arquivo `.env` é ignorado pelo Git para manter as credenciais fora do GitHub. O remetente precisa estar autorizado na SendPulse. Para testes locais, `DEVSHARE_BASE_URL` pode ser `http://localhost:8000`; em produção, use HTTPS.

Depois de preencher o `.env`, inicie o servidor com o roteador seguro:

```sh
php -S localhost:8000 router.php
```

Para testar, abra “Esqueceu a senha?” na tela de login e solicite a recuperação usando o e-mail de uma conta cadastrada. O usuário deve receber uma mensagem de sucesso; confira também a caixa de spam. Se houver erro, consulte o log do PHP e confirme as credenciais OAuth e o remetente autorizado no painel da SendPulse.
