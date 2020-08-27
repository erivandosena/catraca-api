<?php

/*********
  * Copyright (c) 12/07/2017 {INITIAL COPYRIGHT UNILAB} {OTHER COPYRIGHT LABPATI/DISUP/DTI}.
  * All rights reserved. This program and the accompanying materials
  * are made available under the terms of the Eclipse Public License v1.0
  * which accompanies this distribution, and is available at
  * http://www.eclipse.org/legal/epl-v10.html
  *
  * Contributors:
  *    Jefferson Uchôa Ponte - initial API and implementation and/or initial documentation
  *********/
class DAO
{

    // Caso queira configurar homologacao faça novo arquivo ini. e modifique esta constante.
    const ARQUIVO_CONFIGURACAO = "/dados/sites/adm/catraca/config/config_bd.ini";

    const TIPO_CATRACA = 0;

    const TIPO_AUTENTICACAO = 2;

    const TIPO_USUARIOS = 3;

    const TIPO_USUARIOS_SECUNDARIO = 4;

    const TIPO_DEFAULT = self::TIPO_CATRACA;

    protected $conexao;

    private $tipoDeConexao;

    private $sgbb;

    private $entidadeAutenticacao;

    private $entidadeUsuarios;

    public function getSgdb()
    {
        return $this->sgdb;
    }

    public function __construct(PDO $conexao = null, $tipo = self::TIPO_DEFAULT)
    {
        $this->tipoDeConexao = $tipo;
        if ($conexao != null) {
            $this->conexao = $conexao;
        } else {

            $this->fazerConexao();
        }
    }

    public function getEntidadeUsuarios()
    {
        return $this->entidadeUsuarios;
    }

    public function setEntidadeDeUsuarios($strEntidade){
        $this->entidadeUsuarios = $strEntidade;
    }
    public function getEntidadeAutenticacao()
    {
        return $this->entidadeAutenticacao;
    }

    public function fazerConexao()
    {
        $config = parse_ini_file(self::ARQUIVO_CONFIGURACAO);
        $bd = array();

        $this->entidadeUsuarios = $config['usuarios_entidade_nome'];

        switch ($this->tipoDeConexao) {
            case self::TIPO_CATRACA:
                $bd['sgdb'] = $config['catraca_sgdb'];
                $bd['nome'] = $config['catraca_bd_nome'];
                $bd['host'] = $config['catraca_host'];
                $bd['porta'] = $config['catraca_porta'];
                $bd['usuario'] = $config['catraca_usuario'];
                $bd['senha'] = $config['catraca_senha'];
                break;

            case self::TIPO_USUARIOS:
                $bd['sgdb'] = $config['usuarios_sgdb'];
                $bd['nome'] = $config['usuarios_bd_nome'];
                $bd['host'] = $config['usuarios_host'];
                $bd['porta'] = $config['usuarios_porta'];
                $bd['usuario'] = $config['usuarios_usuario'];
                $bd['senha'] = $config['usuarios_senha'];
                $this->entidadeUsuarios = $config['usuarios_entidade_nome'];
                break;
            case self::TIPO_USUARIOS_SECUNDARIO:
                $bd['sgdb'] = $config['usuarios_secundario_sgdb'];
                $bd['nome'] = $config['usuarios_secundario_bd_nome'];
                $bd['host'] = $config['usuarios_secundario_host'];
                $bd['porta'] = $config['usuarios_secundario_porta'];
                $bd['usuario'] = $config['usuarios_secundario_usuario'];
                $bd['senha'] = $config['usuarios_secundario_senha'];
                $this->entidadeUsuarios = $config['usuarios_secundario_entidade_nome'];
                break;
            case self::TIPO_AUTENTICACAO:
                $bd['sgdb'] = $config['autenticacao_sgdb'];
                $bd['nome'] = $config['autenticacao_bd_nome'];
                $bd['host'] = $config['autenticacao_host'];
                $bd['porta'] = $config['autenticacao_porta'];
                $bd['usuario'] = $config['autenticacao_usuario'];
                $bd['senha'] = $config['autenticacao_senha'];
                break;
            default:
                $bd['sgdb'] = $config['catraca_sgdb'];
                $bd['nome'] = $config['catraca_bd_nome'];
                $bd['host'] = $config['catraca_host'];
                $bd['porta'] = $config['catraca_porta'];
                $bd['usuario'] = $config['catraca_usuario'];
                $bd['senha'] = $config['catraca_senha'];
                break;
        }
        if ($bd['sgdb'] == "postgres") {
            $this->conexao = new PDO('pgsql:host=' . $bd['host'] . ' dbname=' . $bd['nome'] . ' user=' . $bd['usuario'] . ' password=' . $bd['senha']);
        } else if ($bd['sgdb'] == "mssql") {
            $this->conexao = new PDO('dblib:host=' . $bd['host'] . ';dbname=' . $bd['nome'], $bd['usuario'], $bd['senha']);
        } else if ($bd['sgdb'] == "sqlite") {
            $this->conexao = new PDO('sqlite:' . $bd['nome']);
        }
        $this->entidadeAutenticacao = $config['autenticacao_entidade_nome'];
        $this->sgdb = $bd['sgdb'];
    }

    public function setConexao($conexao)
    {
        $this->conexao = $conexao;
    }

    public function getConexao()
    {
        return $this->conexao;
    }

    public function fechaConexao()
    {
        $this->conexao = null;
    }

    public function getTipoDeConexao()
    {
        return $this->tipoDeConexao;
    }

    public function setTipoDeConexao($tipo)
    {
        $this->tipoDeConexao = $tipo;
    }
}

?>
