from tkinter import END
from urllib.parse import unquote

def handleData(scr1):
    txt = scr1.get('0.0', 'end')
    txt = unquote(txt.strip("\n"))
    return txt

def toUnicode(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = (''.join([hex(ord(c)).replace('0x', '\\u00') for c in txt]))
    scr2.insert(END, res)  # 输出，需要通过插入来输出


def fromUnicode(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    print(txt)
    res = txt.encode().decode('unicode_escape')
    scr2.insert(END, res)  # 输出，需要通过插入来输出

print('\u0061\u0061\u0061'.encode().decode('unicode_escape'))