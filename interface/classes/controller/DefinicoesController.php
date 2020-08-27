<?php

/**
 *
 * @author Jefferson Uchoa Ponte
 *
 */
class DefinicoesController
{

    private $view;

    private $dao;

    public static function main($nivelDeAcesso)
    {
        switch ($nivelDeAcesso) {
            case Sessao::NIVEL_SUPER:
                $controller = new DefinicoesController();
                $controller->telaDefinicoes();
                break;
            case Sessao::NIVEL_ADMIN:
                $controller = new DefinicoesController();
                $controller->telaDefinicoes();
                break;
            default:
                UsuarioController::main($nivelDeAcesso);
                break;
        }
    }

    public function telaConfiguracaoUnidadeAcademica()
    {
        $this->view->formInserirUnidade();
        $unidadeDao = new UnidadeDAO($this->dao->getConexao());
        if (isset($_GET['cadastrar_unidade'])) {
            if (strlen($_GET['cadastrar_unidade']) > 3) {
                echo '<div class="borda">';
                $this->view->formMensagem("-ajuda", "Deseja adicionar: " . $_GET['cadastrar_unidade'] . "?");
                echo '	<form action="" method="post" class="formulario sequencial texto-preto">
							<input type="hidden" name="certeza_cadastrar_unidade" value="' . $_GET['cadastrar_unidade'] . '" />
							<input  type="submit"  name="certeza" value="Confirmar"/>
					</form>
					</div>';
            }
        }
        
        if (isset($_POST['certeza_cadastrar_unidade'])) {
            if (strlen($_GET['cadastrar_unidade']) > 3) {
                
                $unidade = $_POST['certeza_cadastrar_unidade'];
                $i = 0;
                $sql = "SELECT * FROM unidade WHERE unid_nome = '$unidade'";
                $result = $this->dao->getConexao()->query($sql);
                foreach ($result as $linha) {
                    $i ++;
                }
                if ($i == 0) {
                    $stmt = $this->dao->getConexao()->prepare("INSERT INTO unidade(unid_nome) VALUES(?);");
                    $stmt->bindParam(1, $unidade);
                    if ($stmt->execute()) {
                        $this->view->formMensagem("-sucesso", "Unidade inserida com sucesso!");
                        echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes&">';
                    } else {
                        $this->view->formMensagem("-erro", "Erro ao inseriri a unidade!");
                        echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
                    }
                } else {
                    $this->view->formMensagem("-erro", "Unidade já cadastrada!");
                    echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
                }
                return;
            }
        } else {
            $unidadesAcademicas = $unidadeDao->retornaLista();
            foreach ($unidadesAcademicas as $unidadeAcademica) {
                $unidadeDao->turnosDaUnidade($unidadeAcademica);
            }
            $this->view->listarUnidadesAcademicas($unidadesAcademicas);
            
            if (isset($_GET['turno_na_unidade'])) {
                $unidade = new Unidade();
                $unidade->setId($_GET['turno_na_unidade']);
                $unidadeDao->preenchePorId($unidade);
                
                $turnoDao = new TurnoDAO($unidadeDao->getConexao());
                
                $listaDeTurnos = $turnoDao->retornaLista();
                $this->view->formTurnoNaUnidade($unidade, $listaDeTurnos);
                if (isset($_POST['turno_na_unidade'])) {
                    $turno = new Turno();
                    $unidade = new Unidade();
                    $turno->setId($_POST['id_turno']);
                    $unidade->setId($_POST['id_unidade']);
                    if ($unidadeDao->turnoNaUnidade($turno, $unidade))
                        $this->view->mostraSucesso("Turno Adicionado com Sucesso");
                    else
                        $this->view->mostraSucesso("Erro ao tentar adicionar turno na Unidade");
                    echo '<meta http-equiv="refresh" content="4; url=.\?pagina=definicoes">';
                }
            } 
            else if (isset($_GET['excluir_turno_da_unidade'])) {
                $unidade = new Unidade();
                $unidade->setId($_GET['excluir_turno_da_unidade']);
                $unidadeDao->preenchePorId($unidade);
                $unidadeDao->turnosDaUnidade($unidade);
                
                $turnoDao = new TurnoDAO($unidadeDao->getConexao());
                
                $listaDeTurnos = $turnoDao->retornaLista();
                $this->view->formExcluirTurnoDaUnidade($unidade);
                if (isset($_POST['excluir_turno_da_unidade'])) {
                    $turno = new Turno();
                    $unidade = new Unidade();
                    $turno->setId($_POST['id_turno']);
                    $unidade->setId($_POST['id_unidade']);
                    if ($unidadeDao->excluirTurnoDaUnidade($turno, $unidade))
                        $this->view->mostraSucesso("Turno excluído com Sucesso");
                    else
                        $this->view->mostraSucesso("Erro ao tentar excluir turno na Unidade");
                    echo '<meta http-equiv="refresh" content="4; url=.\?pagina=definicoes">';
                }
            }
        }
        
        // $this->view->formAdicionarTurnoNaUnidade();
    }

