<?php
/**
 * OBot Logs
 * 2019/09/24
 */

class OBot_Message_Logs {

 public static function handle($data) {
  $writelog = OBot_Config::basic('log', 'msg');
  
  switch ($data['message_type']) {
   case 'private':
    self::personal($data, $writelog);
    break;
   case 'group':
    self::group($data, $writelog);
    break;
   default:
    if ($data['meta_event_type'] == 'heartbeat') {
     self::heartbeat($data);
     return false;
    }else{
     self::others($data, $writelog);
    }
  }
 }

 /**
  * Group
  */
 public static function group($data, $writelog) {
 $console = OBot_Config::basic('console', 'group');
 
 if ($writelog === true) {
  $dir = OBOT_DIR.'data/logs/messages/group/';
  $date = date('Y-m-d');
  $dirs = $dir.$date.'/'.$data['group_id'].'.log';

  if (!is_dir($dir.$date.'/')) mkdir($dir.$date.'/', 0777, true);
  
  $msgW = "===== Group[".$data['group_id']."] =====\n[".date('Y-m-d H:i:s')."] - ".$data['sender']['nickname']."(".$data['sender']['user_id'].")：\nRaw Mess: ".$data['raw_message']."\nCoolQ Receive Raw Data: ".json_encode($data)."\n\n";
  $msgA = "[".date('Y-m-d H:i:s')."] - ".$data['sender']['nickname']."(".$data['sender']['user_id'].")：\nRaw Mess: ".$data['raw_message']."\nCoolQ Receive Raw Data: ".json_encode($data)."\n\n";

  if (file_exists($dirs)) {
   $wr = fopen($dirs, "a");
   fwrite($wr, $msgA);
   fclose($wr);
  }else{
   $wr = fopen($dirs, "w+");
   fwrite($wr, $msgW);
   fclose($wr);
  }
 }

  if ($console === true) {
   $msg = $data['sender']['nickname']."(".$data['sender']['user_id'].")：".$data['message'];
   OBot_Console::diy($msg, "[{green}Group{r}({gold}".$data['group_id']."{r})] ");
  }
 }

 /**
  * Personal
  */
 public static function personal($data, $writelog) {
 $console = OBot_Config::basic('console', 'private');
 
 if ($writelog === true) {
  $dir = OBOT_DIR.'data/logs/messages/personal/';
  $date = date('Y-m-d');
  $dirs = $dir.$date.'/'.$data['sender']['user_id'].'.log';

  if (!is_dir($dir.$date.'/')) mkdir($dir.$date.'/', 0777, true);
  
  $msgW = "===== Personal[".$data['sender']['nickname']."(".$data['sender']['user_id'].")] =====\n[".date('Y-m-d H:i:s')."]\nRaw Mess: ".$data['raw_message']."\nCoolQ Receive Raw Data: ".json_encode($data)."\n\n";
  $msgA = "[".date('Y-m-d H:i:s')."]\nRaw Mess: ".$data['raw_message']."\nCoolQ Receive Raw Data: ".json_encode($data)."\n\n";

  if (file_exists($dirs)) {
   $wr = fopen($dirs, "a");
   fwrite($wr, $msgA);
   fclose($wr);
  }else{
   $wr = fopen($dirs, "w+");
   fwrite($wr, $msgW);
   fclose($wr);
  }
 }

  if ($console === true) {
   $msg = $data['sender']['nickname']."(".$data['sender']['user_id'].")：".$data['message'];
   OBot_Console::diy($msg, "[{green}Personal{r}] ");
  }
 }

 /**
  * Others
  */
 public static function others($data, $writelog) {
 $console = OBot_Config::basic('console', 'others');
 
 if ($writelog === true) {
  $dir = OBOT_DIR.'data/logs/messages/others/';
  $date = date('Y-m-d');
  $dirs = $dir.$date.'.log';

  if (!is_dir($dir)) mkdir($dir, 0777, true);
  
  $msgW = "===== Others =====\n[".date('Y-m-d H:i:s')."]\nCoolQ Receive Raw Data: ".json_encode($data)."\n\n";
  $msgA = "CoolQ Receive Raw Data: ".json_encode($data)."\n\n";

  if (file_exists($dirs)) {
   $wr = fopen($dirs, "a");
   fwrite($wr, $msgA);
   fclose($wr);
  }else{
   $wr = fopen($dirs, "w+");
   fwrite($wr, $msgW);
   fclose($wr);
  }
 }

  if ($console === true) {
   if ($writelog === true) {
    $msg = "Logs Dir: ".$dirs;
   }else{
    $msg = "收到一条未知的消息";
   }
   OBot_Console::diy($msg, "[{green}Other{r}] ");
  }
 }

 public static function heartbeat($data) {
  $console = OBot_Config::basic('console', 'heartbeat');
  
  if ($console === true) {
   $online = ($data['status']['online'] === true) ? '{green}Online{r}' : '{red}Offline{r}';
   $msg = "Bot: {pink}".$data['self_id']."{r} | Status: ".$online;
   OBot_Console::diy($msg, "[{gold}Heartbeat{r}] ");
  }
 }
}