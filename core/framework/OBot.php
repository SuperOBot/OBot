<?php
/**
 * OBot Class
 * 2019/09/24
 */

class OBot {

 /**
  * 初始化
  */
 public function __construct() {
  $tmpDir = OBOT_DIR.'data/tmp/';
  if (!is_dir($tmpDir)) mkdir($tmpDir, 0777, true);

  $appDir = OBOT_DIR.'data/app/';
  if (!is_dir($appDir)) mkdir($appDir, 0777, true);

  $logDir = OBOT_DIR.'data/logs/';
  if (!is_dir($logDir)) mkdir($logDir, 0777, true);
 }

 /**
  * 接收消息
  */
 public static function receiveMsg($data) {
  $plugin = new OBot_Plugin($data);
  $plugin->start();
 }

 /**
  * 创建应用自身的数据目录
  */
 public static function needData($file) {
  $info = OBot_Plugin::parseInfo($file);

  $dir = OBOT_DIR.'data/app/'.$info['name'].'/';

  if (!is_dir($dir)) mkdir($dir, 0777, true);
 }

 /**
  * 获取插件数据目录
  */
 public static function getData($file) {
  $info = OBot_Plugin::parseInfo($file);

  $dir = OBOT_DIR.'data/app/'.$info['name'].'/';

  return $dir;
 }
}