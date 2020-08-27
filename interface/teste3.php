<?php

define ( "CONFIG_CATRACA", "/dados/sites/adm/catraca/config/catraca.ini" );
$config = parse_ini_file ( CONFIG_CATRACA );
define ( "CADASTRO_DE_FOTOS", $config ['cadastro_de_fotos'] );
define ( "NOME_INSTITUICAO", $config ['nome_instituicao'] );
define ( "PAGINA_INSTITUICAO", $config ['pagina_instituicao'] );
define ( "LOGIN_LDAP", $config ['login_ldap'] );
define ( "FONT_DADOS_LDAP_ENTIDADE", $config ['font_dados_ldap_entidade'] );
define ( "VERSAO_SINCRONIZADOR", $config ['versao_sincronizador'] );



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


ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);


$dao = new DAO();

$sql = file_get_contents("mudanca.sql");

$arr = explode(';', $sql);
foreach($arr as $sql){
    echo $sql;
    $dao->getConexao()->query($sql);
}

