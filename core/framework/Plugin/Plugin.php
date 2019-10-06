<?php
/**
 * Plugin Core
 */

class OBot_Plugin {

 /**
  * 收到的消息
  * @access private
  * @var array
  */
 private static $_data = array();

 /**
  * 插件指令
  * @access private
  * @var array
  */
 private static $_command = array();

 /**
  * 消息监听
  * @access private
  * @var array
  */
 private static $_messages = array();

 /**
  * 暂时存储变量
  * @access private
  * @var array
  */
 private static $_tmp = array();

 /**
  * 初始化
  */
 public function __construct($data) {
  self::$_data = $data;
 }

 public static function start() {
  if (self::$_data['message_type'] == NULL) return;
  self::loadPList();
  self::internalCommand();
  self::reqPlugins();
  self::handleCommand(self::$_command, self::$_data);
  self::handleMsg(self::$_messages, self::$_data, self::$_command);
 }

 /**
  * 检测插件
  */
 public static function loadPList() {
  //插件状态配置文件
  $cfg = OBOT_DIR.'data/Plugins.json';
  
  //读取插件
  $pl = glob(OBOT_DIR.'plugins/*/Plugin.php');
  
  //插件数量
  $plCount = count($pl);

  //循环读取插件基本信息
  for($i=0; $i<$plCount; $i++) {
   $plugins[] = self::parseInfo($pl[$i]);
  }

  //再次获取插件数量
  $plCount = count($plugins);

  //如果配置文件存在
  if (file_exists($cfg)) {
   $open = json_decode(file_get_contents($cfg), true);

   for($old=0; $old<count($open); $old++) {
    for($new=0; $new<count($plugins); $new++) {
     if ($open[$old]['pluginInfo']['name'] == $plugins[$new]['name'] && $open[$old]['pluginInfo']['path'] == $plugins[$new]['path']) {
      unset($plugins[$new]);
      $plugins = array_values($plugins);
      break;
     }
    }
   }

   //重新计算主键
   $plugins = array_values($plugins);
  
   //删除重复的插件后组成的新组数
   for ($news=0; $news<count($plugins); $news++) {
    $arr[] = array(
     'status' => 'disable',
     'pluginInfo' => array(
      'name' => $plugins[$news]['name'],
      'link' => $plugins[$news]['link'],
      'author' => $plugins[$news]['author'],
      'version' => $plugins[$news]['version'],
      'description' => $plugins[$news]['description'],
      'path' => $plugins[$news]['path']
     )
    );
   }

   if($arr != NULL){
    $arr = array_merge($open, $arr);
   }else{
    $arr = $open;
   }
   //END
  }else{
  //反之
   for ($ii=0; $ii<$plCount; $ii++) {
    $arr[] = array(
     'status' => 'disable',
     'pluginInfo' => array(
      'name' => $plugins[$ii]['name'],
      'link' => $plugins[$ii]['link'],
      'author' => $plugins[$ii]['author'],
      'version' => $plugins[$ii]['version'],
      'description' => $plugins[$ii]['description'],
      'path' => $plugins[$ii]['path']
     )
    );
   }
   //END
  }

  $result = json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
  $wr = fopen($cfg, "w+");
  fwrite($wr, $result);
  fclose($wr);
 }

 /**
  * 注册指令
  */
 public static function regCommand($method, $permission, $command) {
  if ($method == 'fuzzy') {
   $method = 'fuzzy';
  }elseif ($method == 'exact') {
   $method = 'exact';
  }elseif ($method == 'fuzzy_i'){
   $method = 'fuzzy_i';
  }else{
   $method = 'fuzzy';
  }

  if ($permission == '0' || $permission == '1' || $permission == '2' || $permission == '3') {
   $permission = $permission;
  }else{
   $permission = '0';
  }

  $arr = array(
   'command' => $command,
   'method' => $method,
   'permission' => $permission
  );
  for ($i=0;$i<count(self::$_command);$i++) {
   if(key(self::$_command[$i]['command']) == key($arr['command']) && self::$_command[$i]['method'] == $arr['method']) {
    return;
   }
  }
  
  self::$_command[] = $arr;
 }

 /**
  * 注册消息监听
  */
 public static function regMessage($rule) {
  for ($i=0; $i<count(self::$_messages); $i++) {
   if (is_array(self::$_messages[$i])) {
    if(key(self::$_messages[$i]) == key($rule) && current(self::$_messages[$i]) == current($rule)) {
     return;
    }
   }
  }
  
  self::$_messages[] = $rule;
 }

 /**
  * 内置指令
  */
 public static function internalCommand() {
  self::regCommand('fuzzy', '0', [
   '[!|！]plugin list' => ['InternalCommand', 'pluginList']
  ]);

  self::regCommand('fuzzy', '3', [
   '[!|！]plugin enable (.*)' => ['InternalCommand', 'enablePlugin']
  ]);

  self::regCommand('fuzzy', '3', [
   '[!|！]plugin disable (.*)' => ['InternalCommand', 'disablePlugin']
  ]);

  self::regCommand('fuzzy', '3', [
   '[!|！]status' => ['InternalCommand', 'botStatus']
  ]);
 }

