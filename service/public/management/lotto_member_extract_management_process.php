<?
$sub_menu = "500100";
include_once("./_common.php");

//auth_check($auth[$sub_menu], "w");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Member\Auth;
use Acesoft\LottoApp\Lotto;
use Acesoft\LottoApp\LottoService;

//$param = getParameters(array('ar_no','proc')); 

switch($_POST['proc']) {

	case 'addNumber':
		$lottoService = new LottoService();

		$numbers = explode(',', $_POST['selected_numbers']);

		$lottoService->addNumber($numbers, $_POST['mb_id']);
		
		Utils::goUrl($_POST['return_url'],"발급완료");
		break;

	// 번호 수동발급
	case 'addNumbersToQueue':

		Auth::checkLogin(3);

		$lottoService = new LottoService();
		$lottoService->addNumbersToQueue($_POST['mb_id'], $_POST['issue_count'], $_POST['send_msg']);

		Utils::goUrl($_POST['return_url'], '발급되었습니다.');

		break;

	case 'addSelectedNumbersToQueue':
		$lottoService = new LottoService();
		$lottoService->addSelectedNumbersToQueue($_POST['mb_id'], $_POST['chk']);
		
		Utils::goUrl($_POST['return_url'],	"발송대기목록에 추가하였습니다.");
		break;

	case 'deleteNumbers':
		$lottoService = new LottoService();
		$lottoService->deleteNumbers($_POST['le_no']);

		Utils::goUrl($_POST['return_url'],	"삭제완료");
		break;


}
