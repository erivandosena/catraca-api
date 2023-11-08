<?php

/**
* Inicializacao do Slim e controlers RESTful.
*
* Arquivo de definicoes para execucao da aplicacao API RESTful com Framework Slim.
*
* PHP versao 5
*
* LICENCA: Este arquivo fonte esta sujeito a versao 3.01 da licen�a PHP 
* que esta disponivel atraves da world-wide-web na seguinte URI:
* Http://www.php.net/license/3_01.txt. Se voc� n�o recebeu uma copia da 
* Licenca PHP e nao consegue obte-la atraves da web, por favor, envie uma 
* nota para license@php.net para que possamos enviar-lhe uma copia imediatamente.
*
* @category   CategoryName
* @package    PackageName
* @author     Erivando Sena <erivandoramos@unilab.edu.br>, demais participantes
* @copyright  2015-2015 Unilab
* @license    http://www.php.net/license/3_01.txt PHP License 3.01
* @version    SVN: $Id$
* @link       http://www.unilab.edu.br
* @see        NetOther, Net_Sample::Net_Sample()
* @since      File available since Release 1.2.0
* @deprecated File deprecated in Release 2.0.0
*/

$LOCAL_MAIL = '/dados/sites/adm/catraca/mail/';
$LOCAL = '/dados/sites/adm/catraca/webservice';
$LOCAL_API = __DIR__;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \Psr7Middlewares\Middleware;
use \Slim\Middleware\HttpBasicAuthentication;
use \Slim\Middleware\HttpBasicAuthentication\PdoAuthenticator;

require($LOCAL_MAIL."phpmailer/class.phpmailer.php");
require $LOCAL_API . '/../vendor/autoload.php';
require $LOCAL . '/v2/db.php';

$pdo = new \PDO('sqlite:' . $LOCAL . '/v2/users.db');
/*
 * Gerar Hash da senha
 */
/*
$hash = password_hash("senha", PASSWORD_DEFAULT);
echo $hash;
*/

$postgresql = new \Conexao\PostgreSQL();

$config = [
		'settings' => [
				'displayErrorDetails' => true,
				'addContentLengthHeader' => false,
				'bd_homologacao' => $postgresql->getDBHomologacao(),
				'bd_producao' => $postgresql->getDBProducao(),
				'bd_externo' => $postgresql->getDBExterno()]
];

$app = new \Slim\App($config);


$container = $app->getContainer();

/*
 * Logs
*/
$container['logger'] = function($c) {
	$logger = new \Monolog\Logger('WebService-RESTful-Log');
	$logger->pushHandler(new \Monolog\Handler\ChromePHPHandler(\Monolog\Logger::DEBUG));
	$logger->pushHandler(new \Monolog\Handler\RotatingFileHandler($LOCAL_API . "../logs/app.log", 0, \Monolog\Logger::DEBUG));
	return $logger;
};

/*
 * Error Handling
 */
$container['errorHandler'] = function ($c) {
	return function ($request, $response, $exception) use ($c) {
		$data = [[
				'code' => $exception->getCode(),
				'message' => $exception->getMessage(),
				'file' => $exception->getFile(),
				'line' => $exception->getLine(),
				'trace' => explode("\n", $exception->getTraceAsString()),
		]];

		return $c->get('response')
		->withStatus(500)
		->withHeader('Content-Type', 'application/json')
		->write('{"erro":'. json_encode($data) .'}');
	};
};

$container['phpErrorHandler'] = function ($c) {
	return function ($request, $response, $exception) use ($c) {
		$data = [[
				'code' => $exception->getCode(),
				'message' => $exception->getMessage(),
				'file' => $exception->getFile(),
				'line' => $exception->getLine(),
				'trace' => explode("\n", $exception->getTraceAsString()),
		]];

		return $c->get('response')
		->withStatus(500)
		->withHeader('Content-Type', 'application/json')
		->write('{"erro":'. json_encode($data) .'}');
	};
};

$container['notFoundHandler'] = function ($c) {
	return function ($request, $response, $exception) use ($c) {
		$data = [[
				'message' => "Recurso nao encontrado!",
		]];
		
		return $c->get('response')
		->withStatus(404)
		->withHeader('Content-Type', 'application/json; charset=iso-8859-1')
		->write('{"erro":'. json_encode($data) .'}');
	};
};

/*
 * Databases
 */
$container['db_homologacao'] = function ($c) {
	$db = $c['settings']['bd_homologacao'];
	try{
		$string_con = 'pgsql:dbname='.$db['bd'].';host='.$db['host'].';port='.$db['porta'];
		$pdo = new PDO($string_con, $db['usuario'], $db['senha']);
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}catch(PDOexception $error_conecta){
		$c['logger']->err('Erro conectando no PostgreSQL: ' . $error_conecta->getMessage());
		echo '{"erro":[{"mensagem":'. json_encode('Erro conectando no PostgreSQL: ' . $error_conecta->getMessage()) .'}]}';
	}
	return $pdo;
};

