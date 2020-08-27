DELETE FROM atividade_tipo;
DROP TABLE atividade_tipo;

DELETE FROM guiche;
DROP TABLE guiche;

DELETE FROM custo_cartao;
DROP TABLE custo_cartao;


DELETE FROM fluxo;
DROP TABLE fluxo;



DELETE FROM custo_unidade;
DROP TABLE custo_unidade;


DELETE FROM custo_refeicao;


ALTER TABLE registro DROP COLUMN  regi_valor_custo;

DROP TABLE custo_refeicao;

CREATE TABLE custo_refeicao
( 
cure_id serial NOT NULL, 
cure_valor  numeric(8,2), 
cure_inicio date,
cure_fim date,
unid_id integer, 
turn_id integer, 
CONSTRAINT pk_curee_id PRIMARY KEY (cure_id),
CONSTRAINT fk_unid_id FOREIGN KEY (unid_id) REFERENCES unidade(unid_id) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION, 
CONSTRAINT fk_turn_id FOREIGN KEY (turn_id) REFERENCES turno(turn_id) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION 	 
); 