    public function telaCatracas()
    {
        $dao = new DAO();
        $this->view->formAdicionarCatracaVirtual();
        
        if (isset($_GET['nome_catraca'])) {
            if (strlen($_GET['nome_catraca']) < 3) {
                echo '<div class="borda">';
                $this->view->formMensagem("-ajuda", "Insira um nome maior");
                echo '</div>';
                echo '<meta http-equiv="refresh" content="4; url=.\?pagina=definicoes&lista_catracas=1">';
                return;
            }
            $nomeCatraca = $_GET['nome_catraca'];
            $sql1 = "SELECT * FROM catraca WHERE catr_nome = '$nomeCatraca'";
            $result = $dao->getConexao()->query($sql1);
            $i = 0;
            
            foreach ($result as $linha) {
                $i ++;
            }
            
            if ($i > 0) {
                $this->view->formMensagem("-erro", "Esta catraca já existe!");
                echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
            } else {
                if (isset($_POST['confirmar'])) {
                    $sql = "INSERT INTO catraca (catr_nome) VALUES ('$nomeCatraca')";
                    if ($dao->getConexao()->exec($sql)) {
                        echo '<div class="borda">';
                        $this->view->formMensagem("-sucesso", "Catraca adicionada com Sucesso!");
                        echo '</div>';
                    } else {
                        echo '<div class="borda">';
                        $this->view->formMensagem("-erro", "Não foi possível adicionar a catraca!");
                        echo '</div>';
                    }
                    echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
                    return;
                }
                
                echo '<div class="borda">';
                $this->view->formMensagem("-ajuda", "Tem certeza que deseja adicionar esta catraca? " . $_GET['nome_catraca']);
                echo '<form action="" method="post">
						<input type="hidden" value="' . $_GET['nome_catraca'] . '" name="nome_catraca">
						<input type="submit" class="botao" value="Confirmar" name="confirmar" />
						</form>';
                echo '</div>';
            }
        }
        
        $unidadeDao = new UnidadeDAO($this->dao->getConexao());
        $listaDeCatracas = $unidadeDao->retornaCatracasPorUnidade();
        $this->view->listarCatracas($listaDeCatracas);
        
        if (isset($_GET['editar_catraca'])) {
            $id = intval($_GET['editar_catraca']);
            if (! $id) {
                $this->view->mostraSucesso("Erro ao tentar editar catraca");
                echo '<meta http-equiv="refresh" content="4; url=.\?pagina=definicoes&lista_catracas=1">';
                return;
            }
            $catraca = new Catraca();
            $catraca->setId($_GET['editar_catraca']);
            $listaDeUnidades = $unidadeDao->retornaLista();
            
            $unidadeDao->preencheCatracaPorId($catraca);
            
            $this->view->formEditarCatraca($catraca, $listaDeUnidades);
            
            if (isset($_POST['salvar'])) {
                $catraca->setId($_POST['id_catraca']);
                $catraca->setNome($_POST['nome_catraca']);
                $catraca->setOperacao($_POST['operacao']);
                $catraca->setTempoDeGiro($_POST['tempo_giro']);
                $catraca->setInterfaceRede($_POST['interface']);
                $catraca->setUnidade(new Unidade());
                $catraca->getUnidade()->setId($_POST['id_unidade']);
                $catraca->setFinanceiro($_POST['financeiro']);
                if ($unidadeDao->atualizarCatraca($catraca))
                    $this->view->mostraSucesso("Catraca editada com sucesso");
                else
                    $this->view->mostraSucesso("Erro ao tentar editar catraca");
                echo '<meta http-equiv="refresh" content="4; url=.\?pagina=definicoes&lista_catracas=1">';
            }
        }
    }

