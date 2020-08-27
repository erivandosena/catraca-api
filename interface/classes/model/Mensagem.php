<?php 

class Mensagem{

    private $id;
    private $mensagem1;
    private $mensagem2;
    private $mensagem3;
    private $mensagem4;
    private $catraca;
    public function __construct(){
        $this->catraca = new Catraca();
    }
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getMensagem1()
    {
        return $this->mensagem1;
    }

    /**
     * @return mixed
     */
    public function getMensagem2()
    {
        return $this->mensagem2;
    }

    /**
     * @return mixed
     */
    public function getMensagem3()
    {
        return $this->mensagem3;
    }

    /**
     * @return Catraca
     */
    public function getMensagem4()
    {
        return $this->mensagem4;
    }

    /**
     * @return Catraca
     */
    public function getCatraca()
    {
        return $this->catraca;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $mensagem1
     */
    public function setMensagem1($mensagem1)
    {
        $this->mensagem1 = $mensagem1;
    }

    /**
     * @param mixed $mensagem2
     */
    public function setMensagem2($mensagem2)
    {
        $this->mensagem2 = $mensagem2;
    }

    /**
     * @param mixed $mensagem3
     */
    public function setMensagem3($mensagem3)
    {
        $this->mensagem3 = $mensagem3;
    }

    /**
     * @param mixed $mensagem4
     */
    public function setMensagem4($mensagem4)
    {
        $this->mensagem4 = $mensagem4;
    }

    /**
     * @param Catraca $catraca
     */
    public function setCatraca(Catraca $catraca)
    {
        $this->catraca = $catraca;
    }

}

?>