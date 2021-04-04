**一、ASCII**

`ASCII(str)`

返回字符串str的最左面字符的ASCII代码值。如果str是空字符串，返回0。如果str是NULL，返回NULL。

**二、ORD**

`ORD(str)`

如果字符串str最左面字符是一个多字节字符，通过以格式`((first byte ASCII code)*256+(second byte ASCII code))[*256+third byte ASCII code...]`返回字符的ASCII代码值来返回多字节字符代码。如果最左面的字符不是一个多字节字符。返回与ASCII()函数返回的相同值。

**三、CONV**

`CONV(N,from_base,to_base)`
在不同的数字基之间变换数字。返回数字N的字符串数字，从from_base基变换为to_base基，如果任何参数是NULL，返回NULL。参数N解释为一个整数，但是可以指定为一个整数或一个字符串。最小基是2且最大的基是36。如果to_base是一个负数，N被认为是一个有符号数，否则，N被当作无符号数。 CONV以64位点精度工作。

```
mysql> select CONV("a",16,2);
->  '1010'
mysql>select CONV("6E",18,8);
->  '172'
```

**四、BIN**

`BIN(n)`

返回二进制值N的一个字符串表示，在此N是一个长整数(BIGINT)数字，这等价于CONV(N,10,2)。如果N是NULL，返回NULL。

**五、OCT**

`OCT(N)`
返回八进制值N的一个字符串的表示，在此N是一个长整型数字，这等价于CONV(N,10,8)。如果N是NULL，返回NULL。

**六、HEX**

`HEX(N)`
返回十六进制值N一个字符串的表示，在此N是一个长整型(BIGINT)数字，这等价于CONV(N,10,16)。如果N是NULL，返回NULL。
mysql> select HEX(255);

**七、CHAR**

`CHAR(N,...)`
`CHAR()`将参数解释为整数并且返回由这些整数的ASCII代码字符组成的一个字符串。NULL值被跳过。

```sql
mysql> select CHAR(77,121,83,81,'76');
->'MySQL'
mysql>  select CHAR(77,77.3,'77.3');
->'MMM'
```

**八、CONCAT/CONCAT_WS
**

`CONCAT(str1,str2,...)`

返回来自于参数连结的字符串。如果任何参数是NULL，返回NULL。可以有超过2个的参数。一个数字参数被变换为等价的字符串形式。

```
mysql>select CONCAT('My', 'S', 'QL');
-> 'MySQL'
```

`CONCAT_WS(separator,str1,str2,...)`

`CONCAT_WS() `代表 `CONCAT With Separator` ，是CONCAT()的特殊形式。第一个参数是其它参数的分隔符。分隔符的位置放在要连接的两个字符串之间。分隔符可以是一个字符串，也可以是其它参数。



**九、LENGTH/OCTET_LENGTH/CHAR_LENGTH/CHARACTER_LENGTH**

LENGTH(str)/OCTET_LENGTH(str)：字节数

CHAR_LENGTH(str)/CHARACTER_LENGTH(str)：字符数

**十、LOCATE**

该函数是多字节可靠的。

LOCATE(substr,str)
返回子串substr在字符串str第一个出现的位置，如果substr不是在str里面，返回0. 　

LOCATE(substr,str,pos)
返回子串substr在字符串str第一个出现的位置，从位置pos开始。如果substr不是在str里面，返回0。

**十一、LPAD/RPAD**

LPAD(str,len,padstr)
返回字符串str，左面用字符串padstr填补直到str是len个字符长。
RPAD(str,len,padstr)
返回字符串str，右面用字符串padstr填补直到str是len个字符长。
 

**十二、LELT/RIGHT**

LEFT(str,len)
返回字符串str的最左面len个字符。

RIGHT(str,len)
返回字符串str的最右面len个字符。
 

**十三、SUBSTRING**

SUBSTRING(str,pos,len)

从字符串str返回一个len个字符的子串，从位置pos开始。

SUBSTRING(str,pos)

从字符串str的起始位置pos返回一个子串。

**十四、SUBSTRING_INDEX**

SUBSTRING_INDEX(str,delim,count)

返回从字符串str的第count个出现的分隔符delim之后的子串。如果count是正数，返回最后的分隔符到左边(从左边数) 的所有字符。如果count是负数，返回最后的分隔符到右边的所有字符(从右边数)。
该函数对多字节是可靠的。

