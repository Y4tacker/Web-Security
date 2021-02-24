@Author：Y4tacker

@time：2021/02/23 18:40 PM

# thinkPhp3.2.2数据库操作内核分析(一)

首先来看看最简单的函数以便于熟悉它的流程

```php
$data = $_GET['u'];
$data = M('users')->where(array("username"=>$data))->find();
dump($data);
```

这个M作用是去实例化一个对象，这里是实例化User对象

![](pic\1.png)

这里通过简单的分析如果是传入`$User = M('CommonModel:User');`最后就相当于是`new CommonModel('User');`，之后调用对象的`where`方法，我们这里只有一个参数，并且只是个数组，最终只是执行`$this->options['where'] =   $where;`之后返回

![](pic\3.png)

接下来就是调用`find`，因为前面我们什么都没有传入，所以是直接设置limit为1，我们继续跟踪`$this->_parseOptions($options);`

首先它会对前面where当中的参数进行merge合并操作，首先会获取表名

![](pic\4.png)

之后这个parseType经过分析是获取其类型的`varchar(255)`，也就是数据库里面设置的

![](pic\5.png)

由于是空的，因此也就直接返回了

![](pic\6.png)

回到find函数，我们跟进`$this->db->select($options);`

![](pic\7.png)

关键是这个buildSelectSql函数，继续跟进，传入的参数是之前组合好的`$options`

![](pic\8.png)

关键是`parseSql`这里面就开始拼接sql语句了

![](pic\9.png)

进行组合导出我们的sql语句进行执行，分析完毕

![](pic\10.png)