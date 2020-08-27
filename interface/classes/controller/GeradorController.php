<?php

/**
 *
 * @author Jefferson Uchoa Ponte
 *
 */
class GeradorController
{

    private $dao;

    public static function main($nivel)
    {
        if ($nivel == Sessao::NIVEL_SUPER) {
            
            $gerador = new GeradorController();
            $gerador->verificarSelecaoRU();
        }
    }

    public function verificarSelecaoRU()
    {
        $this->dao = new DAO();
        
        if (isset($_SESSION['catraca_id'])) {
            $this->paginaRegistroManual();
        } else {
            if (isset($_POST['catraca_id'])) {
                $_SESSION['catraca_id'] = intval($_POST['catraca_id']);
                echo '<meta http-equiv="refresh" content="0; url=?pagina=gerador">';
            }
            $this->selecionarRU();
        }
    }

    public function selecionarRU()
    {
        $unidadeDao = new UnidadeDAO($this->dao->getConexao());
        $listaDeCatracas = $unidadeDao->retornaCatracasPorUnidade();
        
        echo '<div class="navegacao">
				<div class = "simpleTabs">
			        <ul class = "simpleTabsNavigation">
						<li><a href="#">Cadastro</a></li>
			        </ul>
			        <div class = "simpleTabsContent">		
						<div class="doze colunas borda">';
        
        echo '<form action="" method="post">
				<label for="catraca_id">Selecione o Restaurante:</label><br>
			        <select name="catraca_id" id="catraca_id">';
        
        foreach ($listaDeCatracas as $catraca) {
            if (strpos($catraca->getNome(), 'ablet'))
                echo '<option value="' . $catraca->getId() . '">' . $catraca->getNome() . '</option>';
        }
        
        echo '       </select><br>
					<input type="submit" class="botao" VALUE="Selecionar" />
				</form>	';
        
        echo '</div>
				</div>
				</div>
				</div>';
    }

    public function paginaRegistroManual()
    {
        $tipoDao = new TipoDAO($this->dao->getConexao());
        $listaDeTipos = $tipoDao->retornaLista();
        
        $unidadeDao = new UnidadeDAO($this->dao->getConexao());
        $catraca = new Catraca();
        $catraca->setId($_SESSION['catraca_id']);
        $unidadeDao->preencheCatracaPorId($catraca);
        
        echo '<div class="navegacao"> 
				<div class = "simpleTabs">
			        <ul class = "simpleTabsNavigation">					
						<li><a href="#">Cadastro</a></li>	
			        </ul>
				
			        <div class = "simpleTabsContent">
				
						
				
						<div class="doze colunas borda">';
        
        echo '
							
							<table class="tabela borda-vertical zebrada no-centro centralizado">							    
							    <thead>
							        <tr>
												<th>Unidade</th>';
        foreach ($listaDeTipos as $tipo) {
            echo '<th>' . $tipo->getNome() . '</th>';
        }
        
        echo '
							        </tr>
							    </thead>
							    <tbody>';
        
        echo '					        <tr>
										<th>' . $catraca->getNome() . '</th>';
        foreach ($listaDeTipos as $tipo) {
            $j = $unidadeDao->totalDeGirosDaCatracaTurnoAtual($catraca, $tipo);
            echo '<td>' . $j . '</td>';
        }
        
        echo '
							        </tr>
									<tr>';
        
        echo '
									<tr>
										<th>Selecionar</th>';
        $listaDeCores = array(
            'aviso',
            'primario',
            'sucesso',
            'secundario',
            'erro',
            'erro',
            'primario',
            'sucesso',
            'secundario',
            'aviso',
            'erro'
        );
        $i = 0;
        foreach ($listaDeTipos as $tipo) {
            
            echo '<td><a href="?pagina=gerador&tipo_id=' . $tipo->getId() . '" class="botao b-' . $listaDeCores[$i] . ' icone-checkmar">+1</a></td>';
            $i ++;
        }
        
        echo '		
							            
							        </tr>
							    </tbody>
							</table>
				
						</div>';
        
        if (isset($_GET['tipo_id'])) {
            if (isset($_GET['confirmado'])) {
                
                $idTipo = intval($_GET['tipo_id']);
                $tipo = new Tipo();
                $tipo->setId($idTipo);
                
                $sql = "SELECT * FROM tipo WHERE tipo_id = $idTipo";
                
                foreach ($tipoDao->getConexao()->query($sql) as $linha) {
                    $tipo->setValorCobrado($linha['tipo_valor']);
                }
                $custo = 0;
                $sql = "SELECT cure_valor FROM custo_refeicao ORDER BY cure_id DESC LIMIT 1";
                foreach ($tipoDao->getConexao()->query($sql) as $linha) {
                    $custo = $linha['cure_valor'];
                }
                
                $data = date("Y-m-d G:i:s");
                
                $idCartao = 0;
                $idVinculo = 0;
                $numero = "";
                
                $sql = "SELECT * FROM cartao
				INNER JOIN vinculo ON cartao.cart_id = vinculo.cart_id
				WHERE tipo_id = $idTipo
				AND vinc_avulso = 'TRUE'
				LIMIT 1";
                foreach ($tipoDao->getConexao()->query($sql) as $linha) {
                    $idCartao = $linha['cart_id'];
                    $idVinculo = $linha['vinc_id'];
                    $numero = $linha['cart_numero'];
                    
                    break;
                }
                
                $idCatraca = $_SESSION['catraca_id'];
                $valorPago = $tipo->getValorCobrado();
                
                $sql = "INSERT into registro(regi_data, regi_valor_pago, regi_valor_custo, catr_id, cart_id, vinc_id) 
						VALUES('$data', $valorPago, $custo, $idCatraca, $idCartao, $idVinculo)";
                
                $selectTurno = "Select * FROM turno WHERE '$data' BETWEEN turno.turn_hora_inicio AND turno.turn_hora_fim";
                $result = $this->dao->getConexao()->query($selectTurno);
                $i = 0;
                
                foreach ($result as $linha) {
                    $i ++;
                    break;
                }
                if ($i == 0) {
                    $this->mensagemErro("Fora do hor&aacute;rio de refei&ccedil;&atilde;o");
                    echo '<meta http-equiv="refresh" content="1; url=?pagina=gerador">';
                    return;
                }
                if ($this->dao->getConexao()->exec($sql))
                    $this->mensagemSucesso();
                else
                    $this->mensagemErro();
                echo '<meta http-equiv="refresh" content="1; url=?pagina=gerador">';
            } else {
                echo '<div class="doze colunas borda centralizado"><p>Confirmar Envio de Dados?</p><a href="?pagina=gerador&tipo_id=' . $_GET['tipo_id'] . '&confirmado=1" class="botao b-sucesso">Confimar</a></div>';
            }
        }
        
        echo '</div>
				</div>';
    }

    public function mensagemSucesso()
    {
        echo '<div class="doze colunas borda centralizado">
		
										<div class="alerta-sucesso">
										    <div class="icone icone-download ix24"></div>
										    <div class="titulo-alerta">Ok</div>
										    <div class="subtitulo-alerta">Dados enviados com sucesso!</div>
										</div>
		
									</div>';
    }

    public function mensagemErro($strMensagem = null)
    {
        if ($strMensagem == null)
            $strMensagem = "Erro na tentativa de inserir os dados!";
        echo '<div class="doze colunas borda centralizado">
	
										<div class="alerta-erro">
										    <div class="icone icone-fire ix24"></div>
										    <div class="titulo-alerta">' . $strMensagem . '</div>
										    <div class="subtitulo-alerta">Erro!</div>
										</div>
	
									</div>';
    }
}

?>