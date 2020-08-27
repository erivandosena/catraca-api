<?php
class RelatorioController {
	private $view;
	private $dao;
	public static function main($nivelDeAcesso) {
		switch ($nivelDeAcesso) {
			case Sessao::NIVEL_SUPER :
				$controller = new RelatorioController ();
				$controller->relatorio ();
				break;
			case Sessao::NIVEL_ADMIN :
				$controller = new RelatorioController ();
				$controller->relatorio ();
				break;

			case Sessao::NIVEL_POLIVALENTE:
				$controller = new RelatorioController ();
				$controller->relatorio ();
				break;
			case Sessao::NIVEL_CATRACA_VIRTUAL:
				$controller = new RelatorioController ();
				$controller->relatorio ();
				break;
			case Sessao::NIVEL_RELATORIO:
				$controller = new RelatorioController ();
				$controller->relatorio ();
				break;
			case Sessao::NIVEL_CATRACA_VIRTUAL_ORFA:
				$controller = new RelatorioController ();
				$controller->relatorio ();
				break;
			default :
				UsuarioController::main ( $nivelDeAcesso );
				break;
		}
	}
	public function relatorio() {
		$this->dao = new UnidadeDAO ();
		$listaDeUnidades = $this->dao->retornaLista ();
		
		echo '<div class="doze colunas borda relatorio">
									<form action="" class="formulario sequencial">									
											<div id="data">
												<label for="opcoes-1">
													<object class="rotulo texto-preto">Unidade Acadêmica: </object>
													<select name="unidade" id="unidade" class="texto-preto">';
		foreach ( $listaDeUnidades as $unidade ) {
			echo '<option value="' . $unidade->getId () . '">' . $unidade->getNome () . '</option>';
		}
		//echo '<option value="">Todos as Unidades</option>';
		echo '								            										            
												    </select>
												</label><br>
												<label for="data_inicial" class="texto-preto">
												    Data Inicial: <input id="data_inicial" type="date" name="data_inicial"/>
												</label><br>
												<label for="data_final" class="texto-preto">
												    Data Final: <input id="data_final" type="date" name="data_final"/>
												</label><br>
												<label for="tipo_de_relatorio">
													Tipo De Relatório
												</label>
												<select id="tipo_de_relatorio" name="tipo_de_relatorio">';
		
		echo '										
													<option value="3">Relação Pratos e Valores</option>
													<option value="1">Pratos Consumidos</option>
 													<option value="2">Valores Arrecadados</option>';
		echo '										
												</select>
												<input type="hidden" name="pagina" value="relatorio" />
												<input  type="submit"  name="gerar" value="Gerar"/>
											</div>									    							
									</form>																			
									</div>';
		
		if (isset ( $_GET ['gerar'] )) {
			switch ($_GET ['tipo_de_relatorio']) {
				case "1" :
					$this->gerarPratosConsumidos ( $_GET ['unidade'], $_GET ['data_inicial'], $_GET ['data_final'] );
					break;
				case "2" :
					$this->geraValoresArrecadados ( $_GET ['unidade'], $_GET ['data_inicial'], $_GET ['data_final'] );
					break;
				case "3" :
					$this->geraRelacaoPratosValores ( $_GET ['unidade'], $_GET ['data_inicial'], $_GET ['data_final'] );
					break;
				default :
					$this->geraRelacaoPratosValores ( $_GET ['unidade'], $_GET ['data_inicial'], $_GET ['data_final'] );
					break;
			}
		}else{
			$this->geraRelacaoPratosValores ();
				
		}
	}
	public function gerarPratosConsumidos($idUnidade = NULL, $dateStart = null, $dataEnd = null) {

		if ($dateStart == null)
			$dateStart = date ( 'Y-m-d' );
		if ($dataEnd == null)
			$dataEnd = date ( 'Y-m-d' );
		
		
		$strFiltroUnidade = "";
		$strUnidade =  'Todos os restaurantes ';
		if($idUnidade != NULL){
			
			$idUnidade = intval($idUnidade);
			$strFiltroUnidade  = " AND catraca_unidade.unid_id = $idUnidade";
			$unidadeDao = new UnidadeDAO($this->dao->getConexao());
			$unidade = new Unidade();
			$unidade->setId($idUnidade);
			$unidadeDao->preenchePorId($unidade);
			$strUnidade =  $unidade->getNome();
		}
		
		$dao = new TipoDAO ();
		$tipos = $dao->retornaLista ();
		
		$dateStart = new DateTime ( $dateStart );
		$dateEnd = new DateTime ( $dataEnd );
		
		// Prints days according to the interval
		$dateRange = array ();
		while ( $dateStart <= $dateEnd ) {
			$listaDeDatas [] = $dateStart->format ( 'Y-m-d' );
			$dateStart = $dateStart->modify ( '+1day' );
		}
		
		$listaDeDados = array ();
		foreach ( $listaDeDatas as $data ) {
			$total = 0;
			foreach ( $tipos as $tipo ) {
				
				$dataInicial = $data . ' 00:00:00';
				$dataFinal = $data . ' 23:59:59';
				$tipoId = $tipo->getId ();
				
				$sql = "SELECT sum(1) valor FROM registro
				INNER JOIN vinculo ON vinculo.vinc_id = registro.vinc_id
				INNER JOIN vinculo_tipo ON vinculo.vinc_id = vinculo_tipo.vinc_id
				INNER JOIN catraca ON registro.catr_id = catraca.catr_id
				INNER JOIN catraca_unidade ON catraca.catr_id = catraca_unidade.catr_id
				WHERE (regi_data BETWEEN '$dataInicial' AND '$dataFinal') AND vinculo_tipo.tipo_id =  $tipoId
				$strFiltroUnidade;";
				foreach ( $dao->getConexao ()->query ( $sql ) as $linha ) {
					$valor = $linha ['valor'];
				}
				
				if ($valor)
					$listaDeDados [$data] [$tipo->getId ()] = $valor;
				else
					$listaDeDados [$data] [$tipo->getId ()] = 0;
				
				$total += intval ( $valor );
			}
			$listaDeDados [$data] ['total'] = $total;
		}
			
		$this->mostraListaDeDadosPratos($listaDeDados, $strUnidade.' - Todos os Turnos', $tipos, $listaDeDatas );
		
		$listaDeDados = array ();
		$turnoDao = new TurnoDAO ( $this->dao->getConexao () );
		$listaDeTurnos = $turnoDao->retornaLista ();
		foreach ( $listaDeTurnos as $turno ) {
			foreach ( $listaDeDatas as $data ) {
				$total = 0;
				foreach ( $tipos as $tipo ) {
					
					$dataInicial = $data . ' ' . $turno->getHoraInicial ();
					$dataFinal = $data . ' ' . $turno->getHoraFinal ();
					$tipoId = $tipo->getId ();
					
					$sql = "SELECT sum(1) valor FROM registro
					INNER JOIN vinculo ON vinculo.vinc_id = registro.vinc_id
					INNER JOIN vinculo_tipo ON vinculo.vinc_id = vinculo_tipo.vinc_id
					INNER JOIN catraca ON registro.catr_id = catraca.catr_id
					INNER JOIN catraca_unidade ON catraca.catr_id = catraca_unidade.catr_id
					WHERE (regi_data BETWEEN '$dataInicial' AND '$dataFinal') AND vinculo_tipo.tipo_id =  $tipoId
					$strFiltroUnidade;";
					foreach ( $dao->getConexao ()->query ( $sql ) as $linha ) {
						$valor = $linha ['valor'];
					}
					
					if ($valor)
						$listaDeDados [$data] [$tipo->getId ()] = $valor;
					else
						$listaDeDados [$data] [$tipo->getId ()] = 0;
					
					$total += intval ( $valor );
				}
				$listaDeDados [$data] ['total'] = $total;
			}
			
			$this->mostraListaDeDadosPratos($listaDeDados, $strUnidade.' - turno: ' . $turno->getDescricao () . ' - entre: ' . $turno->getHoraInicial () . ' e ' . $turno->getHoraFinal (), $tipos, $listaDeDatas );
		}
	}
	public function geraValoresArrecadados($idUnidade = NULL, $dateStart = null, $dataEnd = null) {
		if ($dateStart == null)
			$dateStart = date ( 'Y-m-d' );
		if ($dataEnd == null)
			$dataEnd = date ( 'Y-m-d' );
		$idUnidade = intval ( $idUnidade );
		$dao = new TipoDAO ();
		$tipos = $dao->retornaLista ();
		
		$strFiltroUnidade = "";
		$strUnidade =  'Todos os restaurantes ';
		if($idUnidade != NULL){
				
			$idUnidade = intval($idUnidade);
			$strFiltroUnidade  = " AND catraca_unidade.unid_id = $idUnidade";
			$unidadeDao = new UnidadeDAO($this->dao->getConexao());
			$unidade = new Unidade();
			$unidade->setId($idUnidade);
			$unidadeDao->preenchePorId($unidade);
			$strUnidade =  $unidade->getNome();
		}
		
		$dateStart = new DateTime ( $dateStart );
		$dateEnd = new DateTime ( $dataEnd );
		
		// Prints days according to the interval
		$dateRange = array ();
		while ( $dateStart <= $dateEnd ) {
			$listaDeDatas [] = $dateStart->format ( 'Y-m-d' );
			$dateStart = $dateStart->modify ( '+1day' );
		}
		
		$listaDeDados = array ();
		foreach ( $listaDeDatas as $data ) {
			$total = 0;
			foreach ( $tipos as $tipo ) {
				
				$dataInicial = $data . ' 00:00:00';
				$dataFinal = $data . ' 23:59:59';
				$tipoId = $tipo->getId ();
				
				$sql = "SELECT sum(regi_valor_pago) valor FROM registro
				INNER JOIN vinculo ON vinculo.vinc_id = registro.vinc_id
				INNER JOIN vinculo_tipo ON vinculo.vinc_id = vinculo_tipo.vinc_id
				INNER JOIN catraca ON registro.catr_id = catraca.catr_id
				INNER JOIN catraca_unidade ON catraca.catr_id = catraca_unidade.catr_id
				WHERE (regi_data BETWEEN '$dataInicial' AND '$dataFinal') AND vinculo_tipo.tipo_id =  $tipoId
				$strFiltroUnidade;";
				foreach ( $dao->getConexao ()->query ( $sql ) as $linha ) {
					$valor = $linha ['valor'];
				}
				
				if ($valor)
					$listaDeDados [$data] [$tipo->getId ()] = $valor;
				else
					$listaDeDados [$data] [$tipo->getId ()] = '-';
				
				$total += floatval ( $valor );
			}
			$listaDeDados [$data] ['total'] = $total;
		}
		
		$this->mostraListaDeDados ( $listaDeDados, $strUnidade.' - Todos os Turnos', $tipos, $listaDeDatas );
		
		$listaDeDados = array ();
		$turnoDao = new TurnoDAO ( $this->dao->getConexao () );
		$listaDeTurnos = $turnoDao->retornaLista ();
		foreach ( $listaDeTurnos as $turno ) {
			foreach ( $listaDeDatas as $data ) {
				$total = 0;
				foreach ( $tipos as $tipo ) {
					
					$dataInicial = $data . ' ' . $turno->getHoraInicial ();
					$dataFinal = $data . ' ' . $turno->getHoraFinal ();
					$tipoId = $tipo->getId ();
					
					$sql = "SELECT sum(regi_valor_pago) valor FROM registro
					INNER JOIN vinculo ON vinculo.vinc_id = registro.vinc_id
					INNER JOIN vinculo_tipo ON vinculo.vinc_id = vinculo_tipo.vinc_id
					INNER JOIN catraca ON registro.catr_id = catraca.catr_id
					INNER JOIN catraca_unidade ON catraca.catr_id = catraca_unidade.catr_id
					WHERE (regi_data BETWEEN '$dataInicial' AND '$dataFinal') AND vinculo_tipo.tipo_id =  $tipoId
					$strFiltroUnidade;";
					foreach ( $dao->getConexao ()->query ( $sql ) as $linha ) {
						$valor = $linha ['valor'];
					}
					
					if ($valor)
						$listaDeDados [$data] [$tipo->getId ()] = $valor;
					else
						$listaDeDados [$data] [$tipo->getId ()] = '-';
					
					$total += intval ( $valor );
				}
				$listaDeDados [$data] ['total'] = $total;
			}
			$this->mostraListaDeDados ( $listaDeDados, $strUnidade.' - turno: ' . $turno->getDescricao () . ' - entre: ' . $turno->getHoraInicial () . ' e ' . $turno->getHoraFinal (), $tipos, $listaDeDatas );
		}
	}
	public function pegaUltimoCusto($idUnidade = null){
		
		if($idUnidade != null){
			

			$sql = "SELECT cure_valor FROM custo_refeicao
			INNER JOIN custo_unidade
			ON custo_unidade.cure_id = custo_refeicao.cure_id
			INNER JOIN unidade
			ON unidade.unid_id = custo_unidade.unid_id
			INNER JOIN catraca_unidade
			ON catraca_unidade.unid_id = unidade.unid_id
			WHERE catraca_unidade.unid_id = $idUnidade
			ORDER BY custo_unidade.cure_id DESC LIMIT 1
			";
			
			foreach($this->dao->getConexao()->query($sql) as $linha){
				return $linha['cure_valor'];
			}
			
			
		}
		
		$ultimoCusto = 0;
		foreach ( $this->dao->getConexao ()->query ( "SELECT * FROM custo_refeicao ORDER BY cure_id DESC LIMIT 1" ) as $linha ) {
			$ultimoCusto = $linha ['cure_valor'];
		}
		return $ultimoCusto;
	}
	public function geraRelacaoPratosValores($idUnidade = NULL, $data1 = null, $data2 = null) {
		
		if($idUnidade == NULL)
			$idUnidade = 1;
		if ($data1 == null)
			$data1 = date ( 'Y-m-d' );
		if ($data2 == null)
			$data2 = date ( 'Y-m-d' );
		$strFiltroUnidade = "";
		$strUnidade = "Todos os restaurantes";
		if($idUnidade != NULL){
			$idUnidade = intval($idUnidade);
			$strFiltroUnidade  = " AND catraca_unidade.unid_id = $idUnidade";
			$unidadeDao = new UnidadeDAO($this->dao->getConexao());
			$unidade = new Unidade();
			$unidade->setId($idUnidade);
			$unidadeDao->preenchePorId($unidade);
			$strUnidade =  $unidade->getNome();
			
		}
		$idUnidade = intval ( $idUnidade );
		$dao = new TipoDAO ();
		$tipos = $dao->retornaLista ();
		
		$dateStart = new DateTime ( $data1 );
		$dateEnd = new DateTime ( $data2 );
		
		// Prints days according to the interval
		$dateRange = array ();
		while ( $dateStart <= $dateEnd ) {
			$listaDeDatas [] = $dateStart->format ( 'Y-m-d' );
			$dateStart = $dateStart->modify ( '+1day' );
		}
		// Pegar ultimo custo.
		$ultimoCusto = $this->pegaUltimoCusto();
		
		
		$listaDeDados = array ();
		$totalValor = 0;
		$totalPratos = 0;
		foreach ( $tipos as $tipo ) {
			$listaDeDados [$tipo->getId ()] ['pratos'] = 0;
			$listaDeDados [$tipo->getId ()] ['valor'] = 0;
			$listaDeDados [$tipo->getId ()] ['custo'] = 0;
		}
		foreach ( $tipos as $tipo ) {
			
			foreach ( $listaDeDatas as $data ) {
				$dataInicial = $data . ' 00:00:00';
				$dataFinal = $data . ' 23:59:59';
				$tipoId = $tipo->getId ();
				$sql = "SELECT  sum(1) pratos,sum(regi_valor_pago) valor FROM registro
					INNER JOIN vinculo ON vinculo.vinc_id = registro.vinc_id
					INNER JOIN vinculo_tipo ON vinculo.vinc_id = vinculo_tipo.vinc_id
					INNER JOIN catraca ON catraca.catr_id = registro.catr_id
					INNER JOIN catraca_unidade ON catraca.catr_id = catraca_unidade.catr_id
					WHERE (regi_data BETWEEN '$dataInicial' AND '$dataFinal') AND vinculo_tipo.tipo_id =  $tipoId
				$strFiltroUnidade;";
				foreach ( $dao->getConexao ()->query ( $sql ) as $linha ) {
					//print_r($linha);
					$ultimoCusto = $this->pegaUltimoCusto($idUnidade);
					$pratos = floatval($linha ['pratos']);
					$valor = floatval($linha ['valor']);

					$listaDeDados [$tipo->getId ()] ['pratos'] += $pratos;
					$listaDeDados [$tipo->getId ()] ['valor'] += floatval($valor);
					$listaDeDados [$tipo->getId ()] ['custo'] += $ultimoCusto * $pratos;
					

					$totalValor += floatval ( $valor );
					
					$totalPratos += intval ( $pratos );
					$listaDeDados ['total'] ['pratos'] = floatval($totalPratos);
					$listaDeDados ['total'] ['valor'] = floatval($totalValor);
					$listaDeDados ['total'] ['custo'] = floatval($totalPratos * $ultimoCusto);
				}
				
				
			}
			
			
// 			$this->mostraMatriz($listaDeDados);
			
		}

		
		echo '<div class=" doze colunas borda relatorio">';		
		echo '<h2>UNILAB<small class="fim">Universidade da Integração Internacional da Lusofonia Afro-Brasileira</small></h2>
				<hr class="um">
				<h3>'.$strUnidade.'</h3>
				<span>De '. date ( 'd/m/Y', strtotime ( $data1 ) ) . ' a ' . date ( 'd/m/Y', strtotime ( $data2 ) ) .'</span>
				<span>Todos os Turnos</span>
				<hr class="dois">
				
					<table class="tabela-relatorio">
						<thead>
							<tr>
								<th>Tipo Usuário</th>
								<th>Pratos</th>
								<th>%Pratos</th>
								<th>Valores Arrecadados</th>
								<th>Valores Custo</th>
								<th>Despesa(Custo - Arrecadação)</th>
								<th>%Despesa</th>
							</tr>
						<thead>
						<tbody>
							';
		foreach ( $tipos as $tipo ) {
			
			$percentual = 0;
			if($listaDeDados ['total']['custo'] - $listaDeDados ['total']['valor'])
				$percentual = ($listaDeDados [$tipo->getId ()] ['custo'] - $listaDeDados [$tipo->getId ()] ['valor'])/($listaDeDados ['total'] ['custo'] - $listaDeDados ['total']['valor'])*100;
			$percentualPratos = 0;
			if($listaDeDados ['total']['pratos'])
				$percentualPratos = ($listaDeDados [$tipo->getId ()] ['pratos'])/($listaDeDados ['total'] ['pratos'])*100;
			
			echo'	<tr >	
						<th id="limpar">' . $tipo->getNome () . '</th>
						<td>' . $listaDeDados [$tipo->getId ()] ['pratos'] . '</td>
						<td>'.number_format ( $percentualPratos, 2, ',', '.' ).'</td>
						<td>R$ ' . number_format ( $listaDeDados [$tipo->getId ()] ['valor'], 2, ',', '.' ) . '</td>
						<td>R$ ' . number_format ( $listaDeDados [$tipo->getId ()] ['custo'], 2, ',', '.' ) . '</td>
						<td>R$ ' . number_format ( ($listaDeDados [$tipo->getId ()] ['custo'] - $listaDeDados [$tipo->getId ()] ['valor']), 2, ',', '.' ) . '</td>
						
						<td> ' . number_format ( ($percentual), 2, ',', '.' ) . '</td>
														
					</tr>';
		}		
		
		echo'		<tr id="soma">
						<th id="limpar">Somatório</th><td>' . $listaDeDados ['total'] ['pratos'] . '</td><td>-</td>
						<td>R$ ' . number_format ( $listaDeDados ['total'] ['valor'], 2, ',', '.' ) . '</td>
						<td>R$ ' . number_format ( $listaDeDados ['total'] ['custo'], 2, ',', '.' ) . '</td>
						<td>R$ ' . number_format ( ($listaDeDados ['total'] ['custo'] - $listaDeDados ['total']['valor']), 2, ',', '.' ) . '</td>
						<td>-</td>
					</tr>';
		
		echo'			</tbody>
					</table>';
					
		
		echo'<div class="doze colunas relatorio-rodape">
			<span>CATRACA | Copyright © 2015 - DTI</span>
			<span>Relatório Emitido em:'.$date = date('d-m-Y H:i:s').'</span>';
// 		echo '<a class="botao icone-printer"> Imprimir</a>';
		echo '	</div>	
				</div>';		
		
		$turnoDao = new TurnoDAO ( $this->dao->getConexao () );
		$listaDeTurnos = $turnoDao->retornaLista ();
		foreach ( $listaDeTurnos as $turno ) {
			$inicial = $turno->getHoraInicial ();
			$final = $turno->getHoraFinal ();
			
			$listaDeDados = array ();
			$totalValor = 0;
			$totalPratos = 0;
			foreach ( $tipos as $tipo ) {
				$listaDeDados [$tipo->getId ()] ['pratos'] = 0;
				$listaDeDados [$tipo->getId ()] ['valor'] = 0;
				$listaDeDados [$tipo->getId ()] ['custo'] = 0;
				
				foreach ( $listaDeDatas as $data ) {
					$dataInicial = $data . ' '.$turno->getHoraInicial();
					$dataFinal = $data . ' '.$turno->getHoraFinal();
					$tipoId = $tipo->getId ();
					$sql = "SELECT sum(1) pratos,sum(regi_valor_pago) valor  FROM registro
				INNER JOIN vinculo ON vinculo.vinc_id = registro.vinc_id
				INNER JOIN vinculo_tipo ON vinculo.vinc_id = vinculo_tipo.vinc_id
				INNER JOIN catraca ON catraca.catr_id = registro.catr_id
				INNER JOIN catraca_unidade ON catraca.catr_id = catraca_unidade.catr_id
				WHERE (regi_data BETWEEN '$dataInicial' AND '$dataFinal') AND vinculo_tipo.tipo_id =  $tipoId
				$strFiltroUnidade;";
					foreach ( $dao->getConexao ()->query ( $sql ) as $linha ) {
						$ultimoCusto = $this->pegaUltimoCusto($idUnidade);
						$pratos = $linha ['pratos'];
						$valor = $linha ['valor'];
						$listaDeDados [$tipo->getId ()] ['pratos'] += $pratos;
						$listaDeDados [$tipo->getId ()] ['valor'] += $valor;
						$listaDeDados [$tipo->getId ()] ['custo'] += $ultimoCusto * $pratos;

						$totalValor += floatval ( $valor );
						$totalPratos += intval ( $pratos );
						$listaDeDados ['total'] ['pratos'] = $totalPratos;
						$listaDeDados ['total'] ['valor'] = $totalValor;
						$listaDeDados ['total'] ['custo'] = $totalPratos * $ultimoCusto;
					}
					
				}
			}
			
		echo '<div class="doze colunas borda relatorio">
				<h2>UNILAB<small class="fim">Universidade da Integração Internacional da Lusofonia Afro-Brasileira</small></h2>
				<hr class="um">
				<h3>'.$strUnidade.'</h3>
				<span>Data: '. date ( 'd/m/Y', strtotime ( $data1 ) ) . ' e ' . date ( 'd/m/Y', strtotime ( $data2 ) ) .'</span>
				<span>Unidade Acadêmica:</span>
				<span>Turno: '.$turno->getDescricao().'</span>
				<hr class="dois">
						<table class="tabela-relatorio">
							<thead>								
									<tr>
									<th>Tipo Usuário</th>
									<th>Pratos</th>
									<th>%Pratos</th>
									<th>Valores Arrecadados</th>
									<th>Valores Custo</th>
									<th>Despesa(Custo - Arrecadação)</th>
									<th>%Despesa</th>
								
								</tr>';
		foreach ( $tipos as $tipo ) {
			$percentual = 0;
			if($listaDeDados ['total']['custo'] - $listaDeDados ['total']['valor'])
				$percentual = ($listaDeDados [$tipo->getId ()] ['custo'] - $listaDeDados [$tipo->getId ()] ['valor'])/($listaDeDados ['total'] ['custo'] - $listaDeDados ['total']['valor'])*100;
			$percentualPratos = 0;
			if($listaDeDados ['total']['pratos'])
				$percentualPratos = ($listaDeDados [$tipo->getId ()] ['pratos'])/($listaDeDados ['total'] ['pratos'])*100;
			
			
			echo '<tr >
					<th id="limpar">' . $tipo->getNome () . '</th>
					<td>' . $listaDeDados [$tipo->getId ()] ['pratos'] . '</td>
					<td>'.number_format ( $percentualPratos, 2, ',', '.' ).'</td>
					<td>R$ ' . number_format ( $listaDeDados [$tipo->getId ()] ['valor'], 2, ',', '.' ) . '</td>
					<td>R$ ' . number_format ( $listaDeDados [$tipo->getId ()] ['custo'], 2, ',', '.' ) . '</td>
					<td>R$ ' . number_format ( ($listaDeDados [$tipo->getId ()] ['custo'] - $listaDeDados [$tipo->getId ()] ['valor']), 2, ',', '.' ) . '</td>
					<td> ' . number_format ( ($percentual), 2, ',', '.' ) . '</td>
				</tr>';
		}
		echo '<tr id="soma">
			<th id="limpar">Somatário</th><td>' . $listaDeDados ['total'] ['pratos'] . '</td>';
		echo '<td>-</td>';
		echo '<td>R$ ' . number_format ( $listaDeDados ['total'] ['valor'], 2, ',', '.' ) . '</td>';
		echo '<td>R$ ' . number_format ( $listaDeDados ['total'] ['custo'], 2, ',', '.' ) . '</td>';
		echo '<td>R$ ' . number_format ( ($listaDeDados ['total'] ['custo'] - $listaDeDados ['total'] ['valor']), 2, ',', '.' ) . '</td><td>-</td>';
		echo '</tr>';
		echo '</table>';
		echo '</div>';
		}
	}
	public function mostraListaDeDados($listaDeDados, $titulo, $tipos, $listaDeDatas) {
		$subTotal = array ();
		foreach ( $tipos as $tipo ) {
			$subTotal [$tipo->getId ()] = 0;
		}
		$subTotal ['total'] = 0;
	
		
		echo '<div class=" doze colunas borda relatorio">
				<h2>UNILAB<small class="fim">Universidade da Integraçao Internacional da Lusofonia Afro-Brasileira</small></h2>	
				<hr class="um">
				<h3>'.$titulo.'</h3>
				<hr class="dois">';
		
		echo '<table class="tabela-relatorio">
				<thead>
					<tr>
						<th>Data</th>';
		foreach ( $tipos as $tipo ) {
			echo '<th>' . $tipo->getNome () . '</th>';
		}		
		echo'			<th>Total</th>
				</thead>';
		
		echo '<tbody>';
		
		foreach ( $listaDeDatas as $data ) {
			echo '<tr>';
			echo '<td>' . date ( 'd/m/Y', strtotime ( $data ) ) . '</td>';
			foreach ( $tipos as $tipo ) {
				
				echo '<td>'.number_format (floatval($listaDeDados [$data] [$tipo->getId ()]) , 2, ',', '.' )    . '</td>';
				$subTotal [$tipo->getId ()] += $listaDeDados [$data] [$tipo->getId ()];
			}
			echo '<td>'.number_format ($listaDeDados [$data] ['total'] , 2, ',', '.' )   . '</td>';
			echo '</tr>';
			$subTotal ['total'] += $listaDeDados [$data] ['total'];
		}
		echo '<tr id="soma">
				<th>Somatório</th>';
		foreach ( $tipos as $tipo ) {
			echo '<td>'.number_format ( $subTotal [$tipo->getId ()] , 2, ',', '.' )  . '</td>';
		}
		echo '<td>' . number_format ( $subTotal ['total']  , 2, ',', '.' )  . '</td>';
		echo '</tr>';
		echo '</table>
				<div class="doze colunas relatorio-rodape">
					<span>CATRACA | Copyright © 2015 - DTI</span>
					<span>Relatório Emitido em: '.$date = date('d/m/Y').'</span>';
// 		echo '<a class="botao icone-printer"> Imprimir</a>';
				
		echo '		</div>		
			</div>';
	}
	
	
	public function mostraListaDeDadosPratos($listaDeDados, $titulo, $tipos, $listaDeDatas) {
		$subTotal = array ();
		foreach ( $tipos as $tipo ) {
			$subTotal [$tipo->getId ()] = 0;
		}
		$subTotal ['total'] = 0;
	
		echo '<div class=" doze colunas borda relatorio">
				<h2>UNILAB<small class="fim">Universidade da Integração Internacional da Lusofonia Afro-Brasileira</small></h2>	
				<hr class="um">
				<h3>'.$titulo.'</h3>							
				<hr class="dois">';
		
		echo '<table class="tabela-relatorio">
				<thead>
					<tr>
						<th>Data</th>';
		foreach ( $tipos as $tipo ) {
			echo '<th>' . $tipo->getNome () . '</th>';
		}		
		echo'			<th>Total</th>
				</thead>';
		
		echo '<tbody>';
		
		foreach ( $listaDeDatas as $data ) {
			echo '<tr>';
			echo '<td>' . date ( 'd/m/Y', strtotime ( $data ) ) . '</td>';
			foreach ( $tipos as $tipo ) {
				
				echo '<td>'.$listaDeDados [$data] [$tipo->getId ()] . '</td>';
				$subTotal [$tipo->getId ()] += $listaDeDados [$data] [$tipo->getId ()];
			}
			echo '<td>'.$listaDeDados [$data] ['total'] . '</td>';
			echo '</tr>';
			$subTotal ['total'] += $listaDeDados [$data] ['total'];
		}
		echo '<tr id="soma">
				<th id="limpar">Somatório</th>';
		foreach ( $tipos as $tipo ) {
			echo '<td>'.$subTotal [$tipo->getId ()] . '</td>';
		}
		echo '<td>' . $subTotal ['total'] . '</td>';
		echo '</tr>';
		
		
		
		echo '</table>
				<div class="doze colunas relatorio-rodape">
					<span>CATRACA | Copyright © 2015 - DTI</span>
					<span>Relatório Emitido em: '.$date = date('d-m-Y H:i:s').'</span>';
		
// 		echo '<a class="botao icone-printer"> Imprimir</a>';
		echo '</div>		
			</div>';
	}
	
	public function mostraMatriz($matriz){
		echo '<br><br><br><table border="1">';
		foreach($matriz as $chave => $valor){
			echo '<tr><th>'.$chave.'</th>';
			foreach($valor as $chave2 => $valor2){
				echo '<td>'.$chave2.' - '.$valor2.'</td>';
			}
			echo '</tr>';
				
		}
		echo '</table>';
		
	}
}

?>