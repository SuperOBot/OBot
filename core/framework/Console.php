<?php
/**
 * OBot Console
 * 2019/09/23
 */

class OBot_Console {

 /**
  * Info
  */
 public static function info($msg, $r = true) {
  if ($r == true) $end = "\n";
  $before = "[".date("Y-m-d H:i:s")."][{gold}INFO{r}] ";
  $after = ".";
  $merge = color($before.$msg.$after.$end);
  echo $merge;
 }

 /**
  * Warning
  */
 public static function warning($msg, $r = true) {
  if ($r == true) $end = "\n";
  $before = "[".date("Y-m-d H:i:s")."][{yellow}WARNING{r}] ";
  $after = ".";
  $merge = color($before.$msg.$after.$end);
  echo $merge;
 }

 /**
  * Error
  */
 public static function error($msg, $r = true) {
  if ($r == true) $end = "\n";
  $before = "[".date("Y-m-d H:i:s")."][{red}ERROR{r}] ";
  $after = ".";
  $merge = color($before.$msg.$after.$end);
  echo $merge;
 }

 /**
  * DIY Msg
  */
 public static function diy($msg, $level = NULL, $time = true, $r = true) {
  $time = ($time != NULL) ? "[".date('Y-m-d H:i:s')."]" : NULL;
  $r = ($r != false) ? "\n" : NULL;
  $level = ($level != NULL) ? $level : NULL;

  $merge = color($time.$level.$msg.$r);

  echo $merge;
 }
}