<?php


class DefinicoesView{
	
	public function formInserirUnidade(){
		echo '	<h2 class="titulo">Adicionar Unidade Acadêmica</h2>
				<div class="borda">							
										<form id="form-adiciona-unidade" method="get" action="" class="formulario sequencial" >
											<input type="hidden" name="pagina" value="definicoes" />
											<label for="cadastrar_unidade" class="texto-preto">
										        Unidade Acadêmica: <input type="text" name="cadastrar_unidade" id="cadastrar_unidade" />
										    </label>
											<input type="submit" name="salvar" value="Salvar" />
										</form>
				</div>';
		
	}
	
	public function listarCatracas($catracas){
		
		echo '	<div class="doze linhas borda">
							<table id="turno" class="tabela borda-vertical zebrada texto-preto no-centro">
							<thead>
							<tr>
							<th>ID</th>
							<th>IP</th>
							<th>Tempo De Giro</th>
							<th>Operação</th>
							<th>Nome</th>
							<th>Unidade Acadêmica</th>
							<th>Interface de Rede</th>
							<th>Financeiro</th>
							<th class="centralizado">Ações</th>
							</tr>
							</thead>
							<tbody>';
		
		foreach ($catracas as $catraca){
			$this->linhaCatraca($catraca);
		}
		
		echo '
											       
											    </tbody>
											</table>
										</div>';
		
	}

	public function linhaCatraca(Catraca $catraca){
		echo '<tr>';
		echo '<td>'.$catraca->getId().'</td>';
		echo '<td>'.$catraca->getIp().'</td>';
		echo '<td>'.$catraca->getTempodeGiro().'</td>';
		echo '<td>'.$catraca->getStrOperacao().'</td>';
		echo '<td>'.$catraca->getNome().'</td>';
		echo '<td>'.$catraca->getUnidade()->getNome().'</td>';
		echo '<td>'.$catraca->getStrIterfaceRede().'</td>';
		echo '<td>'.$catraca->getStrFincaneito().'</td>';
		echo '<td><a href="?pagina=definicoes&editar_catraca='.$catraca->getId().'"><span class="icone-pencil2 botao texto-amarelo2" title="Editar"></span></a>';
						
		echo '</td>';
		echo '</tr>';
	}
	
	public function listarUnidadesAcademicas($unidadesAcademicas){
		echo '		
										<div class="doze linhas borda">										
											<table id="turno" class="tabela borda-vertical zebrada no-centro">
												<thead>
											        <tr>
											            <th>ID</th>
											            <th>Unidade Acadêmica</th>
											            <th>Turnos</th>						            
											            <th class="centralizado">Ações</th>				            
											        </tr>
											    </thead>				
												<tbody>';
		
		foreach ($unidadesAcademicas as $unidade){
			$this->linhaUnidadeAcademica($unidade);
		}
		

		echo '
											        								        
											    </tbody>
											</table>												
										</div>';
		
		
	}
	public function linhaUnidadeAcademica(Unidade $unidade){
		echo '
											        <tr>
											            <td>'.$unidade->getId().'</td>
											            <td>'.$unidade->getNome().'</td>

											            		<td>';

		$i = 0;
		foreach ($unidade->getTurnosValidos() as $turno){
			if($i)
				echo '\ ';
			echo $turno->getDescricao();
			$i++;
		}
		echo '											</td>
											            <td class="centralizado">
											            	<a href="?pagina=definicoes&turno_na_unidade='.$unidade->getId().'"><span class="icone-pencil2 texto-amarelo2 botao" title="Editar"></span></a>
 															<a href="?pagina=definicoes&excluir_turno_da_unidade='.$unidade->getId().'"><span class="icone-cross texto-vermelho2 botao" title="Exluir"></span></a>
											            </td>
				</tr>';
	}
	public function formAdicionarTurnoNaUnidade(){
		echo '
					
		
										<div class="formulario sequencial borda">
											<form action="">
												<label for="opcoes-1">
												<object class="rotulo texto-preto">Turno: </object>
												<select name="opcoes-1" id="opcoes-1" class="texto-preto">
													<option value="1">Almoço</option>
												</select>
											</label>
											<input type="submit" value="Adicionar" />
											</form>
										</div>';
	}

