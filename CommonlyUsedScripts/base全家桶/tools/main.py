from tkinter import *
from tkinter import ttk
from tkinter import scrolledtext
import tkinter as tk
from func.base import *
from func.uurrll import *
from func.hexx import *
from func.common import *
from func.uunicode import *

win = tk.Tk()

win.title('编码转换工具(自用丑陋工具箱)    By:Y4tacker')

win.geometry("900x600+300+200")

ttk.Style().configure(".", font=("幼圆", 15))

tab = ttk.Notebook(win)

# -----------------------------------------base编码解码部分----------------------------------------------


frame1 = tk.Frame(tab)

tab1 = tab.add(frame1, text=" base全家桶 ")

scr10 = scrolledtext.ScrolledText(frame1, width=95, height=17, font=(1))
scr10.place(x=0, y=0)

scr11 = scrolledtext.ScrolledText(frame1, width=95, height=17, font=(1))
scr11.place(x=0, y=350)

button100 = Button(frame1, text="ba16编码", width=10, command=lambda: tob16(scr10, scr11))  # 按钮
button100.place(x=735, y=1)

button101 = Button(frame1, text="ba16解码", width=10, command=lambda: fromb16(scr10, scr11))
button101.place(x=815, y=1)  # x左右，y是上下

button102 = Button(frame1, text="ba32编码", width=10, command=lambda: tob32(scr10, scr11))
button102.place(x=735, y=30)  # x左右，y是上下

button103 = Button(frame1, text="ba32解码", width=10, command=lambda: fromb32(scr10, scr11))
button103.place(x=815, y=30)

button104 = Button(frame1, text="ba36编码", width=10, command=lambda: tob36(scr10, scr11))
button104.place(x=735, y=60)  # x左右，y是上下

button105 = Button(frame1, text="ba36解码", width=10, command=lambda: fromb36(scr10, scr11))
button105.place(x=815, y=60)

button106 = Button(frame1, text="ba58编码", width=10, command=lambda: tob58(scr10, scr11))
button106.place(x=735, y=90)

button107 = Button(frame1, text="ba58解码", width=10, command=lambda: fromb58(scr10, scr11))
button107.place(x=815, y=90)

button108 = Button(frame1, text="ba62编码", width=10, command=lambda: tob62(scr10, scr11))
button108.place(x=735, y=120)

button109 = Button(frame1, text="ba62解码", width=10, command=lambda: fromb62(scr10, scr11))
button109.place(x=815, y=120)

button108 = Button(frame1, text="ba64编码", width=10, command=lambda: tob64(scr10, scr11))
button108.place(x=735, y=150)

button109 = Button(frame1, text="ba64解码", width=10, command=lambda: fromb64(scr10, scr11))
button109.place(x=815, y=150)

button110 = Button(frame1, text="ba85编码-1", width=10, command=lambda: tob851(scr10, scr11))
button110.place(x=735, y=180)

button111 = Button(frame1, text="ba85解码-1", width=10, command=lambda: fromb851(scr10, scr11))
button111.place(x=815, y=180)

button112 = Button(frame1, text="ba85编码-2", width=10, command=lambda: tob852(scr10, scr11))
button112.place(x=735, y=210)

button113 = Button(frame1, text="ba85解码-2", width=10, command=lambda: fromb852(scr10, scr11))
button113.place(x=815, y=210)

button112 = Button(frame1, text="ba85编码-3", width=10, command=lambda: tob853(scr10, scr11))
button112.place(x=735, y=240)

button113 = Button(frame1, text="ba85解码-3", width=10, command=lambda: fromb853(scr10, scr11))
button113.place(x=815, y=240)

button112 = Button(frame1, text="ba85编码-4", width=10, command=lambda: tob854(scr10, scr11))
button112.place(x=735, y=270)

button113 = Button(frame1, text="ba85解码-4", width=10, command=lambda: fromb854(scr10, scr11))
button113.place(x=815, y=270)

button110 = Button(frame1, text="ba91编码", width=10, command=lambda: tob91(scr10, scr11))
button110.place(x=735, y=300)

