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

$LOCAL = '/dados/sites/adm/catraca/webservice';

require $LOCAL . '/v1/db.php';
require $LOCAL . '/v1/db_externo.php';
require '../Slim/Slim/Slim.php';
require '../Slim/Slim/Middleware.php';
require '../Slim/Slim/Middleware/HttpBasicAuth.php';
require '../Slim/Slim/Log/DateTimeFileWriter.php';
require ('../Slim/Slim/Middleware/CorsSlim.php');
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
	'debug' => true,
	'log.enabled' => true,
	'log.level' => \Slim\Log::WARN,
    'log.writer' => new \Slim\Log\DateTimeFileWriter(array(
		'path' => '/home/erivando/log/api',
		'name_format' => 'd-m-Y',
		'message_format' => '%label% - %date% - %message%'
    ))
));

// GET route
$app->get(
		'/',
		function () {
			$template = <<<EOT
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8"/>
            <title>Slim Framework for PHP 5</title>
            <style>
                html,body,div,span,object,iframe,
                h1,h2,h3,h4,h5,h6,p,blockquote,pre,
                abbr,address,cite,code,
                del,dfn,em,img,ins,kbd,q,samp,
                small,strong,sub,sup,var,
                b,i,
                dl,dt,dd,ol,ul,li,
                fieldset,form,label,legend,
                table,caption,tbody,tfoot,thead,tr,th,td,
                article,aside,canvas,details,figcaption,figure,
                footer,header,hgroup,menu,nav,section,summary,
                time,mark,audio,video{margin:0;padding:0;border:0;outline:0;font-size:100%;vertical-align:baseline;background:transparent;}
                body{line-height:1;}
                article,aside,details,figcaption,figure,
                footer,header,hgroup,menu,nav,section{display:block;}
                nav ul{list-style:none;}
                blockquote,q{quotes:none;}
                blockquote:before,blockquote:after,
                q:before,q:after{content:'';content:none;}
                a{margin:0;padding:0;font-size:100%;vertical-align:baseline;background:transparent;}
                ins{background-color:#ff9;color:#000;text-decoration:none;}
                mark{background-color:#ff9;color:#000;font-style:italic;font-weight:bold;}
                del{text-decoration:line-through;}
                abbr[title],dfn[title]{border-bottom:1px dotted;cursor:help;}
                table{border-collapse:collapse;border-spacing:0;}
                hr{display:block;height:1px;border:0;border-top:1px solid #cccccc;margin:1em 0;padding:0;}
                input,select{vertical-align:middle;}
                html{ background: #EDEDED; height: 100%; }
                body{background:#FFF;margin:0 auto;min-height:100%;padding:0 30px;width:440px;color:#666;font:14px/23px Arial,Verdana,sans-serif;}
                h1,h2,h3,p,ul,ol,form,section{margin:0 0 20px 0;}
                h1{color:#333;font-size:20px;}
                h2,h3{color:#333;font-size:14px;}
                h3{margin:0;font-size:12px;font-weight:bold;}
                ul,ol{list-style-position:inside;color:#999;}
                ul{list-style-type:square;}
                code,kbd{background:#EEE;border:1px solid #DDD;border:1px solid #DDD;border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px;padding:0 4px;color:#666;font-size:12px;}
                pre{background:#EEE;border:1px solid #DDD;border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px;padding:5px 10px;color:#666;font-size:12px;}
                pre code{background:transparent;border:none;padding:0;}
                a{color:#70a23e;}
                header{padding: 30px 0;text-align:center;}
            </style>
        </head>
        <body>
            <header>
                <a href="http://www.slimframework.com"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHIAAAA6CAYAAABs1g18AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABRhJREFUeNrsXY+VsjAMR98twAo6Ao4gI+gIOIKOgCPICDoCjCAjXFdgha+5C3dcv/QfFB5i8h5PD21Bfk3yS9L2VpGnlGW5kS9wJMTHNRxpmjYRy6SycgRvL18OeMQOTYQ8HvIoJKiiz43hgHkq1zvK/h6e/TyJQXeV/VyWBOSHA4C5RvtMAiCc4ZB9FPjgRI8+YuKcrySO515a1hoAY3nc4G2AH52BZsn+MjaAEwIJICKAIR889HljMCcyrR0QE4v/q/BVBQva7Q1tAczG18+x+PvIswHEAslLbfGrMZKiXEOMAMy6LwlisQCJLPFMfKdBtli5dIihRyH7A627Iaiq5sJ1ThP9xoIgSdWSNVIHYmrTQgOgRyRNqm/M5PnrFFopr3F6B41cd8whRUSufUBU5EL4U93AYRnIWimCIiSI1wAaAZpJ9bPnxx8eyI3Gt4QybwWa6T/BvbQECUMQFkhd3jSkPFgrxwcynuBaNT/u6eJIlbGOBWSNIUDFEIwPZFAtBfYrfeIOSRSXuUYCsprCXwUIZWYnmEhJFMIocMDWjn206c2EsGLCJd42aWSyBNMnHxLEq7niMrY2qyDbQUbqrrTbwUPtxN1ZZCitQV4ZSd6DyoxhmRD6OFjuRUS/KdLGRHYowJZaqYgjt9Lchmi3QYA/cXBsHK6VfWNR5jgA1DLhwfFe4HqfODBpINEECCLO47LT/+HSvSd/OCOgQ8qE0DbHQUBqpC4BkKMPYPkFY4iAJXhGAYr1qmaqQDbECCg5A2NMchzR567aA4xcRKclI405Bmt46vYD7/Gcjqfk6GP/kh1wovIDSHDfiAs/8bOCQ4cf4qMt7eH5Cucr3S0aWGFfjdLHD8EhCFvXQlSqRrY5UV2O9cfZtk77jUFMXeqzCEZqSK4ICkSin2tE12/3rbVcE41OBjBjBPSdJ1N5lfYQpIuhr8axnyIy5KvXmkYnw8VbcwtTNj7fDNCmT2kPQXA+bxpEXkB21HlnSQq0gD67jnfh5KavVJa/XQYEFSaagWwbgjNA+ywstLpEWTKgc5gwVpsyO1bTII+tA6B7BPS+0PiznuM9gPKsPVXbFdADMtwbJxSmkXWfRh6AZhyyzBjIHoDmnCGaMZAKjd5hyNJYCBGDOVcg28AXQ5atAVDO3c4dSALQnYblfa3M4kc/cyA7gMIUBQCTyl4kugIpy8yA7ACqK8Uwk30lIFGOEV3rPDAELwQkr/9YjkaCPDQhCcsrAYlF1v8W8jAEYeQDY7qn6tNGWudfq+YUEr6uq6FZzBpJMUfWFDatLHMCciw2mRC+k81qCCA1DzK4aUVfrJpxnloZWCPVnOgYy8L3GvKjE96HpweQoy7iwVQclVutLOEKJxA8gaRCjSzgNI2zhh3bQhzBCQQPIHGaHaUd96GJbZz3Smmjy16u6j3FuKyNxcBarxqWWfYFE0tVVO1Rl3t1Mb05V00MQCJ71YHpNaMcsjWAfkQvPPkaNC7LqTG7JAhGXTKYf+VDeXAX9IvURoAwtTFHvyYIxtnd5tPkywrPafcwbeSuGVwFau3b76NO7SHQrvqhfFE8kM0Wvpv8gVYiYBlxL+fW/34bgP6bIC7JR7YPDubcHCPzIp4+cum7U6NlhZgK7lua3KGLeFwE2m+HblDYWSHG2SAfINuwBBfxbJEIuWZbBH4fAExD7cvaGVyXyH0dhiAYc92z3ZDfUVv+jgb8HrHy7WVO/8BFcy9vuTz+nwADAGnOR39Yg/QkAAAAAElFTkSuQmCC" alt="Slim"/></a>
            </header>
            <h1>Welcome to Slim!</h1>
            <p>
                Congratulations! Your Slim application is running. If this is
                your first time using Slim, start with this <a href="http://docs.slimframework.com/#Hello-World" target="_blank">"Hello World" Tutorial</a>.
            </p>
            <section>
                <h2>Get Started</h2>
                <ol>
                    <li>The application code is in <code>index.php</code></li>
                    <li>Read the <a href="http://docs.slimframework.com/" target="_blank">online documentation</a></li>
                    <li>Follow <a href="http://www.twitter.com/slimphp" target="_blank">@slimphp</a> on Twitter</li>
                </ol>
            </section>
            <section>
                <h2>Slim Framework Community</h2>

                <h3>Support Forum and Knowledge Base</h3>
                <p>
                    Visit the <a href="http://help.slimframework.com" target="_blank">Slim support forum and knowledge base</a>
                    to read announcements, chat with fellow Slim users, ask questions, help others, or show off your cool
                    Slim Framework apps.
                </p>

                <h3>Twitter</h3>
                <p>
                    Follow <a href="http://www.twitter.com/slimphp" target="_blank">@slimphp</a> on Twitter to receive the very latest news
                    and updates about the framework.
                </p>
            </section>
            <section style="padding-bottom: 20px">
                <h2>Slim Framework Extras</h2>
                <p>
                    Custom View classes for Smarty, Twig, Mustache, and other template
                    frameworks are available online in a separate repository.
                </p>
                <p><a href="https://github.com/codeguy/Slim-Extras" target="_blank">Browse the Extras Repository</a></p>
            </section>
        </body>
    </html>
EOT;
			echo $template;
		}
);

$app->contentType('text/html; charset=utf-8');

$app->add(new \HttpBasicAuth());
$app->add(new \CorsSlim\CorsSlim());
$corsOptions = array(
    "origin" => "*",
    "exposeHeaders" => array("Content-Type", "X-Requested-With", "X-authentication", "X-client"),
    "allowMethods" => array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS')
);
$cors = new \CorsSlim\CorsSlim($corsOptions);
$app->add($cors);

//----------------------------------GET-----------------------------------------------
$app->get('/tipo/jtipo','obterTipo');
$app->get('/turno/jturno','obterTurno');
$app->get('/turno/jturno/(:ip)/(:hora)','obterTurnoFuncionamento');
$app->get('/unidade/junidade','obterUnidade');
$app->get('/custo_refeicao/jcusto_refeicao','obterCustoRefeicao');
$app->get('/custo_unidade/jcusto_unidade','obterCustoUnidade');
$app->get('/usuario/jusuario','obterUsuario');
$app->get('/usuario/jusuario/(:id_externo)','obterUsuarioBaseExterna');
$app->get('/usuario/jusuarios','obterUsuariosBaseExterna');
$app->get('/catraca/jcatraca','obterCatraca');
$app->get('/mensagem/jmensagem/(:catraca)','obterMensagemCatraca');
$app->get('/cartao/jcartao','obterCartao');
//$app->get('/cartao/jcartao/(:numero)/(:datahora)','obterCartaoValido');
$app->get('/cartao/jcartao/(:numero)','obterCartaoValido');
$app->get('/vinculo/jvinculo','obterVinculo');
$app->get('/isencao/jisencao','obterIsencao');
$app->get('/isencao/jisencao/(:numero)/(:datahora)','obterIsencaoAtiva');
$app->get('/unidade_turno/junidade_turno','obterUnidadeTurno');
$app->get('/catraca_unidade/jcatraca_unidade','obterCatracaUnidade');
$app->get('/registro/jregistro/(:datahoraini)/(:datahorafim)','obterRegistro');
$app->get('/registro/jregistro/(:datahoraini)/(:datahorafim)/(:id)','obterRegistroUtilizacao');
//----------------------------------POST----------------------------------------------
$app->post('/registro/insere','inserirRegistro');
$app->post('/catraca/insere','inserirCatraca');
//----------------------------------PUT-----------------------------------------------
$app->put('/cartao/atualiza/:id','atualizaCartao');
$app->put('/catraca/atualiza/:id','atualizaCatraca');
//----------------------------------DELETE--------------------------------------------
//$app->response()->header("Content-Type", "application/json");
$app->run();

//----------------------------------GET-----------------------------------------------
function obterTipo() {
	
	$sql = "SELECT tipo_id, tipo_nome, tipo_valor FROM tipo ORDER BY tipo_id;";
	
	try {
		$db = getDB();
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"tipos": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

function obterTurno() {
	
	$sql = "SELECT turn_id, turn_hora_inicio, turn_hora_fim, turn_descricao FROM turno ORDER BY turn_id;";
	
	try {
		$db = getDB();
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"turnos": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

function obterTurnoFuncionamento($ip, $hora) {
	
	$sql = "SELECT distinct turno.turn_id, turno.turn_hora_inicio, turno.turn_hora_fim, turno.turn_descricao FROM turno 
			INNER JOIN unidade_turno ON turno.turn_id = unidade_turno.turn_id 
			INNER JOIN catraca_unidade ON unidade_turno.unid_id = catraca_unidade.unid_id 
			INNER JOIN catraca ON catraca_unidade.catr_id = catraca.catr_id WHERE catraca.catr_ip = :ip 
			AND turno.turn_hora_inicio <= :hora_ini 
			AND turno.turn_hora_fim >= :hora_fim;";
	
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":ip", sprintf(long2ip($ip)), PDO::PARAM_STR);
		$stmt->bindParam(":hora_ini", $hora, PDO::PARAM_STR);
		$stmt->bindParam(":hora_fim", $hora, PDO::PARAM_STR);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"turno": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

function obterUnidade() {
	$sql = "SELECT unid_id, unid_nome FROM unidade ORDER BY unid_id;";
	
	try {
		$db = getDB();
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"unidades": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

function obterCustoRefeicao() {
	
	$sql = "SELECT cure_id, cure_valor, cure_data FROM custo_refeicao;";
	
	try {
		$db = getDB();
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"custo_refeicao": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

function obterCustoUnidade() {
	
	$sql = "SELECT cuun_id, unid_id, cure_id FROM custo_unidade;";
	
	try {
		$db = getDB();
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"custo_unidade": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

function obterUsuario() {
	$dataTimeAtual = date ("Y-m-d G:i:s");
	
	$sql = "SELECT usua_id, usua_nome, usua_email, usua_login, usua_senha, usua_nivel, id_base_externa 
	FROM usuario ORDER BY usua_id;";
	
	try {
		$db = getDB();
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"usuarios": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

function obterUsuarioBaseExterna($id_externo) {
	
	$sql = "SELECT categoria, cpf_cnpj, email, id_categoria, id_usuario, identidade, login, matricula_disc, nivel_discente, nome, passaporte, siape, status_discente, status_servidor, tipo_usuario FROM vw_usuarios_catraca WHERE id_usuario = :id ORDER BY status_discente, status_servidor ASC LIMIT 1;";

	try {
		$db = getDBExterno();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id",$id_externo);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"status_usuario": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

function obterUsuariosBaseExterna() {
	
// 	$sql = "SELECT categoria, cpf_cnpj, id_categoria, id_usuario, identidade, login, matricula_disc, nivel_discente, status_discente, status_servidor, tipo_usuario FROM vw_usuarios_catraca ORDER BY tipo_usuario, status_discente, status_servidor ASC LIMIT 3000;";
	$sql = "SELECT DISTINCT tipo_usuario FROM vw_usuarios_catraca ORDER BY tipo_usuario;";

	try {
		$db = getDBExterno();
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"status_usuarios": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

function obterCatraca() {
	
	$sql = "SELECT catr_id, catr_ip, catr_tempo_giro, catr_operacao, catr_nome, catr_mac_lan, catr_mac_wlan, catr_interface_rede, catr_financeiro FROM catraca ORDER BY catr_id;";
	
	try {
		$db = getDB();
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"catracas": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

function obterMensagemCatraca($id_catraca) {
	
	$sql = "SELECT mens_id, mens_institucional1, mens_institucional2, 
       	mens_institucional3, mens_institucional4, catr_id 
  		FROM mensagem WHERE catr_id = :catraca ORDER BY mens_id;";
	
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":catraca", $id_catraca, PDO::PARAM_STR);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"mensagens": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

function obterCartao() {
	$dataTimeAtual = date ("Y-m-d G:i:s");
	
	$sql = "SELECT cart_id, cart_numero, cart_creditos, tipo_id FROM cartao ORDER BY cart_id;";
	
	try {
		$db = getDB();
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"cartoes": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

/*
function obterCartaoValido($numero, $datahora) {
	$data_hora_atual = date("Y-m-d G:i:s", strtotime($datahora));
	
	$sql = "SELECT cartao.cart_id, cartao.cart_numero, cartao.cart_creditos, 
	tipo.tipo_valor, vinculo.vinc_refeicoes, tipo.tipo_id, vinculo.vinc_id, 
	vinculo.vinc_descricao, TRIM(both ' ' from SUBSTR(usuario.usua_nome, 0, 16)) || '.' as usua_nome FROM cartao 
	INNER JOIN tipo ON cartao.tipo_id = tipo.tipo_id 
	INNER JOIN vinculo ON vinculo.cart_id = cartao.cart_id 
	INNER JOIN usuario ON usuario.usua_id = vinculo.usua_id 
	WHERE (:datahora BETWEEN vinculo.vinc_inicio AND vinculo.vinc_fim) AND 
	(cartao.cart_numero = :numero);";
	
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":datahora", $data_hora_atual, PDO::PARAM_STR);
		$stmt->bindParam(":numero", $numero, PDO::PARAM_STR);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"cartao": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}
*/

function obterCartaoValido($numero) {
	
	$sql = "SELECT cartao.cart_id, cartao.cart_numero, cartao.cart_creditos, 
	tipo.tipo_valor, vinculo.vinc_refeicoes, tipo.tipo_id, vinculo.vinc_id, usuario.id_base_externa, tipo.tipo_nome, 
	vinculo.vinc_descricao, vinculo.vinc_inicio, vinculo.vinc_fim, TRIM(both ' ' from SUBSTR(usuario.usua_nome, 0, 16)) || '.' as usua_nome FROM cartao 
	INNER JOIN tipo ON cartao.tipo_id = tipo.tipo_id 
	INNER JOIN vinculo ON vinculo.cart_id = cartao.cart_id 
	INNER JOIN usuario ON usuario.usua_id = vinculo.usua_id 
	WHERE cartao.cart_numero = :numero;";
	
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":numero", $numero, PDO::PARAM_STR);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"cartao_valido": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

function obterVinculo() {
	$dataTimeAtual = date ("Y-m-d G:i:s");
	
	$sql = "SELECT vinc_id, vinc_avulso, vinc_inicio, vinc_fim, vinc_descricao, vinc_refeicoes, cart_id, usua_id FROM vinculo ORDER BY vinc_id;";
	
	try {
		$db = getDB();
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"vinculos": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

function obterIsencao() {
	$dataTimeAtual = date ("Y-m-d G:i:s");
	
	$sql = "SELECT isen_id, isen_inicio, isen_fim, cart_id FROM isencao ORDER BY isen_id;";
	
	try {
		$db = getDB();
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"isencoes": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

function obterIsencaoAtiva($numero, $datahora) {
	$data_hora_atual = date("Y-m-d G:i:s", strtotime($datahora));
	
	$sql = "SELECT isencao.isen_inicio, isencao.isen_fim, cartao.cart_id FROM cartao 
	INNER JOIN isencao ON isencao.cart_id = cartao.cart_id WHERE cartao.cart_numero = :numero 
	AND (:datahora BETWEEN isencao.isen_inicio AND isencao.isen_fim);";
	
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":datahora", $data_hora_atual, PDO::PARAM_STR);
		$stmt->bindParam(":numero", $numero, PDO::PARAM_STR);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"isencao": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}


function obterUnidadeTurno() {
	$sql = "SELECT untu_id, turn_id, unid_id FROM unidade_turno ORDER BY untu_id;";
	
	try {
		$db = getDB();
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"unidade_turnos": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

function obterCatracaUnidade() {
	$sql = "SELECT caun_id, catr_id, unid_id FROM catraca_unidade ORDER BY caun_id;";
	
	try {
		$db = getDB();
		$stmt = $db->query($sql);
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"catraca_unidades": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

function obterRegistro($datahoraini, $datahorafim) {
	$data_hora_ini = date("Y-m-d G:i:s", strtotime($datahoraini));
	$data_hora_fim = date("Y-m-d G:i:s", strtotime($datahorafim));
	
	$sql = "SELECT regi_id, regi_data, regi_valor_pago, regi_valor_custo, cart_id, catr_id, vinc_id 
	FROM registro WHERE regi_data BETWEEN :datahora_ini AND :datahora_fim ORDER BY regi_id;";
	
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":datahora_ini", $data_hora_ini, PDO::PARAM_STR);
		$stmt->bindParam(":datahora_fim", $data_hora_fim, PDO::PARAM_STR);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"registros": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

function obterRegistroUtilizacao($datahoraini, $datahorafim, $id) {
	$data_hora_ini = date("Y-m-d G:i:s", strtotime($datahoraini));
	$data_hora_fim = date("Y-m-d G:i:s", strtotime($datahorafim));
	
	$sql = "SELECT COUNT(regi_id) as total FROM registro WHERE 
	(regi_data BETWEEN :datahora_ini AND :datahora_fim) AND (cart_id = :id);";
	
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":datahora_ini", $data_hora_ini, PDO::PARAM_STR);
		$stmt->bindParam(":datahora_fim", $data_hora_fim, PDO::PARAM_STR);
		$stmt->bindParam(":id", $id, PDO::PARAM_STR);
		$stmt->execute();
		$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"quantidade": ' . json_encode($dados) . '}';
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

//----------------------------------POST----------------------------------------------

function inserirRegistro() {
	$request = \Slim\Slim::getInstance()->request();
	$body = $request->getBody();
	$dados = json_decode($body);
	
	$sql = "INSERT INTO registro(regi_data, regi_valor_pago, regi_valor_custo, cart_id, catr_id, vinc_id) 
	VALUES (:data, :pago, :custo, :cartao, :catraca, :vinculo);";
	
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("data", $dados->regi_data);
		$stmt->bindParam("pago", $dados->regi_valor_pago);
		$stmt->bindParam("custo", $dados->regi_valor_custo);
		$stmt->bindParam("cartao", $dados->cart_id);
		$stmt->bindParam("catraca", $dados->catr_id);
		$stmt->bindParam("vinculo", $dados->vinc_id);
		$stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

function inserirCatraca() {
	$request = \Slim\Slim::getInstance()->request();
	$body = $request->getBody();
	$dados = json_decode($body);
	
	$sql = "INSERT INTO catraca(catr_ip, catr_tempo_giro, catr_operacao, catr_nome, catr_mac_lan, catr_mac_wlan, catr_interface_rede, catr_financeiro) VALUES (:ip, :tempo, :operacao, :nome, :maclan, :macwlan, :interface, :financeiro);";
	
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("ip", $dados->catr_ip);
		$stmt->bindParam("tempo", $dados->catr_tempo_giro);
		$stmt->bindParam("operacao", $dados->catr_operacao);
		$stmt->bindParam("nome", $dados->catr_nome);
		$stmt->bindParam("maclan",$dados->catr_mac_lan);
		$stmt->bindParam("macwlan",$dados->catr_mac_wlan);
		$stmt->bindParam("interface",$dados->catr_interface_rede);
		$stmt->bindParam("financeiro",$dados->catr_financeiro);
		$stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}


//----------------------------------PUT-----------------------------------------------

function atualizaCartao($id) {
	$request = \Slim\Slim::getInstance()->request();
	$dados = json_decode($request->getBody());
	
	$sql = "UPDATE cartao SET cart_numero=:numero, cart_creditos=:creditos, tipo_id=:tipo WHERE cart_id=:id;";
	
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id",$id);
		$stmt->bindParam("numero",$dados->cart_numero);
		$stmt->bindParam("creditos",$dados->cart_creditos);
		$stmt->bindParam("tipo",$dados->tipo_id);
		$stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

function atualizaCatraca($id) {
	$request = \Slim\Slim::getInstance()->request();
	$dados = json_decode($request->getBody());
	
	$sql = "UPDATE catraca SET catr_ip = :ip, catr_tempo_giro = :tempo, catr_operacao = :operacao, catr_nome = :nome, catr_mac_lan = :maclan, catr_mac_wlan = :macwlan, catr_interface_rede = :interface, catr_financeiro = :financeiro WHERE catr_id = :id;";
	
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id",$id);
		$stmt->bindParam("ip",$dados->catr_ip);
		$stmt->bindParam("tempo",$dados->catr_tempo_giro);
		$stmt->bindParam("operacao",$dados->catr_operacao);
		$stmt->bindParam("nome",$dados->catr_nome);
		$stmt->bindParam("maclan",$dados->catr_mac_lan);
		$stmt->bindParam("macwlan",$dados->catr_mac_wlan);
		$stmt->bindParam("interface",$dados->catr_interface_rede);
		$stmt->bindParam("financeiro",$dados->catr_financeiro);
		$stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		echo '{"erro":{"text":'. $e->getMessage() .'}}';
	}
}

?>