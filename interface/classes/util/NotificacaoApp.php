<?php

	/**
	 * Notiticacoes FireBase Server.
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

/**
 * Classe de envio de mensagens para o aplicativo
 */
class NotificacaoApp {
		
		/**
		 * Envia as notificacoes para o aplicativo do usuario
		 * 
		 * @param string $titulo O titulo da mensagem
		 * @param string $mensagem A mensagem a ser enviada
		 * @param string $token O TOKEN obtido a apartir do aplicativo do usuario
		 */
		function envia_notificacao($titulo, $mensagem, $token) {
			$urlFirebase = 'https://fcm.googleapis.com/fcm/send';
			define( 'API_ACCESS_KEY', 'AIzaSyAGsra0IfOxKCAu6N9j7LFC4QqS9733Ysg');
			
			$msg = array(
					'body'		=> $mensagem,
					'title'		=> $titulo,
					'sound'		=> 'default',
					'icon'		=> 'ic_notification_round'
			);
			
			$dados = array(
					'image'		=> 'https://www.catraca.unilab.edu.br/app/imagens/logo_labpati_fcm.png',
					'extrato'	=> 'CE'
			);
			
			$fields = array(
					'to'			=> $token,
					'priority'		=> 'high',
					'notification'	=> $msg,
					'data'			=> $dados
			);
			
			$headers = array
					(
						'Authorization: key='. API_ACCESS_KEY,
						'Content-Type: application/json'		
			);
			
			
			$ch = curl_init();
			curl_setopt_array($ch, array(
					CURLOPT_URL 			=> $urlFirebase,
					CURLOPT_POST 			=> true,
					CURLOPT_HTTPHEADER 		=> $headers,
					CURLOPT_RETURNTRANSFER 	=> true,
					CURLOPT_POSTFIELDS 		=> json_encode($fields),
					CURLOPT_FRESH_CONNECT 	=> false,
					CURLOPT_NOBODY 			=> false,
					CURLOPT_HEADER 			=> false,
					CURLOPT_NOSIGNAL 		=> 1,	/* Tempo limite imediatamente se o valor for < 1000 ms */
					CURLOPT_TIMEOUT_MS 		=> 0	/* O número máximo de milisegundos para permitir que as funções CURL sejam executadas */
			));
			
			$curl_errno = curl_errno($ch);
			$curl_error = curl_error($ch);
			$out = curl_exec($ch);
			
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = substr($out, 0, $header_size);
			$body = substr($out, $header_size);

			curl_close($ch);
			
			if ($curl_errno > 0) {
				return;
			} else {
				return;
			}
		}
	}
	
?>