<?
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 
// 자신만의 코드를 넣어주세요.
include_once(G5_SMS5_PATH.'/sms5.lib.php');

 $wr_4 = str_replace("-","", $wr_4);
 if ($w == "" || $w == "r") {

	$SMS = new SMS5;

	
	$reply = str_replace("-","", $sms5['cf_phone']);

	//관리자에게 발송
	$mb = get_member('admin');
	$list[] = str_replace("-", "", $mb['mb_hp']);
	
	$wr_message = "[상담신청]$wr_name,$wr_4,$wr_content";
/*
	// SMS전송
	//$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
	$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], 1); //$config['cf_icode_server_port']
	//$result = $SMS->Add2($list, $reply, '', '', $wr_message, $booking, count($list));
	$result = $SMS->Add($list, $reply, '', '', '', $wr_message, '', count($list));

	if ($result) {

        $result = $SMS->Send();
	}
*/


/**************** 문자전송하기 예제 ******************/
/* "result_code":결과코드,"message":결과문구, */
/* "msg_id":메세지ID,"error_cnt":에러갯수,"success_cnt":성공갯수 */
/******************** 인증정보 ********************/
$sms_url = "https://apis.aligo.in/send/"; // 전송요청 URL
$sms['user_id'] = "okxogns"; // SMS 아이디
$sms['key'] = "knfmfn2frv6a0qk8j2d3y6va1394kvcb";//인증키
/******************** 인증정보 ********************/

/******************** 전송정보 ********************/
$_POST['msg'] = $wr_message; // 메세지 내용
$_POST['receiver'] = $_POST['phone1']."-".$_POST['phone2']."-".$_POST['phone3']; // 수신번호
$_POST['destination'] = $_POST['phone1']."-".$_POST['phone2']."-".$_POST['phone3']." admin 에 접수"; // 수신인 %고객
$_POST['sender'] = '1688-7551'; // 발신번호
$_POST['rdate'] = ''; // 예약일자 - 20161004 : 2016-10-04일기준
$_POST['rtime'] = ''; // 예약시간 - 1930 : 오후 7시30분
$_POST['testmode_yn'] = ''; // Y 인경우 실제문자 전송X , 자동취소(환불) 처리
// $_POST['image'] = '/tmp/pic_57f358af08cf7_sms_.jpg'; // MMS 이미지 파일 위치
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

/*
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
*/





	// 회원데이터 등록
	$phone = $_POST['phone1']."-".$_POST['phone2']."-".$_POST['phone3'];
	$mb_id = str_replace("-", "", $phone);
	$mb_hp = $phone;
	$mb_password = substr($mb_id, -4);
//			$mb_name = iconv('euc-kr','utf-8', $_REQUEST['name']);
	$mb_name = $phone;
	
	$mb_nick = substr($mb_id, 0, 8)."****";

	

	$row = sql_fetch(" select * from g5_member where mb_id = '{$mb_id}' ");
	if(!$row['mb_id']) {

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
				 mb_channel = '{$_POST['wr_8']}',
				 mb_channel_referer = '{$mb_channel_referer}',
				 mb_media = Null,
				 mb_sms = '0',
				 mb_extract_weekday = '".$send_weekday."',
				 mb_update_date = '".G5_TIME_YMD."',
				 mb_open_date = '".G5_TIME_YMD."'";

		sql_query($sql);

	}
}
?>


<?

if(($w == "" || $w == "r") || !is_admin($_SESSION['ss_mb_id'])) {

	alert("접수가 완료되었습니다. 신속히 연락 드리겠습니다. 감사합니다.");
	exit;
} else {
	alert("접수내역을 수정하였습니다.");
}
?>
