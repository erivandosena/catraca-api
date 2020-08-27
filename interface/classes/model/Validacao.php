<?php 

/**
 * 
 * @author Jefferson Uchôa Ponte
 *
 */
class Validacao {
	
	private $id;
	private $campo;
	private $valor;
	private $tipo;
	
	public function __construct(){
	    $this->tipo = new Tipo();
	}
		
	public function getId(){
		return $this->id;
	}
	
	public function setId($id){
		$this->id = $id;
	}
	
	public function getCampo(){
		return $this->campo;	
	}
	
	public function setCampo($campo){
		$this->campo = $campo;
	}
	
	public function getValor(){
		return $this->valor;
	}
	
	public function setValor($valor){
		$this->valor = $valor;
	}
	
	/**
	 * @return Tipo
	 */
	public function getTipo(){
	    return $this->tipo;
	}
	/**
	 * @param Tipo $tipo
	 */
	public function setTipo(Tipo $tipo){
	    $this->tipo = $tipo;
	}
	
	
	
}

?>