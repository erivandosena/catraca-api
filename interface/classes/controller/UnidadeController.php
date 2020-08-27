<?php

class UnidadeController
{

    private $view;

    private $dao;

    public static function main($nivelDeAcesso)
    {
        switch ($nivelDeAcesso) {
            case Sessao::NIVEL_ADMIN:
                $controller = new UnidadeController();
                $controller->paginaUnidade();
                break;
            default:
                UsuarioController::main($nivelDeAcesso);
                break;
        }
    }

    public function __construct()
    {
        $this->view = new UnidadeView();
        $this->dao = new UnidadeDAO();
    }

    public function paginaUnidade()
    {
        if (isset($_GET['turno_na_unidade'])) {
            $this->adicionarTurnoNaUnidade();
        } else if (isset($_GET['excluir_turno_da_unidade'])) {
            $this->removerTurnoDaUnidade();
        } else {
            $this->cadastro();
        }
        $this->listar();
    }

    public function adicionarTurnoNaUnidade()
    {
        $unidade = new Unidade();
        $unidade->setId($_GET['turno_na_unidade']);
        $this->dao->preenchePorId($unidade);
        $turnoDao = new TurnoDAO($this->dao->getConexao());
        $listaDeTurnos = $turnoDao->retornaLista ();
        $this->view->formTurnoNaUnidade($unidade, $listaDeTurnos);
        if(!isset($_POST['turno_na_unidade'])){
            return;
        }
        $turno = new Turno();
        $turno->setId($_POST['id_turno']);
        $unidade->setId($_POST['id_unidade']);
        if($this->dao->turnoNaUnidade($turno, $unidade)){
            $this->view->formMensagem("-sucesso", "Turno Adicionado com Sucesso!");
        }
        else{
            $this->view->formMensagem("-erro", "Turno não foi Adicionado!");
        }
        echo '<meta http-equiv="refresh" content="4; url=.\?pagina=definicoes_unidade">';
    
    }

    public function removerTurnoDaUnidade()
    {
        $unidade = new Unidade ();
        $unidade->setId ( $_GET ['excluir_turno_da_unidade'] );
        $this->dao->preenchePorId($unidade);
        $this->dao->turnosDaUnidade($unidade);
        $this->view->formExcluirTurnoDaUnidade($unidade);
        if(!isset($_POST['excluir_turno_da_unidade']) || !isset($_POST['id_turno'])){
            return;
        }
        
        $turno = new Turno();
        $unidade = new Unidade();
        $turno->setId($_POST['id_turno']);
        $unidade->setId($_POST['id_unidade']);
        if($this->dao->excluirTurnoDaUnidade($turno, $unidade)){
            $this->view->formMensagem("-sucesso", "Turno excluído com Sucesso");
        }else{
            $this->view->formMensagem("-erro", "Erro ao tentar excluir turno na Unidade");
        }
        echo '<meta http-equiv="refresh" content="4; url=.\?pagina=definicoes_unidade">';
                    
    }

    public function cadastro()
    {
        $this->view->mostraFormInserir();
        if (! isset($_GET['cadastrar_unidade'])) {
            return;
        }
        if (strlen($_GET['cadastrar_unidade']) < 3) {
            echo "Digite uma Unidade Acadêmica Válida";
            return;
        }
        $this->view->formConfirmacao('Deseja adicionar: ' . $_GET['cadastrar_unidade'] . '?');
        
        if (! isset($_POST['certeza_cadastrar_unidade'])) {
            return;
        }
        if (strlen($_GET['cadastrar_unidade']) < 3) {
            return;
        }
        
        $unidade = new Unidade();
        $unidade->setNome($_POST['certeza_cadastrar_unidade']);
        if ($this->dao->existe($unidade)) {
            $this->view->formMensagem("-erro", "Unidade já cadastrada!");
            echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes_unidade">';
            return;
        }
        if ($this->dao->inserirUnidade($unidade)) {
            $this->view->formMensagem("-sucesso", "Unidade inserida com sucesso!");
            echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes_unidade">';
            return;
        }
        $this->view->formMensagem("-erro", "Erro ao tentar inserir a unidade!");
        echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes_unidade">';
    }

    public function listar()
    {
        $unidadesAcademicas = $this->dao->retornaLista();
        foreach ($unidadesAcademicas as $unidadeAcademica) {
            $this->dao->turnosDaUnidade($unidadeAcademica);
        }
        $this->view->listarUnidadesAcademicas($unidadesAcademicas);
    }
}
?>