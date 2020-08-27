<?php

/**
 * @author Jefferson Uchoa Ponte
 * @package Controller
 **/

class PessoalController {
	public static function main($nivelDeAcesso){
	
		switch ($nivelDeAcesso){
			case Sessao::NIVEL_ADMIN:
				$controller = new PessoalController();
				$controller->consultarUsuario();
				break;
			case Sessao::NIVEL_SUPER:
				$controller = new PessoalController();
				$controller->consultarUsuario();
				break;			
			case Sessao::NIVEL_COMUM:	
				$controller = new PessoalController();
				$controller->telaPessoal();
				break;
			default :
				UsuarioController::main ($nivelDeAcesso);
				break;
		}
	}
	private $dao;
	private $view;
	
	//Modificado...
	
	public function consultarUsuario(){
		$this->view = new PessoalView();
		
		echo '	<div class = "simpleTabs">
			        <ul class = "simpleTabsNavigation">
						<li><a href="#">Identifica&ccedil;&atilde;o</a></li>
			        </ul>
		        <div class = "simpleTabsContent">';
		
		$this->view->formBuscaCartao();
		
		if(isset($_GET['numero_cartao'])){
			if(strlen($_GET['numero_cartao']) > 3){
		
				$cartao = new Cartao();
				$cartao->setNumero($_GET['numero_cartao']);
				$numeroCartao = $cartao->getNumero();
				$dataTimeAtual = date ( "Y-m-d G:i:s" );
				$sqlVerificaNumero = "SELECT * FROM usuario
				INNER JOIN vinculo
				ON vinculo.usua_id = usuario.usua_id
				LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
				LEFT JOIN tipo ON cartao.tipo_id = tipo.tipo_id
				WHERE cartao.cart_numero = :numeroCart ";
								
				$result = $this->dao->getConexao()->prepare($sqlVerificaNumero);
				$result->bindParam(":numeroCart", $numeroCartao, PDO::PARAM_STR);
				$result->execute();
								
				$idCartao = 0;
				$usuario = new Usuario();
				$tipo = new Tipo();
				$vinculoDao = new VinculoDAO($this->dao->getConexao());
				$vinculo = new Vinculo();
				
				while($linha = $result->fetch(PDO::FETCH_ASSOC)){
					$idDoVinculo = $linha['vinc_id'];
					$tipo->setNome($linha['tipo_nome']);
					$usuario->setNome($linha['usua_nome']);
					$usuario->setId($linha['usua_id']);
					$usuario->setIdBaseExterna($linha['id_base_externa']);
					$idCartao = $linha['cart_id'];
					
					$vinculo->setAvulso($linha['vinc_avulso']);
					$avulso = $linha['vinc_avulso'];
					if($avulso){
						$usuario->setNome("Avulso");
					}
					break;
				}
					
				if($idCartao){
		
					$vinculo->setId($idDoVinculo);
					$cartao->setId($idCartao);
					$vinculoDao->vinculoPorId($vinculo);
					$imagem = null;
		
					if(file_exists('fotos/'.$usuario->getIdBaseExterna().'.png')){
						$imagem = $usuario->getIdBaseExterna();
					}else {
						$imagem = "sem-imagem";
					}						
		
					if(!$vinculo->isActive()){
						echo '<div id="pergunta">';
						$this->view->formMensagem("-erro", "vinculo não está ativo.");
						echo '	<a href="?pagina=cartao&numero_cartao='.$_GET['numero_cartao'].'&cartao_renovar=1" class="botao">Renovar</a>
							</div>';
						if(isset($_GET['cartao_renovar'])){
							if(isset($_POST['certeza'])){
								$usuarioDao = new UsuarioDAO($this->dao->getConexao());
		
								$usuarioDao->retornaPorIdBaseExterna($usuario);
		
								if($vinculoDao->usuarioJaTemVinculo($usuario))
								{
									$this->view->formMensagem("-ajuda", "Esse usuário já possui vínculo válido.");
									echo '<meta http-equiv="refresh" content="4; url=.\?pagina=cartao&selecionado=' . $usuario->getIdBaseExterna() . '">';
									return;
								}
								if($vinculo->isAvulso()){
									$this->view->formMensagem("-ajuda", "Não existe renovação de vínculos avulsos!");
									echo '<meta http-equiv="refresh" content="4; url=.\?pagina=cartao&selecionado=' . $usuario->getIdBaseExterna() . '">';
									return;
								}
		
								if(!$this->verificaSeAtivo($usuario)){
									$this->view->formMensagem("-erro", "Esse usuário possui um problema quanto ao status!");
									echo '<meta http-equiv="refresh" content="4; url=.\?pagina=cartao&selecionado=' . $usuario->getIdBaseExterna() . '">';
									return;
								}
		
								$daqui3Meses = date ( 'Y-m-d', strtotime ( "+60 days" ) ) . 'T' . date ( 'G:00:01' );
								$vinculo->setFinalValidade($daqui3Meses);
		
								if($vinculoDao->atualizaValidade($vinculo)){
									$this->view->formMensagem("-sucesso", "Vínculo Atualizado com Sucesso!");
								}else{
									$this->view->formMensagem("-erro", "Erro ao tentar renovar vínculo.");
								}
								echo '<meta http-equiv="refresh" content="2; url=.\?pagina=cartao&selecionado=' . $usuario->getIdBaseExterna() . '">';
								return;
							}
		
							$this->view->formConfirmacaoRenovarVinculo();
						}
					}
				}else{
					$this->view->formMensagem("-erro", "Cartão Não possui Vínculo Válido.");
				}
			}
			$this->telaDeCreditos($usuario);
//			$this->telaDeTransacoes($usuario);
		}
		
		
		echo '	</div>
		    </div>';
	
	}
	public function PessoalController(){
		$this->dao = new DAO();
	}
	
