<?php

/**
 * 
 * @author Jefferson Uchoa Ponte
 *
 */
class ValidacaoDAO extends DAO
{

    public function listaValidacao()
    {
        $lista = array();
        $sql = "SELECT * FROM validacao
				INNER JOIN tipo ON validacao.tipo_id = tipo.tipo_id ";
        $result = $this->getConexao()->query($sql);
        foreach ($result as $linha) {
            $validacao = new Validacao();
            $validacao->setId($linha['vali_id']);
            $validacao->setCampo($linha['vali_campo']);
            $validacao->setValor($linha['vali_valor']);
            $validacao->getTipo()->setId($linha['tipo_id']);
            $validacao->getTipo()->setNome($linha['tipo_nome']);
            $lista[] = $validacao;
        }
        return $lista;
    }

    public function inserirValidacao(Validacao $validacao)
    {
        $campo = $validacao->getCampo();
        $valor = $validacao->getValor();
        $idTipo = $validacao->getTipo()->getId();

        $sql = "INSERT INTO
                validacao(vali_campo, vali_valor, tipo_id)
                VALUES('$campo', '$valor', $idTipo);";
        if ($this->getConexao()->exec($sql)) {
            return true;
        }
        return false;
    }

    /**
     *
     * @return array:string
     */
    public function listaDeCampos()
    {
        $listaCampos = array();
        $sql = "SELECT
		c.relname, a.attname as column
		FROM pg_catalog.pg_attribute a
		INNER JOIN pg_stat_user_tables c on a.attrelid = c.relid
		WHERE c.relname = 'vw_usuarios_catraca' AND a.attnum > 0 AND NOT a.attisdropped	";
        $result = $this->getConexao()->query($sql);
        foreach ($result as $linha) {
            $listaCampos[] = $linha['column'];
        }
        return $listaCampos;
    }

    public function excluirValidacao(Validacao $validacao)
    {
        $id = $validacao->getId();
        $sql = "DELETE FROM validacao WHERE vali_id = $id";
        if ($this->getConexao()->exec($sql)) {
            return true;
        }
        return false;
    }

    public function listaDeTipos(Usuario $usuario)
    {
        $tipoDao = new TipoDAO($this->getConexao());
        $usuarioDao = new UsuarioDAO($this->getConexao());
        $listaDeTipos = $tipoDao->retornaLista();
        $listaDeUsuarios = $usuarioDao->listaPorIdBaseExterna($usuario);
        $tiposValidos = array();

        foreach ($listaDeUsuarios as $usuario2) {
            foreach ($listaDeTipos as $tipo) {
                if ($this->validarTipo($usuario2, $tipo)) {
                    $flagExiste = false;
                    foreach ($tiposValidos as $tipo2) {
                        if ($tipo2->getNome() == $tipo->getNome()) {
                            $flagExiste = true;
                        }
                    }
                    if (! $flagExiste) {
                        $tiposValidos[] = $tipo;
                    }
                }
            }
        }
        return $tiposValidos;
    }

    public function validarTipo(Usuario $usuario, Tipo $tipo)
    {
        if (strtolower(trim($tipo->getNome())) == 'aluno') {
            if (trim($usuario->getStatusDiscente()) == 'CADASTRADO' || strtolower(trim($usuario->getStatusDiscente())) == 'ativo' || strtolower(trim($usuario->getStatusDiscente())) == 'ativo - formando' || strtolower(trim($usuario->getStatusDiscente())) == 'formando' || strtolower(trim($usuario->getStatusDiscente())) == 'ativo - graduando') {
                return true;
            }
        }
        if (strtolower(trim($tipo->getNome())) == 'servidor tae') {
            if (strtolower(trim($usuario->getStatusServidor())) == 'ativo' && strpos(strtolower(trim($usuario->getCategoria())), 'administrativo')) {
                return true;
            }
            if ($usuario->getIDCategoria() == 3) {
                return true;
            }
        }
        if (strtolower(trim($tipo->getNome())) == 'servidor docente') {
            if ((strtolower(trim($usuario->getTipodeUsuario())) == 'docente externo') || (strtolower(trim($usuario->getStatusServidor())) == 'ativo' && strtolower(trim($usuario->getCategoria())) == 'docente')) {
                return true;
            }
        }
        if (strtolower(trim($tipo->getNome())) == 'terceirizado') {
            if (strtolower(trim($usuario->getTipodeUsuario())) == 'terceirizado' || strtolower(trim($usuario->getTipodeUsuario())) == 'outros') {
                return true;
            }
        }
        return false;
    }

    public function verificaSeAtivo(Usuario $usuario)
    {
        $id = $usuario->getIdBaseExterna();
        $strEntidade = $this->getEntidadeUsuarios();
        $sql = "SELECT * FROM $strEntidade
                WHERE id_usuario = $id
                ORDER BY status_discente, status_servidor
                ASC LIMIT 30";

        foreach ($this->getConexao()->query($sql) as $linha) {
            $usuario->setNome($linha['nome']);
            $usuario->setEmail($linha['email']);
            $usuario->setLogin($linha['login']);
            $usuario->setCpf($linha['cpf_cnpj']);
            $usuario->setIdBaseExterna($linha['id_usuario']);
            $usuario->setIdentidade($linha['identidade']);
            $usuario->setPassaporte($linha['passaporte']);
            $usuario->setTipoDeUsuario($linha['tipo_usuario']);
            $usuario->setMatricula($linha['matricula_disc']);
            $usuario->setStatusDiscente($linha['status_discente']);
            $usuario->setIdStatusDiscente($linha['id_status_discente']);
            $usuario->setNivelDiscente($linha['nivel_discente']);
            $usuario->setCategoria($linha['categoria']);
            $usuario->setIDCategoria($linha['id_categoria']);
            $usuario->setSiape($linha['siape']);
            $usuario->setStatusServidor($linha['status_servidor']);

            if (strtolower(trim($usuario->getStatusServidor())) == 'ativo') {
                return true;
            }
            if (trim($usuario->getStatusDiscente()) == 'CADASTRADO' || strtolower(trim($usuario->getStatusDiscente())) == 'ativo' || strtolower(trim($usuario->getStatusDiscente())) == 'ativo - formando' || strtolower(trim($usuario->getStatusDiscente())) == 'formando' || strtolower(trim($usuario->getStatusDiscente())) == 'ativo - graduando' || strtolower(trim($usuario->getIdStatusDiscente())) == self::ID_STATUS_DISCENTE_CONCLUIDO) {

                return true;
            }
            if (strtolower(trim($usuario->getTipodeUsuario())) == 'terceirizado' || strtolower(trim($usuario->getTipodeUsuario())) == 'outros') {
                return true;
            }
            if (strtolower(trim($usuario->getTipodeUsuario())) == 'docente externo') {
                return true;
            }
        }
        return false;
    }

    const ID_STATUS_DISCENTE_ATIVO = 1;

    const ID_STATUS_DISCENTE_CADASTRADO = 3;

    const ID_STATUS_DISCENTE_FORMADO = 9;

    const ID_STATUS_DISCENTE_CONCLUIDO = 3;
}

?>