    public function telaConfiguracaoDeTurnos()
    {
        $this->view->formAdicionarTurno();
        $turnoDao = new TurnoDAO($this->dao->getConexao());
        
        if (isset($_GET['cadastrar_turno'])) {
            if (strlen($_GET['turno_nome']) < 3) {
                $this->view->mostraSucesso("Escreva Um Nome Maior");
                return;
            }
            if (strlen($_GET['hora_inicio']) < 3) {
                $this->view->mostraSucesso("Digite a Hora de Início");
                return;
            }
            if (strlen($_GET['hora_fim']) < 3) {
                $this->view->mostraSucesso("Digite a Hora de fim");
                return;
            }
            
            echo '	<div class="borda">';
            $this->view->formMensagem("-ajuda", "Você tem certeza que quer adicionar esse turno? " . $_GET['turno_nome']);
            echo '	<form action="" method="post" class="formulario sequencial texto-preto">
						<input type="hidden" name="hora_inicio" value="' . $_GET['hora_inicio'] . '" />
						<input type="hidden" name="hora_fim" value="' . $_GET['hora_fim'] . '" />
						<input type="hidden" name="turno_nome" value="' . $_GET['turno_nome'] . '" />
						<input  type="submit"  name="certeza_cadastrar_turno" value="Confirmar"/>
					</form>
					</div>';
        }
        
        if (isset($_POST['certeza_cadastrar_turno'])) {
            if (strlen($_GET['turno_nome']) < 3) {
                $this->view->mostraSucesso("Escreva Um Nome Maior");
                return;
            }
            $turnoNome = $_POST['turno_nome'];
            
            $horaInicio = $_POST['hora_inicio'];
            $horaFim = $_POST['hora_fim'];
            
            $stmt = $this->dao->getConexao()->prepare("INSERT INTO turno(turn_hora_inicio,turn_hora_fim,turn_descricao) VALUES(?, ?, ?);");
            $stmt->bindParam(1, $horaInicio);
            $stmt->bindParam(2, $horaFim);
            $stmt->bindParam(3, $turnoNome);
            
            if ($stmt->execute()) {
                $this->view->mostraSucesso("Sucesso");
            } else {
                $this->view->mostraSucesso("Erro ao tentar inserir unidade");
            }
            echo '<meta http-equiv="refresh" content="4; url=.\?pagina=definicoes">';
            return;
        } else {
            $turnos = $turnoDao->retornaLista();
            $this->view->listarTurnos($turnos);
        }
        
        if (isset($_GET['editar'])) {
            if (isset($_GET['id_turno'])) {
                $dao = new TurnoDAO();
                $turno = new Turno();
                $turno->setId($_GET['id_turno']);
                
                $dao->retornaTurnoPorId($turno);
                $this->view->formEditarTurno($turno);
                
                if (isset($_POST['confirmar'])) {
                    
                    $turno->setHoraInicial($_POST['hora_inicio']);
                    $turno->setHoraFinal($_POST['hora_fim']);
                    
                    if ($dao->atualizaTurno($turno)) {
                        $this->view->formMensagem("-sucesso", "Turno alterado com sucesso!");
                        echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
                    } else {
                        $this->view->formMensagem("-erro", "Erro ao editar o turno.");
                        echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
                    }
                    return;
                }
                return;
            }
        }
        
        // $this->view->formAdicionarTurnoNaUnidade();
    }

