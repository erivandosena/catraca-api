<?php 
class MensagemDAO extends DAO{
    
    public function lista(){
        $lista = array();
        $result = $this->getConexao()->query("SELECT * FROM unidade
				INNER JOIN catraca_unidade ON catraca_unidade.unid_id = unidade.unid_id
				INNER JOIN catraca ON catraca.catr_id = catraca_unidade.catr_id
				INNER JOIN mensagem ON catraca.catr_id = mensagem.catr_id");
        
        foreach ($result as $linha){
            $mensagem = new Mensagem();
            $mensagem->setId($linha['mens_id']);
            $mensagem->setMensagem1($linha['mens_institucional1']);
            $mensagem->setMensagem2($linha['mens_institucional2']);
            $mensagem->setMensagem3($linha['mens_institucional3']);
            $mensagem->setMensagem4($linha['mens_institucional4']);
            $mensagem->getCatraca()->setId($linha['catr_id']);
            $mensagem->getCatraca()->setNome($linha['catr_nome']);
            $mensagem->getCatraca()->getUnidade()->setNome($linha['unid_nome']);     
            $mensagem->getCatraca()->getUnidade()->setId($linha['unid_id']);     
            $lista[] = $mensagem;
            
        }
        return $lista;
    }
    public function atualizaOuInsere(Mensagem $mensagem){
        $idCatraca = $mensagem->getCatraca()->getId();
        $mensagem1 = $mensagem->getMensagem1();
        $mensagem2 = $mensagem->getMensagem2();
        $mensagem3 = $mensagem->getMensagem3();
        $mensagem4 = $mensagem->getMensagem4();
        $result = $this->getConexao()->query("SELECT * FROM mensagem WHERE catr_id = $idCatraca");
        foreach ($result as $linha){
            $sql = "UPDATE mensagem SET
						mens_institucional1 = '$mensagem1',
						mens_institucional2 = '$mensagem2',
						mens_institucional3 = '$mensagem3',
						mens_institucional4 = '$mensagem4'
						WHERE catr_id = $idCatraca";

            return $this->getConexao()->exec($sql);            
        }
        $sql = "INSERT INTO mensagem (
						mens_institucional1, mens_institucional2, mens_institucional3, mens_institucional4, catr_id)
						VALUES ('$mensagem1','$mensagem2','$mensagem3','$mensagem4',$idCatraca)";
       return $this->getConexao()->exec($sql);
        
    }
}

?>