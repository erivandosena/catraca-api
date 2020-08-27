<?php


class UploadArquivoController{
	
	public static function main($nivelDeAcesso) {
		switch ($nivelDeAcesso) {
			case Sessao::NIVEL_SUPER :
				$controller = new UploadArquivoController();
				$controller->receber();
				break;
			default :
				UsuarioController::main ( $nivelDeAcesso );
				break;
		}
	}
	public function receber(){

		define('UPLOAD_DIR', 'fotos/');
		$img = $_POST['img64'];
		$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$data = base64_decode($img);
		$file = UPLOAD_DIR .$_POST['id_usuario']. '.png';
		$success = file_put_contents($file, $data);
		print $success ? "Foto salva com sucesso!" : 'Erro ao tentar salvar arquivo.';
		echo '<meta http-equiv="refresh" content="2; url=.\?pagina=cartao&selecionado=' . $_POST['id_usuario'] . '">';
		
	}
}