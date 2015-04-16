#!/usr/bin/env python
# -*- coding: latin-1 -*-

import pingo
import RPi.GPIO as GPIO

__author__ = "Erivando Sena"
__copyright__ = "Copyright 2015, Unilab"
__email__ = "erivandoramos@unilab.edu.br"
__status__ = "Prototype" # Prototype | Development | Production


# PINOS GPIO
SDA			=	2
SCL			=	3
LIVRE_1		=	4
LCD_RS		=	7
LCD_E		=	8
OPTICA_2	=	9
OPTICA_1	=	10
SOLENOID_1	=	11
TX			=	14
RX			=	15
RFID_D0		=	17
RFID_D1		=	18
LCD_D7		=	22
LCD_D6		=	23
LCD_D5		=	24
LCD_D4		=	25
SOLENOID_2	=	27
LED_SETA_D	=	28
LED_SETA_E	=	29
LED_X		=	30
LIVRE_2		=	31



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

