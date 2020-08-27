<?php
/**
 * 
 * @author Jefferson Uchoa Ponte
 *
 */

class NivelAcessoController{
	private $view;
	public static function main($nivelDeAcesso){
		
		switch ($nivelDeAcesso){
			case Sessao::NIVEL_ADMIN:
				$controller = new NivelAcessoController();
				$controller->telaCartao();
				break;

			case Sessao::NIVEL_SUPER:
				$controller = new NivelAcessoController();
				$controller->telaCartao();
				break;
			default:
				UsuarioController::main ( $nivelDeAcesso );
				break;
		}
	}
	
	
	public function telaCartao(){
		$this->view = new NivelAcessoView();
		echo '<div class="conteudo"> <div class = "simpleTabs">
		        <ul class = "simpleTabsNavigation">
				
					<li><a href="#">Identifica&ccedil;&atilde;o</a></li>
					<li><a href="#">Cadastro</a></li>
					
		        </ul>
		        <div class = "simpleTabsContent">';
		
		$this->telaIdentificacao();
		echo '	</div>
						
						
				<div class = "simpleTabsContent">';
		$this->telaCadastro();
		echo '	</div>		
						
		    </div></div>';
		
		
		
	}

	public function telaIdentificacao(){
		$dao = new DAO();
		if(isset($_GET['usua_id']) && isset($_GET['novo_nivel'])){
			$usuarioDao = new UsuarioDAO($dao->getConexao());
			$usuario = new Usuario();
			$usuario->setIdBaseExterna($_GET['usua_id']);
			$usuarioDao->preenchePorIdBaseExterna($usuario);
			
			
			
			if(!isset($_POST['certeza'])){
				
				$this->view->formAlteraNivel($usuario);
				
			}else{
				$usuario->setNivelAcesso($_GET['novo_nivel']);
				if($usuarioDao->alteraNivelDeAcesso($usuario)){
					$this->view->mostraSucesso("Nível Alterado com Sucesso!");
					
				}else{
					$this->view->mostraSucesso("Erro na tentativa de alterar nível.");
						
					
				}
				echo '<meta http-equiv="refresh" content="4; url=.\?pagina=nivel_acesso">';
				
			}
			return;
		}
		
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
					WHERE cartao.cart_numero = '$numeroCartao'
						";
				
				$result = $dao->getConexao()->query($sqlVerificaNumero);
				$idCartao = 0;
				$usuario = new Usuario();
				
				$tipo = new Tipo();
				$vinculoDao = new VinculoDAO($dao->getConexao());
				$vinculo = new Vinculo();
				
				foreach($result as $linha){
					$idDoVinculo = $linha['vinc_id'];
					$tipo->setNome($linha['tipo_nome']);
					$usuario->setNome($linha['usua_nome']);
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
					
					echo '<div class="borda"><h1>'.ucwords(strtolower(htmlentities($usuario->getNome()))).'. Tipo: '.$tipo->getNome();
					$strNivelAcesso = "Padr&atilde;o";
					switch ($vinculo->getResponsavel()->getNivelAcesso()){
						case Sessao::NIVEL_ADMIN: 
							$strNivelAcesso =  " Administrador";
							break;
						case Sessao::NIVEL_SUPER:
							$strNivelAcesso = "Super Usu&aacute;rio";
							break;
						case Sessao::NIVEL_GUICHE:
							$strNivelAcesso = "Guich&ecirc;";
							break;
						case Sessao::NIVEL_CATRACA_VIRTUAL_ORFA;
							$strNivelAcesso = "Catraca Órfã;";
							break;
						default:
							$strNivelAcesso = "Padr&atilde;o";
							break;
							
					}
					echo ' - Nivel de Acesso:  '. $strNivelAcesso;
					
					
					echo '</h1>';
					echo '<a class="botao b-primario" href="?pagina=nivel_acesso&usua_id='.$vinculo->getResponsavel()->getIdBaseExterna().'&novo_nivel='.Sessao::NIVEL_COMUM.'">Tornar Padr&atilde;o</a>';
					echo '<a class="botao b-sucesso" href="?pagina=nivel_acesso&usua_id='.$vinculo->getResponsavel()->getIdBaseExterna().'&novo_nivel='.Sessao::NIVEL_GUICHE.'">Tornar Guichê</a>';
					echo '<a class="botao b-secundario" href="?pagina=nivel_acesso&usua_id='.$vinculo->getResponsavel()->getIdBaseExterna().'&novo_nivel='.Sessao::NIVEL_CADASTRO.'">Tornar Cadastro</a>';
					echo '<a class="botao b-sucesso" href="?pagina=nivel_acesso&usua_id='.$vinculo->getResponsavel()->getIdBaseExterna().'&novo_nivel='.Sessao::NIVEL_ADMIN.'">Tornar Administrador</a>';
					echo '<a class="botao b-erro" href="?pagina=nivel_acesso&usua_id='.$vinculo->getResponsavel()->getIdBaseExterna().'&novo_nivel='.Sessao::NIVEL_RELATORIO.'">Somente Relatorios</a>';
					echo '<a class="botao b-secundario" href="?pagina=nivel_acesso&usua_id='.$vinculo->getResponsavel()->getIdBaseExterna().'&novo_nivel='.Sessao::NIVEL_CATRACA_VIRTUAL.'">Tornar Catraca Virtual</a>';
					echo '<a class="botao b-secundario" href="?pagina=nivel_acesso&usua_id='.$vinculo->getResponsavel()->getIdBaseExterna().'&novo_nivel='.Sessao::NIVEL_CATRACA_VIRTUAL_ORFA.'">Tornar Catraca Órfã</a>';
						
					$sessao = new Sessao();
					if($sessao->getNivelAcesso() == Sessao::NIVEL_SUPER)
						echo '<a class="botao b-erro" href="?pagina=nivel_acesso&usua_id='.$vinculo->getResponsavel()->getIdBaseExterna().'&novo_nivel='.Sessao::NIVEL_SUPER.'">Tornar Super Usu&aacute;rio</a>';
					
					
					
					
					
					if(file_exists('fotos/'.$usuario->getIdBaseExterna().'.png')){
							
						echo '<img width="300" src="fotos/'.$usuario->getIdBaseExterna().'.png" />';
							
					}
					
					if(!$vinculo->isActive()){
						echo '<p>O vinculo não está ativo </p><br>
								<a href="?pagina=cartao&numero_cartao='.$_GET['numero_cartao'].'&cartao_renovar=1" class="botao">Renovar</a> ';
					
						if(isset($_GET['cartao_renovar'])){
							if(isset($_POST['certeza'])){
								$usuarioDao = new UsuarioDAO();
								
								
								$usuarioDao->retornaPorIdBaseExterna($usuario);
								
								if($vinculoDao->usuarioJaTemVinculo($usuario))
								{
									$this->view->mostraSucesso("Esse usuário já possui vínculo válido.");
									echo '<meta http-equiv="refresh" content="4; url=.\?pagina=cartao&selecionado=' . $usuario->getIdBaseExterna() . '">';
									return;
								}
								if($vinculo->isAvulso()){
							
									$this->view->mostraSucesso("Não existe renovação de vínculos avulsos!");
									echo '<meta http-equiv="refresh" content="4; url=.\?pagina=cartao&selecionado=' . $usuario->getIdBaseExterna() . '">';
									return;
								}
								
								
								
								if(!$this->verificaSeAtivo($usuario)){
									$this->view->mostraSucesso("Esse usuário possui um problema quanto ao status!");
									echo '<meta http-equiv="refresh" content="4; url=.\?pagina=cartao&selecionado=' . $usuario->getIdBaseExterna() . '">';
									return;
							
								}
									
								$daqui3Meses = date ( 'Y-m-d', strtotime ( "+60 days" ) ) . 'T' . date ( 'G:00:01' );
								$vinculo->setFinalValidade($daqui3Meses);
									
								if($vinculoDao->atualizaValidade($vinculo)){
									$this->view->mostraSucesso("Vínculo Atualizado com Sucesso!  ");
								}else{
									$this->view->mostraSucesso("Erro ao tentar renovar vínculo.  ");
									
								}
								echo '<meta http-equiv="refresh" content="4; url=.\?pagina=cartao&selecionado=' . $usuario->getIdBaseExterna() . '">';
								return;
							}
							
							$this->view->formConfirmacaoRenovarVinculo();
						}
					}
					echo '
							
							
							</div>';
				}else
				{
	
					echo '<div class="borda"><h1>Cartão Não possui Vínculo Válido.</h1></div>';
					
				}
				
			
			}
		}
		
		
	}
	public function telaCadastro(){
		$this->view->formBuscaUsuarios();
		$dao = new DAO();
		$usuarioDao2 = new UsuarioDAO($dao->getConexao());
		if(isset($_GET['id_usuario']) && isset($_GET['novo_nivel'])){

			$usuario = new Usuario();
			$usuario->setIdBaseExterna($_GET['id_usuario']);
			$usuarioDao2->preenchePorIdBaseExterna($usuario);
				
			
				
			if(!isset($_POST['certeza'])){
		
				$this->view->formAlteraNivel($usuario);
		
			}else{
				$usuario->setNivelAcesso($_GET['novo_nivel']);
				if($usuarioDao2->alteraNivelDeAcesso($usuario)){
					$this->view->mostraSucesso("Nível Alterado com Sucesso!");
						
				}else{
					$this->view->mostraSucesso("Erro na tentativa de alterar nível.");
		
						
				}
				echo '<meta http-equiv="refresh" content="4; url=.\?pagina=nivel_acesso&selecionado='.$usuario->getIdBaseExterna().'">';
				return;
			}
			return;
		}
		if (isset ( $_GET ['selecionado'] )) {
			
			$idDoSelecionado = $_GET['selecionado'];
			$usuarioDao = new UsuarioDAO();
			$usuario = new Usuario();
			$usuario->setIdBaseExterna($idDoSelecionado);
			
			
				
			$usuarioDao->retornaPorIdBaseExterna($usuario);
			
			$usuarioDao2->preenchePorIdBaseExterna($usuario);
			$this->view->mostraSelecionado($usuario);
			

			if($usuario->getNivelAcesso() == null)
				return;
			echo '<a class="botao b-primario" href="?pagina=nivel_acesso&id_usuario='.$usuario->getIdBaseExterna().'&novo_nivel='.Sessao::NIVEL_COMUM.'">Tornar Padr&atilde;o</a>';
			echo '<a class="botao b-erro" href="?pagina=nivel_acesso&id_usuario='.$usuario->getIdBaseExterna().'&novo_nivel='.Sessao::NIVEL_GUICHE.'">Guiche</a>';
			echo '<a class="botao b-secundario" href="?pagina=nivel_acesso&id_usuario='.$usuario->getIdBaseExterna().'&novo_nivel='.Sessao::NIVEL_CADASTRO.'">Somente Cadastro</a>';
			echo '<a class="botao b-sucesso" href="?pagina=nivel_acesso&id_usuario='.$usuario->getIdBaseExterna().'&novo_nivel='.Sessao::NIVEL_ADMIN.'">Tornar Administrador</a>';
			echo '<a class="botao b-primario" href="?pagina=nivel_acesso&id_usuario='.$usuario->getIdBaseExterna().'&novo_nivel='.Sessao::NIVEL_POLIVALENTE.'">Polivalente</a>';
			echo '<a class="botao b-erro" href="?pagina=nivel_acesso&id_usuario='.$usuario->getIdBaseExterna().'&novo_nivel='.Sessao::NIVEL_RELATORIO.'">Somente Relatorios</a>';
			echo '<a class="botao b-secundario" href="?pagina=nivel_acesso&id_usuario='.$usuario->getIdBaseExterna().'&novo_nivel='.Sessao::NIVEL_CATRACA_VIRTUAL.'">Catraca Virtual</a>';
			echo '<a class="botao b-secundario" href="?pagina=nivel_acesso&id_usuario='.$usuario->getIdBaseExterna().'&novo_nivel='.Sessao::NIVEL_CATRACA_VIRTUAL_ORFA.'">Catraca Órfã</a>';
			
			$sessao = new Sessao();
			if($sessao->getNivelAcesso() == Sessao::NIVEL_SUPER)
				echo '<a class="botao b-erro" href="?pagina=nivel_acesso&id_usuario='.$usuario->getIdBaseExterna().'&novo_nivel='.Sessao::NIVEL_SUPER.'">Tornar Super Usu&aacute;rio</a>';
		
			
			
		}
		

		if (isset ( $_GET ['nome'] )) {
			$usuarioDao = new UsuarioDAO();
			$listaDeUsuarios = $usuarioDao->pesquisaNoSigaa( $_GET ['nome']);
			$this->view->mostraResultadoBuscaDeUsuarios($listaDeUsuarios);
			$usuarioDao->fechaConexao();
		}
		
		
	}
	public function verificaSeAtivo(Usuario $usuario){
		if(strtolower (trim($usuario->getStatusServidor())) == 'ativo'){
			
			return true;
		}
		if(strtolower (trim($usuario->getStatusDiscente())) == 'ativo' || strtolower (trim($usuario->getStatusDiscente())) == 'ativo - formando' || strtolower (trim($usuario->getStatusDiscente())) == 'ativo - graduando'){
			return true;	
		}
		
		if(strtolower (trim($usuario->getTipodeUsuario())) == 'terceirizado' || strtolower (trim($usuario->getTipodeUsuario())) == 'outros'){
			return true;
		}

		return false;
	}
	
	
	
	
}



?>