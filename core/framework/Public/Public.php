<?php
/**
 * OBot Public
 * 2019/10/04
 */

class OBot_Public {

 /**
  * 重写规则
  * @access private
  * @var array
  */
 private static $_rule = array();

 public static function start() {
  self::loadPuList();
  self::internalRoute();
  self::reqPublic();
  self::handleRoute(self::$_rule, self::$_rule);
 }

 public static function loadPuList() {
  //公用板块配置
  $cfg = OBOT_DIR.'data/Public.json';
  
  //读取插件配套的公用板块
  $pu = glob(OBOT_DIR.'plugins/*/Public.php');

  //如果没公用板块
  if ($pu == NULL) { return false; }
  
  //公用板块数量
  $count = count($pu);

  //读取公用板块信息
  //为了不增加体积直接使用 Plugin 板块的读取
  //其实是我懒（（
  for($i=0; $i<$count; $i++) {
   $public[] = OBot_Plugin::parseInfo($pu[$i]);
  }

  //如果配置文件存在
  //这部分就直接复制过来吧！
  if (file_exists($cfg)) {
   $open = json_decode(file_get_contents($cfg), true);

   for($old=0; $old<count($open); $old++) {
    for($new=0; $new<count($public); $new++) {
     if ($open[$old]['publicInfo']['name'] == $public[$new]['name'] && $open[$old]['publicInfo']['path'] == $public[$new]['path']) {
      unset($public[$new]);
      $public = array_values($public);
      break;
     }
    }
   }
   
   //重新计算主键
   $public = array_values($public);
  
   //删除重复的插件后组成的新组数
   for ($news=0; $news<count($public); $news++) {
    $arr[] = array(
     'publicInfo' => array(
      'name' => $public[$news]['name'],
      'link' => $public[$news]['link'],
      'author' => $public[$news]['author'],
      'version' => $public[$news]['version'],
      'description' => $public[$news]['description'],
      'path' => $public[$news]['path']
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
   for ($ii=0; $ii<$count; $ii++) {
    $arr[] = array(
     'publicInfo' => array(
      'name' => $public[$ii]['name'],
      'link' => $public[$ii]['link'],
      'author' => $public[$ii]['author'],
      'version' => $public[$ii]['version'],
      'description' => $public[$ii]['description'],
      'path' => $public[$ii]['path']
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
  * 内置重写规则
  */
 public static function internalRoute() {
  self::reqRoute('file', [
   '/' => OBOT_DIR.'pages/home.php'
  ]);
 }

 /**
  * 注册规则
  */
 public static function reqRoute($method, $rule) {
  if ($method == 'file') {
   $method = 'file';
  }elseif ($method == 'class') {
   $method = 'class';
  }else{
   $method = 'file';
  }

  $arr = [
   'route' => $rule,
   'method' => $method
  ];
  for ($i=0;$i<count(self::$_rule);$i++) {
   if(key(self::$_rule[$i]['route']) == key($arr['route'])) {
    return;
   }
  }

  self::$_rule[] = $arr;
 }

 /**
  * 注册插件
  */
 public static function reqPublic() {
  $dir = OBOT_DIR.'data/Public.json';
  
  //如果没有公用板块
  if (!file_exists($dir)) {
   return false;
  }

  $list = json_decode(file_get_contents($dir), true);

  for($i=0; $i<count($list); $i++) {
   if ($list[$i]['publicInfo']['path'] != NULL ) {
    require_once $list[$i]['publicInfo']['path'];
    preg_match_all('/class (.*)_Public(.*){/', file_get_contents($list[$i]['publicInfo']['path']), $className);
    preg_match_all('/(.*)_Public/', $className[1][0], $className);
    self::executionPublic($className[1][0].'_Public', 'handle');
   }
  }
 }

 public static function executionPublic($class, $function) {
  $class::$function();
 }

 /**
  * 解析引用规则文件
  */
 public static function handleRoute($route, $data) {
  $count = count($route);

  for($i=0; $i<$count; $i++) {
   //获取键名(重写规则名
   $requestUrl = key($route[$i]['route']);
      
   //当前规则的值
   $routeUrl = current($route[$i]['route']);
   
   //正则替换 /?xx=xx 以及 双斜杆 以免404
   $urls = preg_replace("(\?.*)","",$_SERVER['REQUEST_URI']);
   $urls = preg_replace("/\/\//","/",$urls);

   if($requestUrl == $urls) {
    if ($route[$i]['method'] == 'class') {
     call_user_func_array($routeUrl, $data);
     break;
    }

    if ($route[$i]['method'] == 'file') {
     require_once $routeUrl;
     break;
    }
   }
  }
 }

}