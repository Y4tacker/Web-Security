<!DOCTYPE html>
<html lang="zh-cn" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8" />
	<title>游戏系统后台登录</title>
	<meta name="generator" content="EverEdit" />
	<meta name="author" content="" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel='stylesheet' href='static/layui/css/layui.css'>
	  <link rel='stylesheet' href='static/css/stylez.css'>
	  <style>
		  body{
			  background-color:rgba(0, 0, 51, 0.8);

		  }

	  </style>
</head>
<body>

<div class="layui-container">
	<div class="layui-row">
		<div class="login-logo">
			<h1>游戏系统后台登录</h1>
		</div>
		<div class="layui-col-lg4 layui-col-md-offset4" style ="margin-top:4em;">
		<form class="layui-form layui-form-pane" action="">
  <div class="layui-form-item">
    <label class="layui-form-label"><i class="layui-icon layui-icon-username"></i></label>
    <div class="layui-input-block">
      <input type="text" name="user" required  lay-verify="required" placeholder="请输入用户名" autocomplete="off" class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label"><i class="layui-icon layui-icon-password"></i></label>
    <div class="layui-input-block">
      <input type="password" name="password" required  lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
    </div>
  </div>
  <input type="hidden" name="t" value="1" autocomplete="off" class="layui-input">


            <div class="layui-form-item">
    <button class="layui-btn" lay-submit lay-filter="login" style = "width:100%;">登录</button>
  </div>


</form>
		</div>
	</div>
</div>


<script src = 'static/jquery/jquery.min.js'></script>
<script src = 'static/layui/layui.js'></script>
<script src="static/js/embed.js"></script>
</body>
</html>