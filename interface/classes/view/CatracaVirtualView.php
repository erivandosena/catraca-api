<?php


class CatracaVirtualView{
	
	
	public function formBuscaCartao(){
		echo '
				<script>
						  $(document).bind(\'autofocus_ready\', function() {
						    if (!("autofocus" in document.createElement("input"))) {
						      $("#numero_cartao").focus();
						    }
						  });
						</script>
	

						<form method="get" action="" class="formulario em-linha" >
							<input type="hidden" name="pagina" value="gerador" />
							<label for="numero_cartao">
								<object class="rotulo texto-preto">Buscar por Número: </object><br><br>
			
								<input class="texto-preto" type="number" name="numero_cartao" id="numero_cartao" autofocus /><br>
								 <script>$(document).trigger(\'autofocus_ready\');</script>
								<input type="submit" />
							</label>

						</form>';
	}
	
	public function formSelecionarRu($listaDeCatracas){
		


		echo '<div class="navegacao">
				<div class = "simpleTabs">
			        <ul class = "simpleTabsNavigation">
						<li><a href="#">Cadastro</a></li>
			        </ul>
			        <div class = "simpleTabsContent">
						<div class="doze colunas borda">';
		
		echo '<form action="" method="post">
				<label for="catraca_id">Selecione o Restaurante:</label><br>
			        <select name="catraca_id" id="catraca_id">';
		
		foreach ($listaDeCatracas as $catraca){
				
			echo '<option value="'.$catraca->getId().'">'.$catraca->getNome().'</option>';
		
		}
		
		
		 
		echo '       </select><br>
					<input name="catraca_virtual" type="submit" class="botao" VALUE="Selecionar" />
				</form>	';
		
		
		echo '</div>
				</div>
				</div>
				</div>';
		
	}
	
	public function exibirQuantidadesDeCadaTipo($listaDeTipos, $quantidades, Catraca $catraca){
		echo '
				
							<table class="tabela borda-vertical zebrada no-centro centralizado">
							    <thead>
							        <tr>
												<th>Unidade</th>';
		foreach($listaDeTipos as $tipo){
			echo '<th>'.$tipo->getNome().'</th>';
		
		
		}
		echo '<th>Total</th></tr></thead><tbody>';
		echo '					        <tr>
				<th>'.$catraca->getNome().'</th>';
		$somatorio = 0;
		foreach ($quantidades as $quantidade){
			$somatorio += $quantidade;
			echo '<td>'.$quantidade.'</td>';
				
		}
		echo '<td>'.$somatorio.'</td>';
		
		echo '</tr>';
		echo '</tbody></table>';

		
		
	}
	
}


?>