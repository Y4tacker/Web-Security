<?php

use Medoo\Medoo;



function doLogin($user,$pass,$t='2'){
    $data = [
        'database_type' => 'sqlite',
        'database_file' => 'db/user.db3'
    ];
    $db = new medoo($data);
    header('Content-Type:application/json; charset=utf-8');
    $res = $db->select('userTable','*',['username'=>"$user"]);
    if (count($res)>0){
        $username = $res[0]['username'];
        $password = $res[0]['password'];
    }
    if( ($user == $username) && (md5($pass) == $password) ) {
        $_SESSION['login'] = 1;
        define("AQ",1);
        $content = ($t==1)?("上次登录时间：".date("Y-m-d h:i:s")):$t;
        $log = new info($content);
        $log->log();
        $data = [
            'code'      =>  0,
            'msg'   =>  'successful'
        ];
    }
    else{
        $data = [
            'code'      =>  -1012,
            'err_msg'   =>  '用户名或密码错误！'
        ];


    }
    exit(json_encode($data));
}

function getIP() {
    if (getenv('HTTP_CLIENT_IP')) {
        $ip = getenv('HTTP_CLIENT_IP');
    }
    elseif (getenv('HTTP_X_FORWARDED_FOR')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    }
    elseif (getenv('HTTP_X_FORWARDED')) {
        $ip = getenv('HTTP_X_FORWARDED');
    }
    elseif (getenv('HTTP_FORWARDED_FOR')) {
        $ip = getenv('HTTP_FORWARDED_FOR');
    }
    elseif (getenv('HTTP_FORWARDED')) {
        $ip = getenv('HTTP_FORWARDED');
    }
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}