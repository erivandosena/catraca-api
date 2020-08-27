<?php


class Vinculo{
	
	
	
	private $id;
	private $avulso;
	private $responsavel;
	private $inicioValidade;
	private $finalValidade;
	private $quantidadeDeAlimentosPorTurno;
	private $descricao;
	private $cartao;
	private $refeicoesRestantes;
	public function setRefeicoesRestantes($refeicoesRestantes){
		$this->refeicoesRestantes = intval($refeicoesRestantes);
	}
	public function getRefeicoesRestantes(){
		return $this->refeicoesRestantes;
		
	}
	
	private $isento;
	public $isencao;
	public function setIsento($isento){
		if($isento)
			$this->isento = true;
		else
			$this->isento = false;
	}
	
	public function setIsencao(Isencao $isencao){
		$this->isencao = $isencao;
	}
	public function getIsencao(){
		return $this->isencao;
	}
	
	
	public function Vinculo(){
		$this->setIsento(false);
		$this->isencao  = new Isencao();
		$this->setAvulso(false);
		$this->responsavel = new Usuario();
		$this->cartao= new Cartao();
		
	}
	public function ehIsento(){
		if($this->isento)
			return true;
		return false;
	}
	public function setInicioValidade($inicioValidade){
		$this->inicioValidade = $inicioValidade;
	}
	public function getInicioValidade(){
		return $this->inicioValidade;
	}
	public function setFinalValidade($finalValidade){
		$this->finalValidade = $finalValidade;
	}
	public function getFinalValidade(){
		return $this->finalValidade;
	}
	public function setId($id){
		$this->id = intval($id);
	}
	public function getId(){
		return $this->id;
	}
	public function setAvulso($avulso){
		if($avulso)
			$this->avulso = true;
		else
			$this->avulso = false;
	}
	public function isAvulso(){
		return $this->avulso;
	}
	public function setResponsavel(Usuario $usuario){
		$this->responsavel = $usuario;
	}
	public function getResponsavel(){
		return $this->responsavel;
	}
	public function setQuantidadeDeAlimentosPorTurno($quantidade){
		$this->quantidadeDeAlimentosPorTurno = intval($quantidade);
	}
	public function getQuantidadeDeAlimentosPorTurno(){
		return $this->quantidadeDeAlimentosPorTurno;
	}
	public function setDescricao($descricao){
		$descricao = preg_replace ('/[^a-zA-Z0-9\s]/', '', $descricao);
		$this->descricao = $descricao;
	}
	public function getDescricao(){
		return $this->descricao;
		
	}
	public function setCartao(Cartao $cartao){
		$this->cartao = $cartao;
	}
	public function getCartao(){
		return $this->cartao;
	}
	
	public function isActive(){
		$tempoA = strtotime($this->getInicioValidade());
		$tempoB = strtotime($this->getFinalValidade());
		$tempoAgora = time();
		if($tempoAgora > $tempoA && $tempoAgora < $tempoB)
			return true;
		return false;
	}
	public function invalidoParaAdicionar(){
		$time = strtotime(date("Y-m-d 01:00:00"));
		$tempo1 = strtotime($this->getInicioValidade());
		$tempo2 = strtotime($this->getFinalValidade());
		if($time > $tempo1 || $time > $tempo2)
			return true;
		return false;
		
	}
	
}


?>