    public function telaTiposDeUsuarios()
    {
        $this->view->formAdicionarTipoDeUsuario();
        $tipoDao = new TipoDAO($this->dao->getConexao());
        
        if (isset($_GET['cadastrar_tipo'])) {
            $tipo = $_GET['tipo_nome'];
            echo '	<div class="borda">';
            $this->view->formMensagem("-ajuda", "Você tem certeza que quer adicionar esse tipo de usuário? $tipo");
            echo '	<form action="" method="post" class="formulario sequencial texto-preto">
						<input type="hidden" name="tipo_nome" value="' . $_GET['tipo_nome'] . '" />
						<input type="hidden" name="tipo_valor" value="' . $_GET['tipo_valor'] . '" />
						<input  type="submit"  name="certeza_cadastrar_tipo" value="Confirmar"/>
					</form>';
            echo '	</div>';
        }
        if (isset($_POST['certeza_cadastrar_tipo'])) {
            $tipoNome = $_POST['tipo_nome'];
            $tipoValor = floatval($_POST['tipo_valor']);
            $stmt = $this->dao->getConexao()->prepare("INSERT INTO tipo(tipo_nome, tipo_valor) VALUES(?, ?);");
            $stmt->bindParam(1, $tipoNome);
            $stmt->bindParam(2, $tipoValor);
            
            if ($stmt->execute()) {
                $this->view->mostraSucesso("Sucesso");
            } else {
                $this->view->mostraSucesso("Erro ao tentar inserir unidade");
            }
            echo '<meta http-equiv="refresh" content="4; url=.\?pagina=definicoes">';
            return;
        } else {
            $tipos = $tipoDao->retornaLista();
            $this->view->listarTiposDeUsuarios($tipos);
        }
        
        if (isset($_GET['editar_tipo'])) {
            $tipo = new Tipo();
            $tipo->setId($_GET['editar_tipo']);
            $idTipo = $tipo->getId();
            $tipoDao->retornaTipoPorId($tipo);
            $this->view->formEditarTipo($tipo);
            
            if (isset($_POST['alterar'])) {
                
                $novoValor = $_POST['valor_tipo'];
                $sql = "UPDATE tipo SET tipo_valor=$novoValor WHERE tipo_id= $idTipo";
                
                if ($this->dao->getConexao()->exec($sql)) {
                    $this->view->formMensagem("-sucesso", "Valor do " . $tipo->getNome() . " foi alterado com sucesso!");
                } else {
                    $this->view->formMensagem("-erro", "Não foi possivel altera o valor!");
                }
                echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
                return;
            }
        }
    }

