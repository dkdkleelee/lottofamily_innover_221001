<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

include_once dirname(__FILE__)."/../service/vendor/autoload.php";

use \Acesoft\LottoApp\Cron;



define('_GNUBOARD_', 'true');

define('G5_LIB_PATH', dirname(__FILE__).'/../lib');
define('G5_PLUGIN_PATH', dirname(__FILE__).'/../plugin');
define('G5_SMS5_DIR',             'sms5');
define('G5_SMS5_PATH',            G5_PLUGIN_PATH.'/'.G5_SMS5_DIR);

include_once dirname(__FILE__)."/../common.php";
include_once(G5_SMS5_PATH.'/sms5.lib.php');



$cron = new Cron();

echo date('Y-m-d H:i:s')." - Cron task..\n";

//error_log("[".date('Y-m-d H:i:s')."] Auction cron task..", 0);