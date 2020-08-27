/**
 * 
 */
$(function() {

	/*
	 * Combo dinamico pagina mensagem
	 */
	$("#catraca").change(function() {
		$("#mensagens").load("combo.php?catraca=" + $(this).val());
		// $("#turno").load("combo.php?catraca_id="+$(this).val());
	});
	// ========================================================//

	/*
	 * Combo pagina relatório
	 */
	$("#unidade").change(function() {
		$("#catraca").load("combo.php?unidade=" + $(this).val());
	});
	// ========================================================//

	$("#campo").change(function(){		
		$("#valor").load("combo.php?campo=" + $(this).val());
	});
	
});