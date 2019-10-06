<?php
/**
 * OBot Command Line Interface
 * 2019/09/23
 */

//引入必要的文件
require_once OBOT_DIR.'core/framework/GlobalFunctions.php'; //Global Functions
require_once OBOT_DIR.'core/framework/Console.php'; //Console

//检查 data 文件夹是否存在
if (!is_dir(OBOT_DIR.'data')) mkdir(OBOT_DIR.'data', 0777, true);

//设置 data 文件夹权限
chmod(OBOT_DIR.'data/', 0777);

OBot_Console::diy("
{green}   ____  ____        _   {r}  
{green}  / __ \|  _ \      | |  {r}  {gold}Author:{r} {pink}ohmyga{r}
{green} | |  | | |_) | ___ | |_ {r}  {gold}PHP Ver:{r} {pink}".PHP_VERSION."{r}
{green} | |  | |  _ < / _ \| __|{r}  {gold}Version:{r} {pink}".OBOT_VERSION."{r}
{green} | |__| | |_) | (_) | |_ {r}  {gold}Build Date:{r} {pink}".OBOT_DATE."{r}
{green}  \____/|____/ \___/ \__|{r}  {gold}GitHub:{r} {pink}https://github.com/ohmyga233/OBot{r}

", null, false);

//判断操作系统
if (PHP_OS == 'WINNT' || PHP_OS == 'WIN32' || PHP_OS == 'Windows') { OBot_Console::warning('当前操作系统为 Windows，可能会有部分功能无法正常使用，建议使用 Linux'); }

//判断 PHP 版本
if (substr(PHP_VERSION,0,3) < '7.0') { OBot_Console::error('PHP版本必须 >= 7.0'); }

//判断拓展是否安装
//非必选拓展提示如觉得很烦 请自行注解即可
if (!function_exists("curl_exec")) { OBot_Console::error('未找到 {pink}CURL{r} 拓展，请先安装'); }
if (!extension_loaded("swoole")) { OBot_Console::error('未找到 {pink}Swoole{r} 拓展，请先安装'); }
if (!function_exists("mb_substr")) { OBot_Console::error('未找到 {pink}Mbstring{r} 拓展，请先安装'); }
if (!extension_loaded("imagick")) { OBot_Console::warning('未找到 {pink}Imagick{r} 拓展，建议安装({green}非必选{r})'); }
if (!class_exists("ZipArchive")) { OBot_Console::warning('未找到 {pink}ZipArchive{r} 拓展，建议安装({green}非必选{r})'); }

//重要条件不满足则停止运行
if (substr(PHP_VERSION,0,3) < '7.0' || !function_exists("curl_exec") || !extension_loaded("swoole") || !function_exists("mb_substr")) die();

//Core Start
require_once OBOT_DIR.'core/start.php';

//Setup
require_once OBOT_DIR.'core/Cli/setup.php';
$setup = new OBot_Setup();
$setup->start();

//开启 WS 服务端
$websocket = new OBot_Server();
$websocket->start();