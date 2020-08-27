<?php
/**
 * 
 * @author Jefferson Uchôa Ponte
 *
 */
class IdentificacaoController
{

    private $dao;

    private $view;

    public static function main($nivelDeAcesso)
    {
        switch ($nivelDeAcesso) {
            case Sessao::NIVEL_SUPER:
                $controller = new IdentificacaoController();
                $controller->telaIdentificacao();
                break;
            case Sessao::NIVEL_ADMIN:
                $controller = new IdentificacaoController();
                $controller->telaIdentificacao();
                break;
            case Sessao::NIVEL_GUICHE:
                $controller = new IdentificacaoController();
                $controller->telaIdentificacao();
                break;
            case Sessao::NIVEL_POLIVALENTE:
                $controller = new IdentificacaoController();
                $controller->telaIdentificacao();
                break;
            case Sessao::NIVEL_CADASTRO:
                $controller = new IdentificacaoController();
                $controller->telaIdentificacao();
                break;
            case Sessao::NIVEL_CATRACA_VIRTUAL:
                $controller = new IdentificacaoController();
                $controller->telaIdentificacao();
                break;
            default:
                UsuarioController::main($nivelDeAcesso);
                break;
        }
    }

    public function __construct()
    {
        $this->view = new IdentificacaoView();
        $this->dao = new VinculoDAO();
    }

    public function telaIdentificacao()
    {
        $this->view->formBuscaCartao();

        if (! isset($_GET['numero_cartao'])) {
            return;
        }
        if (strlen($_GET['numero_cartao']) < 3) {
            return;
        }
        $cartao = new Cartao();
        $cartao->setNumero($_GET['numero_cartao']);
        $vinculo = $this->dao->vinculoDoCartao($cartao);
        if (! $vinculo) {
            echo "Inexistente";
            return;
        }
        $imagem = "sem-imagem";

        if (file_exists('fotos/' . $vinculo->getResponsavel()->getIdBaseExterna() . '.png')) {
            $imagem = $vinculo->getResponsavel()->getIdBaseExterna();
        }

        $this->view->exibirIdentificacao($vinculo, $imagem);
        if ($vinculo->isActive()) {
            return;
        }
        
        if(!isset($_GET['cartao_renovar'])){
            $this->view->formRenovacao($vinculo);
            return;
        }
        
        if($vinculo->isAvulso())
        {
            $this->view->mensagemErro("Não existe renovação de vínculo avulso.");
            echo '<meta http-equiv="refresh" content="4; url=.\?pagina=identificacao&numero_cartao=' . $vinculo->getCartao()->getNumero() . '">';
            return;
        }
        if($this->dao->usuarioJaTemVinculo($vinculo->getResponsavel()))
        {
            $this->view->mensagemErro("Esse usuário já possui vínculo válido.");
            echo '<meta http-equiv="refresh" content="4; url=.\?pagina=identificacao&numero_cartao=' . $vinculo->getCartao()->getNumero() . '">';
            return;
        }
        
        if(!isset($_POST['certeza'])){
            $this->view->formCerteza();
            return;
        }

        
        $validacaoDao = new ValidacaoDAO();
        if(!$validacaoDao->verificaSeAtivo($vinculo->getResponsavel())){
            $this->view->mensagemErro("Esse usuário possui um problema quanto ao status!");
            echo '<meta http-equiv="refresh" content="4; url=.\?pagina=cartao&selecionado=' . $vinculo->getResponsavel()->getIdBaseExterna() . '">';
            return;
        }
        
        $daqui3Meses = date ( 'Y-m-d', strtotime ( "+60 days" ) ) . 'T' . date ( 'G:00:01' );
        $vinculo->setFinalValidade($daqui3Meses);
        
        if($this->dao->atualizaValidade($vinculo))
        {
            $this->view->mensagemSucesso("Vínculo Atualizado com Sucesso!");
        }else{
            $this->view->mensagemErro("Erro ao tentar renovar vínculo.");
        }
        echo '<meta http-equiv="refresh" content="2; url=.\?pagina=cartao&selecionado=' . $vinculo->getResponsavel()->getIdBaseExterna() . '">';
        return;
        
    }
    
    
}

?>