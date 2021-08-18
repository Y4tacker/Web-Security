#!/usr/bin/python2.7
#coding:utf-8

from base64 import b64decode
import requests

flag = requests.get('http://127.0.0.1/test/index.php').headers['flag']

print b64decode(flag)
