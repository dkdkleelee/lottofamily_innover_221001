<?
$sub_menu = "500100";
include_once("./_common.php");

auth_check($auth[$sub_menu], "w");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\Member\User;

//$param = getParameters(array('ar_no','proc')); 

switch($_POST['proc']) {

	case "extractNumbers" :

		$lottoService = new LottoService();


		// 설정
		$config = Array(
			'lc_cur_ining' => $_POST['lc_cur_ining'],
			'lc_include_numbers' => explode(',', $_POST['lc_include_numbers']),
			'lc_include_rate' => $_POST['lc_include_rate'],
			'lc_exclude_numbers' => explode(',', $_POST['lc_exclude_numbers']),
			'lc_exclude_rate' => $_POST['lc_exclude_rate'],
			'lc_odd_rate' => $_POST['lc_odd_rate'],
			'lc_even_rate' => $_POST['lc_even_rate'],
			'lc_uoddEven_use' => $_POST['lc_uoddEven_use'],
			'lc_permit_continue_num' => $_POST['lc_permit_continue_num'],
			'lc_exclude_win_num' => $_POST['lc_exclude_win_num'],
			'lc_permit_ac_num' => $_POST['lc_permit_ac_num'],
			'lc_ac_num_use' => $_POST['lc_ac_num_use'],
			'lc_except_ining' => $_POST['lc_except_ining']
		);


		$lottoService->extractNumbers($config, '', $_POST['extract_number_count']);


		Utils::goUrl("./lotto_extract_numbers.php?","추출완료");
		break;

	case 'updateExtractDate':
		$user = new User();
		$user->updateExtractDate($_POST['mb_id'], $_POST['mb_extract_weekday']);

		Utils::goUrl('',"수정완료");
		break;

	case 'addNumber':
		$lottoService = new LottoService();

		$numbers = explode(',', $_POST['selected_numbers']);

		$lottoService->addNumber($numbers, $_POST['mb_id']);
		
		Utils::goUrl($_POST['return_url'],"발급완료");
		break;

	case 'addSelectedNumbersToQueue':
		$lottoService = new LottoService();
		$lottoService->addSelectedNumbersToQueue($_POST['mb_id'], $_POST['chk']);
		
		Utils::goUrl($_POST['return_url'],	"발송대기목록에 추가하였습니다.");
		break;


	case 'updateNumbers':

		$lottoService = new LottoService();
		$lottoService->updateNumbers($_POST['le_no'], $_POST['numbers']);


		Utils::goUrl($_POST['return_url'],	"수정완료");
		break;

	case 'deleteNumbers':
		$lottoService = new LottoService();
		$lottoService->deleteNumbers($_POST['le_no']);

		Utils::goUrl($_POST['return_url'],	"삭제완료");
		break;
}
