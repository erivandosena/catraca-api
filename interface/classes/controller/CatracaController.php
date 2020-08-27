<?php
/**
 *
 * @author Jefferson Uchoa Ponte
 *
 */
class CatracaController
{

    private $view;

    private $dao;

    public static function main($nivelDeAcesso)
    {
        switch ($nivelDeAcesso) {
            case Sessao::NIVEL_ADMIN:
                $controller = new CatracaController();
                $controller->definicoesCatraca();
                break;
            default:
                UsuarioController::main($nivelDeAcesso);
        }
    }

    public function __construct()
    {
        $this->view = new CatracaView();
        $this->dao = new CatracaDAO();
    }
    public function definicoesCatraca()
    {
        if (isset($_GET['editar_catraca'])) {
            $this->editar();
        }else{
            $this->cadastro();
        }
        $this->listagem();
    }
    public function cadastro()
    {
        $this->view->mostrarFormCatraca();
        if (! isset($_GET['nome_catraca'])) {
            return;
        }
        if (strlen($_GET['nome_catraca']) < 3) {
            $this->view->formMensagem("-ajuda", "Insira um nome maior");
            return;
        }
        $catraca = new Catraca();
        $catraca->setNome($_GET['nome_catraca']);
        if ($this->dao->existe($catraca)) {
            $this->view->formMensagem("-erro", "Esta catraca já existe!");
            return;
        }
        $this->view->formConfirmacao($catraca);
        if (! isset($_POST['confirmar'])) {
            return;
        }
        if ($this->dao->inserir($catraca)) {
            $this->view->formMensagem("-sucesso", "Catraca adicionada com Sucesso!");
        } else {
            $this->view->formMensagem("-erro", "Não foi possível adicionar a catraca!");
        }
        echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes_catraca">';
    }

    public function listagem()
    {
        $listaDeCatracas = $this->dao->lista();
        $this->view->listarCatracas($listaDeCatracas);
    }

   
    public function editar(){
        $id = intval($_GET['editar_catraca']);
        if (! $id){
            $this->view->formMensagem("-erro", "Erro ao tentar editar catraca");
            return;
        }
        $catraca = new Catraca();
        $catraca->setId($_GET['editar_catraca']);
        $listaDeUnidades = $this->dao->lista();
        $this->dao->preencheCatracaPorId($catraca);
        $this->view->formEditarCatraca($catraca, $listaDeUnidades);
        if (!isset($_POST['salvar'])) {
           return;
        }
        $catraca->setId($_POST['id_catraca']);
        $catraca->setNome($_POST['nome_catraca']);
        $catraca->setOperacao($_POST['operacao']);
        $catraca->setTempoDeGiro($_POST['tempo_giro']);
        $catraca->setInterfaceRede($_POST['interface']);
        $catraca->setUnidade(new Unidade());
        $catraca->getUnidade()->setId($_POST['id_unidade']);
        $catraca->setFinanceiro($_POST['financeiro']);
        if ($this->dao->atualizarCatraca($catraca))
        {
            $this->view->formMensagem("-sucesso", "Catraca editada com sucesso");
        }
        else{
            $this->view->formMensagem("-erro", "Erro ao tentar editar catraca");
        }
        echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes_catraca&lista_catracas=1">';
    }
}

?>