    public function telaDeCustos()
    {
        $custoAtualRefeicao = 0;
        $result = $this->dao->getConexao()->query("SELECT cure_valor FROM custo_refeicao ORDER BY cure_id DESC LIMIT 1 ;");
        foreach ($result as $linha) {
            $custoAtualRefeicao = $linha['cure_valor'];
            break;
        }
        
        $custoCartao = 0;
        $result = $this->dao->getConexao()->query("SELECT cuca_valor  FROM custo_cartao ORDER BY cuca_id DESC LIMIT 1 ;");
        foreach ($result as $linha) {
            $custoCartao = $linha['cuca_valor'];
            break;
        }
        
        $unidadeDao = new UnidadeDAO();
        $listaUnidade = $unidadeDao->retornaLista();
        $listaCusto = $this->dao->getConexao()->query("SELECT * FROM custo_refeicao");
        $listaCustoUnidade = $unidadeDao->custosDaUnidade();
        
        $this->view->formAlterarCustoRefeicao($custoAtualRefeicao);
        
        $this->view->formAlterarCustoCartao($custoCartao);
        
        $this->view->formCustoUnidade($listaUnidade, $listaCusto);
        
        $this->view->listaCustoUnidade($listaCustoUnidade);
        
        if (isset($_GET['custo_refeicao'])) {
            if (isset($_POST['confirmar'])) {
                $dataTimeAtual = date("Y-m-d G:i:s");
                $valor = floatval($_GET['custo_refeicao']);
                $dao = new DAO();
                $i = 0;
                $sql = "SELECT * FROM custo_refeicao  WHERE cure_valor = $valor";
                $result = $this->dao->getConexao()->query($sql);
                foreach ($result as $linha) {
                    $i ++;
                }
                if ($i == 0) {
                    if ($this->dao->getConexao()->exec("INSERT into custo_refeicao(cure_valor, cure_data) VALUES($valor, '$dataTimeAtual')")) {
                        echo '<meta http-equiv="refresh" content="0; url=.\?pagina=definicoes&info_custo=sucesso">';
                    } else {
                        echo '<meta http-equiv="refresh" content="0; url=.\?pagina=definicoes&info_custo=erro">';
                    }
                } else {
                    echo '<meta http-equiv="refresh" content="0; url=.\?pagina=definicoes&info_custo=erro">';
                }
            }
        }
        
        if (isset($_GET['custo_cartao'])) {
            if (isset($_POST['confirmar'])) {
                $dataTimeAtual = date("Y-m-d G:i:s");
                $valor = floatval($_GET['custo_cartao']);
                
                if ($this->dao->getConexao()->exec("INSERT into custo_cartao(cuca_valor , cuca_data ) VALUES($valor, '$dataTimeAtual')"))
                    echo '<meta http-equiv="refresh" content="0; url=.\?pagina=definicoes&info_cartao=sucesso">';
                else
                    echo '<meta http-equiv="refresh" content="0; url=.\?pagina=definicoes&info_cartao=erro">';
                
                echo '<meta http-equiv="refresh" content="0; url=.\?pagina=definicoes&info_cartao=erro">';
            }
        }
        
        /*
         * Inclui e altera o custo específico da unidade.
         */
        if (isset($_GET['unidade'])) {
            
            if (isset($_GET['valor_custo'])) {
                
                if (isset($_POST['confirmar'])) {
                    $idUnidade = $_GET['unidade'];
                    $idValorCusto = $_GET['valor_custo'];
                    $consulta = "SELECT * FROM custo_unidade WHERE unid_id = $idUnidade";
                    $result = $this->dao->getConexao()->query($consulta);
                    $i = 0;
                    
                    foreach ($result as $linha) {
                        $i ++;
                    }
                    
                    if ($i == 0) {
                        $sql = "INSERT INTO custo_unidade (unid_id, cure_id) VALUES ($idUnidade, $idValorCusto)";
                        if ($this->dao->getConexao()->exec($sql)) {
                            echo '<meta http-equiv="refresh" content="0; url=.\?pagina=definicoes&info_unidade=sucesso">';
                        } else {
                            echo '<meta http-equiv="refresh" content="0; url=.\?pagina=definicoes&info_unidade=erro">';
                        }
                        return;
                    } else {
                        $sql = "UPDATE custo_unidade SET cure_id = $idValorCusto WHERE unid_id = $idUnidade";
                        if ($this->dao->getConexao()->exec($sql)) {
                            echo '<meta http-equiv="refresh" content="0; url=.\?pagina=definicoes&info_unidade=alterado">';
                        } else {
                            echo '<meta http-equiv="refresh" content="0; url=.\?pagina=definicoes&info_unidade=erro">';
                        }
                        return;
                    }
                }
            }
        }
        
        if (isset($_GET['excluir'])) {
            if (isset($_POST['confirmar'])) {
                $idValorCusto = $_GET['custo_unidade_id'];
                $sql = "DELETE FROM custo_unidade WHERE unid_id = $idValorCusto";
                if ($this->dao->getConexao()->exec($sql)) {
                    $this->view->formMensagem("-sucesso", "Custo removido com sucesso!");
                    echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
                } else {
                    $this->view->formMensagem("-erro", "Erro ao Excluir o custo da unidade!");
                    echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
                }
                return;
            }
        }
    }

