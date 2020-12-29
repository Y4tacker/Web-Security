# JWT

## 介绍

jwt(JSON Web Token)是一串json格式的字符串，由服务端用加密算法对信息签名来保证其完整性和不可伪造。Token里可以包含所有必要信息，这样服务端就无需保存任何关于用户或会话的信息，JWT可用于身份认证、会话状态维持、信息交换等。它的出现是为了在网络应用环境间传递声明而执行的一种基于JSON的开放标准（[(RFC 7519](https://link.zhihu.com/?target=https%3A//link.jianshu.com/%3Ft%3Dhttps%3A//tools.ietf.org/html/rfc7519)).该token被设计为紧凑且安全的，特别适用于分布式站点的单点登录（SSO）场景。

一个jwt token由三部分组成，header、payload与signature，以点隔开，形如`aaaa.bbbb.cccc`。

一个具体的例子：

```
eyJhbGciOiJOb25lIiwidHlwIjoiand0In0.W3sic3ViIjoidXNlciJ9XQ
```

- header用来声明token的类型和签名用的算法等，需要经过Base64Url编码。比如以上token的头部经过base64解码后为`{"alg":"HS256","typ":"JWT"}`

```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9
解码为 
{   
	"alg": "HS256",
    "typ": "JWT" 
}
alg属性表示签名的算法（algorithm），默认是 HMAC SHA256（写成 HS256）；
typ属性表示这个令牌（token）的类型（type），JWT 令牌统一写为JWT
```

- payload用来表示真正的token信息，也需要经过Base64Url编码。比如以上token的payload经过解码后为`{"sub":"1234567890","name":"John Doe","iat":1516239022}`

```
eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ 
解码为
 {   
 	"sub": "1234567890",  
	"name": "John Doe",
	"iat": 1516239022 
}
JWT 规定了7个官方字段，供选用
iss (issuer)：签发人
exp (expiration time)：过期时间
sub (subject)：主题
aud (audience)：受众
nbf (Not Before)：生效时间
iat (Issued At)：签发时间
jti (JWT ID)：编号
```



- signature，将前两部分用`alg`指定的算法加密，再经过Base64Url编码就是signature了，作用是防止数据篡改。

## 解码

一般推荐去`http://jwt.io/`解码，拿上面这个例子

解密前

```
eyJhbGciOiJOb25lIiwidHlwIjoiand0In0.W3sic3ViIjoidXNlciJ9XQ
```

解密后

```
{
  "alg": "None",
  "typ": "jwt"
}
[
  {
    "sub": "user"
  }
]
```

## JWT的安全问题

```
1.修改算法为none
2.修改算法从RS256到HS256
3.信息泄漏 密钥泄漏
4.爆破密钥
```



# 参考文章

[从hfctf学习JWT伪造](https://zhuanlan.zhihu.com/p/134037462)

[JSON Web Token 入门教程](https://www.ruanyifeng.com/blog/2018/07/json_web_token-tutorial.html)