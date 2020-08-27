<?php
/**
 * 
 * @author Jefferson Uchôa Ponte
 *
 */
class CartaoProprioController
{

    private $view;

    private $dao;

    public static function main($nivelDeAcesso)
    {
        switch ($nivelDeAcesso) {
            case Sessao::NIVEL_ADMIN:
                $controller = new CartaoProprioController();
                $controller->telaCartao();
                break;
            case Sessao::NIVEL_GUICHE:
                $controller = new CartaoProprioController();
                $controller->telaCartao();
                break;
            case Sessao::NIVEL_POLIVALENTE:
                $controller = new CartaoProprioController();
                $controller->telaCartao();
                break;
            case Sessao::NIVEL_CADASTRO:
                $controller = new CartaoProprioController();
                $controller->telaCartao();
                break;
            case Sessao::NIVEL_CATRACA_VIRTUAL:
                $controller = new CartaoProprioController();
                $controller->telaCartao();
                break;
            default:
                UsuarioController::main($nivelDeAcesso);
                break;
        }
    }

    public function __construct()
    {
        $this->view = new CartaoProprioView();
        $this->dao = new UsuarioDAO();
    }

    public function telaCartao()
    {
        if (! isset($_GET['selecionado'])) {
            $this->view->formBuscaUsuarios(); 
            $this->buscar();
            return;
        }
        $this->selecionar();
    }

    /**
     * Resultado da busca. 
     */
    public function buscar()
    {
        if (! isset($_GET['nome'])) {
            return;
        }

        $listaDeUsuarios = $this->dao->pesquisaNoSigaa($_GET['nome']);
        $this->view->mostraResultadoBuscaDeUsuarios($listaDeUsuarios, $_GET['nome']);
    }

    /**
     * Página com usuário selecionado. 
     */
    public function selecionar()
    {
        if (! isset($_GET['selecionado'])) {
            return;
        }

        $idDoSelecionado = intval($_GET['selecionado']);
        $usuario = new Usuario();
        $usuario->setIdBaseExterna($idDoSelecionado);
        
        $lista = $this->dao->listaPorIdBaseExterna($usuario);
        
        if(!count($lista)){
            $this->view->erro("Usuário Não Localizado.");
            return;
        }
        $this->view->mostraSelecionado($lista[0]);
        $this->view->mostraDadosAdicionais($lista);
 
        
        if(isset($_GET['vinculo_cancelar'])){
            $this->invalidarVinculo();
            return;
        }
        if(isset($_GET['vinculo_renovar'])){
            $this->renovarVinculo();
            return;
        }
        
        $vinculoDao = new VinculoDAO($this->dao->getConexao());
        $vinculos = $vinculoDao->retornaVinculosValidosDeUsuario($usuario);
        foreach ($vinculos as $vinculoComIsencao) 
        {
            $vinculoDao->isencaoValidaDoVinculo($vinculoComIsencao);
        }

        if (! count($vinculos)) 
        {
            $this->adicionarCartao($usuario);
        }
        else 
        {
            $this->view->mostraVinculos($vinculos, 'Vinculos ativos');
        }
        $vinculosVencidos = $vinculoDao->retornaVinculosVencidos($usuario);
        if (count($vinculosVencidos))
        {
            $this->view->mostraVinculos($vinculosVencidos, 'Vinculos Vencidos');
        }
        
        
    }
    
