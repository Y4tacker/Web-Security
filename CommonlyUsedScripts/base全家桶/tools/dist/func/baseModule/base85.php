<?php
require __DIR__ . '/vendor/autoload.php';
use Tuupola\Base85;


class b851{
    public static function do($b,$c){
        $ascii85 = new Base85([
        "characters" => Base85::ASCII85,
        "compress.spaces" => false, "compress.zeroes" => true
    ]);
       if("1"==$b){
            echo($ascii85->encode($c));
       }else{
            echo($ascii85->decode($c));
       }
    }
}

class b852{
    public static function do($b,$c){
        $adobe85 = new Base85([
    "characters" => Base85::ASCII85,
    "compress.spaces" => false, "compress.zeroes" => true,
    "prefix" => "<~", "suffix" => "~>"
    ]);
       if("1"==$b){
            echo($adobe85->encode($c));
       }else{
            echo($adobe85->decode($c));
       }
    }
}

class b853{
    public static function do($b,$c){
        $z85 = new Base85([
    "characters" => Base85::Z85,
    "compress.spaces" => false, "compress.zeroes" => false
    ]);
       if("1"==$b){
            echo($z85->encode($c));
       }else{
            echo($z85->decode($c));
       }
    }
}

class b854{
    public static function do($b,$c){
        $rfc1924 = new Base85([
    "characters" => Base85::RFC1924,
    "compress.spaces" => false, "compress.zeroes" => false
]);
       if("1"==$b){
            echo($rfc1924->encode($c));
       }else{
            echo($rfc1924->decode($c));
       }
    }
}

$a= $argv[1];
$b= $argv[2];
$c= $argv[3];
if("1"==$a){
    b851::do($b,$c);
}elseif("2"==$a){
    b852::do($b,$c);
}elseif("3"==$a){
    b853::do($b,$c);
}elseif("4"==$a){
    b854::do($b,$c);
}