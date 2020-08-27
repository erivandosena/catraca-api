<?php

/**
 *
 * @author Jefferson Uchoa Ponte
 *
 */
class CatracaVirtualController{
	
	private $dao;
	private $view;
	private $catracaSelecionada;
	
	public function CatracaVirtualController(){
		
		$this->view = new CatracaVirtualView();
		
	}
	
	public static function main($nivel){

		switch ($nivel){
			case Sessao::NIVEL_SUPER:
				$gerador = new CatracaVirtualController();
				$gerador->verificarSelecaoRU();
				break;
			case Sessao::NIVEL_ADMIN:
				$gerador = new CatracaVirtualController();
				$gerador->verificarSelecaoRU();
				break;
			case Sessao::NIVEL_POLIVALENTE:
				$gerador = new CatracaVirtualController();
				$gerador->verificarSelecaoRU();
				break;
			case Sessao::NIVEL_CATRACA_VIRTUAL:
				$gerador = new CatracaVirtualController();
				$gerador->verificarSelecaoRU();
				break;
			default:
				UsuarioController::main ( $nivel );
				break;
		}
		
		
		
		
	}
	
	public function verificarSelecaoRU(){
		$this->dao = new DAO();
		
		if(isset($_SESSION['catraca_id'])){
			$this->paginaRegistroManual();
		}
		else{
			if(isset($_POST['catraca_id'])){
				$_SESSION['catraca_id'] = intval($_POST['catraca_id']);
				echo '<meta http-equiv="refresh" content="0; url=?pagina=gerador">';
			}
			$this->selecionarRU();
		} 
			
		
	}
	
	public function selecionarRU(){
		$unidadeDao = new UnidadeDAO($this->dao->getConexao());
		$listaDeCatracas = $unidadeDao->retornaCatracasPorUnidade();
		$this->view->formSelecionarRu($listaDeCatracas);		
		
	}

	
	public function paginaRegistroManual(){
		
		$tipoDao = new TipoDAO($this->dao->getConexao());
		$catracaVirtualDao = new CatracaVirtualDAO($this->dao->getConexao());
		$listaDeTipos = $tipoDao->retornaLista();
		$tipoIsento = new Tipo();
		
		
		$unidadeDao = new UnidadeDAO($this->dao->getConexao());
		$catracaDao = new CatracaDAO($this->dao->getConexao());
		
		$catraca = new Catraca();
		$catraca->setId($_SESSION['catraca_id']);
		$catracaDao->preencheCatracaPorId($catraca);
		
		echo '<div class="navegacao"> 
				<div class = "simpleTabs">
			        <ul class = "simpleTabsNavigation">					
						<li><a href="#">Catraca Virtual</a></li>	
			        </ul>
				
			        <div class = "simpleTabsContent">
				
						<div id="catraca-virtual" class="doze colunas borda">';
		$turnoAtivo = false;
		$data = date ( "Y-m-d G:i:s" );
		$selectTurno = "Select * FROM turno WHERE '$data' BETWEEN turno.turn_hora_inicio AND turno.turn_hora_fim";
		$result = $this->dao->getConexao()->query($selectTurno);
		$descricao = "Não";
		foreach($result as $linha){
			$turnoAtivo = true;
			$descricao = $linha['turn_descricao'];
			break;
		}
		
		echo '	<div class="doze colunas conteudo centralizado titulo-com-borda" >
					<div class="quatro colunas">
					<p id="hora">'.date('d/m/Y H:i:s').'</p>
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
					<p>Turno '.$descricao.' iniciado</p>
					</div>
				</div>';	
		
		$somatorio = 0;
		foreach ($listaDeTipos as $tipo){
			$quantidades[] = $unidadeDao->totalGiroTurnoAtualNaoIsento($catraca, $tipo);	
		}
		$isento = new Tipo();
		$isento->setNome("Isento");
		$listaDeTipos[] = $isento;
		$quantidades[] = $unidadeDao->totalGiroTurnoAtualIsento($catraca, $isento);
		
		$this->view->exibirQuantidadesDeCadaTipo($listaDeTipos, $quantidades, $catraca);
		
		
		$this->view->formBuscaCartao();
		$idCatraca = $_SESSION['catraca_id'];
		
		
		echo '
				
						</div>';
		
		if(isset($_GET['numero_cartao'])){
			if($_GET['numero_cartao'] == NULL || $_GET['numero_cartao'] == "")
				return;
			
			if(!($turnoAtual = $catracaVirtualDao->retornaTurnoAtual())){
				$this->mensagemErro("Fora do hor&aacute;rio de refei&ccedil;&atilde;o");
				echo '<meta http-equiv="refresh" content="2; url=?pagina=gerador">';
				return;
			}
			
			$usuarioDao = new UsuarioDAO();
			
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
						
				
					$vinculo->setAvulso($linha['vinc_avulso']);
					$avulso = $linha['vinc_avulso'];
					if($avulso){
						$usuario->setNome("Avulso");
					}
					$vinculo->setCartao($cartao);
					$vinculo->setId($idDoVinculo);
					$vinculo->setResponsavel($usuario);
					
					$usuarioDao->retornaPorIdBaseExterna($vinculo->getResponsavel());					
					break;
				
				}				
								
				$vinculoDao = new VinculoDAO($this->dao->getConexao());
				if(($i != 0) && !$vinculoDao->usuarioJaTemVinculo($usuario) && !$vinculo->isAvulso() && $vinculo->getResponsavel()->verificaSeAtivo()){
					
					$daqui3Meses = date ( 'Y-m-d', strtotime ( "+90 days" ) ) . 'T' . date ( 'G:00:01' );
					$vinculo->setFinalValidade($daqui3Meses);
					
					$vinculoDao->atualizaValidade($vinculo);
						
					
				}else{

					$this->mensagemErro("Verifique o vinculo deste cartão!");
					echo '<meta http-equiv="refresh" content="3; url=?pagina=gerador">';
					return;
				}
				
			}
			
			
			
