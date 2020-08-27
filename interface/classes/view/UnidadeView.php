<?php

class UnidadeView
{

    public function mostraFormInserir()
    {
        echo '	<h2 class="titulo">Adicionar Unidade Acadêmica</h2>
				<div class="borda">
										<form id="form-adiciona-unidade" method="get" action="" class="formulario sequencial" >
											<input type="hidden" name="pagina" value="definicoes_unidade" />
											<label for="cadastrar_unidade" class="texto-preto">
										        Unidade Acadêmica: <input type="text" name="cadastrar_unidade" id="cadastrar_unidade" />
										    </label>
											<input type="submit" name="salvar" value="Salvar" />
										</form>
				</div>';
    }

    public function formConfirmacao($texto)
    {
        echo '<div class="borda">';
        
        echo '		<div class="alerta-ajuda">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">' . $texto . '</div>
					</div>';
        echo '	<form action="" method="post" class="formulario sequencial texto-preto">
							<input type="hidden" name="certeza_cadastrar_unidade" value="' . $_GET['cadastrar_unidade'] . '" />
							<input  type="submit"  name="certeza" value="Confirmar"/>
					</form>
					</div>';
    }

    /**
     *
     * @deprecated
     */
    public function mostraFormulario()
    {
        echo '
			<form action="" method="post">
				<fieldset>
					<legend>Unidade Academica</legend>
					<label for="unid_nome">Nome da Unidade Academica</label><br>
					<input id="unid_nome" type="text" name="unid_nome"/>
					<br>
					<input type="submit" value="Enviar" />
			</fieldset>
	</form>';
    }

    public function mostraLista($lista)
    {
        foreach ($lista as $elemento) {
            echo 'ID: ' . $elemento->getId() . '<br>';
            echo 'Nome: ' . $elemento->getNome() . '<br> ';
            echo '<a href="?delete_unidade=1&unid_id=' . $elemento->getId() . '"> Deletar</a><br>';
        }
    }

    /**
     *
     * @param string $tipo
     *            Tipo = -sucesso, -erro, -ajuda
     * @param string $texto
     */
    public function formMensagem($tipo, $texto)
    {
        //
        echo '		<div class="alerta' . $tipo . '">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">' . $texto . '</div>
					</div>';
    }

    public function cadastroSucesso()
    {
        echo "Inserido com sucesso";
    }

    public function deleteSucesso()
    {
        echo "Deletado com sucesso";
    }

    public function deleteFracasso()
    {
        echo "Erro ao tentar deletar";
    }

    public function cadastroFracasso()
    {
        echo "Erro ao tentar inserir";
    }

    public function listarUnidadesAcademicas($unidadesAcademicas)
    {
        echo '<div class="doze linhas borda">
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
        
        foreach ($unidadesAcademicas as $unidade) {
            $this->linhaUnidadeAcademica($unidade);
        }
        
        echo '
	        
											    </tbody>
											</table>
										</div>';
    }

    public function linhaUnidadeAcademica(Unidade $unidade)
    {
        echo '<tr>
                <td>' . $unidade->getId() . '</td>
				<td>' . $unidade->getNome() . '</td><td>';
        $i = 0;
        foreach ($unidade->getTurnosValidos() as $turno) {
            if ($i){
                echo '\ ';
            }
            echo $turno->getDescricao();
            $i ++;
        }
        echo '</td>
                <td class="centralizado">
                    <a href="?pagina=definicoes_unidade&turno_na_unidade=' . $unidade->getId() . '"><span class="icone-pencil2 texto-amarelo2 botao" title="Editar"></span></a>
                    <a href="?pagina=definicoes_unidade&excluir_turno_da_unidade=' . $unidade->getId() . '"><span class="icone-cross texto-vermelho2 botao" title="Exluir"></span></a>
				</td>
			</tr>';
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
        if(!count($listaDeTurnos)){
            echo "Não existem turnos nesta Unidade Acadêmica";
            return;
        }
        echo '<h2 class="titulo">Deseja remover um turno na unidade: '.$unidade->getNome().'</h2>
                <div class="borda">
						<form action="" method="post" class="formulario sequencial">
								<select name="id_turno">';
        foreach ( $listaDeTurnos as $turno ) {
            echo '<option value="'.$turno->getId().'">' . $turno->getDescricao () . '</option>';
        }
        echo '<input type="hidden" name="id_unidade" value="' . $unidade->getId () . '">';
        echo '</select><input type="submit" name="excluir_turno_da_unidade"></form></div>';
        
        
    }
}

?>