<?php

class GuicheDAO{
	
	public function retornaListaDescridao(){
		
		$dataInicio = date('Y-m-d');
		$dataFim = date('Y-m-d');
		
		$data1 = $dataInicio.' 00:00:00';
		$data2 = $dataFim.' 23:59:59';
		
		$sqlTransacao = "SELECT * FROM transacao as trans
		LEFT JOIN usuario as usuario
		on trans.usua_id = usuario.usua_id
		WHERE (tran_data BETWEEN '$data1' AND '$data2') AND usuario.usua_id = $idDoUsuario";
		
		
	}
	
	
}