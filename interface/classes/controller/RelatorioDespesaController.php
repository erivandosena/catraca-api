<?php
class RelatorioDespesaController {
	private $view;
	private $dao;
	public static function main($nivelDeAcesso) {
		switch ($nivelDeAcesso) {
			case Sessao::NIVEL_SUPER :
				$controller = new RelatorioDespesaController ();
				$controller->relatorio ();
				break;
			case Sessao::NIVEL_ADMIN :
				$controller = new RelatorioDespesaController ();
				$controller->relatorio ();
				break;

			case Sessao::NIVEL_POLIVALENTE:
				$controller = new RelatorioDespesaController ();
				$controller->relatorio ();
				break;
			case Sessao::NIVEL_CATRACA_VIRTUAL:
				$controller = new RelatorioDespesaController ();
				$controller->relatorio ();
				break;
			case Sessao::NIVEL_RELATORIO:
				$controller = new RelatorioDespesaController ();
				$controller->relatorio ();
				break;
			case Sessao::NIVEL_CATRACA_VIRTUAL_ORFA:
				$controller = new RelatorioDespesaController ();
				$controller->relatorio ();
				break;
			default :
				UsuarioController::main ( $nivelDeAcesso );
				break;
		}
	}
	public function __construct(){
	    $this->dao = new UnidadeDAO ();
	    $this->view = new RelatorioDespesaView();
	}
	
