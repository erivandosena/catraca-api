<?php 

class RegistroOrfaoController {
	
	private $view;
	private $dao;
	
	public static function main($nivelAcesso){		
		switch ($nivelAcesso) {
			case Sessao::NIVEL_SUPER :
				$telaRegistro = new RegistroOrfaoController();
				$telaRegistro->verificarSelecaoRU();	
				break;
			case Sessao::NIVEL_ADMIN:
				$telaRegistro = new RegistroOrfaoController();
				$telaRegistro->verificarSelecaoRU();
				break;
			case Sessao::NIVEL_CATRACA_VIRTUAL_ORFA:
				$telaRegistro = new RegistroOrfaoController();
				$telaRegistro->verificarSelecaoRU();
				break;
			default :
				UsuarioController::main ( $nivelAcesso );
				break;
		}
			
	}
	
	public function RegistroOrfaoController(){
		$this->dao = new DAO();
		$this->view = new RegistroOrfaoView();		
	}
	
	public function verificarSelecaoRU(){
		$erro = false;
		if(isset($_SESSION['id_catraca'])){			
			$this->telaRegistroOrfao();
			return ;			
		}else{
			if(isset($_GET['id_catraca'])){
				$timeAtual = strtotime(date("Y-m-d"));
				$timeSelecionado = strtotime($_GET['data']);
				$horaIni = strtotime($_GET['hora_inicio']);
				$horaFim = strtotime($_GET['hora_fim']);
				
				if ($horaIni == null || $horaFim == null || $horaIni > $horaFim || $horaIni == $horaFim){
					$erro = true;
				} else if ($timeAtual < $timeSelecionado || $timeSelecionado == null){
					$erro = true;
				}else{
					$_SESSION['id_catraca'] = intval($_GET['id_catraca']);
					$_SESSION['turno_id'] = intval($_GET['turno_id']);
					$_SESSION['data'] = $_GET['data'];
					echo '<meta http-equiv="refresh" content="0; url=?pagina=registro_orfao">';
				}
			}
			
			$this->selecionarRU($erro);
		}
		
	}
	
	public function selecionarRU($erro){
		$unidadeDao = new UnidadeDAO($this->dao->getConexao());
		$turnoDao = new TurnoDAO();
		$listaDeCatracas = $unidadeDao->retornaCatracasPorUnidade();
		$listaTurno = $turnoDao->retornaLista();
		$this->view->formSelecionarPeriodo($listaTurno, $listaDeCatracas, $erro);	
	}
	
