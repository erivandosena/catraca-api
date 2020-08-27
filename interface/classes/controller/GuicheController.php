<?php
/**
 * Classe utilizada para centralizar as demais instancias das classes dos pacotes (DAO, Model, View, Util).
 * Esta classe será instaciada no index.php.
 * 
 * @author Alan Cleber Morais Gomes 
 * @version 1.0
 * @copyright UNILAB - Universidade da Integracao Internacional da Lusofonia Afro-Brasileira.
 * @package Controle
 */

/**
 * Classe responsável pela realização de venda e estorno de créditos dos usuários.
 * 
 * @link https://www.catraca.unilab.edu.br/docs/index.html
 */
class GuicheController {
	
	/**
	 * Variável utilizada para instaciar a classe View.
	 *
	 * @var GuicheView
	 */
	private $view;
	
	/**
	 * Variável utilizada para instaciar a classe DAO.
	 *
	 * @var DAO;
	 */
	private $dao;
	
	/**
	 * Metodo principal utilizada para controlar o acesso a classe através do nível de acesso do usuario.
	 *
	 * @param Sessao $nivel
	 *        	Recebe uma Sessão que contém o nível de acesso do usuario,
	 *        	esta Sessão é iniciada na página principal, durante o login do usuario.
	 *        	
	 * @link https://www.catraca.unilab.edu.br/docs/index.html
	 */
	public static function main($nivel) {
		switch ($nivel) {
			case Sessao::NIVEL_SUPER :
				$controller = new GuicheController ();
				$controller->telaGuiche ();
				break;
			case Sessao::NIVEL_ADMIN :
				$controller = new GuicheController ();
				$controller->telaGuiche ();
				break;
			
			case Sessao::NIVEL_POLIVALENTE :
				$controller = new GuicheController ();
				$controller->telaGuiche ();
				break;
			case Sessao::NIVEL_GUICHE :
				$controller = new GuicheController ();
				$controller->telaGuiche ();
				break;
			case Sessao::NIVEL_CATRACA_VIRTUAL :
				$controller = new GuicheController ();
				$controller->telaGuiche ();
				break;
			default :
				UsuarioController::main ( $nivel );
				break;
		}
	}
	
