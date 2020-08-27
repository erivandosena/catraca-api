<?php
class PessoalView {
	
	public function formBuscaCartao(){
		echo '				
				<script>
  $(document).bind(\'autofocus_ready\', function() {
    if (!("autofocus" in document.createElement("input"))) {
      $("#numero_cartao").focus();
    }
  });
</script>
				
				<div class="doze colunas borda">
									<form method="get" action="" class="formulario em-linha" >
										<input type="hidden" name="pagina" value="pessoal" />
										<label for="numero_cartao">												
											Buscar por Número:<input type="number" name="numero_cartao" id="numero_cartao" autofocus /><br>
											<script>$(document).trigger(\'autofocus_ready\');</script>
											<input type="submit" value="Buscar" />
										</label>		
									</form>
				
									</div>';
	}
	public function mostraResultadoBuscaDeCartoes($cartoes) {
		echo '<div class="doze linhas">';
		echo '<br><h2 class="texto-preto">Busca de CartÃµes:</h2>';
		echo '</div>';
		echo '<div class="borda">
				<table class="tabela borda-vertical zebrada texto-preto">
				<thead>
					<tr>
			            <th>Numero</th>
			            <th>CrÃ©ditos</th>
			            <th>Tipo de UsuÃ¡rio</th>
			            <th>Selecionar</th>
			        </tr>
			    </thead>
			<tbody>';
		foreach ( $cartoes as $cartao) {
			$this->mostraLinhaDaBuscaCartao ( $cartao);
		}
		echo '</tbody></table></div>';
	}
	public function mostraLinhaDaBuscaCartao(Cartao $cartao) {
		echo '<tr>';
		echo '<td>' .  $cartao->getNumero() . '</a></td>';
		echo '<td>' .$cartao->getCreditos(). '</td>';
		echo '<td>' . $cartao->getTipo()->getNome() . '</td>';
		echo '<td class="centralizado"><a href="?pagina=cartao&cartaoselecionado=' . $cartao->getId() . '"><span class="icone-checkmark texto-verde2 botao" title="Selecionar"></span></a></td>';
		echo '</tr>';
	}
	public function mostraCartaoSelecionado(Cartao $cartao){
		echo '<div class="borda">
				Nome: ' . $cartao->getNumero() . '
				<br>Creditos: ' . $cartao->getCreditos() . '
				<br>Nome do Tipo: ' . $cartao->getTipo()->getNome(). '
				</div>';
	}
	public function formBuscaUsuarios() {
		echo '					<div class="borda">
									<form method="get" action="" class="formulario em-linha" >
		
										<label for="opcoes-1">
											<object class="rotulo texto-preto">Nome do Usuario: </object>
											<input class="texto-preto" type="text" name="nome" id="campo-texto" /><br>										
										</label>
										<input type="hidden" name="pagina" value="cartao" />
										<input type="submit" value="Buscar"/>
									</form>
								</div>';
	}
	public function formBuscaVinculo() {
		echo '					<div class="borda">
									<form method="get" action="" class="formulario em-linha" >
	
										<label for="parametro">Buscar por:</label>
											<select name="parametro" id="parametro" class="texto-preto">
												<option value="1">Nome do UsuÃ¡rio</option>
											</select>';
		if(isset($_GET['filtro_data']))
			echo '<input type="hidden" name="filtro_data" value="'.$_GET['filtro_data'].'"/>';
		echo '		
											<input class="texto-preto" type="text" name="busca_vinculos" id="busca_vinculos" /><br>
											<input type="submit" />
										
	
									</form>
									</div>';
	}
	public function formBuscaVinculoIsencao() {
		echo '					<div class="borda">
									<form method="get" action="" class="formulario em-linha" >
	
										<label for="parametro">Buscar por:</label>
											<select name="parametro" id="parametro" class="texto-preto">
												<option value="1">Nome do UsuÃ¡rio</option>
											</select>';
		if(isset($_GET['filtro_data_isen']))
			echo '<input type="hidden" name="filtro_data_isen" value="'.$_GET['filtro_data_isen'].'"/>';
		echo '
											<input class="texto-preto" type="text" name="busca_vinculos_isen" id="busca_vinculos_isen" /><br>
											<input type="submit" />
	
	
									</form>
									</div>';
	}
	public function filtroData(){
		
		$dataHoje = date ('Y-m-d') . 'T' . date ( 'H:00:01' );
		
		echo '					<div class="borda">
									<form method="get" action="" class="formulario em-linha" >
										<label for="filtro_data">Filtro de Data:</label>
											<input class="texto-preto" value="'.$dataHoje.'" type="datetime-local" name="filtro_data" id="filtro_data" /><br>';
		if(isset($_GET['busca_vinculos']))
			echo '<input type="hidden" name="busca_vinculos" value="'.$_GET['busca_vinculos'].'"/>';
		echo '
											<input type="submit" />
									</form>
									</div>';
	}
	public function filtroDataIsencao(){
	
		$dataHoje = date ('Y-m-d') . 'T' . date ( 'H:00:01' );
	
		echo '					<div class="borda">
									<form method="get" action="" class="formulario em-linha" >
										<label for="filtro_data_isen">Filtro de Data:</label>
											<input class="texto-preto" value="'.$dataHoje.'" type="datetime-local" name="filtro_data_isen" id="filtro_data_isen" /><br>';
		if(isset($_GET['busca_vinculos_isen']))
			echo '<input type="hidden" name="busca_vinculos_isen" value="'.$_GET['busca_vinculos_isen'].'"/>';
		echo '
											<input type="submit" />
									</form>
									</div>';
	}
	/**
	 *
	 * @param array $usuarios        	
	 */
	public function mostraResultadoBuscaDeUsuarios($usuarios) {
		echo '<h2 class="titulo">Resultado da busca:</h2>';
		echo '<div class="doze colunas">';		
		echo '<div class="borda">
				<table class="tabela borda-vertical zebrada texto-preto">
				<thead>
					<tr>
											            <th>Nome</th>
											            <th>CPF</th>
											            <th>Passaporte</th>
											            <th>Status Discente</th>
														<th>Status Servidor</th>
														<th>Tipo de Usuario</th>
											            <th>Selecionar</th>
											        </tr>
											    </thead>
												<tbody>';
		foreach ( $usuarios as $usuario ) {
			$this->mostraLinhaDaBusca ( $usuario );
		}
		echo '</tbody></table></div>';
	}
	
	
	
