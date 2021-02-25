# TP3信息泄露

## 日志泄露

ThinkPHP在开启DEBUG的情况下会在Runtime目录下生成日志，如果debug模式不关，可直接输入路径造成目录遍历。

ThinkPHP3.2结构：Application\Runtime\Logs\Home\17_07_22.log
ThinkPHP3.1结构：Runtime\Logs\Home\17_07_22.log

可以看到是 ：项目名\Runtime\Logs\Home\年份_月份_日期.log
这样的话日志很容易被猜解到，而且日志里面有执行SQL语句的记录，这是很危险的！

## S和F方法缓存泄露

### F函数

假设我们的控制器这样写

```
class IndexController extends Controller {
    public function index(){
        F("data","<?php phpinfo(); ?>");

    }
}
```

默认在`Application/Runtime/Data/data.php`写入数据

### S函数

```
<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        S("data","123456");

    }
}
```

默认在`Application/Runtime/Temp/md5S函数第一个参数名字.php`写入数据