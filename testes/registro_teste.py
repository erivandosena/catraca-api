#!/usr/bin/env python
# -*- coding: latin-1 -*-


import os
import socket
import locale
import datetime
from catraca.dao.registro import Registro
from catraca.dao.registrodao import RegistroDAO
from catraca.dao.cartaodao import CartaoDAO
from catraca.dao.catracadao import CatracaDAO
from catraca.dao.finalidadedao import FinalidadeDAO


__author__ = "Erivando Sena"
__copyright__ = "Copyright 2015, Unilab"
__email__ = "erivandoramos@unilab.edu.br"
__status__ = "Prototype" # Prototype | Development | Production



socket = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
socket.connect(('unilab.edu.br', 0))
IP = '%s' % (socket.getsockname()[0])

locale.setlocale(locale.LC_ALL, 'pt_BR.UTF-8')

def main():
    print 'Iniciando os testes tabela registro...'
    
    registro = Registro()
    registro_dao = RegistroDAO()
    cartao_dao = CartaoDAO()

    registro.data = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S.%f")
    registro.giro = 1
    registro.cartao = cartao_dao.busca(4)
    registro.valor = registro.cartao.perfil.tipo.valor
    
#     if registro_dao.conexao_status():
#          print "conexao_status: "+ str(registro_dao.conexao_status())

# 
#     if not registro_dao.mantem(registro,False):
#         raise Exception(registro_dao.aviso)
#     else:
#         print registro_dao.aviso


#     print registro.cartao.perfil.tipo.nome
#     print registro.cartao.perfil.nome
#     print registro.cartao.numero
#     print locale.currency(registro.valor).format()

    #registro = registro_dao.busca(6)

    """
    registro_dao.mantem(registro,False)
    print registro_dao.aviso
    
    print registro.cartao


    catraca = CatracaDAO().busca_por_ip(IP)
    turnos = catraca.turno

    p1_hora_inicio = datetime.datetime.strptime('00:00:00','%H:%M:%S').time()
    p1_hora_fim = datetime.datetime.strptime('00:00:00','%H:%M:%S').time()

    conta_turnos = 0
    hora_atual = datetime.datetime.strptime(datetime.datetime.now().strftime('%H:%M:%S'),'%H:%M:%S').time()
    for turno in turnos:
        conta_turnos += 1
        print conta_turnos
        p1_hora_inicio = datetime.datetime.strptime(str(turno[1]),'%H:%M:%S').time()
        p1_hora_fim = datetime.datetime.strptime(str(turno[2]),'%H:%M:%S').time()

        if ((hora_atual >= p1_hora_inicio) and (hora_atual <= p1_hora_fim)):
            finalidade_turno = FinalidadeDAO().busca(turno[3]).nome
            print "Turno encontrado entre:" +str(p1_hora_inicio)+" "+str(p1_hora_fim)+ " -> " + finalidade_turno
            break
    if not (((hora_atual >= p1_hora_inicio) and (hora_atual <= p1_hora_fim)) or ((hora_atual >= p1_hora_inicio) and (hora_atual <= p1_hora_fim))):
        print "Fora do horario!"
        return None
    elif ((hora_atual >= p1_hora_inicio) and (hora_atual <= p1_hora_fim)):
        print "Turno liberado entre:" +str(p1_hora_inicio)+" "+str(p1_hora_fim)
        print "faco coisas apartir daqui!"
    if True:
        print "outras coisas..."

            
    
    """  
    print 62 * "="
    print '======################### RELATORIO ####################======'
    print 62 * "="
    

    for registro in registro_dao.busca():
        cartao = cartao_dao.busca(registro[4])
        print str(registro[1]) +" "\
        + str(registro[2]) +" "\
        + str(registro[3]) +" "\
        + str(cartao.numero) +" "\
        + str(cartao.perfil.nome) +" "\
        + str(cartao.perfil.tipo.nome)
        print 62 * "-"
    print 62 * "="
