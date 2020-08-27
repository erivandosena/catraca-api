<?php
class RelatorioTurnoController{
	private $view;
	private $dao;
	public static function main($nivelDeAcesso) {
		switch ($nivelDeAcesso) {
			case Sessao::NIVEL_SUPER :
				$controller = new RelatorioTurnoController ();
				$controller->relatorio ();
				break;
			case Sessao::NIVEL_ADMIN :
				$controller = new RelatorioTurnoController ();
				$controller->relatorio ();
				break;
			default :
				UsuarioController::main ( $nivelDeAcesso );
				break;
		}
	}
	public function RelatorioTurnoController(){
		$this->view = new RelatorioTurnoView();
		$this->dao = new UnidadeDAO ();
		
	}
	public function relatorio() {
		
		$listaDeUnidades = $this->dao->retornaLista ();

		
		$this->view->mostrarFormulario($listaDeUnidades);
				
		
		if (isset ( $_GET ['gerar'] )) {
			$dados = $this->gerarDados( $_GET ['unidade'], $_GET ['data_inicial'], $_GET ['data_final']);
			$this->mostraRelatorio($dados);
		}
	}
	
	private $strUnidade;
	private $data1;
	private $data2;
	
	public function gerarDados($idUnidade = NULL, $data1 = null, $data2 = null){

		if($idUnidade == NULL){
			$idUnidade = 1;
		}
		if ($data1 == null){
			$data1 = date ( 'Y-m-d' );
		}
		if ($data2 == null){
			$data2 = date ( 'Y-m-d' );
		}
		$this->data1 = $data1;
		$this->data2 = $data2;
		
		$listaDeDatas = $this->intervaloDeDatas($data1, $data2);
		$turnoDao = new TurnoDAO ( $this->dao->getConexao () );
		$listaDeTurnos = $turnoDao->retornaLista ();
		
		
		
		$strFiltroUnidade = "";
		$this->strUnidade =  'Todos os restaurantes ';
		if($idUnidade != NULL){
				
			$idUnidade = intval($idUnidade);
			$strFiltroUnidade  = " AND catraca_unidade.unid_id = $idUnidade";
			
			$unidade = new Unidade();
			$unidade->setId($idUnidade);
			$this->dao->preenchePorId($unidade);
			$this->strUnidade =  $unidade->getNome();
		}
		

		$matriz = array();
		foreach($listaDeTurnos as $turno){
			$matriz[$turno->getDescricao()] = array();
			foreach($listaDeDatas as $data){
				
				$dataInicial = $data . ' '.$turno->getHoraInicial();
				$dataFinal = $data . ' '.$turno->getHoraFinal();
				$result = $this->dao->getConexao()->query("SELECT * FROM registro
				INNER JOIN vinculo ON vinculo.vinc_id = registro.vinc_id
				INNER JOIN usuario ON vinculo.usua_id = usuario.usua_id
				INNER JOIN catraca_unidade ON registro.catr_id = catraca_unidade.catr_id
				WHERE (regi_data BETWEEN '$dataInicial' AND '$dataFinal') $strFiltroUnidade");
				foreach($result as $linha){
						
					$info = $this->retornaCurso($linha['id_base_externa']);
					if(!isset($matriz[$turno->getDescricao()][$info['curso'].' - '.$info['turno']])){
						$matriz[$turno->getDescricao()][$info['curso'].' - '.$info['turno']] = 0;
					}
					$matriz[$turno->getDescricao()][$info['curso'].' - '.$info['turno']]++;
					
					if(!isset($matriz[$turno->getDescricao()]['Total - '.$info['turno']])){
						$matriz[$turno->getDescricao()]['Total - '.$info['turno']] = 0;
					}
					$matriz[$turno->getDescricao()]['Total - '.$info['turno']]++;
					
				}
				
			}	
			
		}		
		return $matriz;
		
	}
	public $contante = 0;
	public function retornaCurso($id){
	    $info = array();
		$sql2 = "SELECT * FROM vw_usuarios_catraca WHERE id_usuario = $id LIMIT 1";
		$result = $this->dao->getConexao()->query($sql2);
		
		foreach ($result as $linha){
			$info['curso'] = "-";
			$info['turno'] = " ";
			if($linha['nome_curso'] != null){
				$info['curso'] = $linha['nome_curso'];
				$info['turno'] = $linha['turno'];
				
				if(!$linha['turno']){
					$info['turno'] = " Não Informado ";
				}
				
				
			}
			else{
				$info['curso'] = $linha['tipo_usuario'];
				$info['turno'] = " Não Informado ";
			}
			return $info;
		}
	}
	/**
	 * @param string $data1
	 * @param string $data2
	 * 
	 * @return array $listaDeDatas
	 */
	public function intervaloDeDatas($data1, $data2){
		$dateStart = new DateTime ( $data1 );
		$dateEnd = new DateTime ( $data2 );
		$listaDeDatas = array();
		while ( $dateStart <= $dateEnd ) {
			$listaDeDatas [] = $dateStart->format ( 'Y-m-d' );
			$dateStart = $dateStart->modify ( '+1day' );
		}
		return $listaDeDatas;
	
	}

	
	public function mostraRelatorio($dados){
		foreach($dados as $turno => $vetor){
		

			echo '<div class=" doze colunas borda relatorio">';
			echo '<h2>UNILAB<small class="fim">Universidade da Integração Internacional da Lusofonia Afro-Brasileira</small></h2>
				
				<hr class="um">
				<h3>'.$this->strUnidade.'</h3>
				<span>De '. date ( 'd/m/Y', strtotime ( $this->data1 ) ) . ' a ' . date ( 'd/m/Y', strtotime ( $this->data2) ) .'</span>
				<span>'.$turno.'</span>
				<hr class="dois">
			
					<table class="tabela">
						<thead>
							<tr>
								<th>Curso - Turno</th>
								<th>Número de Pratos</th>
							</tr>
						<thead>
						<tbody>';
			
			$totalGeral = 0;
			foreach($vetor as $chave => $valor){
				if(!(substr($chave, 0, 5) == "Total")){
					echo '<tr><td>'.$chave.'</td><td>'.$valor.'</td></tr>';
					$totalGeral += $valor;
				}
				
			}
			foreach($vetor as $chave => $valor){
				
				if(substr($chave, 0, 5) == "Total"){
					echo '<tr><td>'.$chave.'</td><td>'.$valor.'</td></tr>';
				}
			
			}
			
			
			echo '<tr><td>Total Geral</td><td>'.$totalGeral.'</td></tr>';
			echo'			</tbody>
					</table>';
			
			echo'<div class="doze colunas relatorio-rodape">
			<span>CATRACA | Copyright © 2015 - DTI</span>
			<span>Relatório Emitido em: '. date ( 'd/m/Y H:i:s' ).'</span>';
			echo '	</div>
				</div>';
			
		}
	}

		
		
}

?>