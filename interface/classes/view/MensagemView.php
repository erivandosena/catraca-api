<?php 

class MensagemView{
    
    public function formularioInserir($catracas)
    {
        
        echo '	<h2 class="titulo">Incluir e Editar Mensagem da Catraca</h2>
				<div class="borda doze colunas">
					<form action="" class="formulario-organizado" method="">
						<select name="id_catraca">';
        echo'				<option>Selecione uma Catraca</option>';
        foreach ($catracas as $catraca){
            
            echo'			<option value="'.$catraca->getId().'">'.$catraca->getNome().' - '.$catraca->getUnidade()->getNome().'</option>';
        }
        echo '
							</select>
                
                        <br>

						<input type="hidden" name="pagina" value="definicoes_mensagem"/>
						<div class="doze colunas">
						<div class="tres colunas">
								<input type="text" name="msg_um" maxlength="16" placeholder="1ª Mensagem" value="">
						</div>
								    
						<div class="tres colunas">
								<input type="text" name="msg_dois" maxlength="80" placeholder="2ª Mensagem" value="">
						</div>
								    
						<div class="tres colunas">
								<input type="text" name="msg_tres" maxlength="16" placeholder="3ª Mensagem" value="">
						</div>
								    
						<div class="tres colunas">
								<input type="text" name="msg_quatro" maxlength="80" placeholder="4ª Mensagem" value="">
						</div>
							
							<input type="submit" name="salvar" value="Salvar">
						</form>
					</div>';
    }
    public function listarMensagensCatraca($listaMensagemCatraca){
        
        echo '		</div>
				<div class="borda doze colunas">
					<table class="tabela borda-vertical zebrada no-centro">
						<thead>
							<tr class="centralizado">
								<th>Unidade Acadêmica</th>
								<th>Catraca</th>
								<th>Mensagem 1</th>
								<th>Mensagem 2</th>
								<th>Mensagem 3</th>
								<th>Mensagem 4</th>
							</tr>
						</thead>
					<tbody>';
        
        foreach ($listaMensagemCatraca as $mensagem)
        {
            
            echo'				<tr>
								<td>'.$mensagem->getCatraca()->getUnidade()->getNome().'</td>
								<td>'.$mensagem->getCatraca()->getNome().'</td>
								<td>'.substr ($mensagem->getMensagem1(),0,16).'</td>
								<td>'.substr ($mensagem->getMensagem2(),0,80).'</td>
								<td>'.substr ($mensagem->getMensagem3(),0,16).'</td>
								<td>'.substr ($mensagem->getMensagem4(),0,80).'</td>
							</tr>';
        }
        echo '			</tbody>
					</table>';
        
    }
    public function formConfirmacao(){
        echo '		<div class="alerta-ajuda">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">Deseja incluir estas Mensagem?</div>
					</div>';
        
        echo '	<form class="formulario-organizado" method="post">
						<input type="submit" name="confirmar" value="Confirmar">
					</form>';
    }
    
    public function sucesso(){
        echo '		<div class="alerta-sucesso">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">Mesnagem Inserida Com sucesso!</div>
					</div>';
        
    }
    public function erro(){
        echo '		<div class="alerta-erro">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">Erro ao tentar Inserir Mensagem!</div>
					</div>';
        
    }
}




?>