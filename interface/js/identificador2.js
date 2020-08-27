$(function(){
	
	var tempo = window.setInterval(carrega, 1000);
	
	function carrega(){$('#usuario').load('?pagina=identificador #usuario');}
	
//	var altura = $(window).height();
//	var largura = $(window).width();
//	alert('Altura:'+altura+',Largura:'+largura);
	
	if ($("div#dados").length) {
		$("span#aproxime").css("display","none");		
	}else{
		$("span#aproxime").css("display","block");		
	}	
	
	if ($("div#usuario").length) {
		var altura = $(window).height();
		var largura = $(window).width();
		
		$("#topo").css("display","none");
		$("#barra-governo").css("display","none");
		$(".acessibilidade").css("display","none");		
		$(".barra-menu").css("display","none");
		$(".config").css({	"max-width": largura, "height": altura })
		
		
		
//		$(document).keydown(function(e){
//			if(e.wich == 122 || e.keyCode == 122){
//				window.location.href="?pagina=identificador_cliente"			
//			}
//		});
//		
//		$(document).keydown(function(e){
//			if(e.wich == 27 || e.keyCode == 27){
//				window.location.href="?pagina=gerador"
//			}
//		});
		
	}
	
	var tempo2 = window.setInterval(carrega2, 1000);	
	function carrega2(){$('#resumo').load('?pagina=resumo_compra #resumo');}
	
	if ($("#resumo").length){		
		$("#topo").css("display","none");
		$("#barra-governo").css("display","none");
		$(".acessibilidade").css("display","none");		
		$(".barra-menu").css("display","none");
	}
	
});