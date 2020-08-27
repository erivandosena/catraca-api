<?php

class Cartao{
	private $id;
	private $numero;
	private $creditos;
	private $tipo;
	
	
	
	public function __construct(){
		$this->tipo = new Tipo();
		$this->creditos = 0;
		$this->id = 0;
	}
	
	public function setId($id){
		$this->id = $id;
		
	}
	public function getId(){
		return $this->id;
	}
	public function setNumero($numero){
		$this->numero = preg_replace ('/[^0-9]/', '', $numero);
	}
	public function getNumero(){
		return $this->numero;
	}
	public function setCreditos($creditos){
		$this->creditos = $creditos;
	}
	public function getCreditos(){
		return $this->creditos;
	}
	public function setTipo(Tipo $tipo){
		$this->tipo = $tipo;
	}
	public function getTipo(){
		return $this->tipo;
	}
	public function adicionaCreditos($creditos){
		if(is_numeric($creditos)){
			$this->setCreditos($this->getCreditos()+$creditos);
		}
	}
}





?>