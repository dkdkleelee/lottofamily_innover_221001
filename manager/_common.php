<?php
define('G5_IS_MANAGER', true);
include_once ('../common.php');
include_once(G5_MANAGER_PATH.'/admin.lib.php');

require dirname(__FILE__)."/../service/vendor/autoload.php";

if( isset($token) ){
    $token = @htmlspecialchars(strip_tags($token), ENT_QUOTES);
}
?>