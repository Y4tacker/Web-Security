<?php

/*
 * ***********************************************************************
 * Copyright © Erich Pribitzer 2012
 *
 * This file is part of base128
    base128 is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    Cmsfromscratch is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with Cmsfromscratch.  If not, see <http://www.gnu.org/licenses/>.
    ***********************************************************************
 */



class base128
{
    // iso 8859-1 removed chars <>?'"`+&/\
    private static $ascii='!#$%()*,.0123456789:;=@ABCDEFGHIJKLMNOPQRSTUVWXYZ[]^_abcdefghijklmnopqrstuvwxyz{|}~¡¢£¤¥¦§¨©ª«¬®¯°±²³´µ¶·¸¹º»¼½¾¿ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎ';

    public static function encode ($buffer)
    {
        return self::encode_custom($buffer,self::$ascii);
    }

    public static function decode($buffer)
    {
        return self::decode_custom($buffer,self::$ascii);
    }


    public static function encode_custom($buffer,$ascii)
    {
        $size=strlen($buffer);
        $size++;                // add an empty byte to the end
        $ls=0;
        $rs=7;
        $r=0;
        $encoded="";

        for($inx=0;$inx<$size;$inx++)
        {
            if($ls>7)
            {
                $inx--;
                $ls=0;
                $rs=7;
            }
            $nc=ord(substr($buffer,$inx,1));
            $r1=$nc;                 // save $nc
            $nc=($nc<<$ls);          // shift left for $rs
            $nc=($nc & 0x7f)|$r;     // OR carry bits
            $r=($r1>>$rs) & 0x7F;    // shift right and save carry bits
            $ls++;
            $rs--;
            $encoded.=substr($ascii,$nc,1);
        }
        return $encoded;
    }

    public static function decode_custom($buffer,$ascii)
    {
        $size=strlen($buffer);
        $rs=8;
        $ls=7;
        $r=0;
        $decoded="";

        for($inx=0;$inx<$size;$inx++)
        {
            $nc=strpos($ascii,substr($buffer,$inx,1));
            if($rs>7)
            {
                $rs=1;
                $ls=7;
                $r=$nc;
                continue;
            }
            $r1=$nc;
            $nc=($nc<<$ls) & 0xFF;
            $nc=$nc|$r;
            $r=$r1>>$rs;
            $rs++;
            $ls--;
            $decoded.=chr($nc);
        }
        return $decoded;
    }
}

$a= $argv[1];
$b= $argv[2];
if("1"==$a){
    $x=base128::encode($b);
}else{
    $x=base128::decode($b);
}
echo($x);
?>