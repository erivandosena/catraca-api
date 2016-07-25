#!/usr/bin/env python
# -*- coding: utf-8 -*-


import simplejson as json
import hashlib
import decimal


__author__ = "Erivando Sena"
__copyright__ = "(C) Copyright 2015, Unilab"
__email__ = "erivandoramos@unilab.edu.br"
__status__ = "Prototype" # Prototype | Development | Production


class Usuario(object):
    
    def __init__(self):
        self.__usua_id = None
        self.__usua_nome = None
        self.__usua_email = None
        self.__usua_login = None
        self.__usua_senha = None
        self.__usua_nivel = None
        
    def __eq__(self, outro):
        return self.hash_dict(self) == self.hash_dict(outro)
    
    def __ne__(self, outro):
        return not self.__eq__(outro)
    
    def hash_dict(self, obj):
        return hashlib.sha1(json.dumps(obj.__dict__, default=self.json_encode_decimal, use_decimal=False, ensure_ascii=False, sort_keys=False, encoding='utf-8')).hexdigest()
    
    def json_encode_decimal(self, obj):
        if isinstance(obj, decimal.Decimal):
            return str(obj)
        raise TypeError(repr(obj) + " nao JSON serializado")
    
    @property
    def id(self):
        return self.__usua_id
    
    @id.setter
    def id(self, valor):
        self.__usua_id = valor
        
    @property
    def nome(self):
        return self.__usua_nome
    
    @nome.setter
    def nome(self, valor):
        self.__usua_nome = valor
        
    @property
    def email(self):
        return self.__usua_email
    
    @email.setter
    def email(self, valor):
        self.__usua_email = valor
        
    @property
    def login(self):
        return self.__usua_login
    
    @login.setter
    def login(self, valor):
        self.__usua_login = valor
    
    @property
    def senha(self):
        return self.__usua_senha
    
    @senha.setter
    def senha(self, valor):
        self.__usua_senha = valor
        
    @property
    def nivel(self):
        return self.__usua_nivel
    
    @nivel.setter
    def nivel(self, valor):
        self.__usua_nivel = valor
        
        