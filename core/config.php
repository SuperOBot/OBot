<?php
/**
 * OBot Config
 * 2019/09/23
 */

class OBot_Config {

 public static function basic($value, $value2) {
  $file = file_get_contents(OBOT_DIR.'data/config.json');
  $arr = json_decode($file, true);

  if ($value2 != NULL) {
   $out = $arr[$value][$value2];
  }else{
   $out = $arr[$value];
  }

  return $out;
 }

}