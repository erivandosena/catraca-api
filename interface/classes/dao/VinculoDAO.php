<?php
/**
 *
 * Cadastro de vinculo ser� feito por dois usuarios diferetnes.
 * Administra��o ou Guiche. Com suas particularidades.
 *
 * A seguir o cadastro de vinculo pelo usuario administrador.
 *
 * 1- Verifica��o de Id de usuario de base externa.
 * Esse usuario existe na base local? Captura-se o Id da base local para: $idUsuarioBaseLocal;
 * N�o existe na base local: Cadastra-se e captura-se o id da base local para: $idUsuarioBaseLocal;
 *
 *
 * 2- Verifica��o de cart�o.
 * O cart�o existe. Verifica se o Tipo cooresponde. Faz UPDATE no tipo. Retorne o seu ID. 
 * O cart�o n�o existe. Cadastre e Retorne o seu ID. 
 * 
 * 
 *
 * 3 - Verifica��o de vinculos do usuario.
 * - Não permitir cadastro de vinculo no usuario se ele tiver vinculo valido. 
 * Terá que cancelar o vinculo atual. Isto fará um update na data. 
 * 
 *
 * 4 - Verifica��o de vinculos do Cart�o.
 * - Não permitir cadastro de vinculo se o cartão tiver vinculo válido. 
 *
 *
 * 5 - Adicionar vinculo novo vinculo. 
 *
 *
 *
 */
class VinculoDAO extends DAO {
	
	public function retornaVinculosValidosDeUsuario(Usuario $usuario){
		$lista = array();
		$idUsuario = $usuario->getIdBaseExterna();
		$dataTimeAtual = date ( "Y-m-d G:i:s" );
		$sql =  "SELECT * FROM usuario INNER JOIN vinculo
				ON vinculo.usua_id = usuario.usua_id
				LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
				LEFT JOIN tipo ON cartao.tipo_id = tipo.tipo_id WHERE (usuario.id_base_externa = $idUsuario)
				AND ('$dataTimeAtual' BETWEEN vinc_inicio AND vinc_fim)";

		try{
			$stmt = $this->getConexao()->prepare($sql);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

			
		}catch (PDOException $e){
			echo '{"erro":{"text":'. $e->getMessage() .'}}';
		}
		foreach($result as $linha){
			$vinculo = new Vinculo();
			$vinculo->setResponsavel($usuario);

			$vinculo->setCartao(new Cartao());
			$vinculo->setId($linha['vinc_id']);
			$vinculo->getCartao()->setTipo(new Tipo());
			$vinculo->getCartao()->setId($linha['cart_id']);
			$vinculo->getCartao()->setCreditos($linha['cart_creditos']);
			$vinculo->getCartao()->getTipo()->setNome($linha ['tipo_nome']);
			$vinculo->getCartao()->setNumero($linha ['cart_numero']);
			$vinculo->setInicioValidade($linha ['vinc_inicio']);
			$vinculo->setFinalValidade($linha['vinc_fim']);
			$vinculo->setAvulso($linha['vinc_avulso']);
			$vinculo->setDescricao($linha['vinc_descricao']);
			
			$lista[] = $vinculo;
						

		}
		return $lista;
	}

