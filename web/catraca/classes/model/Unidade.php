<?php


/**
 * � a Unidade Academica. 
 * Ser� usada em muitos lugares. 
 * Turno tem unidade academica, catraca tem unidade, etc. 
 * @author jefponte
 *
 */
class Unidade{
	private $id;
	private $nome;
	private $turnosValidos;
	private $catracas;
	
	public function Unidade(){
		$this->turnosValidos = array();
	}
	public function setTurnosValidos($lista){
		$this->turnosValidos;
	}
	
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
	
	
}