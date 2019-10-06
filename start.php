<?php
/**
 * OBot Start
 * @author ohmyga
 * @version 0.1.0
 * @link https://github.com/ohmyga233
 * 2019/09/23
 */

//常量
define('OBOT_DIR', __DIR__ .'/');
define('OBOT_VERSION', '0.1.0');
define('OBOT_DATE', '2019-09-23');
define('OBOT_TYPE', 'Cli');

//设置时区
date_default_timezone_set("Asia/Shanghai");

//Cli Core
require_once OBOT_DIR.'core/Cli/cli.php';