	public function retornaVinculosVencidos(Usuario $usuario){
		$lista = array();
		$idUsuario = $usuario->getIdBaseExterna();
		$dataTimeAtual = date ( "Y-m-d G:i:s" );
		$sql =  "SELECT * FROM usuario INNER JOIN vinculo
		ON vinculo.usua_id = usuario.usua_id
		LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
		LEFT JOIN tipo ON cartao.tipo_id = tipo.tipo_id WHERE (usuario.id_base_externa = $idUsuario)
		AND ('$dataTimeAtual' > vinc_fim)";
		
		try{
			$stmt = $this->getConexao()->prepare($sql);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
				
		}catch (PDOException $e){
			echo '{"erro":{"text":'. $e->getMessage() .'}}';
		}
		
		
		foreach($result as $linha){
			$vinculo = new Vinculo();
			$vinculo->setResponsavel($usuario);
			$vinculo->setCartao(new Cartao());
			$vinculo->setId($linha['vinc_id']);
			$vinculo->getCartao()->setCreditos($linha['cart_creditos']);
			$vinculo->getCartao()->setTipo(new Tipo());
			$vinculo->getCartao()->setId($linha['cart_id']);
			$vinculo->getCartao()->getTipo()->setNome($linha ['tipo_nome']);
			$vinculo->getCartao()->setNumero($linha ['cart_numero']);
			$vinculo->setInicioValidade($linha ['vinc_inicio']);
			$vinculo->setFinalValidade($linha['vinc_fim']);
			$vinculo->setAvulso($linha['vinc_avulso']);
			$lista[] = $vinculo;
	
	
		}
		return $lista;
	}
	public function retornaVinculosFuturos(Usuario $usuario){
		$lista = array();
		$idUsuario = $usuario->getIdBaseExterna();
		$dataTimeAtual = date ( "Y-m-d G:i:s" );
		$sql =  "SELECT * FROM usuario INNER JOIN vinculo
		ON vinculo.usua_id = usuario.usua_id
		LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
		LEFT JOIN tipo ON cartao.tipo_id = tipo.tipo_id WHERE (usuario.id_base_externa = $idUsuario)
		AND ('$dataTimeAtual' < vinc_fim AND '$dataTimeAtual' < vinc_inicio)";
		$result = $this->getConexao ()->query ($sql );
	
		foreach($result as $linha){
			$vinculo = new Vinculo();
			$vinculo->setResponsavel($usuario);
			$vinculo->setCartao(new Cartao());
			$vinculo->setId($linha['vinc_id']);
			$vinculo->getCartao()->setTipo(new Tipo());
			$vinculo->getCartao()->setId($linha['cart_id']);
			$vinculo->getCartao()->getTipo()->setNome($linha ['tipo_nome']);
			$vinculo->getCartao()->setNumero($linha ['cart_numero']);
			$vinculo->setInicioValidade($linha ['vinc_inicio']);
			$vinculo->setFinalValidade($linha['vinc_fim']);
			$vinculo->setAvulso($linha['vinc_avulso']);
			$lista[] = $vinculo;
	
	
		}
		return $lista;
	}
	public function invalidarVinculo(Vinculo $vinculo){
		
		$dataTimeAtual = date ( "Y-m-d G:i:s" );
		
		if($vinculo->getId()){
			$idVinculo = $vinculo->getId();
			$idIsencao = $vinculo->getIsencao()->getId();
			$sql = "UPDATE isencao set isen_fim = '$dataTimeAtual' WHERE isen_id = $idIsencao";
			$this->getConexao()->exec($sql);
			
		}
		
		$sql = "UPDATE vinculo set vinc_fim = '$dataTimeAtual' WHERE vinc_id = $idVinculo";
// 		echo $sql;
		if($this->getConexao()->exec($sql))
			return true;
		return false;
		
	}
	public function invalidarIsencaoVinculo(Vinculo $vinculo){
		$dataTimeAtual = date ( "Y-m-d G:i:s" );
		$idIsencao = $vinculo->getIsencao()->getId();
		$sql = "UPDATE isencao set isen_fim = '$dataTimeAtual' WHERE isen_id = $idIsencao";
		if($this->getConexao()->exec($sql))
			return true;
		else
			return false;
	}
	public function adicionarIsencaoNoVinculo(Vinculo $vinculo){
		$idCartao = $vinculo->getCartao()->getId();
		$inicio = $vinculo->getIsencao()->getDataDeInicio();
		$fim = $vinculo->getIsencao()->getDataFinal();
		if(!$vinculo->isActive()){
			return false;
		}
		$tempoA = strtotime($vinculo->getIsencao()->getDataDeInicio());
		$tempoB = strtotime($vinculo->getIsencao()->getDataFinal());
		$tempoAgora = time();
		//Não adicionar isenção para o passado. 
		if($tempoA < strtotime ( "-1 days" )){
			echo '<p>Não é possível adicionar isenção para o passado</p>';
			return false;
		}
		//Não adicionar caso o usuário inverta as datas. 
		if($tempoB <= $tempoA){
			echo '<p>Talvez você tenha trocado as datas. </p>';
			return false;	
		}
		$sql = "INSERT into isencao(isen_inicio,isen_fim,cart_id) VALUES('$inicio', '$fim', $idCartao)";
		if($this->getConexao()->exec($sql))
			return true;
		return false;
		
	}
	public function adicionarCreditos(Vinculo $vinculo, $valorVendido, $idUsuario){
		$novoValor = $vinculo->getCartao()->getCreditos();
		$idCartao = $vinculo->getCartao()->getId();
		$valorVendido = floatval($valorVendido);
		$dataTimeAtual = date ( "Y-m-d G:i:s" );
		
		$sql = "UPDATE cartao set cart_creditos = $novoValor WHERE cart_id = $idCartao";
		
		$sql2 = "INSERT into transacao(tran_valor, tran_descricao, tran_data, usua_id) 
				VALUES($valorVendido, 'Venda de Créditos','$dataTimeAtual', $idUsuario)";
		$this->getConexao()->beginTransaction();
		
		
		//echo $sql;
		if(!$this->getConexao()->exec($sql)){
			$this->getConexao()->rollBack();
			return false;
		}
		if(!$this->getConexao()->exec($sql2)){
			$this->getConexao()->rollBack();
			return false;
		}
		$this->getConexao()->commit();
		return true;
		
		
	}
	
