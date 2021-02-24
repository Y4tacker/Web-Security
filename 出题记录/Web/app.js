var createError = require('http-errors');
var express = require('express');
var path = require('path');
var cookieParser = require('cookie-parser');
var logger = require('morgan');
var indexRouter = require('./routes/index'); /* 朋友我明天上班请假了，配置我已经给你搞好了，你刚学nodejs，public目录下有我给你敲的示例，你跟着敲一下，加点错误逻辑就可以上线了，别忘了删除啊 */
var app = express();

app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'html');


app.use(logger('dev'));
app.use(express.json());
app.use(express.urlencoded({ extended: false }));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'public')));

app.use('/', indexRouter);
//app.use('/users', usersRouter);

// catch 404 and forward to error handler
app.use(function(req, res, next) {
    res.json({
        "error": "404"
    })
    next(createError(404));
});

// error handler

module.exports = app;
