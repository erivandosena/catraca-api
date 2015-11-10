<?php
class CartaoDAO extends DAO{

	public function retornaTipos(){
		$lista = array();
		$result = $this->getConexao()->query("SELECT * FROM tipo");
		
		foreach ($result as $linha){
			$tipo = new Tipo();
			$tipo->setId($linha['tipo_id']);
			$tipo->setNome($linha['tipo_nome']);
			
				
			$lista[] = $tipo;
		}
		return $lista;
		
	}
	public function retornaLista(){
		$lista = array();
		$result = $this->getConexao()->query("SELECT * FROM cartao INNER JOIN tipo ON cartao.tipo_id = tipo.tipo_id LIMIT 100");

		foreach ($result as $linha){
			$cartao = new Cartao();
			$cartao->setId($linha['cart_id']);
			$cartao->setCreditos($linha['cart_creditos']);
			$cartao->setNumero($linha['cart_numero']);
			$tipo = new Tipo();
			$tipo->setId($linha['tipo_id']);
			$tipo->setNome($linha['tipo_nome']);
			$tipo->setValorCobrado($linha['tipo_valor']);
			$cartao->setTipo($tipo);
			
			$lista[] = $cartao;
		}
		return $lista;
	}

	

	public function inserir(Cartao $cartao){
		
		$numero = $cartao->getNumero();
		$idTipo = $cartao->getTipo()->getId();
		
		if($this->getConexao()->query("INSERT INTO cartao(cart_numero, cart_creditos, tipo_id) VALUES($numero, 0, $idTipo)"))
			return true;
		return false;
		
		
	}
	public function deletarUnidade(Unidade $unidade){
		$id = $unidade->getId();
		$sql = "DELETE FROM unidade WHERE unid_id = $id";
		if($this->getConexao()->query($sql))
			return true;
		return false;
	}
	
	
	
	
}


?>