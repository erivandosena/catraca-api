<?php

class MenuController
{

    public static function main($nivelDeAcesso)
    {
        $controller = new MenuController();
        $controller->exibirMenu($nivelDeAcesso);
    }

    public function exibirMenu($nivelDeAcesso)
    {
        switch ($nivelDeAcesso) {

            case Sessao::NIVEL_ADMIN:
                echo '
							<div  class="doze colunas barra-menu">
								    <div class="menu-horizontal config">
								        <ol class="a-esquerda">';

                echo '<li><a href="?pagina=inicio" class="item-ativo"><span class="icone-home3"></span> <span class="item-texto">Início</span></a></li>';

                echo '<li><a href="?pagina=cartao" class="item"><span class="icone-credit-card"></span> <span class="item-texto">Cartão</span></a>
									<ul>
										<li><a href="?pagina=identificacao">Identificação</a></li>
										<li><a href="?pagina=cartao">Próprio</a></li>
										<li><a href="?pagina=avulso">Avulso</a></li>
										<li><a href="?pagina=isento">Isento</a></li>
										<li><a href="?pagina=info">Informações</a></li>
                    
									</ul>
                    
							</li>';
                echo '	<li><a href="?pagina=gerador" class="item"><span class="icone-credit-card"></span> <span class="item-texto">Catraca Virtual</span></a></li>';
                echo ' 	<li><a href="?pagina=guiche" class="item"><span class="icone-user"></span> <span class="item-texto">Guichê</span></a>
								<ul>
										<li><a href="?pagina=resumo_compra" target="_blank">Tela de Atendimento</a></li>
									</ul>
                    
								</li>';

                echo ' 	<li><a href="?pagina=definicoes_unidade" class="item"><span class="icone-cogs"></span> <span class="item-texto">Definições</span></a>
                    
										<ul>
											<li><a href="?pagina=definicoes_unidade">Unidades Acadêmicas</a></li>
                                            <li><a href="?pagina=definicoes_turno">Turnos</a></li>
                                            <li><a href="?pagina=definicoes_catraca">Catracas</a></li>
                                            <li><a href="?pagina=definicoes_tipo">Tipos de Usuários</a></li>
                                            <li><a href="?pagina=definicoes_mensagem">Mensagem Catraca</a></li>
                                            <li><a href="?pagina=definicoes_custo">Custo Refeição</a></li>';
                    
// 											<li><a href="?pagina=validacao">Validações</a></li>
                echo '                  </ul>
                    
                    
							</li>';

                echo ' 	<li><a href="#" class="item"><span class="icone-file-text2"></span> <span class="item-texto">Relatórios</span></a>
									<ul>
										<li><a href="?pagina=relatorio_despesa">Relatório Despesa</a></li>
                                        <li><a href="?pagina=relatorio_arrecadacao">Relatório Arrecadação</a></li>
                                        <li><a href="?pagina=relatorio_consumo">Relatório Consumo</a></li>
                                        <li><a href="?pagina=relatorio_avulso">Relatório Avulso</a></li>
										<li><a href="?pagina=relatorio_guiche">Relatório Guichê</a></li>
										<li><a href="?pagina=pessoal">Histórico Pessoal</a></li>
										<li><a href="?pagina=relatorio_turno">Por Turno</a></li>
                                        <li><a href="?pagina=relatorio_registro">Registros</a></li>
									</ul>
								</li>';
                echo ' <li><a href="?pagina=nivel_acesso" class="item"><span class="icone-file-text2"></span> <span class="item-texto">Nivel de Acesso</span></a></li>';

                echo '</ol>
								        <ol class="a-direita" start="4">
                    
											<li><a href="" class="item"><span class="item-texto">Status: Adm</span></a></li>
											<li><a href="?sair=sair" class="item"><span class="icone-exit"></span> <span class="item-texto">Sair</span></a></li>
								        </ol>
								    </div>
								</div>';
                break;

            case Sessao::NIVEL_POLIVALENTE:
                echo '
							<div  class="doze colunas barra-menu">
								    <div class="menu-horizontal config">
								        <ol class="a-esquerda">';

                echo '<li><a href="?pagina=inicio" class="item-ativo"><span class="icone-home3"></span> <span class="item-texto">Início</span></a></li>';

                echo '<li><a href="?pagina=cartao" class="item"><span class="icone-credit-card"></span> <span class="item-texto">Cartão</span></a>
							</li>';
                echo '	<li><a href="?pagina=gerador" class="item"><span class="icone-credit-card"></span> <span class="item-texto">Catraca Virtual</span></a>
									<ul>
										<li><a href="?pagina=resumo_compra">Tela de Atendimento</a></li>
									</ul>
                    
									</li>';
                echo ' 	<li><a href="?pagina=guiche" class="item"><span class="icone-user"></span> <span class="item-texto">Guichê</span></a>
								<ul>
										<li><a href="?pagina=resumo_compra" target="_blank">Tela de Atendimento</a></li>
									</ul>
                    
								</li>';

                echo ' 	<li><a href="?pagina=relatorio_despesa" class="item"><span class="icone-file-text2"></span> <span class="item-texto">Relatório</span></a>
									<ul>
										<li><a href="?pagina=relatorio_despesa">Relatório RU</a></li>
									</ul>
								</li>';

                echo '</ol>
								        <ol class="a-direita" start="4">
                    
											<li><a href="" class="item"><span class="item-texto">Status: Pol</span></a></li>
											<li><a href="?sair=sair" class="item"><span class="icone-exit"></span> <span class="item-texto">Sair</span></a></li>
								        </ol>
								    </div>
								</div>';
                break;

            case Sessao::NIVEL_GUICHE:
                echo '
							<div  class="doze colunas barra-menu">
								    <div class="menu-horizontal config">
								        <ol class="a-esquerda">';

                /*
                 * Como deveria ser.
                 */
                echo '<li><a href="?pagina=inicio" class="item-ativo"><span class="icone-home3"></span> <span class="item-texto">Início</span></a></li>';
                echo '<li><a href="?pagina=cartao" class="item"><span class="icone-credit-card"></span> <span class="item-texto">Cartão</span></a></li>';
                echo ' <li><a href="?pagina=guiche" class="item"><span class="icone-user"></span> <span class="item-texto">Guichê</span></a>
								<ul>
										<li><a href="?pagina=resumo_compra" target="_blank">Tela de Atendimento</a></li>
									</ul>
								</li>';

                echo '</ol>
								        <ol class="a-direita" start="4">
											<li><a href="" class="item"><span class="item-texto">Status: Guiche</span></a></li>
								            <li><a href="?sair=sair" class="item"><span class="icone-exit"></span> <span class="item-texto">Sair</span></a></li>
								        </ol>
								    </div>
								</div>';
                break;
            case Sessao::NIVEL_CATRACA_VIRTUAL:

                echo '
						<div  class="doze colunas barra-menu">
							    <div class="menu-horizontal config">
							        <ol class="a-esquerda">';

                echo '<li><a href="?pagina=inicio" class="item-ativo"><span class="icone-home3"></span> <span class="item-texto">Início</span></a></li>';
                echo '<li><a href="?pagina=cartao" class="item"><span class="icone-credit-card"></span> <span class="item-texto">Cartão</span></a></li>';

                echo '<li><a href="?pagina=gerador" class="item"><span class="icone-credit-card"></span> <span class="item-texto">Catraca Virtual</span></a></li>';
                echo ' <li><a href="?pagina=relatorio_despesa" class="item"><span class="icone-file-text2"></span> <span class="item-texto">Relatório</span></a></li>';

                echo '</ol>
							        <ol class="a-direita" start="4">
										<li><a href="" class="item"><span class="item-texto">Status: Catraca Virtual</span></a></li>
							            <li><a href="?sair=sair" class="item"><span class="icone-exit"></span> <span class="item-texto">Sair</span></a></li>
							        </ol>
							    </div>
							</div>';
                break;
            case Sessao::NIVEL_CATRACA_VIRTUAL_ORFA:
                echo '
						<div  class="doze colunas barra-menu">
							    <div class="menu-horizontal config">
							        <ol class="a-esquerda">';

                echo '<li><a href="?pagina=inicio" class="item-ativo"><span class="icone-home3"></span> <span class="item-texto">Início</span></a></li>';

                echo '<li><a href="?pagina=registro_orfao" class="item"><span class="icone-credit-card"></span> <span class="item-texto">Catraca Virtual Órfã</span></a></li>';
                echo ' <li><a href="?pagina=relatorio_despesa" class="item"><span class="icone-file-text2"></span> <span class="item-texto">Relatório</span></a></li>';

                echo '</ol>
							        <ol class="a-direita" start="4">
										<li><a href="" class="item"><span class="item-texto">Status: Catraca Órfã</span></a></li>
							            <li><a href="?sair=sair" class="item"><span class="icone-exit"></span> <span class="item-texto">Sair</span></a></li>
							        </ol>
							    </div>
							</div>';

                break;
            case Sessao::NIVEL_CADASTRO:

                echo '
					<div  class="doze colunas barra-menu">
						    <div class="menu-horizontal config">
						        <ol class="a-esquerda">';

                echo '<li><a href="?pagina=inicio" class="item-ativo"><span class="icone-home3"></span> <span class="item-texto">Início</span></a></li>';
                echo '<li><a href="?pagina=cartao" class="item"><span class="icone-credit-card"></span> <span class="item-texto">Cartão</span></a></li>';

                echo '</ol>
						        <ol class="a-direita" start="4">
									<li><a href="" class="item"><span class="item-texto">Status: Cadastro</span></a></li>
						            <li><a href="?sair=sair" class="item"><span class="icone-exit"></span> <span class="item-texto">Sair</span></a></li>
						        </ol>
						    </div>
						</div>';
                break;

            case Sessao::NIVEL_CATRACA_VIRTUAL:

                echo '
						<div  class="doze colunas barra-menu">
							    <div class="menu-horizontal config">
							        <ol class="a-esquerda">';

                echo '<li><a href="?pagina=inicio" class="item-ativo"><span class="icone-home3"></span> <span class="item-texto">Início</span></a></li>';
                echo ' <li><a href="?pagina=catraca" class="item"><span class="icone-loop2"></span> <span class="item-texto">Catraca</span></a></li>';
                echo '<li><a href="?pagina=cartao" class="item"><span class="icone-credit-card"></span> <span class="item-texto">Cartão</span></a></li>';
                echo '<li><a href="?pagina=gerador" class="item"><span class="icone-credit-card"></span> <span class="item-texto">Catraca Virtual</span></a></li>';
                echo ' <li><a href="?pagina=relatorio_despesa" class="item"><span class="icone-file-text2"></span> <span class="item-texto">Relatório</span></a></li>';

                echo '</ol>
							        <ol class="a-direita" start="4">
										<li><a href="" class="item"><span class="item-texto">Status: Catraca Virtual</span></a></li>
							            <li><a href="?sair=sair" class="item"><span class="icone-exit"></span> <span class="item-texto">Sair</span></a></li>
							        </ol>
							    </div>
							</div>';
                break;
            case Sessao::NIVEL_RELATORIO:

                echo '
								<div  class="doze colunas barra-menu">
									    <div class="menu-horizontal config">
									        <ol class="a-esquerda">';

                echo '<li><a href="?pagina=inicio" class="item-ativo"><span class="icone-home3"></span> <span class="item-texto">Início</span></a></li>';
                echo ' <li><a href="?pagina=relatorio_despesa" class="item"><span class="icone-file-text2"></span> <span class="item-texto">Relatório - Despesa</span></a></li>';

                echo '</ol>
						        <ol class="a-direita" start="4">
									<li><a href="" class="item"><span class="item-texto">Status: Relatorio</span></a></li>
						            <li><a href="?sair=sair" class="item"><span class="icone-exit"></span> <span class="item-texto">Sair</span></a></li>
						        </ol>
						    </div>
						</div>';
                break;

            case Sessao::NIVEL_COMUM:
                echo '<div  class="doze colunas barra-menu">
								    <div class="menu-horizontal config">
								        <ol class="a-esquerda">';

                /*
                 * Como deveria ser.
                 */
                echo '<li><a href="?pagina=inicio" class="item-ativo"><span class="icone-home3"></span> <span class="item-texto">Início</span></a></li>';
                echo '<li><a href="?pagina=pessoal" class="item"><span class="icone-credit-card"></span> <span class="item-texto">Pessoal</span></a></li>';

                echo '</ol>
								        <ol class="a-direita" start="4">
											<li><a href="" class="item"><span class="item-texto">Status: Padrão</span></a></li>
								            <li><a href="?sair=sair" class="item"><span class="icone-exit"></span> <span class="item-texto">Sair</span></a></li>
								        </ol>
								    </div>
								</div>';
                break;
            default:
                break;
        }
    }
}

?>