	public function listarTurnos($turnos){
		
		
		echo '
								<div class="doze linhas borda">										
										<table class="tabela borda-vertical zebrada texto-preto no-centro">
											<thead>
										        <tr>
										            <th>ID</th>
										            <th>Turno</th>
										            <th>Início</th>
										            <th>Fim</th>
										            <th>Ações</th>				            				            
										        </tr>
										    </thead>				
											<tbody>';
		
		foreach($turnos as $turno){
			$this->mostraLinhaTurno($turno);
		}
		
		
		echo '									       
										    </tbody>
										</table>																			
									</div>';
	}
	public function mostraLinhaTurno(Turno $turno){
		echo '
										        <tr>
										            <td>'.$turno->getId().'</td>
										            <td>'.$turno->getDescricao().'</td>
										            <td>'.$turno->getHoraInicial().'</td>
										            <td>'.$turno->getHoraFinal().'</td>
										            <td class="centralizado">
										            	<a href="?pagina=definicoes&id_turno='.$turno->getId().'&editar"><span class="icone-pencil2 botao texto-amarelo2" title="Editar"></span></a>										
										            </td>
										        </tr>';		
	}
	
	public function formAdicionarTurno(){
		echo '<h2 class="titulo">Adicionar Turnos</h2>
									<div class="borda">
											<form method="get" action="" class="formulario" >
											<input type="hidden" name="pagina" value="definicoes" />
											<label for="turno_nome" class="">
										        Turno: <input type="text" name="turno_nome" id="turno" />
										    </label><br>
										    <label for="hora_inicio" class="">
										        Hora Inicio: <input type="time" name="hora_inicio" id="hora_inicio"/>
										    </label><br>
										    <label for="hora_fim" class="">
										        Hora Fim: <input type="time" name="hora_fim" id="hora_fim"/>
										    </label><br>										    
										    <input type="submit" name="cadastrar_turno" value="Salvar" />
											</form>
										</div>';
	}
	
	
	
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
				    					<input type="hidden" name="pagina"  value="definicoes" />
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
									            <th>Vavor por refeição</th>
									            <th>Status</th>							            
									            <th>Ações</th>				            				            
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
				<td>Ativado</td>								           					            
				<td class="centralizado">
				<a href="?pagina=definicoes&editar_tipo='.$tipo->getId().'"><span class="icone-pencil2 botao texto-amarelo2" title="Editar"></span></a>
				<a href=""><span class="icone-checkmark botao texto-verde2" title="Ativar"></span></a>
				</td>
				</tr>	';
		
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
	
	public function formAlterarCustoRefeicao($custoAtualRefeicao){
		$custoAtualRefeicao = floatval($custoAtualRefeicao);	
		
		echo '	<h2 class="titulo">Valor de Custo Padrão da Refeição: R$' . number_format($custoAtualRefeicao, 2, ',', '.').'</h2>
				<div class="borda doze colunas">					
						<form id="form-custo-refeicao" action="" class="formulario">
							<label for="custo_refeicao" class="">
								Valor de Custo Refeição:  <input type="number"  max="100"  step="0.01" name="custo_refeicao" id="custo_refeicao" value="'.$custoAtualRefeicao.'" />
							</label><br>
							<input type="hidden" name="pagina" value="definicoes" />
							<input type="submit" name="alterar" value="Alterar" class="botao"/>
						</form>';		
		
		if (@$_REQUEST['custo_refeicao']){
			$this->formMensagem("-ajuda", "Deseja Alterar o Custo Atual?");
			echo '	<form id="custo-refeicao" method="post" class="formulario-organizado">
							<input type="hidden" name="custo_refeicao" value="'.$_GET['custo_refeicao'].'">
							<input type="submit" name="confirmar" value="Confirmar">
					</form>';
		}
		
		$var = "";
		$var = @$_REQUEST['info_custo'];
		$this->formMesagem2($var);
		echo '	</div>';		
		
	}
	
