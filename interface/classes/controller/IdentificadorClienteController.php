<?php

class IdentificadorClienteController{
	
	public static function main($nivel){
	
		switch ($nivel){
			case Sessao::NIVEL_SUPER:
				$identificar = new IdentificadorClienteController();
				$identificar->telaCliente();
				break;
			case Sessao::NIVEL_ADMIN:
				$identificar = new IdentificadorClienteController();
				$identificar->telaCliente();
				break;
			case Sessao::NIVEL_POLIVALENTE:
				$identificar = new IdentificadorClienteController();
				$identificar->telaCliente();
				break;
			case Sessao::NIVEL_CATRACA_VIRTUAL:
				$identificar = new IdentificadorClienteController();
				$identificar->telaCliente();
				break;
			default:
				UsuarioController::main ( $nivel );
				break;
		}
	
	
	}
	
	public function telaCliente(){				
		
		$numeroCartao = @$_SESSION['numero_cartao'];
		$nome = @$_SESSION['nome_usuario'];
		$tipo = @$_SESSION['tipo_usuario'];
		$confirma = @$_SESSION['confirmado'];
		$imagem = @$_SESSION['id_base_externa'];
		$refeicoes = @$_SESSION['refeicoes_restante'];	
				
		if (!file_exists('fotos/'.$imagem.'.png')){
			$imagem = "sem-imagem";
		}		
		
		echo'			<div id="usuario">				
							<div class="doze colunas borda relatorio">				
								<div class="doze colunas">									
									<div class="fundo-cliente">
										<img class="imagem-fundo-cliente" src="img/Simbolo_da_UNILAB.png" alt="">
									</div>									
									<div class="duas colunas">
										<a href="http://www.unilab.edu.br">
											<img class="imagem-responsiva centralizada" src="img/logo-unilab2.png" style="width:12cm;height:2cm;">
										</a>
									</div>
									<div class="oito colunas">
										<h2 style="font-size:48px">Restaurante Universitário</h2>
									</div>
									<div class="duas colunas">
										<img class="imagem-responsiva centralizada" src="img/logo-dti-preto.png" style="width:6cm;height:2cm">
									</div>
									<hr class="um"><br>
								</div>				
								<div class="doze colunas dados-usuario">				
									<h1 id="titulo-dois" class="centralizado" style="font-size:36px">Identificação do Usuario</h1><br>				
									<hr class="um">
									<div>
										<div id="imagem" class="quatro colunas zoom-cliente">
											<img id="img" src="fotos/'.$imagem.'.png" alt="">
										</div>					
										<div class="oito colunas">											
											<div id="informacao" class="fundo-cinza1" style="height:680px;width:1370px;">											
												<br><br><br><br>
												<div id="dados" class="dados" style="font-size:46px">';											
		if ($numeroCartao == ""){			
			echo '<span id="aproxime" style="padding:160px;">Por favor, aproxime o seu cartão.</span>';		
		}else {
			echo'									
													<span >Cartão: '.$numeroCartao.'</span><br>
													<span >Nome: '.$nome.'</span><br>
													<span >Tipo: '.$tipo.'</span><br>';
		}
		echo'									</div>
											</div>
										</div>
									</div>
								</div>
				
								<div class="doze colunas">
									<br>';
			
		if ($confirma == "confirmado"){				
		echo'						<div class="alerta-sucesso dez no-centro">
										<div class="icone icone-warning ix48"></div>
										<div class="titulo-alerta">Ok!</div>
										<div class="subtitulo-alerta" style="font-size:22px">Acesso Liberado!</div>
									</div>';		
		}elseif ($confirma == "aguarde"){
		echo'						<div class="alerta-ajuda dez no-centro">
										<div class="icone icone-checkmark ix48"></div>
										<div class="titulo-alerta">Aguarde!</div>
										<div class="subtitulo-alerta" style="font-size:22px">Estamos liberando seu acesso!</div>
									</div>';
		}
		echo'					</div>
				
							</div>
							</div>		
						</div>				
					</div>
				</div>';		
	}
	
}

?>