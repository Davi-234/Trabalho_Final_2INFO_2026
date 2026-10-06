select distinct GROUP_CONCAT(tag.nome separator ', ') as tecnologias
from tag 
	inner join projeto_tag on tag_id = tag.id
where 2 = projeto_id;

select distinct GROUP_CONCAT(tecnologia.nome separator ', ') as tecnologias
from tecnologia
	inner join projeto_tecnologia on tecnologias_id = tecnologia.id
where 2 = projeto_id;