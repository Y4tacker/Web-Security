<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>游戏后台管理</title>
    <link rel='stylesheet' href='static/layui/css/layui.css'>
    <link rel='stylesheet' href='static/css/stylez.css'>
</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <div class="layui-logo"><a href="/index.php?page=admin&c=admin" style="color:#009688;"><h1>游戏后台管理</h1></a></div>
        <!-- 头部区域（可配合layui已有的水平导航） -->
        <ul class="layui-nav layui-layout-left">
            <li class="layui-nav-item"><a href="/"><i class="layui-icon layui-icon-home"></i> 前台首页</a></li>
            <li class="layui-nav-item"><a href="/index.php?c=admin&page=category_list"><i class="layui-icon layui-icon-list"></i> 分类列表</a></li>
            <li class="layui-nav-item"><a href="/index.php?c=admin&page=add_category"><i class="layui-icon layui-icon-add-circle-fine"></i> 添加分类</a></li>
            <li class="layui-nav-item"><a href="/index.php?c=admin&page=link_list"><i class="layui-icon layui-icon-link"></i> 我的链接</a></li>
            <li class="layui-nav-item"><a href="/index.php?c=admin&page=add_link"><i class="layui-icon layui-icon-add-circle-fine"></i> 添加链接</a></li>

        </ul>
        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item">
                <a href="javascript:;">
                    <img src="class/showImage.php?file=logo.jpg" class="layui-nav-img">
                    <?php echo USER; ?>
                </a>
                <dl class="layui-nav-child">
                    <dd><a href="/index.php?c=logout">退出</a></dd>
                </dl>
            </li>
        </ul>
    </div>