$(document).ready(function(){	
	
	$("#valor").val(0,00);
	$("#valorrec").val(0,00);
	$("#troco").text("0.00").css('color', '#0000FF');

	$("#valor, #valorrec").keyup(function(event) {
		
		var valor = $("#valor").val();
		var valorRec = $("#valorrec").val();
		var troco = valorRec - valor;	
		
		if(troco < 0){
			$("#troco").text("0.00").css('color', '#0000FF');
		}else {
			$("#troco").text(troco.toFixed(2)).css('color', '#0000FF');;
		}					
		
	});
	
});