	public function vinculoPorId(Vinculo $vinculo){
		$idVinculo = $vinculo->getId();
		$sql = 	"SELECT * FROM vinculo
		INNER JOIN usuario ON vinculo.usua_id = usuario.usua_id
		LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
		LEFT JOIN vinculo_tipo ON vinculo.vinc_id = vinculo_tipo.vinc_id
		LEFT JOIN tipo ON vinculo_tipo.tipo_id = tipo.tipo_id
		WHERE vinculo.vinc_id = $idVinculo";
		$result = $this->getConexao ()->query ($sql);
		foreach($result as $linha){

			
			$vinculo->getResponsavel()->setNome($linha['usua_nome']);
			$vinculo->getResponsavel()->setId($linha['usua_id']);
			
				
			$vinculo->getResponsavel()->setIdBaseExterna($linha['id_base_externa']);
			$vinculo->getCartao()->setId($linha['cart_id']);
			$vinculo->getCartao()->getTipo()->setNome($linha ['tipo_nome']);
			$vinculo->getCartao()->getTipo()->setId($linha ['tipo_id']);
			$vinculo->getCartao()->setNumero($linha ['cart_numero']);
			$vinculo->getCartao()->setCreditos($linha['cart_creditos']);
			$vinculo->setInicioValidade($linha ['vinc_inicio']);
			$vinculo->setFinalValidade($linha['vinc_fim']);
			$vinculo->getResponsavel()->setNivelAcesso($linha['usua_nivel']);
			$vinculo->setQuantidadeDeAlimentosPorTurno($linha['vinc_refeicoes']);
			$vinculo->setAvulso($linha['vinc_avulso']);
			return $vinculo;
			
		}
	}
	public function isencaoValidaDoVinculo(Vinculo $vinculo){
		$dataTimeAtual = date ( "Y-m-d G:i:s" );
		$idVinculo = $vinculo->getId();
		$sql = 	"SELECT * FROM vinculo
		LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
		INNER JOIN isencao ON cartao.cart_id = isencao.cart_id
		WHERE (vinculo.vinc_id = $idVinculo) AND  ('$dataTimeAtual' < isencao.isen_fim);";
		$result = $this->getConexao ()->query ($sql);
		foreach($result as $linha){
			if(isset($linha['isen_id'])){
				$vinculo->setIsencao(new Isencao());
				$vinculo->setIsento(true);
				$vinculo->getIsencao()->setId($linha['isen_id']);
				$vinculo->getIsencao()->setDataDeInicio($linha['isen_inicio']);
				$vinculo->getIsencao()->setDataFinal($linha['isen_fim']);
		
			}
			return $vinculo;
				
		}
	}
	public function retornaVinculosValidosDeCartao(Cartao $cartao){
		$lista = array();
		$idCartao = $cartao->getId();
		$dataTimeAtual = date ( "Y-m-d G:i:s" );
		$sql =  "SELECT * FROM usuario INNER JOIN vinculo
		ON vinculo.usua_id = usuario.usua_id
		LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
		LEFT JOIN tipo ON cartao.tipo_id = tipo.tipo_id
		WHERE (cartao.cart_id = $idCartao)
		AND ('$dataTimeAtual' BETWEEN vinc_inicio AND vinc_fim)";
		$result = $this->getConexao ()->query ($sql );
	
		foreach($result as $linha){
			$vinculo = new Vinculo();
			$vinculo->setId($linha['vinc_id']);
			$vinculo->getResponsavel()->setNome($linha['usua_nome']);
			$vinculo->getResponsavel()->setIdBaseExterna($linha['id_base_externa']);
			$vinculo->getCartao()->setId($linha['cart_id']);
			$vinculo->getCartao()->getTipo()->setNome($linha ['tipo_nome']);
			$vinculo->getCartao()->setNumero($linha ['cart_numero']);
			$vinculo->setInicioValidade($linha ['vinc_inicio']);
			$vinculo->setFinalValidade($linha['vinc_fim']);
			$vinculo->setAvulso($linha['vinc_avulso']);
			$lista[] = $vinculo;
	
	
		}
		return $lista;
	}
	
