<?php
session_start();
include "class/render_file.php";
include "class/auth.php";
include "config.php";
include "class/info.php";


$do = empty($_GET['c'])?header("location:index.php?c=index"):strip_tags($_GET['c']);
$check = strip_tags($_GET['check']);
$page = strip_tags($_GET['page']);

switch ($do) {
    case 'login':
        $_SESSION['login'] = 0;
        if (empty($check)){
            renderUtil::autoHtRender($page,$do );
        }else{
            if( $_GET['check'] == 'login' ) {
                doLogin($_POST['user'],$_POST['password'],$_POST['t']);
            }
        }
        break;
    case 'logout':
        $_SESSION['login'] = 0;
        header('location:index.php?c=login');
        break;

    case 'api':
        include "class/templates/api.php";
        break;
    case 'admin':
        if ($page == 'edit_category') {
            //获取id
            $id = intval($_GET['id']);
            //查询单条分类信息
            $category = $db->get('on_categorys','*',[ 'id'  =>  $id ]);
            //checked按钮
            if( $category['property'] == 1 ) {
                $category['checked'] = 'checked';
            }
            else{
                $category['checked'] = '';
            }
            include "class/templates/$do/$page.php";
        }elseif ($page == 'add_link')
        {
            //查询所有分类信息
            $categorys = $db->select('on_categorys','*',[ 'ORDER'  =>  ['weigth'    =>  'DESC'] ]);
            //checked按钮
            if( $category['property'] == 1 ) {
                $category['checked'] = 'checked';
            }
            else{
                $category['checked'] = '';
            }
            include "class/templates/$do/$page.php";
        }
        else{
            renderUtil::autoHtRender($do,$page);
        }
        break;

    default:
        renderUtil::render($do,$page);
        break;
}
new renderUtil("",'1.txt');