<?php
/**
 *
 * @author Jefferson Uchoa Ponte
 *
 */

class AuditoriaController{
	

	public static function main($nivelDeAcesso){
	
		switch ($nivelDeAcesso){
			case Sessao::NIVEL_SUPER:
				$controller = new AuditoriaController();
				$controller->telaAuditoria();		
				break;
			default:
				UsuarioController::main ( $nivelDeAcesso );
				break;
		}
	}
	
	public function telaAuditoria(){
		
		echo '<div class="doze colunas borda">
				<div class="resolucao config">';
		echo '<form action="">
				<input type="datetime-local" name="data_inicial" />
				<input type="datetime-local" name="data_final" />
				<input type="text" name="id_usuario" />
				<input type="hidden" name="pagina" value="auditoria" />
				<input type="submit" />
				</form>';
		if(isset($_GET['data_inicial']) && isset($_GET['data_final']) && isset($_GET['id_usuario'])){
			$dataInicial = $_GET['data_inicial'];
			$dataFinal = $_GET['data_final'];
			$nome = $_GET['id_usuario'];
			
			$dao = new DAO();
			Auditoria::mostrar($dao->getConexao(), $nome, $dataInicial, $dataFinal);
			
		}else{
		

			$dao = new DAO();
			Auditoria::mostrar($dao->getConexao());
		}
		echo '</div>';
		echo '</div>';
	}
	
	
}