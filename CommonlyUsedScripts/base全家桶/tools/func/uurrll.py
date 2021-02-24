import urllib.parse
from urllib.parse import unquote
from tkinter import END

def handleData(scr1):
    txt = scr1.get('0.0', 'end')
    txt = unquote(txt.strip("\n"))
    return txt


def tourl(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = urllib.parse.quote(txt)
    scr2.insert(END, res)


def fromurl(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = urllib.parse.unquote(txt)
    scr2.insert(END, res)