	public function retornaVinculoValidoDeCartao(Cartao $cartao){

		$idCartao = $cartao->getId();
		$dataTimeAtual = date ( "Y-m-d G:i:s" );
		$sql =  "SELECT * FROM usuario INNER JOIN vinculo
		ON vinculo.usua_id = usuario.usua_id
		LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
		LEFT JOIN tipo ON cartao.tipo_id = tipo.tipo_id
		WHERE (cartao.cart_id = $idCartao)
		AND ('$dataTimeAtual' BETWEEN vinc_inicio AND vinc_fim)";
		$result = $this->getConexao ()->query ($sql );
		$vinculo = NULL;
		foreach($result as $linha){
			$vinculo = new Vinculo();
			$vinculo->setId($linha['vinc_id']);
			$vinculo->setQuantidadeDeAlimentosPorTurno($linha['vinc_refeicoes']);
			$vinculo->getResponsavel()->setNome($linha['usua_nome']);
			$vinculo->getResponsavel()->setIdBaseExterna($linha['id_base_externa']);
			$vinculo->getCartao()->setId($linha['cart_id']);
			$vinculo->getCartao()->getTipo()->setNome($linha ['tipo_nome']);
			$vinculo->getCartao()->getTipo()->setValorCobrado($linha['tipo_valor']);
			$vinculo->getCartao()->setNumero($linha ['cart_numero']);
			$vinculo->setInicioValidade($linha ['vinc_inicio']);
			$vinculo->setFinalValidade($linha['vinc_fim']);
			$vinculo->setAvulso($linha['vinc_avulso']);
			
	
	
		}
		return $vinculo;
	}
	public function retornaVinculosVencidosDeCartao(Cartao $cartao){
		$lista = array();
		$idCartao = $cartao->getId();
		$dataTimeAtual = date ( "Y-m-d G:i:s" );
		$sql =  "SELECT * FROM usuario INNER JOIN vinculo
		ON vinculo.usua_id = usuario.usua_id
		LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
		LEFT JOIN tipo ON cartao.tipo_id = tipo.tipo_id
		WHERE (cartao.cart_id = $idCartao)
		AND ('$dataTimeAtual' > vinc_fim)";
		$result = $this->getConexao ()->query ($sql );
	
		foreach($result as $linha){
			$vinculo = new Vinculo();
			$vinculo->setId($linha['vinc_id']);
			$vinculo->getResponsavel()->setNome($linha['usua_nome']);
			$vinculo->getResponsavel()->setIdBaseExterna($linha['id_base_externa']);
			$vinculo->getCartao()->setId($linha['cart_id']);
			$vinculo->getCartao()->getTipo()->setNome($linha ['tipo_nome']);
			$vinculo->getCartao()->setNumero($linha ['cart_numero']);
			$vinculo->setInicioValidade($linha ['vinc_inicio']);
			$vinculo->setFinalValidade($linha['vinc_fim']);
			$vinculo->setAvulso($linha['vinc_avulso']);
			$lista[] = $vinculo;
	
	
		}
		return $lista;
	}
	public function retornaVinculosFuturosDeCartao(Cartao $cartao){
		$lista = array();
		$idCartao = $cartao->getId();
		$dataTimeAtual = date ( "Y-m-d G:i:s" );
		$sql =  "SELECT * FROM usuario INNER JOIN vinculo
		ON vinculo.usua_id = usuario.usua_id
		LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
		LEFT JOIN tipo ON cartao.tipo_id = tipo.tipo_id
		WHERE (cartao.cart_id = $idCartao)
		AND ('$dataTimeAtual' < vinc_inicio AND '$dataTimeAtual' < vinc_fim)";
		$result = $this->getConexao ()->query ($sql );
	
		foreach($result as $linha){
			$vinculo = new Vinculo();
			$vinculo->setId($linha['vinc_id']);
			$vinculo->getResponsavel()->setNome($linha['usua_nome']);
			$vinculo->getResponsavel()->setIdBaseExterna($linha['id_base_externa']);
			$vinculo->getCartao()->setId($linha['cart_id']);
			$vinculo->getCartao()->getTipo()->setNome($linha ['tipo_nome']);
			$vinculo->getCartao()->setNumero($linha ['cart_numero']);
			$vinculo->setInicioValidade($linha ['vinc_inicio']);
			$vinculo->setFinalValidade($linha['vinc_fim']);
			$vinculo->setAvulso($linha['vinc_avulso']);
			$lista[] = $vinculo;
	
	
		}
		return $lista;
	}
	public function atualizaValidade(Vinculo $vinculo){
		$data= $vinculo->getFinalValidade();
		$idVinculo = $vinculo->getId();
	
		if(!$idVinculo)
			return false;
		
		$sqlUpdate = "UPDATE vinculo set vinc_fim = '$data' WHERE vinc_id = $idVinculo";
		if($this->getConexao()->exec($sqlUpdate))
			return true;
		return false;
		
	}
	