$container['db_producao'] = function ($c) {
	$db = $c['settings']['bd_producao'];
	try{
		$string_con = 'pgsql:dbname='.$db['bd'].';host='.$db['host'].';port='.$db['porta'];
		$pdo = new PDO($string_con, $db['usuario'], $db['senha']);
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}catch(PDOexception $error_conecta){
		$c['logger']->err('Erro conectando no PostgreSQL: ' . $error_conecta->getMessage());
		echo '{"erro":[{"mensagem":'. json_encode('Erro conectando no PostgreSQL: ' . $error_conecta->getMessage()) .'}]}';
	}
	return $pdo;
};

$container['db_externo'] = function ($c) {
	$db = $c['settings']['bd_externo'];
	try{
		$string_con = 'pgsql:dbname='.$db['bd'].';host='.$db['host'].';port='.$db['porta'];
		$pdo = new PDO($string_con, $db['usuario'], $db['senha']);
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}catch(PDOexception $error_conecta){
		$c['logger']->err('Erro conectando no PostgreSQL: ' . $error_conecta->getMessage());
		echo '{"erro":[{"mensagem":'. json_encode('Erro conectando no PostgreSQL: ' . $error_conecta->getMessage()) .'}]}';
	}
	return $pdo;
};

/*
 * Autenticacao
 */
$app->add(new \Slim\Middleware\HttpBasicAuthentication([
		"secure" => true,
		"relaxed" => ["www.catraca.unilab.edu.br"],
		"realm" => "Protected",
		"authenticator" => new PdoAuthenticator(["pdo" => $pdo]),
		"error" => function ($request, $response, $arguments) {
			$data = [];
			$data["status"] = "error";
			$data["message"] = $arguments["message"];
			return $response->write('{"erro":'. json_encode([$data]) .'}');
		}
]));

$app->add(function($request, $response, $next) {
	$response = $next($request, $response);
	return $response->withHeader('Content-Type', 'application/json');
});


/*
 * Rotas
 */
//----------------------------------GET-----------------------------------------------
$app->get('/tipo/tipos', function (Request $request, Response $response, $args) {

	$sql = "SELECT tipo_id, tipo_nome, tipo_valor FROM tipo ORDER BY tipo_id;";
	try {
		$db = $this->db_producao;
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"tipos":' . json_encode($dados) . '}');
});

$app->get('/turno/turnos', function (Request $request, Response $response, $args) {

	$sql = "SELECT turn_id, turn_hora_inicio, turn_hora_fim, turn_descricao FROM turno ORDER BY turn_id;";
	try {
		$db = $this->db_producao;
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"turnos":' . json_encode($dados) . '}');
});

$app->get('/turno/{ip}/{hora}', function (Request $request, Response $response, $args) {
	$ip = $request->getAttribute('ip');
	$hora = $request->getAttribute('hora');
	$hora_atual = date("G:i:s", strtotime($hora));

	$sql = "SELECT distinct turno.turn_id, turno.turn_hora_inicio, turno.turn_hora_fim, turno.turn_descricao FROM turno
		INNER JOIN unidade_turno ON turno.turn_id = unidade_turno.turn_id
		INNER JOIN catraca_unidade ON unidade_turno.unid_id = catraca_unidade.unid_id
		INNER JOIN catraca ON catraca_unidade.catr_id = catraca.catr_id WHERE catraca.catr_ip = :ip
		AND turno.turn_hora_inicio <= :hora_ini
		AND turno.turn_hora_fim >= :hora_fim;";
	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":ip", sprintf(long2ip($ip)), PDO::PARAM_STR);
		$stmt->bindParam(":hora_ini", $hora_atual, PDO::PARAM_STR);
		$stmt->bindParam(":hora_fim", $hora_atual, PDO::PARAM_STR);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"turno":' . json_encode($dados) . '}');
});

$app->get('/unidade/unidades', function (Request $request, Response $response, $args) {

	$sql = "SELECT unid_id, unid_nome FROM unidade ORDER BY unid_id;";
	try {
		$db = $this->db_producao;
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"unidades":' . json_encode($dados) . '}');
});

$app->get('/custo_refeicao/custos_refeicao', function (Request $request, Response $response, $args) {

	$sql = "SELECT cure_valor, cure_data,cure_id FROM custo_refeicao;";
	try {
		$db = $this->db_producao;
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"custos_refeicao":' . json_encode($dados) . '}');
});

