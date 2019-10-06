<?php
/**
 * Public Start
 * 2019/10/04
 */

define('OBOT_DIR', __DIR__ .'/');
define('OBOT_TITLE', 'OBot');
define('OBOT_TYPE', 'Web');

//设置时区
date_default_timezone_set("Asia/Shanghai");

if (!is_dir(OBOT_DIR.'data')) die('<h2>先在命令行运行 ['.OBOT_DIR.'start.php] 再访问 Public 啊.jpg');

//判断拓展是否安装
if (substr(PHP_VERSION,0,3) < '7.0') { echo 'PHP版本必须 >= 7.0'; }
if (!function_exists("curl_exec")) { echo '未找到 {pink}CURL{r} 拓展，请先安装'; }
if (!extension_loaded("swoole")) { echo '未找到 {pink}Swoole{r} 拓展，请先安装'; }
if (!function_exists("mb_substr")) { echo '未找到 {pink}Mbstring{r} 拓展，请先安装'; }

//重要条件不满足则停止运行
if (substr(PHP_VERSION,0,3) < '7.0' || !function_exists("curl_exec") || !extension_loaded("swoole") || !function_exists("mb_substr")) die();

//引入错误处理板块
require_once OBOT_DIR.'core/framework/Error.php';

//Global Functions
require_once OBOT_DIR.'core/framework/GlobalFunctions.php'; 

//引入 Config
require_once OBOT_DIR.'core/config.php';

//引入 libs 文件
requireFile(OBOT_DIR.'core/libs/', 'php');

//CoolQ
requireFile(OBOT_DIR.'core/framework/CoolQ/', 'php');

//Plugin
requireFile(OBOT_DIR.'core/framework/Plugin/', 'php');

//OBot
require_once OBOT_DIR.'core/framework/OBot.php';

//引入 Public 板块
requireFile(OBOT_DIR.'core/framework/Public/', 'php');

$public = new OBot_Public();
$public->start();