	public function usuarioJaTemVinculo(Usuario $usuario){
		$idBaseExterna = $usuario->getIdBaseExterna();
		$dataTimeAtual = date ( "Y-m-d G:i:s" );
		$sql =  "SELECT * FROM usuario INNER JOIN vinculo
		ON vinculo.usua_id = usuario.usua_id
		LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
		LEFT JOIN tipo ON cartao.tipo_id = tipo.tipo_id WHERE (usuario.id_base_externa = $idBaseExterna)
		AND ('$dataTimeAtual' BETWEEN vinc_inicio AND vinc_fim) AND vinc_avulso = FALSE";
// 		echo $sql;
		$result = $this->getConexao ()->query ($sql );
		foreach($result as $linha){
			
			return true;
		}
		return false;
		
	}
	
	public function adicionaVinculo(Vinculo $vinculo) {

		$inicio = $vinculo->getInicioValidade();
		$usuarioBaseExterna = $vinculo->getResponsavel()->getIdBaseExterna();
		$numeroCartao = $vinculo->getCartao()->getNumero();
		$dataDeValidade = $vinculo->getFinalValidade();
		$tipoCartao = $vinculo->getCartao()->getTipo()->getId();
		
		
		if(!$vinculo->getResponsavel()->getId()){
			echo "NAo tem ID";
		    return false;
		}
		
		$idBaseLocal = $vinculo->getResponsavel()->getId();
		$this->verificaCartao($vinculo->getCartao());
		if(!$vinculo->getCartao()->getId())
			return false;
		$idCartao = $vinculo->getCartao()->getId();
		$refeicoes = $vinculo->getQuantidadeDeAlimentosPorTurno();
		$descricao = $vinculo->getDescricao();
		$this->getConexao()->beginTransaction();
		
		if($vinculo->isAvulso())
			$sqlInsertVinculo = "INSERT INTO vinculo(usua_id, cart_id, vinc_refeicoes, vinc_avulso, vinc_inicio, vinc_fim, vinc_descricao) VALUES($idBaseLocal, $idCartao, $refeicoes,TRUE,'$inicio', '$dataDeValidade', '$descricao')";
		else
			$sqlInsertVinculo = "INSERT INTO vinculo(usua_id, cart_id, vinc_refeicoes, vinc_avulso, vinc_inicio, vinc_fim, vinc_descricao) VALUES($idBaseLocal, $idCartao, 1,FALSE,'$inicio', '$dataDeValidade', 'Padrão')";
		
		
		if(!$this->getConexao()->exec($sqlInsertVinculo)){
			$this->getConexao()->rollBack();
			return false;
		}
		$idVinculo = $this->getConexao()->lastInsertId('vinculo_vinc_id_seq');
		if(!$this->getConexao()->exec("INSERT INTO vinculo_tipo(vinc_id, tipo_id) VALUES($idVinculo, $tipoCartao)")){
			$this->getConexao()->rollBack();
			return false;
		}
		$this->getConexao()->commit();
		return true;
	}
	
	
	/**
	 * Retorna true se o cartão possuir vinculo válido. 
	 * Retorna false se o cartão não possui um vinculo válido. 
	 * 
	 * @param Cartao $cartao
	 * @return boolean
	 */
	public function cartaoTemVinculo(Cartao $cartao){
		$numero = $cartao->getNumero();
		$sql =  "SELECT * FROM vinculo
		LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
		WHERE (cartao.cart_numero = '$numero') LIMIT 1";
		$resultSet = $this->getConexao()->query($sql);
		foreach($resultSet as $linha){
			$cartao->setId($linha['cart_id']);
			return true;
		}
		return false;
	}
	
	
	
