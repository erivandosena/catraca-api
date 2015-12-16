#!/usr/bin/env python
# -*- coding: latin-1 -*-


import time
import threading
from catraca.logs import Logs
from catraca.util import Util
from catraca.visao.interface.aviso import Aviso
from catraca.controle.dispositivos.leitorcartao import LeitorCartao
from catraca.modelo.dao.mensagem_dao import MensagemDAO
from catraca.controle.restful.relogio import Relogio
from catraca.modelo.dao.catraca_dao import CatracaDAO


__author__ = "Erivando Sena" 
__copyright__ = "Copyright 2015, Unilab" 
__email__ = "erivandoramos@unilab.edu.br" 
__status__ = "Prototype" # Prototype | Development | Production 


class Mensagem(threading.Thread):
    
    log = Logs()
    aviso = Aviso()
    util = Util()
    catraca_dao = CatracaDAO()
    mensagem_dao = MensagemDAO()
    
    def __init__(self):
        super(Mensagem, self).__init__()
        threading.Thread.__init__(self)
        self.pause = False
        self.unpause = threading.Event()
        self.name = 'Thread Mensagem.'
       

    def run(self):
        print "%s Rodando... " % self.name
        self.aviso.exibir_estatus_catraca(self.util.obtem_ip())
#         self.aviso.exibir_datahora(self.util.obtem_datahora_display())
#         self.aviso.exibir_aguarda_cartao()
        while True:
            print "|-----------------------------------------------<Mensagem DISPLAY "+str(LeitorCartao.uso_do_cartao)+">---------o"

            if Relogio.periodo:
                time.sleep(60)
            elif LeitorCartao.uso_do_cartao == False:
                self.unpausa()
                self.exibe_mensagem()
            else:
                self.pausa(self)

            if self.pause:
                self.unpause.wait()
            time.sleep(1)
            
    def pausa(self):
        self.unpause.clear()
        self.pause = True
        
    def unpausa(self):
        self.pause = False
        self.unpause.set()

    def exibe_mensagem(self):
        catraca = self.catraca_dao.obtem_catraca()
        if catraca:
            mensagens = self.mensagem_dao.obtem_mensagens(catraca)
            try:
                self.aviso.exibir_mensagem_institucional_fixa(self.aviso.saldacao(), self.util.obtem_datahora_display(), 5)
                if mensagens is not []:
                    for msg in mensagens:
                        for i in range (len(msg)-2):
                            self.aviso.exibir_mensagem_institucional_scroll(str(msg[i+1]), 0.5, False)
                self.aviso.exibir_mensagem_institucional_fixa("Temperatura CPU", self.util.obtem_cpu_temp() +" C", 5)
                self.aviso.exibir_mensagem_institucional_fixa("Desempenho CPU", self.util.obtem_cpu_speed() +" RPM", 5)
            except Exception as excecao:
                print excecao
                self.log.logger.error('Erro exibindo mensagem no display', exc_info=True)
            finally:
                pass
