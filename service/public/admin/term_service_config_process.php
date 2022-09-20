<?
define('G5_IS_ADMIN', true);
$sub_menu = "400100";
include_once("./_common.php");

auth_check($auth[$sub_menu], "w");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\TermService;

$termService = new TermService();

switch($_POST['proc']) {

	case "addTermServiceConfig" :
	
	    
	    $termService->addTermServiceConfig();
	    
		Utils::goUrl('./term_service_config.php?');
		break;

	case "modifyTermServiceConfig" :

        $termService->modifyTermServiceConfig();
        
		Utils::goUrl('./term_service_config.php?');
		break;
		
    case "deleteTermServiceConfig" :

        $termService->deleteTermServiceConfig($_POST['no']);
        
		Utils::goUrl('./term_service_config.php?'.$param);
		break;
		
    case "updateTermServiceConfig" :

        $termService->modifyTermServiceConfig();
        
		Utils::goUrl('./term_service_config.php?');
		break;

	case "updateDiscountConfig" :

		$termService->updateDiscountConfig();
		Utils::goUrl('./term_service_config.php?');
		break;

    case "getGugun" :

        $gugun = $termService->getServiceGugun($_POST['sido']);
        
        $json = new JSON();
        $data = $json->encode($gugun);
        
        echo $data;
        
    case "getGugunArr" :

        $gugun = $termService->getGugun($_POST['sido']);
        
        $json = new JSON();
        $data = $json->encode($gugun);
        
        echo $data;



}