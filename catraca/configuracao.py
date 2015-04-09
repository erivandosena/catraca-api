#!/usr/bin/env python
# -*- coding: latin-1 -*-

import pingo
import RPi.GPIO as GPIO

__author__ = "Erivando Sena"
__copyright__ = "Copyright 2015, Unilab"
__email__ = "erivandoramos@unilab.edu.br"
__status__ = "Prototype" # Prototype | Development | Production


def pinos_rpi():
    return pingo.rpi.RaspberryPi()


def pino_entrada():
    return pingo.IN


def pino_saida():
    return pingo.OUT


def pino_baixo():
    return pingo.LOW


def pino_alto():
    return pingo.HIGH


def ativa_avisos():
    GPIO.setwarnings(True)


def desativa_avisos():
    GPIO.setwarnings(False)


def limpa_pinos():
    GPIO.cleanup()


def protege_pino_entrada_up(pino):
    GPIO.setup(int(pino), GPIO.IN, pull_up_down=GPIO.PUD_UP)


def detecta_evento(pino,obj):
    GPIO.add_event_detect(int(pino), GPIO.FALLING, callback=obj)
