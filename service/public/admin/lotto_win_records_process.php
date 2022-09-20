<?
$sub_menu = "500100";
include_once("./_common.php");

auth_check($auth[$sub_menu], "w");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\LottoWinRecords;

//$param = getParameters(array('ar_no','proc')); 

switch($_POST['proc']) {

	case "addWinRecord" :

		$lottoWinRecords = new LottoWinRecords();
		$lottoWinRecords->addWinRecord();

		Utils::goUrl("./lotto_win_records.php?");
		break;

	case "modifyWinRecord" :

		$lottoWinRecords = new LottoWinRecords();
		$lottoWinRecords->modifyWinRecord();

		Utils::goUrl("./lotto_win_records.php?","수정완료");
		break;

	case "deleteWinRecord":
		$lottoWinRecords = new LottoWinRecords();
		$result = $lottoWinRecords->deleteWinRecord($_POST['wr_inning']);

		if($result) {
			Utils::goUrl("./lotto_win_records.php?","삭제완료");
		} else {
			Utils::goUrl("./lotto_win_records.php?","삭제실패");
		}
		break;
}
