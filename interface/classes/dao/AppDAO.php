<?php

/**
 * Realiza consultas no banco de dados referente app. 
 *
 * Script de pesistencia da classe app.
 * 
 * PHP versao 5
 *
 * LICENCA: Este arquivo fonte esta sujeito a versao 3.01 da licenï¿½a PHP
 * que esta disponivel atraves da world-wide-web na seguinte URI:
 * Http://www.php.net/license/3_01.txt. Se vocï¿½ nï¿½o recebeu uma copia da
 * Licenca PHP e nao consegue obte-la atraves da web, por favor, envie uma
 * nota para license@php.net para que possamos enviar-lhe uma copia imediatamente.
 *
 * @category   Aplicacao Web
 * @package    dao
 * @author     Erivando Sena <erivandoramos@unilab.edu.br> 
 * @copyright  2010-2017 UNILAB - Universidade da Integracao Internacional da Lusofonia Afro-Brasileira.
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 *
 */


/**
 * Classe DAO da app
 *
 */


class AppDAO extends DAO{

	/**
	 * Retorna app por id do usuario. 
	 * @return App
	 */
	public function retornaAppPorUsuarioId($idUsuario){
		$app = new App();
		$sql = "SELECT app_token FROM app WHERE usua_id = $idUsuario";
		$result = $this->getConexao()->query($sql);
		foreach ($result as $linha){
			$app->setApp_token($linha['app_token']);
		}
		/* Valida token unico por usuario */
		if($this->verificaTokenUnico($app->getApp_token()) == 1) {
			return $app;
		}
		return null;
	}
	
	/**
	 * Retorna token em duplicidade.
	 * @param string $token
	 * @return number
	 */
	public function verificaTokenUnico($token) {
		$sql = "SELECT app_token FROM app WHERE app_token = '$token'";
		$result = $this->getConexao()->query($sql);
		$i = 0;
		foreach ($result as $linha){
			$i++;
		}
		return $i;
	}
	
}


?>