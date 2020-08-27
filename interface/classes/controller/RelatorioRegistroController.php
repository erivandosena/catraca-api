<?php
class RelatorioRegistroController{
	private $view;
	private $dao;
	public static function main($nivelDeAcesso, $idUsuario) {
		
		switch ($nivelDeAcesso) {
			case Sessao::NIVEL_SUPER :
				$controller = new RelatorioRegistroController($idUsuario);
				$controller->relatorio ();
				break;
			case Sessao::NIVEL_ADMIN :
				$controller = new RelatorioRegistroController($idUsuario);
				$controller->relatorio ();
				break;
			default :
				UsuarioController::main ( $nivelDeAcesso );
				break;
		}
	}
	private $usuario; 
	public function RelatorioRegistroController($idUsuario){
		$this->usuario = new Usuario();
		$this->usuario->setId($idUsuario);
		
		$this->view = new RelatorioRegistroView();
		$this->dao = new UnidadeDAO ();
		$usuarioDao = new UsuarioDAO($this->dao->getConexao());
		$usuarioDao->preenchePorId($this->usuario);
		
		
	}
	public function relatorio() {
		
		
		if(isset($_GET['id_registro']) && isset($_GET['anular'])){
			if(isset($_POST['certeza'])){
				
				echo '<div class="doze colunas borda relatorio">';
				if($this->anularRegistro($_GET['id_registro'])){
					$this->view->formMensagem("-sucesso","Eliminado com sucesso!");
					
				}else{
					$this->view->formMensagem("-sucesso","Erro!");
				}
				
				
				echo '<meta http-equiv="refresh" content="4; url=.\?pagina=relatorio_registro">';
				
				
				echo '</div>';
				
				return;
			}
			
			
			if($_GET['anular'] == 1){
				
				echo '<div class="borda doze colunas">';
				echo '<p>'.$this->usuario->getNome().', tem certeza que deseja anular este registro? </p>';
				echo '<form action="" method="post">
						<input type="submit" class="botao" value="Tenho Certeza!" name="certeza"/>
				</form>';
				echo '</div>';
				
			}
			

		}
		
		if(isset($_GET['id_registro'])){
		
			$idRegistro = intval($_GET['id_registro']);
			echo '<div class="doze colunas borda relatorio">';
			
			$sql = "SELECT * FROM registro
					INNER JOIN vinculo ON vinculo.vinc_id = registro.vinc_id
					INNER JOIN cartao ON cartao.cart_id = vinculo.cart_id
					INNER JOIN usuario ON vinculo.usua_id = usuario.usua_id
					INNER JOIN catraca_unidade ON registro.catr_id = catraca_unidade.catr_id
					WHERE regi_id = $idRegistro";
			
			
			$result = $this->dao->getConexao()->query($sql);
			
			foreach($result as $linha){
			
			
				echo '<table  class="tabela borda-vertical zebrada">
					<tr><th>ID Registro:</th><td> '.$idRegistro.'</td></tr>
					<tr><th>Usuário:</th><td>'.$linha['usua_nome'].'</td></tr>
					<tr><th>Data e Hora </th><td>'.$linha['regi_data'].'</td></tr>
					<tr><th>Cartão:</th><td>'.$linha['cart_id'].'</td></tr>
					<tr><th>Valor Pago:</th><td>'.$linha['regi_valor_pago'].'</td></tr>
                    
					<form>
                        <label for="motivo">Digite o motivo da anulação: </label><br>
                        <input type="text" id="motivo" name="motivo" />
                        <input type="hidden" name="pagina" value="relatorio_registro"/>
                        <input type="hidden" name="anular" value="1" />
                        <input type="hidden" name="id_registro" value="'.$idRegistro.'" /><br/>
                        <input type="submit" class="botao" value="Anular Registro" />
                    </form>
				
				
			';
				
			}
			
			
			
			echo '</div>';
		
			return;
		}
		
		
		$listaDeUnidades = $this->dao->retornaLista ();		
		$this->view->mostrarFormulario($listaDeUnidades);
				
		
		if (isset ( $_GET ['gerar'] )) {
			$this->gerarDados( $_GET ['unidade'], $_GET ['data_inicial'], $_GET ['data_final']);
			
		}
	}
	
