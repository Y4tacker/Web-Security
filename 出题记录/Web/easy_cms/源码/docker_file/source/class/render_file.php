<?php
include('file_class.php');

class renderUtil
{
    public $file;
    public $filename;
    public $content;

    public function __construct($file, $filename="", $content = '')
    {
        $this->file = empty($file) ? (new fileUtil()) : $file;
        $this->filename = $filename;
        $this->content = $content;
    }

    public static function render($template,$page, $arg = array())
    {
        $whiteList = ['', 'admin','category_list','add_category',"link_list"];
        empty($template) ? '' : $template;
        empty($page) ? '' : $page;
        if (in_array($template, $whiteList)) {
            if (preg_match("/[\x01-\x2c]|flag|\.\.|\.|\\\\/i", $page)) {
                die("hacker");
            }
            require("class/templates/$page/$template.php");
        } else {
            header("location:index.php?c=login");
        }

    }

    public static function autoHtRender($template,$page, $arg = array())
    {
        $whiteList = ['', 'admin','category_list','add_category',"link_list","login","add_link"];
        empty($template) ? '' : $template;
        empty($page) ? '' : $page;
        if (in_array($page, $whiteList)) {
            if (preg_match("/[\x01-\x2c]|flag|\.\.|\.|\\\\/i", $template)) {
                die("hacker");
            }
            require("class/templates/$template/$page.php");
        } else {
            header("location:index.php?c=login");
        }

    }

    public static function shade($templateContent, $arg = array())
    {
        $templateContent = templateUtil::retImage($templateContent);
        return $templateContent;
    }

    public static function retImage($templateContent, $arg = array())
    {
        foreach ($arg as $key => $value) {
            if (stripos($templateContent, '{{img:' . $key . '}}')) {
                $templateContent = str_replace('{{img:' . $key . '}}', $value, $templateContent);
            }

        }
        return $templateContent;
    }

    public static function checkVar($templateContent, $arg)
    {
        foreach ($arg as $key => $value) {
            if (stripos($templateContent, '{{var:' . $key . '}}')) {
                if (!preg_match('/\(|\[|\`|\'|\"|\+|nginx|\)|\]|include|data|text|filter|input|file|require|GET|POST|COOKIE|SESSION|file/i', $value)) {
                    $v=$value;
                    $templateContent = str_replace('{{var:' . $key . '}}', $v, $templateContent);
                }

            }
        }
        return $templateContent;
    }


    public function __destruct(){
        if (!empty($this->file)){
            $ret = $this->file->open($this->filename,$this->content);
        }
        if (!empty($ret)){
            fileUtil::extractZip($this->filename, $this->content);
        }
    }

    public function __toString(){
        $tmp = file_get_contents('class/cache/lastTime.txt');
        return empty(explode("：",$tmp)[1])?"Error":explode("：",$tmp)[1];
    }

}