    public function telaAdicionarAcatracaVirtual()
    {
        $dao = new DAO();
        $this->view->formAdicionarCatracaVirtual();
        
        if (isset($_POST['nome_catraca'])) {
            
            $nomeCatraca = $_POST['nome_catraca'];
            $sql1 = "SELECT * FROM catraca WHERE catr_nome = '$nomeCatraca'";
            $result = $dao->getConexao()->query($sql1);
            $i = 0;
            
            foreach ($result as $linha) {
                $i ++;
            }
            
            if ($i > 0) {
                $this->view->formMensagem("-erro", "Esta catraca já existe!");
                echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
            } else {
                if (isset($_POST['confirmar'])) {
                    $sql = "INSERT INTO catraca (catr_nome) VALUES ('$nomeCatraca')";
                    if ($dao->getConexao()->exec($sql)) {
                        echo '<div class="borda">';
                        $this->view->formMensagem("-sucesso", "Catraca adicionada com Sucesso!");
                        echo '</div>';
                    } else {
                        echo '<div class="borda">';
                        $this->view->formMensagem("-erro", "Não foi possível adicionar a catraca!");
                        echo '</div>';
                    }
                    echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
                    return;
                }
                
                echo '<div class="borda">';
                $this->view->formMensagem("-ajuda", "Tem certeza que deseja adicionar esta catraca?");
                echo '<form action="" method="post">
						<input type="hidden" value="' . $_POST['nome_catraca'] . '" name="nome_catraca">
						<input type="submit" class="botao" value="Confirmar" name="confirmar" />
						</form>';
                echo '</div>';
            }
        }
        return;
    }

