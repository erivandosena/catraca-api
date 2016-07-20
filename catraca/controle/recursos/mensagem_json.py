#!/usr/bin/env python
# -*- coding: utf-8 -*-


import json
import requests
from catraca.logs import Logs
from catraca.util import Util
from catraca.modelo.dados.servidor_restful import ServidorRestful
from catraca.modelo.dao.mensagem_dao import MensagemDAO
from catraca.modelo.entidades.mensagem import Mensagem
from catraca.modelo.dao.catraca_dao import CatracaDAO


__author__ = "Erivando Sena" 
__copyright__ = "(C) Copyright 2015, Unilab" 
__email__ = "erivandoramos@unilab.edu.br" 
__status__ = "Prototype" # Prototype | Development | Production 


class MensagemJson(ServidorRestful):
    
    util = Util()
    log = Logs()
    mensagem_dao = MensagemDAO()
    
    def __init__(self):
        super(MensagemJson, self).__init__()
        ServidorRestful.__init__(self)
        
    def mensagem_get(self, mantem_tabela=False, limpa_tabela=False):
        hostname = self.util.obtem_nome_rpi()
        servidor = self.obter_servidor()
        try:
            if servidor:
                url = str(self.URL) + "mensagem/jmensagem/" + str(CatracaDAO().busca_por_nome(hostname.upper()).id)
                r = servidor.get(url)
                if r.text != '':
                    dados  = json.loads(r.text)
                    LISTA_JSON = dados["mensagens"]
                    if LISTA_JSON != []:
                        lista = []
                        for item in LISTA_JSON:
                            obj = self.dict_obj(item)
                            if obj:
                                lista.append(obj)
                                if mantem_tabela:
                                    self.mantem_tabela_local(obj, limpa_tabela)
                        return lista
                    else:
                        self.atualiza_exclui(None, True)
                        return None
                else:
                    return None
        except Exception as excecao:
            print excecao
            self.log.logger.error('Erro obtendo json mensagem', exc_info=True)
        finally:
            pass
        
    def mantem_tabela_local(self, obj, limpa_tabela=False):
        if limpa_tabela:
            self.atualiza_exclui(None, limpa_tabela)
        if obj:
            resultado = self.mensagem_dao.busca(obj.id)
            if resultado:
                self.atualiza_exclui(obj, False)
            else:
                self.insere(obj)
        else:
            return None
        
    def atualiza_exclui(self, obj, boleano):
        self.mensagem_dao.atualiza_exclui(obj, boleano)
        print self.mensagem_dao.aviso
        
    def insere(self, obj):
        self.mensagem_dao.insere(obj)
        print self.mensagem_dao.aviso
        
    def dict_obj(self, formato_json):
        mensagem = Mensagem()
        if isinstance(formato_json, list):
            formato_json = [self.dict_obj(x) for x in formato_json]
        if not isinstance(formato_json, dict):
            return formato_json
        for item in formato_json:
            
            if item == "mens_id":
                mensagem.id = self.dict_obj(formato_json[item])
            if item == "mens_institucional1":
                mensagem.institucional1 = self.dict_obj(formato_json[item]) if self.dict_obj(formato_json[item]) is None else self.dict_obj(formato_json[item]).encode('utf-8')
            if item == "mens_institucional2":
                mensagem.institucional2 = self.dict_obj(formato_json[item]) if self.dict_obj(formato_json[item]) is None else self.dict_obj(formato_json[item]).encode('utf-8')
            if item == "mens_institucional3":
                mensagem.institucional3 = self.dict_obj(formato_json[item]) if self.dict_obj(formato_json[item]) is None else self.dict_obj(formato_json[item]).encode('utf-8')
            if item == "mens_institucional4":
                mensagem.institucional4 = self.dict_obj(formato_json[item]) if self.dict_obj(formato_json[item]) is None else self.dict_obj(formato_json[item]).encode('utf-8')
            if item == "catr_id":
                mensagem.catraca = self.dict_obj(formato_json[item])

        return mensagem
    