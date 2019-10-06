<?php
/**
 * OBot Setup
 * 2019/09/23
 */

class OBot_Setup {

 private $_configDir = '';

 private $_setting = array();

 /**
  * 初始化
  */
 public function __construct() {
  $this->_configDir = OBOT_DIR.'data/config.json';
 }

 public function start() {
  //检查是否配置完成
  self::checkSetup();
 }

 public function checkSetup() {
  if (!file_exists($this->_configDir)) {
   OBot_Console::diy("配置文件未找到，将在 3s 后进入设置程序.", "[{green}Setup{r}] ", false, true);
   sleep(3);
   self::startSetup();
  }

  if (file_exists($this->_configDir)) {
   OBot_Console::diy("配置 ".$this->_configDir." 已经加载.", "[{green}Setup{r}] ", true, true);
   return false;
  }
 }

 /**
  * 配置程序入口
  */
 public function startSetup() {
  $setup = '1';
  
  while(true) {
   switch($setup) {
    //第一步
    case '1':
     OBot_Console::diy("WebSocket 的监听地址（默认0.0.0.0）：", "[{green}Setup{r}] ", false, false);
     $ip = trim(fgets(STDIN));
     if ($ip == NULL) {
      $this->_setting['websocket']['ip'] = '0.0.0.0';
      OBot_Console::diy("{green}已设置 WebSocket 的监听地址：{pink}0.0.0.0{r}（默认）{r}\n", "", false, true);
     }elseif (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $ip)) {
      $this->_setting['websocket']['ip'] = $ip;
      OBot_Console::diy("{green}已设置 WebSocket 的监听地址：{pink}".$ip."{r}{r}\n", "", false, true);
     }else{
      OBot_Console::diy("{red}请输入正确的 IP（例如 0.0.0.0）{r}\n", "", false, true);
      break;
     }
     $setup = '2';
     break;
    
    //第二步
    case '2':
     OBot_Console::diy("WebSocket 的监听端口（默认23333）：", "[{green}Setup{r}] ", false, false);
     $port = trim(fgets(STDIN));
     if ($port == NULL) {
      $this->_setting['websocket']['port'] = '23333';
      OBot_Console::diy("{green}已设置 WebSocket 的监听端口：{pink}23333{r}（默认）{r}\n", "", false, true);
     }elseif (preg_match('/^([0-9]|[1-9]\d{1,3}|[1-5]\d{4}|6[0-5]{2}[0-3][0-5])$/', $port)){
      $this->_setting['websocket']['port'] = $port;
      OBot_Console::diy("{green}已设置 WebSocket 的监听端口：{pink}".$port."{r}{r}\n", "", false, true);
     }else{
      OBot_Console::diy("{red}请输入正确的端口（例如 23333 | 范围 0-65535）{r}\n", "", false, true);
      break;
     }
     $setup = '3';
     break;

    //第三步
    case '3':
     OBot_Console::diy("HTTP API 的URI（例如 127.0.0.1）：", "[{green}Setup{r}] ", false, false);
     $httpURI = trim(fgets(STDIN));
     if ($httpURI == NULL) {
      $this->_setting['cqhttp']['uri'] = '127.0.0.1';
      OBot_Console::diy("{green}已设置 HTTP API 的URI：{pink}127.0.0.1{r}（默认）{r}\n", "", false, true);
     }elseif (preg_match('/^http(s)?:\/\//i', $httpURI)) {
      OBot_Console::diy("{red}请勿带 http(s):// 请重新输入（例如 127.0.0.1）{r}\n", "", false, true);
      break;
     }else{
      $this->_setting['cqhttp']['uri'] = $httpURI;
      OBot_Console::diy("{green}已设置  HTTP API 的URI：{pink}".$httpURI."{r}{r}\n", "", false, true);
     }

     $setup = '4';
     break;

    //第四步
    case '4':
     OBot_Console::diy("HTTP API 的端口（默认 5700）：", "[{green}Setup{r}] ", false, false);
     $httpPort = trim(fgets(STDIN));
     if ($httpPort == NULL) {
      $this->_setting['cqhttp']['port'] = '5700';
      OBot_Console::diy("{green}已设置 HTTP API 的端口：{pink}5700{r}（默认）{r}\n", "", false, true);
     }elseif (preg_match('/^([0-9]|[1-9]\d{1,3}|[1-5]\d{4}|6[0-5]{2}[0-3][0-5])$/', $httpPort)) {
      $this->_setting['cqhttp']['port'] = $httpPort;
      OBot_Console::diy("{green}已设置 HTTP API 的端口：{pink}".$httpPort."{r}{r}\n", "", false, true);
     }else{
      OBot_Console::diy("{red}请输入正确的端口（例如 5700 | 范围 0-65535）{r}\n", "", false, true);
      break;
     }
      
     $setup = '5';
     break;
    
    //第五步
    case '5':
     OBot_Console::diy("HTTP API 是否启用了 SSL 链接 [yes/NO]", "[{green}Setup{r}] ", false, false);
     $httpSSL = trim(fgets(STDIN));
     if ($httpSSL == 'yes' || $httpSSL == 'Yes' || $httpSSL == 'YES') {
      $this->_setting['cqhttp']['ssl'] = 'true';
      OBot_Console::diy("{green}已保存 HTTP API{r} {red}SSL{r} {green}启用状态{r}\n", "", false, true);
     }elseif ($httpSSL == 'no' || $httpSSL == 'No' || $httpSSL == 'NO') {
      $this->_setting['cqhttp']['ssl'] = false;
      OBot_Console::diy("{green}没有启用 HTTP API{r} {red}SSL{r} {green}已经跳过此项{r}\n", "", false, true);
     }elseif($httpSSL == NULL){
      $this->_setting['cqhttp']['ssl'] = false;
      OBot_Console::diy("{green}没有启用 HTTP API{r} {red}SSL{r} {green}已经跳过此项{r}\n", "", false, true);
     }else{
      OBot_Console::diy("{red}输入错误 请输入 yes 或 no（默认为 No）{r}\n", "", false, true);
      break;
     }
     
     $setup = '6';
     break;

    //第六步
    case '6':
     OBot_Console::diy("HTTP API 是否设置了 {red}access_token{r} [yes/NO]: ", "[{green}Setup{r}] ", false, false);
     $httpTokenD = trim(fgets(STDIN));
     if ($httpTokenD == 'yes' || $httpTokenD == 'Yes' || $httpTokenD == 'YES') {
      $setup = '7';
      break;
     }elseif ($httpTokenD == 'no' || $httpTokenD == 'No' || $httpTokenD == 'NO') {
      $this->_setting['cqhttp']['token'] = false;
      OBot_Console::diy("{green}没有设置 HTTP API{r} {red}access_token{r} {green}已经跳过此项{r}\n", "", false, true);
      $setup = '8';
      break;
     }elseif($httpTokenD == NULL){
      $this->_setting['cqhttp']['token'] = false;
      OBot_Console::diy("{green}没有设置 HTTP API{r} {red}access_token{r} {green}已经跳过此项{r}\n", "", false, true);
      $setup = '8';
      break;
     }else{
      OBot_Console::diy("{red}输入错误 请输入 yes 或 no（默认为 No）{r}\n", "", false, true);
      break;
     }

    //第七步
    case '7':
     OBot_Console::diy("HTTP API 的 {red}access_token{r}：", "[{green}Setup{r}] ", false, false);
     $httpToken = trim(fgets(STDIN));
     if ($httpToken == NULL) {
      OBot_Console::diy("{red}access_token 不能为空{r}\n", "", false, true);
      break;
     }else{
      $this->_setting['cqhttp']['token'] = $httpToken;
      OBot_Console::diy("{green}已成功设置 HTTP API {red}access_token{r}.\n", "", false, true);
     }

     $setup = '8';
     break;
    
    //第八步
    case '8':
     OBot_Console::diy("BOT 的主人 QQ 号：", "[{green}Setup{r}] ", false, false);
     $qq = trim(fgets(STDIN));
     if ($qq == NULL) {
      OBot_Console::diy("{red}主人 QQ 号不能为空{r}\n", "", false, true);
      break;
     }elseif (preg_match('/^[1-9][0-9]{4,15}$/', $qq)){
      $this->_setting['admin']['qq'] = $qq;
      OBot_Console::diy("{green}已设置 BOT 的主人 QQ 号：{pink}".$qq."{r}{r}\n", "", false, true);
     }else{
      OBot_Console::diy("{red}主人 QQ 号格式错误{r}\n", "", false, true);
      break;
     }

     $setup = '9';
     break;

    //第九步
    case '9':
     OBot_Console::diy("BOT 的管理者 QQ 号（多个请空格分隔，如10001 10002 10003）：", "[{green}Setup{r}] ", false, false);
     $qqList = trim(fgets(STDIN));
     if ($qqList == NULL) {
      OBot_Console::diy("{green}管理者 QQ 号已经跳过设置{r}\n", "", false, true);
      $this->_setting['admin']['list'] = [$this->_setting['admin']['qq']];
      break 2;
     }else{
      OBot_Console::diy("{green}已设置管理者 QQ 号.{r}\n", "", false, true);
      $arr = explode(" ", $qqList);
      $arr = array_filter($arr);
      $this->_setting['admin']['list'] = $arr;
     }
     break 2;

    //默认返回
    default:
     OBot_Console::diy("{red}输入有误，请重新输入{r}\n", "", false, true);
     break;
   }
  }
  
