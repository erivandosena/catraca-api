<?php


class RelatorioDespesaView{
    
    public function exibirFormulario($listaDeUnidades)
    {
        echo '<div class="doze colunas borda relatorio">
                <div id="data">
                <h3>Relatório de Despesas</h3>
			     <form action="" class="formulario sequencial">
					Unidades Acadêmicas: <br>';
        $i = 0;
        foreach ( $listaDeUnidades as $unidade ) {
            echo '<label for="unidade'.$i.'">'.$unidade->getNome () .'</label>';
            echo '      <input type="checkbox" id="unidade'.$i.'" name="unidade'.$i.'" value="' . $unidade->getId () . '">';
            
            $i++;
        }

        echo '          <label for="data_inicial" class="texto-preto">Data Inicial: </label>
                        <input id="data_inicial" type="date" name="data_inicial" required/><br>
                        <label for="data_final" class="texto-preto">Data Final: </label>
                        <input id="data_final" type="date" name="data_final" required/><br>
        				<input type="hidden" name="pagina" value="relatorio_despesa" /><br>
        				<input  type="submit"  name="gerar" value="Gerar"/>
                        <input  type="submit"  name="gerar" value="Excel"/>
        		</form>
            </div>
        	</div>';
    }
    
    public function mostrarRelatorio($titulos, $listaDeDados, $tipos){
        
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
        echo '

					<table class="tabela-relatorio">
						<thead>
							<tr>
								<th>Tipo Usuário</th>
								<th>Pratos</th>
								<th>%Pratos</th>
								<th>Valores Arrecadados</th>
								<th>Valores Custo</th>
								<th>Despesa(Custo - Arrecadação)</th>
								<th>%Despesa</th>
							</tr>
						<thead>
						<tbody>
							';
        foreach ( $tipos as $tipo ) {
            $percentual = 0;
            if($listaDeDados ['total']['custo'] - $listaDeDados ['total']['valor']){
                $percentual = ($listaDeDados [$tipo->getId ()] ['custo'] - $listaDeDados [$tipo->getId ()] ['valor'])/($listaDeDados ['total'] ['custo'] - $listaDeDados ['total']['valor'])*100;
            }
            $percentualPratos = 0;
            if($listaDeDados ['total']['pratos']){
                $percentualPratos = ($listaDeDados [$tipo->getId ()] ['pratos'])/($listaDeDados ['total'] ['pratos'])*100;
            }
            echo'	<tr >
						<th id="limpar">' . $tipo->getNome () . '</th>
						<td>' . $listaDeDados [$tipo->getId ()] ['pratos'] . '</td>
						<td>'.number_format ( $percentualPratos, 2, ',', '.' ).'</td>
						<td>R$ ' . number_format ( $listaDeDados [$tipo->getId ()] ['valor'], 2, ',', '.' ) . '</td>
						<td>R$ ' . number_format ( $listaDeDados [$tipo->getId ()] ['custo'], 2, ',', '.' ) . '</td>
						<td>R$ ' . number_format ( ($listaDeDados [$tipo->getId ()] ['custo'] - $listaDeDados [$tipo->getId ()] ['valor']), 2, ',', '.' ) . '</td>
						    
						<td> ' . number_format ( ($percentual), 2, ',', '.' ) . '</td>
						    
					</tr>';
            
            
        }
        echo'		<tr id="soma">
						<th id="limpar">Somatório</th><td>' . $listaDeDados ['total'] ['pratos'] . '</td><td>-</td>
						<td>R$ ' . number_format ( $listaDeDados ['total'] ['valor'], 2, ',', '.' ) . '</td>
						<td>R$ ' . number_format ( $listaDeDados ['total'] ['custo'], 2, ',', '.' ) . '</td>
						<td>R$ ' . number_format ( ($listaDeDados ['total'] ['custo'] - $listaDeDados ['total']['valor']), 2, ',', '.' ) . '</td>
						<td>-</td>
					</tr>';
        
        echo'			</tbody>
					</table>';
        echo'<div class="doze colunas relatorio-rodape">
			<span>Emitido em:'.date('d/m/Y H:i:s').'</span>';
        echo '	</div></div>';
        
    }
    public function gerarStrCSV($titulos, $listaDeDados, $tipos){
        
        $dados = '';
        
        $i = 0;
        foreach($titulos as $titulo){
            
            if(!$i){
                $dados .= $titulo;
                
                $i++;
            }else{
                $dados .= "\n".$titulo;
            }
        }
        $dados .= "\n";
        $dados .= "Tipo Usuário; Pratos;%Pratos;Valores Arrecadados;Valores Custo;Despesa(Custo - Arrecadação);%Despesa";
       
      
        foreach ( $tipos as $tipo ) {
            $percentual = 0;
            if($listaDeDados ['total']['custo'] - $listaDeDados ['total']['valor']){
                $percentual = ($listaDeDados [$tipo->getId ()] ['custo'] - $listaDeDados [$tipo->getId ()] ['valor'])/($listaDeDados ['total'] ['custo'] - $listaDeDados ['total']['valor'])*100;
            }
            $percentualPratos = 0;
            if($listaDeDados ['total']['pratos']){
                $percentualPratos = ($listaDeDados [$tipo->getId ()] ['pratos'])/($listaDeDados ['total'] ['pratos'])*100;
            }
            
            $dados .= "\n";
            

            
            $dados .= $tipo->getNome () . ';' . $listaDeDados [$tipo->getId ()] ['pratos'].';';
            $dados .= number_format ( $percentualPratos, 2, ',', '.' ).';';
            $dados .= 'R$ ' . number_format ( $listaDeDados [$tipo->getId ()] ['valor'], 2, ',', '.' ).';';
            $dados .= 'R$ ' . number_format ( $listaDeDados [$tipo->getId ()] ['custo'], 2, ',', '.' ).';';
            $dados .= 'R$ ' . number_format ( ($listaDeDados [$tipo->getId ()] ['custo'] - $listaDeDados [$tipo->getId ()] ['valor']), 2, ',', '.' ).';';
            $dados .= number_format ( ($percentual), 2, ',', '.' );

            
            
            
        }
        $dados .= "\n";
        $dados .= 'Somatorio;' . $listaDeDados ['total'] ['pratos'].';';
        $dados .= '100;';
        $dados .= 'R$ ' . number_format ( $listaDeDados ['total'] ['valor'], 2, ',', '.' ) . ';';
        $dados .= 'R$ ' . number_format ( $listaDeDados ['total'] ['custo'], 2, ',', '.' ) . ';';
        $dados .= 'R$ ' . number_format ( ($listaDeDados ['total'] ['custo'] - $listaDeDados ['total']['valor']), 2, ',', '.' ) . ';';
        $dados .= '100;';
        $dados .= "\n";
        $dados .= "CATRACA | Copyright © 2015 - DTI \n Relatório Emitido em: ".date('d/m/Y H:i');
        
        return $dados;
        

    
    }
    public function mensagemErro($mensagem){
        echo '<div class=" doze colunas borda relatorio">';
        echo $mensagem;
        echo '</div>';
        
    }
    
    
}