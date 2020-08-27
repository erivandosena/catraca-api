<?php

class CartaoProprioView
{

    public function formBuscaUsuarios()
    {

        
        echo '<script language="javascript">
                function altera() {
                  document.getElementById("botao").value="AGUARDE! Pesquisando...";
                  //document.getElementById("botao").disabled=true;
                            
                }
                </script>';
       
        echo '					<h2 class="titulo">Cartão Próprio</h2>
                                <div class="borda">
									<form method="get" action="" class="formulario em-linha" >
            
										<label for="nome">
											<object class="rotulo texto-preto">Nome do Usuario: </object>
											<input class="texto-preto" type="text" name="nome" id="nome" /><br>
										</label>
										<input type="hidden" name="pagina" value="cartao_proprio" />
										<input id="botao" onclick="altera();" type="submit" value="Buscar"/>
									</form>
								</div>';
    }
    
    
    public function mostraResultadoBuscaDeUsuarios($usuarios, $mensagem = "") {
        echo '<h2 class="titulo">Resultado da busca:'.$mensagem.'</h2>';
        echo '<div class="doze colunas">';
        echo '<div class="borda">
				<table class="tabela borda-vertical zebrada texto-preto">
				<thead>
					<tr>
											            <th>Nome</th>
											            <th>CPF</th>
											            <th>Passaporte</th>
											            <th>Status Discente</th>
														<th>Status Servidor</th>
														<th>Tipo de Usuario</th>
											            <th>Selecionar</th>
											        </tr>
											    </thead>
												<tbody>';
        foreach ( $usuarios as $usuario ) {
            echo '<tr>';
            echo '<td>' . $usuario->getNome () . '</a></td>';
            echo '<td>' . $usuario->getCpf () . '</td>';
            echo '<td>' . $usuario->getPassaporte () . '</td>';
            echo '<td>' . $usuario->getStatusDiscente () . '</td>';
            echo '<td>' . $usuario->getStatusServidor () . '</td>';
            echo '<td>' . $usuario->getTipodeUsuario () . '</td>';
            echo '<td class="centralizado"><a href="?pagina=cartao_proprio&selecionado=' . $usuario->getIdBaseExterna () . '"><span class="icone-checkmark texto-verde2 botao" title="Selecionar"></span></a></td>';
            echo '</tr>';
        }
        echo '</tbody></table>
            </div>';
    }
    public function mostraDadosAdicionais($usuarios) {

        echo '<div class="doze colunas">';
        echo '<div class="borda">
                <p>Dados Adicionais</p>
				<table class="tabela borda-vertical zebrada texto-preto">
				<thead>
					<tr>

                                                        <th>nivel_discente</th>
											            <th>status_discente</th>
														<th>status_servidor</th>
														<th>tipo_usuario</th>
                                                		<th>categoria</th>
                                                
											        </tr>
											    </thead>
												<tbody>';
        foreach ( $usuarios as $usuario ) {
            echo '<tr>';
            echo '<td>' . $usuario->getNivelDiscente() . '</td>';
            echo '<td>' . $usuario->getStatusDiscente () . '</td>';
            echo '<td>' . $usuario->getStatusServidor () . '</td>';
            echo '<td>' . $usuario->getTipodeUsuario () . '</td>';
            echo '<td>' . $usuario->getCategoria() . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>
            </div>';
        
    }
    
    public function mostraSelecionado(Usuario $usuario) {
        echo '<h2 class="titulo">Cartão Próprio -> Usuário Selecionado</h2>';
        echo '<div class="doze colunas borda">';
       
        //Descomente esta linha para ativar o botão para cadastrar foto.
        // 		echo '<a href="?pagina=cartao&selecionado='.$_GET['selecionado'].'&foto=1" class="botao">Adicionar Foto</a>';
        
        if(isset($_GET['foto'])){
            
            echo '	<div class="doze colunas fotos">
					    <div class="quatro colunas">
							<div class="borda-foto">
								<div class="foto-salva">';
            
            if(file_exists('fotos/'.$_GET['selecionado'].'.png')){
                
                echo '<img src="fotos/'.$_GET['selecionado'].'.png" />';
                
            }else{
                
                echo '<img src="img/camera.png" />';
            }
            
            echo '      </div>
                    </div>
				</div>
                
					    <div class="quatro colunas">
							<div class="borda-foto">
                
								<img id="marcacao" src="img/avatar.png"/>
                
					        	<div class="tela">
									<video width="320" height="200" id="video" autoplay></video>
					        	</div>
								<div class="centralizado">
					                <button id="btnStart" class="botao icone-switch title="Ligar Câmera"></button>
					                <button id="btnStop" class="botao icone-cross"></button>
					                <button id="btnPhoto" class="botao icone-camera"></button>
									<button id="btnUser" class="botao icone-user"></button>
								</div>
							</div>
					    </div>
                
					    <div class="quatro colunas">
							<div class="borda-foto ">
								<div class="foto-salva">
							    	<canvas id="canvas" width="320" height="240"></canvas>
							    </div>
								<div class="centralizado">
									<form class="formulario" id="formulario" enctype="multipart/form-data" action="enviar.php" method="POST" id="youform" name="youform">
								        <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
								        <input name="img64" id="img64" type="hidden" />
								        <input name="id_usuario" id="id_usuario" value="'.$_GET['selecionado'].'" type="hidden" />
								        <input class="" type="submit" value="Enviar arquivo" onsubmit="document.getElementById(\'img64\').value = img" />
							        </form>
							    </div>
					    	</div>
					    </div>
								            
					</div>
					<hr class="um"/>
					';
            
            
        }
        
        echo '<div class="doze colunas">
                <p>Dados Pessoais</p>
				<table  class="tabela borda-vertical zebrada">
					<tr><th>Nome:</th><td> ' . $usuario->getNome () . '.</td>';
        echo '
             		</tr>
					<tr><th>Login:</th><td>'. $usuario->getLogin () .'.</td></tr>
					<tr><th>Identidade: </th><td>' . $usuario->getIdentidade () . '.</td></tr>
					<tr><th>CPF:</th><td>' . $usuario->getCpf () . '.</tr>
					<tr></td><th>Passaporte:</th><td>' . $usuario->getPassaporte() . '</td></tr>';
        echo '</table></div></div>';
    }
    public function botaoAdicionarCartao($idDoSelecionado)
    {
        echo '<a class="botao" href="?pagina=cartao_proprio&selecionado=' . $idDoSelecionado . '&add_cartao=add">Adicionar</a>';    
    }
    public function mostraVinculos($lista, $titulo = "Vinculos"){
        if(!count($lista))
        {
            echo '<div class="borda"><p>Nenhum ítem na lista</p></div>';
            return;
        }
        echo '<h2 class="titulo">'.$titulo.'</h2>';
        echo '<div class="borda">
						<table class="tabela borda-vertical zebrada texto-preto">';
        echo '<tr>
                <th>ID</th>
				<th>Tipo</th>
				<th>Cartão</th>
				<th>Validade</th>
			    <th>Isento</th>
                <th>Avulso</th>
                <th>Ação</th>
            </tr>';
        
            foreach ( $lista as $vinculo) {
                echo '<tr>';
                echo '<td>'.$vinculo->getId().'</td>
                      <td>' .$vinculo->getCartao()->getTipo()->getNome() . '</td>
                      <td>' . $vinculo->getCartao()->getNumero() . '</td>
                      <td>' . date("d/m/Y G:i:s", strtotime($vinculo->getFinalValidade())) . '</td>';
                        
                if($vinculo->getIsencao()->getId())
                {
                    echo '<td>Isento</td>';
                }
                else
                {
                    echo '<td>Não Isento</td>';
                }
                if($vinculo->isAvulso()){
                    echo '<td>Avulso</td>';
                }else{
                    echo '<td>Próprio</td>';
                }
                if($vinculo->isActive()){
                    echo '<td><a href="?pagina=cartao_proprio&selecionado='.$vinculo->getResponsavel()->getIdBaseExterna().'&vinculo_cancelar='.$vinculo->getId().'" class="botao">Cancelar</a></td>';
                }
                else
                {
                    if(!$vinculo->isAvulso()){
                        echo '<td><a href="?pagina=cartao_proprio&selecionado='.$vinculo->getResponsavel()->getIdBaseExterna().'&vinculo_renovar='.$vinculo->getId().'" class="botao">Renovar</a></td>';
                    }
                    else{
                        echo '<td>--</td>';
                    }
                }
                                    
                echo '</tr>';
                
            }
            echo '</table></div>';
            
            
    }
    /**
     * Exibe um formulário de confirmação com o qual um post de valor confirmar será
     * enviado. 
     * 
     * @param string $texto mensagem a ser exibida no formulário. 
     * 
     */
    public function formConfirmacao($texto = "Deseja Confirmar?"){
        echo '	<div class="borda doze colunas">';
        echo '		<div class="alerta-ajuda">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">'.$texto.'</div>
					</div>';
        echo '		<form action="" method="post">
						<input type="submit" class="botao" value="Confirmar" name="certeza" />
					</form>
				</div>';
    }
    
    public function sucesso($texto = "Dados alterados com sucesso!"){
        echo '	<div class="borda doze colunas">';
        echo '		<div class="alerta-sucesso">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">'.$texto.'</div>
					</div></div>';
    }
    public function erro($texto = "Erro ao alterar os dados!"){
        echo '	<div class="borda doze colunas">';
        echo '		<div class="alerta-erro">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">'.$texto.'</div>
					</div></div>';
    }
    
    public function formAddCartao($listaDeTipos, $idSelecionado){
        if(!count($listaDeTipos)){
            $this->erro("Nenhum Vínculo Ativo Encontrado.");
            return;
        }
        echo '
				<script>
				  $(document).bind(\'autofocus_ready\', function() {
				    if (!("autofocus" in document.createElement("input"))) {
				      $("#numero_cartao2").focus();
				    }
				  });
				</script>';
        echo '<div class="doze colunas borda">
				<form method="get" action="" class="formulario texto-preto" >
						    <label for="numero_cartao2">Número do Cartão</label>
								<input type="text" name="numero_cartao2" id="numero_cartao2" autofocus/>
								 <script>$(document).trigger(\'autofocus_ready\');</script>
						     <label for="id_tipo">Tipo</label>
						       <select id="id_tipo" name="id_tipo">';
        foreach ( $listaDeTipos as $tipo) {
            echo '<option value="' . $tipo->getId() . '">' . $tipo->getNome() . '</option>';
        }
        echo '
            
			        </select>
				<input type="hidden" name="pagina"  value="cartao_proprio"/>
                <input type="hidden" name="add_cartao"  value="add"/>
				<input type="hidden" name="add_cartao_numero"  value="add"/>
				<input type="hidden" name="selecionado"  value="' . $idSelecionado . '"/>
			   <br> 	<input  type="submit"  name="salvar" value="Salvar"/>
			</form>
			</div>';
    }
    
 
    
    
}

?>