	public function mostraLinhaDaBusca(Usuario $usuario) {
		echo '<tr>';
		echo '<td>' . $usuario->getNome () . '</a></td>';
		echo '<td>' . $usuario->getCpf () . '</td>';
		echo '<td>' . $usuario->getPassaporte () . '</td>';
		echo '<td>' . $usuario->getStatusDiscente () . '</td>';
		echo '<td>' . $usuario->getStatusServidor () . '</td>';
		echo '<td>' . $usuario->getTipodeUsuario () . '</td>';
		echo '<td class="centralizado"><a href="?pagina=cartao&selecionado=' . $usuario->getIdBaseExterna () . '"><span class="icone-checkmark texto-verde2 botao" title="Selecionar"></span></a></td>';
		echo '</tr>';
	}
	

	public function mostraSelecionado(Usuario $usuario) {
		echo '<div class="doze colunas borda">';
		
		
				
		//Descomente esta linha para ativar o botão para cadastrar foto.	
// 		echo '<a href="?pagina=cartao&selecionado='.$_GET['selecionado'].'&foto=1" class="botao">Adicionar Foto</a>';
		
		
		if(isset($_GET['foto'])){
		
			echo '	<div class="doze colunas fotos">
					    <div class="quatro colunas">
							<div class="borda-foto">								
								<div class="foto-salva">';
	
			if(file_exists('fotos/'.$_GET['selecionado'].'.png')){
	
				echo '<img src="fotos/'.$_GET['selecionado'].'.png" />';
	
			}else{
	
				echo '<img src="img/camera.png" />';
			}
	
			echo '				</div>
							</div>
						</div>
	
					    <div class="quatro colunas">
							<div class="borda-foto">
								
								<img id="marcacao" src="img/avatar.png"/>
								
					        	<div class="tela">									
									<video width="320" height="200" id="video" autoplay></video>
					        	</div>
								<div class="centralizado">	
					                <button id="btnStart" class="botao icone-switch title="Ligar Câmera"></button>
					                <button id="btnStop" class="botao icone-cross"></button>
					                <button id="btnPhoto" class="botao icone-camera"></button>
									<button id="btnUser" class="botao icone-user"></button>
								</div>
							</div>
					    </div>
	
					    <div class="quatro colunas">
							<div class="borda-foto ">
								<div class="foto-salva">
							    	<canvas id="canvas" width="320" height="240"></canvas>									
							    </div>
								<div class="centralizado">
									<form class="formulario" id="formulario" enctype="multipart/form-data" action="enviar.php" method="POST" id="youform" name="youform">
								        <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
								        <input name="img64" id="img64" type="hidden" />
								        <input name="id_usuario" id="id_usuario" value="'.$_GET['selecionado'].'" type="hidden" />
								        <input class="" type="submit" value="Enviar arquivo" onsubmit="document.getElementById(\'img64\').value = img" />
							        </form>								   
							    </div>
					    	</div>
					    </div>
	
					</div>
					<hr class="um"/>
					';

// 			echo '
			
// 								<table  class="tabela borda-vertical zebrada texto-preto">
// 					<tr>
// 						<td>';
// 			if(file_exists('fotos/'.$_GET['selecionado'].'.png')){
			
// 				echo '<img width="300"  src="fotos/'.$_GET['selecionado'].'.png" />';
			
// 			}else{
			
// 				echo '<img width="300" src="img/camera.png" />';
			
// 			}
			
// 			echo '				</td>
			
			
// 						<td>
// 				<video  id="video" width="320" height="200" autoplay></video>
// 		            <section>
// 		                <button id="btnStart">Iniciar Video</button>
// 		                <button id="btnStop">Parar</button>
// 		                <button id="btnPhoto">Bater Foto</button>
// 		            </section>
// 						</td>
// 						<td>
// 				 <canvas id="canvas" width="320" height="240"></canvas>
// 				 <form id="formulario" enctype="multipart/form-data" action="enviar.php" method="POST" id="youform" name="youform">
// 	            <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
// 	            <input name="img64" id="img64" type="hidden" />
// 	            <input name="id_usuario" id="id_usuario" value="'.$_GET['selecionado'].'" type="hidden" />
	      
// 	            <input type="submit" value="Enviar arquivo" onsubmit="document.getElementById(\'img64\').value = img" />
// 	        </form>
// 						</td>
			
// 					</tr>
// 				</table>';
		}
		
		echo '<div class="doze colunas">				
				<table  class="tabela borda-vertical zebrada">
					<tr><th>Nome:</th><td> ' . $usuario->getNome () . '.</td>';
		echo '
             		</tr>
					<tr><th>Login:</th><td>'. $usuario->getLogin () .'.</td></tr>
					<tr><th>Identidade: </th><td>' . $usuario->getIdentidade () . '.</td></tr>
					<tr><th>CPF:</th><td>' . $usuario->getCpf () . '.</tr>
					<tr></td><th>Passaporte:</th><td>' . $usuario->getPassaporte() . '</td></tr>';
				

		if(strtolower (trim($usuario->getStatusServidor())) == 'ativo' && strtolower (trim($usuario->getCategoria())) == 'docente'){
			echo '<tr><th>Servidor </th><td>Docente</td></tr>
					<tr><th>SIAPE: </th><td>' . $usuario->getSiape().'</td></tr></table>';
		}
		else if(strtolower (trim($usuario->getCategoria())) == 'docente'){
			echo "<tr><th>Servidor </th><td>Docente Inativo</td></tr>";
			echo '<tr><th>SIAPE:</th> <td>' . $usuario->getSiape().'</td></tr>';
		}
		if(strtolower (trim($usuario->getStatusServidor())) == 'ativo' && strpos(strtolower (trim($usuario->getCategoria())), 'administrativo')){
			echo "<tr><th>Servidor </th><td>TAE</td></tr>";
			echo '<tr><th>SIAPE:</th><td>' . $usuario->getSiape().'</td></tr>';
		}else if(strpos(strtolower (trim($usuario->getCategoria())), 'administrativo' )){
			echo "<tr><th>Servidor </th><td>TAE Inativo</td></tr>";
			echo '<tr><th>SIAPE:</th><td>' . $usuario->getSiape().'</td></tr>';
			
		}
			
		
		
		if(strtolower (trim($usuario->getTipodeUsuario())) == 'aluno'){
			if(strtolower (trim($usuario->getStatusDiscente())) == 'ativo'){
				echo '<tr><th>Aluno </th><td>Ativo</td></tr>';
			}else{
				echo '<tr><th>Aluno </th><td>Inativo. </td></tr>';
				
			}
			echo '<tr><th>Nivel Discente:</th><td> ' . $usuario->getNivelDiscente().'</td></tr>';
			echo '<tr><th>Matricula:</th><td>'.$usuario->getMatricula().'</td></tr>';
			
		}else if(strtolower (trim($usuario->getStatusDiscente())) == 'ativo'){
				echo '<tr><th>Aluno </th><td>Ativo</td></tr>';
				echo '<tr><th>Nivel Discente:</th><td> ' . $usuario->getNivelDiscente().'</td></tr>';
				echo '<tr><th>Matricula:</th><td>'.$usuario->getMatricula().'</td></tr>';

		}


		
		if(strtolower (trim($usuario->getTipodeUsuario())) == 'terceirizado'){
			echo '<tr><th colspan=2>Terceirizado Sem Informação de Status</th></tr>';
		}
		
		
		echo '</table>
				</div>
				
				

				
				
				</div>';
	}
	
	
	
