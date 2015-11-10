<?php 
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);


function __autoload($classe) {
	if (file_exists ( 'classes/dao/' . $classe . '.php' ))
		include_once 'classes/dao/' . $classe . '.php';
	if (file_exists ( 'classes/model/' . $classe . '.php' ))
		include_once 'classes/model/' . $classe . '.php';
	if (file_exists ( 'classes/controller/' . $classe . '.php' ))
		include_once 'classes/controller/' . $classe . '.php';
	if (file_exists ( 'classes/util/' . $classe . '.php' ))
		include_once 'classes/util/' . $classe . '.php';
	if (file_exists ( 'classes/view/' . $classe . '.php' ))
		include_once 'classes/view/' . $classe . '.php';


}

$sessao = new Sessao ();

if (isset ( $_GET ["sair"] )) {

	$sessao->mataSessao ();
	header ( "Location:/interface/index.php" );
}

?>
<!DOCTYPE html>
<html lang="pt-BR" xml:lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Catraca</title>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<!-- meta tag para responsividade em Windows e Linux -->
	<link rel="stylesheet" href="http://spa.dsi.unilab.edu.br/spa/css/spa.css" />
	<link rel="stylesheet" href="css/style.css" />

</head>
<body>
<div class="pagina fundo-cinza1">
    <div id="barra-governo">
        <div class="resolucao">
           <div class="a-esquerda">
              <a href="http://brasil.gov.br/" target="_blank"><span id="bandeira"></span><span>BRASIL</span></a>
              <a href="http://acessoainformacao.unilab.edu.br/" target="_blank">Acesso à informação</a>
           </div>
           <div class="a-direita"><a href="#"><i class="icone-menu"></i></a></div>
           <ul>
              <li><a href="http://brasil.gov.br/barra#participe" target="_blank">Participe</a></li>
              <li><a href="http://www.servicos.gov.br/" target="_blank">Serviços</a></li>
              <li><a href="http://www.planalto.gov.br/legislacao" target="_blank">Legislação</a></li>
              <li><a href="http://brasil.gov.br/barra#orgaos-atuacao-canais" target="_blank">Canais</a></li>
           </ul>
        </div>
    </div>
<?php 

if($sessao->getNivelAcesso() != Sessao::NIVEL_SUPER){
	UsuarioController::main(Sessao::NIVEL_DESLOGADO);
	
}else{
	echo '<div class="doze colunas barra-menu">
    <div class="menu-horizontal resolucao">
        <ol class="a-esquerda">
            <li><a href="?pagina=inicio" class="item-ativo"><span class="icone-home3"></span> <span class="item-texto">Início</span></a></li>
            <li><a href="?pagina=unidade"><span class="icone-drawer"></span> <span class="item-texto">Unidade Academica</span><span class="icone-expande"></span></a> </li>
            <li><a href="?pagina=turno"><span class="icone-stack"></span> <span class="item-texto">Turno</span><span class="icone-expande"></span></a></li>
		    <li><a href="?pagina=cartao"><span class="icone-stack"></span> <span class="item-texto">Cartao</span><span class="icone-expande"></span></a></li>
		
        </ol>
        <ol class="a-direita" start="6">
            <li><a href="?sair=daqui" class="item"><span class="icone-exit"></span> <span class="item-texto">Sair</span></a></li>
        </ol>
    </div>
</div>
	
	
	
	
				  <div class="doze colunas margem-acima">
            	    <div class="conteudo resolucao">
                 	   <div class="doze colunas">
				
	
				
				';
	
	echo '<br><br><br><br><br><br>';

	if(isset($_GET['pagina']))
	
		switch ($_GET['pagina']){
			case "unidade":
				UnidadeController::main();
				break;
			case "unidade_detalhe":
				UnidadeController::mainDetalhe();
				break;
			case "turno":
				TurnoController::main();
				break;
			case "cartao":
				CartaoController::main();
				break;
			default:
				UnidadeController::main();
				break;
	}
	else
		UnidadeController::main();
	

	
	
	
	
	
}


?>

</div></body></html>