```
mysql> select SUBSTRING_INDEX('www.mysql.com', '.', 2);
-> 'www.mysql'
mysql> select SUBSTRING_INDEX('www.mysql.com', '.', -2);
-> 'mysql.com'
```

**十五、TRIM/LTRIM/RTRIM**

`TRIM([BOTH | LEADING | TRAILING] [remstr] FROM] str)`
返回字符串str，其所有remstr前缀或后缀被删除了。如果没有修饰符BOTH、LEADING或TRAILING给出，BOTH被假定。如果remstr没被指定，空格被删除。

`LTRIM(str)`
返回删除了其前置空格字符的字符串str。

RTRIM(str)`
返回删除了其拖后空格字符的字符串str。

**十六、SPACE**

SPACE(N)
返回由N个空格字符组成的一个字符串。

**十七、REPLACE**

REPLACE(str,from_str,to_str)
返回字符串str，其字符串from_str的所有出现由字符串to_str代替。

```
mysql> select REPLACE('www.mysql.com', 'w', 'Ww');
-> 'WwWwWw.mysql.com'
```

**十八、REPEAT**

REPEAT(str,count)
返回由重复countTimes次的字符串str组成的一个字符串。如果count ＜= 0，返回一个空字符串。如果str或count是NULL，返回NULL。 

**十九、REVERSE**

REVERSE(str)
返回颠倒字符顺序的字符串str。

**二十、INSERT**

INSERT(str,pos,len,newstr)
返回字符串str，在位置pos起始的子串且len个字符长得子串由字符串newstr代替。

```
mysql> select INSERT('Quadratic', 3, 4, 'What');
-> 'QuWhattic'
```

**二十一、ELT**

ELT(N,str1,str2,str3,...)
如果N= 1，返回str1，如果N= 2，返回str2，等等。如果N小于1或大于参数个数，返回NULL。ELT()是FIELD()反运算。

**二十二、FIELD**

FIELD(str,str1,str2,str3,...)
返回str在str1, str2, str3, ...清单的索引。如果str没找到，返回0。FIELD()是ELT()反运算。

**二十三、FIND_IN_SET**

FIND_IN_SET(str,strlist)
如果字符串str在由N子串组成的表strlist之中，返回一个1到N的值。一个字符串表是被“,”分隔的子串组成的一个字符串。如果第一个参数是一个常数字符串并且第二个参数是一种类型为SET的列，FIND_IN_SET()函数被优化而使用位运算！如果str不是在strlist里面或如果strlist是空字符串，返回0。如果任何一个参数是NULL，返回NULL。如果第一个参数包含一个“,”，该函数将工作不正常。

**二十四、MAKE_SET**

MAKE_SET(bits,str1,str2,...)
返回一个集合 (包含由“,”字符分隔的子串组成的一个字符串)，由相应的位在bits集合中的的字符串组成。str1对应于位0，str2对应位1，等等。在str1, str2, ...中的NULL串不添加到结果中。

```
mysql>SELECT MAKE_SET(1,'a','b','c');
-> 'a'
mysql>SELECTMAKE_SET(1 | 4,'hello','nice','world');
-> 'hello,world'
```

**二十五、EXPORT_SET**

EXPORT_SET(bits,on,off,[separator,[number_of_bits])

返回一个字符串，在这里对于在“bits”中设定每一位，你得到一个“on”字符串，并且对于每个复位(reset)的位，你得到一个“off”字符串。每个字符串用“separator”分隔(缺省“,”)，并且只有“bits”的“number_of_bits” (缺省64)位被使用。

**二十六、LOWER/LCASE/UPPER/UCASE**

LCASE(str)/LOWER(str) ：返回字符串str，根据当前字符集映射(缺省是ISO-8859-1 Latin1)把所有的字符改变成小写。该函数对多字节是可靠的。

UCASE(str)/UPPER(str) ：返回字符串str，根据当前字符集映射(缺省是ISO-8859-1 Latin1)把所有的字符改变成大写。该函数对多字节是可靠的。

**二十七、LOAD_FILE**

LOAD_FILE(file_name)
读入文件并且作为一个字符串返回文件内容。文件必须在服务器上，你必须指定到文件的完整路径名，而且你必须有file权限。文件必须所有内容都是可读的并且小于max_allowed_packet。如果文件不存在或由于上面原因之一不能被读出，函数返回NULL。