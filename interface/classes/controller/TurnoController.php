<?php

class TurnoController
{

    private $view;
    private $dao;

    public static function main($nivelDeAcesso)
    {
        switch ($nivelDeAcesso) {
            case Sessao::NIVEL_ADMIN:
                $controller = new TurnoController();
                $controller->definicoesTurno();
                break;
            default:
                UsuarioController::main($nivelDeAcesso);
                break;
        }
    }
    public function __construct()
    {
        $this->view = new TurnoView();
        $this->dao = new TurnoDAO();
    }
    public function definicoesTurno(){
        if (isset($_GET['editar'])){
            $this->editar();
        }else{
            $this->cadastrar();
        }
        $this->listar();
    }
    public function cadastrar()
    {
        $this->view->mostraFormulario();
        if (! isset($_GET['cadastrar_turno'])) {
            return;
        }
        if (strlen($_GET['turno_nome']) < 3) {
            $this->view->mostraMensagem("Escreva Um Nome Maior");
            return;
        }
        if (strlen($_GET['hora_inicio']) < 3) {
            $this->view->mostraMensagem("Digite a Hora de Início");
            return;
        }
        if (strlen($_GET['hora_fim']) < 3) {
            $this->view->mostraMensagem("Digite a Hora de fim");
            return;
        }
        $turno = new Turno();
        $turno->setDescricao($_GET['turno_nome']);
        $turno->setHoraInicial($_GET['hora_inicio']);
        $turno->setHoraFinal($_GET['hora_fim']);
        $this->view->mostraFormConfirmacao($turno);
        if (! isset($_POST['certeza_cadastrar_turno'])) {
            return;
        }
        if ($this->dao->inserir($turno)) {
            $this->view->mostraMensagem("Sucesso");
        } else {
            $this->view->mostraMensagem("Erro ao tentar inserir unidade");
        }
        echo '<meta http-equiv="refresh" content="4; url=.\?pagina=definicoes_turno">';
    }

    public function listar()
    {
        $turnos = $this->dao->retornaLista();
        $this->view->listarTurnos($turnos);
    }

    public function editar(){
        if (!isset($_GET['id_turno'])){
            return;
        }
        $turno = new Turno();
        $turno->setId($_GET['id_turno']);
        $this->dao->retornaTurnoPorId($turno);
        $this->view->formEditar($turno);
        
        if (!isset($_POST['confirmar'])){
            return;
        }
        $turno->setHoraInicial($_POST['hora_inicio']);
        $turno->setHoraFinal($_POST['hora_fim']);
        
        if ($this->dao->atualizaTurno($turno)){
            $this->view->mostraMensagem("Turno alterado com sucesso!");
            
        }else{
            $this->view->mostraMensagem("Erro ao editar o turno.");
        }
        echo '<meta http-equiv="refresh" content="2; url=.\?pagina=definicoes_turno">';
        
    }



}

?>
