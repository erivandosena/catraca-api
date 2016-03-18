<?php
class DAO {
	protected $conexao;
	private $tipoDeConexao;
	
	public function DAO($conexao = null, $tipo = self::TIPO_DEFAULT) {
		$this->tipoDeConexao = $tipo;
		if ($conexao != null) {
			$this->conexao = $conexao;
		} else {
			
			$this->fazerConexao();
		}
	}
	public function fazerConexao(){
		switch ($this->tipoDeConexao) {
			case self::TIPO_PG_LOCAL:
				$this->conexao = new PDO ( "pgsql:host=localhost dbname=desenvolvimento user=catraca password=CaTraCa@unilab2015" );
				break;
			case self::TIPO_PG_PRODUCAO:
					$this->conexao = new PDO ( "pgsql:host=localhost dbname=producao user=catraca password=CaTraCa@unilab2015" );
					break;
			case self::TIPO_PG_TESTE :
				$this->conexao = new PDO ( "pgsql:host=10.5.1.8 dbname=unicafe user=unicafe password=unicafe@unilab" );
				break;
			case self::TIPO_PG_SIGAAA ://Produ��o do SIG. 
				$this->conexao = new PDO ( "pgsql:host=200.129.19.80 dbname=sigaa user=catraca password=c4Tr@3a" );
				break;
			case self::TIPO_PG_SISTEMAS_COMUM ://Produ��o do SIG. 
				$this->conexao = new PDO ( "pgsql:host=200.129.19.80 dbname=sistemas_comum user=catraca password=c4Tr@3a" );
				break;
			case self::TIPO_PG_CATRACA_TESTE://Maquina do GIO. Banco de dados local. 
				$this->conexao = new PDO ("pgsql:host=10.5.0.15 dbname=desenvolvimento user=postgres password=postgres");
				break;
			default :
				$this->conexao = new PDO ( "pgsql:host=10.5.1.8 dbname=unicafe user=unicafe password=unicafe@unilab" );
				break;
		}
	}
	public function setConexao($conexao) {
		$this->conexao = $conexao;
	}
	public function getConexao() {
		return $this->conexao;
	}
	public function fechaConexao() {
		$this->conexao = null;
	}
	public function getTipoDeConexao(){
		return $this->tipoDeConexao;
	}
	public function setTipoDeConexao($tipo){
		$this->tipoDeConexao = $tipo;
	}
	const TIPO_PG_LOCAL = 0;
	const TIPO_PG_TESTE = 1;//Unicafe
	const TIPO_PG_CATRACA_TESTE = 2;
	const TIPO_PG_SIGAAA = 4;
	const TIPO_PG_SISTEMAS_COMUM = 6;
	const TIPO_PG_PRODUCAO = 7;
	const TIPO_DEFAULT = self::TIPO_PG_PRODUCAO;
	
}

?>