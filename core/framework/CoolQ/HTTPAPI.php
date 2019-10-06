<?php
/**
 * OBot CoolQ HTTP API
 */

class OBot_HTTP_API {

 /**
  * HTTP API 上报地址
  *
  * @access private
  * @var string
  */
 private $_ip = '';

 /**
  * HTTP API 上报端口
  *
  * @access private
  * @var string
  */
 private $_port = '';

 /**
  * HTTP API SSL
  *
  * @access private
  * @var string
  */
 private $_ssl = '';

 /**
  * HTTP API Access Token
  *
  * @access private
  * @var string
  */
 private $_token = '';

 /**
  * HTTP API Header
  *
  * @access private
  * @var array
  */
 private $_header = array();

 /**
  * HTTP API 上报地址
  *
  * @access private
  * @var string
  */
 private $_url = '';

 /**
  * 错误代码
  */
 static $_error = [
  '1400' => 'POST 请求的正文格式不正确',
  '1404' => 'API 不存在',
  '201' => '工作线程池未正确初始化',
  '100' => '参数缺失或参数无效',
  '102' => 'CoolQ 操作权限不足',
  '103' => '用户权限不足或文件系统异常',
  '-1' => '请求发送失败',
  '-2' => '未收到服务器回复，可能未发送成功',
  '-3' => "消息过长或为空",
  '-4' => '消息解析过程异常',
  '-5' => '日志功能未启用',
  '-6' => '日志优先级错误',
  '-7' => '数据入库失败',
  '-8' => '不支持对系统帐号操作',
  '-9' => '帐号不在该群内，消息无法发送',
  '-10' => '该用户不存在/不在群内',
  '-11' => '数据错误，无法请求发送',
  '-12' => '不支持对匿名成员解除禁言',
  '-13' => '无法解析要禁言的匿名成员数据',
  '-14' => '由于未知原因，操作失败',
  '-15' => '群未开启匿名发言功能，或匿名帐号被禁言',
  '-16' => '帐号不在群内或网络错误，无法退出/解散该群',
  '-17' => '帐号为群主，无法退出该群',
  '-18' => '帐号非群主，无法解散该群',
  '-19' => '临时消息已失效或未建立',
  '-20' => '参数错误',
  '-21' => '临时消息已失效或未建立',
  '-22' => '获取QQ信息失败',
  '-23' => '找不到与目标 QQ 的关系，消息无法发送',
  '-26' => '消息过长'
 ];

 /**
  * API 列表
  */
 static $_api = [
   'send_private_msg',
   'send_group_msg',
   'send_discuss_msg',
   'send_msg',
   'delete_msg',
   'send_like',
   'set_group_kick',
   'set_group_ban',
   'set_group_anonymous_ban',
   'set_group_whole_ban',
   'set_group_admin',
   'set_group_anonymous',
   'set_group_card',
   'set_group_leave',
   'set_group_special_title',
   'set_discuss_leave',
   'set_friend_add_request',
   'set_group_add_request',
   'get_login_info',
   'get_stranger_info',
   'get_group_list',
   'get_group_member_info',
   'get_group_member_list',
   'get_cookies',
   'get_csrf_token',
   'get_credentials',
   'get_record',
   'get_image',
   'can_send_image',
   'can_send_record',
   'get_status',
   'get_version_info',
   'set_restart_plugin',
   'clean_data_dir',
   'clean_plugin_log'
  ];

 /**
  * 初始化
  */
 public function __construct() {
  $this->_ip = OBot_Config::basic('cqhttp', 'uri');
  $this->_port = OBot_Config::basic('cqhttp', 'port');
  if (OBot_Config::basic('cqhttp', 'ssl') == true) {
   $this->_ssl = 'https';
  }else{
   $this->_ssl = 'http';
  }
  if (OBot_Config::basic('cqhttp', 'token') != false) {
   $this->_token = OBot_Config::basic('cqhttp', 'token');
   $this->_header = ['Authorization: Bearer '.$this->_token];
  }else{
   $this->_token = NULL;
   $this->_header = NULL;
  }

  $this->_url = $this->_ssl.'://'.$this->_ip.':'.$this->_port.'/';
 }

