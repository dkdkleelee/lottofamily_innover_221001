<?
include_once("./_common.php");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\TermService;
use Acesoft\LottoApp\PG;

// term service
$termService = new TermService();


switch($_POST['proc']) {

	case "addServiceBuy" :

		$_SESSION['sb_no'] = '';

		$_POST['tm_id'] = $member['mb_tm_id'];

		$sb_no = $termService->addServiceBuy();

		$data['sc_no'] = $_POST['sc_no'];
		$data['sb_no'] = $sb_no;
		$data['is_login'] = $_SESSION['ss_mb_id'] ? 'ok' : '';

		if($_POST['from_ajax'] != 'true') {
			if($data['is_login']) {
				if($_POST['type'] == 'manual') {

					$row_sb = $termService->getServiceBuy($sb_no);

					$pg = new PG();
					$pg->pay("SHCard2", $sb_no);
/*
					$pg_info = $termService->getPGInfo();
					$pg_url = $pg_info['pay_url'];
					$g_code = $pg_info['id'];

					$redirect_url = "http://".$_SERVER[HTTP_HOST]."/service/public/common/pay_result_process.php?sb_no=".$sb_no."&return_url=close&";

					if(!$g_code) {
						alert_close('PG정보를 확인해주세요.');
						exit;
					}

					$param = array(
							'g_code'   => $g_code,
							'amt'   => $row_sb['sb_price'],
							'cardNo' => $row_sb['sb_ay_cardno'],
							'c_month' => $row_sb['sb_ay_expmon'],
							'c_year' => substr($row_sb['sb_ay_expyear'],2),
							'h_month' => sprintf('%02d', $row_sb['sb_ay_installment']),
							'g_tel' => $row_sb['sb_buyer_hp'],
							'c_name' => $row_sb['sb_buyer_name'],
							'c_ma' => $g_code,
							'redirect_url' => $redirect_url
						);

				?>
					<html>
					<head></head>
					<body onLoad="document.payForm.submit()">
					<form name="payForm" method="post" action="<?=$pg_url?>">
				<?
					foreach($param as $key => $value) {
				?>
						<input type="hidden" name="<?=$key?>" value="<?=$value?>">
				<?
					}
				?>
					</form>
					</body>
					</html>
				<?
*/

				} else {
					Utils::message("신청이 완료되었습니다.","close");
				}
			} else {
				$_SESSION['sb_no'] = $data['sb_no'];
				Utils::goUrl("./service_buy_step2.php");
			}
		} else {
			$_SESSION['sb_no'] = $data['sb_no'];
			echo json_encode($data);
		}

		break;

	case "servicePay" :
	
		if( $_POST['pay_method'] != 'agencypay') {
    	    $termService->updateServiceBuy($_POST['sb_no']);

			Utils::goUrl("/", "서비스신청이 완료되었습니다.\\n입금확인 후 서비스 이용이 가능합니다.");
	    } else  if($_POST['pay_method'] != 'account') {
    	    if($_POST['pay_result'] == 'y') {
				if($_POST['pay_method'] != 'virtual') {
					$termService->changeBuyStatus($_POST['sb_no'], 'Y', $_POST['pay_method']);
					Utils::goUrl("/", "서비스신청 및 결제가 완료되었습니다.");
				} else {
					$termService->changeBuyStatus($_POST['sb_no'], 'N', $_POST['pay_method']);
					Utils::goUrl("/", "서비스신청이 완료되었습니다.\\n입금확인 후 서비스 이용이 가능합니다.");
				}
	        } else {
    	        $termService->changeBuyStatus($_POST['sb_no'], 'F', $_POST['pay_method']);
	        }
	    } else {
    	    $termService->changeBuyStatus($_POST['sb_no'], 'N', $_POST['pay_method'], $_POST['payer_name']);

			Utils::goUrl("/", "서비스신청이 완료되었습니다.\\n입금확인 후 서비스 이용이 가능합니다.");
	    } 
	
		break;

	case "joinUser" :

		$termService->joinServiceUser();

		Utils::message("신청이 완료되었습니다.","close");

		break;
}


?>
