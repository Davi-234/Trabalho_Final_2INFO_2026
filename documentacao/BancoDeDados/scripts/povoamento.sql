USE dev_share;

-- Povoamento fictício para o banco DevShare.
-- 30 registros por tabela, respeitando PKs, FKs, UNIQUEs e a autorreferência de comentario.
-- Premissa para os dados de teste: visibilidade = 1 (público) e 0 (privado).

-- ======================================================
-- USUARIO (30 registros)
-- ======================================================
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (1, 'Ana Martins', 'ana.martins@devshare.com', SHA2('DevShareSenha01', 256), 'https://github.com/ana.martins', 'https://www.linkedin.com/in/ana.martins', 'https://lattes.cnpq.br/devshare0001', 'adm', 'foto_01.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (2, 'Bruno Almeida', 'bruno.almeida@devshare.com', SHA2('DevShareSenha02', 256), 'https://github.com/bruno.almeida', 'https://www.linkedin.com/in/bruno.almeida', 'https://lattes.cnpq.br/devshare0002', 'comum', 'foto_02.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (3, 'Carlos Oliveira', 'carlos.oliveira@devshare.com', SHA2('DevShareSenha03', 256), 'https://github.com/carlos.oliveira', 'https://www.linkedin.com/in/carlos.oliveira', 'https://lattes.cnpq.br/devshare0003', 'comum', 'foto_03.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (4, 'Daniel Souza', 'daniel.souza@devshare.com', SHA2('DevShareSenha04', 256), 'https://github.com/daniel.souza', 'https://www.linkedin.com/in/daniel.souza', 'https://lattes.cnpq.br/devshare0004', 'comum', 'foto_04.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (5, 'Eduardo Pereira', 'eduardo.pereira@devshare.com', SHA2('DevShareSenha05', 256), 'https://github.com/eduardo.pereira', 'https://www.linkedin.com/in/eduardo.pereira', 'https://lattes.cnpq.br/devshare0005', 'comum', 'foto_05.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (6, 'Fernanda Costa', 'fernanda.costa@devshare.com', SHA2('DevShareSenha06', 256), 'https://github.com/fernanda.costa', 'https://www.linkedin.com/in/fernanda.costa', 'https://lattes.cnpq.br/devshare0006', 'comum', 'foto_06.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (7, 'Gabriel Santos', 'gabriel.santos@devshare.com', SHA2('DevShareSenha07', 256), 'https://github.com/gabriel.santos', 'https://www.linkedin.com/in/gabriel.santos', 'https://lattes.cnpq.br/devshare0007', 'comum', 'foto_07.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (8, 'Helena Ribeiro', 'helena.ribeiro@devshare.com', SHA2('DevShareSenha08', 256), 'https://github.com/helena.ribeiro', 'https://www.linkedin.com/in/helena.ribeiro', 'https://lattes.cnpq.br/devshare0008', 'comum', 'foto_08.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (9, 'Igor Carvalho', 'igor.carvalho@devshare.com', SHA2('DevShareSenha09', 256), 'https://github.com/igor.carvalho', 'https://www.linkedin.com/in/igor.carvalho', 'https://lattes.cnpq.br/devshare0009', 'comum', 'foto_09.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (10, 'Juliana Ferreira', 'juliana.ferreira@devshare.com', SHA2('DevShareSenha10', 256), 'https://github.com/juliana.ferreira', 'https://www.linkedin.com/in/juliana.ferreira', 'https://lattes.cnpq.br/devshare0010', 'comum', 'foto_10.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (11, 'Kaique Mendes', 'kaique.mendes@devshare.com', SHA2('DevShareSenha11', 256), 'https://github.com/kaique.mendes', 'https://www.linkedin.com/in/kaique.mendes', 'https://lattes.cnpq.br/devshare0011', 'comum', 'foto_11.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (12, 'Larissa Rocha', 'larissa.rocha@devshare.com', SHA2('DevShareSenha12', 256), 'https://github.com/larissa.rocha', 'https://www.linkedin.com/in/larissa.rocha', 'https://lattes.cnpq.br/devshare0012', 'comum', 'foto_12.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (13, 'Marcos Lima', 'marcos.lima@devshare.com', SHA2('DevShareSenha13', 256), 'https://github.com/marcos.lima', 'https://www.linkedin.com/in/marcos.lima', 'https://lattes.cnpq.br/devshare0013', 'comum', 'foto_13.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (14, 'Natália Gomes', 'natalia.gomes@devshare.com', SHA2('DevShareSenha14', 256), 'https://github.com/natalia.gomes', 'https://www.linkedin.com/in/natalia.gomes', 'https://lattes.cnpq.br/devshare0014', 'comum', 'foto_14.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (15, 'Otávio Nunes', 'otavio.nunes@devshare.com', SHA2('DevShareSenha15', 256), 'https://github.com/otavio.nunes', 'https://www.linkedin.com/in/otavio.nunes', 'https://lattes.cnpq.br/devshare0015', 'comum', 'foto_15.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (16, 'Patrícia Dias', 'patricia.dias@devshare.com', SHA2('DevShareSenha16', 256), 'https://github.com/patricia.dias', 'https://www.linkedin.com/in/patricia.dias', 'https://lattes.cnpq.br/devshare0016', 'comum', 'foto_16.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (17, 'Rafael Teixeira', 'rafael.teixeira@devshare.com', SHA2('DevShareSenha17', 256), 'https://github.com/rafael.teixeira', 'https://www.linkedin.com/in/rafael.teixeira', 'https://lattes.cnpq.br/devshare0017', 'comum', 'foto_17.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (18, 'Sabrina Lopes', 'sabrina.lopes@devshare.com', SHA2('DevShareSenha18', 256), 'https://github.com/sabrina.lopes', 'https://www.linkedin.com/in/sabrina.lopes', 'https://lattes.cnpq.br/devshare0018', 'comum', 'foto_18.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (19, 'Thiago Barbosa', 'thiago.barbosa@devshare.com', SHA2('DevShareSenha19', 256), 'https://github.com/thiago.barbosa', 'https://www.linkedin.com/in/thiago.barbosa', 'https://lattes.cnpq.br/devshare0019', 'comum', 'foto_19.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (20, 'Úrsula Castro', 'ursula.castro@devshare.com', SHA2('DevShareSenha20', 256), 'https://github.com/ursula.castro', 'https://www.linkedin.com/in/ursula.castro', 'https://lattes.cnpq.br/devshare0020', 'comum', 'foto_20.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (21, 'Vinícius Cardoso', 'vinicius.cardoso@devshare.com', SHA2('DevShareSenha21', 256), 'https://github.com/vinicius.cardoso', 'https://www.linkedin.com/in/vinicius.cardoso', 'https://lattes.cnpq.br/devshare0021', 'comum', 'foto_21.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (22, 'Wesley Duarte', 'wesley.duarte@devshare.com', SHA2('DevShareSenha22', 256), 'https://github.com/wesley.duarte', 'https://www.linkedin.com/in/wesley.duarte', 'https://lattes.cnpq.br/devshare0022', 'comum', 'foto_22.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (23, 'Yasmin Moreira', 'yasmin.moreira@devshare.com', SHA2('DevShareSenha23', 256), 'https://github.com/yasmin.moreira', 'https://www.linkedin.com/in/yasmin.moreira', 'https://lattes.cnpq.br/devshare0023', 'comum', 'foto_23.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (24, 'Zélia Monteiro', 'zelia.monteiro@devshare.com', SHA2('DevShareSenha24', 256), 'https://github.com/zelia.monteiro', 'https://www.linkedin.com/in/zelia.monteiro', 'https://lattes.cnpq.br/devshare0024', 'comum', 'foto_24.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (25, 'Arthur Freitas', 'arthur.freitas@devshare.com', SHA2('DevShareSenha25', 256), 'https://github.com/arthur.freitas', 'https://www.linkedin.com/in/arthur.freitas', 'https://lattes.cnpq.br/devshare0025', 'comum', 'foto_25.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (26, 'Beatriz Ramos', 'beatriz.ramos@devshare.com', SHA2('DevShareSenha26', 256), 'https://github.com/beatriz.ramos', 'https://www.linkedin.com/in/beatriz.ramos', 'https://lattes.cnpq.br/devshare0026', 'comum', 'foto_26.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (27, 'Caio Batista', 'caio.batista@devshare.com', SHA2('DevShareSenha27', 256), 'https://github.com/caio.batista', 'https://www.linkedin.com/in/caio.batista', 'https://lattes.cnpq.br/devshare0027', 'comum', 'foto_27.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (28, 'Débora Moraes', 'debora.moraes@devshare.com', SHA2('DevShareSenha28', 256), 'https://github.com/debora.moraes', 'https://www.linkedin.com/in/debora.moraes', 'https://lattes.cnpq.br/devshare0028', 'comum', 'foto_28.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (29, 'Enzo Vieira', 'enzo.vieira@devshare.com', SHA2('DevShareSenha29', 256), 'https://github.com/enzo.vieira', 'https://www.linkedin.com/in/enzo.vieira', 'https://lattes.cnpq.br/devshare0029', 'comum', 'foto_29.png');
INSERT INTO usuario (id, nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil) VALUES (30, 'Flávia Correia', 'flavia.correia@devshare.com', SHA2('DevShareSenha30', 256), 'https://github.com/flavia.correia', 'https://www.linkedin.com/in/flavia.correia', 'https://lattes.cnpq.br/devshare0030', 'comum', 'foto_30.png');

-- ======================================================
-- PROJETO (30 registros)
-- ======================================================
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (1, 1, '2026-07-02 09:03:00', 'Sistema para organizar compromissos, provas e tarefas.', '2026-08-02 14:05:00', 'Agenda Acadêmica', 'em_desenvolvimento', 'iniciante', 'projeto_01.png', 137, 'https://github.com/devshare/projeto-01', 7);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (2, 1, '2026-07-03 09:06:00', 'API REST para gerenciamento de livros, autores e empréstimos.', '2026-08-03 14:10:00', 'API de Biblioteca', 'completo', 'intermediario', 'projeto_02.png', 274, 'https://github.com/devshare/projeto-02', 14);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (3, 1, '2026-07-04 09:09:00', 'Aplicação para registrar produtos, entradas, saídas e inventário.', '2026-08-04 14:15:00', 'Controle de Estoque', 'pausado', 'avancado', 'projeto_03.png', 411, 'https://github.com/devshare/projeto-03', 21);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (4, 0, '2026-07-05 09:12:00', 'Plataforma para divulgação e inscrição em eventos acadêmicos.', '2026-08-05 14:20:00', 'Portal de Eventos', 'em_desenvolvimento', 'iniciante', 'projeto_04.png', 548, 'https://github.com/devshare/projeto-04', 28);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (5, 1, '2026-07-06 09:15:00', 'Aplicação de mensagens para equipes e grupos de estudo.', '2026-08-06 14:25:00', 'Chat Colaborativo', 'completo', 'intermediario', NULL, 685, 'https://github.com/devshare/projeto-05', 5);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (6, 1, '2026-07-07 09:18:00', 'Dashboard para acompanhamento de receitas, despesas e metas.', '2026-08-07 14:30:00', 'Painel Financeiro', 'pausado', 'avancado', 'projeto_06.png', 822, 'https://github.com/devshare/projeto-06', 12);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (7, 1, '2026-07-08 09:21:00', 'Sistema para cadastro de cursos, módulos e materiais.', '2026-08-08 14:35:00', 'Gerenciador de Cursos', 'em_desenvolvimento', 'iniciante', 'projeto_07.png', 959, 'https://github.com/devshare/projeto-07', 19);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (8, 0, '2026-07-09 09:24:00', 'Aplicação web para consultar e classificar filmes.', '2026-08-09 14:40:00', 'Catálogo de Filmes', 'completo', 'intermediario', 'projeto_08.png', 1096, 'https://github.com/devshare/projeto-08', 26);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (9, 1, '2026-07-10 09:27:00', 'Painel para acompanhar disponibilidade e métricas básicas.', '2026-08-10 14:45:00', 'Monitor de Servidores', 'pausado', 'avancado', 'projeto_09.png', 1233, 'https://github.com/devshare/projeto-09', 3);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (10, 1, '2026-07-11 09:30:00', 'Gerenciador simples de tarefas pessoais e acadêmicas.', '2026-08-11 14:50:00', 'Lista de Tarefas', 'em_desenvolvimento', 'iniciante', NULL, 1370, 'https://github.com/devshare/projeto-10', 10);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (11, 1, '2026-07-12 09:33:00', 'Controle de acervo, usuários, reservas e devoluções.', '2026-08-12 14:55:00', 'Sistema de Biblioteca', 'completo', 'intermediario', 'projeto_11.png', 1507, 'https://github.com/devshare/projeto-11', 17);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (12, 0, '2026-07-13 09:36:00', 'Mapa visual para descoberta e organização de projetos.', '2026-08-13 14:00:00', 'Mapa de Projetos', 'pausado', 'avancado', 'projeto_12.png', 1644, 'https://github.com/devshare/projeto-12', 24);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (13, 1, '2026-07-14 09:39:00', 'Portal para publicação e discussão de artigos técnicos.', '2026-08-14 14:05:00', 'Blog de Tecnologia', 'em_desenvolvimento', 'iniciante', 'projeto_13.png', 1781, 'https://github.com/devshare/projeto-13', 1);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (14, 1, '2026-07-15 09:42:00', 'Jogo de perguntas sobre lógica e desenvolvimento.', '2026-08-15 14:10:00', 'Quiz de Programação', 'completo', 'intermediario', 'projeto_14.png', 1918, 'https://github.com/devshare/projeto-14', 8);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (15, 1, '2026-07-16 09:45:00', 'Sistema de agendamento e controle de horários.', '2026-08-16 14:15:00', 'Agenda de Consultas', 'pausado', 'avancado', NULL, 2055, 'https://github.com/devshare/projeto-15', 15);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (16, 0, '2026-07-17 09:48:00', 'Protótipo de comércio de materiais didáticos.', '2026-08-17 14:20:00', 'Loja Virtual Educacional', 'em_desenvolvimento', 'iniciante', 'projeto_16.png', 2192, 'https://github.com/devshare/projeto-16', 22);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (17, 1, '2026-07-18 09:51:00', 'Aplicação para operações matemáticas e funções científicas.', '2026-08-18 14:25:00', 'Calculadora Científica', 'completo', 'intermediario', 'projeto_17.png', 2329, 'https://github.com/devshare/projeto-17', 29);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (18, 1, '2026-07-19 09:54:00', 'Protótipo para organização segura de credenciais.', '2026-08-01 14:30:00', 'Gerenciador de Senhas', 'pausado', 'avancado', 'projeto_18.png', 2466, 'https://github.com/devshare/projeto-18', 6);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (19, 1, '2026-07-20 09:57:00', 'Sistema para acompanhar hábitos e metas pessoais.', '2026-08-02 14:35:00', 'Controle de Hábitos', 'em_desenvolvimento', 'iniciante', 'projeto_19.png', 2603, 'https://github.com/devshare/projeto-19', 13);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (20, 0, '2026-07-21 09:00:00', 'Aplicação para criação e apuração de votações.', '2026-08-03 14:40:00', 'Sistema de Votação', 'completo', 'intermediario', NULL, 2740, 'https://github.com/devshare/projeto-20', 20);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (21, 1, '2026-07-22 09:03:00', 'Ferramenta para leitura e sumarização de arquivos de log.', '2026-08-04 14:45:00', 'Analisador de Logs', 'pausado', 'avancado', 'projeto_21.png', 2877, 'https://github.com/devshare/projeto-21', 27);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (22, 1, '2026-07-23 09:06:00', 'Serviço para conversão entre formatos de documentos.', '2026-08-05 14:50:00', 'Conversor de Arquivos', 'em_desenvolvimento', 'iniciante', 'projeto_22.png', 3014, 'https://github.com/devshare/projeto-22', 4);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (23, 1, '2026-07-24 09:09:00', 'Aplicação para montar cronogramas de estudos.', '2026-08-06 14:55:00', 'Planner de Estudos', 'completo', 'intermediario', 'projeto_23.png', 3151, 'https://github.com/devshare/projeto-23', 11);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (24, 0, '2026-07-25 09:12:00', 'Plataforma de apresentação de projetos e competências.', '2026-08-07 14:00:00', 'Rede de Portfólios', 'pausado', 'avancado', 'projeto_24.png', 3288, 'https://github.com/devshare/projeto-24', 18);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (25, 1, '2026-07-26 09:15:00', 'Editor web com visualização de documentos Markdown.', '2026-08-08 14:05:00', 'Editor Markdown', 'em_desenvolvimento', 'iniciante', NULL, 3425, 'https://github.com/devshare/projeto-25', 25);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (26, 1, '2026-07-27 09:18:00', 'Sistema para organizar equipes, papéis e tarefas.', '2026-08-09 14:10:00', 'Gestor de Equipes', 'completo', 'intermediario', 'projeto_26.png', 3562, 'https://github.com/devshare/projeto-26', 2);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (27, 1, '2026-07-28 09:21:00', 'Serviço para centralizar notificações de aplicações.', '2026-08-10 14:15:00', 'API de Notificações', 'pausado', 'avancado', 'projeto_27.png', 3699, 'https://github.com/devshare/projeto-27', 9);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (28, 0, '2026-07-01 09:24:00', 'Aplicação para reservar salas e recursos.', '2026-08-11 14:20:00', 'Sistema de Reservas', 'em_desenvolvimento', 'iniciante', 'projeto_28.png', 3836, 'https://github.com/devshare/projeto-28', 16);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (29, 1, '2026-07-02 09:27:00', 'Dashboard para indicadores de aplicações web.', '2026-08-12 14:25:00', 'Painel de Métricas', 'completo', 'intermediario', 'projeto_29.png', 3973, 'https://github.com/devshare/projeto-29', 23);
INSERT INTO projeto (id, visibilidade, data_criacao, descricao, data_atualizacao, nome, status, nivel, imagem_projeto, views, link_repositorio, usuario_id) VALUES (30, 1, '2026-07-03 09:30:00', 'Projeto experimental para monitoramento de sensores.', '2026-08-13 14:30:00', 'Laboratório IoT', 'pausado', 'avancado', NULL, 4110, 'https://github.com/devshare/projeto-30', 30);

-- ======================================================
-- AVALIACAO (30 registros)
-- ======================================================
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (1, 8, '2026-08-02 09:02:00', 1, 10);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (2, 5, '2026-08-03 10:04:00', 2, 11);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (3, 2, '2026-08-04 11:06:00', 3, 12);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (4, 9, '2026-08-05 12:08:00', 4, 13);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (5, 6, '2026-08-06 13:10:00', 5, 14);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (6, 3, '2026-08-07 14:12:00', 6, 15);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (7, 10, '2026-08-08 15:14:00', 7, 16);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (8, 7, '2026-08-09 16:16:00', 8, 17);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (9, 4, '2026-08-10 17:18:00', 9, 18);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (10, 1, '2026-08-11 08:20:00', 10, 19);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (11, 8, '2026-08-12 09:22:00', 11, 20);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (12, 5, '2026-08-13 10:24:00', 12, 21);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (13, 2, '2026-08-14 11:26:00', 13, 22);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (14, 9, '2026-08-15 12:28:00', 14, 23);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (15, 6, '2026-08-16 13:30:00', 15, 24);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (16, 3, '2026-08-17 14:32:00', 16, 25);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (17, 10, '2026-08-18 15:34:00', 17, 26);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (18, 7, '2026-08-01 16:36:00', 18, 27);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (19, 4, '2026-08-02 17:38:00', 19, 28);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (20, 1, '2026-08-03 08:40:00', 20, 29);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (21, 8, '2026-08-04 09:42:00', 21, 30);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (22, 5, '2026-08-05 10:44:00', 22, 1);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (23, 2, '2026-08-06 11:46:00', 23, 2);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (24, 9, '2026-08-07 12:48:00', 24, 3);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (25, 6, '2026-08-08 13:50:00', 25, 4);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (26, 3, '2026-08-09 14:52:00', 26, 5);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (27, 10, '2026-08-10 15:54:00', 27, 6);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (28, 7, '2026-08-11 16:56:00', 28, 7);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (29, 4, '2026-08-12 17:58:00', 29, 8);
INSERT INTO avaliacao (id, nota, data_criacao, projeto_id, usuario_id) VALUES (30, 1, '2026-08-13 08:00:00', 30, 9);

-- ======================================================
-- COMENTARIO (30 registros)
-- ======================================================
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (1, 'Projeto bem estruturado e fácil de compreender.', '2026-08-02 11:03:00', 1, 1, NULL);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (2, 'A descrição do projeto está bastante clara.', '2026-08-03 12:06:00', 2, 1, 1);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (3, 'Gostei da organização das funcionalidades.', '2026-08-04 13:09:00', 3, 1, 2);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (4, 'Seria interessante acrescentar testes automatizados.', '2026-08-05 14:12:00', 4, 2, NULL);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (5, 'A proposta atende bem ao objetivo apresentado.', '2026-08-06 15:15:00', 5, 2, 4);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (6, 'A interface parece simples e funcional.', '2026-08-07 16:18:00', 6, 2, 5);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (7, 'A arquitetura escolhida é adequada ao problema.', '2026-08-08 17:21:00', 7, 3, NULL);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (8, 'Talvez valha documentar melhor a API.', '2026-08-09 18:24:00', 8, 3, 7);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (9, 'O escopo do projeto está bem delimitado.', '2026-08-10 10:27:00', 9, 3, 8);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (10, 'A solução apresenta uma boa separação de responsabilidades.', '2026-08-11 11:30:00', 10, 4, NULL);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (11, 'Seria útil incluir exemplos de uso no repositório.', '2026-08-12 12:33:00', 11, 4, 10);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (12, 'O projeto mostra uma evolução interessante.', '2026-08-13 13:36:00', 12, 4, 11);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (13, 'A ideia pode ser expandida para outros cenários.', '2026-08-14 14:39:00', 13, 5, NULL);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (14, 'Os dados utilizados ficaram coerentes com a proposta.', '2026-08-15 15:42:00', 14, 5, 13);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (15, 'Gostei da combinação das tecnologias.', '2026-08-16 16:45:00', 15, 5, 14);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (16, 'A documentação poderia trazer mais detalhes de instalação.', '2026-08-17 17:48:00', 16, 6, NULL);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (17, 'A funcionalidade principal está bem definida.', '2026-08-18 18:51:00', 17, 6, 16);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (18, 'O projeto parece promissor.', '2026-08-01 10:54:00', 18, 6, 17);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (19, 'A modelagem apresentada é consistente.', '2026-08-02 11:57:00', 19, 7, NULL);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (20, 'Seria interessante disponibilizar uma demonstração.', '2026-08-03 12:00:00', 20, 7, 19);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (21, 'O código aparenta seguir uma organização razoável.', '2026-08-04 13:03:00', 21, 7, 20);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (22, 'A descrição poderia ser um pouco mais detalhada.', '2026-08-05 14:06:00', 22, 8, NULL);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (23, 'Boa escolha para um projeto de estudo.', '2026-08-06 15:09:00', 23, 8, 22);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (24, 'A solução é compatível com o nível informado.', '2026-08-07 16:12:00', 24, 8, 23);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (25, 'Gostei da ideia e da divisão dos módulos.', '2026-08-08 17:15:00', 25, 9, NULL);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (26, 'Seria interessante acrescentar tratamento de erros.', '2026-08-09 18:18:00', 26, 9, 25);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (27, 'O objetivo do projeto ficou evidente.', '2026-08-10 10:21:00', 27, 9, 26);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (28, 'A proposta possui espaço para futuras melhorias.', '2026-08-11 11:24:00', 28, 10, NULL);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (29, 'A implementação parece coerente com a descrição.', '2026-08-12 12:27:00', 29, 10, 28);
INSERT INTO comentario (id, descricao, data, usuario_id, projeto_id, comentario_pai_id) VALUES (30, 'Bom exemplo de aplicação prática.', '2026-08-13 13:30:00', 30, 10, 29);

-- ======================================================
-- DENUNCIA (30 registros)
-- ======================================================
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (1, 'Spam', '2026-08-02 13:04:00', 'pendente', 'projeto', 3, NULL, 5, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (2, 'Conteúdo impróprio', '2026-08-03 14:08:00', 'analisando', 'comentario', 6, 14, NULL, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (3, 'Linguagem ofensiva', '2026-08-04 15:12:00', 'removida', 'usuario', 9, NULL, NULL, 3);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (4, 'Informação enganosa', '2026-08-05 16:16:00', 'pendente', 'projeto', 12, NULL, 20, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (5, 'Conteúdo duplicado', '2026-08-06 17:20:00', 'analisando', 'comentario', 15, 5, NULL, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (6, 'Uso indevido da plataforma', '2026-08-07 18:24:00', 'removida', 'usuario', 18, NULL, NULL, 6);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (7, 'Código malicioso', '2026-08-08 19:28:00', 'pendente', 'projeto', 21, NULL, 5, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (8, 'Publicidade excessiva', '2026-08-09 12:32:00', 'analisando', 'comentario', 24, 26, NULL, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (9, 'Assédio', '2026-08-10 13:36:00', 'removida', 'usuario', 27, NULL, NULL, 9);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (10, 'Plágio aparente', '2026-08-11 14:40:00', 'pendente', 'projeto', NULL, NULL, 20, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (11, 'Spam', '2026-08-12 15:44:00', 'analisando', 'comentario', 3, 17, NULL, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (12, 'Conteúdo impróprio', '2026-08-13 16:48:00', 'removida', 'usuario', 6, NULL, NULL, 12);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (13, 'Linguagem ofensiva', '2026-08-14 17:52:00', 'pendente', 'projeto', 9, NULL, 5, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (14, 'Informação enganosa', '2026-08-15 18:56:00', 'analisando', 'comentario', 12, 8, NULL, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (15, 'Conteúdo duplicado', '2026-08-16 19:00:00', 'removida', 'usuario', 15, NULL, NULL, 15);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (16, 'Uso indevido da plataforma', '2026-08-17 12:04:00', 'pendente', 'projeto', 18, NULL, 20, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (17, 'Código malicioso', '2026-08-18 13:08:00', 'analisando', 'comentario', 21, 29, NULL, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (18, 'Publicidade excessiva', '2026-08-01 14:12:00', 'removida', 'usuario', 24, NULL, NULL, 18);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (19, 'Assédio', '2026-08-02 15:16:00', 'pendente', 'projeto', 27, NULL, 5, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (20, 'Plágio aparente', '2026-08-03 16:20:00', 'analisando', 'comentario', NULL, 20, NULL, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (21, 'Spam', '2026-08-04 17:24:00', 'removida', 'usuario', 3, NULL, NULL, 21);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (22, 'Conteúdo impróprio', '2026-08-05 18:28:00', 'pendente', 'projeto', 6, NULL, 20, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (23, 'Linguagem ofensiva', '2026-08-06 19:32:00', 'analisando', 'comentario', 9, 11, NULL, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (24, 'Informação enganosa', '2026-08-07 12:36:00', 'removida', 'usuario', 12, NULL, NULL, 24);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (25, 'Conteúdo duplicado', '2026-08-08 13:40:00', 'pendente', 'projeto', 15, NULL, 5, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (26, 'Uso indevido da plataforma', '2026-08-09 14:44:00', 'analisando', 'comentario', 18, 2, NULL, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (27, 'Código malicioso', '2026-08-10 15:48:00', 'removida', 'usuario', 21, NULL, NULL, 27);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (28, 'Publicidade excessiva', '2026-08-11 16:52:00', 'pendente', 'projeto', 24, NULL, 20, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (29, 'Assédio', '2026-08-12 17:56:00', 'analisando', 'comentario', 27, 23, NULL, NULL);
INSERT INTO denuncia (id, motivo, data, status, tipo, denunciante_id, comentario_denunciado_id, projeto_denunciado_id, usuario_denunciado_id) VALUES (30, 'Plágio aparente', '2026-08-13 18:00:00', 'removida', 'usuario', NULL, NULL, NULL, 30);

-- ======================================================
-- TAG (30 registros)
-- ======================================================
INSERT INTO tag (id, nome) VALUES (1, 'API');
INSERT INTO tag (id, nome) VALUES (2, 'Backend');
INSERT INTO tag (id, nome) VALUES (3, 'Banco de Dados');
INSERT INTO tag (id, nome) VALUES (4, 'Desktop');
INSERT INTO tag (id, nome) VALUES (5, 'Frontend');
INSERT INTO tag (id, nome) VALUES (6, 'Mobile');
INSERT INTO tag (id, nome) VALUES (7, 'Web');
INSERT INTO tag (id, nome) VALUES (8, 'Segurança');
INSERT INTO tag (id, nome) VALUES (9, 'DevOps');
INSERT INTO tag (id, nome) VALUES (10, 'Automação');
INSERT INTO tag (id, nome) VALUES (11, 'Testes');
INSERT INTO tag (id, nome) VALUES (12, 'Educação');
INSERT INTO tag (id, nome) VALUES (13, 'Finanças');
INSERT INTO tag (id, nome) VALUES (14, 'Gestão');
INSERT INTO tag (id, nome) VALUES (15, 'Dados');
INSERT INTO tag (id, nome) VALUES (16, 'IoT');
INSERT INTO tag (id, nome) VALUES (17, 'Cloud');
INSERT INTO tag (id, nome) VALUES (18, 'Open Source');
INSERT INTO tag (id, nome) VALUES (19, 'Machine Learning');
INSERT INTO tag (id, nome) VALUES (20, 'Algoritmos');
INSERT INTO tag (id, nome) VALUES (21, 'Estruturas de Dados');
INSERT INTO tag (id, nome) VALUES (22, 'Redes');
INSERT INTO tag (id, nome) VALUES (23, 'Sistemas Operacionais');
INSERT INTO tag (id, nome) VALUES (24, 'Java');
INSERT INTO tag (id, nome) VALUES (25, 'Python');
INSERT INTO tag (id, nome) VALUES (26, 'JavaScript');
INSERT INTO tag (id, nome) VALUES (27, 'TypeScript');
INSERT INTO tag (id, nome) VALUES (28, 'CSharp');
INSERT INTO tag (id, nome) VALUES (29, 'PHP');
INSERT INTO tag (id, nome) VALUES (30, 'Kotlin');

-- ======================================================
-- PROJETO_TAG (30 registros)
-- ======================================================
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (1, 7);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (2, 14);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (3, 21);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (4, 28);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (5, 5);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (6, 12);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (7, 19);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (8, 26);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (9, 3);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (10, 10);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (11, 17);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (12, 24);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (13, 1);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (14, 8);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (15, 15);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (16, 22);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (17, 29);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (18, 6);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (19, 13);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (20, 20);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (21, 27);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (22, 4);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (23, 11);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (24, 18);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (25, 25);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (26, 2);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (27, 9);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (28, 16);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (29, 23);
INSERT INTO projeto_tag (projeto_id, tag_id) VALUES (30, 30);

-- ======================================================
-- TECNOLOGIA (30 registros)
-- ======================================================
INSERT INTO tecnologia (id, nome) VALUES (1, 'Angular');
INSERT INTO tecnologia (id, nome) VALUES (2, 'Docker');
INSERT INTO tecnologia (id, nome) VALUES (3, 'Java');
INSERT INTO tecnologia (id, nome) VALUES (4, 'JavaScript');
INSERT INTO tecnologia (id, nome) VALUES (5, 'MySQL');
INSERT INTO tecnologia (id, nome) VALUES (6, 'Python');
INSERT INTO tecnologia (id, nome) VALUES (7, 'React');
INSERT INTO tecnologia (id, nome) VALUES (8, 'Spring');
INSERT INTO tecnologia (id, nome) VALUES (9, 'TypeScript');
INSERT INTO tecnologia (id, nome) VALUES (10, 'C');
INSERT INTO tecnologia (id, nome) VALUES (11, 'C++');
INSERT INTO tecnologia (id, nome) VALUES (12, 'CSharp');
INSERT INTO tecnologia (id, nome) VALUES (13, 'PHP');
INSERT INTO tecnologia (id, nome) VALUES (14, 'Kotlin');
INSERT INTO tecnologia (id, nome) VALUES (15, 'Go');
INSERT INTO tecnologia (id, nome) VALUES (16, 'Rust');
INSERT INTO tecnologia (id, nome) VALUES (17, 'Dart');
INSERT INTO tecnologia (id, nome) VALUES (18, 'Flutter');
INSERT INTO tecnologia (id, nome) VALUES (19, 'Node.js');
INSERT INTO tecnologia (id, nome) VALUES (20, 'Express');
INSERT INTO tecnologia (id, nome) VALUES (21, 'Vue.js');
INSERT INTO tecnologia (id, nome) VALUES (22, 'Laravel');
INSERT INTO tecnologia (id, nome) VALUES (23, 'Django');
INSERT INTO tecnologia (id, nome) VALUES (24, 'FastAPI');
INSERT INTO tecnologia (id, nome) VALUES (25, 'PostgreSQL');
INSERT INTO tecnologia (id, nome) VALUES (26, 'SQLite');
INSERT INTO tecnologia (id, nome) VALUES (27, 'Redis');
INSERT INTO tecnologia (id, nome) VALUES (28, 'Git');
INSERT INTO tecnologia (id, nome) VALUES (29, 'Linux');
INSERT INTO tecnologia (id, nome) VALUES (30, 'TensorFlow');

-- ======================================================
-- PROJETO_TECNOLOGIA (30 registros)
-- ======================================================
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (11, 1);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (22, 2);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (3, 3);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (14, 4);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (25, 5);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (6, 6);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (17, 7);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (28, 8);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (9, 9);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (20, 10);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (1, 11);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (12, 12);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (23, 13);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (4, 14);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (15, 15);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (26, 16);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (7, 17);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (18, 18);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (29, 19);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (10, 20);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (21, 21);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (2, 22);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (13, 23);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (24, 24);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (5, 25);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (16, 26);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (27, 27);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (8, 28);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (19, 29);
INSERT INTO projeto_tecnologia (tecnologias_id, projeto_id) VALUES (30, 30);