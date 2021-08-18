<?php

error_reporting(0);
class fileUtil{

    public static function open($filename, $content){
        //  未完成的功能，待开发
        return $content;
    }

    public static function checkLog(){
        if (!file_get_contents()){
            echo '';
        }else{
            echo "";
        }
    }

    public static function extractZip($file,$content){
        $zip = new ZipArchive();
        $res = $zip->open($file);
        if ($res){
            $zip->extractTo($content);
        }else{
            echo 'no ZipFile';
        }
        $zip->close();
    }


}

class ExportExcel{

    public $filename;
    public $exportname;
    public $do;

    public function __construct($filename, $exportname, $do)
    {
        $this->filename = $filename;
        $this->exportname = $exportname;
        $this->do = $do;
    }

    public function __invoke(){
        if (wudiWaf($this->do)&&wudiWaf($this->filename)){
            ($this->do)($this->filename);
        }

    }
}

function wudiWaf($name){
    if(preg_match('/system|call|proc|ob|mail|put|env|dl|ini|exec|array|create|_|ch|op|log|link|pcntl|imap|cat|tac|>|more|less|head|tail|nl|sort|od|base|awk|cut|grep|uniq|string|sh|sed|rev|zip|\\\\|py|[\x01-\x19]|\*|\?/',$name)){
        die("NO");
    }else{
        return true;
    }
}
