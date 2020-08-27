<?php 

class TipoController{
    
    private $view;
    private $dao;
    
    
    public static function main($nivelDeAcesso) {
        switch ($nivelDeAcesso) {
            case Sessao::NIVEL_ADMIN:
                $controller = new TipoController();
                $controller->definicoesTipo();
                break;
            default :
                UsuarioController::main ( $nivelDeAcesso );
                break;
        }
    }
    

    public function __construct(){
        $this->dao = new TipoDAO();
        $this->view = new TipoView();
    }
    

    public function cadastro(){
        $this->view->formAdicionarTipoDeUsuario();
        if (!isset ( $_GET ['cadastrar_tipo'] )) {
            return;
        }
        $tipo = new Tipo(); 
        
        $tipo->setNome($_GET ['tipo_nome']);
        $tipo->setValorCobrado($_GET ['tipo_valor']);
        $this->view->formConfirmacao($tipo);
        if (!isset ( $_POST ['certeza_cadastrar_tipo'] )) {
            return;
        }
        if ($this->dao->inserir($tipo)) {
            $this->view->formMensagem("-sucesso", "Sucesso") ;
        } else {
            $this->view->formMensagem ("-erro",  "Erro ao tentar inserir unidade" );
        }
        echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes_tipo">';
        
    }
    public function listagem(){
        $this->view->listarTiposDeUsuarios ( $this->dao->retornaLista () );
    }
    public function editar(){
        $tipo = new Tipo();
        $tipo->setId($_GET['editar_tipo']);
        $this->dao->retornaTipoPorId($tipo);
        $this->view->formEditarTipo($tipo);
        
        if (!isset($_POST['alterar'])){
            return;
        }
        $tipo->setValorCobrado($_POST['valor_tipo']);
        if ($this->dao->atualizar($tipo)){
            $this->view->formMensagem("-sucesso", "Valor do ".$tipo->getNome()." foi alterado com sucesso!");
        }else{
            $this->view->formMensagem("-erro", "Não foi possivel altera o valor!");
        }
        echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes_tipo">';
        
    
        
    }
    public function definicoesTipo(){
        if (isset($_GET['editar_tipo'])){
            $this->editar();
        }else{
            $this->cadastro();
        }        
        $this->listagem();
    }
}




?>