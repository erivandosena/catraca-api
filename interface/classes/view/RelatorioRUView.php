<?php


class RelatorioRUView{
    
    public function exibirFormulario($listaDeUnidades)
    {
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
												<label for="tipo_de_relatorio">
													Tipo De Relatório
												</label>
												<select id="tipo_de_relatorio" name="tipo_de_relatorio">';
        
        echo '
													<option value="3">Relação Pratos e Valores</option>
													<option value="1">Pratos Consumidos</option>
 													<option value="2">Valores Arrecadados</option>';
        echo '
												</select>
												<input type="hidden" name="pagina" value="relatorio" />
												<input  type="submit"  name="gerar" value="Gerar"/>
											</div>
									</form>
									</div>';
    }
    
    
}