<?php

define ( "CONFIG_CATRACA", "/dados/sites/adm/catraca/config/catraca.ini" );
$config = parse_ini_file ( CONFIG_CATRACA );
define ( "CADASTRO_DE_FOTOS", $config ['cadastro_de_fotos'] );
define ( "NOME_INSTITUICAO", $config ['nome_instituicao'] );
define ( "PAGINA_INSTITUICAO", $config ['pagina_instituicao'] );
define ( "LOGIN_LDAP", $config ['login_ldap'] );
define ( "FONT_DADOS_LDAP_ENTIDADE", $config ['font_dados_ldap_entidade'] );
define ( "VERSAO_SINCRONIZADOR", $config ['versao_sincronizador'] );



function __autoload($classe) {
    if (file_exists ( 'classes/dao/' . $classe . '.php' )){
		include_once 'classes/dao/' . $classe . '.php';
    }
    else if (file_exists ( 'classes/model/' . $classe . '.php' )){
		include_once 'classes/model/' . $classe . '.php';
    }
    else if (file_exists ( 'classes/controller/' . $classe . '.php' )){
		include_once 'classes/controller/' . $classe . '.php';
    }
    else if (file_exists ( 'classes/util/' . $classe . '.php' )){
		include_once 'classes/util/' . $classe . '.php';
    }
    else if (file_exists ( 'classes/view/' . $classe . '.php' )){
		include_once 'classes/view/' . $classe . '.php';
    }
}

$sessao = new Sessao ();

if (isset ( $_GET ["sair"] )) {
	$sessao->mataSessao ();
	header ( "Location:./index.php" );
}
if (VERSAO_SINCRONIZADOR == 1) {
	$s = new SincronizadorController ();
	$s->sincronizar ();
} else {
	// Aqui faremos sincronizacao se for o da UECE.
}

if (isset ( $_GET ['gerar'] ) && isset ( $_GET ['pagina'] )) {
    if($_GET['gerar'] == 'Excel'){
        switch ($_GET ['pagina']) {
            case 'relatorio_despesa' :
                RelatorioDespesaController::main($sessao->getNivelAcesso());
                exit(0);
                break;
            case 'relatorio_arrecadacao':
                RelatorioArrecadacaoController::main($sessao->getNivelAcesso());
                exit(0);
                break;
            case 'relatorio_consumo':
                RelatorioConsumoController::main($sessao->getNivelAcesso());
                exit(0);
                break;
                
        }
    }
    
}


?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>

<meta charset="UTF-8">
<meta name="viewport"
	content="width=device-width, height=device-height, initial-scale=1, maximum-scale=1, user-scalable=no" />

<title>Projeto Catraca</title>

<script type="text/javascript" src="js/simpletabs_1.3.js"></script>
<link rel="stylesheet" href="css/simpletabs.css" />
<link rel="stylesheet" href="css_spa/spa.css" />
<link rel="stylesheet" href="css/estilo_comum.css" type="text/css" media="screen">
<?php
echo '<link rel="stylesheet" href="css/estilo_' . NOME_INSTITUICAO . '.css" type="text/css" media="screen">';

?>

<link rel="stylesheet" href="css/estilo_responsivo.css" type="text/css"
	media="screen">
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/mostra_troco.js"></script>
<script type="text/javascript" src="js/modal.js"></script>
<script type="text/javascript" src="js/identificador.js"></script>

