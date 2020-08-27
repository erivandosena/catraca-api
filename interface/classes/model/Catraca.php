<?php

class Catraca{
	
	private $id;
	private $nome;
	private $ip;
	private $tempoDeGiro;
	private $operacao;
	private $macLan;
	private $macWlan;
	private $interfaceRede;
	private $unidade;
	private $financeiro;
	public function __construct(){
		$this->unidade = new Unidade();
	}
	public function getStrFincaneito(){
		$financeiro = "";
		switch ($this->financeiroAtivo()){
			case true:
				$financeiro = "Habilitado";
				break;
			case false:
				$financeiro = "Desabilitado";
				break;
			default:
				$financeiro = "Não Identificado";
				break;
		}return $financeiro;
	}
	public function setUnidade(Unidade $unidade){
		$this->unidade = $unidade;
	}
	public function getUnidade(){
		return $this->unidade;
	}	
	
	public function setId($id){
		$this->id = intval($id);
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
	
	public function setIp($ip){
		$this->ip = $ip;
	}
	public function getIp(){
		return $this->ip;
	}
	public function setTempoDeGiro($tempoDeGiro){
		$this->tempoDeGiro = $tempoDeGiro;
	}
	public function getTempodeGiro(){
		return $this->tempoDeGiro;
	}
	public function setOperacao($operacao){
		$this->operacao = $operacao;
	}
	public function getOperacao(){
		return $this->operacao;
	}
	
	public function getStrOperacao(){
		$strOperacao = "Não Listado";
		switch ($this->getOperacao()){
			case self::GIRO_ANTI_VAL_HOR_BLOQ:
				$strOperacao = "Giro Anti-Horário";
				break;
			case self::GIRO_HOR_VAL_ANTI_BLOQ:
				$strOperacao = "Giro Horário";
			break;
			case self::GIRO_LIVRE:
				$strOperacao = "Giro Livre";
				break;
			case self::GIRO_ANTI_LIVRE_HOR_VAL:
				$strOperacao = "Anti-Horário(LIVRE) / Horário(VALIDADO)";
				break;
			case self::GIRO_HOR_LIVRE_ANTI_VAL:
				$strOperacao = "Horário(LIVRE) / Anti-Horário(VALIDADO)";
				break;
			default:
				$strOperacao = "Não Listado";
			break;	
		}
		return $strOperacao;
	}
	
	public function setMacLan($macLan){
		$this->macLan = $macLan;
		
	}
	
	public function getMacLan(){
		return $this->macLan;
	}
	
	public function setMacWlan($macWlan){
		$this->macWlan = $macWlan;
	}
	
	public function getMacWlan(){
		return $this->macWlan;
	}
	
	public function setInterfaceRede($InterfaceRede){
		$this->interfaceRede = $InterfaceRede;
	}
	
	public function getInterfaceRede(){
		return $this->interfaceRede;
	}
	
	public function getStrIterfaceRede(){
		$interface = "Não Identificado";
		switch ($this->getInterfaceRede()){
			case 'eth0':
				$interface = "Rede Cabeada";
			break;
			case 'wlan0':
				$interface = "Rede Sem Fio";
			break;
			default:
				$interface = "Não Identificado";
			break;
		}
		return $interface;
	}
	/*
	 * 1 = Horario Validado -> Anti-horário Bloqueado;
	 * 2 = Anti-horário Validado -> Horario Bloqueado;
	 * 3 = Anti-horário Livre -> Horario Validado;
	 * 4 = Hoário Livre -> Anti-horário Validado;
	 * 5 = Livre em abos os sentidos.
	 */
	 public function __toString(){
		$str = $this->getStrOperacao();
		$str .= $this->getUnidade()->getNome();
		if($this->financeiroAtivo())
			$str .= "Financeiro Ativo";
		else
			$str .= "Sem financeiro";
		return $str;
	}
		public function setFinanceiro($financeiro){
		
		if($financeiro)
			$this->financeiro = true;
		else
			$this->financeiro = false;
		
	}
	public function financeiroAtivo(){
		if($this->financeiro)
			return true;
		return false;
		
	}
	const GIRO_HOR_VAL_ANTI_BLOQ = 1;
	const GIRO_ANTI_VAL_HOR_BLOQ = 2;
	const GIRO_ANTI_LIVRE_HOR_VAL = 3;
	const GIRO_HOR_LIVRE_ANTI_VAL = 4;
	const GIRO_LIVRE = 5;
	
}


?>