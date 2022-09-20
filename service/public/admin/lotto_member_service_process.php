<?
$sub_menu = "500100";
include_once("./_common.php");

auth_check($auth[$sub_menu], "w");

use Acesoft\Common\Utils;
use Acesoft\Common\Message;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\Member\User;

//$param = getParameters(array('ar_no','proc')); 

switch($_POST['proc']) {

	case "updateConfirmStatus" :


		$message = new Message();
		$message->setConfirmStatus($_POST['status'], $_POST['chk']);

		Utils::goUrl($_POST['return_url'],"변경완료");
		break;


}
