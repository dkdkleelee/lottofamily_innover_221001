<?php
include_once("./_common.php");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\Member\Auth;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Lotto;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\TermService;
use Acesoft\Common\Message;
use Acesoft\LottoApp\PG;


$lottoService = new LottoService();




switch($_POST['proc']) {

	case 'agreeProvision':
		$termService = new TermService();
		$termService->db->where("sb_agree_provision", 0);
		$termService->db->where("md5(CONCAT(mb_id,'-',sb_no))", $_POST['sn']);
		$rows = $termService->db->arraybuilder()->get($termService->tb['TermServiceBuy'],null, '*');

		if(count($rows) == '1') {
			$termService->db->where("md5(CONCAT(mb_id,'-',sb_no))", $_POST['sn']);
			$termService->db->update($termService->tb['TermServiceBuy'], array('sb_agree_provision' => '1'));

			Utils::goUrl("/?", "약관동의 완료");
		} else {
			Utils::goUrl("", "잘못된 접근 입니다.");
		}
		
		

		break;

	case 'checkAgreeProvision':
		$termService = new TermService();
		$termService->db->where("sb_no", $_POST['sb_no']);
		$row = $termService->db->arraybuilder()->getOne($termService->tb['TermServiceBuy']);

		echo $row['sb_agree_provision'];

		break;

	case 'getServicePrice':
		$termService = new TermService();
		$row = $termService->getService($_POST['sc_no']);

		//▶ 등급별 발급설정갯수
		/*
		$lottoServiceConfig = new LottoServiceConfig();
		$serviceConfig = $lottoServiceConfig->getConfig();

		$extract_count_per_grade = unserialize($serviceConfig['lc_extract_count']);

		$data['price'] = $row['sc_price'];
		$data['extract_per_week'] = $extract_count_per_grade[$row['sg_no']];

		echo json_encode($data);
*/		
		echo $row['sc_price'];

		break;

	// 계좌SMS전송
	case "sendAccountSms" :

		Auth::checkLogin(3);

		include_once(G5_SMS5_PATH.'/sms5.lib.php');

		$SMS = new SMS5;

		$termService = new TermService();
		$row = $termService->getServiceBuy($_POST['sb_no']);

		//관리자에게 발송
		$mb = get_member($row['mb_id']);
		$list[] = str_replace("-", "", $mb['mb_hp']);
		$sn = md5($row['mb_id']."-".$row['sb_no']);

		//$reply = str_replace("-","", $sms5['cf_phone']);

		$wr_message = "[".$termService->site_conf['name']."] 입금금액: ".number_format($row['sb_price'])."원 / 입금계좌: ".$row['sb_bank_account']." 입니다.";

$sms_url = "https://apis.aligo.in/send/"; // 전송요청 URL
$sms['user_id'] = "okxogns"; // SMS 아이디
$sms['key'] = "knfmfn2frv6a0qk8j2d3y6va1394kvcb";//인증키
/******************** 인증정보 ********************/
/******************** 전송정보 ********************/
$_POST['msg'] = $wr_message; // 메세지 내용
$_POST['receiver'] = $mb['mb_hp']; // 수신번호
$_POST['destination'] = ''; // 수신인 %고객
$_POST['sender'] = '1688-7551'; // 발신번호
$_POST['rdate'] = ''; // 예약일자 - 20161004 : 2016-10-04일기준
$_POST['rtime'] = ''; // 예약시간 - 1930 : 오후 7시30분
$_POST['testmode_yn'] = ''; // Y 인경우 실제문자 전송X , 자동취소(환불) 처리
/******************** 전송정보 ********************/

$sms['msg'] = stripslashes($_POST['msg']);
$sms['receiver'] = $_POST['receiver'];
$sms['destination'] = $_POST['destination'];
$sms['sender'] = $_POST['sender'];
$sms['rdate'] = $_POST['rdate'];
$sms['rtime'] = $_POST['rtime'];
$sms['testmode_yn'] = empty($_POST['testmode_yn']) ? '' : $_POST['testmode_yn'];
// 이미지 전송시
if(!empty($_POST['image'])) {
        if(file_exists($_POST['image'])) {
                $tmpFile = explode('/',$_POST['image']);
                $str_filename = $tmpFile[sizeof($tmpFile)-1];
                $tmp_filetype = 'image/jpeg';
                $sms['image'] = '@'.$_POST['image'].';filename='.$str_filename. ';type='.$tmp_filetype;
        }
}
/*****/

$host_info = explode("/", $sms_url);
$port = $host_info[0] == 'https:' ? 443 : 80;

$oCurl = curl_init();

curl_setopt($oCurl, CURLOPT_PORT, $port);
curl_setopt($oCurl, CURLOPT_URL, $sms_url);
curl_setopt($oCurl, CURLOPT_POST, 1);
curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($oCurl, CURLOPT_POSTFIELDS, $sms);
curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
$ret = curl_exec($oCurl);
curl_close($oCurl);

$row = sql_fetch("select max(wr_no) as wr_no from {$g5['sms5_write_table']}");

if ($row)
        $wr_no = $row['wr_no'] + 1;
else
        $wr_no = 1;

        $termService = new TermService();

        $termService->SaveSendSMS($wr_no, $_POST['sender'], $_POST['content'], $wr_booking, 1, 1);

        $termService->OKSendSMS($wr_no, $_POST['id'], $_POST['name'], $_POST['hp'], "문자를 발송하였습니다.");

/*
		// 약관 SMS전송
		$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], 1);
		$result = $SMS->Add($list, $reply, '', '', '', $wr_message, '', count($list));

		if ($result) {
			$result = $SMS->Send();
		} else {
			echo 'falil';
		}
*/



		break;

	// 번호 수동발급
	case 'addNumbersToQueue':

		Auth::checkLogin(3);

		$lottoService = new LottoService();
		$lottoService->addNumbersToQueue($_POST['mb_id'], $_POST['issue_count']);

		Utils::goUrl('', '발급되었습니다.');

		break;

	case 'naveStatus':

		$_SESSION['ss_nav_status'] = $_POST['status'];

		break;


	case 'agencyPayUni':

		$pg = new PG();
		$pg->pay($_POST['pg'], $_POST['sb_no']);

		break;


	case 'agencyPayCancelUni':
		$pg = new PG();
		$pg->cancel($_POST['pg'], $_POST['sb_no']);

		break;

	// 결제확인
	case 'agencyPay':
		$lottoService = new LottoService();
		$termService = new TermService();

		$row_sb = $termService->getServiceBuy($_POST['sb_no']);

		$pg_info = $lottoService->getPGInfo();
		$pg_url = $pg_info['pay_url'];
		$g_code = $pg_info['id'];

		if(!$g_code) {
			alert_close('PG정보를 확인해주세요.');
			exit;
		}

		$redirect_url = "http://".$_SERVER[HTTP_HOST]."/service/public/common/pay_result_process.php?sb_no=".$row_sb['sb_no']."&mb_id=".$_POST['mb_id']."&return_url=".$_POST['return_url']."&";

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

		break;

}
