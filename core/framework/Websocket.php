<?php
/**
 * OBot WebSocket
 * 2019/09/23
 */

class OBot_Server {

 public function start() {
  $server = new Swoole\WebSocket\Server(OBot_Config::basic('websocket', 'ip'), OBot_Config::basic('websocket', 'port'));

  $swLogDir = OBot_Config::basic('swoole', 'log_dir');
  if (!is_dir($swLogDir)) mkdir($swLogDir, 0777, true);

  $server->set([
   'daemonize' => OBot_Config::basic('swoole', 'daemonize'), //守护进程
   'worker_num' => OBot_Config::basic('swoole', 'worker_num'), //Worker 进程数
   'task_worker_num' => OBot_Config::basic('swoole', 'task_worker_num'), //Task 进程数
   'max_request' => OBot_Config::basic('swoole', 'max_request'), //worker 进程的最大任务数
   'heartbeat_idle_time' => OBot_Config::basic('swoole', 'heartbeat_idle_time'), //心跳空闲时间
   'heartbeat_check_interval' => OBot_Config::basic('swoole', 'heartbeat_check_interval'), //心跳检查间隔
   'log_file' => $swLogDir.date('Y-m-d').'.log' //Swoole 日志目录
  ]);

  $server->on('start', [$this, 'onStart']);
  $server->on('open', [$this, 'onOpen']);
  $server->on('Message', [$this, 'onMessage']);
  $server->on('task', [$this, 'onTask']);
  $server->on("close", [$this, 'onClose']);
  $server->start();
 }

 public function onStart($server) {
  OBot_Console::diy("{green}已成功绑定并开始监听{r} {gold}".OBot_Config::basic('websocket', 'ip').":".OBot_Config::basic('websocket', 'port')."{r}{green}.{r}", "[{lightblue}WebSocket{r}] ");
 }

 public function onOpen($server, $request) {
  OBot_Console::diy("{green}与客户端{r} {gold}{$request->fd}{r} {green}号握手成功{r}", "[{lightblue}WebSocket{r}] ");
 }

 public function onMessage($server, $frame) {
  $arr = json_decode($frame->data, true);
  $server->task($arr);
 }

 public function onTask($server, $task_id, $from_id, $data) {
  OBot_Message_Logs::handle($data);
  $OBot = new OBot();
  $OBot->receiveMsg($data);
 }

 public function onClose($server, $fd) {
  OBot_Console::diy("{red}与客户端{r} {gold}{$fd}{r} {red}号失去连接{r}", "[{lightblue}WebSocket{r}] ");
 }
}