	public function formAlterarCustoCartao($custoAtualCartao){
		$custoAtualCartao= floatval($custoAtualCartao);
		echo '	<h2 class="titulo">Custo do cartão: R$' . number_format($custoAtualCartao, 2, ',', '.').'</h2>
				<div class="borda">
					<form id="form-custo-cartao" action="" class="formulario sequencial">
						<label for="custo_cartao" class="">
						Valor de Custo Refeição: <input type="number"  max="100"  step="0.01" name="custo_cartao" id="custo_cartao" value="'.$custoAtualCartao.'" />
						</label>
						<input type="hidden" name="pagina" value="definicoes" />
						<input type="submit" name="alterar" value="Alterar" />
					</form>';
		
		if (@$_REQUEST['custo_cartao']){
			$this->formMensagem("-ajuda", "Deseja Alterar o Custo Atual?");			
		echo '		<form id="custo-cartao" method="post" class="formulario-organizado">
							<input type="hidden" name="custo_refeicao" value="'.$_GET['custo_cartao'].'">
							<input type="submit" name="confirmar" value="Confirmar">
					</form>';
		}
		
		$var = "";
		$var = @$_REQUEST['info_cartao'];
		$this->formMesagem2($var);
		
		echo '	</div>';
	
	}
	public function mostraSucesso($mensagem){
		echo '<div class="borda"><p>'.$mensagem.'</p></div>';
	
	}
	public function formEditarCatraca(Catraca $catraca, $listaDeUnidades){	
		
		echo '	<h2 class="titulo">
				Editar Catraca : '.$catraca->getNome().'<br> 
				IP: '.$catraca->getIp().'</h2>
				<div class="borda">						
					<form action="" method="post" class="formulario em-linha">';	
		
		echo '	<label for="nome_catraca">
					Nome da Catraca: <input  type="text" name="nome_catraca" id="nome_catraca" value="'.$catraca->getNome().'">
				</label><br>				
				<label for="unidade_academica">
				<object class="rotulo">Unidade Acadêmica: </object>';		
		echo '<select name="id_unidade" id="unidade_academica">';
		foreach ($listaDeUnidades as $unidade){
			$atributo = "";
			if($unidade->getId() == $catraca->getUnidade()->getId())
				$atributo = "selected";
			echo '<option value="'.$unidade->getId().'"'.$atributo.'>'.$unidade->getNome().'</option>';
		}
		echo '</select></label><br>';
	
		switch ($catraca->getOperacao()){
			case Catraca::GIRO_ANTI_VAL_HOR_BLOQ:
				$horario = "";
				$anti = "selected";
				$livre = "";
				$horario_anti = "";
				$anti_horario = "";
			break;
			case Catraca::GIRO_HOR_VAL_ANTI_BLOQ:
				$horario = "selected";
				$anti = "";
				$livre = "";
				$horario_anti = "";
				$anti_horario = "";
				break;
			case Catraca::GIRO_LIVRE:
				$horario = "";
				$anti = "";
				$livre = "selected";
				$horario_anti = "";
				$anti_horario = "";
				break;
			case Catraca::GIRO_ANTI_LIVRE_HOR_VAL:
				$horario = "";
				$anti = "";
				$livre = "";
				$horario_anti = "";
				$anti_horario = "selected";
				break;
			case Catraca::GIRO_HOR_LIVRE_ANTI_VAL:
				$horario = "";
				$anti = "";
				$livre = "";
				$horario_anti = "selected";
				$anti_horario = "";
				break;
			default:
				$horario = "selected";
				$anti = "";
				$livre = "";
				$horario_anti = "";
				$anti_horario = "";
			break;			
		}
		echo '<label for="operacao"><object class="rotulo">Operação: </object>';
		echo '<select name="operacao" id="operacao">';
		echo '<option '.$horario.' value="'.Catraca::GIRO_HOR_VAL_ANTI_BLOQ.'" '.$horario.'>Sentido Horário</option>';
		echo '<option '.$anti.' value="'.Catraca::GIRO_ANTI_VAL_HOR_BLOQ.'" '.$anti.'>Sentido Anti-Horário</option>';
		echo '<option '.$horario_anti.' value="'.Catraca::GIRO_HOR_LIVRE_ANTI_VAL.'" '.$horario_anti.'>Horário(LIVRE) / Anti-Horário(VALIDADO)</option>';
		echo '<option '.$anti_horario.' value="'.Catraca::GIRO_ANTI_LIVRE_HOR_VAL.'" '.$anti_horario.'>Anti-Horário(LIVRE) / Horário(VALIDADO)</option>';
		echo '<option  '.$livre.' value="'.Catraca::GIRO_LIVRE.'" '.$livre.'>Livre</option>';
		echo '</select></label><br>';
		echo '<label for="tempo_giro">Tempo De Giro: ';
		echo '<input type="number" min="0" max="100" step="1" name="tempo_giro" id="tempo_giro" value="'.$catraca->getTempodeGiro().'"/></label><br>';
		
		echo '	<label for="interface">
				<object class="rotulo">Interface de Rede: </object>
				<select name="interface" id="interface">';
		if ($catraca->getInterfaceRede() == 'eth0'){
		echo '  <option selected="selected" value="eth0">Rede Cabeada</option>
				<option value="wlan0">Rede Sem fio</option>';
		} elseif ($catraca->getInterfaceRede() == 'wlan0'){
		echo '	<option value="eth0">Rede Cabeada</option>
				<option selected="selected" value="wlan0">Rede Sem fio</option>';
		}else{
		echo '	<option selected="selected" value="eth0">Não Identificado</option>
				<option value="eth0">Rede Cabeada</option>
				<option value="wlan0">Rede Sem fio</option>';
		}
		echo'	</select>
				</label><br>
				<label for="financeiro">
				<object class="rotulo">Financeiro: </object>
				<select name="financeiro" id="financeiro">';

		if(!$catraca->financeiroAtivo()){
		echo '	<option selected="selected" value="0">Desabilitado</option>
				<option value="1">Habilitado</option>';
		}else{
		echo '	<option value="0">Desabilitado</option>
				<option selected="selected" value="1">Habilitado</option>';
			
		}
		echo '
				</select>
				</label>';	
		echo '<input type="hidden" name="id_catraca" value="' . $catraca->getId() . '"><br>';
		echo '<input type="submit" name="salvar" value="Salvar"></form></div>';
		return ;
	
	}
	
