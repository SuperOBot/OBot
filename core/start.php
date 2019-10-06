<?php
/**
 * OBot Core Start
 * 2019/09/23
 */

//引入 Config
require_once OBOT_DIR.'core/config.php';

//引入错误处理板块
require_once OBOT_DIR.'core/framework/Error.php';

//引入 MessageLogs 文件
require_once OBOT_DIR.'core/framework/MessageLogs.php';

//引入 libs 文件
requireFile(OBOT_DIR.'core/libs/', 'php');

//Cache
require_once OBOT_DIR.'core/framework/Cache.php';

//CoolQ
requireFile(OBOT_DIR.'core/framework/CoolQ/', 'php');

//Plugin
requireFile(OBOT_DIR.'core/framework/Plugin/', 'php');

//OBot
require_once OBOT_DIR.'core/framework/OBot.php';

//引入 WS
require_once OBOT_DIR.'core/framework/Websocket.php';