USE dev_share;

-- ==========================
-- USUÁRIOS
-- ==========================

INSERT INTO usuario
(nome, email, senha_hash, link_github, link_linkedin, perfil_lattes, tipo, foto_perfil)
VALUES
('Ana',     'ana@devshare.com',     'senha_a', 'https://github.com/ana',     'https://linkedin.com/in/ana',     'https://lattes.cnpq.br/0001', 'adm',    'a.png'),
('Bruno',   'bruno@devshare.com',   'senha_b', 'https://github.com/bruno',   'https://linkedin.com/in/bruno',   'https://lattes.cnpq.br/0002', 'comum', 'b.png'),
('Carlos',  'carlos@devshare.com',  'senha_c', 'https://github.com/carlos',  'https://linkedin.com/in/carlos',  'https://lattes.cnpq.br/0003', 'comum', 'c.png'),
('Daniel',  'daniel@devshare.com',  'senha_d', 'https://github.com/daniel',  'https://linkedin.com/in/daniel',  'https://lattes.cnpq.br/0004', 'comum', 'd.png'),
('Eduardo', 'eduardo@devshare.com', 'senha_e', 'https://github.com/eduardo', 'https://linkedin.com/in/eduardo', 'https://lattes.cnpq.br/0005', 'comum', 'e.png');

-- ==========================
-- PROJETOS
-- ==========================

INSERT INTO projeto
(visibilidade, descricao, nome, status, nivel, usuario_id, imagem_projeto)
VALUES
(1,'Aplicação Alfa',   'Alfa',   'em_desenvolvimento','iniciante',2,'alfa.png'),
(1,'Aplicação Beta',   'Beta',   'completo','intermediario',3,'beta.png'),
(1,'Aplicação Gama',   'Gama',   'pausado','avancado',4,'gama.png'),
(1,'Aplicação Delta',  'Delta',  'em_desenvolvimento','intermediario',5,'delta.png');

-- ==========================
-- TECNOLOGIAS
-- ==========================

INSERT INTO tecnologia(nome)
VALUES
('Angular'),
('Docker'),
('Java'),
('JavaScript'),
('MySQL'),
('Python'),
('React'),
('Spring');

-- ==========================
-- TAGS
-- ==========================

INSERT INTO tag(nome)
VALUES
('API'),
('Backend'),
('Banco'),
('Desktop'),
('Frontend'),
('Mobile'),
('Web');

-- ==========================
-- PROJETO_TAG
-- ==========================

INSERT INTO projeto_tag
VALUES
(1,1),
(1,2),
(2,5),
(2,7),
(3,2),
(3,3),
(4,1),
(4,5);

-- ==========================
-- PROJETO_TECNOLOGIA
-- ==========================

INSERT INTO projeto_tecnologia
VALUES
(3,1),
(5,1),
(1,2),
(4,2),
(6,3),
(5,3),
(8,4),
(2,4);

-- ==========================
-- URLS
-- ==========================

INSERT INTO url(link, projeto_id)
VALUES
('https://github.com/devshare/alfa',1),
('https://github.com/devshare/beta',2),
('https://github.com/devshare/delta',4),
('https://github.com/devshare/gama',3);

-- ==========================
-- COMENTÁRIOS
-- ==========================

INSERT INTO comentario
(descricao, usuario_id, projeto_id)
VALUES
('Alfa comentário',2,1),
('Beta comentário',3,2),
('Delta comentário',5,4),
('Gama comentário',4,3);

INSERT INTO comentario
(descricao, usuario_id, projeto_id, comentario_pai_id)
VALUES
('Resposta Alfa',3,1,1),
('Resposta Beta',4,2,2);

-- ==========================
-- AVALIAÇÕES
-- ==========================

INSERT INTO avaliacao
(nota, projeto_id, usuario_id)
VALUES
(10,1,3),
(9,2,2),
(8,3,5),
(7,4,4);

-- ==========================
-- VISUALIZAÇÕES
-- ==========================

INSERT INTO visualizacao
(usuario_id, projeto_id)
VALUES
(2,1),
(3,2),
(4,3),
(5,4),
(2,3),
(3,4);

-- ==========================
-- DENÚNCIAS
-- ==========================

INSERT INTO denuncia
(motivo, status)
VALUES
('Abuso','pendente'),
('Conteúdo impróprio','analisando'),
('Spam','removida');

-- ==========================
-- DENÚNCIA DE PROJETO
-- ==========================

INSERT INTO denuncia_projeto
VALUES
(1,1,3);

-- ==========================
-- DENÚNCIA DE COMENTÁRIO
-- ==========================

INSERT INTO denuncia_comentario
VALUES
(2,2,4);

-- ==========================
-- DENÚNCIA DE USUÁRIO
-- ==========================

INSERT INTO denuncia_usuario
VALUES
(3,5,2);