	public function telaPessoal(){
		
		echo '<div class="conteudo"> 
				<div class = "simpleTabs">
			        <ul class = "simpleTabsNavigation">				
						<li><a href="#">Minhas Transações</a></li>		
			        </ul>
			        <div class = "simpleTabsContent">';
					$usuario = new Usuario();
					$sessao = new Sessao();
					$usuarioDao = new UsuarioDAO($this->dao->getConexao());
					$usuario->setId($sessao->getIdUsuario());
					$usuarioDao->preenchePorId($usuario);
					$this->telaDeCreditos($usuario);
		echo '		</div>
		   		</div>
			</div>';	
	}
	
	public function telaDeTransacoes(Usuario $usuario){
// 		$idUsuario = $usuario->getId();
// 		$result = $this->dao->getConexao()->query("SELECT * FROM transacao 
// 				INNER JOIN usuario ON usuario.usua_id = transacao.usua_id
// 				WHERE usua_id1 = $idUsuario
// 				ORDER BY tran_data DESC LIMIT 100");
// 		echo '<p>&Uacute;ltimas 100 transa&ccedil;&otilde;es</p>';
// 		echo '<table class="tabela borda-vertical zebrada texto-preto">';
// 		echo '<thead>';
// 		echo '<tr><th>Data/Hora</th><th>Valor</th><th>Descrição</th><th>Operador</th></tr>';
// 		echo '</thead>';
// 		echo '<tbody>';
// 		foreach($result as $linha){
// 			$time = strtotime($linha['tran_data']);
			
// 			echo '<tr><td>'.date('d/m/Y H:i:s', $time).'</td><td>'.$linha['tran_valor'].'</td><td>'.$linha['tran_descricao'].'<td>'.$linha['usua_nome'].'</td></td></tr>';
			
// 		}
// 		echo '</tbody>';
// 		echo '</table>';
		
	}
	
	public function telaDeCreditos(Usuario $usuario){
		
		$vinculoDao = new VinculoDAO($this->dao->getConexao());		
		$vinculos = $vinculoDao->retornaVinculosValidosDeUsuario($usuario);
		
		if(count($vinculos) < 1){
			echo '<h2 class="titulo">Nenhum cartao ativo</h2>';
			
			$inativos = $vinculoDao->retornaVinculosVencidos($usuario);
			if(count($inativos) > 0){
			    if(count($inativos) == 1){
			        echo '<h2 class="titulo">Cartão Inativo</h2>';
			    }else{
			        echo '<h2 class="titulo">Cartões Inativos</h2>';
			    }
			    
			    $this->exibirVinculos($inativos, $usuario);
			    
			}
			
			
		}else if(count($vinculos) == 1){
			echo '<h2 class="titulo">Vínculo Ativo</h2>';
			$this->exibirVinculos($vinculos, $usuario);			
		}else if(count($vinculos) > 1){
			echo '<h2 class="titulo">Vínculos Ativos</h2>';
			$this->exibirVinculos($vinculos, $usuario);
		}		
		
		$vinculos = array();
		$vinculos = $vinculoDao->retornaVinculosVencidos($usuario);
		if(count($vinculos) < 1){
			echo '<h2 class="titulo">Nenhum cartao Inativo</h2>';
		}else if(count($vinculos) == 1 && $vinculos[0]->isActive()){
			echo '<h2 class="titulo">Vínculo Inativo</h2>';			
			$this->exibirVinculos($vinculos, $usuario);				
		}else if(count($vinculos) > 1){
			echo '<h2 class="titulo">Vínculos Inativos</h2>';
			$this->exibirVinculos($vinculos, $usuario);
		}
		
	}
	
