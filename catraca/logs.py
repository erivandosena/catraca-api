#!/usr/bin/env python
# -*- coding: latin-1 -*-


import logging
import logging.handlers


__author__ = "Erivando Sena" 
__copyright__ = "Copyright 2015, © 09/02/2015" 
__email__ = "erivandoramos@bol.com.br" 
__status__ = "Prototype"


#logger.debug('debug message')
#logger.info('info message')
#logger.warn('warn message')
#logger.error('error message')
#logger.critical('critical message')


class Logs(object):

    def __init__(self):
        super(Logs, self).__init__()
    
    @property
    def logger(self):
        name = '.'.join([__name__, self.__class__.__name__])
        return logging.getLogger(name)
    
    def main(self):
        LOG_FILENAME = '/home/pi/CatracaEletronica/log/catraca.log'
        logging.basicConfig(
                            level=logging.ERROR, 
                            filename= LOG_FILENAME, 
                            #filemode='w',
                            handler = logging.handlers.RotatingFileHandler(LOG_FILENAME, maxBytes=20, backupCount=5),
                            format='\n%(asctime)-15s %(name)-5s %(levelname)-8s %(message)s')
    
    if __name__ == '__main__':
        main()
        