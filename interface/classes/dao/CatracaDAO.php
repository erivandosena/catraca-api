<?php

class CatracaDAO extends DAO
{

    public function existe(Catraca $catraca)
    {
        $nomeCatraca = $catraca->getNome();
        $sql1 = "SELECT * FROM catraca WHERE catr_nome = '$nomeCatraca' LIMIT 1";
        $result = $this->getConexao()->query($sql1);
        foreach ($result as $linha) {
            return true;
        }
        return false;
    }

    public function inserir(Catraca $catraca)
    {
        $nomeCatraca = $catraca->getNome();
        $sql = "INSERT INTO catraca (catr_nome) VALUES ('$nomeCatraca')";
        return $this->getConexao()->exec($sql);
    }

    public function lista()
    {
        $lista = array();
        $sql = "SELECT *, catraca.catr_id as
					catraca_id
					 FROM catraca
					INNER JOIN catraca_unidade
					ON catraca.catr_id = catraca_unidade.catr_id
					INNER JOIN unidade
					ON unidade.unid_id = catraca_unidade.unid_id
					ORDER BY catraca.catr_id ASC
					";
        
        foreach ($this->getConexao()->query($sql) as $linha) {
            $catraca = new Catraca();
            $catraca->setNome($linha['catr_nome']);
            $catraca->setOperacao($linha['catr_operacao']);
            $catraca->setTempoDeGiro($linha['catr_tempo_giro']);
            $catraca->setIp($linha['catr_ip']);
            $catraca->setId($linha['catraca_id']);
            $catraca->setMacLan($linha['catr_mac_lan']);
            $catraca->setMacWlan($linha['catr_mac_wlan']);
            $catraca->setInterfaceRede($linha['catr_interface_rede']);
            $catraca->setUnidade(new Unidade());
            $catraca->getUnidade()->setNome($linha['unid_nome']);
            $catraca->setFinanceiro($linha['catr_financeiro']);
            $lista[] = $catraca;
        }
        return $lista;
    }
    public function preencheCatracaPorId(Catraca $catraca){
        $idCatraca = $catraca->getId();
        $sql = "
				SELECT *, catraca.catr_id as catraca_id FROM catraca
		LEFT JOIN catraca_unidade
		ON catraca.catr_id = catraca_unidade.catr_id
		LEFT JOIN unidade
		ON unidade.unid_id = catraca_unidade.unid_id
		WHERE catraca.catr_id = $idCatraca";
        foreach ( $this->getConexao ()->query ( $sql ) as $linha ) {
            $catraca->setNome ( $linha ['catr_nome'] );
            $catraca->setId ( $linha ['catraca_id'] );
            $catraca->setOperacao($linha['catr_operacao']);
            $catraca->setTempoDeGiro($linha['catr_tempo_giro']);
            $catraca->setIp($linha['catr_ip']);
            $catraca->setInterfaceRede($linha['catr_interface_rede']);
            $catraca->setFinanceiro($linha['catr_financeiro']);
            
            $catraca->setUnidade(new Unidade());
            $catraca->getUnidade()->setNome($linha['unid_nome']);
            $catraca->getUnidade()->setId($linha['unid_id']);
            
        }
        
    }
    /**
     *
     * @param Catraca $catraca
     */
    public function atualizarCatraca(Catraca $catraca){
        $giro = $catraca->getTempodeGiro();
        $id = $catraca->getId();
        $nomeCatraca = $catraca->getNome();
        $operacao = $catraca->getOperacao();
        $idUnidade= $catraca->getUnidade()->getId();
        $interface = $catraca->getInterfaceRede();
        
        if($catraca->financeiroAtivo()){
            $financeiro = 'TRUE';
        }else{
            $financeiro = 'FALSE';
        }
        
        $this->getConexao()->beginTransaction();
        $sqlUpdate = "UPDATE catraca SET catr_tempo_giro = $giro,
						catr_operacao = $operacao,
						catr_nome = '$nomeCatraca',
						catr_interface_rede = '$interface',
						catr_financeiro = '$financeiro'
						WHERE catr_id = $id";
        
        if(!$this->getConexao()->exec($sqlUpdate))
        {
            $this->getConexao()->rollBack();
            return false;
        }
        $this->getConexao()->exec("DELETE FROM catraca_unidade WHERE catr_id = $id");
        
        if(!$this->getConexao()->exec("INSERT into catraca_unidade(catr_id, unid_id) VALUES($id, $idUnidade)"))
        {
            $this->getConexao()->rollBack();
            return false;
        }
        
        $this->getConexao()->commit();
        return true;
        
        
        
    }
}

?>