$app->get('/custo_refeicao/catraca/{id_catraca}', function (Request $request, Response $response, $args) {
	$id_catraca = $request->getAttribute('id_catraca');
	
	$sql = "SELECT custo_refeicao.cure_valor, custo_refeicao.cure_data, custo_refeicao.cure_id FROM custo_refeicao 
			INNER JOIN custo_unidade ON custo_refeicao.cure_id = custo_unidade.cure_id 
			INNER JOIN unidade ON unidade.unid_id = custo_unidade.unid_id 
			INNER JOIN catraca_unidade ON catraca_unidade.unid_id = unidade.unid_id 
			WHERE catraca_unidade.catr_id = :catraca LIMIT 1;";
	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":catraca", $id_catraca, PDO::PARAM_INT);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"custo_refeicao":' . json_encode($dados) . '}');
});

$app->get('/custo_unidade/custos_unidade', function (Request $request, Response $response, $args) {

	$sql = "SELECT cuun_id, unid_id, cure_id FROM custo_unidade;";
	try {
		$db = $this->db_producao;
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"custos_unidade":' . json_encode($dados) . '}');
});

$app->get('/usuario/usuarios', function (Request $request, Response $response, $args) {

	$sql = "SELECT usua_id, usua_nome, usua_email, usua_login, usua_senha, usua_nivel, id_base_externa FROM usuario ORDER BY usua_id DESC;";
	try {
		$db = $this->db_producao;
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"usuarios":' . json_encode($dados) . '}');
});

$app->get('/usuario/{login}', function (Request $request, Response $response, $args) {
	$login_base64 = $request->getAttribute('login');
	$login = base64_decode($login_base64);
	$sql = "SELECT usua_id, usua_nome, usua_email, usua_login, usua_senha, usua_nivel, id_base_externa FROM usuario WHERE LOWER(usua_login) = LOWER(:login) LIMIT 1;";
	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":login", $login, PDO::PARAM_STR);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"usuario":' . json_encode($dados) . '}');
});

$app->get('/usuario/externo/usuarios', function (Request $request, Response $response, $args) {
	$sql = "SELECT DISTINCT id_usuario, categoria, tipo_usuario, id_status_servidor, status_discente, status_servidor FROM vw_usuarios_catraca ORDER BY 1 ASC;";

	try {
		$db = $this->db_externo;
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"usuarios_externo":' . json_encode($dados) . '}');
});

$app->get('/usuario/externo/{id_externo}', function (Request $request, Response $response, $args) {
	$id_externo = $request->getAttribute('id_externo');
	
	$sql = "SELECT id_usuario, id_tipo_usuario, id_categoria, id_status_servidor, id_status_discente, siape, cpf_cnpj FROM vw_usuarios_catraca WHERE id_usuario = :id LIMIT 1;";
	try {
		$db = $this->db_externo;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":id", $id_externo, PDO::PARAM_INT);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"usuario_externo":' . json_encode($dados) . '}');
});

$app->get('/usuario/app/login/{login}', function (Request $request, Response $response, $args) {
	$login_base64 = $request->getAttribute('login');
	$login = base64_decode($login_base64);
	
	$sql = "SELECT usuario.usua_id, view.nome AS usua_nome, view.email AS usua_email, view.login AS usua_login, view.senha AS usua_senha, usuario.usua_nivel, view.id_usuario AS id_base_externa FROM usuario
					INNER JOIN vw_usuarios_autenticacao_catraca as view ON view.id_usuario = usuario.id_base_externa
					WHERE LOWER(usua_login) = LOWER(:login) LIMIT 1;";
	
	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":login", $login, PDO::PARAM_STR);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"usuario":' . json_encode($dados) . '}');
});

$app->get('/usuario/externo/documento/{numero}', function (Request $request, Response $response, $args) {
	$numero_doc = $request->getAttribute('numero');

	$sql = "SELECT id_usuario, id_tipo_usuario, id_categoria, id_status_servidor, id_status_discente, siape, cpf_cnpj FROM vw_usuarios_catraca WHERE siape = :numero_documento;";
	try {
		$db = $this->db_externo;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":numero_documento", $numero_doc, PDO::PARAM_STR);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"usuario_externo":' . json_encode($dados) . '}');
});

