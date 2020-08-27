<?php

class Custo
{

    private $id;

    private $valor;

    private $unidade;

    private $turno;

    private $inicio;

    private $fim;

    public function __construct()
    {
        $this->unidade = new Unidade();
        $this->turno = new Turno();
    }

    public function setUnidade(Unidade $unidade)
    {
        $this->unidade = $unidade;
    }

    public function getUnidade()
    {
        return $this->unidade;
    }

    public function setTurno(Turno $turno)
    {
        $this->turno = $turno;
    }

    public function getTurno()
    {
        return $this->turno;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * @param mixed $valor
     */
    public function setValor($valor)
    {
        $this->valor = floatval($valor);
    }

    /**
     * @param string $inicio
     */
    public function setInicio($inicio)
    {
        $this->inicio = $inicio;
    }

    /**
     * @return string
     */
    public function getInicio()
    {
        return $this->inicio;
    }

    /**
     * @param string $fim
     */
    public function setFim($fim)
    {
        $this->fim = $fim;
    }

    /**
     *
     * @return string
     */
    public function getFim()
    {
        return $this->fim;
    }
}

?>