

# 漏洞原因

对于目录没有迭代完全，少了判断

# 漏洞位置

`phpsso_server\phpcms\modules\phpsso\index.ph`下面的文件上传函数中

```
public function uploadavatar() {
		
。。。。。。。
		
		//判断文件安全，删除压缩包和非jpg图片
		$avatararr = array('180x180.jpg', '30x30.jpg', '45x45.jpg', '90x90.jpg');
		if($handle = opendir($dir)) {
		    while(false !== ($file = readdir($handle))) {
				if($file !== '.' && $file !== '..') {
					if(!in_array($file, $avatararr)) {
						@unlink($dir.$file);
					} else {
						$info = @getimagesize($dir.$file);
						if(!$info || $info[2] !=2) {
							@unlink($dir.$file);
						}
					}
				}
		    }
		    closedir($handle);    
		}
。。。。
	}
```

如果我们在上传的时候截断数据传入一个压缩包，这个压缩包目录结构是这样的

```php
- 1.zip
 --directory
  --shell.php
```

那么`@unlink($dir.$file);`则会因为目标是文件夹而导致无法删除

，因而成功上传getshell