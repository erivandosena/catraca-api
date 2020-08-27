<?php

class CustoView{
    
    public function listaCustoUnidade($listaCustos){

        echo '
               <h2 class="titulo">Custo De Refeição</h2>
            
                <div class="borda">';
        if(sizeof($listaCustos) != 0){
            echo '		<table id="turno" class="tabela borda-vertical zebrada texto-preto no-centro">
						<thead>
							<tr>
								<th>ID</th>
								<th>Valor</th>
								<th>Unidade Acadêmica</th>
								<th>Turno</th>
                                <th>Início</th>
                                <th>Fim</th>
                                <th>Excluir</th>
							</tr>
						</thead>
						<tbody>';
            
            foreach ($listaCustos as $custo){
                echo'				<tr>
								<td>'.$custo->getId().'</td>
								<td>'.$custo->getValor().'</td>
								<td>'.$custo->getUnidade()->getNome().'</td>
								<td>'.$custo->getTurno()->getDescricao().'</td>
                                <td>'.date('d/m/Y', strtotime($custo->getInicio())).'</td>
                                <td>'.date('d/m/Y', strtotime($custo->getFim())).'</td>
								<td><a class="botao" href="?pagina=definicoes_custo&excluir='.$custo->getId().'">Excluir</a></td>
							</tr>';
            }
            
            echo'			</tbody></table>';
        }
        else{
            echo '<h2>Não existem custos cadastrados.</h2>';
        }
        echo '</div>';

    }
    public function botaoInserirCusto(){
        echo '
            <div class="borda">
                <a class ="botao" href="?pagina=definicoes_custo&custoadd=1">Adicionar Custo</a>
            </div>';
    }
    
    public function formInserirCusto($listaDeUnidades, $listaDeTurnos){
        echo '
                <h2 class="titulo">Adicionar Custo</h2>
                <div class="borda">
					<form id="form-custo-unidade" action="" method="get" class="formulario-organizado">
						<input type="hidden" name="pagina" value="definicoes_custo" />
                        
                        
                        <input type="hidden" name="custoadd" value="1" />
                        <label for="valor">
								Valor de Custo Refeição:  
                        </label>
                        <input type="number"  max="100"  step="0.01" name="valor" id="valor" value="" />
					   <br>';
        
        echo '<label for="unidade">Unidade Academica: </label>';
        echo '
                        <select name="unidade" id="unidade">';
        foreach ($listaDeUnidades as $unidade){
            echo'
                            <option value="'.$unidade->getId().'">'.$unidade->getNome().'</option>';
        }
        echo'				</select>';
        echo '<label for="turno">Turno: </label>';
        echo '
                         <select name="turno" id="turno">';
        foreach ($listaDeTurnos as $turno){
            echo'
                             <option value="'.$turno->getId().'">'.$turno->getDescricao().'</option>';
        }
        echo'				</select>';

        echo '<label for="inicio" class="texto-preto">Data Inicial:</label>
                 <input id="inicio" type="date" name="inicio"/>
												<br>';
        
        echo '<label for="fim" class="texto-preto">Data Fim:</label>
                 <input id="fim" type="date" name="fim"/>
												<br>';

        echo             '<input type="submit" name="adicionar" value="Adicionar" class="botao"/></form>';
        echo '</div>';
    }
    
    public function formConfirmar(){
        
        echo '		<div class="borda doze colunas">
                        <div class="alerta-ajuda">
				    	   <div class="icone icone-notification ix16"></div>
				    	   <div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	   <div class="subtitulo-alerta">Conforma que quer adicionar este custo? </div>
                        </div>
                        <form id="custo-refeicao" method="post" class="formulario-organizado">
    							<input type="submit" name="confirmar" value="Confirmar">
    					</form>
					</div>
                        
                    </div>';
    }
    
    public function sucesso(){
        echo '	 <div class="borda">	<div class="alerta-sucesso">
					    	<div class="icone icone-notification ix16"></div>
					    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
					    	<div class="subtitulo-alerta">Alteração feita com sucesso!</div>
						</div>
                    </div>';
    }
    
    public function erro($mensagem = "Erro ao alterar os dados!"){
        
        echo '<div class="borda">	<div class="alerta-erro">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
                        <div class="subtitulo-alerta">'.$mensagem.'</div>
					</div></div>';
    }
    public function formConfirmarExcluir(){
        echo '		<div class="borda doze colunas">
                        <div class="alerta-ajuda">
				    	   <div class="icone icone-notification ix16"></div>
				    	   <div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	   <div class="subtitulo-alerta">Conforma que quer excluir este custo? </div>
                        </div>
                        <form id="custo-refeicao" method="post" class="formulario-organizado">
    							<input type="submit" name="confirmar_excluir" value="Confirmar">
    					</form>
					</div>
                        
                    </div>';
    }
    
    
 
}



?>