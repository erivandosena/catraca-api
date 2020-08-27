<?php

/**
 *
 * @author Jefferson Uchôa Ponte
 *
 */
class CustoController
{

    private $view;

    private $dao;

    public static function main($nivelDeAcesso)
    {
        switch ($nivelDeAcesso) {
            case Sessao::NIVEL_ADMIN:
                $controller = new CustoController();
                $controller->definicoesCusto();
                break;
            default:
                UsuarioController::main($nivelDeAcesso);
                break;
        }
    }

    public function __construct()
    {
        $this->view = new CustoView();
        $this->dao = new CustoDAO();
    }

    public function definicoesCusto(){
        if(!isset($_GET['custoadd']) && !isset($_GET['excluir'])){
            $this->view->listaCustoUnidade($this->dao->listaDeCustos());
            $this->view->botaoInserirCusto();
            return;
        }
        $this->adicionar();
        $this->excluir();
    }
    public function adicionar(){
        if(!isset($_GET['custoadd'])){
            return;
        }
        if(!isset($_GET['adicionar'])){
            $unidadeDao = new UnidadeDAO($this->dao->getConexao());
            $turnoDao = new TurnoDAO($this->dao->getConexao());
            $this->view->formInserirCusto($unidadeDao->retornaLista(), $turnoDao->retornaLista());
            return;
        }
        
        $timeI1 = strtotime($_GET['inicio']);
        $timeI2 = strtotime($_GET['fim']);
        if($timeI2 <= $timeI1){
            $this->view->erro("A data final deve ser maior que a data inicial.");
            echo '<meta http-equiv="refresh" content="3; url=.\?pagina=definicoes_custo">';
            return;
        }
        
        $listaDeCustos = $this->dao->listaDeCustos();
        foreach($listaDeCustos as $esseCusto){
            if($esseCusto->getTurno()->getId() != $_GET['turno'])
            {
                continue;
            }
            if($esseCusto->getUnidade()->getId() != $_GET['unidade'])
            {
                continue;
            }
            
            $t1 = strtotime($esseCusto->getInicio());
            $t2 = strtotime($esseCusto->getInicio());
            if($timeI1 < $t1 && $timeI2 < $t1){
                continue;
            }
            if($timeI1 > $t2 && $timeI2 > $t2){
                continue;
            }
            //Se o algoritmo chegou aqui então temos um choque de custos.
            $this->view->erro("Choque de custos. ");
            echo '<meta http-equiv="refresh" content="3; url=.\?pagina=definicoes_custo">';
            return;
            
        }
        
        if(!isset($_POST['confirmar'])){
            $this->view->formConfirmar();
            return;
        }
        $custo = new Custo();
        $custo->setValor($_GET['valor']);
        $unidade = new Unidade();
        $unidade->setId($_GET['unidade']);
        $turno = new Turno();
        $turno->setId($_GET['turno']);
        
        $custo->setUnidade($unidade);
        $custo->setTurno($turno);
        $custo->setInicio($_GET['inicio']);
        $custo->setFim($_GET['fim']);
        

        

        
        
        if($this->dao->inserir($custo)){
            $this->view->sucesso();
        }else{
            $this->view->erro();
        }
        echo '<meta http-equiv="refresh" content="3; url=.\?pagina=definicoes_custo">';
    }
  
    public function excluir(){
        if(!isset($_GET['excluir'])){
            return;
        }
        if(!isset($_POST['confirmar_excluir'])){
            $this->view->formConfirmarExcluir();
            return;
        }
        $custo = new Custo();
        $custo->setId($_GET['excluir']);
        if($this->dao->excluirCusto($custo)){
            $this->view->sucesso();
        }else{
            $this->view->erro();
        }
        echo '<meta http-equiv="refresh" content="3; url=.\?pagina=definicoes_custo">';
    }

}

?>