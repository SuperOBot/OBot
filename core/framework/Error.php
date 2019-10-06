<?php
/**
 * 错误处理板块
 * 
 * @author ohmyga
 * @version 0.1.0
 * 2019/08/17
 */

/**
 * 记录Logs
 * 
 * @param $data
 */
function logs($data) {
 $ws = OBot_Config::basic('log', 'error');
 
 if ($ws != false) {
  //Log目录
  $logDIR = OBOT_DIR.'data/logs/error/';

  //Log名称 格式：年-月-日(Y-m-d)
  $logName = date('Y-m-d').'.log';

  if (!is_dir($logDIR)) mkdir($logDIR, 0777, true);

  //判断是否Log是否存在
  //存在则直接写入 不存在则创建后写入
  if (file_exists($logDIR.$logName)) {
   $wr = fopen($logDIR.$logName, "a");
   fwrite($wr, "\n".$data);
   fclose($wr);
  }else{
   $wr = fopen($logDIR.$logName, "w+");
   fwrite($wr, $data);
   fclose($wr);
  }
 }
}

/**
 * 错误处理器
 * @param $error_level
 * @param $error_message
 * @param $error_file
 * @param $error_line
 * @param $error_context
**/
function error_handler($error_level,$error_message,$error_file,$error_line,$error_context) {
 /**
  * Error Level
  **/
  switch ($error_level) {
    case 2:
     $level = 'WARNING';
     break;
    case 8:
     $level = 'INFO';
     break;
    case 256:
     $level = 'ERROR';
     break;
    case 512:
     $level = 'WARNING';
     break;
    case 1024:
     $level = 'NOTICE';
     break;
    case 4096:
     $level = 'ERROR';
     break;
    case 8191:
     $level = 'ERROR';
     break;
   }

 //组合成错误信息发送给 logs() 函数处理
 $data = date("[Y-m-d H:i:s]").' PHP '.$level.': '.$error_message.' in '.$error_file.' on line '.$error_line.'.';
 //如果错误级别为 INFO 则无需记录且不报错
 if ($level == 'INFO') {
  return true;
 }else{
  logs($data);
 }

 if (OBOT_TYPE == 'Cli') {
 switch ($level) {
  case 'WARNING':
   OBot_Console::warning("{yellow}{$error_message} in {$error_file} on line {$error_line}.{r}");
   break;
  case 'INFO':
   OBot_Console::info("{gold}{$error_message} in {$error_file} on line {$error_line}.{r}");
   break;
  case 'ERROR':
   OBot_Console::error("{red}{$error_message} in {$error_file} on line {$error_line}.{r}");
   break;
  case 'NOTICE':
   return true;
   break;
  default:
   OBot_Console::error("{red}{$error_message} in {$error_file} on line {$error_line}.{r}");
   break;
 }
 }

 die();
}
set_error_handler("error_handler", E_ALL | E_STRICT);

/**
 * 致命错误处理器
 * @param $exception
 **/
function eachxception_handler($exception){

 //组合成错误信息发送给 logs() 函数处理
 $data = date("[Y-m-d H:i:s]").' PHP Exception: '.$exception->getMessage().' in '.$exception->getFile().' on line '.$exception->getLine().'.';
 logs($data);
 
 if (OBOT_TYPE == 'Cli') {
  OBot_Console::diy("{red}{$exception->getMessage()} in {$exception->getFile()} on line {$exception->getLine()}.{r}", "[{red}Exception{r}] ");
 }
 die();
}
set_exception_handler("eachxception_handler");