<?php
session_start();
if (!$_SESSION['login']){
    header("location:index.php?c=login");
}
?>
<?php
error_reporting(0);

if ($_GET['file']){
    $filename = $_GET['file'];
    if ($filename=='logo.jpg'){
        header("Content-Type:image/png");
        echo file_get_contents("../static/images/logo.jpg");
    }else{
        ini_set('open_basedir','./');
        if ($filename=='hint.php'){
            echo 'nononono!';
        } else{
            if(preg_match('/read|[\x00-\x24\x26-\x2c]| |base|rot|strip|encode|flag|tags|iconv|utf|input|convertstring|lib|crypt|\.\.|\.\//i', $filename)){
                echo "hacker";
            }else{
                include($filename);
            }
        }
    }
}else{
    highlight_file(__FILE__);
}