$app->get('/usuario/extrato/{login_usuario}', function (Request $request, Response $response, $args) {
	$login_usuario_base64 = $request->getAttribute('login_usuario');

	$login_usuario = base64_decode($login_usuario_base64);
	$sql = "
		SELECT 
		descricao, 
		data, 
		campus as local, 
		valor 
		FROM 
		(
		SELECT 
		CASE transacao.tran_descricao 
		WHEN 'Venda de Créditos' THEN 'Crédito' 
		WHEN 'Estorno de valores' THEN 'Estorno' 
		ELSE 'Operação' 
		END 
		AS descricao, transacao.tran_data AS data, null as campus, transacao.tran_valor AS valor FROM transacao 
		INNER JOIN usuario ON transacao.usua_id1 = usuario.usua_id 
		INNER JOIN vinculo ON usuario.usua_id = vinculo.usua_id 
		WHERE transacao.tran_data::date BETWEEN CURRENT_DATE-30 AND CURRENT_DATE and 
		LOWER(usuario.usua_login) = LOWER(:login) 
		GROUP BY transacao.tran_descricao, transacao.tran_data, transacao.tran_valor 
		
		UNION ALL 
		
		SELECT turno.turn_descricao AS descricao, registro.regi_data AS data, unidade.unid_nome AS campus, registro.regi_valor_pago AS valor FROM registro 
		INNER JOIN vinculo ON registro.vinc_id = vinculo.vinc_id 
		INNER JOIN usuario ON vinculo.usua_id = usuario.usua_id 
		INNER JOIN catraca ON registro.catr_id = catraca.catr_id 
		INNER JOIN catraca_unidade ON registro.catr_id = catraca_unidade.catr_id 
		INNER JOIN unidade ON catraca_unidade.unid_id = unidade.unid_id 
		INNER JOIN unidade_turno ON unidade.unid_id = unidade_turno.unid_id 
		INNER JOIN turno ON unidade_turno.turn_id = turno.turn_id 
		INNER JOIN cartao ON registro.cart_id = cartao.cart_id 
		INNER JOIN tipo ON cartao.tipo_id = tipo.tipo_id and 
		registro.regi_data::date BETWEEN CURRENT_DATE-30 AND CURRENT_DATE and 
		registro.regi_data::time BETWEEN turno.turn_hora_inicio::time AND turno.turn_hora_fim::time and 
		LOWER(usuario.usua_login) = LOWER(:login) 
		GROUP BY turno.turn_descricao, registro.regi_data, unidade.unid_nome, registro.regi_valor_pago 
		
		UNION ALL 

		SELECT
		CASE WHEN cartao.cart_creditos=0 THEN NULL ELSE 'SALDO' END AS descricao,
		CASE WHEN vinculo.vinc_fim < CURRENT_TIMESTAMP THEN vinculo.vinc_fim ELSE CURRENT_DATE END AS data, 
		CASE 
		WHEN (vinculo.vinc_fim < CURRENT_TIMESTAMP AND cartao.cart_creditos=0) THEN 'VÍNCULO INATIVO em '|| TO_CHAR(vinculo.vinc_fim, 'DD/MM/YYYY ás HH24:MI:SSh') 
		WHEN (vinculo.vinc_fim < CURRENT_TIMESTAMP AND cartao.cart_creditos>0) THEN 'VÍNCULO VENCIDO em '|| TO_CHAR(vinculo.vinc_fim, 'DD/MM/YYYY ás HH24:MI:SSh') 
		ELSE NULL 
		END 
		AS campus,
		CASE WHEN (vinculo.vinc_fim < CURRENT_TIMESTAMP AND cartao.cart_creditos=0) THEN NULL ELSE cartao.cart_creditos END AS valor 
		FROM cartao
		INNER JOIN vinculo ON cartao.cart_id = vinculo.cart_id
		INNER JOIN usuario ON vinculo.usua_id = usuario.usua_id
		WHERE LOWER(usuario.usua_login) = LOWER(:login) AND vinculo.vinc_avulso = false 
		
		) extrato 
		ORDER BY data DESC;
		";
	
	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":login", $login_usuario, PDO::PARAM_STR);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		if ($dados == null) {
			
			$dados = [[
					'descricao' => "CARTÃO NÃO CADASTRADO",
					'data' => date("Y-m-d"),
					'local' => 'Até a presente data',
					'valor' => 0.00
			]];
		}
		
		$db = null;
		
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"extrato":'  . json_encode($dados) .  '}');
});

$app->get('/catraca/catracas', function (Request $request, Response $response, $args) {

	$sql = "SELECT catr_id, catr_ip, catr_tempo_giro, catr_operacao, catr_nome, catr_mac_lan, catr_mac_wlan, catr_interface_rede, catr_financeiro FROM catraca ORDER BY catr_id;";
	try {
		$db = $this->db_producao;
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"catracas":' . json_encode($dados) . '}');
});

$app->get('/mensagem/{id_catraca}', function (Request $request, Response $response, $args) {
	$id_catraca = $request->getAttribute('id_catraca');

	$sql = "SELECT mens_id, mens_institucional1, mens_institucional2,
			mens_institucional3, mens_institucional4, catr_id 
			FROM mensagem WHERE catr_id = :catraca ORDER BY mens_id;";
	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":catraca", $id_catraca, PDO::PARAM_INT);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"mensagem":' . json_encode($dados) . '}');
});

