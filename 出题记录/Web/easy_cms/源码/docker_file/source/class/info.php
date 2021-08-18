<?php

class info{
    public $logContent;

    public function __construct($lastlogTime=''){
        $this->logContent = $lastlogTime;
    }

    public function log(){
        file_put_contents("./class/cache/lastTime.txt",$this->logContent);
    }



    public function __toString(){
        $tmp = file_get_contents('./class/cache/lastTime.txt');
        return explode("：",$tmp)[1];
    }


}


class UserInfo{
    public $username;
    public $nickname;
    public $role;
    public $userFunc;

    public function __construct($username, $nickname, $userFunc,$role=''){
        $this->username = $username;
        $this->nickname = $nickname;
        $this->userFunc = $userFunc;
        $this->role = $role;
    }

    public function modifyUsername($username){
        $this->username = $username;
    }
    public function modifyNicknam($username){
        $this->nickname = $username;
    }
    public function modifyRole($username){
        $this->username = $username;
    }

    public function retJsonInfo(){
        $data = '["username":"'.$this->username.'","nickname":"'.$this->nickname.'","role":"'.$this->role.'"]';
        return json_encode($data);
    }

    public function __destruct(){
        echo $this->retJsonInfo();
    }

}

class SuperAdmin{
    public $username;
    public $role;
    public $isSuperAdmin;
    public $OwnMember;

    public function __construct($username, $OwnMember, $superAdmin='',$role=''){
        $this->username = $username;
        $this->OwnMember = $OwnMember;
        $this->isSuperAdmin = $superAdmin;
        $this->role = $role;
    }

    public function isAdmin(){
        $this->isSuperAdmin = ($this->role==1)?true:false;
        return $this->isSuperAdmin;
    }

    public function retFunctionList(){
        $superFunc = ['editprofit','exportExcel','editUser','addUser','delUser','showUser','modify','authority'];
        $comFunc = ['showUser','editprofit','exportExcel'];
        return ($this->isSuperAdmin)()?$superFunc:$comFunc;
    }
    public function __toString(){
        $retData = $this->retFunctionList();
        return implode(",", $retData);
    }
}

