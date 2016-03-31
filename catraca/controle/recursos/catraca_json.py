#!/usr/bin/env python
# -*- coding: utf-8 -*-


import json
import requests
import traceback
from catraca.logs import Logs
from catraca.util import Util
from catraca.visao.interface.aviso import Aviso
from catraca.modelo.dados.servidor_restful import ServidorRestful
from catraca.modelo.dao.catraca_dao import CatracaDAO
from catraca.modelo.entidades.catraca import Catraca


__author__ = "Erivando Sena" 
__copyright__ = "(C) Copyright 2015, Unilab" 
__email__ = "erivandoramos@unilab.edu.br" 
__status__ = "Prototype" # Prototype | Development | Production 


class CatracaJson(ServidorRestful):
    
    log = Logs()
    aviso = Aviso()
    catraca_dao = CatracaDAO()
    contador_acesso_servidor = 0
    util = Util()
    interface = catraca_dao.obtem_interface_rede(util.obtem_nome_rpi())
    IP = util.obtem_ip_por_interface()
    
    def __init__(self):
        super(CatracaJson, self).__init__()
        ServidorRestful.__init__(self)
        
    def catraca_get(self, mantem_tabela=False, limpa_tabela=False):
        servidor = self.obter_servidor()
        try:
            if servidor:
                url = str(servidor) + "catraca/jcatraca"
                header = {'Content-type': 'application/json'}
                r = requests.get(url, auth=(self.usuario, self.senha), headers=header)
                print "Status HTTP: " + str(r.status_code)
                #print r.text
                if r.text == '':
                    self.contador_acesso_servidor += 1
                    if self.contador_acesso_servidor < 4:
                        self.aviso.exibir_falha_servidor()
                        self.aviso.exibir_aguarda_cartao()
                    else:
                        self.contador_acesso_servidor = 0
                else:
                    dados  = json.loads(r.text)
                    LISTA_JSON = dados["catracas"]
                    if LISTA_JSON != []:
                        catraca_local = None
                        for item in LISTA_JSON:
                            obj = self.dict_obj(item)
                            if obj:
                                print "MAC " + str(self.util.obtem_MAC_por_interface(self.interface)) + " == " + str(obj.maclan)
                                if self.util.obtem_MAC_por_interface(self.interface) == obj.maclan:
                                    # Atualiza catraca local
                                    obj.ip = self.IP
                                    obj.nome = obj.nome.upper()
                                    obj.interface = "eth0" if obj.interface == None else obj.interface
