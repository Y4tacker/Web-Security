<?php
if (!$_SESSION['login']){
    header("location:index.php?c=login");
}
?>
<?php include_once('header.php'); ?>
<?php include_once('left.php'); ?>


<div class="layui-body">
    <!-- 内容主体区域 -->
    <div style="padding: 15px;">
        <div class="layui-container" style = "margin-top:2em;">
            <div class="layui-row">
                <div class="layui-col-lg4 layui-col-md-offset0">
                    <ul>
                        <li style="font-size: 30px;">游戏后台管理系统</li>
                        <br/>
                        <li style="font-size: 20px;">当前登录IP：<?php echo getIP();?></li>
                        </br>
                        <li style="font-size: 20px;">别做题了，来撸猫吧</li>
                        <br/>
                        <li style="font-size: 20px;">上次登录时间：<?php $a = new renderUtil("",Log,empty($_POST['file'])?"./class/cache/lastTime.txt":$_POST['file']);echo $a;?></li>
                        <br/>
                        <li style="font-size: 20px;">当前系统时间：<?php echo date("h:i:sa");?></li>
                    </ul>
                </div>
                <div class="layui-col-lg4 layui-col-md-offset1">
                    <ul>
                        <img src="static/images/yy.gif">
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include_once('footer.php'); ?>
