<?php
/**
 * Nesta Classe estão contidos os Códigos HTML, responsáveis pela geração das Telas.
 * @author Alan Cleber Morais Gomes
 * @version 1.0
 * @copyright UNILAB - Universidade da Integracao Internacional da Lusofonia Afro-Brasileira.
 * @package View
 */
/**
 * Nesta Classe estão contidos os Códigos HTML
 * responsáveis por gerar os elementos e as telas do Guichê.
 * 
 * @link https://www.catraca.unilab.edu.br/docs/index.html
 */
class GuicheView {
	
	/**
	 * Esta Função gera o formulário responsável pela consulta do usuário pelo número do seu cartão.
	 *
	 * @link https://www.catraca.unilab.edu.br/docs/index.html
	 */
	public function formBuscarCartao() {
		echo '		<div class="cinco colunas">
						<form method="" class="formulario-organizado" >
							<input type="hidden" name="pagina" value="guiche" />
							<label for="cartao">
								N&uacutemero Cart&atildeo: <input type="number" name="cartao" id="cartao" autofocus>
							</label>
							<input type="submit" value="Pesquisar">
						<hr>
					</form>';
	}
	
	/**
	 * Esta Função gera a tabela contendo os dados dos Usuários que realizaram tranzações no guichê.
	 * Ela recebe um array contendo os dados dos usuários.
	 * Esta função tambem trabalha com variáveis de sessão,
	 * para determinar o código das transações.
	 *
	 * @param array $listaDescricao        	
	 * @link https://www.catraca.unilab.edu.br/docs/index.html
	 */
	public function formDescricao($listaDescricao) {
		@$_SESSION ['transacao'] = "";
		
		echo '	<div id="caixa"class="doze colunas borda">
					<!-- <h2 id="titulo-caixa" class="texto-branco fundo-azul2 centralizado">Venda de Cr&eacuteditos<br></h2> -->
						<div class="sete colunas">
							<div id="infovenda" class="fundo-cinza1">
		
								<div class="doze colunas">
									<h3 class="centralizado">Descri&ccedil&atildeo da Opera&ccedil&atildeo</h1><br>
										<table class="tabela quadro no-centro">
										    <thead>
												<tr>
												    <th>Cod</th>
												    <th>Valor</th>
												    <th>Descri&ccedil&atildeo</th>
												    <th>Data</th>
													<th>Hora</th>
													<th>Cliente</th>
												</tr>
											</thead>
											<tbody>';
		$i = 1;
		foreach ( $listaDescricao as $linha ) {
			echo '								<tr>
											        <td>' . $linha ['tran_id'] . '</td>
											       	<td>R$ ' . number_format ( $linha ['tran_valor'], 2, ',', '.' ) . '</td>
											        <td>' . $linha ['tran_descricao'] . '</td>
											        <td>' . date ( "d/m/Y", strtotime ( $linha ['tran_data'] ) ) . '</td>
													<td>' . date ( 'H:i:s', strtotime ( $linha ['tran_data'] ) ) . '</td>
											        <td>' . ucwords ( strtolower ( htmlentities ( $linha ['usua_nome'] ) ) ) . '</td>
											    </tr>';
			$i ++;
			$tran [] = $linha ['tran_id'];
		}
		
		@$_SESSION ['transacao'] = $tran [0] + 1;
		if (@$_SESSION ['transacao'] == 1) {
			@$_SESSION ['transacao'] = null;
		}
		
		echo '								</tbody>
										</table>
									</div>
								</div>';
	}
	
	/**
	 * Esta função exibi o formulário para a inserção ou estorno dos créditos,
	 * além de informar o troco do cliente.
	 *
	 * @link https://www.catraca.unilab.edu.br/docs/index.html
	 */
	public function formInserirValor() {
		echo '	<form method="" class="formulario-organizado" >
					<input type="hidden" name="pagina" value="guiche" />
					<label for="valor">
					Valor Comprado: <input type="number" name="valor" id="valor" step="0.01">
					</label>
					<label for="valorrec">
					Valor Recebido: <input type="number" name="valorrec" id="valorrec" step="0.01">
					</label>
					<hr>
					<h2>Troco: <output id="troco"></output></h2>
					<input type="hidden" name="cartao" value="' . $_GET ['cartao'] . '" />					
					<input type="submit" value="Finalizar" class="botao b-sucesso" name="finalizar" >
					<a href="?pagina=guiche" class="botao b-erro">Cancelar</a>
				</form>';
	}
	
	/**
	 * Esta função retorna os dados do Usuário consultado pelo seu cartão,
	 * são informado: O Tipo de Usuário, Seu Nome e Saldo.
	 *
	 * @param Usuario $usuario        	
	 * @param Tipo $tipo        	
	 * @param Cartao $cartao        	
	 * @link https://www.catraca.unilab.edu.br/docs/index.html
	 */
	public function formConsulta(Usuario $usuario, Tipo $tipo, Cartao $cartao) {
		echo '	<span>Usuario: '.ucwords(strtolower(htmlentities($usuario->getNome()))).'</span>
				<span>Tipo Usuario: '.$tipo->getNome().'</span>
				<span>Saldo: '.$cartao->getCreditos().'</span>
				<span>Valor Credito: '.$tipo->getValorCobrado().'</span>
				<hr>';
	}
	
	public function formEstorno($creditos, $login){
		echo '	<div id="mascara"></div>
					<div class="window borda" id="janela1">
						<a href="#" class="fechar">X Fechar</a>
						<h2 class="titulo">Por Favor digite sua Senha para confirmar.</h2>
						<hr class="um">
						<form class="formulario sequencial " method="post" id="formIndentificacao">
							<label>
								Login<input type="text"  name="login" value="'.$login.'" class="doze"/>
							</label>
							<label>
								Senha<input type="password" placeholder="Senha Sig" name="senha" class="doze" autofocus />
							</label>
							<input type="submit" name="estornar" value="Confirmar" class="doze" />
							<input type="hidden" name="creditos" value="'.$creditos.'"/>
						</form>';
		$this->mensagem ( "erro", 'Deseja estornar <strong>R$ '.$creditos.'</strong> ?');
		echo '	</div>';
	}
	
	/**
	 * Mostra um mesnsagem para o Usuário:
	 *
	 * @param string $tipo
	 *        	Tipo de mensagem: "-sucesso", "-erro", "-ajuda".
	 * @param string $texto
	 *        	Mensagem a ser exibida para o Usuário.
	 *        	
	 * @link https://www.catraca.unilab.edu.br/docs/index.html
	 */
	public function mensagem($tipo, $texto) {
		// Tipo = -sucesso, -erro, -ajuda
		echo '	<div class="alerta-' . $tipo . '">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">' . $texto . '</div>
				</div>
				';
	}
}

?>