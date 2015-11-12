<?php


class VinculoController{
	
	public static function main($tela){
		switch ($tela){
			case Sessao::NIVEL_SUPER:
				$controller = new VinculoController();
				$controller->telaVinculo();
				/*
				 * Queremos 
				 * um formulario de pesquisa. 
				 * Ao digitar um nome, vamos buscar. 
				 * Temos uma lista que tras SIAP, Matricula, Nome, documentos. 
				 * Vamos fazer o teste. 
				 * 
				 */
				
				break;
			case Sessao::NIVEL_DESLOGADO:
				break;
			default:
				break;
		}
		
		
	
	
	}
	public function telaVinculo(){
		
		echo '								
									<form method="post" action="" class="formulario em-linha" >
										<div class="borda">
										<label for="opcoes-1">
											<object class="rotulo texto-preto">Buscar por: </object>
											<select name="opcoes-1" id="opcoes-1" class="texto-preto">
												<option value="1">Nome</option>
												<option value="2">CPF</option>
												<option value="3">RG</option>
												<option value="3">Matrícula</option>							            										            
												<option value="3">Vinculo</option>
												<option value="3">SIAPE</option>
											</select>
											<input class="texto-preto" type="text" name="nome" id="campo-texto-2" /><br>
											<input type="submit" />											    
										</label>
									</form>';
		
		
		
		if(isset($_POST['nome'])){
			$pesquisa = preg_replace('/[^[:alnum:]]/', '',$_POST['nome']);
			$pesquisa = strtoupper($pesquisa);
			$sql = "SELECT * FROM vw_usuarios_catraca WHERE nome LIKE '%$pesquisa%'";
			$dao = new DAO(null, DAO::TIPO_PG_SIGAAA);
			$result = $dao->getConexao()->query($sql);
			echo '
											<div class="doze linhas">
												<br><h2 class="texto-preto">Resultado da busca:</h2><br><br>
											</div>
											<table class="tabela borda-vertical zebrada texto-preto">
												<thead>
											        <tr>
											            <th>Nome</th>
											            <th>CPF</th>
											            <th>Passaporte</th>
											            <th>Matrícula</th>
														<th>SIAPE</th>
											            <th>Selecionar</th>
											        </tr>
											    </thead>
												<tbody>';
												foreach($result as $linha){
													echo '<tr>';
													echo '<td>'.$linha['nome'].'</a></td>';
													echo '<td>'.$linha['cpf_cnpj'].'</td>';
													echo '<td>'.$linha['passaporte'].'</td>';
													echo '<td>'.$linha['matricula_disc'].'</td>';
													echo '<td>'.$linha['siape'].'</td>';
													echo '<td class="centralizado">
											            	<a href="?selecionado='.$linha['id_usuario'].'"><span class="icone-checkmark texto-verde2 botao" title="Selecionar"></span></a>
											            </td>';
													echo '</tr>';
												}
			echo '<br><br><br>
											    </tbody>
											</table>
										</div>';
				

		}
		if(isset($_GET['selecionado'])){
			if(is_int(intval($_GET['selecionado'])))
			{
				$dao = new DAO(null, DAO::TIPO_PG_SIGAAA);
				$id = intval($_GET['selecionado']);
				$sql = "SELECT * FROM vw_usuarios_catraca WHERE id_usuario = $id";
				$result = $dao->getConexao()->query($sql);
				foreach($result as $row){
					echo '<div class="borda">
									        Nome: '.$row['nome'].' CPF: '.$row['cpf_cnpj'].'
									        		</div>
									        		';
					
					break;
					
				}
				$dao->fazerConexao();
				$dao= new DAO(null, DAO::TIPO_PG_LOCAL);
				//Agora vamos pegar os vinculos ativos desse usuario. 
				$sql = "SELECT * FROM vinculo INNER JOIN usuario ON vinculo.usua_id = usuario.usua_id 
						WHERE usuario.usua_id = $id";
				$result = $dao->getConexao()->query($sql);
				foreach ($result as $row){
					print_r($row);
					
				}
				if(isset($_GET['cartao']))
				{
					echo '<form method="post" action="" class="formulario texto-preto" >
										<div class="borda">										
									    <label for="campo-texto-1">
									        Cartão: <input type="text" name="cartao" id="cartao" />
									    </label>
									    <label for="campo-texto-1">
									        Validade: <input type="date" name="validade" id="validade" />
									    </label>
									    <fieldset>
									        <legend>Cartão Avulso:</legend>
									        <label for="checkbox-1.1">
									            <input type="checkbox" name="checkbox-1" id="checkbox-1.1" value="1" /> Sim
									        </label>									        
									    </fieldset><br>
										
										<label for="campo-texto-1">
									        Quantidade de refeições: <input type="text" name="periodo" id="periodo" />
									    </label><br>

									   	<input type="submit" name="salvar" value="Salvar"/>
									   								    
									</form>';
				}
				else{
					echo '<a href="?selecionado='.$_GET['selecionado'].'&cartao=add">Adicionar</a>';
				}
				
				
			}
		}
	}

}


?>