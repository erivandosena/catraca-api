<?php


date_default_timezone_set ( 'America/Fortaleza' );

function __autoload($classe) {
	if (file_exists ( 'classes/dao/' . $classe . '.php' ))
		include_once 'classes/dao/' . $classe . '.php';
	if (file_exists ( 'classes/model/' . $classe . '.php' ))
		include_once 'classes/model/' . $classe . '.php';
	if (file_exists ( 'classes/controller/' . $classe . '.php' ))
		include_once 'classes/controller/' . $classe . '.php';
	if (file_exists ( 'classes/util/' . $classe . '.php' ))
		include_once 'classes/util/' . $classe . '.php';
	if (file_exists ( 'classes/view/' . $classe . '.php' ))
		include_once 'classes/view/' . $classe . '.php';
}

$sessao = new Sessao ();

if (isset ( $_GET ["sair"] )) {
	
	$sessao->mataSessao ();
	header ( "Location:./index.php" );
}

CatracaController::main($sessao->getNivelAcesso());




?>