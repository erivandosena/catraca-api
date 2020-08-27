<?php 

class CustoDAO extends DAO{

    
    public function listaDeCustos(){
        $lista = array();
        $result = $this->getConexao()->query("
                SELECT cure_id, cure_valor, unid_nome, turn_descricao, cure_inicio, cure_fim,
                 turno.turn_id as turn_id, custo_refeicao.unid_id as unid_id
                 FROM custo_refeicao
                INNER JOIN unidade ON unidade.unid_id = custo_refeicao.unid_id
                INNER JOIN turno ON turno.turn_id = custo_refeicao.turn_id;
            ");
        foreach($result as $linha){
            $custo = new Custo();
            $custo->setId($linha['cure_id']);
            $custo->setValor($linha['cure_valor']);
            $custo->getUnidade()->setId($linha['unid_id']);
            $custo->getUnidade()->setNome($linha['unid_nome']);
            $custo->getTurno()->setId($linha['turn_id']);
            $custo->getTurno()->setDescricao($linha['turn_descricao']);
            $custo->setInicio($linha['cure_inicio']);
            $custo->setFim($linha['cure_fim']);
            $lista[] = $custo;
        }
        return $lista;
        
    }
    public function inserir(Custo $custo){
        $valor = $custo->getValor();
        $idUnidade = $custo->getUnidade()->getId();
        $idTurno = $custo->getTurno()->getId();
        
        $timeInicio = strtotime($custo->getInicio());
        $timeFim = strtotime($custo->getFim());
        
        if($timeInicio > $timeFim){
            return false;
        }
        
        $inicio = date('Y-m-d', $timeInicio);
        $fim = date('Y-m-d', $timeFim);
        return $this->getConexao()->exec("INSERT into custo_refeicao(cure_valor, unid_id, turn_id, cure_inicio, cure_fim) 
                VALUES($valor, $idUnidade, $idTurno, '$inicio', '$fim')");
        
    }
    
    
    
    public function excluirCusto(Custo $custo){
        $idCusto = $custo->getId();
        $sql = "DELETE FROM custo_refeicao WHERE cure_id = $idCusto";
        return $this->getConexao()->exec($sql);
    }

    
    
}

?>