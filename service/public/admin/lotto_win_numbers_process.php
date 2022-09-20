<?
$sub_menu = "500100";
include_once("./_common.php");

auth_check($auth[$sub_menu], "w");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\LottoService;

//$param = getParameters(array('ar_no','proc')); 

switch($_POST['proc']) {

	case "getNextWinNumbers" :

		$lottoService = new LottoService();
		$id = $lottoService->getNextWinNumbers($config);


		Utils::goUrl("./lotto_win_numbers.php?");
		break;

	case "modifyWinNumbers" :

		$lottoService = new LottoService();
		$lottoService->modifyWinNumbers($_POST['numbers'], $_POST['lw_no']);

		Utils::goUrl("./lotto_win_numbers.php?","수정완료");
		break;

	case "reCheckWinNumbers":
		$lottoService = new LottoService();
		$result = $lottoService->reCheckWinNumbers($_POST['inning'], $_POST['lw_no']);

		if($result) {
			Utils::goUrl("./lotto_win_numbers.php?","갱신완료");
		} else {
			Utils::goUrl("./lotto_win_numbers.php?","갱신실패");
		}
		break;
}

switch($_GET['proc']) {
	case 'downloadWinnerExcel': 
		$lottoService = new LottoService();
		$lottoService->downloadWinnerExcel();

		break;
}
