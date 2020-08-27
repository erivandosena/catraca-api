<?php 
/**
 * 
 * @author Jefferson Uchôa Ponte
 *
 */
class CatracaOrfaController{
    private $view;
    private $dao;
    
    public static function main($nivelAcesso){
        switch ($nivelAcesso) {
            case Sessao::NIVEL_SUPER :
                $telaRegistro = new RegistroOrfaoController();
                $telaRegistro->verificarSelecaoRU();
                break;
            case Sessao::NIVEL_ADMIN:
                $telaRegistro = new RegistroOrfaoController();
                $telaRegistro->verificarSelecaoRU();
                break;
            case Sessao::NIVEL_CATRACA_VIRTUAL_ORFA:
                $telaRegistro = new RegistroOrfaoController();
                $telaRegistro->verificarSelecaoRU();
                break;
            default :
                UsuarioController::main ( $nivelAcesso );
                break;
        }
        
    }

    public function __construct(){
        
    }
    
}




?>