    public function telaMensagem()
    {
        $unidade = new Unidade();
        $unidadeDao = new UnidadeDAO();
        $unidades = $unidadeDao->retornaLista();
        
        echo '	<h2 class="titulo">Incluir e Editar Mensagens da Catraca</h2>
				<div class="borda doze colunas">
					<form action="" class="formulario-organizado" method="">
						<input type="hidden" name="pagina" value="definicoes">
						<label for="">
							<object class="rotulo">Unidade Acadêmica: </object>
							<select name="unidade" id="unidade">';
        echo '				<option>Selecione uma Unidade</option>';
        foreach ($unidades as $unidade) {
            echo '			<option value="' . $unidade->getId() . '">' . $unidade->getNome() . '</option>';
        }
        echo '
							</select>
						</label>';
        
        echo '	<label for="">
					<object class="rotulo">Catraca: </object>
					<select id="catraca" name="catraca">
						<option>Selecione uma Catraca</option>
					</select>
				</label>
				<input type="submit" value="Buscar">
			</form>
			<div id="mensagens">';
        
        if (isset($_GET['unidade'])) {
            
            if (intval($_GET['catraca']) && intval($_GET['unidade'])) {
                
                $idCatraca = $_GET['catraca'];
                $idUnidade = $_GET['unidade'];
                $dao = new DAO();
                $view = new DefinicoesView();
                
                $sql = "SELECT * FROM catraca
					INNER JOIN catraca_unidade ON catraca_unidade.catr_id = catraca.catr_id
					INNER JOIN unidade ON unidade.unid_id = catraca_unidade.unid_id
					WHERE unidade.unid_id = $idUnidade AND catraca.catr_id = $idCatraca";
                $result = $dao->getConexao()->query($sql);
                foreach ($result as $linha) {
                    $nomeCatraca = $linha['catr_nome'];
                    $nomeUnidade = $linha['unid_nome'];
                }
                
                $sql = "SELECT * FROM mensagem
					INNER JOIN catraca_unidade ON catraca_unidade.catr_id = mensagem.catr_id
					INNER JOIN unidade ON unidade.unid_id = catraca_unidade.unid_id
					INNER JOIN catraca ON catraca_unidade.catr_id = catraca.catr_id
					WHERE mensagem.catr_id = $idCatraca";
                $result = $dao->getConexao()->query($sql);
                foreach ($result as $linha) {
                    $msg1 = $linha['mens_institucional1'];
                    $msg2 = $linha['mens_institucional2'];
                    $msg3 = $linha['mens_institucional3'];
                    $msg4 = $linha['mens_institucional4'];
                    $nomeUnidade = $linha['unid_nome'];
                    $nomeCatraca = $linha['catr_nome'];
                }
                $args = array(
                    $nomeUnidade,
                    $nomeCatraca,
                    @$msg1,
                    @$msg2,
                    @$msg3,
                    @$msg4
                );
                $view->formEditarMensagensCatraca($args);
            }
        }
        echo '</div>';
        
        if (isset($_GET['salvar'])) {
            if (isset($_POST['confirmar'])) {
                $msg1 = $_GET['msg_um'];
                $msg2 = $_GET['msg_dois'];
                $msg3 = $_GET['msg_tres'];
                $msg4 = $_GET['msg_quatro'];
                $id_catraca = $_GET['id_catraca'];
                $i = 0;
                $result = $this->dao->getConexao()->query("SELECT * FROM mensagem WHERE catr_id = $id_catraca");
                foreach ($result as $linha) {
                    $i ++;
                }
                if ($i == 0) {
                    $sql = "INSERT INTO mensagem (
						mens_institucional1, mens_institucional2, mens_institucional3, mens_institucional4, catr_id)
						VALUES ('$msg1','$msg2','$msg3','$msg4',$id_catraca)";
                    if ($this->dao->getConexao()->exec($sql)) {
                        $this->view->formMensagem("-sucesso", "Mensagem inserida com sucesso!");
                        echo '</div>';
                        echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
                        return;
                    } else {
                        $this->view->formMensagem("-erro", "Erro ao inserir mensagem!");
                        echo '</div>';
                        echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
                        return;
                    }
                } else {
                    $sql = "UPDATE mensagem SET
						mens_institucional1 = '$msg1',
						mens_institucional2 = '$msg2',
						mens_institucional3 = '$msg3',
						mens_institucional4 = '$msg4'
						WHERE catr_id = $id_catraca";
                    if ($this->dao->getConexao()->exec($sql)) {
                        $this->view->formMensagem("-sucesso", "Mensagem atualizada com sucesso!");
                        echo '</div>';
                        echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
                        return;
                    } else {
                        $this->view->formMensagem("-erro", "Erro ao atualizar mensagem!");
                        echo '</div>';
                        echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
                        return;
                    }
                }
            }
            $this->view->formMensagem("-ajuda", "Deseja incluir estas mensagens?");
            echo '	<form class="formulario-organizado" method="post">
						<input type="submit" name="confirmar" value="Confirmar">
					</form>';
        }
        
        $sql = "SELECT * FROM unidade
				INNER JOIN catraca_unidade ON catraca_unidade.unid_id = unidade.unid_id
				INNER JOIN catraca ON catraca.catr_id = catraca_unidade.catr_id
				INNER JOIN mensagem ON catraca.catr_id = mensagem.catr_id";
        $listaMensagemCatraca = $this->dao->getConexao()->query($sql);
        $this->view->listarMensagensCatraca($listaMensagemCatraca);
        
