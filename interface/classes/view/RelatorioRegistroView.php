<?php


class RelatorioRegistroView{
	
	public function mostrarFormulario($listaDeUnidades){
		
		echo '<div class="doze colunas borda relatorio">
									<form action="" class="formulario sequencial">
											<div id="data">
												<label for="opcoes-1">
													<object class="rotulo texto-preto">Unidade Acadêmica: </object>
													<select name="unidade" id="unidade" class="texto-preto">';
		foreach ( $listaDeUnidades as $unidade ) {
			echo '<option value="' . $unidade->getId () . '">' . $unidade->getNome () . '</option>';
		}
		//echo '<option value="">Todos as Unidades</option>';
		echo '
												    </select>
												</label><br>
												<label for="data_inicial" class="texto-preto">
												    Data Inicial: <input id="data_inicial" type="date" name="data_inicial"/>
												</label><br>
												<label for="data_final" class="texto-preto">
												    Data Final: <input id="data_final" type="date" name="data_final"/>
												</label><br>
												
												<input type="hidden" name="pagina" value="relatorio_registro" />
												<input  type="submit"  name="gerar" value="Gerar"/>
											</div>
									</form>
									</div>';
		
	}
	public function mostraMatriz($matriz){
		echo '<br><br><br><table border="1">';
		foreach($matriz as $chave => $valor){
			echo '<tr><th>'.$chave.'</th>';
			foreach($valor as $chave2 => $valor2){
				echo '<td>'.$chave2.' - '.$valor2.'</td>';
			}
			echo '</tr>';
	
		}
		echo '</table>';
	
	}


	/**
	 * Tipo = -sucesso, -erro, -ajuda
	 * @param string $tipo
	 * @param string $texto
	 */
	public function formMensagem($tipo, $texto){
		
		echo '		<div class="alerta'.$tipo.'">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">'.$texto.'</div>
					</div>';
	}
	
}


?>