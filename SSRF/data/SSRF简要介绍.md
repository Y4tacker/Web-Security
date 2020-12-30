# SSRF

## SSRF简介

SSRF，Server-Side Request Forgery，服务端请求伪造，是一种由攻击者构造形成由服务器端发起请求的一个漏洞。一般情况下，SSRF 攻击的目标是从外网无法访问的内部系统。

漏洞形成的原因大多是因为服务端提供了从其他服务器应用获取数据的功能且没有对目标地址作过滤和限制。

攻击者可以利用 SSRF 实现的攻击主要有 5 种：

1. 可以对外网、服务器所在内网、本地进行端口扫描，获取一些服务的 banner 信息
2. 攻击运行在内网或本地的应用程序（比如溢出）
3. 对内网 WEB 应用进行指纹识别，通过访问默认文件实现
4. 攻击内外网的 web 应用，主要是使用 GET 参数就可以实现的攻击（比如 Struts2，sqli 等）
5. 利用 `file` 协议读取本地文件等

## 常见的漏洞产生

1. curl

2. file_get_contents

3. fsockopen

## 利用方式

一般配合一些协议进行攻击

1. file协议
2. dict协议
3. gopher协议
4. http与https协议

## 危害

- 可以对外网、服务器所在内网、本地进行端口扫描，获取一些服务的 banner 信息;
- 攻击运行在内网或本地的应用程序（比如溢出）;
- 对内网 web 应用进行指纹识别，通过访问默认文件实现;
- 攻击内外网的 web 应用，主要是使用 get 参数就可以实现的攻击（比如 struts2，sqli 等）;
- 利用 file 协议读取本地文件等。

# 如何防御

- 服务器开启 OpenSSL 无法进行交互利用
- 服务端需要鉴权（Cookies & User：Pass）不能完美利用
- 限制请求的端口为 http 常用的端口，比如，80,443,8080,8090。
- 禁用不需要的协议。仅仅允许 http 和 https 请求。可以防止类似于 file:///,gopher://,ftp:// 等引起的问题。
- 统一错误信息，避免用户可以根据错误信息来判断远端服务器的端口状态。

# 其他

### file协议与http协议的区别

（1）file协议主要用于读取服务器本地文件，访问的是本地的静态资源 
（2）http是访问本地的html文件，简单来说file只能静态读取，http可以动态解析 
（3）http服务器可以开放端口，让他人通过http访问服务器资源，但file不可以 
（4）file对应的类似http的协议是ftp协议（文件传输协议） 
（5）file不能跨域 

## 参考文章

[CTF Wiki](https://ctf-wiki.github.io/ctf-wiki/web/ssrf-zh/)

[SSRF漏洞用到的其他协议（dict协议，file协议）](https://www.cnblogs.com/zzjdbk/archive/2004/01/13/12970919.html)