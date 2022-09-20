<?php
include_once('./_common.php');
include_once(G5_LIB_PATH.'/register.lib.php');
include_once(G5_SMS5_PATH.'/sms5.lib.php');

use Acesoft\LottoApp\LottoServiceConfig;

switch($_POST['proc']) {
	case 'getCode' :
		$mb_hp   = trim(str_replace("-","",$_POST['hp']));

		if ($msg = valid_mb_hp($mb_hp)) {
			die($msg);
		}

		// 중복체크
		$row = sql_fetch(" select count(*) as cnt from `{$g5['member_table']}` where replace(mb_hp, '-', '')  = '$mb_hp' ");

		if ($row['cnt']) {
			echo  "이미 사용중인 휴대폰번호입니다.";
			exit;
		}


		$allowedNumbers = range(0, 9);
		$digits = array_rand($allowedNumbers, 4);
		$number = '';
		foreach($digits as $d){
			$number .= $allowedNumbers[$d];
		}
		$_SESSION['auth_hp'] = $_POST['hp'];
		$_SESSION['auth_code'] = $number;

		//관리자에게 발송
		$mb = get_member('admin');
		$list[] = str_replace("-", "", $mb_hp);
		
		$wr_message = "[로또패밀리 인증번호] ".$number;


$sms_url = "https://apis.aligo.in/send/"; // 전송요청 URL
$sms['user_id'] = "okxogns"; // SMS 아이디
$sms['key'] = "knfmfn2frv6a0qk8j2d3y6va1394kvcb";//인증키
$_POST['msg'] = $wr_message; // 메세지 내용
$_POST['receiver'] = $_POST['hp']; // 수신번호
$_POST['destination'] = ''; // 수신인 %고객
$_POST['sender'] = '1688-7551'; // 발신번호
$_POST['rdate'] = ''; // 예약일자 - 20161004 : 2016-10-04일기준
$_POST['rtime'] = ''; // 예약시간 - 1930 : 오후 7시30분
$_POST['testmode_yn'] = ''; // Y 인경우 실제문자 전송X , 자동취소(환불) 처리

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
		// SMS전송
		$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], 1); //$config['cf_icode_server_port']
		//$result = $SMS->Add2($list, $reply, '', '', $wr_message, $booking, count($list));
		$result = $SMS->Add($list, $reply, '', '', '', $wr_message, '', count($list));

		if ($result) {
			$result = $SMS->Send();
		}
*/

		echo "ok";

		break;

	case 'checkCode' :


		//▶ 설정정보 인출
		$lottoServiceConfig = new LottoServiceConfig();
		$data = $lottoServiceConfig->getConfig();

		$send_weekday = $data['lc_send_weekdays'] ? $data['lc_send_weekdays'] : 2;

		if($_POST['code'] == $_SESSION['auth_code']) {

			echo "ok";

		}  else {
			echo "코드가 일치하지 않습니다.\다시 확인해 주세요.";
		}


		break;


/*	인증시 회원가입용 
	case 'checkCode' :


		//▶ 설정정보 인출
		$lottoServiceConfig = new LottoServiceConfig();
		$data = $lottoServiceConfig->getConfig();

		$send_weekday = $data['lc_send_weekdays'] ? $data['lc_send_weekdays'] : 2;

		if($_POST['code'] == $_SESSION['auth_code']) {

			$tmp = explode("-", $_SESSION['auth_hp']);

			$mb_id = str_replace("-", "", $_SESSION['auth_hp']);
			$mb_password = $tmp[2];
			$mb_name = $mb_id;
			$mb_hp = $mb_id;
			$mb_nick = $tmp[0].$tmp[1]."****";

			$sql = " insert into {$g5['member_table']}
                set mb_id = '{$mb_id}',
                     mb_password = '".get_encrypt_string($mb_password)."',
                     mb_name = '{$mb_name}',
                     mb_nick = '{$mb_nick}',
                     mb_nick_date = '".G5_TIME_YMD."',
                     mb_today_login = '".G5_TIME_YMDHIS."',
					 mb_hp = '".$mb_hp."',
                     mb_datetime = '".G5_TIME_YMDHIS."',
                     mb_ip = '{$_SERVER['REMOTE_ADDR']}',
                     mb_level = '{$config['cf_register_level']}',
                     mb_recommend = '{$mb_recommend}',
                     mb_login_ip = '{$_SERVER['REMOTE_ADDR']}',
                     mb_mailling = '{$mb_mailling}',
                     mb_sms = '1',
                     mb_extract_weekday = '".$send_weekday."',
                     mb_open_date = '".G5_TIME_YMD."'";

			sql_query($sql);

			set_session('ss_mb_id', $mb_id);
			set_session('ss_mb_reg', $mb_id);

			echo "ok";

		}  else {
			echo "코드가 일치하지 않습니다.\다시 확인해 주세요.";
		}


		break;
*/
}
?>