    /**
     * @param Usuario $usuario
     */
    public function adicionarCartao(Usuario $usuario){

        if(!isset($_GET['add_cartao'])){
            $this->view->botaoAdicionarCartao($usuario->getIdBaseExterna());
            return;
        }
        
        $validacaoDao = new ValidacaoDAO();
        $listaTipos = $validacaoDao->listaDeTipos($usuario);
        
        if(!isset($_GET['add_cartao_numero'])){
            $this->view->formAddCartao($listaTipos, $usuario->getIdBaseExterna());
            return;
        }
        if(!isset($_GET['numero_cartao2'])){
            return;
        }
        if(!isset($_GET['id_tipo'])){
            return;
        }
        
        $this->dao->preenchePorIdBaseExterna($usuario);

        if(!isset($_POST['certeza'])){
            $this->view->formConfirmacao("Tem certeza que deseja enviar esse cartão para o usuário ".$usuario->getNome());
            return;
        }
        $vinculoDao = new VinculoDAO($this->dao->getConexao());
        $vinculo = new Vinculo();
        $daqui3Meses = date ( 'Y-m-d', strtotime ( "+90 days" ) ) . 'T' . date ( 'G:00:01' );
        $vinculo->setFinalValidade($daqui3Meses);
        $vinculo->getCartao()->getTipo()->setId($_GET['id_tipo']);
        $vinculo->getCartao()->setNumero($_GET['numero_cartao2']);
        $vinculo->setResponsavel($usuario);
        $vinculo->setInicioValidade(date ( "Y-m-d G:i:s" ));
        
        if($vinculoDao->cartaoTemVinculo($vinculo->getCartao())){
            $this->view->erro("Esse cartão já foi utilizado. Tente Outro.");
            $this->view->botaoAdicionarCartao($usuario->getIdBaseExterna());
            return;
            
        }
        if($vinculoDao->adicionaVinculo ($vinculo)){
            $this->view->sucesso("Cartão Adicionado Com Sucesso!");
        }
        else
        {
            $this->view->erro("Erro Ao Adicionar Cartão!");
        }
        echo '<meta http-equiv="refresh" content="2; url=.\?pagina=cartao_proprio&selecionado=' . $_GET['selecionado'] . '">';
        
    }
    
    public function invalidarVinculo(){
        if(!isset($_GET['vinculo_cancelar'])){
            return;
        }
        $vinculo = new Vinculo();
        $vinculo->setId($_GET['vinculo_cancelar']);
        
        if(!isset($_POST['certeza'])){
            $this->view->formConfirmacao("Deseja Confirmar o Cancelamento deste Vínculo?");
            return;
        }
        $vinculoDao = new VinculoDAO($this->dao->getConexao());
        if($vinculoDao->invalidarVinculo($vinculo)){
            $this->view->sucesso();
        }
        else
        {
            $this->view->erro();
        }
        if(isset($_GET['selecionado'])){
            echo '<meta http-equiv="refresh" content="2; url=.\?pagina=cartao_proprio&selecionado=' . $_GET['selecionado'] . '">';
        }
        
               
    }
    
    public function renovarVinculo(){
        if(!isset($_GET['vinculo_renovar'])){
            return;
        }
        $vinculo = new Vinculo();
        $vinculo->setId($_GET['vinculo_renovar']);
        $vinculoDao = new VinculoDAO($this->dao->getConexao());
        $vinculoDao->vinculoPorId($vinculo);
        $validacaoDao = new ValidacaoDAO($this->dao->getConexao());

        $listaDeTipos = $validacaoDao->listaDeTipos($vinculo->getResponsavel());
        
        
       
        
        $encontrei = false;
        foreach($listaDeTipos as $tipo){
            if($tipo->getId() == $vinculo->getCartao()->getTipo()->getId()){
                $encontrei = true;
                break;
            }
        }
        if(!$encontrei){
            $this->view->erro("Usuário Não Está Mais Ativo Para o Tipo: ".$vinculo->getCartao()->getTipo()->getNome());
            echo '<meta http-equiv="refresh" content="2; url=.\?pagina=cartao_proprio&selecionado=' . $_GET['selecionado'] . '">';
            return;
        }
        $daqui3Meses = date ( 'Y-m-d', strtotime ( "+90 days" ) ) . 'T' . date ( 'G:00:01' );
        $vinculo->setFinalValidade($daqui3Meses);	
        
        if(!isset($_POST['certeza'])){
            $this->view->formConfirmacao("Deseja Confirmar a Renovação deste Vínculo?");
            return;
        }
        $vinculoDao = new VinculoDAO($this->dao->getConexao());
        if($vinculoDao->atualizaValidade($vinculo)){
            $this->view->sucesso();
        }
        else
        {
            $this->view->erro();
        }
       
        echo '<meta http-equiv="refresh" content="2; url=.\?pagina=cartao_proprio&selecionado=' . $_GET['selecionado'] . '">';
        
    }
}

?>