	public function telaRegistroOrfao(){
		
		$tipoDao = new TipoDAO($this->dao->getConexao());
		$catracaVirtualDao = new CatracaVirtualDAO($this->dao->getConexao());
		$listaDeTipos = $tipoDao->retornaLista();
		
		$catraca = new Catraca();
		
		$catraca->setId($_SESSION['id_catraca']);
		$turnoId = $_SESSION['turno_id'];
		
		$catracaDao = new CatracaDAO($this->dao->getConexao());
		
		$catracaDao->preencheCatracaPorId($catraca);
		
		echo '<div class="navegacao">
				<div class = "simpleTabs">
			        <ul class = "simpleTabsNavigation">
						<li><a href="#">Catraca Virtual</a></li>
			        </ul>
		
			        <div class = "simpleTabsContent">
		
						<div id="catraca-virtual" class="doze colunas borda">';
		

		
		$turno = new Turno();
		$turno->setId($turnoId);
		$turnoDao = new TurnoDAO();
		$turnoAtual = $turnoDao->retornaTurnoPorId($turno);
	
		echo '	<div class="doze colunas conteudo centralizado titulo-com-borda" >
					<div class="quatro colunas">
					<p id="hora">'.date('d/m/Y', strtotime($_SESSION['data'])).' - Registros Pendentes</p>
					</div>';
		
		if($catraca->financeiroAtivo()){
			echo '		<div class="quatro colunas fundo-verde1 texto-branco negrito" >
				 	<p>Módulo Financeiro Habilitado</p>
					</div>';
		}
		else{
			echo '	<div class="quatro colunas fundo-vermelho1 texto-branco negrito" >
				 	<p>Módulo Financeiro Desabilitado</p>
					</div>';
		}
		
		echo '		<div class="quatro colunas">
					<p>Turno '.$turnoAtual->getDescricao().' iniciado</p>
					</div>
				</div>';
		
		$somatorio = 0;
		$quantidades = array();
		foreach ($listaDeTipos as $tipo){
			$quantidades[] = $this->totalGiroTurnoAtualNaoIsento($catraca, $tipo, $turnoAtual);
		}
		
		$isento = new Tipo();
		$isento->setNome("Isento");
		$listaDeTipos[] = $isento;
		$quantidades[] = $this->totalGiroTurnoAtualIsento($catraca, $isento, $turnoAtual);
		
		$this->view->exibirQuantidadesDeCadaTipo($listaDeTipos, $quantidades, $catraca);
				
		$this->view->formBuscaCartao();
		$idCatraca = $_SESSION['id_catraca'];
		$custo = 0;
		$custo = 6.50;
		if(strtolower(trim($catraca->getUnidade()->getNome())) == "campus de liberdade")
		{
		    if(strtolower(trim($descricao)) == "jantar"){
		        $custo = 6.24;
		    }
		}
		
		$custo = $catracaVirtualDao->custoDaRefeicao($catraca);
		
		echo '			</div>';
		
// 		$timeAtual = strtotime(date("Y-m-d"));
// 		$timeSelecionado = strtotime($_SESSION['data']);
		
// 		if ($timeAtual < $timeSelecionado){
// 			$this->view->mensagem('erro', 'A data Selecionada não pode ser maior que a data atual');
// 			unset($_SESSION['id_catraca']);
// 			unset($_SESSION['turno_id']);
// 			unset($_SESSION['data']);
// 			echo '<meta http-equiv="refresh" content="2; url=?pagina=registro_orfao">';
// 		}
		
		if (isset($_GET['encerrar'])){
			unset($_SESSION['id_catraca']);
			unset($_SESSION['turno_id']);
			unset($_SESSION['data']);
			echo '<meta http-equiv="refresh" content="0; url=?pagina=registro_orfao">';
		}
		
		if(isset($_GET['numero_cartao'])){
			if($_GET['numero_cartao'] == NULL || $_GET['numero_cartao'] == "")
				return;
			
// 				if(!($turnoAtual = $catracaVirtualDao->retornaTurnoAtual())){
// 					$this->mensagemErro("Fora do hor&aacute;rio de refei&ccedil;&atilde;o");
// 					echo '<meta http-equiv="refresh" content="2; url=?pagina=gerador">';
// 					return;
// 				}					
					
				$cartao = new Cartao();
				$cartao->setNumero($_GET['numero_cartao']);
				$vinculo = new Vinculo();
				$vinculo->setCartao($cartao);
										
				if(!$catracaVirtualDao->verificaVinculo($vinculo)){
					//Aqui a gente tenta renovar se tiver vinculo proprio nesse cartao.
					$numeroCartao = $vinculo->getCartao()->getNumero();
					$sqlVerificaNumero = "SELECT * FROM usuario
					INNER JOIN vinculo
					ON vinculo.usua_id = usuario.usua_id
					LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
					LEFT JOIN tipo ON cartao.tipo_id = tipo.tipo_id
					WHERE cartao.cart_numero = '$numeroCartao'";
					$result = $this->dao->getConexao()->query($sqlVerificaNumero);
					$idCartao = 0;
					$usuario = new Usuario();
					$tipo = new Tipo();
						
					$vinculo = new Vinculo();
					$i = 0;
					foreach($result as $linha){
						$i++;
						$idDoVinculo = $linha['vinc_id'];
						$tipo->setNome($linha['tipo_nome']);
						$tipo->setValorCobrado($linha['tipo_valor']);
						$usuario->setNome($linha['usua_nome']);
						$usuario->setIdBaseExterna($linha['id_base_externa']);
						$idCartao = $linha['cart_id'];
		
						$cartao->setId($idCartao);
						$cartao->setTipo($tipo);
						$cartao->setCreditos($linha['cart_creditos']);
		
						$vinculo->setQuantidadeDeAlimentosPorTurno($linha['vinc_refeicoes']);
						$vinculo->setAvulso($linha['vinc_avulso']);
						$avulso = $linha['vinc_avulso'];
						if($avulso){
							$usuario->setNome("Avulso");
						}
						$vinculo->setCartao($cartao);
						$vinculo->setId($idDoVinculo);
						$vinculo->setResponsavel($usuario);
						$usuarioDao = new UsuarioDAO();
						$usuarioDao->retornaPorIdBaseExterna($vinculo->getResponsavel());
						break;		
					}
		
					$vinculoDao = new VinculoDAO($this->dao->getConexao());		
						
					if (($i != 0) && ! $vinculoDao->usuarioJaTemVinculo ( $usuario ) && ! $vinculo->isAvulso () && $vinculo->getResponsavel ()->verificaSeAtivo ()) {
						$daqui3Meses = date ( 'Y-m-d', strtotime ( "+90 days" ) ) . 'T' . date ( 'G:00:01' );
						$vinculo->setFinalValidade ( $daqui3Meses );
						$vinculoDao->atualizaValidade ( $vinculo );							
					}else{		
						$this->view->mensagem("erro","Verifique o vinculo deste cartão!");
						echo '<meta http-equiv="refresh" content="2; url=?pagina=registro_orfao">';
						return;
					}
		
				}					
					
				if(!$this->podeContinuarComendo($vinculo, $turnoAtual)){
					$this->view->mensagem("erro","Usuário já passou neste turno!");
					echo '<meta http-equiv="refresh" content="2; url=?pagina=registro_orfao">';
					return;
				}		
					
				if($catracaVirtualDao->vinculoEhIsento($vinculo)){
					$valorPago = 0;		
				}
				else{
					$valorPago = $vinculo->getCartao()->getTipo()->getValorCobrado();
// 					if(($vinculo->getCartao()->getCreditos() < $valorPago) && $catraca->financeiroAtivo()){							
// 						$this->view->mensagem("erro","Usuário créditos insuficiente. ");
// 						echo '<meta http-equiv="refresh" content="4; url=?pagina=registro_orfao">';
// 						return;							
// 					}
				}
					
				$idCartao = $cartao->getId();
				$strNome = $vinculo->getResponsavel()->getNome();
				$data = $_SESSION['data'].' '.$turno->getHoraInicial();
				
				if(isset($_GET['confirmado'])){
		
					$idVinculo= $vinculo->getId();
		
					if($catraca->financeiroAtivo()){						
						
						$this->dao->getConexao()->beginTransaction();
						$novoValor = floatval($vinculo->getCartao()->getCreditos()) - floatval($valorPago);
						$sql0 = "UPDATE cartao set cart_creditos = $novoValor WHERE cart_id = $idCartao";
							
						if(!$this->dao->getConexao()->exec($sql0)){
							$this->dao->getConexao()->rollBack();
							$this->view->mensagem("erro", "Erro ao inserir os dados.");
							echo '<meta http-equiv="refresh" content="2; url=?pagina=registro_orfao">';
							return;
						}							
							
						$sql = "INSERT into registro(regi_data, regi_valor_pago, regi_valor_custo, catr_id, cart_id, vinc_id)
						VALUES('$data', $valorPago, $custo, $idCatraca, $idCartao, $idVinculo)";
						//echo $sql;
							
						if(!$this->dao->getConexao()->exec($sql)){
							$this->dao->getConexao()->rollBack();		
							$this->view->mensagem("erro", "Erro ao inserir os dados.");		
							echo '<meta http-equiv="refresh" content="2; url=?pagina=registro_orfao">';
							return;
						}else{
							$this->dao->getConexao()->commit();
							$this->view->mensagem("sucesso", "Dados inseridos com sucesso.");
							$_SESSION['confirmado'] = "confirmado";
							unset($_SESSION['id_base_externa']);
							unset($_SESSION['numero_cartao']);
							unset($_SESSION['nome_usuario']);
							unset($_SESSION['tipo_usuario']);
							unset($_SESSION['refeicoes_restante']);
		
							/**
							 * #### EXPERIMENTAL ####
							 * FUNCIONALIDADE DE ENVIO DE MENSAGENS PUSH PARA O APP ANDROID
							 *
							 * Chamada da funcao de envios de notificacoes pela Api FireBase
							 * Uso de creditos do cartao
							 */
							NotificacaoBackground::executaPidCatracaVirtual($vinculo->getResponsavel()->getId(), 'catraca_virtual', $valorPago);
		
							echo '<meta http-equiv="refresh" content="2; url=?pagina=registro_orfao">';
		
							/**
							 * #### EXPERIMENTAL ####
							 * FUNCIONALIDADE DE ENVIO DE MENSAGENS PUSH PARA O APP ANDROID
							 *
							 * Chamada da funcao de envios de notificacoes pela Api FireBase
							 * Avisos de limite dos créditos
							 */
							NotificacaoBackground::executaPidCatracaVirtualUtilizacao($vinculo->getResponsavel()->getId(), 'catraca_virtual_utilizacao', $novoValor, $valorPago);
						}
						
					}else{
							
		
						$sql = "INSERT into registro(regi_data, regi_valor_pago, regi_valor_custo, catr_id, cart_id, vinc_id)
						VALUES('$data', $valorPago, $custo, $idCatraca, $idCartao, $idVinculo)";
						//echo $sql;
						if($this->dao->getConexao()->exec($sql)){
							$this->view->mensagem("sucesso", "Dados inseridos com sucesso.");
						}else{
							$this->view->mensagem("erro", "Erro ao inserir os dados.");		
						}
						$_SESSION['confirmado'] = "confirmado";
						unset($_SESSION['id_base_externa']);
						unset($_SESSION['numero_cartao']);
						unset($_SESSION['nome_usuario']);
						unset($_SESSION['tipo_usuario']);
						unset($_SESSION['refeicoes_restante']);
		
						echo '<meta http-equiv="refresh" content="2; url=?pagina=registro_orfao">';
							
					}
		
				}
					
				else{
		
					$strNome = $vinculo->getResponsavel()->getNome();
					$imagem = $vinculo->getResponsavel()->getIdBaseExterna();
		
					/*=======================================================
					 * Vari�vei de sess�o para a p�gina identidicador_cliente
					 * ======================================================*/
					$_SESSION['id_base_externa'] = $imagem;
					$_SESSION['numero_cartao'] = $_GET['numero_cartao'];
					$_SESSION['nome_usuario'] = $strNome;
					$_SESSION['tipo_usuario'] = $cartao->getTipo()->getNome();
					$_SESSION['confirmado'] = "aguarde";
					$_SESSION['refeicoes_restante'] = "";
					/*=======================================================*/
		
					echo '	<div class="borda doze colunas">
		
						<div class="doze colunas dados-usuario">
							<div class="nove colunas">
								<div id="informacao" class="fundo-cinza1">
										<div id="dados" class="dados">';
					if($vinculo->isAvulso()){
						echo '<p>Cartão Avulso: <strong>'.$_SESSION['nome_usuario'] = $vinculo->getDescricao().'</strong></p>';
						echo '<p>Refeições Restantes:<strong>'.$_SESSION['refeicoes_restante'] = $vinculo->getRefeicoesRestantes().' </strong></p>';
					}else{
						echo '<p>Usuário: <strong>'.$strNome.'</strong> - '.$cartao->getTipo()->getNome().'</p>';
						echo '<p>Refeições Restantes:<strong> '.$_SESSION['refeicoes_restante'] = $vinculo->getRefeicoesRestantes().' </strong></p>';
					}
						
					if($catraca->financeiroAtivo())
						echo '<p>Créditos restante: <strong>R$ '.number_format($vinculo->getCartao()->getCreditos(), 2, ',', '.').'</strong></p>';
						if($vinculo->getIsencao()->isActive()){
							echo '<p>Usuário Isento</p>';
							$_SESSION['tipo_usuario'] = "Usuario Isento";
						}else{
							echo '<p>Valor Cobrado: <strong>R$'.number_format($valorPago, 2, ',', '.').'</strong></p>';
						}
						echo '					</div>
		
							<a href="?pagina=registro_orfao&numero_cartao='.$_GET['numero_cartao'].'&confirmado=1" class="botao b-sucesso no-centro">Confimar</a>
								</div>		
							</div>
							<div class="tres colunas zoom">
								<img id="imagem" src="fotos/'.$imagem.'.png" alt="">
							</div>
						</div>
					</div>';
				}
		}
		echo '</div>
				</div>';				
	}
	
