<?php 
class TipoView{
    
    
    
    
    public function formAdicionarTipoDeUsuario(){
        echo '  <h2 class="titulo">Adicionar Tipo de Usuário</h2>
				<div class="borda">
									<form method="get" action="" class="formulario sequencial">
										<label for="tipo_nome" class="">
										    Tipo Usuario: <input type="text" name="tipo_nome" id="tipo_nome" />
										</label>
										<label for="tipo_valor" class="">
										    Valor por Refeição : <input type="number"  max="100"  step="0.01" value="1.6" name="tipo_valor" id="tipo_valor" />
										</label>
				    					<input type="hidden" name="pagina"  value="definicoes_tipo" />
										<input type="submit" name="cadastrar_tipo" value="Salvar" />
									</form>
								</div>';
    }
    public function listarTiposDeUsuarios($tipos){
        
        echo '
								<div class="doze linhas borda">
									<table class="tabela borda-vertical zebrada texto-preto no-centro">
										<thead>
									        <tr>
									            <th>ID</th>
									            <th>Tipo de Usuario</th>
									            <th>Valor por refeição</th>
									            <th>Editar</th>

									        </tr>
									    </thead>
										<tbody>';
        foreach($tipos as $tipo){
            $this->mostrarLinhaTipo($tipo);
        }
        echo '
            
									    </tbody>
									</table>
								</div>';
    }
    
    public function mostrarLinhaTipo(Tipo $tipo){
        echo '<tr>
				<td>'.$tipo->getId().'</td>
				<td>'.$tipo->getNome().'</td>
				<td>R$' . number_format($tipo->getValorCobrado(), 2, ',', '.').'</td>
				<td class="centralizado">
				<a href="?pagina=definicoes_tipo&editar_tipo='.$tipo->getId().'"><span class="icone-pencil2 botao texto-amarelo2" title="Editar"></span></a>
				
				</td>
				</tr>	';
        
    }
    
    public function formMensagem($tipo, $texto){
        //Tipo = -sucesso, -erro, -ajuda
        echo '		<div class="alerta'.$tipo.'">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">'.$texto.'</div>
					</div>';
    }
    public function formConfirmacao(Tipo $tipo){
        
        echo '	<div class="borda">';
        $this->formMensagem("-ajuda", 'Você tem certeza que quer adicionar esse tipo de usuário? '.$tipo->getNome());
        echo '	<form action="" method="post" class="formulario sequencial texto-preto">
						<input type="hidden" name="tipo_nome" value="' . $tipo->getNome() . '" />
						<input type="hidden" name="tipo_valor" value="' . $tipo->getValorCobrado(). '" />
						<input  type="submit"  name="certeza_cadastrar_tipo" value="Confirmar"/>
					</form>';
        echo '	</div>';
    }
    
    public function formEditarTipo(Tipo $tipo){
        echo '	<h2 class="titulo">Editar Tipo : '.$tipo->getNome().'</h2>
				<div class="borda">
					<form method="post" class="formulario em-linha">
						<label for="valor_tipo">
							Novo valor: <input type="number" max="100" step="0.01" name="valor_tipo" value="'.$tipo->getValorCobrado().'">
						</label>
						<input type="submit" name="alterar" value="Alterar">
					</form>
				</div>';
    }
}




?>