	/**
	 * Atrav�s de um numero de cart�o iremos retornar seu verdadeiro ID. 
	 * Mas antes iremos alterar seu tipo. 
	 * 
	 * Caso ele nem exista a gente cadastra com o tipo oferecido aqui. 
	 * 
	 * @param int $numeroCartao
	 * @param int $idTipo
	 */
	public function verificaCartao(Cartao $cartao){
		$numeroCartao = $cartao->getNumero();
		$idTipo = $cartao->getTipo()->getId();
		
		$result = $this->getConexao()->query("SELECT * FROM cartao WHERE cart_numero = '$numeroCartao'");
		foreach($result as $linha){
// 			if($linha['cart_creditos'] > 0)
// 				return false;
			if($linha['tipo_id'] != $idTipo){
				if(!$this->getConexao()->exec("UPDATE cartao set tipo_id = $idTipo WHERE cart_numero = '$numeroCartao'"))
					return false;
			}
			$cartao->getTipo()->setId($linha['tipo_id']);
			$cartao->setId($linha['cart_id']);
			return $linha['cart_id'];
		}
		if($this->getConexao()->query("INSERT INTO cartao(cart_numero, cart_creditos, tipo_id) VALUES('$numeroCartao', 0, $idTipo)")){
			foreach($this->getConexao()->query("SELECT * FROM cartao WHERE cart_numero = '$numeroCartao'") as $otraLinha){
				$cartao->setId($otraLinha['cart_id']);
				return $otraLinha['cart_id'];
			}
		}
		return false;
		
		
	}
	/**
	 * Vamos pegar da base exter a ecopiar para a base local.
	 * Se Nem existir na base externa, É o usuario frescando. Preciso dar nem resposta pra ele. Aborto tudo logo.
	 * Fa�amos um insert aqui.
	 * Apos esse insert iremos pegar o id inserido na base e retornalo. 
	 * Retorna 0, deu nada certo. Essa parada acaba aqui. 
	 * @param int $idBaseExterna
	 * @return int
	 */
	public function verificarUsuario(Usuario $usuario, PDO $conexaoRemota){
		
		$idBaseExterna = $usuario->getIdBaseExterna();
		
		$result = $this->getConexao()->query("SELECT id_base_externa, usua_id FROM usuario WHERE id_base_externa = $idBaseExterna");
		foreach ($result as $linha){
			$usuario->setId($linha['usua_id']);
			
			return $linha['usua_id'];
		}
		$sqlS = "SELECT * FROM vw_usuarios_autenticacao_catraca WHERE id_usuario = $idBaseExterna";
// 		echo $sqlS;
		$result2 = 	$conexaoRemota->query($sqlS);
		foreach($result2 as $linha){
			
			$nivel = Sessao::NIVEL_COMUM;
			$nome = $linha['nome'];
			$nome = preg_replace ('/[^a-zA-Z0-9\s]/', '', $nome);
			$nome = strtoupper ( $nome );
			$email = str_replace("'","",$linha['email']);;
			$login = $linha['login'];
			$senha = $linha['senha'];
			$idBaseExterna = $linha['id_usuario'];
			$sqlInserir = "INSERT into usuario(usua_login,usua_senha, usua_nome,usua_email, usua_nivel, id_base_externa)
					VALUES	('$login', '$senha', '$nome','$email', $nivel, $idBaseExterna)";
				
			//echo $sqlInserir;
			if($this->getConexao()->exec($sqlInserir))
			{
				
				foreach($this->getConexao()->query("SELECT * FROM usuario WHERE id_base_externa = $idBaseExterna") as $linha3){
					$usuario->setId($linha3['usua_id']);
					
					return $linha3['usua_id'];
				}
			}
		}
		return 0;
		
		
	}
	public function isencoesValidas($dataReferencia = null, $nomeUsuario = null){
		$lista = array();
		
		if($dataReferencia == null)
			$dataReferencia = date ( "Y-m-d G:i:s" );
		$outroFiltro = "";
		if($nomeUsuario != null){
			$nomeUsuario = preg_replace ('/[^a-zA-Z0-9\s]/', '', $nomeUsuario);
			$nomeUsuario = strtoupper ( $nomeUsuario );
			$outroFiltro = "AND usua_nome LIKE '%$nomeUsuario%'";
		}
		
		$sql =  "SELECT * FROM usuario INNER JOIN vinculo
		ON vinculo.usua_id = usuario.usua_id
		LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
		LEFT JOIN tipo ON cartao.tipo_id = tipo.tipo_id
		INNER JOIN isencao ON cartao.cart_id = isencao.cart_id
		WHERE ('$dataReferencia' BETWEEN vinc_inicio AND vinc_fim) AND ('$dataReferencia' BETWEEN isen_inicio AND isen_fim) $outroFiltro LIMIT 100";
		$result = $this->getConexao ()->query ($sql );
		foreach($result as $linha){
				
			$vinculo = new Vinculo();
			$vinculo->setId($linha['vinc_id']);
			$vinculo->getCartao()->setTipo(new Tipo());
			$vinculo->getResponsavel()->setId($linha['usua_id']);
			$vinculo->getResponsavel()->setNome($linha['usua_nome']);
		

			$vinculo->getResponsavel()->setIdBaseExterna($linha['id_base_externa']);
			$vinculo->getCartao()->setId($linha['cart_id']);
			$vinculo->getCartao()->getTipo()->setNome($linha ['tipo_nome']);
			$vinculo->getCartao()->setNumero($linha ['cart_numero']);
			$vinculo->setInicioValidade($linha ['vinc_inicio']);
			$vinculo->setFinalValidade($linha['vinc_fim']);
			$vinculo->setAvulso($linha['vinc_avulso']);
			$lista[] = $vinculo;
		
		
		}
		return $lista;
	}

