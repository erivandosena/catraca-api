<?php

/**
 * � a Unidade Academica. 
 * Ser� usada em muitos lugares. 
 * Turno tem unidade academica, catraca tem unidade, etc. 
 * @author jefponte
 *
 */
class Unidade {
	private $id;
	private $nome;
	private $turnosValidos;
	private $catracas;
	private $custoUnidade;
	
	public function Unidade() {
		$this->turnosValidos = array();
		
	}
	

	
	public function getTurnosValidos() {
		return $this->turnosValidos;
	}
	

	public function adicionaTurno(Turno $turno){
		$this->turnosValidos[] = $turno;
	}
	public function setId($id) {
		$this->id = intval ( $id );
	}
	public function getId() {
		return $this->id;
	}
	public function setNome($nome) {
		$this->nome = $nome;
	}
	public function getNome() {
		return $this->nome;
	}
	public function getCustoUnidade(){
		return $this->custoUnidade;
	}
	
	public function setCustoUnidade($custoUnidade){
		$this->custoUnidade = $custoUnidade;
	}
}