	public function formTurnoNaUnidade(Unidade $unidade, $listaDeTurnos){
		
		echo '	<h2 class="titulo">Adicionar Turno à Unidade: '.$unidade->getNome().'</h2>
					<div class="borda">					 
					<form action="" method="post" class="formulario sequencial">
								<select name="id_turno">';
		foreach ( $listaDeTurnos as $turno ) {
			echo '<option value="'.$turno->getId().'">' . $turno->getDescricao () . '</option>';
		}
		echo '</select><input type="hidden" name="id_unidade" value="' . $unidade->getId () . '">';
		echo '<input type="submit" name="turno_na_unidade"></form></div>';
		
		
	}
	public function formExcluirTurnoDaUnidade(Unidade $unidade){
		$listaDeTurnos = $unidade->getTurnosValidos();
	
		echo '<div class="borda">
						Deseja remover um turno na unidade: '.$unidade->getNome().'
						<form action="" method="post" class="formulario sequencial">
								<select name="id_turno">';
		foreach ( $listaDeTurnos as $turno ) {
			echo '<option value="'.$turno->getId().'">' . $turno->getDescricao () . '</option>';
		}
		echo '<input type="hidden" name="id_unidade" value="' . $unidade->getId () . '">';
		echo '</select><input type="submit" name="excluir_turno_da_unidade"></form></div>';
	
	
	}
	
