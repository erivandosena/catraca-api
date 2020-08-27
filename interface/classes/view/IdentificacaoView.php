<?php


class IdentificacaoView{
    
    public function formBuscaCartao(){
        echo '<div class="doze colunas borda relatorio">';
        echo '
				<script>
                  $(document).bind(\'autofocus_ready\', function() {
                    if (!("autofocus" in document.createElement("input"))) {
                      $("#numero_cartao").focus();
                    }
                  });
                </script>
				
						<form method="get" action="" class="formulario em-linha" >
							<input type="hidden" name="pagina" value="identificacao" />
							<label for="numero_cartao">
								Buscar por Número:<input type="number" name="numero_cartao" id="numero_cartao" autofocus /><br>
								<script>$(document).trigger(\'autofocus_ready\');</script>
								<input type="submit" value="Buscar" />
							</label>
						</form>';
        echo '</div>';
    }
    
 
    public function exibirIdentificacao(Vinculo $vinculo, $imagem){
        
        echo '	<div class="borda doze colunas">
            
						<div class="doze colunas dados-usuario">
							<h2 class="titulo centralizado">Identificação do Usuario</h1><br>
							<hr class="um">
							<div class="nove colunas">
								<div id="informacao" class="fundo-cinza1">
										<div id="dados" class="dados">
										<p>Cartão: <strong>'.$vinculo->getCartao()->getNumero().'</strong></p>
										<p>Nome: <strong>'.ucwords(strtolower(htmlentities($vinculo->getResponsavel()->getNome()))).'</strong></p>
										<p>Tipo: <strong>'.$vinculo->getCartao()->getTipo()->getNome().'</strong></p>
									</div>
								</div>
							</div>
							<div class="tres colunas zoom">
								<img id="imagem" src="fotos/'.$imagem.'.png" alt="">
							</div>
						</div>
					</div>';
    }	
    
    
    public function formRenovacao(Vinculo $vinculo){
        
        echo '		
                <div class="borda doze colunas"><div class="alerta-erro">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">O vínculo está inativo, deseja renovar?</div>
                        
					</div>
                    <a href="?pagina=identificacao&numero_cartao='.$vinculo->getCartao()->getNumero().'&cartao_renovar=1" class="botao">Renovar</a>
                </div>';
    }
    
    public function formCerteza(){
        echo '
                <div class="borda doze colunas"><div class="alerta-ajuda">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">Tem certeza que deseja renovar esse vínculo??</div>
            
					</div>
                    <form id="form-confirma-cartao" action="" method="post">
					   <input type="submit" class="botao" value="certeza" name="certeza" />
				    </form>
                </div>';
        
        
    }
    public function mensagemErro($texto){
        echo '
                <div class="borda doze colunas"><div class="alerta-erro">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">'.$texto.'</div>
            
					</div>

                </div>';
        
        
    }
    public function mensagemSucesso($texto){
        echo '
                <div class="borda doze colunas"><div class="alerta-sucesso">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">'.$texto.'</div>
				    	    
					</div>
				    	    
                </div>';
        
    }
    
}


?>