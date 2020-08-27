<?php

class ResumoCompraController{
	
	public static function main($nivel){
	
		switch ($nivel){
			case Sessao::NIVEL_SUPER:
				$resumo = new ResumoCompraController();
				$resumo->telaResumo();
				break;
			case Sessao::NIVEL_ADMIN:
				$resumo = new ResumoCompraController();
				$resumo->telaResumo();
				break;
			case Sessao::NIVEL_GUICHE:
				$resumo = new ResumoCompraController();
				$resumo->telaResumo();
				break;
			case Sessao::NIVEL_POLIVALENTE:
				$resumo = new ResumoCompraController();
				$resumo->telaResumo();
				break;
			default:
				UsuarioController::main ( $nivel );
				break;
		}
	
	
	}
	
	public function telaResumo(){
		
		$transacao = @$_SESSION['transacao'];
		$cartao = @$_SESSION['cartao'];
		$usuario = @$_SESSION['nome_usuario'];
		$tipo = @$_SESSION['tipo_usuario'];
		$valorInserido = @$_SESSION['valor_inserido'];
		$novoSaldo = @$_SESSION['novo_saldo'];
		$saldoAtual = @$_SESSION['saldo_anterior'];
		$autorizado = @$_SESSION['autorizado'];
		
		echo '	<div id="resumo">
					<div class="doze colunas borda relatorio">				
						<div class="doze colunas">								
							
				
				
							<div class="duas colunas">
								<a href="http://www.unilab.edu.br">
									<img class="imagem-responsiva centralizada" src="img/logo-dti-preto.png" style="width:12cm;height:2cm;">
								</a>
							</div>
	
							<div class="oito colunas">
								<h2 style="font-size:36px">Restaurante Universitário</h2>
							</div>
							<div class="duas colunas">
								<img class="imagem-responsiva centralizada" src="img/logo-unilab2.png" style="width:6cm;height:2cm">
							</div>
								<hr class="um"><br>
							</div>							';		
		
		
		if ($cartao != ""){			
		echo '				<div class="doze colunas dados-usuario">				
								<h1 id="titulo-dois" class="centralizado" style="font-size:28px">Dados do Usuário</h1><br>				
								<hr class="um">									
							</div>
							<div class="doze colunas fundo-cinza1" style="font-size:28px;">
								<p><strong>Código da Operação: </strong>'.$transacao.'</p>
								<p><strong>Cartão: </strong>'.$cartao.'</p>
								<p><strong>Cliente: </strong>'.$usuario.'</p>
								<p><strong>Tipo Usuario: </strong>'.$tipo.'</p>
								<p><strong>Saldo Atual: R$ </strong>'.number_format($saldoAtual, 2,',','.').'</p>					
							</div>';
		}else{			
			echo'				
					<div class="doze colunas">						
						<div class="resumo">
							<span class="no-centro texto-azul2 centralizado">Guichê de Atendimento</span>
							<img class="imagem-responsiva" src="img/Simbolo_da_UNILAB.png" alt="">					
						</div>
						
						<img class="qr_code" src="app/imagens/qrcode_app.jpg">

					</div>
								
				';			
		}
		
		
		
		if ($valorInserido !=""){
		echo'					<div class="oito colunas">
								<span class="fundo-verde2 texto-branco" style="font-size:60px;">Créditos a Inserir: R$ '.number_format($valorInserido, 2,',','.').'</span>
							</div>
										
							<div class="doze colunas">
								<span style="font-size:36px;">Novo Saldo: R$ '.number_format($novoSaldo, 2,',','.').'</span>';
			if ($autorizado == false){				
				echo'			<span class="centralizado fundo-verde2 texto-branco" style="font-size:32px;"><strong>Verifique sua compra e passe o cartão para confirmar!</span>';
			}else{
				echo'			<span class="centralizado fundo-verde2 texto-branco" style="font-size:48px;"><strong>Créditos inseridos com sucesso!</span>';
			}
		}
			
			echo'			</div>
										
						</div>
					</div>

					
				
				';
		
	}
	
	
}

?>