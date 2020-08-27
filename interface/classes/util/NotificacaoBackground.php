<?php

/**
 * Envio de Notiticacoes via FireBase Server.
 *
 * Script de envio de notiticacoes em background via api firebase.
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

class NotificacaoBackground {
	
	private static function getCaminhoAbsoluto() {
		return $_SERVER["DOCUMENT_ROOT"];
	}
	
	/**
	 * 
	 * @param integer $idUsuario
	 * @param string $tela_sistema
	 * @param float $valorVendido
	 * @param float $novoValor
	 * @return string
	 */
	public static function executaPidGuiche($idUsuario, $tela_sistema, $valorVendido, $novoValor) {
		$path = NotificacaoBackground::getCaminhoAbsoluto();
		$PID = shell_exec('nohup nice -n 10 php '.$path.'/classes/util/NotificacaoFirebase.php '.$idUsuario.' '.$tela_sistema.' '.$valorVendido.' '.$novoValor.' > /dev/null & printf "%u" $!');
		return $PID;
	}
	
	/**
	 * 
	 * @param integer $idUsuario
	 * @param string $tela_sistema
	 * @param float $valorPago
	 * @return string
	 */
	public static function executaPidCatracaVirtual($idUsuario, $tela_sistema, $valorPago) {
		$path = NotificacaoBackground::getCaminhoAbsoluto();
		$PID = shell_exec('nohup nice -n 10 php '.$path.'/classes/util/NotificacaoFirebase.php '.$idUsuario.' '.$tela_sistema.' '.$valorPago.' > /dev/null & printf "%u" $!');
		return $PID;
	}
	
	/**
	 * 
	 * @param integer $idUsuario
	 * @param string $tela_sistema
	 * @param float $valorAtual
	 * @param float $valorPago
	 * @return string
	 */
	public static function executaPidCatracaVirtualUtilizacao($idUsuario, $tela_sistema, $valorAtual, $valorPago) {
		$path = NotificacaoBackground::getCaminhoAbsoluto();
		$PID = shell_exec('nohup nice -n 10 php '.$path.'/classes/util/NotificacaoFirebase.php '.$idUsuario.' '.$tela_sistema.' '.$valorAtual.' '.$valorPago.' > /dev/null & printf "%u" $!');
		return $PID;
	}
	
}

?>		