$app->get('/cartao/cartoes', function (Request $request, Response $response, $args) {

	$sql = "SELECT cart_id, cart_numero, cart_creditos, tipo_id FROM cartao ORDER BY cart_id DESC;";
	try {
		$db = $this->db_producao;
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"cartoes":' . json_encode($dados) . '}');
});

$app->get('/cartao/{numero}', function (Request $request, Response $response, $args) {
	$numero = $request->getAttribute('numero');
	
	$sql = "SELECT cartao.cart_id, cartao.cart_numero, cartao.cart_creditos, vinculo.vinc_avulso,
	tipo.tipo_valor, vinculo.vinc_refeicoes, tipo.tipo_id, vinculo.vinc_id, usuario.id_base_externa, tipo.tipo_nome,
	vinculo.vinc_descricao, vinculo.vinc_inicio, vinculo.vinc_fim, TRIM(both ' ' from SUBSTR(usuario.usua_nome, 0, 16)) || '.' as usua_nome FROM cartao
	INNER JOIN tipo ON cartao.tipo_id = tipo.tipo_id
	INNER JOIN vinculo ON vinculo.cart_id = cartao.cart_id
	INNER JOIN usuario ON usuario.usua_id = vinculo.usua_id 
	WHERE cartao.cart_numero = :numero;";
	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":numero", $numero, PDO::PARAM_STR);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"cartao_valido":' . json_encode($dados) . '}');
});

$app->get('/cartao/usuario/vinculo/isencao/{numero_cartao}', function (Request $request, Response $response, $args) {
	$numero_cartao = $request->getAttribute('numero_cartao');
	
	$sql = "select cartao.*, usuario.*, vinculo.*, isencao.* from cartao 
			inner join vinculo on vinculo.cart_id = cartao.cart_id 
			inner join usuario on vinculo.usua_id = usuario.usua_id 
			left join isencao on cartao.cart_id = isencao.cart_id 
			where cartao.cart_numero = :numero LIMIT 1;";
	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":numero", $numero_cartao, PDO::PARAM_STR);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"tabelas":' . json_encode($dados) . '}');
});

$app->get('/vinculo/vinculos', function (Request $request, Response $response, $args) {

	$sql = "SELECT vinc_id, vinc_avulso, vinc_inicio, vinc_fim, vinc_descricao, vinc_refeicoes, cart_id, usua_id FROM vinculo ORDER BY vinc_id DESC;";
	try {
		$db = $this->db_producao;
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"vinculos":' . json_encode($dados) . '}');
});

$app->get('/vinculo/vinculos/tipo', function (Request $request, Response $response, $args) {

	$sql = "SELECT vinculo.vinc_id, vinculo.vinc_avulso, vinculo.vinc_inicio, vinculo.vinc_fim, vinculo.vinc_descricao, vinculo.vinc_refeicoes,
		vinculo.cart_id, vinculo.usua_id, vinculo_tipo.tipo_id FROM
		vinculo INNER JOIN vinculo_tipo ON vinculo.vinc_id = vinculo_tipo.vinc_id ORDER BY vinc_id;";
	try {
		$db = $this->db_producao;
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"vinculos":' . json_encode($dados) . '}');
});

$app->get('/vinculo/{id}', function (Request $request, Response $response, $args) {
	$id = $request->getAttribute('id');

	$sql = "SELECT vinc_id, vinc_avulso, vinc_inicio, vinc_fim, vinc_descricao, vinc_refeicoes, cart_id, usua_id FROM vinculo WHERE vinc_id = :id ORDER BY vinc_id;";
	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"vinculo":' . json_encode($dados) . '}');
});

$app->get('/isencao/isencoes', function (Request $request, Response $response, $args) {

	$sql = "SELECT isen_id, isen_inicio, isen_fim, cart_id FROM isencao ORDER BY isen_id DESC;";
	try {
		$db = $this->db_producao;
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"isencoes":' . json_encode($dados) . '}');
});

$app->get('/isencao/{numero}/{datahora}', function (Request $request, Response $response, $args) {
	$numero = $request->getAttribute('numero');
	$datahora = $request->getAttribute('datahora');
	$data_hora_atual = date("Y-m-d G:i:s", strtotime($datahora));

	$sql = "SELECT isencao.isen_inicio, isencao.isen_fim, cartao.cart_id FROM cartao 
			INNER JOIN isencao ON isencao.cart_id = cartao.cart_id WHERE cartao.cart_numero = :numero 
			AND (:datahora BETWEEN isencao.isen_inicio AND isencao.isen_fim);";
	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":datahora", $data_hora_atual, PDO::PARAM_STR);
		$stmt->bindParam(":numero", $numero, PDO::PARAM_STR);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"isencao":' . json_encode($dados) . '}');
});

