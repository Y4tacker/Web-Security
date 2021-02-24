import base64
import math
import base36
from func.baseModule import base91, base62, base100
import py3base92
from tkinter import END
from urllib.parse import unquote
import base58
import os


def handleData(scr1):
    txt = scr1.get('0.0', 'end')
    txt = unquote(txt.strip("\n"))
    return txt


def tob16(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = base64.b16encode(txt.encode()).decode()
    scr2.insert(END, res)  # 输出，需要通过插入来输出


def fromb16(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = ''
    for i in range(0, len(txt), 2):
        j = txt[i] + txt[i + 1]
        res += chr(int(j, 16))
    scr2.insert(END, res)  # 输出，需要通过插入来输出


def tob32(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = base64.b32encode(txt.encode()).decode()
    scr2.insert(END, res)


def fromb32(scr1, scr2):  # 解码
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    e = base64.b32decode(txt.encode('utf-8'))
    res = e.decode()
    scr2.insert(END, res)


def tob36(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = base36.loads(txt)
    scr2.insert(END, res)


def fromb36(scr1, scr2):  # 解码
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = base36.dumps(int(txt))
    scr2.insert(END, res)


def tob58(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = base58.b58encode(txt.encode()).decode()
    scr2.insert(END, res)


def fromb58(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = base58.b58decode(txt.encode()).decode()
    scr2.insert(END, res)


def tob62(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = base62.encodebytes(txt.encode())
    scr2.insert(END, res)


def fromb62(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = base62.decodebytes(txt)
    scr2.insert(END, res)


def tob64(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = base64.b64encode(txt.encode()).decode()
    scr2.insert(END, res)


def fromb64(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = base64.b64decode(txt.encode()).decode()
    scr2.insert(END, res)


def tob851(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = os.popen(f"php ./func/baseModule/base85.php 1 1 \"{txt}\"").read()
    scr2.insert(END, res)


def fromb851(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = os.popen(f"php ./func/baseModule/base85.php 1 2 \"{txt}\"").read()
    scr2.insert(END, res)


def tob852(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = os.popen(f"php ./func/baseModule/base85.php 2 1 \"{txt}\"").read()
    scr2.insert(END, res)


def fromb852(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = os.popen(f"php ./func/baseModule/base85.php 2 2 \"{txt}\"").read()
    scr2.insert(END, res)


def tob853(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = os.popen(f"php ./func/baseModule/base85.php 3 1 \"{txt}\"").read()
    scr2.insert(END, res)


def fromb853(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = os.popen(f"php ./func/baseModule/base85.php 3 2 \"{txt}\"").read()
    scr2.insert(END, res)


def tob854(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = os.popen(f"php ./func/baseModule/base85.php 4 1 \"{txt}\"").read()
    scr2.insert(END, res)


def fromb854(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = os.popen(f"php ./func/baseModule/base85.php 4 2 \"{txt}\"").read()
    scr2.insert(END, res)


def tob91(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = base91.encode(txt.encode())
    scr2.insert(END, res)


def fromb91(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = base91.decode(txt)
    scr2.insert(END, res)


def tob92(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = py3base92.encode(txt)
    scr2.insert(END, res)


def fromb92(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = py3base92.decode(txt)
    scr2.insert(END, res)


def toba94(scr1, scr2):
    base = 94
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    out_data = []
    abc = '''!"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~'''
    in_data = int.from_bytes(txt.encode(), 'big')

    d, r = in_data % base, in_data // base
    out_data.append(abc[d])
    while r:
        d, r = r % base, r // base
        out_data.append(abc[d])
    scr2.insert(END, ''.join(out_data))


def fromba94(scr1, scr2):
    base = 94
    out_data = 0
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    abc = '''!"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~'''
    # read one long string at once to memory
    in_data = txt

    # convert a big baseN number to decimal
    for i, ch in enumerate(in_data):
        out_data = abc.index(ch) * (base ** i) + out_data

    # write a big decimal number to a file as a sequence of bytes
    scr2.insert(END, out_data.to_bytes(math.ceil(out_data.bit_length() / 8), 'big'))


def tob100(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = base100.encode(txt).decode()
    scr2.insert(END, res)


def fromb100(scr1, scr2):
    scr2.delete('0.0', 'end')
    txt = handleData(scr1)
    res = base100.decode(txt)
    scr2.insert(END, res)

# def tob128(scr1, scr2):
#     scr2.delete('0.0', 'end')
#     txt = handleData(scr1)
#     res = os.popen(f"php ./func/baseModule/base128.php 1 \"{txt}\"").read()
#     with open("1.txt","w") as f:
#         f.writelines(res)
#     scr2.insert(END, res)
#
#
# def fromb128(scr1, scr2):
#     scr2.delete('0.0', 'end')
#     txt = handleData(scr1)
#     with open("1.txt","r") as f:
#         res = f.readlines()[0]
#     res = os.popen(f"php ./func/baseModule/base128.php 2 \"{res}\"").read()
#     scr2.insert(END, res)
