<?php


class Tipo{
	private $id;
	private $nome;
	private $valorCobrado;
	
	public function setId($id){
		$this->id = $id;
	}
	public function getId(){
		return $this->id;
	}
	public function setNome($nome){
		$this->nome = $nome;
	}
	public function getNome(){
		return $this->nome;
	}
	public function setValorCobrado($valorCobrado){
		$this->valorCobrado = $valorCobrado;
	}
	public function getValorCobrado(){
		return $this->valorCobrado;
	}
	
	
	
}
?>