$app->get('/unidade_turno/unidades_turno', function (Request $request, Response $response, $args) {

	$sql = "SELECT untu_id, turn_id, unid_id FROM unidade_turno ORDER BY untu_id;";
	try {
		$db = $this->db_producao;
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"unidades_turno":' . json_encode($dados) . '}');
});

$app->get('/catraca_unidade/catracas_unidade', function (Request $request, Response $response, $args) {

	$sql = "SELECT caun_id, catr_id, unid_id FROM catraca_unidade ORDER BY caun_id;";
	try {
		$db = $this->db_producao;
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"catracas_unidade":' . json_encode($dados) . '}');
});

$app->get('/catraca_unidade/catraca/{id_catraca}', function (Request $request, Response $response, $args) {
	$id_catraca = $request->getAttribute('id_catraca');

	$sql = "SELECT caun_id, catr_id, unid_id FROM catraca_unidade WHERE catr_id = :catraca;";
	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":catraca", $id_catraca, PDO::PARAM_INT);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);

		if ($dados == null) {
			$status = false;
		} else {
			$status = true;
		}

		$db = null;

	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"status":' . json_encode($status) . '}');
});

$app->get('/registro/{datahoraini}/{datahorafim}', function (Request $request, Response $response, $args) {
	$datahoraini = $request->getAttribute('datahoraini');
	$datahorafim = $request->getAttribute('datahorafim');
	$data_hora_ini = date("Y-m-d G:i:s", strtotime($datahoraini));
	$data_hora_fim = date("Y-m-d G:i:s", strtotime($datahorafim));

	$sql = "SELECT regi_id, regi_data, regi_valor_pago, regi_valor_custo, cart_id, catr_id, vinc_id 
			FROM registro WHERE regi_data BETWEEN :datahora_ini AND :datahora_fim ORDER BY regi_id;";
	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":datahora_ini", $data_hora_ini, PDO::PARAM_STR);
		$stmt->bindParam(":datahora_fim", $data_hora_fim, PDO::PARAM_STR);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"registros":' . json_encode($dados) . '}');
});

$app->get('/registro/{datahoraini}/{datahorafim}/{id}', function (Request $request, Response $response, $args) {
	$datahoraini = $request->getAttribute('datahoraini');
	$datahorafim = $request->getAttribute('datahorafim');
	$id = $request->getAttribute('id');
	$data_hora_ini = date("Y-m-d G:i:s", strtotime($datahoraini));
	$data_hora_fim = date("Y-m-d G:i:s", strtotime($datahorafim));

	$sql = "SELECT COUNT(regi_id) as total FROM registro WHERE regi_data BETWEEN :datahora_ini AND :datahora_fim AND cart_id = :id;";
	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":datahora_ini", $data_hora_ini, PDO::PARAM_STR);
		$stmt->bindParam(":datahora_fim", $data_hora_fim, PDO::PARAM_STR);
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"quantidade":' . json_encode($dados) . '}');
});

$app->get('/app/{uid}', function (Request $request, Response $response, $args) {
	$uid = $request->getAttribute('uid');
	
	$sql = "SELECT app_id, app_token, usua_id, id_base_externa FROM app WHERE id_base_externa::text = :uid OR app_token::text = :uid LIMIT 1;";
	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":uid", $uid, PDO::PARAM_STR);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"app":' . json_encode($dados) . '}');
});

//----------------------------------POST----------------------------------------------
$app->post('/registro/insere', function (Request $request, Response $response, $args) {
	$dados = json_decode($request->getBody());

	$sql = "INSERT INTO registro(regi_data, regi_valor_pago, regi_valor_custo, cart_id, catr_id, vinc_id) 
			VALUES (:data, :pago, :custo, :cartao, :catraca, :vinculo);";
	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":data", $dados->regi_data, PDO::PARAM_STR);
		$stmt->bindParam(":pago", $dados->regi_valor_pago, PDO::PARAM_STR);
		$stmt->bindParam(":custo", $dados->regi_valor_custo, PDO::PARAM_STR);
		$stmt->bindParam(":cartao", $dados->cart_id, PDO::PARAM_INT);
		$stmt->bindParam(":catraca", $dados->catr_id, PDO::PARAM_INT);
		$stmt->bindParam(":vinculo", $dados->vinc_id, PDO::PARAM_INT);
		$status = $stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"status":' . json_encode($status) . '}');
});