 /**
  * 启用/禁用插件
  * @param string $method
  * @param string $name
  * @param string $status
  */
 public static function alterPlugin($method, $name) {
  $dir = OBOT_DIR.'data/Plugins.json';

  if (!file_exists($dir)) return 'dirnotfound';

  $arr = json_decode(file_get_contents($dir), true);

  for ($search=0; $search<count($arr); $search++) {
   if ($arr[$search]['pluginInfo']['name'] == $name) {
    $searchs = 'yes';
    break;
   }
  }

  if($searchs == NULL) return 'notfound';

  if ($method == 'enable') {
   for($enable=0; $enable<count($arr); $enable++) {
    if ($arr[$enable]['pluginInfo']['name'] == $name && $arr[$enable]['status'] == 'disable') {
     $arr[$enable]['status'] = 'enable';
     $rw = json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
     $wr = fopen($dir, "w+");
     fwrite($wr, $rw);
     fclose($wr);
     
     $status = 'activate';
     break;
    }

    if ($arr[$enable]['pluginInfo']['name'] == $name && $arr[$enable]['status'] == 'enable') {
     $status = 'activated';
     break;
    }
   }
  }elseif ($method == 'disable') {
   for($disable=0; $disable<count($arr); $disable++) {
    if ($arr[$disable]['pluginInfo']['name'] == $name && $arr[$disable]['status'] == 'enable') {
     $arr[$disable]['status'] = 'disable';
     $rw = json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
     $wr = fopen($dir, "w+");
     fwrite($wr, $rw);
     fclose($wr);
     
     $status = 'disable';
     break;
    }

    if ($arr[$disable]['pluginInfo']['name'] == $name && $arr[$disable]['status'] == 'disable') {
     $status = 'disabled';
     break;
    }
   }
  }

  return $status;
 }

 /**
  * 插件列表
  */
 public static function pluginList() {
  $dir = OBOT_DIR.'data/Plugins.json';

  if (!file_exists($dir)) return "[Warning] 没有安装任何插件哦 (っ °Д °;)っ";

  $arr = json_decode(file_get_contents($dir), true);

  $tmpDir = OBOT_DIR.'data/tmp/pluginList/';
  $tmpName = $tmpDir.date('Y-m-d_H-i-s_'.rand(0,1000)).'.tmp';
  if (!is_dir($tmpDir)) mkdir($tmpDir, 0777, true);

  for($pl=0; $pl<count($arr); $pl++){
   if ($arr[$pl]['status'] == 'disable') {
    $status = '禁用';
   }elseif ($arr[$pl]['status'] == 'enable') {
    $status = '启用';
   }
   $out = $arr[$pl]['pluginInfo']['name']."(".$status.")";

   if (file_exists($tmpName)) {
     $wr = fopen($tmpName, "a");
     fwrite($wr, "\n".$out);
     fclose($wr);
   }else{
     $wr = fopen($tmpName, "w+");
     fwrite($wr, $out);
     fclose($wr);
   }
  }

  $getList = file_get_contents($tmpName);
  $msg = "Plugins List:\n".$getList;
  return $msg;
 }

 /**
  * 读取插件信息
  * From Typecho
  * @param $file
  * @return $info
  */
 public static function parseInfo($file) {
  $tokens = token_get_all(file_get_contents($file));

  /** 初始信息 */
  $info = array(
   'name' => '',
   'link' => '',
   'author' => '',
   'version' => '',
   'description' => '',
   'path' => $file
  );

  $map = array(
   'link' => 'link',
   'package' => 'name',
   'author' => 'author',
   'version' => 'version'
  );
 
  foreach ($tokens as $token) {
   $described = false;
   $lines = preg_split("(\r|\n)", $token[1]);
   foreach ($lines as $line) {
    $line = trim($line);
    if (!empty($line) && '*' == $line[0]) {
     $line = trim(substr($line, 1));
     if (!$described && !empty($line) && '@' == $line[0]) {
      $described = true;
     }

     if (!$described && !empty($line)) {
      $info['description'] .= $line . "\n";
     } else if ($described && !empty($line) && '@' == $line[0]) {
      $info['description'] = trim($info['description']);
      $line = trim(substr($line, 1));
      $args = explode(' ', $line);
      $key = array_shift($args);

      if (isset($map[$key])) {
       $info[$map[$key]] = trim(implode(' ', $args));
      }
     }
    }
   }
  }

  return $info;
 }

