<?php

class Isencao
{

    private $id;

    private $dataDeInicio;

    private $dataFinal;

    public function Isencao()
    {
        $this->id = 0;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setDataDeInicio($dataInicio)
    {
        $this->dataDeInicio = $dataInicio;
    }

    public function getDataDeInicio()
    {
        return $this->dataDeInicio;
    }

    public function setDataFinal($dataFinal)
    {
        $this->dataFinal = $dataFinal;
    }

    public function getDataFinal()
    {
        return $this->dataFinal;
    }

    public function isActive()
    {
        $tempoA = strtotime($this->getDataDeInicio());
        $tempoB = strtotime($this->getDataFinal());
        $tempoAgora = time();
        if ($tempoAgora > $tempoA && $tempoAgora < $tempoB) {
            return true;
        }
        return false;
    }
}

?>