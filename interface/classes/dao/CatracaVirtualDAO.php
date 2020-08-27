<?php

class CatracaVirtualDAO extends DAO{
	
	
	/**
	 * 1º
	 * 
	 * @param Cartao $cartao
	 * 
	 * Temos que saber se esse indivíduo possui um cartão válido. Para isso é necessário que esteja
	 * cadastrado e que não tenha comido neste turno. Ou que tenha comido de acordo com a quantidade
	 * permitida para este cartão.
	 * 
	 */
	
	public function verificaVinculo(Vinculo $vinculo){
	
		$numero = $vinculo->getCartao()->getNumero();
		$data = date ( "Y-m-d G:i:s" );
		
		$sql = "SELECT * FROM cartao 
				INNER JOIN vinculo
				ON cartao.cart_id = vinculo.cart_id
				INNER JOIN vinculo_tipo ON vinculo_tipo.vinc_id = vinculo.vinc_id
				INNER JOIN tipo ON tipo.tipo_id = vinculo_tipo.tipo_id
				INNER JOIN usuario on vinculo.usua_id = usuario.usua_id
				WHERE ('$data' BETWEEN vinculo.vinc_inicio AND vinculo.vinc_fim)
				AND (cartao.cart_numero =:numero)";
		
		
		try{
			$stmt = $this->getConexao()->prepare($sql);
			$stmt->bindParam(":numero", $numero, PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}catch (PDOException $e){
			echo '{"erro":{"text":'. $e->getMessage() .'}}';
		}
		foreach($result as $linha){
			$usuario = new Usuario();
			$vinculo->setResponsavel($usuario);
			$vinculo->getResponsavel()->setNome($linha['usua_nome']);
			// INCLUSAO PARA ENVIO DE PUSH
			$vinculo->getResponsavel()->setId($linha['usua_id']);
			// INCLUSAO PARA ENVIO DE PUSH
			$vinculo->setFinalValidade($linha['vinc_fim']);
			$vinculo->getResponsavel()->setIdBaseExterna($linha['id_base_externa']);
			$vinculo->getCartao()->setCreditos($linha['cart_creditos']);
			$vinculo->getCartao()->setId($linha['cart_id']);
			$vinculo->getCartao()->getTipo()->setNome($linha ['tipo_nome']);
			$vinculo->getCartao()->getTipo()->setValorCobrado($linha['tipo_valor']);
			$vinculo->getCartao()->setNumero($linha ['cart_numero']);
			$vinculo->getCartao()->setId($linha['cart_id']);
			$vinculo->setQuantidadeDeAlimentosPorTurno($linha['vinc_refeicoes']);
			$vinculo->setId($linha['vinc_id']);
			$vinculo->setAvulso($linha['vinc_avulso']);
			$vinculo->setDescricao($linha['vinc_descricao']);
			return true;
		}
		return false;
	}

	public function retornaTurnoAtual(){
		
		$data = date ( "Y-m-d G:i:s" );
		$selectTurno = "Select * FROM turno WHERE '$data' BETWEEN
		turno.turn_hora_inicio AND turno.turn_hora_fim";
		$result = $this->getConexao()->query($selectTurno);
		foreach($result as $linha){
			$turno = new Turno();
			$turno->setHoraFinal($linha['turn_hora_fim']);
			$turno->setHoraInicial($linha['turn_hora_inicio']);
			return $turno;
		}
		return false;
		
	}
	
	
	/**
	 * 
	 * @param Vinculo $vinculo
	 * @param Turno $turnoAtual
	 * @return boolean
	 */
	public function podeContinuarComendo(Vinculo $vinculo, Turno $turnoAtual){
		$horaInicial = date('Y-m-d').' '.$turnoAtual->getHoraInicial();
		$horaFinal = date('Y-m-d').' '.$turnoAtual->getHoraFinal();
		$idCartao = $vinculo->getCartao()->getId(); 
		$quantidadePermitida = $vinculo->getQuantidadeDeAlimentosPorTurno();
		
		$numero = $vinculo->getCartao()->getNumero();
		$data = date ( "Y-m-d G:i:s" );
		
		$sql = "SELECT * FROM registro
				WHERE(registro.regi_data BETWEEN '$horaInicial' AND '$horaFinal')
				AND (registro.cart_id = $idCartao)
				ORDER BY registro.regi_id DESC
				LIMIT $quantidadePermitida;
				";
		
		try{
			$stmt = $this->getConexao()->prepare($sql);
// 			$stmt->bindParam(":numero", $numero, PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}catch (PDOException $e){
			echo '{"erro":{"text":'. $e->getMessage() .'}}';
		}
		$i = 0;
		foreach($result as $linha){
			$i++;
		}
		$vinculo->setRefeicoesRestantes($quantidadePermitida - $i);
		if($i < $quantidadePermitida){
			return true;
		}
		return false;
	}
	

	
	
	public function vinculoEhIsento(Vinculo $vinculo){
		$dataTimeAtual = date ( "Y-m-d G:i:s" );
		$idVinculo = $vinculo->getId();
		$sql = 	"SELECT * FROM vinculo
		LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
		INNER JOIN isencao ON cartao.cart_id = isencao.cart_id
		WHERE (vinculo.vinc_id = $idVinculo) AND  ('$dataTimeAtual' < isencao.isen_fim);";
		$result = $this->getConexao ()->query ($sql);

		foreach($result as $linha){
			if(isset($linha['isen_id'])){
				$vinculo->setIsencao(new Isencao());
				$vinculo->setIsento(true);
				$vinculo->getIsencao()->setId($linha['isen_id']);
				$vinculo->getIsencao()->setDataDeInicio($linha['isen_inicio']);
				$vinculo->getIsencao()->setDataFinal($linha['isen_fim']);
				return true;	
			}
		}
		return false;
	}
	


	
}


?>