 public static function reqPlugins() {
  $dir = OBOT_DIR.'data/Plugins.json';

  //如果没有插件
  if (!file_exists($dir)) {
   return false;
  }

  $list = json_decode(file_get_contents($dir), true);

  for($i=0;$i<count($list);$i++) {
   if ($list[$i]['pluginInfo']['name'] != NULL && $list[$i]['status'] == 'enable') {
    require_once $list[$i]['pluginInfo']['path'];
    preg_match_all('/class (.*)_Plugin(.*){/', file_get_contents($list[$i]['pluginInfo']['path']), $className);
    preg_match_all('/(.*)_Plugin/', $className[1][0], $className);
    self::executionPlugin($className[1][0].'_Plugin', 'handle');
   }
  }
 }

 public static function executionPlugin($class, $function) {
  $class::$function(self::$_data);
 }

 /**
  * 消息解析
  */
 public static function handleMsg($messages, $data, $command) {
  $count = count($messages);

  for($i=0; $i<$count; $i++) {
   call_user_func_array($messages[$i], [$data, $command]);
  }
 }

 /**
  * 指令解析
  */
 public static function handleCommand($command, $data) {
  $count = count($command);
  for($i=0;$i<$count;$i++) {

   //精准匹配指令
   if ($command[$i]['method'] == 'exact') {
    if ($data['message'] == key($command[$i]['command'])) {
     //判断权限
     if (self::permission($command[$i]['permission'], $data) == 'false') { CQ::sendMsg($data, '[Warning] 您无权使用此命令喔 ( ╯□╰ )'); break; }
     call_user_func_array(current($command[$i]['command']), array($data));
     break;
    }
   }
   
   //模糊匹配指令
   if ($command[$i]['method'] == 'fuzzy') {
    if (preg_match('/'.key($command[$i]['command']).'/', $data['message'])) {
     //判断权限
     if (self::permission($command[$i]['permission'], $data) == 'false') { CQ::sendMsg($data, '[Warning] 您无权使用此命令喔 ( ╯□╰ )'); break; }
     call_user_func_array(current($command[$i]['command']), array($data));
     break;
    }
   }

   //模糊匹配指令 | 不区分大小写
   if ($command[$i]['method'] == 'fuzzy_i') {
    if (preg_match('/'.key($command[$i]['command']).'/i', $data['message'])) {
     //判断权限
     if (self::permission($command[$i]['permission'], $data) == 'false') { CQ::sendMsg($data, '[Warning] 您无权使用此命令喔 ( ╯□╰ )'); break; }
     call_user_func_array(current($command[$i]['command']), array($data));
     break;
    }
   }
  }
 }

 /**
  * 权限判断
  */
 public static function permission($permission, $data) {
  $info = OBot_HTTP_API::diyAPI('get_group_member_info', ['group_id' => $data['group_id'], 'user_id' => $data['sender']['user_id']], true);
  $info = json_decode($info, true);
  
  switch ($permission) {
   //所有人
   case '0':
    $return = 'true';
    break;
   
   //群主、主人、管理列表
   case '1':
    if ($data['sender']['user_id'] == OBot_Config::basic('admin', 'qq') || $info['data']['role'] == 'owner') {
     $return = 'true';
    }else{
     $AList = OBot_Config::basic('admin', 'list');
     $ALists = '';
     for($list=0; $list<count($AList); $list++) {
      if ($data['sender']['user_id'] == $AList[$list]) {
       $ALists = 'true';
       break;
      }
     }
     if ($ALists == 'true') {
      $return = 'true';
     }else{
      $return = 'false';
     }
    }
    break;

   //群主、管理员、主人、管理列表
   case '2':
    if ($data['sender']['user_id'] == OBot_Config::basic('admin', 'qq') || $info['data']['role'] == 'admin' || $info['data']['role'] == 'owner') {
     $return = 'true';
    }else{
     $AList = OBot_Config::basic('admin', 'list');
     $ALists = '';
     for($list=0; $list<count($AList); $list++) {
      if ($data['sender']['user_id'] == $AList[$list]) {
       $ALists = 'true';
       break;
      }
     }
     if ($ALists == 'true') {
      $return = 'true';
     }else{
      $return = 'false';
     }
    }
    break;

   //主人、管理列表
   case '3':
    if ($data['sender']['user_id'] == OBot_Config::basic('admin', 'qq')) {
     $return = 'true';
    }else{
     $AList = OBot_Config::basic('admin', 'list');
     $ALists = '';
     for($list=0; $list<count($AList); $list++) {
      if ($data['sender']['user_id'] == $AList[$list]) {
       $ALists = 'true';
       break;
      }
     }
     if ($ALists == 'true') {
      $return = 'true';
     }else{
      $return = 'false';
     }
    }
    break;

   //主人
   case '4':
    if ($data['sender']['user_id'] == OBot_Config::basic('admin', 'qq')) {
     $return = 'true';
    }else{
     $return = 'false';
    }
    break;

   //默认所有人
   default:
    $return = 'true';
    break;
  }

  return $return;
 }
}