	public function relatorio() {
	    if (!isset ( $_GET ['gerar'] )) {
	        $listaDeUnidades = $this->dao->retornaLista ();
	        $this->view->exibirFormulario($listaDeUnidades);
	        return;
	    }
	    if (! isset($_GET['data_inicial']) || ! isset($_GET['data_final'])) {
	        echo "Preencha duas datas";
	        return;
	    }
	    
		$unidades = array();
		foreach($_GET as $chave => $valor)
		{
		    if(strstr($chave, 'unidade'))
		    {
		        $unidades[] = $valor;
		    }
		}
		
		
		
	    $this->geraRelacaoPratosValores ( $unidades, $_GET ['data_inicial'], $_GET ['data_final']);
			
	}


	
	public function geraRelacaoPratosValores($listaDeUnidades, $data1, $data2) {
	    
	    if(!count($listaDeUnidades)){
	        echo "Nenhuma unidade selecionada";
	        return;
	    }

	    
		$strUnidade = "";
		$unidadeDao = new UnidadeDAO($this->dao->getConexao());
		$strFiltroUnidade  = " AND (";
		$i = 0;
		$tamanho = count($listaDeUnidades) - 1;
		foreach($listaDeUnidades as $idUnidade)
		{
		
		    if($i > 0 && $i < $tamanho){
		        $strFiltroUnidade  .= " OR ";
		        $strUnidade .= ', ';
		    }else if($i > 0){
		        $strFiltroUnidade  .= " OR ";
		        $strUnidade .= ' e ';
		    }
			$strFiltroUnidade  .= " catraca_unidade.unid_id = $idUnidade ";
			
			$unidade = new Unidade();
			$unidade->setId($idUnidade);
			$unidadeDao->preenchePorId($unidade);
			
			$strUnidade .=  $unidade->getNome();
			$i++;
		}
		$strFiltroUnidade .= ")";
		
		$dao = new TipoDAO ($this->dao->getConexao());
		$tipos = $dao->retornaLista ();
		$listaDeDados = array ();

		foreach ( $tipos as $tipo ) {
			$listaDeDados [$tipo->getId ()] ['pratos'] = 0;
			$listaDeDados [$tipo->getId ()] ['valor'] = 0;
			$listaDeDados [$tipo->getId ()] ['custo'] = 0;
		}
		$listaDeDados ['total'] ['pratos'] = 0;
		$listaDeDados ['total'] ['valor'] = 0;
		$listaDeDados ['total'] ['custo'] = 0;
		$turnoDao = new TurnoDAO($this->dao->getConexao());
		$listaDeTurnos = $turnoDao->retornaLista();
		$listaDeDadosTurno = array();
		foreach($listaDeTurnos as $turno){
		
		    foreach ( $tipos as $tipo ) {
		        $listaDeDadosTurno[$turno->getId()] [$tipo->getId ()] ['pratos'] = 0;
		        $listaDeDadosTurno[$turno->getId()] [$tipo->getId ()] ['valor'] = 0;
		        $listaDeDadosTurno [$turno->getId()][$tipo->getId ()] ['custo'] = 0;
		    }
		    $listaDeDadosTurno [$turno->getId()] ['total'] ['pratos'] = 0;
		    $listaDeDadosTurno [$turno->getId()] ['total'] ['valor'] = 0;
		    $listaDeDadosTurno [$turno->getId()] ['total'] ['custo'] = 0;
		    
		}
		
		$dataInicial = $data1 . ' 00:00:00';
		$dataFinal = $data2. ' 23:59:59';

		$sql = "SELECT  regi_valor_pago, unid_id as unn, regi_data, tipo_id  FROM registro
			INNER JOIN vinculo ON vinculo.vinc_id = registro.vinc_id
			INNER JOIN vinculo_tipo ON vinculo.vinc_id = vinculo_tipo.vinc_id
			INNER JOIN catraca ON catraca.catr_id = registro.catr_id
			INNER JOIN catraca_unidade ON catraca.catr_id = catraca_unidade.catr_id
			WHERE (regi_data BETWEEN '$dataInicial' AND '$dataFinal') $strFiltroUnidade;";


        $custoDAO = new CustoDAO($this->dao->getConexao());
        $listaDeCustos = $custoDAO->listaDeCustos();
        foreach($listaDeCustos as $umCusto){
            $umCusto->setInicio(strtotime($umCusto->getInicio().' 00:00:00'));
            $umCusto->setFim(strtotime($umCusto->getFim().' 23:59:59'));
            
        }

        foreach($listaDeTurnos as $umTurno){
            $umTurno->setHoraInicial(strtotime($umTurno->getHoraInicial()));
            $umTurno->setHoraFinal(strtotime($umTurno->getHoraFinal()));
        }
		foreach ( $dao->getConexao ()->query ( $sql ) as $linha ) {

		    $timeRegistro = strtotime(date("H:i:s", strtotime($linha['regi_data'])));
		    
		    foreach($listaDeTurnos as $umTurno){
		       
		        if($timeRegistro <= $umTurno->getHoraFinal() && $timeRegistro >$umTurno->getHoraInicial())
		        {
		            $turnoDoRegistro = $umTurno;
		            break;
		        }
		        
		    }
		    $timeRegistro = strtotime($linha['regi_data']);
		    $flag = false;
		    foreach($listaDeCustos as $umCusto){
		        if($umCusto->getTurno()->getId() != $turnoDoRegistro->getId()){
		            continue;
		        }
		        if($umCusto->getUnidade()->getId() != $linha['unn']){
		            continue;
		        }
		        if($umCusto->getInicio() <= $timeRegistro && $umCusto->getFim() >= $timeRegistro )
		        {
		            $custoDoRegistro = $umCusto;
		            $flag = true;
		            break;
		        }
		        
		    }
		    if(!$flag){
		        $mensagem = "Registro sem custo definido encontrado.";
		        $mensagem .= "<br>Cadastre um custo que contemple este registro. <br><br> ";
		        $mensagem .= "Data: ".date('d/m/Y H:i:s', $timeRegistro)."<br>";
		        $mensagem.= 'Turno:  '.$turnoDoRegistro->getDescricao()."<br>";
		        $unidadeDao = new UnidadeDAO($this->dao->getConexao());
		        $unidade = new Unidade();
		        $unidade->setId($linha['unn']);
		        $unidadeDao->preenchePorId($unidade);
		        $mensagem .= "Unidade: ".$unidade->getNome();
		        $this->view->mensagemErro($mensagem);	
		        return;
		    }
		  

		    
		    
		    $listaDeDados [$linha['tipo_id']] ['pratos']++;
		    $listaDeDados [$linha['tipo_id']] ['valor'] += floatval($linha ['regi_valor_pago']);
		    $listaDeDados [$linha['tipo_id']] ['custo'] += $custoDoRegistro->getValor();
		    
		    $listaDeDados ['total'] ['pratos']++;
		    $listaDeDados ['total'] ['valor'] += floatval($linha['regi_valor_pago']);
		    $listaDeDados ['total'] ['custo'] += $custoDoRegistro->getValor();
		    
		    
		    $listaDeDadosTurno[$turnoDoRegistro->getId()] [$linha['tipo_id']] ['pratos']++;
		    $listaDeDadosTurno [$turnoDoRegistro->getId()] [$linha['tipo_id']] ['valor'] += floatval($linha ['regi_valor_pago']);
		    $listaDeDadosTurno [$turnoDoRegistro->getId()] [$linha['tipo_id']] ['custo'] += $custoDoRegistro->getValor();
		    
		    $listaDeDadosTurno [$turnoDoRegistro->getId()] ['total'] ['pratos']++;
		    $listaDeDadosTurno [$turnoDoRegistro->getId()] ['total'] ['valor'] += floatval($linha['regi_valor_pago']);
		    $listaDeDadosTurno [$turnoDoRegistro->getId()] ['total'] ['custo'] += $custoDoRegistro->getValor();
		    
		    
		    
		}
		$titulos = array();
		$titulos[0] = "Relatorio de Despesas";
		$titulos[1] = $strUnidade;
		$titulos[2] = 'De '. date ( 'd/m/Y', strtotime ( $data1 ) ) . ' a ' . date ( 'd/m/Y', strtotime ( $data2 ) );
		$titulos[3] = "Todos os Turnos";



		
		if($_GET['gerar'] == 'Excel'){
		    $dados = $this->view->gerarStrCSV($titulos, $listaDeDados, $tipos);
		    
		    foreach($listaDeTurnos as $turno){
		        $dados .= "\n\n";
		        $titulos[3] = $turno->getDescricao();
		        $dados .= $this->view->gerarStrCSV($titulos ,$listaDeDadosTurno[$turno->getId()], $tipos);
		        
		    }
		    $dados =  utf8_decode($dados);
		    $nomeArquivo = "../tmp/relatorio".uniqid().".csv";
		    if(fwrite($file=fopen($nomeArquivo,'w+'),$dados)) {
		        
		        
		        header('Content-Description: File Transfer');
		        header('Content-Disposition: attachment; filename="relatorio.csv"');
		        header('Content-Type: application/octet-stream');
		        header('Content-Transfer-Encoding: binary');
		        header('Content-Length: ' . filesize($nomeArquivo));
		        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		        header('Pragma: public');
		        header('Expires: 0');
		        fclose($file);
		        readfile($nomeArquivo);
		        unlink($nomeArquivo);
		        
		    }
		    
		}else{
		    
		    $this->view->mostrarRelatorio($titulos, $listaDeDados, $tipos);
		    foreach($listaDeTurnos as $turno){
		        $titulos[3] = $turno->getDescricao();
		        $this->view->mostrarRelatorio($titulos ,$listaDeDadosTurno[$turno->getId()], $tipos);
		    }
		}
		

        
        
        
		
		
		
	}
}

?>