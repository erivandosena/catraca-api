<?php

/**
 * Envio de Notiticacoes FireBase Server.
 *
 * Script de envio de notiticacoes via api firebase.
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
 * @package    util
 * @author     Erivando Sena <erivandoramos@unilab.edu.br>
 * @copyright  2010-2017 UNILAB - Universidade da Integracao Internacional da Lusofonia Afro-Brasileira.
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 *
 */

function __autoload($classe) {
	if (file_exists ( 'classes/dao/' . $classe . '.php' ))
		include_once 'classes/dao/' . $classe . '.php';
	if (file_exists ( 'classes/model/' . $classe . '.php' ))
		include_once 'classes/model/' . $classe . '.php';
	if (file_exists ( 'classes/util/' . $classe . '.php' ))
		include_once 'classes/util/' . $classe . '.php';
}

if ($argc < 2) {
	exit(1);
}
$arg1 = $argv[1];
$arg2 = $argv[2];
$arg3 = $argv[3];
$arg4 = $argv[4];

if (!empty($arg1)) {
	$notificacaoFcm = new NotificacaoFirebase($arg1);
	
	if (strcasecmp($arg2, "guiche") == 0)
		$notificacaoFcm->notificacaoGuiche($arg3, $arg4);
	
	if (strcasecmp($arg2, "catraca_virtual") == 0)
		$notificacaoFcm->notificacaoCatracaVirtual($arg3);
	
	if (strcasecmp($arg2, "catraca_virtual_utilizacao") == 0)
		$notificacaoFcm->notificacaoCatracaVirtualUtilizacao($arg3, $arg4);
}

/**
 * Classe de envio de mensagens para o aplicativo em background
 */
class NotificacaoFirebase {
	private $appDao;
	private $notificacaoApp;
	private $idUsuario;
	
	/**
	 * 
	 * @param integer $idUsuario
	 */
	public function NotificacaoFirebase($idUsuario){
		$this->appDao = new AppDAO();
		$this->notificacaoApp = new NotificacaoApp();
		$this->idUsuario = $idUsuario;
	}
	
	/**
	 * 
	 * @param float $valorVendido
	 * @param float $novoValor
	 */
	public function notificacaoGuiche($valorVendido, $novoValor){
		$app = $this->appDao->retornaAppPorUsuarioId($this->idUsuario);
		if($app != null){
			$token = $app->getApp_token();
			if($token != null){
				$this->notificacaoApp->envia_notificacao(
						($valorVendido > 0) ? 'Recarga de Crédito' : 'Estorno de Crédito',
						(($valorVendido > 0) ? 'Recarga R$' : 'Estorno R$') . number_format($valorVendido, 2, ',', '.') . ', saldo R$' . number_format($novoValor, 2, ',', '.'),
						$token);
			}
		}
	}
	
	/**
	 *
	 * @param float $valorPago
	 */
	public function notificacaoCatracaVirtual($valorPago){
		$app = $this->appDao->retornaAppPorUsuarioId($this->idUsuario);
		if($app != null){
			$token = $app->getApp_token();
			if($token != null){
				$this->notificacaoApp->envia_notificacao('Uso de Crédito', 'Você consumiu R$'. number_format($valorPago, 2, ',', '.'), $token);
			}
		}
	}
	
	/**
	 *
	 * @param float $valorAtual
	 * @param float $valorPago
	 * @return string
	 */
	public function notificacaoCatracaVirtualUtilizacao($valorAtual, $valorPago){
		$app = $this->appDao->retornaAppPorUsuarioId($this->idUsuario);
		if($app == null){
			return;
		}
			
		$token = $app->getApp_token();
		if($token == null){
			return;
		}
		
		if(round($valorAtual, 2)>= round($valorPago*4, 2) ){
			return;
		} 
		
		if(round($valorAtual, 2)>= round($valorPago*3, 2) ){
			return $this->notificacaoApp->envia_notificacao('Faça Recarga!', 'Crédito atual para 3 refeições', $token);
		}
		
		if(round($valorAtual, 2)>= round($valorPago*2, 2) ){
			return $this->notificacaoApp->envia_notificacao('Faça Recarga!', 'Crédito atual para 2 refeições', $token);
		}
		if(round($valorAtual, 2)>= round($valorPago, 2) ){
			return $this->notificacaoApp->envia_notificacao('Faça Recarga!', 'Crédito final para 1 refeição', $token);
		}
		else{
			return $this->notificacaoApp->envia_notificacao('Faça recarga agora!', 'Você está sem crédito', $token);
		}
		
	}
	
	
}

?>