	/**
	 * Nesta função é gerada a tela Guichê,
	 * nela é realizada a consulta através do número do cartão do usuário,
	 * onde são consultados vínculo, tipo, créditos...
	 * O valor do caixa está atrelado ao operador, ou seja, cada operador terá seu caixa,
	 * não compartilhando valores.
	 *
	 * 1 - A inserção dos créditos se dá por um segunda validação com o cartão do usuário,
	 * para evitar erros provenientes de cookies do navegador.
	 *
	 * 2 - O estorno só é possivel com a autenticação do usuário titular do cartão,
	 * ou de um operador autorizado a realizar esta operação.
	 *
	 * Nesta classe é criada variáveis de Sessão utilizadas na Classe ResumoCompraController.
	 *
	 * @link https://www.catraca.unilab.edu.br/docs/index.html
	 */
	public function telaGuiche() {
		$controller = new GuicheController ();
		$this->view = new GuicheView ();
		$unidade = new Unidade ();
		$dao = new DAO ();
		$sessao = new Sessao ();
		$idDoUsuario = $sessao->getIdUsuario ();
		
		// Variáveis de sessão utilizadas na Classe ResumoCompraController
		$_SESSION ['nome_usuario'] = null;
		$_SESSION ['tipo_usuario'] = null;
		$_SESSION ['valor_inserido'] = null;
		$_SESSION ['novo_saldo'] = null;
		$_SESSION ['transacao'] = null;
		$_SESSION ['cartao'] = null;
		$_SESSION ['saldo_anterior'] = null;
		$_SESSION ['autorizado'] = false;
		
		/*
		 * Preenche a tabela 'Descrição da Operação'
		 * apenas com dados das operações realizadas no dia
		 * corrente do operador logado. *
		 */
		
		$dataInicio = date ( 'Y-m-d' );
		$dataFim = date ( 'Y-m-d' );
		
		$data1 = $dataInicio . ' 00:00:00';
		$data2 = $dataFim . ' 23:59:59';
		
		$sqlTransacao = "SELECT cliente.usua_nome cliente,* FROM transacao
		INNER JOIN usuario
		on transacao.usua_id = usuario.usua_id
		INNER JOIN usuario as cliente
		ON cliente.usua_id = transacao.usua_id1
		WHERE (tran_data BETWEEN '$data1' AND '$data2') 
		AND usuario.usua_id = $idDoUsuario ORDER BY tran_id DESC ";
		
		$listaDescricao = $dao->getConexao ()->query ( $sqlTransacao );
		$this->view->formDescricao ( $listaDescricao );
		
		/*
		 * Soma os campos de valores.
		 */
		$valorTotal = 0;
		$result = $dao->getConexao ()->query ( $sqlTransacao );
		foreach ( $result as $linha ) {
			
			$valor = $linha ['tran_valor'];
			floatval ( $valor );
			if ($linha) {
				$valorTotal = $valorTotal + $valor;
			}
		}
		echo '					<h2>Saldo em Caixa: R$ ' . number_format ( $valorTotal, 2, ',', '.' ) . ' </h1>
							<div class="sete borda">';
		
		$sqlUsuario = "SELECT * FROM usuario WHERE usua_id = '$idDoUsuario'";
		$result = $dao->getConexao ()->query ( $sqlUsuario );
		foreach ( $result as $linha ) {
			echo '	<span class="icone-user"> Operador: ' . ucwords ( strtolower ( htmlentities ( $linha ['usua_nome'] ) ) ) . '</span>';
		}
		echo '	</div>
		</div>';
		
		$this->view->formBuscarCartao ();
		
		/*
		 * Realiza a pesquisa pelo numero do cartão identificando se existe vinculo ativo.
		 */
		
		if (isset ( $_GET ['cartao'] )) {
			if (strlen ( $_GET ['cartao'] ) > 3) {
				$cartao = new Cartao ();
				$cartao->setNumero ( $_GET ['cartao'] );
				$numeroCartao = $cartao->getNumero ();
				@$_SESSION ['cartao'] = $_GET ['cartao'];
				$sqlVerificaNumero = "SELECT * FROM usuario
				INNER JOIN vinculo ON vinculo.usua_id = usuario.usua_id
				LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
				LEFT JOIN tipo ON cartao.tipo_id = tipo.tipo_id
				WHERE cartao.cart_numero = '$numeroCartao'";
				$result = $dao->getConexao ()->query ( $sqlVerificaNumero );
				$usuario = new Usuario ();
				$idCartao = 0;
				$i = 0;
				$tipo = new Tipo ();
				$vinculoDao = new VinculoDAO ( $dao->getConexao () );
				$vinculo = new Vinculo ();
				foreach ( $result as $linha ) {
					$i++;
					$idDoVinculo = $linha ['vinc_id'];
					$tipo->setNome ( $linha ['tipo_nome'] );
					$tipo->setValorCobrado ( $linha ['tipo_valor'] );
					$cartao->setId ( $linha ['cart_id'] );
					$cartao->setCreditos ( $linha ['cart_creditos'] );
					$usuario->setNome ( $linha ['usua_nome'] );
					$usuario->setId ( $linha ['usua_id'] );
					$usuario->setLogin ( $linha ['usua_login'] );
					$usuario->setIdBaseExterna ( $linha ['id_base_externa'] );
					$vinculo->setAvulso ( $linha ['vinc_avulso'] );
					$vinculo->setResponsavel ( $usuario );
					$idCartao = $linha ['cart_id'];
					$avulso = $linha ['vinc_avulso'];
					if ($avulso) {
						$usuario->setNome ( 'Avulso' );
					}
					$usuarioDao = new UsuarioDAO ();
					$usuarioDao->retornaPorIdBaseExterna ( $vinculo->getResponsavel () );
					break;
					
					
				}				
				
				if ($idCartao && $_GET ['cartao'] != '') {
					$idBeneficiado = $usuario->getId ();
					$vinculo->setId($idDoVinculo);
					$cartao->setId($idCartao);
					$vinculoDao->vinculoPorId ( $vinculo );					
					$catracaVirtualDao = new CatracaVirtualDAO();
					$dao = new DAO();
					
					if (!$catracaVirtualDao->verificaVinculo ( $vinculo )) {					
						if (($i != 0) && ! $vinculoDao->usuarioJaTemVinculo ( $usuario ) && ! $vinculo->isAvulso () && $vinculo->getResponsavel ()->verificaSeAtivo ()) {
							$daqui3Meses = date ( 'Y-m-d', strtotime ( "+90 days" ) ) . 'T' . date ( 'G:00:01' );
							$vinculo->setFinalValidade ( $daqui3Meses );
							$vinculoDao->atualizaValidade ( $vinculo );
						}
					}
					
					if (! $vinculo->isActive () ) {												
						
						
						$creditos = $vinculo->getCartao()->getCreditos ();
						
						if ($creditos <= 0){
							$this->view->mensagem('erro', 'Cartão sem vínculo ativo e sem saldo para estornar.');
							return ;
						}
						$usuarioDao->retornaPorIdBaseExterna ( $usuario );
						$this->view->formConsulta($usuario, $tipo, $cartao);
						echo '	<form class="formulario em-linha" method="post">
									<label>Valor a ser estornado:
										<input type="text" disabled="disabled"  value="R$ '.number_format ($creditos, 2, ',', '.' ).'">
									</label>
									<input type="hidden" name="creditos" value="'.$creditos.'"/>
									<input type="submit" value="Estornar" name="estorno" class="b-erro">
								</form>';
						
						if (isset($_POST['estorno'])){
							$this->view->formEstorno($creditos, $usuario->getLogin());
						}
						
						if (isset($_POST['estornar'])){
							$autorizado = $this->verificaUsuario($usuario, $_POST ['login'], $_POST ['senha']);
						}
						
						if (isset ( $autorizado )) {							
							
							$idCartao = $vinculo->getCartao ()->getId ();
							$idUsuario = $usuario->getId ();
							$dataTimeAtual = date ( "Y-m-d G:i:s" );
							$valorEstorno = $_POST['creditos'];
							$novoValor = $vinculo->getCartao()->getCreditos() - $valorEstorno;
						
							$dao->getConexao ()->beginTransaction ();
						
							$sql = "UPDATE cartao set cart_creditos = $novoValor WHERE cart_id = $idCartao";
						
							$sql2 = "INSERT into transacao(tran_valor, tran_descricao, tran_data, usua_id,usua_id1)
							VALUES( -1*$valorEstorno , 'Estorno de valores' ,'$dataTimeAtual', $idDoUsuario, $idBeneficiado)";
						
							// echo $sql;
							if (! $dao->getConexao ()->exec ( $sql )) {
								$dao->getConexao ()->rollBack ();
								$this->view->mensagem ( 'erro', 'Erro ao inserir os creditos.' );
								return false;
							}
							if (! $dao->getConexao ()->exec ( $sql2 )) {
								$dao->getConexao ()->rollBack ();
								$this->view->mensagem ( 'erro', 'Erro ao inserir os creditos.' );
								return false;
							}
						
							$dao->getConexao ()->commit ();
							echo '<div id="msgconfirmado">';
							$this->view->mensagem ( 'sucesso', 'Valor estornado com sucesso!' );
							$_SESSION ['autorizado'] = $autorizado;
							echo '</div>';
							echo '<meta http-equiv="refresh" content="3; url=.\?pagina=guiche">';
						}
						return ;
						
						$this->view->mensagem ( 'erro', 'O vinculo n&atildeo est&aacute ativo.' );					
						
						
					} else {
					    $usuarioDao->retornaPorIdBaseExterna ( $usuario );
						$this->view->formConsulta ( $usuario, $tipo, $cartao );
						$this->view->formInserirValor ();
						
						$_SESSION ['nome_usuario'] = ucwords ( strtolower ( htmlentities ( $usuario->getNome () ) ) );
						$_SESSION ['tipo_usuario'] = $tipo->getNome ();
						$_SESSION ['saldo_anterior'] = $vinculo->getCartao ()->getCreditos ();
						
						// $_SESSION['confirmado'] = "aguarde";
						// $_SESSION['refeicoes_restante'] = "";
						
						/*
						 * Insere ou estorna os creditos do usuario pesquisado com vinculo ativo.
						 */
						
						if (isset ( $_GET ['valor'] )) {
							
							$cartao->setCreditos ( $_GET ['valor'] );
							$valorVendido = $cartao->getCreditos ();
							$login = $usuario->getLogin ();
							$valorAnt = $vinculo->getCartao()->getCreditos ();
							$valorVendido = $cartao->getCreditos ();
							$novoValor = $valorAnt + $valorVendido;
							$valor = number_format ( $valorVendido, 2, ',', '.' );
							
							$_SESSION ['valor_inserido'] = $valorVendido;
							$_SESSION ['novo_saldo'] = $novoValor;
							
							if ($valorVendido == 0) {
								$this->view->mensagem ( "erro", "Valor Inválido! Digite novamente." );
								return;
							}
							
							if ($valorVendido < 0) {
								if ($valorAnt <= 0) {
									echo '<div id="msgconfirmado">';
									$this->view->mensagem ( "erro", "Saldo Insuficiente para realizar estorno!" );
									echo '</div>';
									echo '<meta http-equiv="refresh" content="3; url=.\?pagina=guiche">';
									return;
								} else if ($novoValor < 0) {
									echo '<div id="msgconfirmado">';
									$this->view->mensagem ( "erro", "Saldo Insuficiente para realizar estorno!" );
									echo '</div>';
									echo '<meta http-equiv="refresh" content="3; url=.\?pagina=guiche">';
									return;
								}
								$estorno = true;
							} else {
								$this->view->mensagem ( "ajuda", "Deseja inserir R\$ $valor?" );
								$tipoTransacao = 'Venda de Créditos';
								$tipo = "sucesso";
								$mensagem = "Valor inserido com sucesso.";
								$estorno = false;
							}
							
							if ($estorno) {
								$this->view->formEstorno($valor, $login);
							} else {
								echo '	<form method="post" class="formulario">
											<input type="number" name="confirmar" id="confirmar">										
									    </form>';								
								if (isset ( $_POST ['confirmar'] ) && $_POST ['confirmar'] != "") {
									
									$cartaoConfirma = $_POST ['confirmar'];
									if ($cartaoConfirma == $cartao->getNumero ()) {
										$autorizado = true;
									} else {
										$this->view->mensagem ( "erro", "Número do cartão diferente." );
										echo '<meta http-equiv="refresh" content="3; url=.\?pagina=guiche">';
									}
								}
							}
							
							if (isset ( $_POST ['estornar'] )) {
								$usuarioDao = new UsuarioDAO ();
								$usuario2 = new Usuario ();
								$usuario2->setLogin ( $login = $_POST ['login'] );
								$usuario2->setSenha ( $senha = $_POST ['senha'] );
								if ($usuarioDao->autentica ( $usuario2 )) {
									$idUsuario1 = $usuario->getId ();
									$idUsuario2 = $usuario2->getId ();
									if ($idUsuario1 == $idUsuario2 || $usuario2->getNivelAcesso () >= 2) {
										$tipo = "ajuda";
										$mensagem = "Valor estornado com sucesso!";
										$tipoTransacao = 'Estorno de valores';
										$autorizado = true;
									} else {
										echo '<div id="msgconfirmado">';
										$this->view->mensagem ( "erro", "Este cartão não pertence a este usuario!" );
										echo '</div>';
										echo '<meta http-equiv="refresh" content="3; url=.\?pagina=guiche">';
									}
								} else {
									echo '<div id="msgconfirmado">';
									$this->view->mensagem ( "erro", "Usuario ou Senha Inválidos!" );
									echo '</div>';
									echo '<meta http-equiv="refresh" content="3; url=.\?pagina=guiche">';
									return;
								}
							}
														
							if (isset ( $autorizado )) {
								
								$idCartao = $vinculo->getCartao ()->getId ();
								$idUsuario = $usuario->getId ();
								$dataTimeAtual = date ( "Y-m-d G:i:s" );
								
								$dao->getConexao ()->beginTransaction ();
								
								$sql = "UPDATE cartao set cart_creditos = $novoValor WHERE cart_id = $idCartao";
								
								$sql2 = "INSERT into transacao(tran_valor, tran_descricao, tran_data, usua_id,usua_id1)
								VALUES($valorVendido, '$tipoTransacao' ,'$dataTimeAtual', $idDoUsuario, $idBeneficiado)";
								
								// echo $sql;
								if (! $dao->getConexao ()->exec ( $sql )) {
									$dao->getConexao ()->rollBack ();
									$this->view->mensagem ( 'erro', 'Erro ao inserir os creditos.' );
									return false;
								}
								if (! $dao->getConexao ()->exec ( $sql2 )) {
									$dao->getConexao ()->rollBack ();
									$this->view->mensagem ( 'erro', 'Erro ao inserir os creditos.' );
									return false;
								}
								
								$dao->getConexao ()->commit ();
								echo '<div id="msgconfirmado">';
								$this->view->mensagem ( $tipo, $mensagem );
								$_SESSION ['autorizado'] = $autorizado;
								echo '</div>';
								
								/**
								 * #### EXPERIMENTAL ####
								 * FUNCIONALIDADE DE ENVIO DE MENSAGENS PUSH PARA O APP ANDROID
								 * 
								 * Chamada da funcao de envios de notificacoes pela Api FireBase
								 * Recarga e Estorno de creditos do cartao
								 */
								NotificacaoBackground::executaPidGuiche($idUsuario, 'guiche', $valorVendido, $novoValor);
								
								echo '<meta http-equiv="refresh" content="3; url=.\?pagina=guiche">';
							}
						}
					}

				} else {
					$_SESSION ['cartao'] = null;
					$this->view->mensagem ( 'erro', 'Cart&atildeo sem vinculo v&aacutelido.' );
					echo '<meta http-equiv="refresh" content="3; url=.\?pagina=guiche">';
				}
			}
		}
	}
	
