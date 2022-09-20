<?
define('G5_IS_ADMIN', true);
$sub_menu = "400100";
include_once("./_common.php");

auth_check($auth[$sub_menu], "w");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\TermService;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Member\User;

$termService = new TermService();

switch($_POST['proc']) {

	case "addServiceBuy" :
		$user = new User();
		$member_row = $user->getUser($_POST['mb_id']);

		$_POST['ordername'] = $member_row['mb_name'];
		$_POST['phoneno'] = $member_row['mb_hp'];

		$sb_no = $termService->addServiceBuy();

		$url = $_POST['return_url'] ? $_POST['return_url'] : './term_service_buy_list.php?'.$param;
		Utils::goUrl($url, "등록되었습니다.");

		break;


	case "changeBuyStatus" :
/*
		$lottoServiceConfig = new LottoServiceConfig();
		$config = $lottoServiceConfig->getConfig();
		$service = $termService->getServiceBuy($_POST['sb_no']);
		if($_POST['sb_pay_status'] == 'Y') {
			$user = new User();
			$mb = $user->getUser($service['mb_id']);

			if(!$mb['mb_extract_weekday']) {
				$user->updateExtractDate($service['mb_id'], $config['lc_send_weekdays']);
			}
		}
*/
	    $termService->changeBuyStatus($_POST['sb_no'], $_POST['sb_pay_status'], $_POST['sb_pay_method']);

		$url = $_POST['return_url'] ? $_POST['return_url'] : './term_service_buy_list.php?'.$param;
		Utils::goUrl($url);
		break;

	case "deleteServiceBuy" :
		
        $termService->deleteServiceBuy($_POST['no']);
        
		$url = $_POST['return_url'] ? $_POST['return_url'] : './term_service_buy_list.php?'.$param;
		Utils::goUrl($url);
		break;

	// 약관전송
	case "sendProvisionSms" :
/*
		include_once(G5_SMS5_PATH.'/sms5.lib.php');

		$SMS = new SMS5;
*/
		$row = $termService->getServiceBuy($_POST['sb_no']);

		//관리자에게 발송
		$mb = get_member($row['mb_id']);
		$list[] = str_replace("-", "", $mb['mb_hp']);
		$sn = md5($row['mb_id']."-".$row['sb_no']);

		//$reply = str_replace("-","", $sms5['cf_phone']);

		$wr_message = "[".$termService->site_conf['name']."약관] http://".$_SERVER['HTTP_HOST']."/service/public/common/provision.php?sn=".$sn;


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

	// 발급갯수 조정
	case 'modifyExtractNum' :

		$termService->modifyExtractNum($_POST['su_no'], $_POST['num']);

		break;

}

switch($_GET['proc']) {
	case 'downloadServiceBuyExcel' :

		$termService->downloadServiceBuyExcel();

		break;

}