	public function formAdicionarCatracaVirtual(){
		
		echo '	<h2 class="titulo">Adicionar Catraca Virtual<br></h2>
				<div class="borda">					
					<form action="" method="get" class="formulario em-linha">
					<input type="hidden" name="pagina" value="definicoes" />
					<label for="nome_catraca">
						Nome da Catraca: <input type="text" name="nome_catraca" id="nome_catraca" value="">
					</label><br>
						<input type="submit" name="adicionar" value="Adicionar">
					</form>
				</div>';
		
	}
	
	public function formEditarTurno(Turno $turno){
		echo'	<h2 class="titulo">Editar Turno: '.$turno->getDescricao().'</h2>
				<div class="borda">					
					<form method="post" class="formulario-organizado">
						<label for="hora_inicio">
							Hora Inicio: <input type="time" name="hora_inicio" value="'.$turno->getHoraInicial().'">
						</label>
						<label for="hora_inicio">
							Hora Fim: <input type="time" name="hora_fim" value="'.$turno->getHoraFinal().'">
						</label>
						<input type="submit" class="botao" value="Alterar" name="confirmar">
					</form>
				</div>';
	}
	
	public function formCustoUnidade($listaUnidade, $listaCusto){
		
		echo'	<h2 class="titulo">Adicionar um custo para Unidade.</h2>
				<div class="borda">					
					<form id="form-custo-unidade" action="" method="get" class="formulario-organizado">
						<label>
							<object class="rotulo">Unidade Academica: </object>
							<select name="unidade" id="unidade">';		
		foreach ($listaUnidade as $unidade){
			echo'					<option value="'.$unidade->getId().'">'.$unidade->getNome().'</option>';
		}
		echo'				</select>
						</label>
						<label>
							<object class="rotulo">Valor de Custo: </object>
							<select name="valor_custo" id="valor_custo">';		
		foreach ($listaCusto as $linha){
			echo'				<option value="'.$linha['cure_id'].'">'.$linha['cure_valor'].'</option>';
		}
		echo'			</select>
						</label>
						<input type="hidden" name="pagina" value="definicoes" />
						<input type="submit" value="Salvar" name="salvar">
					</form>';		
		
		if (@$_REQUEST['unidade']){
			if(strlen($_GET['valor_custo']) < 1 || strlen($_GET['unidade']) < 1){
				return;
			}
				
			$this->formMensagem("-ajuda", "Deseja incluir este custo na unidade?");
			echo '		<form id="form-confirma" method="post" class="formulario-organizado">
							<input type="hidden" name="unidade" value="'.$_GET['unidade'].'">
							<input type="hidden" name="valor_custo" value="'.$_GET['valor_custo'].'">
							<input type="submit" name="confirmar" value="Confirmar">
						</form>';
		}
		$var = "";
		$var = @$_REQUEST['info_unidade'];
		$this->formMesagem2($var);		
		echo '	</div>';
		
	}
	
	public function listaCustoUnidade($listaCustoUnidade){
		
		echo '	<div class="borda">';
		
		if (@$_REQUEST['excluir']){		
		$this->formMensagem("-ajuda", "Deseja remover o custo de: ".$_GET['unidade_nome']."?");
		echo'	<form method="post" class="formulario-organizado">
					<input type="submit" class="botao" value="Confirmar" name="confirmar">
				</form>';
		}
		
		echo '		<table id="turno" class="tabela borda-vertical zebrada texto-preto no-centro">
						<thead>
							<tr>
								<th>ID</th>
								<th>Unidade</th>
								<th>Valor Custo</th>
								<th>Ação</th>
							</tr>
						</thead>
						<tbody>';
		$i = 0;
		foreach ($listaCustoUnidade as $unidade){
			$i++;
			$custoRefeicao = $unidade->getCustoUnidade();
			if (!$custoRefeicao){
				$custoRefeicao = "Valor padrão";
			}				
			echo'				<tr>
								<td>'.$i.'</td>
								<td>'.$unidade->getNome().'</td>
								<td>'.$custoRefeicao.'</td>
								<td><a href="?pagina=definicoes&custo_unidade_id='.$unidade->getId().'
										&unidade_nome='.$unidade->getNome().'&excluir=1" class="botao">Excluir</a></td>
							</tr>';
		}
		echo'			</tbody>
				</div>';
	}
	
