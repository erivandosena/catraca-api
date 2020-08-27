<?php

class MensagemController
{

    private $view;

    private $dao;

    public static function main($nivelDeAcesso)
    {
        switch ($nivelDeAcesso) {
            case Sessao::NIVEL_ADMIN:
                $controller = new MensagemController();
                $controller->definicoesMensagem();
                break;
            default:
                UsuarioController::main($nivelDeAcesso);
                break;
        }
    }

    public function __construct()
    {
        $this->dao = new MensagemDAO();
        $this->view = new MensagemView();
    }

    public function definicoesMensagem()
    {
        $this->cadastrar();
        $this->listar();
    }

    public function cadastrar()
    {
        $catracaDao = new CatracaDAO($this->dao->getConexao());
        $this->view->formularioInserir($catracaDao->lista());
        if (! isset($_GET['salvar'])) {
            return;
        }
        if (! isset($_POST['confirmar'])) {
            $this->view->formConfirmacao();
            return;
        }

        $mensagem = new Mensagem();
        $mensagem->setMensagem1($_GET['msg_um']);
        $mensagem->setMensagem2($_GET['msg_dois']);
        $mensagem->setMensagem3($_GET['msg_tres']); 
        $mensagem->setMensagem4($_GET['msg_quatro']);
        $mensagem->getCatraca()->setId($_GET['id_catraca']);
        
        if($this->dao->atualizaOuInsere($mensagem)){
           $this->view->sucesso();
        }
        else{
            $this->view->erro();
        }
        echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes_mensagem">';
    }

    public function listar()
    {
        $this->view->listarMensagensCatraca($this->dao->lista());
    }
}

?>