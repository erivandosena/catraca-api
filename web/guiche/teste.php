<?php

ini_set ( 'display_errors', 1 );
ini_set ( 'display_startup_erros', 1 );
error_reporting ( E_ALL );

date_default_timezone_set ( 'America/Araguaina' );

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



$dao = new DAO();

// $dataAtual = date("Y-m-d G:i:s");
//$sql = "UPDATE guiche SET guic_encerramento = '2016-05-03 17:37:55', guic_ativo = '0' WHERE guic_id = 64";
//$sql = "UPDATE usuario set usua_nivel = 3 WHERE usua_login = 'jefponte';";
// $sql = "INSERT INTO guiche (guic_abertura, guic_ativo, unid_id, usua_id)
// 		VALUES('2016-05-03 10:32:47', '1', 1, 3)";
// $sql = "DELETE FROM catraca WHERE catr_id > 60";
// echo $dao->getConexao()->exec($sql);

// $sql = "DELETE FROM vinculo_tipo";
// $sql = "DELETE FROM vinculo WHERE vinc_avulso <> 'TRUE'";
//$sql = "INSERT INTO vinculo_tipo (vinc_id, tipo_id) VALUES(8, 13)";
// $sql = "DELETE FROM catraca WHERE catr_id > 5";

// $sql = "UPDATE turno set turn_hora_fim = '13:30:00'
// 		WHERE turn_id = 1";

// $i = $result = $dao->getConexao()->exec($sql);
// echo $i;

$sql = "DELETE FROM catraca WHERE catr_id > 5";
echo $dao->getConexao()->exec($sql);

?>