 /**
  * Send Group Message
  *
  * @param int $group_id
  * @param string $message
  * @param bool $return
  * @return string $curl
  */
 public static function Group($group_id, $message, $return = false) {
  $api = 'send_group_msg';
  $url = (new self())->_url;
  $head = (new self())->_header;

  //Data
  $data = ['group_id' => $group_id, 'message' => $message];
  
  $curl = curl::get($url.$api, $data, $head);

  self::handleRetcode($curl);
  if ($return === true) { return $curl; }
 }

 /**
  * Send Privates Message
  *
  * @param int $user_id
  * @param string $message
  * @param bool $return
  * @return string $curl
  */
 public static function Privates($user_id, $message, $return = false) {
  $api = 'send_private_msg';
  $url = (new self())->_url;
  $head = (new self())->_header;

  //Data
  $data = ['user_id' => $user_id, 'message' => $message];
  
  $curl = curl::get($url.$api, $data, $head);

  self::handleRetcode($curl);
  if ($return === true) { return $curl; }
 }

 /**
  * Send Discuss Message
  *
  * @param int $discuss_id
  * @param string $message
  * @param bool $return
  * @return string $curl
  */
 public static function Discuss($discuss_id, $message, $return = false) {
  $api = 'send_discuss_msg';
  $url = (new self())->_url;
  $head = (new self())->_header;

  //Data
  $data = ['discuss_id' => $discuss_id, 'message' => $message];

  $curl = curl::get($url.$api, $data, $head);

  self::handleRetcode($curl);
  if ($return === true) { return $curl; }
 }

 /**
  * Delete Message
  *
  * @param int $message_id
  * @return string $curl
  */
 public static function DeleteMsg($message_id, $return = false) {
  $api = 'delete_msg';
  $url = (new self())->_url;
  $head = (new self())->_header;

  //Data
  $data = ['message_id' => $message_id];
  
  $curl = curl::get($url.$api, $data, $head);

  self::handleRetcode($curl);
  if ($return === true) { return $curl; }
 }

 /**
  * DIY API
  *
  * @param string $api
  * @param array $data
  * @param bool $return
  * @return string $curl
  */
 public static function diyAPI($api, array $data = NULL, $return = false) {
  //判断API是否存在
  self::handleAPI($api);
  
  $url = (new self())->_url;
  $head = (new self())->_header;

  $curl = curl::get($url.$api, $data, $head);

  self::handleRetcode($curl);
  if ($return === true) { return $curl; }
 }


 /**
  * Retcode Handle
  * @param string $data
  */
 public static function handleRetcode($data) {
  $data = json_decode($data, true);
  $retcode = $data['retcode'];

  if ($retcode == '0' || $retcode == '1') return true;

  $error = self::$_error;

  for ($i=0;$i<count($error);$i++) {
   $code = key($error);
   $msg = current($error);
   if ($retcode == $code) {
    $out = (['retcode'=>$code, 'msg'=>$msg]);
    break;
   }
   next($error);
  }
  if($retcode == NULL) $retcode = '-233';
  if ($out == NULL) $out = ['retcode'=>$retcode, 'msg'=>'未知错误代码'];

  OBot_Console::diy("Code: {pink}".$out['retcode']."{r} | Description: {pink}".$out['msg']."{r}", "[{red}HTTP API ERROR{r}] ");
 }

 /**
  * API Handle
  * @param string $api
  */
 public static function handleAPI($api) {
  $_api = self::$_api;
  $output = '';

  for($i=0;$i<count($_api);$i++) {
   if ($api == $_api[$i]) {
    $output = $_api[$i];
    break;
   }
  }

  if ($output == NULL || $output == '') OBot_Console::diy("找不到名为 {pink}".$api."{r} 的 HTTP API.", "[{red}HTTP API ERROR{r}] ");
 }

}