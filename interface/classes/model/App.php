<?php

/**
 * Entidade app.
 *
 * Script da classe de entidade para o objeto App.
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
 * @package    model
 * @author     Erivando Sena <erivandoramos@unilab.edu.br>
 * @copyright  2010-2017 UNILAB - Universidade da Integracao Internacional da Lusofonia Afro-Brasileira.
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 *
 */

// namespace App;

/**
 * Classe app para instancia do objeto App
 *
 */
class App{
		private $app_id;
		private $app_token;
		private $usua_id;
		private $id_base_externa;
		
		public function setApp_id($app_id){
			$this->app_id = intval($app_id);
		}
		public function getApp_id(){
			return $this->app_id;
		}
		public function setApp_token($app_token){
			$this->app_token = $app_token;
		}
		public function getApp_token(){
			return $this->app_token;
		}
		public function setUsua_id($usua_id){
			$this->usua_id = $usua_id;
		}
		public function getUsua_id(){
			return  $this->usua_id;
			
		}
		public function setId_base_externa($id_base_externa){
			$this->id_base_externa = $id_base_externa;
		}
		public function getId_base_externa(){
			return $this->id_base_externa;
		}
		
	}


?>