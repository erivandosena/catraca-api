<?php


class CatracaView{
    public function mostrarFormCatraca(){
        
        echo '	<h2 class="titulo">Adicionar Catraca Virtual<br></h2>
				<div class="borda">
					<form action="" method="get" class="formulario em-linha">
					<input type="hidden" name="pagina" value="definicoes_catraca" />
					<label for="nome_catraca">
						Nome da Catraca: <input type="text" name="nome_catraca" id="nome_catraca" value="">
					</label><br>
						<input type="submit" name="adicionar" value="Adicionar">
					</form>
				</div>';
        
    }
    /**
     * Tipo = -sucesso, -erro, -ajuda
     * @param string $tipo
     * @param string $texto
     */
    public function formMensagem($tipo, $texto){
        
        echo '<div class="borda">';
        echo '		<div class="alerta'.$tipo.'">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">'.$texto.'</div>
					</div>';
        echo '</div>';
    }
    public function formConfirmacao(Catraca $catraca){
        echo '<div class="borda">';
        $this->formMensagem("-ajuda", "Tem certeza que deseja adicionar esta catraca? ".$catraca->getNome());
        echo '<form action="" method="post">
				<input type="hidden" value="'.$catraca->getNome().'" name="nome_catraca">
				<input type="submit" class="botao" value="Confirmar" name="confirmar" />
				</form>';
        echo '</div>';
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
        echo '<td><a href="?pagina=definicoes_catraca&editar_catraca='.$catraca->getId().'"><span class="icone-pencil2 botao texto-amarelo2" title="Editar"></span></a>';
        
        echo '</td>';
        echo '</tr>';
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
    
}



?>