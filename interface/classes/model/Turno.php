<?php


class Turno{
	private $id;
	private $horaInicial;
	private $horaFinal;
	private $descricao;
	
	
	public function setId($id){
		$this->id = intval($id);
	}
	public function getId(){
		return $this->id;
	}
	public function setHoraInicial($horaInicial){
		$this->horaInicial = $horaInicial;
	}
	public function getHoraInicial(){
		return $this->horaInicial;
	}
	
	public function setHoraFinal($horaFinal){
		$this->horaFinal = $horaFinal;
	}
	public function getHoraFinal(){
		return  $this->horaFinal;
		
	}
	public function setDescricao($descricao){
		$this->descricao = $descricao;
	}
	public function getDescricao(){
		return $this->descricao;
	}
	
	
	
	
}



?>