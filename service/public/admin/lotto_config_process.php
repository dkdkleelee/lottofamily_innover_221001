<?
$sub_menu = "500100";
include_once("./_common.php");

auth_check($auth[$sub_menu], "w");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;

//$param = getParameters(array('ar_no','proc')); 

switch($_POST['proc']) {

	case "updateServiceConfig" :

		$lottoServiceConfig = new LottoServiceConfig();
		$lottoServiceConfig->updateConfig();


		Utils::goUrl($_POST['return_url'],"수정하였습니다");
		break;

	case 'updateServiceExtractorConfig' :

		$lottoServiceConfig = new LottoServiceConfig();
		$lottoServiceConfig->updateExtractorConfig();


		Utils::goUrl($_POST['return_url'],"수정하였습니다");
		break;
}