</head>
<body>
	<div class="pagina fundo-cinza1">
		<div class="acessibilidade">
			<div class="config">
				<div class="a-esquerda">
					<!-- 					<a href="#conteudo" tabindex="1" accesskey="1">Ir para o conteúdo <b>1</b></a> -->
					<!-- 					<a href="#menu" tabindex="2" accesskey="2"><span>Ir para o</span> menu <b>2</b></a> -->
					<!-- 					<a href="#busca" tabindex="3" accesskey="3"><span>Ir para a</span> busca <b>3</b></a> -->
					<!-- 					<a href="#rodape" tabindex="4" accesskey="4"><span>Ir para o</span> rodapé <b>4</b></a> -->
				</div>
				<div class="a-direita">
					<!-- 					<a href="#" id="alto-contraste">ALTO <b>CONTRASTE</b></a> -->
					<!-- 					<a href="#" id="mapa-do-site"><b>MAPA DO SITE</b></a> -->
				</div>
			</div>
		</div>
		<div id="barra-governo">
			<div class="resolucao config">
				<div class="a-esquerda">
					<a href="http://brasil.gov.br/" target="_blank"><span id="bandeira"></span><span>BRASIL</span></a>
					<a href="http://acessoainformacao.unilab.edu.br/" target="_blank">Acesso
						&agrave;  informa&ccedil;&atilde;o</a>
				</div>
				<div class="a-direita">
					<a href="#"><i class="icone-menu"></i></a>
				</div>
				<ul>
					<li><a href="http://brasil.gov.br/barra#participe" target="_blank">Participe</a></li>
					<li><a href="http://www.servicos.gov.br/" target="_blank">Servi&ccedil;os</a></li>
					<li><a href="http://www.planalto.gov.br/legislacao" target="_blank">Legisla&ccedil;&atilde;o</a></li>
					<li><a href="http://brasil.gov.br/barra#orgaos-atuacao-canais"
						target="_blank">Canais</a></li>
				</ul>
			</div>
		</div>
		<div class="doze colunas gradiente">
			<div id="topo" class="resolucao ">
				<div class="tres colunas">
				<?php
				echo '<a href="' . PAGINA_INSTITUICAO . '"><img
						src="img/logo_instituicao_' . NOME_INSTITUICAO . '.png"
						alt=""></a>';
				?>
				</div>
				<div class="seis colunas centralizado">
					<h1>CATRACA<br> <small class="texto-branco">Controle Administrativo de Tr&aacute;fego Acadêmico Automatizado</small></h1>
				</div>
				<div class="tres colunas alinhado-a-direita">
					<a href="http://www.unilab.edu.br"><img src="img/logo_labpati_branco.png" alt=""></a>
				</div>
			</div>
		</div>
				<?php
				function auditar() {
					$dao = new DAO ();
					$sessao = new Sessao ();
					$auditoria = new Auditoria ( $dao->getConexao () );
					
					$obs = " - ";
					if (isset ( $_POST ['catraca_virtual'] ) && isset ( $_POST ['catraca_id'] )) {
						$obs = "Selecionou Catraca virtual: " . $_POST ['catraca_id'];
					}
					
					$auditoria->cadastrar ( $sessao->getIdUsuario (), $obs );
					$dao->fechaConexao ();
				}
				
				MenuController::main($sessao->getNivelAcesso());
				
				?>
				
			

		<div class="doze colunas">
			<div class="resolucao config">					
						
					<?php
					
					if (isset ( $_GET ['pagina'] )) {
					    auditar();
						switch ($_GET ['pagina']) {
						    case 'identificacao' :
						        IdentificacaoController::main($sessao->getNivelAcesso());
						        break;
						    case 'cartao_proprio':
						        CartaoProprioController::main($sessao->getNivelAcesso());
						        break;
						    case 'definicoes_unidade' :
						        UnidadeController::main($sessao->getNivelAcesso());
						        break;
						    case 'definicoes_turno' :
						        TurnoController::main($sessao->getNivelAcesso());
						        break;
						    case 'definicoes_catraca' :
						        CatracaController::main($sessao->getNivelAcesso());
						        break;
						    case 'definicoes_tipo' :
						        TipoController::main($sessao->getNivelAcesso());
						        break;
						    case 'definicoes_mensagem' :
						        MensagemController::main($sessao->getNivelAcesso());
						        break;
						    case 'definicoes_custo' :
						        CustoController::main($sessao->getNivelAcesso());
						        break;
							case 'inicio' :
								HomeController::main ( $sessao->getNivelAcesso () );
								break;
							case 'cartao' :
								CartaoController::main ( $sessao->getNivelAcesso () );
								break;
							case 'gerador' :
								CatracaVirtualController::main ( $sessao->getNivelAcesso () );
								break;
							case 'relatorio_despesa' :
								RelatorioDespesaController::main ( $sessao->getNivelAcesso () );
								break;
							case 'guiche' :
								GuicheController::main ( $sessao->getNivelAcesso () );
								break;
							
							case 'nivel_acesso' :
								NivelAcessoController::main ( $sessao->getNivelAcesso () );
								break;
							case 'avulso' :
								CartaoAvulsoController::main ( $sessao->getNivelAcesso () );
								break;
							case 'isento' :
								CartaoIsentoController::main ( $sessao->getNivelAcesso () );
								break;
							case 'relatorio_guiche' :
								RelatorioControllerGuiche::main ( $sessao->getNivelAcesso () );
								break;
							case 'info' :
								InfoController::main ( $sessao->getNivelAcesso () );
								break;
							case 'identificador' :
								IdentificadorClienteController::main ( $sessao->getNivelAcesso () );
								break;
							case 'pessoal' :
								PessoalController::main ( $sessao->getNivelAcesso () );
								break;
							case 'resumo_compra' :
								ResumoCompraController::main ( $sessao->getNivelAcesso () );
								break;
							case 'relatorio_turno' :
								RelatorioTurnoController::main ( $sessao->getNivelAcesso () );
								break;
							case 'relatorio_registro' :
								RelatorioRegistroController::main ( $sessao->getNivelAcesso (), $sessao->getIdUsuario () );
								break;
							case 'registro_orfao' :
								RegistroOrfaoController::main ( $sessao->getNivelAcesso () );
								break;
							case 'validacao' :
								ValidacaoController::main ( $sessao->getNivelAcesso () );
								break;
							case 'relatorio_arrecadacao':
							    RelatorioArrecadacaoController::main($sessao->getNivelAcesso());
							    break;
							case 'relatorio_consumo':
							    RelatorioConsumoController::main($sessao->getNivelAcesso());
							    break;
							case 'relatorio_avulso':
							    RelatorioAvulsoController::main($sessao->getNivelAcesso());
							    break;
							default :
								echo '404 NOT FOUND';
								break;
						}
					} else {
						
						HomeController::main ( $sessao->getNivelAcesso () );
					}
					
					?>
			</div>
			<script type="text/javascript">
        	var img;

        	navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia;
        	
            var canvas = document.getElementById("canvas"),
                context = canvas.getContext("2d"),
                video = document.getElementById("video"),
                btnStart = document.getElementById("btnStart"),
                btnStop = document.getElementById("btnStop"),
                btnPhoto = document.getElementById("btnPhoto"),
                videoObj = {
                    video: true,
                    audio: false
                };
            
	        
				 //Compatibility
	            
	
	            btnStart.addEventListener("click", function() {
	                var localMediaStream;
	
	                if (navigator.getUserMedia) {
	                    navigator.getUserMedia(videoObj, function(stream) {              
	                        video.src = (navigator.webkitGetUserMedia) ? window.webkitURL.createObjectURL(stream) : stream;
	                        localMediaStream = stream;
	                        
	                    }, function(error) {
	                        console.error("Video capture error: ", error.code);

	                    });
	                	
	                    btnStop.addEventListener("click", function() {
	                        localMediaStream.stop();
	                        
	                    });
	
	                    btnPhoto.addEventListener("click", function() {
	                        context.drawImage(video, 0, 0, 320, 240);

	                        img = canvas.toDataURL("image/png");
	                        formulario.img64.value = img;

							
	                    });

	                    
	                }
	            });
        </script>
		</div>
	</div>
</body>
</html>