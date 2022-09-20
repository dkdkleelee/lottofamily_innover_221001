<?
define('G5_IS_ADMIN', true);
$sub_menu = "400100";
include_once("./_common.php");

auth_check($auth[$sub_menu], "w");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\TermService;

$termService = new TermService();

switch($_POST['proc']) {

	case "updateServiceDefaultConfig" :
	
	    
	    $termService->updateTermServiceDefaultConfig();
	    
		Utils::goUrl('./term_service_default_config.php?');
		break;

	


}