	public function mostraVinculos($lista, $podeRenovar = true){
		if(!count($lista))
		{
			echo '<div class="borda"><p>Nenhum ítem na lista</p></div>';
			return;
		}
		echo '<div class="borda">
						<table class="tabela borda-vertical zebrada texto-preto">';
		echo '<tr>
								<th>Avulso</th>
								<th>Responsável</th>
								<th>Tipo</th>
								<th>Cartão</th>
								<th>Validade</th>
								<th>Isento</th>
				';
		
		if($podeRenovar)
			echo '<th>-</th>';
		echo '
							</tr>';
		foreach ( $lista as $vinculo) {
			$this->mostraLinhaVinculo($vinculo, $podeRenovar);
			
		}
		echo '</table></div>';
		
	
	}
	
	
	public function mostraLinhaVinculo(Vinculo $vinculo, $podeRenovar = true){
		echo '<tr>';
		if($vinculo->isAvulso())
			echo 	'<td>Sim</td>';
		else 
			echo 	'<td>Não</td>';
		
		echo '
				<td>' . $vinculo->getResponsavel()->getNome(). '</td>
				<td>' .$vinculo->getCartao()->getTipo()->getNome() . '</td>
				<td>' . $vinculo->getCartao()->getNumero() . '</td>
				<td>' . date("d/m/Y G:i:s", strtotime($vinculo->getFinalValidade())) . '</td>';
		if($vinculo->getIsencao()->getId())
			echo '<td>Sim</td>';
		else
			echo '<td>Não</td>';
		
		if($vinculo->isActive())
			echo '<td><a href="?pagina=cartao&selecionado='.$vinculo->getResponsavel()->getIdBaseExterna().'&vinculo_cancelar='.$vinculo->getId().'" class="botao">Cancelar</a></td>';
		else{
			if($podeRenovar)
				echo '<td><a href="?pagina=cartao&selecionado='.$vinculo->getResponsavel()->getIdBaseExterna().'&vinculo_renovar='.$vinculo->getId().'" class="botao">Renovar</a></td>';
			
			
		}
			
		echo '</tr>';
	}
	public function formConfirmacaoEnvioVinculo(Usuario $usuario, $numeroCartao, Tipo $tipo){
		echo '<div class="borda">';
		echo '<p>Tem certeza que deseja adicionar o cartão '.$numeroCartao.' para o usuario '.$usuario->getNome().' com o tipo '.$tipo->getNome().'? </p>';
		echo '<form action="" method="post">
				<input type="submit" class="botao" value="certeza" name="enviar_vinculo"/>
				
				</form>';
		
		echo '</div>';
	}
	public function formConfirmacaoEliminarVinculo(Vinculo $vinculo){
		echo '	<div class="borda doze colunas">';
		$this->formMensagem("-ajuda", "Deseja eliminar esse vínculo?");		
		echo '		<form action="" method="post">
						<input type="submit" class="botao" value="Confirmar" name="certeza" />	
					</form>
				</div>';
	}
	public function formConfirmacaoRenovarVinculo(){		
		$this->formMensagem("-ajuda", "Tem certeza que deseja renovar esse vínculo?");		
		echo '	<form id="form-confirma-cartao" action="" method="post">
					<input type="submit" class="botao" value="certeza" name="certeza" />	
				</form>';
	}
	
	
	public function mostraFormAdicionarVinculo($listaDeTipos, $idSelecionado){
		$daqui3Meses = date ( 'Y-m-d', strtotime ( "+60 days" ) ) . 'T' . date ( 'H:00:01' );
		$dataHoje = date ('Y-m-d') . 'T' . date ( 'H:00:01' );
		if(isset($_SESSION['ultima_hora_inserida'])){
			$daqui3Meses = $_SESSION['ultima_hora_inserida'];
		}
		echo '
				<script>
				  $(document).bind(\'autofocus_ready\', function() {
				    if (!("autofocus" in document.createElement("input"))) {
				      $("#numero_cartao2").focus();
				    }
				  });
				</script>';
		echo '<div class="doze colunas borda">
				
