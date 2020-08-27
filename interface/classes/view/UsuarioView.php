<?php
class UsuarioView {
	public function mostraFormularioLogin($erro = false, $msg_erro = "") {
		echo '<div class="fundo-cinza1">
     <div class="duas colunas no-meio">
            <div class="no-centro">
                <h1>Catraca</h1>
            </div>
            <div class="linha fundo-branco com-bordas">
                <div class="conteudo">';
		
		if ($erro)
			echo '     
                    <div class="alerta-erro">
                       <div class="icone icone-fire ix16"></div>
                       <div class="titulo-alerta">' . $msg_erro . '</div>
                       <div class="subtitulo-alerta">Favor verificar novamente.</div>
                    </div>';
		
		echo '<form method="post" action="" class="formulario-organizado">

                       <label for="idTextLogin">
                           Login
                           <input type="text" name="login" id="idTextLogin" class="doze" placeholder="Digite seu Usuário"/>
                        </label>
                        <label for="idTextSenha">
                            Senha
                            <input type="password" name="senha" id="idTextSenha" class="doze" placeholder="Digite sua Senha" />
                        </label>
                       <button type="submit" name="formlogin" class="botao b-primario doze"><span class="icone-redo2"></span> Entrar </button>                
                    </form>
                    <a href="http://sigadmin.unilab.edu.br/admin/public/recuperar_login.jsf" class="medio centralizado doze colunas">Não consegue acessar o sistema?</a>                 
                </div>
            </div>
            
     </div>
</div>';
	}
	public function formularioGerarAdministrador($erro = false, $msg_erro = "") {
		
	}
}