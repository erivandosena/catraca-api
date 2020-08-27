<?php

define ( "CONFIG_CATRACA", "/dados/config/catraca.ini" );
$config = parse_ini_file ( CONFIG_CATRACA );
define ( "CADASTRO_DE_FOTOS", $config ['cadastro_de_fotos'] );
define ( "NOME_INSTITUICAO", $config ['nome_instituicao'] );
define ( "PAGINA_INSTITUICAO", $config ['pagina_instituicao'] );
define ( "LOGIN_LDAP", $config ['login_ldap'] );
define ( "FONT_DADOS_LDAP_ENTIDADE", $config ['font_dados_ldap_entidade'] );
define ( "VERSAO_SINCRONIZADOR", $config ['versao_sincronizador'] );
define("PARAMETROS_LDAP_BASE_LOCAL", $config['parametros_ldap_base_local']);
define("BARRA_GOVERNO_FEDERAL", $config['barra_governo_federal']);


ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

function __autoload($classe) {
	if (file_exists ( 'classes/dao/' . $classe . '.php' )){
		include_once 'classes/dao/' . $classe . '.php';
	}
	else if (file_exists ( 'classes/model/' . $classe . '.php' )){
		include_once 'classes/model/' . $classe . '.php';
	}
	else if (file_exists ( 'classes/controller/' . $classe . '.php' )){
		include_once 'classes/controller/' . $classe . '.php';
	}
	else if (file_exists ( 'classes/util/' . $classe . '.php' )){
		include_once 'classes/util/' . $classe . '.php';
	}
	else if (file_exists ( 'classes/view/' . $classe . '.php' )){
		include_once 'classes/view/' . $classe . '.php';
	}
}



$dao = new DAO();

$sql = "SELECT * FROM usuario INNER JOIN vinculo ON usuario.usua_id = vinculo.usua_id 
		INNER JOIN cartao ON cartao.cart_id = vinculo.cart_id WHERE cart_numero = '3995498478'";



$result = $dao->getConexao()->query($sql);
$idFabrine = 0;
foreach($result as $linha){
	$idFabrine = $linha['usua_id'];
	echo $linha['usua_id'].'  - '.$linha['usua_nome'];
}

$sqlTransacao = "SELECT * FROM transacao WHERE usua_id1 = $idFabrine";
echo "<br><br>";
foreach ($dao->getConexao()->query($sqlTransacao) as $linha)
{
	
	echo $linha['tran_id'].' - '.$linha['tran_valor'];
	echo "<br>";	
}

