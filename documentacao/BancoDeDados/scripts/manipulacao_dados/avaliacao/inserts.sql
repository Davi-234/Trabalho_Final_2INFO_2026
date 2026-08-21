-- insert into avaliacao (nota,data_criacao,projeto_id,usuario_id) values (5,"2026-05-02 15:20:00",1,1),(5,"2026-05-02 15:20:00",1,2);
-- update avaliacao set nota = 10 where avaliacao.id <= 10 and day(avaliacao.data_criacao) < 10;
-- delete from avaliacao where avaliacao.id <= 10 and avaliacao.usuario_id = 10;
select avaliacao.*
from avaliacao