button111 = Button(frame1, text="ba91解码", width=10, command=lambda: fromb91(scr10, scr11))
button111.place(x=815, y=300)

button112 = Button(frame1, text="ba92编码", width=10, command=lambda: tob92(scr10, scr11))
button112.place(x=735, y=330)

button113 = Button(frame1, text="ba92解码", width=10, command=lambda: fromb92(scr10, scr11))
button113.place(x=815, y=330)

button114 = Button(frame1, text="ba94编码", width=10, command=lambda: toba94(scr10, scr11))
button114.place(x=735, y=360)

button115 = Button(frame1, text="ba94解码", width=10, command=lambda: fromba94(scr10, scr11))
button115.place(x=815, y=360)

button116 = Button(frame1, text="ba100编码", width=10, command=lambda: tob100(scr10, scr11))
button116.place(x=735, y=390)

button117 = Button(frame1, text="ba100解码", width=10, command=lambda: fromb100(scr10, scr11))
button117.place(x=815, y=390)

button117 = Button(frame1, text="Base全家桶", width=22, height=8,command=lambda: fromb100(scr10, scr11))
button117.place(x=735, y=420)
# ----------------------------------------------url编码解码--------------------------------------------------
frame2 = tk.Frame(tab)

tab2 = tab.add(frame2, text=" URL编码/解码 ")

scr20 = scrolledtext.ScrolledText(frame2, width=95, height=17, font=(1))
scr20.place(x=0, y=0)
scr21 = scrolledtext.ScrolledText(frame2, width=95, height=17, font=(1))
scr21.place(x=0, y=285)

button20 = Button(frame2, text="编码", width=10, command=lambda: tourl(scr20, scr21))
button20.place(x=800, y=50)

button21 = Button(frame2, text="解码", width=10, command=lambda: fromurl(scr20, scr21))
button21.place(x=800, y=150)

button32 = Button(frame2,text="清除",width=10,command = lambda: clean(scr20, scr21))#按钮
button32.place(x = 800,y = 390)


# -----------------------------------进制转字符串-------------------------------------------

frame3 = tk.Frame(tab)

tab3 = tab.add(frame3, text=" 16(2)进制转字符串 ")

scr30 = scrolledtext.ScrolledText(frame3, width=95, height=17, font=(1))
scr30.place(x=0, y=0)
scr31 = scrolledtext.ScrolledText(frame3, width=95, height=17, font=(1))
scr31.place(x=0, y=285)

button30 = Button(frame3, text="16进制转字符", width=10, command=lambda: tohex(scr30, scr31))  # 按钮
button30.place(x=735, y=50)
button31 = Button(frame3, text="字符转16进制", width=10, command=lambda: fromhex(scr30, scr31))
button31.place(x=815, y=50)
button30 = Button(frame3, text="2进制转字符", width=10, command=lambda: tobin(scr30, scr31))  # 按钮
button30.place(x=735, y=85)
button31 = Button(frame3, text="字符转2进制", width=10, command=lambda: frombin(scr30, scr31))
button31.place(x=815, y=85)
button32 = Button(frame3,text="清除",width=10,command = lambda: clean(scr30, scr31))#按钮
button32.place(x = 800,y = 390)

# -------------------------------Unicode编码-------------------------------------

frame4 = tk.Frame(tab)

tab4 = tab.add(frame4, text=" Unicode编码转换 ")

scr40 = scrolledtext.ScrolledText(frame4, width=95, height=17, font=(1))
scr40.place(x=0, y=0)
scr41 = scrolledtext.ScrolledText(frame4, width=95, height=17, font=(1))
scr41.place(x=0, y=285)

button40 = Button(frame4, text="字符转Unicode", width=15, command=lambda: toUnicode(scr40, scr41))
button40.place(x=735, y=50)
button41 = Button(frame4, text="Unicode转字符", width=15, command=lambda: fromUnicode(scr40, scr41))
button41.place(x=735, y=100)
button42 = Button(frame4,text="清除",width=10,command = lambda: clean(scr30, scr31))
button42.place(x = 735,y = 390)


tab.pack(expand=True, fill='both')

win.mainloop()
