<?php
function get($data){
    $data = str_replace('forfun', chr(0)."*".chr(0), $data);
    return $data;
}

function checkData($data){
    if(stristr($data, 'username')!==False&&stristr($data, 'password')!==False){
        die("fuc**** hacker!!!\n");
    }
    else{
        return $data;
    }
}

function checkLogData($data){
    if (preg_match("/register|magic|PersonalFunction/",$data)){
        die("fuc**** hacker!!!!\n");
    }
    else{
        return $data;
    }
}