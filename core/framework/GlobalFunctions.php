<?php
/**
 * OBot Global Functions
 * 2019/09/23
 */

/**
 * 命令行颜色
 * From Github crazywhalecc/cqbot-swoole
 */
function color($text) {
 $text = str_replace("{red}", "\e[38;5;203m", $text);
 $text = str_replace("{green}", "\e[38;5;83m", $text);
 $text = str_replace("{yellow}", "\e[38;5;227m", $text);
 $text = str_replace("{lightpurple}", "\e[38;5;207m", $text);
 $text = str_replace("{lightblue}", "\e[38;5;87m", $text);
 $text = str_replace("{gold}", "\e[38;5;214m", $text);
 $text = str_replace("{gray}", "\e[38;5;59m", $text);
 $text = str_replace("{pink}", "\e[38;5;207m", $text);
 $text = str_replace("{lightlightblue}", "\e[38;5;63m", $text);
 $text = str_replace("{r}", "\e[m", $text);
 $text .= "\e[m";
 
 return $text;
}

/**
 * 引入文件
 */
function requireFile($dir, $type) {
 $all = glob($dir.'*.'.$type);
 foreach ($all as $file) {
  require_once $file;
 }
}

/**
 * Unicode解析
 * @param $str
 * @return null|string|string[]
 */
function unicodeDecode($str) {
 return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', function ($matches) {
  return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");
 }, $str);
}

/**
 * ASCII解析
 */
function asciiDecode($str, $prefix="&#") {
 $str = str_replace($prefix, "", $str);
 $a = explode(";", $str);
 foreach ($a as $dec) {
  if ($dec < 128) {
   $utf .= chr($dec);
  }else if ($dec < 2048) {
   $utf .= chr(192 + (($dec - ($dec % 64)) / 64));
   $utf .= chr(128 + ($dec % 64));
  }else{
   $utf .= chr(224 + (($dec - ($dec % 4096)) / 4096));
   $utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
   $utf .= chr(128 + ($dec % 64));
  }
 }
 return $utf;
}