				<form method="get" action="" class="formulario texto-preto" >
						    <label for="numero_cartao2">Número do Cartão</label>
								<input type="text" name="numero_cartao2" id="numero_cartao2" autofocus/>
								 <script>$(document).trigger(\'autofocus_ready\');</script>
						     <label for="id_tipo">Tipo</label>
						       <select id="id_tipo" name="id_tipo">';
		foreach ( $listaDeTipos as $tipo) {
			echo '<option value="' . $tipo->getId() . '">' . $tipo->getNome() . '</option>';
		}
		echo '

			        </select>
				<input type="hidden" name="pagina"  value="cartao"/>
				<input type="hidden" name="cartao"  value="add"/>
				<input type="hidden" name="selecionado"  value="' . $idSelecionado . '"/>
			   <br> 	<input  type="submit"  name="salvar" value="Salvar"/>
			</form>
			</div>';
	}
	public function mostraSucesso($mensagem){
		echo '<div class="borda"><p>'.$mensagem.'</p></div>';
	
	}
	public function mostrarVinculoDetalhe(Vinculo $vinculo){
		echo '<div class="doze linhas">';
		echo '<br><h2 class="texto-preto">Vinculo Selecionado:</h2>';
		echo '</div>';
		echo '<div class="borda">';
		if($vinculo->isAvulso())
			echo '<p>Avulso</p>';
		echo '<p>ResponsÃ¡vel: '.ucwords(strtolower($vinculo->getResponsavel()->getNome())).'</p>';
		echo '<p>CartÃ£o: '.$vinculo->getCartao()->getNumero().'</p>';
		echo '<p>CrÃ©ditos no CartÃ£o: R$' . number_format($vinculo->getCartao()->getCreditos(), 2, ',', '.').'</p>';
		echo '<p>Tipo de VÃ­nculo: '.$vinculo->getCartao()->getTipo()->getNome().'</p>';
		echo '<p>RefeiÃ§Ãµes Por Turno: '.$vinculo->getQuantidadeDeAlimentosPorTurno().'</p>';
		
		if($vinculo->isActive()){
			echo '<p>Vinculo ativo</p>';
			echo '<p>InÃ­cio do VÃ­nculo: '.date('d/m/Y H:i:s', strtotime($vinculo->getInicioValidade())).'</p>';
			echo '<p>Fim do VÃ­nculo: '.date('d/m/Y H:i:s', strtotime($vinculo->getFinalValidade())).'</p>';
			
			echo '<a class="botao b-erro" href="?pagina=cartao&vinculoselecionado='.$vinculo->getId().'&deletar=1">Eliminar Vinculo</a>';
		}
		else{
			echo '<p>Vinculo inativo</p>';
			echo '<p>InÃ­cio do VÃ­nculo: '.date('d/m/Y H:i:s', strtotime($vinculo->getInicioValidade())).'</p>';
			echo '<p>Fim do VÃ­nculo: '.date('d/m/Y H:i:s', strtotime($vinculo->getFinalValidade())).'</p>';
			echo '<a class="botao b-erro" href="?pagina=cartao&vinculoselecionado='.$vinculo->getId().'&reativar=1">Reativar Vinculo</a>';
			
			
		}
		
		
		
		echo '</div>';
		
	}
	
	
	public function mostraIsencaoDoVinculo(Vinculo $vinculo){
		echo '<div class="doze linhas">';
		echo '<br><h2 class="texto-preto">Vinculo Selecionado:</h2>';
		echo '</div>';
		echo '<div class="borda">';
		if($vinculo->getIsencao()->getId())
		{
			$tempoA = strtotime($vinculo->getIsencao()->getDataDeInicio());
			$tempoB = strtotime($vinculo->getIsencao()->getDataFinal());
			$tempoAgora = time();
			if($tempoAgora > $tempoA && $tempoAgora < $tempoB){
				echo '<p>IsenÃ§Ã£o ativa</p>';
				echo '<p>InÃ­cio da IsenÃ§Ã£o: '.date('d/m/Y H:i:s', strtotime($vinculo->getIsencao()->getDataDeInicio())).'</p>';
				echo '<p>Fim da IsenÃ§Ã£o: '.date('d/m/Y H:i:s', strtotime($vinculo->getIsencao()->getDataFinal())).'</p>';
				echo '<a href="?pagina=cartao&vinculoselecionado='.$vinculo->getId().'&delisencao=1">Eliminar IsenÃ§Ã£o</a>';
				
			}
			else if($tempoAgora < $tempoB){
				echo '<p>IsenÃ§Ã£o no Futuro</p>';
				echo '<p>InÃ­cio da IsenÃ§Ã£o: '.date('d/m/Y H:i:s', strtotime($vinculo->getIsencao()->getDataDeInicio())).'</p>';
				echo '<p>Fim da IsenÃ§Ã£o: '.date('d/m/Y H:i:s', strtotime($vinculo->getIsencao()->getDataFinal())).'</p>';
				echo '<p><a href="?pagina=cartao&vinculoselecionado='.$vinculo->getId().'&delisencao=1">Eliminar IsenÃ§Ã£o</a></p>';
			}else
			{
				echo '<p>IsenÃ§Ã£o inativa</p>';
			}
				
			
				
		}
		echo '</div>';
	}
	