	public function verificaUsuario(Usuario $usuario1, $loginUser2, $senhaUser2){
		
		$usuarioDao = new UsuarioDAO ();
		$usuario2 = new Usuario ();
		$usuario2->setLogin ( $loginUser2 );
		$usuario2->setSenha ( $senhaUser2 );
		if ($usuarioDao->autentica ( $usuario2 )) {
			$idUsuario1 = $usuario1->getId ();
			$idUsuario2 = $usuario2->getId ();
			if ($idUsuario1 == $idUsuario2 || $usuario2->getNivelAcesso () >= 2) {
				$tipo = "ajuda";
				$mensagem = "Valor estornado com sucesso!";
				$tipoTransacao = 'Estorno Aluno Formado';
				return true;
			} else {
				echo '<div id="msgconfirmado">';
				$this->view->mensagem ( "erro", "Este cartão não pertence a este usuario!" );
				echo '</div>';
				echo '<meta http-equiv="refresh" content="3; url=.\?pagina=guiche">';
			}
		} else {
			echo '<div id="msgconfirmado">';
			$this->view->mensagem ( "erro", "Usuario ou Senha Inválidos!" );
			echo '</div>';
			echo '<meta http-equiv="refresh" content="3; url=.\?pagina=guiche">';
			return false;
		}
		return false;
	}
	
}

?>