	private $strUnidade;
	private $data1;
	private $data2;
	
	
	
	/**
	 * 
	 * Iniciar transaction
	 * Seleciona registro
	 * Atualizar tabela dos créditos. 
	 * Inserir transacao referente ao erro deste registro. 
	 * Inserir transacao referente à esta correção. 
	 * Eliminar registro por ID. 
	 * Commit
	 * 
	 * @param int $idRegistro
	 */
	public function anularRegistro($idRegistro){
		$idRegistro = intval($idRegistro);
		$this->dao->getConexao()->beginTransaction();
		
		$sqlRegistro = "SELECT * FROM registro 
		INNER JOIN vinculo ON vinculo.vinc_id = registro.vinc_id
		INNER JOIN cartao ON cartao.cart_id = vinculo.cart_id
		WHERE regi_id = $idRegistro ORDER BY regi_data LIMIT 1";
		$result = $this->dao->getConexao()->query($sqlRegistro);
		foreach($result as $linha){
			
			$valorPago = $linha['regi_valor_pago'];
			
			$credito = $linha['cart_creditos']+$valorPago;
			
			$idCartao = $linha['cart_id'];
			
			$idUsuario = $linha['usua_id'];
			
			$idOperador = $this->usuario->getId();
			
			$dataRegistro = $linha['regi_data'];
			$dataAtual = date ( "Y-m-d G:i:s" );
			
			$sqlUpdate = "UPDATE cartao
			SET cart_creditos = $credito
			WHERE cart_id = $idCartao";
			
			if(!$this->dao->getConexao()->exec($sqlUpdate)){
				$this->dao->getConexao()->rollBack();
				return false;
			}
			
			$negativo = 0-$valorPago;
			
			$descricao = "Refeição extra cadastrada em ".date("d/m/Y", strtotime($dataRegistro));
			$descricao2 = "Estorno refeição extra cadastrada em ".date("d/m/Y", strtotime($dataRegistro));			
			
			$sqlTransacao = "INSERT INTO transacao
					(tran_valor, tran_descricao, tran_data, usua_id, usua_id1)
					VALUES
					($negativo, '$descricao ', '$dataRegistro', $idOperador, $idUsuario);";
			
			if(!$this->dao->getConexao()->exec($sqlTransacao)){
				
				$this->dao->getConexao()->rollBack();
				return false;
			}
			
			
			$sqlTransacao2 = "INSERT INTO transacao
				(tran_valor, tran_descricao, tran_data, usua_id, usua_id1)
				VALUES
				($valorPago, '$descricao2', '$dataAtual', $idOperador, $idUsuario);";
				
			
			if(!$this->dao->getConexao()->exec($sqlTransacao2)){
			
				$this->dao->getConexao()->rollBack();
				return false;
			}
				
			$sqlDeletar = "DELETE FROM registro Where regi_id = $idRegistro";
			if(!$this->dao->getConexao()->exec($sqlDeletar)){
				$this->dao->getConexao()->rollBack();
				return false;
			}
				
			$this->dao->getConexao()->commit();
			return true;
			
		}
		return false;
		
		
		
		
	}
	
	
	public function gerarDados($idUnidade = NULL, $data1 = null, $data2 = null){
		if($idUnidade == NULL){
			$idUnidade = 1;
		}
		if ($data1 == null){
			$data1 = date ( 'Y-m-d' );
		}
		if ($data2 == null){
			$data2 = date ( 'Y-m-d' );
		}
		$this->data1 = $data1;
		$this->data2 = $data2;
		
		$listaDeDatas = $this->intervaloDeDatas($data1, $data2);
		$turnoDao = new TurnoDAO ( $this->dao->getConexao () );
		$listaDeTurnos = $turnoDao->retornaLista ();
		
		
		
		$strFiltroUnidade = "";
		$this->strUnidade =  'Todos os restaurantes ';
		if($idUnidade != NULL){
				
			$idUnidade = intval($idUnidade);
			$strFiltroUnidade  = " AND catraca_unidade.unid_id = $idUnidade";
			
			$unidade = new Unidade();
			$unidade->setId($idUnidade);
			$this->dao->preenchePorId($unidade);
			$this->strUnidade =  $unidade->getNome();
		}
		
        $matriz = array();
		
		foreach($listaDeTurnos as $turno){
			$matriz[$turno->getDescricao()] = array();
			foreach($listaDeDatas as $data){
				
				$dataInicial = $data . ' '.$turno->getHoraInicial();
				$dataFinal = $data . ' '.$turno->getHoraFinal();
				$result = $this->dao->getConexao()->query("SELECT * FROM registro
				INNER JOIN vinculo ON vinculo.vinc_id = registro.vinc_id
				INNER JOIN cartao ON cartao.cart_id = vinculo.cart_id
				INNER JOIN usuario ON vinculo.usua_id = usuario.usua_id
				INNER JOIN catraca_unidade ON registro.catr_id = catraca_unidade.catr_id
				WHERE (regi_data BETWEEN '$dataInicial' AND '$dataFinal') $strFiltroUnidade ORDER BY regi_data");
				
				
				echo '<div class=" doze colunas borda relatorio">';
				echo '<h2>UNILAB<small class="fim">Universidade da Integração Internacional da Lusofonia Afro-Brasileira</small></h2>
				
				<hr class="um">
				<h3>'.$this->strUnidade.'</h3>
				<span>De '. date ( 'd/m/Y', strtotime ( $this->data1 ) ) . ' a ' . date ( 'd/m/Y', strtotime ( $this->data2) ) .'</span>
				<span>'.$turno->getDescricao().'</span>
				<hr class="dois">
		
					<table class="tabela">
						<thead>
							<tr>
								<th>ID</th>
								<th>Data/Hora</th>
								<th>Nome</th>
								<th>Cartão</th>
								<th>Valor Pago</th>
								<th>Selecionar</th>
							</tr>
						<thead>
						<tbody>';
				foreach($result as $linha){
						
					echo '<tr><td>'.$linha['regi_id'].'</td><td>'.$linha['regi_data'].'</td><td>'.$linha['usua_nome'].'</td><td>'.$linha['cart_numero'].'</td><td>'.$linha['regi_valor_pago'].'</td><td><a href="?pagina=relatorio_registro&id_registro='.$linha['regi_id'].'" class="botao">Selecionar</a></td></tr>';
				}
				echo '</tbody>
					</table>';
					
				echo'<div class="doze colunas relatorio-rodape">
			<span>CATRACA | Copyright © 2015 - DTI</span>
			<span>Relatório Emitido em: '. date ( 'd/m/Y H:i:s' ).'</span>';
				echo '	</div>
				</div>';
				
				
			}	
			
		}		
		return $matriz;
		
	}
	public $contante = 0;
	public function retornaCurso($id){
		$sql2 = "SELECT * FROM vw_usuarios_catraca WHERE id_usuario = $id LIMIT 1";
		$result = $this->dao->getConexao()->query($sql2);
		$info = array();
		foreach ($result as $linha){
			$info['curso'] = "-";
			$info['turno'] = " ";
			if($linha['nome_curso'] != null){
				$info['curso'] = $linha['nome_curso'];
				$info['turno'] = $linha['turno'];
				
				if(!$linha['turno']){
					$info['turno'] = " Não Informado ";
				}
				
				
			}
			else{
				$info['curso'] = $linha['tipo_usuario'];
				$info['turno'] = " Não Informado ";
			}
			return $info;
		}
	}
	/**
	 * @param string $data1
	 * @param string $data2
	 * 
	 * @return array $listaDeDatas
	 */
	public function intervaloDeDatas($data1, $data2){
		$dateStart = new DateTime ( $data1 );
		$dateEnd = new DateTime ( $data2 );
		$listaDeDatas = array();
		while ( $dateStart <= $dateEnd ) {
			$listaDeDatas [] = $dateStart->format ( 'Y-m-d' );
			$dateStart = $dateStart->modify ( '+1day' );
		}
		return $listaDeDatas;
	
	}

		
		
}

?>