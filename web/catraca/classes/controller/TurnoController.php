<?php

class TurnoController{
	
	public static function main(){
	
		$controller = new TurnoController();
		$controller->listagem();
		$controller->cadastro();
		$controller->delete();
	
	
	
	}
	
	public function detalhe(Unidade $unidade = null){
	
	
	}
	/**
	 * @var TurnoDAO
	 */
	private $dao;
	
	/**
	 * @var UnidadeView
	 */
	private $view;
	public function TurnoController(){
		$this->dao = new TurnoDAO();
		$this->view = new TurnoView();
	}
	
	
	/**
	 * Faz a listagem. 
	 */
	public function listagem(){
	
		$lista = $this->dao->retornaLista();
		$this->view->mostraLista($lista);
	
	
	}
	public function cadastro(){
		$this->view->mostraFormulario();
	
		if(isset($_POST['turno_descricao']) && isset($_POST['turno_hora_inicial'])&& isset($_POST['turno_hora_final']))
			if($_POST['turno_descricao'] != null && $_POST['turno_descricao'] != "")
			{
				$turno = new Turno();
				$turno->setDescricao($_POST['turno_descricao']);
				$turno->setHoraInicial($_POST['turno_hora_inicial']);
				$turno->setHoraFinal($_POST['turno_hora_final']);
				
				if($this->dao->inserir($turno))
					$this->view->cadastroSucesso();
				else
					$this->view->cadastroFracasso();
				echo '<meta http-equiv="refresh" content="2; url=/interface/index.php?pagina=turno">';
					
			}
	}
	
	public function delete(){
	
		if(isset($_GET['delete_turno'])){
			if(isset($_GET['turn_id'])){
				if(is_int(intval($_GET['turn_id']))){
					$id = intval($_GET['turn_id']);
					$turno = new Turno();
					$turno->setId($id);
	
					if($this->dao->deletarUnidade($unidade))
						$this->view->deleteSucesso();
					else
						$this->view->deleteFracasso();
	
				}
			}
			echo '<meta http-equiv="refresh" content="2; url=/interface/index.php?pagina=turno">';
	
		}
	}
	
	
	
	
}