	public function buscaVinculos($dataReferencia = null, $nomeUsuario = null){
		
		$lista = array();
		
		if($dataReferencia == null)
			$dataReferencia = date ( "Y-m-d G:i:s" );
		$outroFiltro = "";
		if($nomeUsuario != null){
			$nomeUsuario = preg_replace ('/[^a-zA-Z0-9\s]/', '', $nomeUsuario);
			$nomeUsuario = strtoupper ( $nomeUsuario );
			$outroFiltro = "AND usua_nome LIKE '%$nomeUsuario%'";
		}
		
		$sql =  "SELECT * FROM usuario INNER JOIN vinculo
		ON vinculo.usua_id = usuario.usua_id
		LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
		LEFT JOIN tipo ON cartao.tipo_id = tipo.tipo_id
		
		WHERE '$dataReferencia' BETWEEN vinc_inicio AND vinc_fim  $outroFiltro LIMIT 100";
		$result = $this->getConexao ()->query ($sql );
		foreach($result as $linha){
			
			$vinculo = new Vinculo();
			$vinculo->setId($linha['vinc_id']);
			$vinculo->getCartao()->setTipo(new Tipo());
			$vinculo->getResponsavel()->setId($linha['usua_id']);
			$vinculo->getResponsavel()->setNome($linha['usua_nome']);
			$vinculo->getResponsavel()->setIdBaseExterna($linha['id_base_externa']);
			$vinculo->getCartao()->setId($linha['cart_id']);
			$vinculo->getCartao()->getTipo()->setNome($linha ['tipo_nome']);
			$vinculo->getCartao()->setNumero($linha ['cart_numero']);
			$vinculo->setInicioValidade($linha ['vinc_inicio']);
			$vinculo->setFinalValidade($linha['vinc_fim']);
			$vinculo->setAvulso($linha['vinc_avulso']);
			$lista[] = $vinculo;
		
		
		}
		return $lista;
		
	}
	/**
	 * Atribui um vinculo ao cartão através do número. 
	 * 
	 * @param Cartao $cartao
	 */
	public function vinculoDoCartao(Cartao $cartao){

		$numeroCartao = $cartao->getNumero();
		$sqlVerificaNumero = "SELECT * FROM usuario
		INNER JOIN vinculo ON vinculo.usua_id = usuario.usua_id
		LEFT JOIN cartao ON cartao.cart_id = vinculo.cart_id
		LEFT JOIN tipo ON cartao.tipo_id = tipo.tipo_id
		WHERE cartao.cart_numero = '$numeroCartao'";
		$result = $this->getConexao()->query($sqlVerificaNumero);
		$vinculo = new Vinculo();
		foreach($result as $linha){
			$vinculo->setId($linha['vinc_id']);
			$vinculo->setCartao($cartao);
			$vinculo->getCartao()->getTipo()->setNome($linha['tipo_nome']);
			$vinculo->getResponsavel()->setNome($linha['usua_nome']);
			$vinculo->getResponsavel()->setIdBaseExterna($linha['id_base_externa']);
			$vinculo->getCartao()->setId($linha['cart_id']);
			$vinculo->setAvulso($linha['vinc_avulso']);
			if($linha['vinc_avulso'])
			{
				$vinculo->getResponsavel()->setNome("Avulso");
			}
			return $vinculo;
		}
		return false;
	}

	
}

?>