  //保存必要的设置
  //请勿直接修改此处 如需修改请到 data/config.json 修改
  $this->_setting['log']['msg'] = false; //保存收到的消息（默认关闭 | 生产环境不建议开启）
  $this->_setting['log']['error'] = true; //保存错误日志 （建议开启 | 记录除了 Notice 的报错信息）
  
  $this->_setting['swoole']['daemonize'] = false; //守护进程
  $this->_setting['swoole']['worker_num'] = 8; //Worker 进程数
  $this->_setting['swoole']['task_worker_num'] = 8; //Task 进程数
  $this->_setting['swoole']['max_request'] = 1000; //worker 进程的最大任务数
  $this->_setting['swoole']['heartbeat_idle_time'] = 600; //心跳空闲时间
  $this->_setting['swoole']['heartbeat_check_interval'] = 300; //心跳检查间隔
  $this->_setting['swoole']['log_dir'] = OBOT_DIR.'data/logs/swoole/'; //Swoole 日志目录

  $this->_setting['console']['group'] = true; //控制台显示收到的群组消息
  $this->_setting['console']['private'] = true; //控制台显示收到的私聊消息
  $this->_setting['console']['others'] = false; //控制台显示收到的其他消息
  $this->_setting['console']['heartbeat'] = false; //控制台显示收到的心跳包

  self::saveConfig($this->_setting);
  OBot_Console::diy("{green}设置完成，3s 后将启动服务端 qwq{r}\n", "[{green}Setup{r}] ", false, true);

  sleep(3);
 }

 /**
  * 保存配置文件
  * @param array $data
  */
 public function saveConfig($data) {
  //将组数转换为 JSON
  $result = json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
  
  $wr = fopen($this->_configDir, "w+");
  fwrite($wr, $result);
  fclose($wr);
 }
}