			if(!$catracaVirtualDao->podeContinuarComendo($vinculo, $turnoAtual)){
				$this->mensagemErro("Usuário já passou neste turno!");
				echo '<meta http-equiv="refresh" content="3; url=?pagina=gerador">';
				return;
			}
				
			
			if($catracaVirtualDao->vinculoEhIsento($vinculo)){
				$valorPago = 0;
				
			}else{
				$valorPago = $vinculo->getCartao()->getTipo()->getValorCobrado();
				if(($vinculo->getCartao()->getCreditos() < $valorPago) && $catraca->financeiroAtivo()){
					
					$this->mensagemErro("Usuário créditos insuficiente. ");
					echo '<meta http-equiv="refresh" content="4; url=?pagina=gerador">';
					return;
					
				}
			}
			
			$idCartao = $cartao->getId();
			$usuarioDao->preenchePorIdBaseExterna($vinculo->getResponsavel());
			$strNome = $vinculo->getResponsavel()->getNome();
					
			if(isset($_GET['confirmado'])){				
				
				$idVinculo= $vinculo->getId();
				
				if($catraca->financeiroAtivo()){

					$this->dao->getConexao()->beginTransaction();
					$novoValor = floatval($vinculo->getCartao()->getCreditos()) - floatval($valorPago);
					$sql0 = "UPDATE cartao set cart_creditos = $novoValor WHERE cart_id = $idCartao";
					
					if(!$this->dao->getConexao()->exec($sql0)){
						$this->dao->getConexao()->rollBack();
						$this->mensagemErro();
						
						echo '<meta http-equiv="refresh" content="2; url=?pagina=gerador">';
						return;
					}
					
					
					$sql = "INSERT into registro(regi_data, regi_valor_pago, catr_id, cart_id, vinc_id)
					VALUES('$data', $valorPago, $idCatraca, $idCartao, $idVinculo)";
					
					if(!$this->dao->getConexao()->exec($sql)){
						$this->dao->getConexao()->rollBack();
						
						$this->mensagemErro();
						
						echo '<meta http-equiv="refresh" content="2; url=?pagina=gerador">';
						return;
					}else{
						$this->dao->getConexao()->commit();
						$this->mensagemSucesso();
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
						
						echo '<meta http-equiv="refresh" content="2; url=?pagina=gerador">';	
						
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
					

					$sql = "INSERT into registro(regi_data, regi_valor_pago, catr_id, cart_id, vinc_id)
					VALUES('$data', $valorPago, $idCatraca, $idCartao, $idVinculo)";
					//echo $sql;
					if($this->dao->getConexao()->exec($sql)){
						$this->mensagemSucesso();
					}else{
							$this->mensagemErro();
					}
					$_SESSION['confirmado'] = "confirmado";
					unset($_SESSION['id_base_externa']);
					unset($_SESSION['numero_cartao']);
					unset($_SESSION['nome_usuario']);
					unset($_SESSION['tipo_usuario']);
					unset($_SESSION['refeicoes_restante']);
						
					echo '<meta http-equiv="refresh" content="2; url=?pagina=gerador">';
					
				}			
				
			}
			
			else{				
			    $usuarioDao->retornaPorIdBaseExterna($vinculo->getResponsavel());
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
								
						<a href="?pagina=gerador&numero_cartao='.$_GET['numero_cartao'].'&confirmado=1" class="botao b-sucesso no-centro">Confimar</a>
								</div>
						
								
						
							</div>
							<div class="tres colunas zoom">';
				if(file_exists("fotos/'.$imagem.'.png")){
					echo '<img id="imagem" src="fotos/'.$imagem.'.png" alt="">';
					
				}
					
				echo '			</div>
						</div>
					</div>';				
			}
		}
		echo '</div>
				</div>';
		        
		
		
	}
	
	
	
	public function mensagemSucesso(){
		echo '<div class="doze colunas borda centralizado">
		
										<div class="alerta-sucesso">
										    <div class="icone icone-download ix24"></div>
										    <div class="titulo-alerta">Ok</div>
										    <div class="subtitulo-alerta">Dados enviados com sucesso!</div>
										</div>
		
									</div>';
		
	}
	
	public function mensagemErro($strMensagem = null){
		if($strMensagem == null)
			$strMensagem = "Erro na tentativa de inserir os dados!";
		echo '<div class="doze colunas borda centralizado">
	
										<div class="alerta-erro">
										    <div class="icone icone-fire ix24"></div>
										    <div class="titulo-alerta">'.$strMensagem.'</div>
										    <div class="subtitulo-alerta">Erro!</div>
										</div>
	
									</div>';
	
	}

}

?>