<?php
/**
 * OBot Internal Commands
 */

class InternalCommand {

 /**
  * 插件列表
  */
 public static function pluginList($data) {
  $msg = OBot_Plugin::pluginList();
  CQ::sendMsg($data, $msg);
 }

 /**
  * 启用插件
  */
 public static function enablePlugin($data) {
  preg_match_all('/[!|！]plugin enable (.*)/i', $data['message'], $pluginName);
  $return = OBot_Plugin::alterPlugin('enable', $pluginName[1][0]);
  if ($return == 'activate') {
   CQ::sendMsg($data, '[Info] 插件 '.$pluginName[1][0].' 已经启用。');
   OBot_Console::diy("插件 {pink}".$pluginName[1][0]."{r} 启用成功 | 操作者: {pink}".$data['sender']['nickname']."{r}({gold}".$data['sender']['user_id']."{r})", "[{lightblue}Plugin{r}] ");
  }
  if ($return == 'activated') {
   CQ::sendMsg($data, '[Info] 插件 '.$pluginName[1][0].' 已启用，请勿重复启用。');
   OBot_Console::diy("插件 {pink}".$pluginName[1][0]."{r} 处于启用状态，无需重复启用 | 操作者: {pink}".$data['sender']['nickname']."{r}({gold}".$data['sender']['user_id']."{r})", "[{lightblue}Plugin{r}] ");
  }
  if ($return == 'notfound') {
   CQ::sendMsg($data, '[Warning] 插件 '.$pluginName[1][0].' 不存在！');
   OBot_Console::diy("操作者: {pink}".$data['sender']['nickname']."{pink}({gold}".$data['sender']['user_id']."{r}) 试图启用名为 {pink}".$pluginName[1][0]."{r} 不存在的插件", "[{yellow}Plugin Warning{r}] ");
  }
  if ($return == 'dirnotfound') {
   CQ::sendMsg($data, '[Error] 插件配置文件不存在，或许没安装插件！');
   OBot_Console::diy("{red}插件配置文件不存在，或许没安装任何插件！{r} | 操作者: {pink}".$data['sender']['nickname']."{r}({gold}".$data['sender']['user_id']."{r})", "[{red}Plugin Error{r}] ");
  }
 }

 /**
  * 禁用插件
  */
 public static function disablePlugin($data) {
  preg_match_all('/[!|！]plugin disable (.*)/i', $data['message'], $pluginName);
  $return = OBot_Plugin::alterPlugin('disable', $pluginName[1][0]);
  if ($return == 'disable') {
   CQ::sendMsg($data, '[Info] 插件 '.$pluginName[1][0].' 已经禁用。');
   OBot_Console::diy("插件 {pink}".$pluginName[1][0]."{r} 禁用成功 | 操作者: {pink}".$data['sender']['nickname']."{r}({gold}".$data['sender']['user_id']."{r})", "[{lightblue}Plugin{r}] ");
  }
  if ($return == 'disabled') {
   CQ::sendMsg($data, '[Info] 插件 '.$pluginName[1][0].' 已启用，请勿重复禁用。');
   OBot_Console::diy("插件 {pink}".$pluginName[1][0]."{r} 处于禁用状态，无需重复禁用 | 操作者: {pink}".$data['sender']['nickname']."{r}({gold}".$data['sender']['user_id']."{r})", "[{lightblue}Plugin{r}] ");
  }
  if ($return == 'notfound') {
   CQ::sendMsg($data, '[Warning] 插件 '.$pluginName[1][0].' 不存在！');
   OBot_Console::diy("操作者: {pink}".$data['sender']['nickname']."{pink}({gold}".$data['sender']['user_id']."{r}) 试图禁用名为 {pink}".$pluginName[1][0]."{r} 不存在的插件", "[{yellow}Plugin Warning{r}] ");
  }
  if ($return == 'dirnotfound') {
   CQ::sendMsg($data, '[Error] 插件配置文件不存在，或许没安装插件！');
   OBot_Console::diy("{red}插件配置文件不存在，或许没安装任何插件！{r} | 操作者: {pink}".$data['sender']['nickname']."{r}({gold}".$data['sender']['user_id']."{r})", "[{red}Plugin Error{r}] ");
  }
 }

 /**
  * Bot 状态
  */
 public static function botStatus($data) {
  $info = OBot_HTTP_API::diyAPI('get_login_info', NULL, true);
  $arr = json_decode($info, true);

  $on = OBot_HTTP_API::diyAPI('get_status', NULL, true);
  $arrOn = json_decode($on ,true);

  $enable = 0;
  $disable = 0;

  if (file_exists(OBOT_DIR.'data/Plugins.json')) {
   $plugin = json_decode(file_get_contents(OBOT_DIR.'data/Plugins.json'), true);
   for($i=0; $i<count($plugin); $i++) {
    if ($plugin[$i]['status'] == 'enable') {
     $enable++;
    }
    if ($plugin[$i]['status'] == 'disable') {
     $disable++;
    }
   }
   $plugins = "Enable[{$enable}] Disable[{$disable}]";
  }else{
   $plugins = 'No Plugin';
  }

  if($arrOn['data']['online'] === true) {
   $online = 'Online';
  }else{
   $online = 'Offline';
  }
  
  $msg = "[Status]
Status: {$online}
Plugin: {$plugins}";

  CQ::sendMsg($data, $msg);
 }
}