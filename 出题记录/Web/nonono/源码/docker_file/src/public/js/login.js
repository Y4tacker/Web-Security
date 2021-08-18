$(function(){

    $("#submit").click(function(){

        $.ajax({
            type: "POST",
            url: "/login.php",
            dataType: "json",
            contentType:'application/json;charset=UTF-8',
            data: JSON.stringify({
                "user": $("#username").val(),
                "passwd": $("#password").val()
            }),
            error: function(){
                alert("请求失败!");
            },
            success: function(data){
                alert(data.msg)
            }

        });

    })})