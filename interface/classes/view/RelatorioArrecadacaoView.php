<?php


class RelatorioArrecadacaoView{
    
    public function exibirFormulario($listaDeUnidades)
    {
        echo '<div class="doze colunas borda relatorio">
                <h3>Relatório de Arrecadação Diária</h3>
									<form action="" class="formulario sequencial">
											<div id="data">Unidades Acadêmicas: <br>';
        $i = 0;
        foreach ( $listaDeUnidades as $unidade ) {
            echo '<label for="unidade'.$i.'">'.$unidade->getNome () .'</label>';
            echo '      <input type="checkbox" id="unidade'.$i.'" name="unidade'.$i.'" value="' . $unidade->getId () . '">';
            
            $i++;
        }

        echo '          <label for="data_inicial" class="texto-preto">Data Inicial: </label>
                        <input id="data_inicial" type="date" name="data_inicial"/><br>
                        <label for="data_final" class="texto-preto">Data Final: </label>
                        <input id="data_final" type="date" name="data_final"/><br>
        				<input type="hidden" name="pagina" value="relatorio_arrecadacao" /><br>
        				<input  type="submit"  name="gerar" value="Gerar"/>
                        <input  type="submit"  name="gerar" value="Excel"/>
        			</div>
        		</form>
        	</div>';
    }
    public function mostraListaDeDados($listaDeDados, $titulos, $tipos, $listaDeDatas) {
        $subTotal = array ();
        foreach ( $tipos as $tipo ) {
            $subTotal [$tipo->getId ()] = 0;
        }
        $subTotal ['total'] = 0;
        
        
        echo '<div class=" doze colunas borda relatorio">';
        $i = 0;
        foreach($titulos as $titulo){
            
            if(!$i){
                echo  '<h3>'.$titulo.'</h3><hr class="um">';
                $i++;
            }else{
                echo '<span>'.$titulo.'</span>';
            }
        } 
        echo '<table class="tabela-relatorio">
				<thead>
					<tr>
						<th>Data</th>';
        foreach ( $tipos as $tipo ) {
            echo '<th>' . $tipo->getNome () . '</th>';
        }
        echo'			<th>Total</th>
				</thead>';
        
        echo '<tbody>';
        
        foreach ( $listaDeDatas as $data ) {
            echo '<tr>';
            echo '<td>' . date ( 'd/m/Y', strtotime ( $data ) ) . '</td>';
            foreach ( $tipos as $tipo ) {
                
                echo '<td>'.number_format (floatval($listaDeDados [$data] [$tipo->getId ()]) , 2, ',', '.' )    . '</td>';
                $subTotal [$tipo->getId ()] += $listaDeDados [$data] [$tipo->getId ()];
            }
            echo '<td>'.number_format ($listaDeDados [$data] ['total'] , 2, ',', '.' )   . '</td>';
            echo '</tr>';
            $subTotal ['total'] += $listaDeDados [$data] ['total'];
        }
        echo '<tr id="soma">
				<th>Somatório</th>';
        foreach ( $tipos as $tipo ) {
            echo '<td>'.number_format ( $subTotal [$tipo->getId ()] , 2, ',', '.' )  . '</td>';
        }
        echo '<td>' . number_format ( $subTotal ['total']  , 2, ',', '.' )  . '</td>';
        echo '</tr>';
        echo '</table>
				<div class="doze colunas relatorio-rodape">
					<span>CATRACA | Copyright © 2015 - DTI</span>
                    <span>Relatório Emitido em: '.date('d/m/Y').'</span>';
        echo '		</div></div>';
    }
    public function geraStrCSV($listaDeDados, $titulos, $tipos, $listaDeDatas) {
        
        $dados = "";
        $subTotal = array ();
        foreach ( $tipos as $tipo ) {
            $subTotal [$tipo->getId ()] = 0;
        }
        $subTotal ['total'] = 0;
        
        
        
        $i = 0;
        foreach($titulos as $titulo){
            
            if(!$i){
                $dados .= $titulo;
                $i++;
            }else{
                $dados .= "\n".$titulo;
            }
        }
       
        $dados .= "\n Datas;";
        foreach ( $tipos as $tipo ) {
            $dados .=  $tipo->getNome ().';';
        }
        $dados .= "Total\n";
        foreach ( $listaDeDatas as $data ) {
            $dados .=  date ( 'd/m/Y', strtotime ( $data ) ) . ';';
            foreach ( $tipos as $tipo ) {
                $dados .= number_format (floatval($listaDeDados [$data] [$tipo->getId ()]) , 2, ',', '.' )    . ';';
                $subTotal [$tipo->getId ()] += $listaDeDados [$data] [$tipo->getId ()];
            }
            $dados .= number_format ($listaDeDados [$data] ['total'] , 2, ',', '.' );

            $dados .= "\n";
            $subTotal ['total'] += $listaDeDados [$data] ['total'];
        }

        $dados .= "Somatório;";

        foreach ( $tipos as $tipo ) {
            $dados .= number_format ( $subTotal [$tipo->getId ()] , 2, ',', '.' )  . ';';
        }
        $dados .= number_format ( $subTotal ['total']  , 2, ',', '.' )  . ';';
        $dados .= "\nCATRACA | Copyright © 2015 - DTI\n Relatório Emitido em: ".date('d/m/Y');
        
        
        return $dados;
    }
    
    
}