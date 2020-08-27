<?php


/**
 * 
 * @author Jefferson Uchôa Ponte
 *
 */


class UsuarioController{
	
	public static function main($nivelDeAcesso){
		switch ($nivelDeAcesso){

			case Sessao::NIVEL_DESLOGADO:
				$usuarioController = new UsuarioController();
				$usuarioController->telaLogin();
				break;
			default:
				$sessao = new Sessao();
				
				$usuario = new Usuario();
				$usuario->setLogin($sessao->getLoginUsuario());
				
				$usuarioDao = new UsuarioDAO();
				
				$usuarioDao->preenchePorLogin($usuario);
				$usuarioDao->retornaPorIdBaseExterna($usuario);
				$nome = $usuario->getNome();
				$lista = explode(' ', $nome);
				$nome = $lista[0];
				echo '<p>Olá, '.ucfirst  ( strtolower ($nome)).'!</p>
						<p>Visualize abaixo seus dados referentes ao Restaurante Universitário</p>';
				
				PessoalController::main($nivelDeAcesso);		

				break;
		}

		
	}
	
	public static function gerenciaAdmin($nivelDeAcesso){
		switch ($nivelDeAcesso)
		{
			case Sessao::NIVEL_SUPER:
				$usuarioController = new UsuarioController();
				$usuarioController->gerenciamentoDeAdministrador();
				break;
			default:
				break;
		}
	}
	public function telaLogin(){
		$usuarioView = new UsuarioView();
		$erro=FALSE;
		if(isset($_POST['formlogin']))
		
		{
			$usuarioDAO = new UsuarioDAO();
			$usuario = new Usuario();
			$usuario->setLogin($_POST['login']);
			$usuario->setSenha($_POST['senha']);
			if($usuarioDAO->autentica($usuario)){
		
				$sessao2 = new Sessao();
				$sessao2->criaSessao($usuario->getId(), $usuario->getNivelAcesso(), $usuario->getLogin());
				echo '<meta http-equiv="refresh" content=1;url="./index.php">';
			}else{
				$msg_erro= "Senha ou usuário Inválido";
				$erro=true;
				
				$usuarioView->mostraFormularioLogin($erro, $msg_erro);
				return;
		
			}
		}
		$usuarioView->mostraFormularioLogin();
	}


}
?>