	public function exibirVinculos($vinculos, $usuario){
		foreach($vinculos as $vinculo){
			$this->exibirVinculo($vinculo, $usuario);
		}
	}
	
	public function exibirVinculo(Vinculo $vinculo, Usuario $usuario){
		if($vinculo->isAvulso() && !$vinculo->isActive()){
			return;
		}
		echo '<div class="borda">';
		echo '<strong><p>Usuário: </strong>'.$vinculo->getResponsavel()->getNome().'</p>';
		echo '<strong><p>Créditos: </strong>R$ '.number_format($vinculo->getCartao ()->getCreditos(), 2, ',', '.' ) . '</p>';
		echo '<strong><p>Número do Cartão: </strong>'.$vinculo->getCartao()->getNumero().'</p>';
		$time = strtotime($vinculo->getInicioValidade());
		$timeFim = strtotime($vinculo->getFinalValidade());
		echo '<strong><p>Início da Validade: </strong>'.date('d/m/Y',$time).'</p>';
		echo '<strong><p>Fim da Validade: </strong>'.date('d/m/Y',$timeFim).'</p>';
		
		if ($vinculo->isAvulso()) {
			echo '<strong>Cartão Avulso</strong>';
		} else {
			echo '<strong>Cartão Pessoal</strong>';
		}
		
		if(isset($_GET['id_refeicoes'])){
			$idTransacao = intval($_GET['id_refeicoes']);			
			$periodo = "&Uacute;ltimas Transações com o Cartão";					
			
			if($vinculo->getId() == $idTransacao){
				
				$idUsuario = $usuario->getId();
				$ano = strval(date('Y'));
				$inicio = date('Y-m'.'-01 00:00:00');				 
				$fim = date('Y-m-d 23:59:59');					
				$strPeriodoTran = "(tran_data BETWEEN '$inicio' AND '$fim') AND";
				$strPeriodo = "(registro.regi_data BETWEEN '$inicio' AND '$fim') AND";
				$strOrdenar = 'ORDER BY regi_data DESC';				
				$saldo = floatval(0);				
				
				$meses = array (	'1' => "Janeiro",	'2' => "Fevereiro",	'3' => "Março",
									'4' => "Abril",		'5' => "Maio",		'6' => "Junho",
									'7' => "Julho",		'8' => "Agosto",	'9' => "Setembro",
									'10' => "Outubro",	'11' => "Novembro",	'12' => "Dezembro");
// 				$meses = array();
// 				$meses[] = array ("mes"=>"Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
// 				$meses[] = array ("abrev"=>"Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
				
// 				foreach ($meses as $chave => $mes){
// 					echo $meses[$chave]['mes'];
// 				}
				
				//var_dump($mes);
				
				// var_dump($meses);
				
// 				$meses = array (	"Jan"=>"Janeiro", 	"Feb"=>"Fevereiro",	"Mar"=>"Março",
// 									"Apr"=>"Abril",		"May"=>"Maio",		"Jun"=>"Junho",
// 									"Jul"=>"Julho",		"Aug"=>"Agosto",	"Sep"=>"Setembro",
// 									"Oct"=>"Outubro",	"Nov"=>"Novembro",	"Dec"=>"Dezembro");
								
// 				for ($i=1;$i<=12;$i++){
// 				echo 'Mes=>'.date('M', strtotime('+'.$i.'month'))."<br>";
// 				}
				
				echo '	<div class="fundo-cinza1" style="padding:10px;border-radius:10px;">
					<form action="" class="formulario sequencial" method="post">
						<object class="rotulo"><strong>Demosntrativo por Período: </strong></object>
				
						<select name="ano" id="">							
							<option value="'.(date('Y')-1).'">'.(date('Y')-1).'</option>
							<option value="'.date('Y').'" selected>'.date('Y').'</option>
						</select>
			
						<select name="mes" id="">
							<option value="" selected>Mês</option>';
				foreach ($meses as  $chave => $mes) {
					echo '	<option value="'.$chave.'">'.$mes.'</option>';
				}
				echo '	</select>
						<input type="submit" value="Buscar" nome="buscar">
					</form>
				</div>';
				
				if (isset ($_POST['mes'])) {
						
					if ($_POST['ano'] != "") {
						$ano = $_POST['ano'];
					}				
										
					if ($_POST['mes'] != "") {
				
						$mes = $_POST['mes'];
						$dia = '31';
				
						if ($mes == '02') {
							$dia = "28";
						}
				
						if ($mes == '04' || $mes == '06' || $mes == '09' || $mes == '11') {
							$dia = "30";
						}						
						
						$periodo = 'Demonstrativo do Mês de: '.$meses[$mes].'/ '.$ano;
						
						$inicio = date($ano.'-'.$mes.'-01'.' 00:00:00');						
						$fim = date($ano.'-'.$mes.'-'.$dia.' 23:59:59');
				
						$strPeriodo = "(registro.regi_data BETWEEN '$inicio' AND '$fim') AND";
						$strPeriodoTran = "(tran_data BETWEEN '$inicio' AND '$fim') AND";
						$strOrdenar = "ORDER BY registro.regi_data DESC";
					}					
					
				}				
				
				//Calculando o saldo com base em todo histórico do usuario.
					
				$sqlSaldo1 = "	SELECT * FROM transacao
								INNER JOIN usuario ON usuario.usua_id = transacao.usua_id
								WHERE (tran_data BETWEEN '2016-10-16 23:59:59' AND '$inicio') and usua_id1 = $idUsuario";
				$resultSaldo1 = $this->dao->getConexao()->query($sqlSaldo1);
				foreach ($resultSaldo1 as $sald1){
					$saldo += floatval($sald1['tran_valor']);
				}
					
				$sqlSaldo2 = "	SELECT * FROM registro
								INNER JOIN catraca ON registro.catr_id = catraca.catr_id
								INNER JOIN catraca_unidade ON catraca_unidade.catr_id = catraca.catr_id
								INNER JOIN unidade ON catraca_unidade.unid_id = unidade.unid_id									
								INNER JOIN vinculo ON registro.vinc_id = vinculo.vinc_id
								INNER JOIN usuario ON vinculo.usua_id = usuario.usua_id									
								WHERE regi_data BETWEEN '2016-10-16 23:59:59' AND '$inicio' AND usuario.usua_id = $idUsuario";
				$resultSaldo2 = $this->dao->getConexao()->query($sqlSaldo2);
				foreach ($resultSaldo2 as $sald2){
					$saldo -= floatval($sald2['regi_valor_pago']);
				}
				//=========================================================================================================//
							
				
				
				//Criação da matriz com dos dados(Registros e Transações), para gerar o demonstrativos
				$sql = "	SELECT * FROM registro 
							INNER JOIN catraca ON registro.catr_id = catraca.catr_id
							INNER JOIN catraca_unidade ON catraca_unidade.catr_id = catraca.catr_id 
							INNER JOIN unidade ON catraca_unidade.unid_id = unidade.unid_id
							INNER JOIN unidade_turno ON unidade.unid_id = unidade_turno.unid_id
							INNER JOIN turno ON unidade_turno.turn_id = turno.turn_id							
							INNER JOIN vinculo ON registro.vinc_id = vinculo.vinc_id
							INNER JOIN usuario ON vinculo.usua_id = usuario.usua_id							
							WHERE (registro.regi_data::time BETWEEN turno.turn_hora_inicio::time AND turno.turn_hora_fim::time)
							AND $strPeriodo registro.regi_data >= '2016-10-16 23:59:59' AND usuario.usua_id = $idUsuario $strOrdenar";				
				$result = $this->dao->getConexao()->query($sql);
				
				foreach ( $result as $linha ) {
					$time = strtotime ($linha['regi_data']);					
					$demonstrativo[] = array(
							'data'=> date('Y/m/d',$time),
							'hora'=> date('H:i:s',$time),
							'operação'=> $linha ['turn_descricao'],
							'crédito'=> 0,
							'débito'=> floatval($linha['regi_valor_pago']),
							'unidade/ operador'=> $linha['unid_nome'],
							//'saldo'=> 0
							);					
				}				
				
				$sql2 = "	SELECT * FROM transacao
							INNER JOIN usuario ON usuario.usua_id = transacao.usua_id
							WHERE $strPeriodoTran usua_id1 = $idUsuario";
				$result2 = $this->dao->getConexao()->query($sql2);
				
				foreach ($result2 as $linha2 ) {
					$time = strtotime ($linha2['tran_data']);
					$demonstrativo[] = array(
							'data'=> date('Y/m/d',$time),
							'hora'=> date('H:i:s',$time),
							'operação'=> $linha2['tran_descricao'],
							'crédito'=>floatval($linha2['tran_valor']),
							'débito'=> 0,
							'unidade/ operador'=> $linha2['usua_nome'],
							//'saldo'=> floatval($linha2['tran_valor']),
					);					
				}					
				
				echo '<h2 class="titulo">'.$periodo.'</h2>';
				if (isset($demonstrativo)){				
					
					// Array com o nome de todas as colunas ("data","hora" etc.)
					$cols = array_keys($demonstrativo[0]);
					
					//Ordenar Matriz utilizando a funcao array_multisort.
					$data = array();
					$hora = array();										
					foreach ($demonstrativo as $key => $coluna){
						$data[$key] = $coluna['data'];
						$hora[$key] = $coluna['hora'];
					}
					array_multisort($data, SORT_ASC, $hora, SORT_ASC, $demonstrativo);
					//=========================================================================================================//					
					
					if (number_format($saldo, 2,',','.') == number_format(floatval(-2.7755575615629E-14), 2,',','.')){
						$saldo = floatval(0);						
					}
					
					echo '<table  class="tabela borda-vertical zebrada texto-preto" style="width:100%">							
							<thead>
								<tr class="centralizado">
									<th>Data</th>
									<th>Hora</th>
									<th>Operação</th>
									<th>Crédito</th>
									<th>Débito</th>
									<th>Unidade/ Operador</th>';
// 					foreach ($cols as $titulos) {
// 						echo'<th>'.ucwords(strtolower(htmlentities($titulos))).'</th>';
// 					}
					echo '      </tr>
							</thead>
							<tbody>
							';	
					
					foreach ($demonstrativo as $chave => $demo){
						echo'<tr class="centralizado">';
						echo'<td >'.date('d/m/Y',strtotime($demonstrativo[$chave]['data'])).'</td>';
						echo'<td >'.$demonstrativo[$chave]['hora'].'</td>';
							
						if ($demonstrativo[$chave]['operação'] == 'Venda de Créditos'){
							echo'<td class="texto-verde1">Recarga do Cartão</td>';
						}else if ($demonstrativo[$chave]['operação'] == 'Estorno de valores'){
							echo'<td class="texto-vermelho1">Estorno de Créditos</td>';
						}else{
							echo'<td>'.$demonstrativo[$chave]['operação'].'</td>';
						}
						
						if ($demonstrativo[$chave]['crédito'] < 0){
							echo'<td class="texto-vermelho1">R$ '.number_format($demonstrativo[$chave]['crédito'], 2,',','.').'</td>';
						}else if($demonstrativo[$chave]['crédito'] > 0){							
							echo'<td class="texto-verde1">R$ '.number_format($demonstrativo[$chave]['crédito'], 2,',','.').'</td>';							
						}else{
							echo'<td>-</td>';
						}
												
						if ($demonstrativo[$chave]['débito']){
							echo'<td class="texto-vermelho1 ">R$ '.number_format($demonstrativo[$chave]['débito'], 2,',','.').'</td>';
						}else{
							echo'<td>-</td>';
						}						
						
						echo'<td>'.$demonstrativo[$chave]['unidade/ operador'].'</td>';													
						
						if ($demonstrativo[$chave]['débito'] != 0){
							$saldo -= floatval($demonstrativo[$chave]['débito']);
						}
						
						if ($demonstrativo[$chave]['crédito'] != 0){
							$saldo += floatval($demonstrativo[$chave]['crédito']);
						}					
						
						if ($saldo >= 0){
							//echo'<td class="texto-azul1">R$ '.number_format($saldo, 2,',','.').'</td>';
						}else{
							if (number_format($saldo, 2,',','.') == number_format(floatval(-2.7755575615629E-14), 2,',','.')){
								$saldo = floatval(0);
								//echo'<td>R$ '.number_format($saldo, 2,',','.').'</td>';
							}else{
								//echo'<td class="texto-vermelho1">R$ '.number_format($saldo, 2,',','.').'</td>';
							}							
						}						
					
						echo'</tr>';						
					}
					
					echo '	</tbody>
							</table>';
					
				}else{					
					echo '	<div class="alerta-erro">
						    	<div class="icone icone-notification ix16"></div>
						    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
						    	<div class="subtitulo-alerta">Sem Transações Nesse Período.</div>
							</div>';				
				}
			} 
		}
		
		if ($vinculo->isActive()){
			if(isset($_GET['numero_cartao'])){				
				echo '<p><a href="?pagina=pessoal&id_refeicoes='.$vinculo->getId().'&numero_cartao='.$_GET['numero_cartao'].'" class="botao">Ultimas Refeições</a></p>';				
			}else{			
				echo '<p><a href="?pagina=pessoal&id_refeicoes='.$vinculo->getId().'" class="botao">Ultimas Refeições</a></p>';						
			}
		}
		echo '</div>';
	}

	
}

?>