<?php

class Cartao{
	private $id;
	private $numero;
	private $creditos;
	private $tipo;
	
	public function Cartao(){
		$this->tipo = new Tipo();
	}
	
	public function setId($id){
		$this->id = $id;
		
	}
	public function getId(){
		return $this->id;
	}
	public function setNumero($numero){
		$this->numero = $numero;
		
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
}





?>