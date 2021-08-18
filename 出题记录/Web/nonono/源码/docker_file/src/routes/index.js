var express = require('express');
var router = express.Router();
var jwt = require('jsonwebtoken');
var fs = require('fs');
var secr0t = require("./secret.js");


router.get('/', function(req, res, next) {
	res.redirect('/login.php')
  
});

router.get('/login.php', function(req, res, next) {
	res.type('html');
	var Info = '';
	if (req.cookies.auth==undefined){
		Info = "Please Login"
	}else {
		Info = "Nothing here"
	}
	res.render('index', {info: Info});

});

router.post('/login.php', function(req, res, next) {
	res.json({
		"error": "error","msg":"这不得先注册？？？"
	})
});

router.get('/register.php', function(req, res, next) {
	res.send("No get method!")
});

router.post('/register.php',function(req,res,next){
	var username = '';
	var password = '';
	try{
		 username = req.body.username;
		 password = req.body.password;
	}catch (e) {
		res.send("{status:500,info: Please input username and password}");
	}
	if (username!=''&&password!=''&&username!=undefined&&password!=undefined){
		var privateKey = fs.readFileSync(process.cwd()+'//public/private.key');
		var token = jwt.sign({ user: 'admin' }, privateKey, { algorithm: 'RS256' });
		res.cookie('auth',token);
		res.cookie('username',username)
		res.redirect('/user/'+(username.length+password.length))
	}else {
		res.send("{status:200,info:no username or password}");
	}
});

router.get('/user/:id',async(req,res)=>{
	const id=req.params.id;
	var auth = req.cookies.auth;
	var username = req.cookies.username;
	if (undefined == username){
		res.clearCookie('auth');
		res.redirect("/login.php");
	}
	var cert = fs.readFileSync(process.cwd()+'//public/public.key');
	jwt.verify(auth, cert,function(err, decoded) {
		if(decoded.user==='admin'){
			if(id==1){
				res.render('success', {secr0t: "恭喜你解锁隐藏路由："+secr0t.secretP0ge});
			}else {
				res.render('loser', {loser: 'Id not 1：'+decoded.user});
			}
		}else{
			res.render("common",{info:'Hello user'+username+', but you are not admin and Nothing here(bushi'})
		}
	});

});

router.post('/gi0e10uF10g', function(req, res, next) {
	res.type('html');
	var auth = req.cookies.auth;
	var username = req.cookies.username;
	if (undefined == username||undefined == auth){
		res.clearCookie('auth');
		res.redirect("/login.php");
	}
	var cert = fs.readFileSync(process.cwd()+'//public/public.key');
	jwt.verify(auth, cert,function(err, decoded) {
		if(decoded.user==='admin'){
			var status = JSON.parse(req.query.status);
			if(!ezwaf(req.query.status)&&!ezfuncwaf(status.rce)&&status.rce!==undefined&&status.isAdmin===true&&status.isLogin===true){
				res.end('<h3>Hello your name is '+decoded.user+'</h3>Please RCE: '+eval(status.rce));
			}else{
				res.end('不行啊小老弟');
			}
		}else{
			res.end('<h3>Hello your name is '+decoded.user+'</h3>Please RCE: 666');
		}
	});

});

router.get('/source.php', function(req, res, next) {
	res.send(fs.readFileSync(process.cwd()+'//routes/source.js'))
});

function ezfuncwaf(res){
	if (res.match(/main|proto|process|require|exec|var|"|:|\[|\]|[0-9]/)){
		return true;
	}else {
		return false;
	}


}
function ezwaf(res){
	try {

		res.forEach(function(item, index){
			if (item.match(/,/)){
				return true;
			}
		});
	}catch (e) {
		if (res.match(/,/)){
			return true;
		}
	}
	return false;

}
module.exports = router;