	public function formAdicionarIsencao($idSelecionado){
		
		$daqui3Meses = date ( 'Y-m-d', strtotime ( "+60 days" ) ) . 'T' . date ( 'H:00:01' );
		$hoje = date ('Y-m-d') . 'T' . date ( 'H:00:01' );
		
		
		
		echo '<div class="borda">
				<form method="post" action="" class="formulario sequencial texto-preto" >
					
						    <label for="isen_inicio">InÃ­cio:</label>
						         <input id="isen_inicio" type="datetime-local" name="isen_inicio" value="' . $hoje . '" />
				    	    <label for="isen_fim">Fim:</label>
						         <input id="isen_fim" type="datetime-local" name="isen_fim" value="' . $daqui3Meses . '" />
							<input type="hidden" name="id_card"  value="' . $idSelecionado . '"/>
			   <br> <br>	<input  type="submit"  name="salve_isencao" value="Salvar"/>
			</form>
			</div>';
		
	}
	
	public function formAdicionarCreditos($idSelecionado){
	
		$daqui3Meses = date ( 'Y-m-d', strtotime ( "+60 days" ) ) . 'T' . date ( 'H:00:01' );
		$hoje = date ('Y-m-d') . 'T' . date ( 'H:00:01' );
	
	
	
		echo '<div class="borda">
				<form method="post" action="" class="formulario sequencial texto-preto" >
			
						   
				    	    <label for="valor_creditos">Valor A Adicionar:</label>
						         <input id="valor_creditos" type="number"  max="100"  name="valor_creditos" step="0.01" value="1.6" />
							<input type="hidden" name="id_card"  value="' . $idSelecionado . '"/>
			   <br> <br>	<input  type="submit"  name="salve_creditos" value="Salvar"/>
			</form>
			</div>';
	
	}
	
	public function formIdentificacao(Cartao $cartao, Usuario $usuario, Tipo $tipo, $imagem){
		
		echo '	<div class="borda doze colunas">
								
						<div class="doze colunas dados-usuario">		
							<h2 class="titulo centralizado">Identificação do Usuario</h1><br>				
							<hr class="um">
							<div class="nove colunas">
								<div id="informacao" class="fundo-cinza1">
										<div id="dados" class="dados">
										<p>Cartão: <strong>'.$cartao->getNumero().'</strong></p>
										<p>Nome: <strong>'.ucwords(strtolower(htmlentities($usuario->getNome()))).'</strong></p>
										<p>Tipo: <strong>'.$tipo->getNome().'</strong></p>
									</div>
								</div>
							</div>
							<div class="tres colunas zoom">
								<img id="imagem" src="fotos/'.$imagem.'.png" alt="">
							</div>
						</div>
					</div>';				
	}	
	
	
	public function formMensagem($tipo, $texto){
		//Tipo = -sucesso, -erro, -ajuda
		echo '		<div class="alerta'.$tipo.'">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">'.$texto.'</div>
					</div>';
	}
	
}

?>