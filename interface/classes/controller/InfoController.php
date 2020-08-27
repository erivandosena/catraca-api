<?php
class InfoController{
	
	
	private $view;
	public static function main($nivelDeAcesso){
	
		switch ($nivelDeAcesso){
			case Sessao::NIVEL_SUPER:
				$controller = new InfoController();
				$controller->telaInfo();
				break;
			case Sessao::NIVEL_ADMIN:
				$controller = new InfoController();
				$controller->telaInfo();
				break;
			case Sessao::NIVEL_GUICHE:
				$controller = new InfoController();
				$controller->telaInfo();
				break;
			case Sessao::NIVEL_CADASTRO:
				$controller = new InfoController();
				$controller->telaInfo();
				break;
			case Sessao::NIVEL_CATRACA_VIRTUAL:
				$controller = new InfoController();
				$controller->telaInfo();
				break;
			default:
				UsuarioController::main ( $nivelDeAcesso );
				break;
		}
	
	
	}	
	
	public function telaInfo(){
		
		echo '<div class="conteudo"> <div class = "simpleTabs">
		        <ul class = "simpleTabsNavigation">
		
					<li><a href="#">Informações</a></li>

			
		        </ul>
		        <div class = "simpleTabsContent">';
		
		$this->mostrarDados();
		echo '	</div>
		
		
		    </div></div>';
		
	}
	
	public function mostrarDados(){

		
		
		
		
		$dao = new DAO();
		
		//Total de cartões cadastrados.
		$sql = "SELECT sum(1) as soma FROM cartao";

		$result = $dao->getConexao()->query($sql);
		foreach($result as $linha){
			
			echo '<p>Cartões Cadastrados ' .$linha['soma'].'</p>';
				
		}
		
		
		//Total de cartões cadastrados não avulsos.
		$sql = "SELECT sum(1) as soma FROM cartao INNER JOIN vinculo ON cartao.cart_id = vinculo.cart_id 
				WHERE vinculo.vinc_avulso = 'FALSE'";
		
		$result = $dao->getConexao()->query($sql);
		foreach($result as $linha){
			$cadastradosProprios = $linha['soma'];
			echo '<p>Cartões não avulsos ' .$linha['soma'].'</p>';
		
		}
		
		//Total de cartões com vinculo ativo. 
		$dataTimeAtual = date ( "Y-m-d G:i:s" );
		$sql =  "SELECT sum(1) as soma FROM usuario INNER JOIN vinculo
		ON vinculo.usua_id = usuario.usua_id
		LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
		LEFT JOIN tipo ON cartao.tipo_id = tipo.tipo_id
		WHERE '$dataTimeAtual' BETWEEN vinc_inicio AND vinc_fim";
		
		

		$result = $dao->getConexao()->query($sql);
		foreach($result as $linha){
		
			echo '<p>Cartões com validade ativa ' .$linha['soma'].'</p>';
		
		}
		
		//Total de cartões com vinculo ativo Próprios. 
		$dataTimeAtual = date ( "Y-m-d G:i:s" );
		$sql =  "SELECT sum(1) as soma FROM usuario INNER JOIN vinculo
		ON vinculo.usua_id = usuario.usua_id
		LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
		LEFT JOIN tipo ON cartao.tipo_id = tipo.tipo_id
		WHERE '$dataTimeAtual' BETWEEN vinc_inicio AND vinc_fim AND vinc_avulso = 'FALSE'";
		
	
		$result = $dao->getConexao()->query($sql);
		foreach($result as $linha){
		
			echo '<p>Cartões próprios com validade ativa ' .$linha['soma'].'</p>';
		
		}
		

		$sql =  "SELECT cart_id FROM registro GROUP BY cart_id";
		
		
		$result = $dao->getConexao()->query($sql);
		$i = 0;
		foreach($result as $linha){
			$i++;
			
		}
		
		echo '<p>Cartões usados pelo menos uma vez no RU: ' .$i.'</p>';
		echo '<p>Cartões próprios que nunca foram utilizados:'.($cadastradosProprios-$i).' </p>';
		
	}
		
		
	
	
}

?>