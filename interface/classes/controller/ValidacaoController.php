<?php

/**
 * 
 * @author Jefferson Uchôa Ponte
 *
 */
class ValidacaoController
{

    private $view;

    private $dao;

    /**
     * Método estático feito para ser chamado na página desejada,
     * inicia as definições da validação.
     *
     * @param int $nivelAcesso
     */
    public static function main($nivelAcesso)
    {
        switch ($nivelAcesso) {
            case Sessao::NIVEL_ADMIN:
                $valida = new ValidacaoController();
                $valida->definicoesValidacao();
                break;
            default:
                UsuarioController::main($nivelAcesso);
                break;
        }
    }

    public function __construct()
    {
        $this->view = new ValidacaoView();
        $this->dao = new ValidacaoDAO();
    }

    public function definicoesValidacao()
    {
        if (! isset($_GET['validacaoadd']) && ! isset($_GET['excluir'])) {

            $this->view->exibirLista($this->dao->listaValidacao());
            $this->view->botaoInserirValidacao();
            return;
        }
        $this->adicionar();
        $this->excluir();
    }

    public function adicionar()
    {
        if (! isset($_GET['validacaoadd'])) {
            return;
        }
        if (! isset($_GET['adicionar'])) {
            $tipoDao = new TipoDAO($this->dao->getConexao());
            $listaTipos = $tipoDao->retornaLista();
            $listaCampos = $this->dao->listaDeCampos();
            $this->view->formValidacao($listaTipos, $listaCampos);
            return;
        }
        if (! isset($_POST['confirmar'])) {
            $this->view->formConfirmar("Tem certeza que deseja adicionar esta validação?");
            return;
        }

        $validacao = new Validacao();
        $validacao->getTipo()->setId($_GET['tipo']);
        $validacao->setCampo($_GET['campo']);
        $validacao->setValor($_GET['valor']);
        if ($this->dao->inserirValidacao($validacao)) {
            $this->view->mensagemSucesso("Validação Inserida Com Sucesso!");
        } else {
            $this->view->mensagemErro("Falha ao Inserir Validação!");
        }

        echo '<meta http-equiv="refresh" content="3; url=.\?pagina=validacao">';
    }

    public function excluir()
    {
        if (! isset($_GET['excluir'])) {
            return;
        }
        if (! isset($_POST['confirmar'])) {
            $this->view->formConfirmar("Tem certeza que deseja excluir esta validação?");
            return;
        }
        $validacao = new Validacao();
        $validacao->setId($_GET['validacao_id']);
        if ($this->dao->excluirValidacao($validacao)) {
            $this->view->mensagemSucesso("Validação Excluida Com Sucesso!");
        } else {
            $this->view->mensagemErro("Erro ao tentar Excluir Validação. ");
        }
        echo '<meta http-equiv="refresh" content="3; url=.\?pagina=validacao">';
    }
}
?>