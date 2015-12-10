#!/usr/bin/env python
# -*- coding: utf-8 -*-


from contextlib import closing
from catraca.modelo.dados.conexao import ConexaoFactory
from catraca.modelo.dados.conexaogenerica import ConexaoGenerica
from catraca.modelo.entidades.custo_refeicao import CustoRefeicao


__author__ = "Erivando Sena"
__copyright__ = "Copyright 2015, Unilab"
__email__ = "erivandoramos@unilab.edu.br"
__status__ = "Prototype" # Prototype | Development | Production


class CustoRefeicaoDAO(ConexaoGenerica):
    
    def __init__(self):
        super(CustoRefeicaoDAO, self).__init__()
        ConexaoGenerica.__init__(self)
        
    def busca(self, *arg):
        obj = CustoRefeicao()
        id = None
        for i in arg:
            id = i
        if id:
            sql = "SELECT cure_id, cure_valor, cure_data FROM custo_refeicao WHERE cure_id = " + str(id)
        elif id is None:
            sql = "SELECT cure_id, cure_valor, cure_data FROM custo_refeicao ORDER BY cure_id DESC LIMIT 1"
        try:
            with closing(self.abre_conexao().cursor()) as cursor:
                cursor.execute(sql)
                dados = cursor.fetchone()
                if dados:
                    obj.id = dados[0]
                    obj.valor = dados[1]
                    obj.data = dados[2]
                    return obj
                else:
                    return None
        except Exception as excecao:
            self.aviso = str(excecao)
            self.log.logger.error('[custo-refeicao] Erro ao realizar SELECT.', exc_info=True)
        finally:
            pass
        
    def insere(self, obj):
        try:
            if obj:
                sql = "INSERT INTO custo_refeicao("\
                    "cure_id, "\
                    "cure_valor, "\
                    "cure_data) VALUES (" +\
                    str(obj.id) + ", " +\
                    str(obj.valor) + ", '" +\
                    str(obj.data) + "')"
                self.aviso = "[custo-refeicao] Inserido com sucesso!"
                with closing(self.abre_conexao().cursor()) as cursor:
                    cursor.execute(sql)
                    self.commit()
                    return False
            else:
                self.aviso = "Objeto inexistente!"
                return False
        except Exception as excecao:
            self.aviso = str(excecao)
            self.log.logger.error('[custo-refeicao] Erro realizando INSERT.', exc_info=True)
            return False
        finally:
            pass   
        
    def atualiza_exclui(self, obj, delete):
        try:
            if obj or delete:
                if delete:
                    if obj:
                        sql = "DELETE FROM custo_refeicao WHERE caun_id = " + str(obj.id)
                    else:
                        sql = "DELETE FROM custo_refeicao"
                    self.aviso = "[custo-refeicao] Excluido com sucesso!"
                else:
                    sql = "UPDATE custo_refeicao SET " +\
                        "cure_valor = " + str(obj.valor) + ", " +\
                        "cure_data = '" + str(obj.data) +\
                        "' WHERE "\
                        "cure_id = " + str(obj.id)
                    self.aviso = "[custo-refeicao] Alterado com sucesso!"
                with closing(self.abre_conexao().cursor()) as cursor:
                    cursor.execute(sql)
                    self.commit()
                    return True
            else:
                self.aviso = "[custo-refeicao] inexistente!"
                return False
        except Exception as excecao:
            self.aviso = str(excecao)
            self.log.logger.error('[custo-refeicao] Erro realizando DELETE/UPDATE.', exc_info=True)
            return False
        finally:
            pass
        