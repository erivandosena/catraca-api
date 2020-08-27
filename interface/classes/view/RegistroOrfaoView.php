<?php 
class RegistroOrfaoView{
	
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
							<input type="hidden" name="pagina" value="registro_orfao" />
							<label for="numero_cartao">
								<object class="rotulo texto-preto">Buscar por Número: </object><br><br>
	
								<input class="texto-preto" type="number" name="numero_cartao" id="numero_cartao" autofocus /><br>
								 <script>$(document).trigger(\'autofocus_ready\');</script>
								<input type="submit" />
								<input type="submit" name="encerrar" value="Encerrar" class="b-erro a-direita"/>
							</label>							
						</form>';
	}
	
	public function formSelecionarRu($listaDeCatracas){
	
	
	
		echo '<div class="navegacao">
				<div class = "simpleTabs">
			        <ul class = "simpleTabsNavigation">
						<li><a href="#">Seleção RU</a></li>
			        </ul>
			        <div class = "simpleTabsContent">
						<div class="doze colunas borda">';
	
		echo '<form class="formulario-organizado" action="" method="post">
				<label for="id_catraca" class="seis">
					<object class="rotulo cinco">Selecione o Restaurante: </object>
			        	<select name="id_catraca" id="id_catraca" class="cinco">';
	
		foreach ($listaDeCatracas as $catraca){
	
			echo '<option value="'.$catraca->getId().'">'.$catraca->getNome().'</option>';
	
		}
	
	
			
		echo '       	</select>
					</label>
					<input name="catraca_virtual" type="submit" class="botao" VALUE="Selecionar" class="linha" />
				</form>	';
	
	
		echo '</div>
				</div>
				</div>
				</div>';
	
	}
	
	public function formSelecionarPeriodo($listaTurno, $listaDeCatracas, $erro = false){		
		echo '	<div class="navegacao">
				<div class = "simpleTabs">
			        <ul class = "simpleTabsNavigation">
						<li><a href="#">Seleção Período</a></li>
			        </ul>
			        <div class = "simpleTabsContent">
				<div class="doze colunas borda">
					<form class="formulario-organizado">
						<input type="hidden" name="pagina" value="registro_orfao">	
						
						<h2 class="titulo">Período em que o sistema ficou indisponível</h2>
						<hr class="um">
						<label class="cinco ">
							<object class="rotulo tres">Hora Inicial: </object>
							<input type="time" name="hora_inicio" class="duas" title="Hora em que o sistema ficou indisponível.">
						</label>
				
						<label class="cinco linha">
							<object class="rotulo tres">Hora Final: </object>
							<input type="time" name="hora_fim" class="duas" title="Hora em que o sistema voltou a funcionar.">
						</label>
						<hr class="um linha">';
				
		echo '			<label for="id_catraca" class="cinco tres">
							<object class="rotulo tres">Restaurante: </object>
							<select name="id_catraca" id="id_catraca" class="cinco">';
					
							foreach ($listaDeCatracas as $catraca){					
								echo '<option value="'.$catraca->getId().'">'.$catraca->getNome().'</option>';					
							}		
							
		echo '      	 	</select>
						</label>				
						<label class="cinco linha">
							<object class="rotulo tres">Data: </object>
							<input type="date" name="data" class="cinco" value="'.date('Y-m-d').'">
						</label>
						<label class="cinco linha">
       						<object class="rotulo tres" >Turno: </object>
							<select name="turno_id" class="cinco">';
								foreach ($listaTurno as $turno){
									echo '<option value="'.$turno->getId().'">'.$turno->getDescricao().'</option>';
								}
		echo '				</select>
						</label>
						<input type="submit" name="confirmar" value="Confirmar">
					</form>
				</div>';
		if ($erro){
			$this->mensagem('erro','Verifique os dados inseridos.');
			echo '<meta http-equiv="refresh" content="2; url=?pagina=registro_orfao">';
		}
		echo '	</div>';
		
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
			if ($quantidade == null){
				echo '<td>0</td>';
			}else{
				echo '<td>'.$quantidade.'</td>';
			}	
		}
		echo '<td>'.$somatorio.'</td>';
	
		echo '</tr>';
		echo '</tbody></table>';	
	
	}

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