$app->post('/catraca/insere', function (Request $request, Response $response, $args) {
	$dados = json_decode($request->getBody());

	$sql = "INSERT INTO catraca(catr_ip, catr_tempo_giro, catr_operacao, catr_nome, catr_mac_lan, catr_mac_wlan, catr_interface_rede, catr_financeiro) VALUES (:ip, :tempo, :operacao, :nome, :maclan, :macwlan, :interface, :financeiro);";

	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":ip",$dados->catr_ip, PDO::PARAM_STR);
		$stmt->bindParam(":tempo",$dados->catr_tempo_giro, PDO::PARAM_INT);
		$stmt->bindParam(":operacao",$dados->catr_operacao, PDO::PARAM_INT);
		$stmt->bindParam(":nome",$dados->catr_nome, PDO::PARAM_STR);
		$stmt->bindParam(":maclan",$dados->catr_mac_lan, PDO::PARAM_STR);
		$stmt->bindParam(":macwlan",$dados->catr_mac_wlan, PDO::PARAM_STR);
		$stmt->bindParam(":interface",$dados->catr_interface_rede, PDO::PARAM_STR);
		$stmt->bindParam(":financeiro",$dados->catr_financeiro, PDO::PARAM_BOOL);
		$status = $stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"status":' . json_encode($status) . '}');
});

$app->post('/app/insere', function (Request $request, Response $response, $args) {
	$dados = json_decode($request->getBody());
	
	$sql = "INSERT INTO app(app_token, usua_id, id_base_externa) VALUES (:token, :uid, :usid);";
	
	$db= $this->db_producao;
	try {
		$db->beginTransaction();
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":token",$dados->app_token, PDO::PARAM_STR);
		$stmt->bindParam(":uid",$dados->usua_id, PDO::PARAM_INT);
		$stmt->bindParam(":usid",$dados->id_base_externa, PDO::PARAM_INT);
		$status = $stmt->execute();
		$db->commit();
	} catch(Exception $e){
		$db->rollback();
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	} finally {
		$db = null;
	}
	return $response->write('{"status":' . json_encode($status) . '}');
});

//----------------------------------PUT-----------------------------------------------
$app->put('/catraca/atualiza/{id}', function (Request $request, Response $response, $args) {
	$id = $request->getAttribute('id');

	$dados = json_decode($request->getBody());

	$sql = "UPDATE catraca SET catr_ip = :ip, catr_tempo_giro = :tempo, catr_operacao = :operacao, catr_nome = :nome, catr_mac_lan = :maclan, catr_mac_wlan = :macwlan,
		catr_interface_rede = :interface, catr_financeiro = :financeiro WHERE catr_id = :id;";
	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":ip",$dados->catr_ip, PDO::PARAM_STR);
		$stmt->bindParam(":tempo",$dados->catr_tempo_giro, PDO::PARAM_INT);
		$stmt->bindParam(":operacao",$dados->catr_operacao, PDO::PARAM_INT);
		$stmt->bindParam(":nome",$dados->catr_nome, PDO::PARAM_STR);
		$stmt->bindParam(":maclan",$dados->catr_mac_lan, PDO::PARAM_STR);
		$stmt->bindParam(":macwlan",$dados->catr_mac_wlan, PDO::PARAM_STR);
		$stmt->bindParam(":interface",$dados->catr_interface_rede, PDO::PARAM_STR);
		$stmt->bindParam(":financeiro",$dados->catr_financeiro, PDO::PARAM_BOOL);
		$stmt->bindParam(":id",$id, PDO::PARAM_INT);
		$status = $stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"status":' . json_encode($status) . '}');
});

$app->put('/vinculo/atualiza/{id}', function (Request $request, Response $response, $args) {
	$id = $request->getAttribute('id');

	$dados = json_decode($request->getBody());

	$sql = "UPDATE vinculo SET vinc_avulso=:avulso, vinc_inicio=:inicio, vinc_fim=:fim, vinc_descricao=:descricao, vinc_refeicoes=:refeicoes,
		cart_id=:cartao, usua_id=:usuario WHERE vinc_id=:id;";
	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":avulso",$dados->vinc_avulso, PDO::PARAM_BOOL);
		$stmt->bindParam(":inicio",$dados->vinc_inicio, PDO::PARAM_STR);
		$stmt->bindParam(":fim",$dados->vinc_fim, PDO::PARAM_STR);
		$stmt->bindParam(":descricao",$dados->vinc_descricao, PDO::PARAM_STR);
		$stmt->bindParam(":refeicoes",$dados->vinc_refeicoes, PDO::PARAM_INT);
		$stmt->bindParam(":cartao",$dados->cart_id, PDO::PARAM_INT);
		$stmt->bindParam(":usuario",$dados->usua_id, PDO::PARAM_INT);
		$stmt->bindParam(":id",$id, PDO::PARAM_INT);
		$status = $stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"status":' . json_encode($status) . '}');
});

