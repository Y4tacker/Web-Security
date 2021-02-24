import binascii
from urllib.parse import unquote
from tkinter import END
def handleData(scr1):
    txt = scr1.get('0.0', 'end')
    txt = unquote(txt.strip("\n"))
    return txt


def tohex(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = binascii.hexlify(txt.encode()).decode('ascii')

    scr2.insert('0.0', res)


def fromhex(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = binascii.unhexlify(txt.strip('\n')).decode('ascii')
    scr2.insert('0.0', res)


def tobin(scr1, scr2):
    def encode(s):
        return ' '.join([bin(ord(c)).replace('0b', '') for c in s])
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    scr2.insert(END, encode(txt))


def frombin(scr1, scr2):
    def decode(s):
        return ''.join([chr(i) for i in [int(b, 2) for b in s.split(' ')]])
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    scr2.insert(END, decode(txt))



