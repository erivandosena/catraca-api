<?php

class TurnoView
{

    public function mostraFormulario()
    {
        echo '<h2 class="titulo">Adicionar Turnos</h2>
									<div class="borda">
											<form method="get" action="" class="formulario" >
											<input type="hidden" name="pagina" value="definicoes_turno" />
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

    public function mostraFormConfirmacao(Turno $turno)
    {
        echo '	<div class="borda">';
        echo '		<div class="alerta-ajuda">
				    	<div class="icone icone-notification ix16"></div>
				    	<div class="titulo-alerta">Aten&ccedil&atildeo</div>
				    	<div class="subtitulo-alerta">Você tem certeza que quer adicionar turno ' . $turno->getDescricao() . '? </div>
					</div>';
        echo '	<form action="" method="post" class="formulario sequencial texto-preto">
						<input type="hidden" name="hora_inicio" value="' . $turno->getHoraInicial() . '" />
						<input type="hidden" name="hora_fim" value="' . $turno->getHoraFinal() . '" />
						<input type="hidden" name="turno_nome" value="' . $turno->getDescricao() . '" />
						<input  type="submit"  name="certeza_cadastrar_turno" value="Confirmar"/>
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
										            	<a href="?pagina=definicoes_turno&id_turno='.$turno->getId().'&editar"><span class="icone-pencil2 botao texto-amarelo2" title="Editar"></span></a>
										            </td>
										        </tr>';
    }
    public function formEditar(Turno $turno){
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
    public function mostraMensagem($mensagem)
    {
        echo '<div class="borda"><p>' . $mensagem . '</p></div>';
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
}

?>