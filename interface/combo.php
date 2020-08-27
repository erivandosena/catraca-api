<?php

/**
 * Este arquivo é utilizado junto com o combo.js,
 * arquivo javascript que controa o combobox na tela de messagens,
 * gerando um efeito instatâneo sem mostrar o efeito de atualização
 * da página para o usuário. 
 * 
 * @link https://www.catraca.unilab.edu.br/docs/index.html
 */

date_default_timezone_set ( 'America/Araguaina' );

/**
 * @ignore
 */

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

/*
 * Combo dinamico do relatório.
 */
if (isset($_GET['unidade'])){
	$dao = new DAO();
	echo $idUnidade = $_GET['unidade'];
	$sql = "SELECT * FROM catraca
	INNER JOIN catraca_unidade ON catraca_unidade.catr_id = catraca.catr_id
	INNER JOIN unidade ON unidade.unid_id = catraca_unidade.unid_id
	WHERE unidade.unid_id = $idUnidade";
	$result = $dao->getConexao()->query($sql);
	$option = '<option value="">Selecione uma Catraca</option>';
	foreach ($result as $linha){
		$option .= '<option value="'.$linha['catr_id'].'">'.$linha['catr_nome'].'</option>';
	}	
	print $option;
	return ;
}

if(isset($_GET['campo'])){	
	$dao = new DAO();
	$campo = $_GET['campo'];	
	$sqlValores = "	SELECT $campo
					FROM vw_usuarios_catraca
					GROUP BY $campo ORDER BY $campo ASC LIMIT 20";
	$result = $dao->getConexao()->query($sqlValores);
	$option = '<option value="">Selecione um Valor</option>';
	foreach ($result as $linha){
		if($linha[$campo]!=""){
			$option .= '<option value="'.$linha[$campo].'">'.$linha[$campo].'</option>';
		}		
	}
	print $option;
	return;
}

?>