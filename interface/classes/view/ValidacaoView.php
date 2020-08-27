<?php

class ValidacaoView
{

    public function botaoInserirValidacao()
    {
        echo '
            <div class="borda">
                <a class ="botao" href="?pagina=validacao&validacaoadd=1">Adicionar Validação</a>
            </div>';
    }

    public function formValidacao($listaTipos, $listaCampos)
    {
        echo '
                     <h2 class="titulo">Adicionar Validação</h2>';
        echo '			<div class="borda">
							<form class="formulario-organizado" method="get">
								<input type="hidden" name="pagina" value="validacao">
				 				<label for="">Tipo:</label>
							    <select name="tipo" >
									<option selected="selected" value="">Selecione um Tipo</option>';
        foreach ($listaTipos as $tipo) {
            echo '<option value="' . $tipo->getId() . '">' . $tipo->getNome() . '</option>';
        }
        echo '				    </select>
								<label for="" >Campo: </label>
									
							        <select name="campo" id="campo">
										<option value="">Selecione um Campo</option>';
        foreach ($listaCampos as $campo) {
            echo '<option value="' . $campo . '">' . $campo . '</option>';
        }
        echo '				        </select>
				
								<label for="" class="cinco linha">Valor: </label>
							    <input type="text" name="valor" id="valor" />
								<input type="submit" value="Salvar" name="adicionar"/>
                                <input type="hidden" name="validacaoadd" value="1" />
							</form>					
						</div>';
    }

    public function formConfirmar($texto)
    {
        echo '		<div class="borda doze colunas">
                        <div class="alerta-ajuda">
				    	   <div class="icone icone-notification ix16"></div>
				    	   <div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	   <div class="subtitulo-alerta">' . $texto . ' </div>
                        </div>
                        <form method="post" class="formulario-organizado">
            					<input type="submit" name="confirmar" value="Confirmar">
            				</form>
					</div>
            
                    </div>';
    }

    public function exibirLista($listaValidacao)
    {
        echo '			
                     <h2 class="titulo">Validações</h2>
                    <div class="borda">';

        if (count($listaValidacao)) {
            echo '
		        
							<table class="tabela borda-vertical zebrada no-centro">
								<thead>
									<tr>
										<th>Id</th>
										<th>Tipo</th>
										<th>Campo</th>
										<th>Valor</th>
										<th>Excluir</th>
									</tr>
								</thead>
								<tbody>';
            foreach ($listaValidacao as $validacao) {
                echo '	<tr>
											<td>' . $validacao->getId() . '</td>
											<td>' . $validacao->getTipo()->getNome() . '</td>
											<td>' . $validacao->getCampo() . '</td>
											<td>' . $validacao->getValor() . '</td>
											<td><a href="?pagina=validacao&excluir=ok&validacao_id=' . $validacao->getId() . '" class="icone-cross botao b-erro centralizado"></a></td>
										</tr>';
            }
            echo '					</tbody></table>';
        } else {
            echo '
                     <p>Nenhuma Validação Cadastrada</p>';
        }

        echo '
                        </div>';
    }

    public function mensagemSucesso($texto)
    {
        echo '		<div class="borda doze colunas">
                        <div class="alerta-sucesso">
				    	   <div class="icone icone-notification ix16"></div>
				    	   <div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	   <div class="subtitulo-alerta">' . $texto . ' </div>
                        </div>

					</div>
				    	       
                    </div>';
    }

    public function mensagemErro($texto)
    {
        echo '		<div class="borda doze colunas">
                        <div class="alerta-erro">
				    	   <div class="icone icone-notification ix16"></div>
				    	   <div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	   <div class="subtitulo-alerta">' . $texto . ' </div>
                        </div>
					</div>
				    	       
                    </div>';
    }
}

?>