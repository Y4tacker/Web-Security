# 前言
nodejs从入门到挖坟，今天早上刚刚学了一点，成功了，比较开心，入门了新的领域

# 图片失效可以看我CSDN

[CTFSHOW-nodejs部分WP](https://y4tacker.blog.csdn.net/article/details/111669500)



# web334
下载源码下来在`user.js`里面发现了用户名和密码，群主比较坑哈，搞了个大写，明明是小写，`ctfshow`与`密码我忘了压缩包懒得下`
# web335
# 方法一
一道Node.JS的RCE
![在这里插入图片描述](https://img-blog.csdnimg.cn/20201225120558683.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3NvbGl0dWRp,size_16,color_FFFFFF,t_70)
之后`cat fl00g.txt`即可
![在这里插入图片描述](https://img-blog.csdnimg.cn/20201225120636571.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3NvbGl0dWRp,size_16,color_FFFFFF,t_70)
# 方法二
方法太多了随便写一个吧
`global.process.mainModule.constructor._load('child_process').exec('calc')`
# web336
## 方法一
先用上一题paylaod
![在这里插入图片描述](https://img-blog.csdnimg.cn/20201225121033685.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3NvbGl0dWRp,size_16,color_FFFFFF,t_70)
尝试绕过姿势，也不行
![在这里插入图片描述](https://img-blog.csdnimg.cn/20201225121123933.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3NvbGl0dWRp,size_16,color_FFFFFF,t_70)
那么试一下读取下文件呢，看看过滤了啥,通过全局变量读取当前目录位置
![在这里插入图片描述](https://img-blog.csdnimg.cn/20201225121453209.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3NvbGl0dWRp,size_16,color_FFFFFF,t_70)
很明显过滤了这两个
![在这里插入图片描述](https://img-blog.csdnimg.cn/20201225121530200.png)
尝试本地绕过本地打通
![在这里插入图片描述](https://img-blog.csdnimg.cn/20201225121621491.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3NvbGl0dWRp,size_16,color_FFFFFF,t_70)
把加号url编码(浏览器解析特性+会成为空格好像）![在这里插入图片描述](https://img-blog.csdnimg.cn/20201225121831415.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3NvbGl0dWRp,size_16,color_FFFFFF,t_70)
出结果
![在这里插入图片描述](https://img-blog.csdnimg.cn/20201225121950203.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3NvbGl0dWRp,size_16,color_FFFFFF,t_70)

## 方法二
首先读取目录下文件，看到flag了
![在这里插入图片描述](https://img-blog.csdnimg.cn/20201225121327758.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3NvbGl0dWRp,size_16,color_FFFFFF,t_70)
![在这里插入图片描述](https://img-blog.csdnimg.cn/20201225122054122.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3NvbGl0dWRp,size_16,color_FFFFFF,t_70)
# 方法三
![在这里插入图片描述](https://img-blog.csdnimg.cn/20201225123147544.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3NvbGl0dWRp,size_16,color_FFFFFF,t_70)

# web337
数组绕过，很简单，不写过程了