#                                     obj.maclan = self.util.obtem_MAC_por_interface('eth0')            
#                                     obj.macwlan = self.util.obtem_MAC_por_interface('wlan0')
                                    catraca_local = obj
                                    # Atualiza remoto
                                    self.objeto_json(obj, 'PUT')
                                    if self.util.obtem_nome_rpi().lower() != obj.nome.lower():
                                        print "VAI REINICIAR...."
                                        self.util.altera_hostname(obj.nome.lower())
                                        self.util.reinicia_raspberrypi()
                                        return self.aviso.exibir_reinicia_catraca()
                                    print "NÃO VAI DAR REBOOT!!!"
                                if mantem_tabela:
                                    self.mantem_tabela_local(obj, limpa_tabela)
                        if catraca_local is None:
                            self.cadastra_catraca_remoto()
                            return self.catraca_get(True, True)
                        return catraca_local
                    else:
                        self.mantem_tabela_local(None, True)
                        self.contador_acesso_servidor += 1
                        if self.contador_acesso_servidor < 4:
                            self.cadastra_catraca_remoto()
                            return self.catraca_get(True,True)
                        else:
                            self.aviso.exibir_falha_servidor()
                            self.aviso.exibir_aguarda_cartao()
                            self.contador_acesso_servidor = 0
        except Exception:
            print traceback.format_exc()
            self.log.logger.error('Erro obtendo json catraca', exc_info=True)
        finally:
            pass
        
    def mantem_tabela_local(self, obj, limpa_tabela=False):
        if limpa_tabela:
            self.atualiza_exclui(None, limpa_tabela)
            print "excluiu tudo!"
        if obj:
            resultado = self.catraca_dao.busca(obj.id)
            if resultado:
                self.atualiza_exclui(obj, False)
                print "atualizou!"
            else:
                self.insere(obj)
                print "inseriu!"
        else:
            return None
        
    def atualiza_exclui(self, obj, boleano):
        self.catraca_dao.atualiza_exclui(obj, boleano)
        print self.catraca_dao.aviso
        
    def insere(self, obj):
        self.catraca_dao.insere(obj)
        print self.catraca_dao.aviso
        
    def dict_obj(self, formato_json):
        catraca = Catraca()
        if isinstance(formato_json, list):
            formato_json = [self.dict_obj(x) for x in formato_json]
        if not isinstance(formato_json, dict):
            return formato_json
        for item in formato_json:
            
            if item == "catr_id":
                catraca.id = self.dict_obj(formato_json[item])
            if item == "catr_ip":
                catraca.ip = self.dict_obj(formato_json[item])
            if item == "catr_tempo_giro":
                catraca.tempo = self.dict_obj(formato_json[item])
            if item == "catr_operacao":
                catraca.operacao = self.dict_obj(formato_json[item])
            if item == "catr_nome":
                catraca.nome = self.dict_obj(formato_json[item]) if self.dict_obj(formato_json[item]) is None else self.dict_obj(formato_json[item]).encode('utf-8')
            if item == "catr_mac_lan":
                catraca.maclan = self.dict_obj(formato_json[item]) if self.dict_obj(formato_json[item]) is None else self.dict_obj(formato_json[item]).encode('utf-8')
            if item == "catr_mac_wlan":
                catraca.macwlan = self.dict_obj(formato_json[item]) if self.dict_obj(formato_json[item]) is None else self.dict_obj(formato_json[item]).encode('utf-8')
            if item == "catr_interface_rede":
                catraca.interface = self.dict_obj(formato_json[item]) if self.dict_obj(formato_json[item]) is None else self.dict_obj(formato_json[item]).encode('utf-8')
                
        return catraca
    
    def lista_json(self, lista):
        if lista:
            for item in lista:
                catraca = {
                    "catr_ip":str(item[1]),
                    "catr_tempo_giro":item[2],
                    "catr_operacao":item[3],
                    "catr_nome":str(item[4]),
                    "catr_mac_lan":str(item[5]),
                    "catr_mac_wlan":str(item[6]),
                    "catr_interface_rede":str(item[7])
                }
                self.catraca_post(catraca)

    def objeto_json(self, obj, operacao="POST"):
        if obj:
            catraca = {
                "catr_ip":str(obj.ip),
                "catr_tempo_giro":obj.tempo,
                "catr_operacao":obj.operacao,
                "catr_nome":str(obj.nome),
                "catr_mac_lan":str(obj.maclan),
                "catr_mac_wlan":str(obj.macwlan),
                "catr_interface_rede":str(obj.interface)
            }
            if operacao == "POST":
                self.catraca_post(catraca)
            if operacao == "PUT":
                self.catraca_put(catraca, obj.id)
            
    def catraca_post(self, formato_json):
        servidor = self.obter_servidor()
        try:
            if servidor:
                url = str(servidor) + "catraca/insere"
                header = {'Content-type': 'application/json'}
                r = requests.post(url, auth=(self.usuario, self.senha), headers=header, data=json.dumps(formato_json))
                #print r.text
                #print r.status_code
        except Exception as excecao:
            print excecao
            self.log.logger.error('Erro enviando json catraca.', exc_info=True)
        finally:
            pass
        
    def catraca_put(self, formato_json, id):
        servidor = self.obter_servidor()
        try:
            if servidor:
                url = str(servidor) + "catraca/atualiza/"+ str(id)
                #print url
                header = {'Content-type': 'application/json'}
                r = requests.put(url, auth=(self.usuario, self.senha), headers=header, data=json.dumps(formato_json))
                #print r.text
                #print r.status_code
                return True
            else:
                return False
        except Exception as excecao:
            print excecao
            self.log.logger.error('Erro enviando json catraca.', exc_info=True)
        finally:
            pass
    
    def cadastra_catraca_remoto(self):
        catraca = Catraca()
        catraca.ip = self.IP
        catraca.tempo = 20
        catraca.operacao = 1
        catraca.nome = self.util.obtem_nome_rpi().upper()
        catraca.maclan = self.util.obtem_MAC_por_interface('eth0')
        catraca.macwlan = self.util.obtem_MAC_por_interface('wlan0')
        catraca.interface = "eth0"
        self.objeto_json(catraca)
            
    