$app->put('/cartao/atualiza/{id}', function (Request $request, Response $response, $args) {
	$id = $request->getAttribute('id');

	$dados = json_decode($request->getBody());

	$sql = "UPDATE cartao SET cart_numero=:numero, cart_creditos=:creditos, tipo_id=:tipo WHERE cart_id=:id;";
	try {
		$db = $this->db_homologacao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":numero",$dados->cart_numero, PDO::PARAM_STR);
		$stmt->bindParam(":creditos",$dados->cart_creditos, PDO::PARAM_STR);
		$stmt->bindParam(":tipo",$dados->tipo_id, PDO::PARAM_INT);
		$stmt->bindParam(":id",$id, PDO::PARAM_INT);
		$status = $stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"status":' . json_encode($status) . '}');
});

$app->put('/cartao/atualiza/debito/{id}', function (Request $request, Response $response, $args) {
	$id = $request->getAttribute('id');

	$dados = json_decode($request->getBody());

	$sql = "UPDATE cartao SET cart_creditos = (cart_creditos - (SELECT tipo.tipo_valor FROM tipo WHERE tipo.tipo_id = cartao.tipo_id)) WHERE cart_id = :id;";
	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":id",$id, PDO::PARAM_INT);
		$status = $stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"status":' . json_encode($status) . '}');
});

$app->put('/cartao/atualiza/credito/{id}', function (Request $request, Response $response, $args) {
	$id = $request->getAttribute('id');

	$dados = json_decode($request->getBody());

	$sql = "UPDATE cartao SET cart_creditos = (cart_creditos + (SELECT tipo.tipo_valor FROM tipo WHERE tipo.tipo_id = cartao.tipo_id)) WHERE cart_id = :id;";
	try {
		$db = $this->db_producao;
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":id",$id, PDO::PARAM_INT);
		$status = $stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"mensagem":'. json_encode($e->getMessage()) .'}]}');
	}
	return $response->write('{"status":' . json_encode($status) . '}');
});

$app->put('/app/atualiza/{uid}', function (Request $request, Response $response, $args) {
	$uid= $request->getAttribute('uid');
	$dados = json_decode($request->getBody());
	
	$sql = "UPDATE app SET app_token=:token WHERE id_base_externa=:uid;";
	
	$db = $this->db_producao;
	try {
		$db->beginTransaction();
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":token",$dados->app_token, PDO::PARAM_STR);
		$stmt->bindParam(":uid",$uid, PDO::PARAM_INT);
		$status = $stmt->execute();
		$db->commit();

	} catch(Exception $e){
		$db->rollback();
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"app":'. json_encode($e->getMessage()) .'}]}');
	} finally {
		$db = null;
	}
	return $response->write('{"status":' . json_encode($status) . '}');
});

//----------------------------------DELETE--------------------------------------------
//----------------------------------DELETE--------------------------------------------

//------------------------------EMAIL NOREPLAY----------------------------------------
$app->post('/app/email', function (Request $request, Response $response, $args) {
	$email = json_decode($request->getBody());
	try {

		$de =  extrairNomeEmail($email->de)['email'];
		$nomeDe = extrairNomeEmail($email->de)['nome'];
		$para = extrairNomeEmail($email->para)['email'];
		$nomePara = extrairNomeEmail($email->para)['nome'];
		$cc = extrairNomeEmail($email->de)['email'];
		$nomeCc = extrairNomeEmail($email->de)['nome'];
		$assunto = $email->assunto;
		$corpo = $email->corpo;
		
		$mail = new PHPMailer();
		$mail->CharSet = 'UTF-8';
		$mail->IsSMTP();
		$mail->Host = "smtp.noreply.unilab.edu.br";
		$mail->Port = "25";
		$mail->SMTPAuth = false;
		//$mail->Username = "usuario";
		//$mail->Password = "senha";
		$mail->From = "catraca@noreply.unilab.edu.br";
		$mail->AddReplyTo($de, $nomeDe);
		$mail->FromName = "App Catraca Unilab";
		$mail->AddAddress($de, $nomeDe);
		$mail->AddAddress($para, $nomePara);
		//$mail->AddAttachment("arquivo.zip");
		$mail->IsHTML(false);
		$mail->Subject = $assunto;
		$mail->Body = $corpo;
		
		if ($de != null && $para != null && $corpo != null)
			$status = $mail->Send();
		else 
			$status = false;
				
	} catch(Exception $e) {
		$this->logger->err($e->getMessage());
		return $response->write('{"erro":[{"email":'. json_encode($e->getMessage()) .'}]}');
	}
	
	return $response->write('{"status":' . json_encode($status) . '}');
});

function extrairNomeEmail($enderecoEmail){
	$v1 = explode("<", $enderecoEmail);
	$v2['nome'] = $v1[0];
	$v1 = explode(">", $v1[1]);
	$v2['email'] = $v1[0];
	
	return $v2;
}
//------------------------------EMAIL NOREPLAY----------------------------------------

$app->run();