        if (isset($_GET['excluir'])) {
            echo '<hr class="um">';
            $this->view->formMensagem("-ajuda", "Deseja realmente excluir estas mensagens?");
            echo '	<form class="formulario" method="post">
						<input type="submit" name="confirma" value="Confirmar">
					</form>';
            if (isset($_POST['confirma'])) {
                $id_catraca = $_GET['id-catraca'];
                $sql = "DELETE FROM mensagem WHERE catr_id = $id_catraca";
                if ($this->dao->getConexao()->exec($sql)) {
                    $this->view->formMensagem("-sucesso", "Mensagem excluída com sucesso!");
                    echo '</div>';
                    echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
                    return;
                } else {
                    $this->view->formMensagem("-erro", "Erro ao exluir a mensagem!");
                    echo '</div>';
                    echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes">';
                    return;
                }
            }
        }
        echo '		</div>';
    }

    public function telaDefinicoes()
    {
        $this->dao = new DAO();
        $this->view = new DefinicoesView();
        
        $selecaoUnidade = "active";
        $selecaoTurnos = "";
        $selecaoTipos = "";
        $selecaoCustos = "";
        $selecaoCatracas = "";
        if (isset($_GET['cadastrar_unidade']) || isset($_POST['certeza_cadastrar_unidade'])) {
            $selecaoUnidade = "active";
            $selecaoTurnos = "";
            $selecaoTipos = "";
            $selecaoCatracas = "";
            $selecaoCustos = "";
        } else if (isset($_POST['certeza_cadastrar_turno']) || isset($_GET['cadastrar_turno'])) {
            $selecaoUnidade = "";
            $selecaoTurnos = "active";
            $selecaoTipos = "";
            $selecaoCatracas = "";
            $selecaoCustos = "";
        } else if (isset($_GET['cadastrar_tipo']) || isset($_POST['certeza_cadastrar_tipo'])) {
            $selecaoUnidade = "";
            $selecaoTurnos = "";
            $selecaoCatracas = "";
            $selecaoTipos = "active";
            $selecaoCustos = "";
        } else if (isset($_GET['custo_cartao']) || isset($_GET['custo_refeicao'])) {
            $selecaoUnidade = "";
            $selecaoTurnos = "";
            $selecaoCatracas = "";
            $selecaoTipos = "";
            $selecaoCustos = "active";
        } else if (isset($_GET['editar_catraca']) || isset($_GET['lista_catracas'])) {
            $selecaoUnidade = "";
            $selecaoTurnos = "";
            $selecaoCatracas = "active";
            $selecaoTipos = "";
            $selecaoCustos = "";
        }
        
        echo '<div class="navegacao">
				<div class = "simpleTabs">
			        <ul class = "simpleTabsNavigation">
			            <li><a href="#">Unidades Acadêmicas</a></li>
						<li><a href="#">Turnos</a></li>
						<li><a href="#">Catracas</a></li>
						<li><a href="#">Tipos de Usuários</a></li>
						<li><a href="#">Mensagens Catraca</a></li>
						<li><a href="#">Custos</a></li>
			        </ul>
	
					<div class = "simpleTabsContent"> ';
        $this->telaConfiguracaoUnidadeAcademica();
        
        echo '  	</div>
		        	<div class = "simpleTabsContent">';
        
        $this->telaConfiguracaoDeTurnos();
        
        echo '  	</div>
		        	<div class = "simpleTabsContent">';
        
        $this->telaCatracas();
        
        echo '  	</div>
		        	<div class = "simpleTabsContent">';
        
        $this->telaTiposDeUsuarios();
        
        echo '  	</div>
		  			<div class = "simpleTabsContent">';
        
        $this->telaMensagem();
        
        echo '  	</div>
		  			<div class = "simpleTabsContent">';
        
        $this->telaDeCustos();
        
        echo '		</div>
		  		</div>
			  </div>';
    }
}

?>