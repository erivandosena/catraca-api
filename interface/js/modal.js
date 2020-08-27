$(document).ready(function(){
//    $("#janela1").length(function(ev){
//        ev.preventDefault();
 
        var id = $(this).attr("id");
        
        var alturaTela = $("#caixa").height();
		var larguraTela = $("#caixa").width();
 
//        var alturaTela = $(document).height();
//        var larguraTela = $(window).width();
     
        //colocando o fundo preto
        $('#mascara').css({'width':larguraTela,'height':alturaTela});
        $('#mascara').fadeIn(1000); 
        $('#mascara').fadeTo("slow",0.8);
 
        var left = ($("#caixa").width() /2) - ( $('#janela1').width() / 2 );
        var top = ($("#caixa").height() / 2) - ( $('#janela1').height() / 2 );
     
        $('#janela1').css({'top':top,'left':left});
        $('#janela1').show();   
//    });
 
    if($("#msgconfirmado").length){
        $("#mascara").hide();
        $(".window").hide();
    }
 
    $('.fechar').click(function(ev){
        ev.preventDefault();
        $("#mascara").hide();
        $(".window").hide();
        window.location.href = "?pagina=guiche";
    });
});