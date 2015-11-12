#!/usr/bin/env python
# -*- coding: latin-1 -*-

"""Fornece uma leitura de n�meros decimais de um cart�o de aproxima��o.

O n�mero em decimal obtido � convertido de bin�rio para decimal cujo este
n�mero dever� ser sempre igual a n�mero ID do cart�o RFID (Radio-Frequency
IDentification) de 13.56Mhz lido atrav�s do protocolo Wiegand por meio do
leitor de TAGs da marca HID mod. R-640X-300 iCLASS(2kbits, 16kbits, 32Kbits)
R10 Reader 6100.
"""

import RPi.GPIO as GPIO
from time import sleep

__author__ = "Erivando, Sena, e Ramos"
__copyright__ = "Copyright 2015, Unilab"
__credits__ = ["Erivando", "Sena", "Ramos"]
__license__ = "GPL"
__version__ = "1.0.0"
__maintainer__ = "Erivando"
__email__ = "erivandoramos@unilab.edu.br"
__status__ = "Prot�tipo"

# green/data0 is pin 11
# white/data1 is pin 12

D0 = 17
D1 = 27
bits = ''


GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(True)

GPIO.setup(D0, GPIO.IN, pull_up_down=GPIO.PUD_UP)
GPIO.setup(D1, GPIO.IN, pull_up_down=GPIO.PUD_UP)

def zero(self):
    global bits
    bits = bits + '0'


def one(self):
    global bits
    bits = bits + '1'


GPIO.add_event_detect(D0, GPIO.RISING, callback=zero, bouncetime=1)
GPIO.add_event_detect(D1, GPIO.RISING, callback=one, bouncetime=1)

print "Apresente o Cart�o"

while True:
    if len(bits) == 32:
        sleep(1)
        # print 25 * "-"
        # print "32 Bit Mifare Card"
        print "Bin�rio:", bits
        print "ID: ", int(str(bits), 2)
        # print "Hexadecimal:", hex(int(str(bits), 2))
        bits = ''
        # print 25 * "-"
        # print
        print "Apresente o Cart�o"

GPIO.cleanup()