	//Classes dao
	public function podeContinuarComendo(Vinculo $vinculo, Turno $turnoAtual){
		$horaInicial = $_SESSION['data'].' '.$turnoAtual->getHoraInicial();
		$horaFinal = $_SESSION['data'].' '.$turnoAtual->getHoraFinal();
		$idCartao = $vinculo->getCartao()->getId();
		$quantidadePermitida = $vinculo->getQuantidadeDeAlimentosPorTurno();
	
		$sql = "SELECT * FROM registro
		WHERE(registro.regi_data BETWEEN '$horaInicial' AND '$horaFinal')
		AND (registro.cart_id = $idCartao)
		ORDER BY registro.regi_id DESC
		LIMIT $quantidadePermitida ";
	
		try{
			$stmt = $this->dao->getConexao()->prepare($sql);
//			$stmt->bindParam(":numero", $numero, PDO::PARAM_STR);
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
	
	public function totalGiroTurnoAtualNaoIsento(Catraca $catraca, Tipo $tipo = null, $turnoAtual){
		$resultado = 0;
		$idCatraca = $catraca->getId();
		$turno = $turnoAtual;
		if($turno){
			$horaInicial = $_SESSION['data'].' '.$turno->getHoraInicial();
			$horaFinal = $_SESSION['data'].' '.$turno->getHoraFinal();
			if($tipo == null){
	
				$sql = "SELECT sum(1) as resultado FROM registro INNER JOIN catraca ON registro.catr_id = catraca.catr_id
				WHERE (catraca.catr_id = $idCatraca) AND (registro.regi_data BETWEEN '$horaInicial' AND '$horaFinal') AND
				(regi_valor_pago > 0)
				";
	
					
			}
			else{
				$idTipo = $tipo->getId();
				$sql = "SELECT sum(1) as resultado FROM registro INNER JOIN catraca ON registro.catr_id = catraca.catr_id
				INNER JOIN cartao ON cartao.cart_id = registro.cart_id
				WHERE (catraca.catr_id = $idCatraca) AND (registro.regi_data BETWEEN '$horaInicial' AND '$horaFinal') AND (cartao.tipo_id = $idTipo) AND
				(regi_valor_pago > 0)
				";
	
			}
	
			foreach ($this->dao->getConexao()->query($sql) as $linha){
				$resultado = $linha['resultado'];
			}
		}
		return $resultado;
	}
	
	public function totalGiroTurnoAtualIsento(Catraca $catraca, Tipo $tipo = null, $turnoAtual){
		$resultado = 0;
		$idCatraca = $catraca->getId();
		$turno = $turnoAtual;
		if($turno){
			$horaInicial = $_SESSION['data'].' '.$turno->getHoraInicial();
			$horaFinal = $_SESSION['data'].' '.$turno->getHoraFinal();
	
			$sql = "SELECT sum(1) as resultado FROM registro INNER JOIN catraca ON registro.catr_id = catraca.catr_id
			WHERE catraca.catr_id = $idCatraca AND registro.regi_data BETWEEN '$horaInicial' AND '$horaFinal'
			AND (regi_valor_pago = 0)
			";
	
				
	
			foreach ($this->dao->getConexao()->query($sql) as $linha){
				$resultado = $linha['resultado'];
			}
		}
		return $resultado;
	}
	public function pegaTurnoAtualSeExistir(Turno $turno){
		
		$dataTimeAtual = $turno->getHoraFinal();
		$sql = "Select * FROM turno WHERE '$dataTimeAtual' BETWEEN turno.turn_hora_inicio AND turno.turn_hora_fim";
		$result = $this->dao->getConexao()->query($sql);
		$turno = null;
		foreach ($result as $linha){
			$turno = new Turno();
			$turno->setHoraInicial($linha['turn_hora_inicio']);
			$turno->setHoraFinal($linha['turn_hora_fim']);
				
		}
		return $turno;
	}

}

?>