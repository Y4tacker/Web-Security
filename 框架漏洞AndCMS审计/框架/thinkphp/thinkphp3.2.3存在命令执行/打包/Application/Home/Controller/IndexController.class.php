<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index($name='111',$from='ctfshow'){
        $this->assign($name,$from);
        $this->display('index');
    }
}