	public function formEditarMensagensCatraca($args){
		echo' 	<form action="" class="formulario-organizado" method="">
						<input type="hidden" name="pagina" value="definicoes"/>
						<div class="doze colunas">
						<hr class="um">
						<h2 class="titulo">Unidade: '.$args[0].'</h2>
						<h2 class="titulo">Catraca: '.$args[1].'</h2>
						<hr class="um">
						<div class="tres colunas">
							<label for="">
								<input type="text" name="msg_um" maxlength="16" placeholder="1ª Mensagem" value="'.$args[2].'">
							</label>
						</div>
		
						<div class="tres colunas">
							<label for="">
								<input type="text" name="msg_dois" maxlength="80" placeholder="2ª Mensagem" value="'.$args[3].'">
							</label>
						</div>
		
						<div class="tres colunas">
							<label for="">
								<input type="text" name="msg_tres" maxlength="16" placeholder="3ª Mensagem" value="'.$args[4].'">
							</label>
						</div>
		
						<div class="tres colunas">
							<label for="">
								<input type="text" name="msg_quatro" maxlength="80" placeholder="4ª Mensagem" value="'.$args[5].'">
							</label>
						</div>
							<input type="hidden" name="id_catraca" value="'.$_GET['catraca'].'"/>
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
								<th>Ações</th>
							</tr>
						</thead>
					<tbody>';
		foreach ($listaMensagemCatraca as $linha){
			echo'				<tr>
								<td>'.$linha['unid_nome'].'</td>
								<td>'.$linha['catr_nome'].'</td>
								<td>'.substr ($linha['mens_institucional1'],0,16).'</td>
								<td>'.substr ($linha['mens_institucional2'],0,80).'</td>
								<td>'.substr ($linha['mens_institucional3'],0,16).'</td>
								<td>'.substr ($linha['mens_institucional4'],0,80).'</td>
								<td><a href="?pagina=definicoes&editar=1&catraca='.$linha['catr_id'].'&unidade='.$linha['unid_id'].'"
										class="botao icone-pencil2 texto-amarelo2" title="Editar"></a>
									<a href="?pagina=definicoes&id-catraca='.$linha['catr_id'].'&excluir=1"
										class="botao icone-cross texto-vermelho2" title="Excluir"></a>
								</td>
							</tr>';
		}
		echo '			</tbody>
					</table>';
		
	}
	
	public function formMensagem($tipo, $texto){
 		//Tipo = -sucesso, -erro, -ajuda
 		echo '		<div class="alerta'.$tipo.'">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">'.$texto.'</div>
					</div>'; 		
 	}
 	
 	public function formMesagem2($var){
 	
 		if ($var == "sucesso"){
 			echo '		<div class="alerta-sucesso">
					    	<div class="icone icone-notification ix16"></div>
					    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
					    	<div class="subtitulo-alerta">Dados salvos com sucesso!</div>
						</div>';
 			echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
 		} 	
 		
	 	if ($var == "alterado"){
			echo '	<div class="alerta-sucesso">
					    	<div class="icone icone-notification ix16"></div>
					    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
					    	<div class="subtitulo-alerta">Dados altedados  com sucesso!</div>
						</div>';
			echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
		}
			
		if ($var == "erro"){
			echo '	<div class="alerta-erro">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">Erro ao alterar os dados!</div>
					</div>';
			echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
			
		}
 		
 	}
 	
}


?>