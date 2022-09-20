<?
define('G5_IS_ADMIN', true);
$sub_menu = "400100";
include_once("./_common.php");
include_once(G5_SMS5_PATH.'/sms5.lib.php');

auth_check($auth[$sub_menu], "w");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\TermService;

switch($_POST['proc']) {

	case "updateServiceUse" :
	
	    $termService = new TermService();
	    $termService->updateServiceUse($_POST['su_no'], $_POST['sg_no'], $_POST['su_enddate']);
	    
		Utils::goUrl('./term_service_use_list.php?'.$param);
		break;

	case "updateServiceEndDate" :

		$termService = new TermService();
	    $termService->updateServiceEndDate($_POST['su_no'], substr($_POST['su_enddate'],0,10)." 23:59:59");

		Utils::goUrl($_POST['return_url']);
		break;

	case "deleteServiceUse" :
		$termService = new TermService();
        $termService->deleteServiceUse($_POST['no']);
        
		Utils::goUrl('./term_service_use_list.php?'.$param);
		break;
    case "addServiceTerm" :
        $termService = new TermService();
     
        $termService->addServiceTerm($_POST['mb_id'], $_POST['sg_no'], $_POST['term'], $_POST['term_type']);
        
        
        Utils::goUrl('./term_service_use_list.php?'.$param2);
        break;
    
	case "sendSMS" :
/*
		$sms5 = sql_fetch("select * from ".$g5['sms5_config_table'] );

		$SMS = new SMS5;
		$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], 1); //$config['cf_icode_server_port']
		$reply = str_replace('-', '', trim($sms5['cf_phone']));
*/

		/*
		$i = 0;
		foreach($_POST['chk'] as $key => $value) {
			
			if($value == '1') {
				$list[$i]['hp'] = str_replace("-", "", $_POST['hp'][$key]);
				$list[$i]['name'] =  $_POST['name'][$key];
				$list[$i]['mb_id'] =  $_POST['mb_id'][$key];
				$dest[] =  str_replace("-", "", $_POST['hp'][$key]);

				$i++;
			}
		}
		*/


$sms_url = "https://apis.aligo.in/send/"; // 전송요청 URL
$sms['user_id'] = "okxogns"; // SMS 아이디
$sms['key'] = "knfmfn2frv6a0qk8j2d3y6va1394kvcb";//인증키
/******************** 인증정보 ********************/
/******************** 전송정보 ********************/
$_POST['msg'] = $_POST['content']; // 메세지 내용
$_POST['receiver'] = $_POST['hp']; // 수신번호
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

        $termService->OKSendSMS($wr_no, $_POST['id'], $_POST['name'], $_POST['hp'], "수기로 발송하였습니다.");

/*
		$list[0]['hp'] = str_replace('-', '', $_POST['hp']);
		$list[0]['name'] = $_POST['name'];
		$list[0]['mb_id'] = $_POST['id'];
		$dest[] = str_replace('-', '', $_POST['hp']);


		$result = $SMS->Add($dest, $reply, '', '', '', $_POST['content'], '', count($dest));
			//			Add($strDest, $strCallBack, $strCaller, $strSubject, $strURL, $strData, $strDate="", $nCount)

		if($result) { 
			$result = $SMS->Send();

			//$result = $SMS->Result;

			if($result) {
				$row = sql_fetch("select max(wr_no) as wr_no from {$g5['sms5_write_table']}");
				if ($row)
					$wr_no = $row['wr_no'] + 1;
				else
					$wr_no = 1;

				sql_query("insert into {$g5['sms5_write_table']} set wr_no='$wr_no', wr_renum=0, wr_reply='$reply', wr_message='".$_POST['content']."', wr_booking='$wr_booking', wr_total='".count($dest)."', wr_datetime='".G5_TIME_YMDHIS."'");

				$wr_success = 0;
				$wr_failure = 0;
				$count      = 0;

				foreach ($SMS->Result as $result) {
					
					list($phone, $code) = explode(":", $result);

					if (substr($code,0,5) == "Error")
					{
						$hs_code = substr($code,6,2);

						switch ($hs_code) {
							case '02':	 // "02:형식오류"
								$hs_memo = "형식이 잘못되어 전송이 실패하였습니다.";
								break;
							case '23':	 // "23:인증실패,데이터오류,전송날짜오류"
								$hs_memo = "데이터를 다시 확인해 주시기바랍니다.";
								break;
							case '97':	 // "97:잔여코인부족"
								$hs_memo = "잔여코인이 부족합니다.";
								break;
							case '98':	 // "98:사용기간만료"
								$hs_memo = "사용기간이 만료되었습니다.";
								break;
							case '99':	 // "99:인증실패"
								$hs_memo = "인증 받지 못하였습니다. 계정을 다시 확인해 주세요.";
								break;
							default:	 // "미 확인 오류"
								$hs_memo = "알 수 없는 오류로 전송이 실패하였습니다.";
								break;
						}
						$wr_failure++;
						$hs_flag = 0;
					}
					else
					{
						$hs_code = $code;
						$hs_memo = get_hp($phone, 1)."로 전송했습니다.";
						$wr_success++;
						$hs_flag = 1;
					}

					$row = array_shift($list);
					$row['bk_hp'] = get_hp($row['bk_hp'], 1);

					$log = array_shift($SMS->Log);
					$log = @iconv('euc-kr', 'utf-8', $log);


					sql_query("insert into {$g5['sms5_history_table']} set wr_no='$wr_no', wr_renum=0, bg_no='{$row['bg_no']}', mb_id='".$row['mb_id']."', bk_no='{$row['bk_no']}', hs_name='".addslashes($row['name'])."', hs_hp='{$row['hp']}', hs_datetime='".G5_TIME_YMDHIS."', hs_flag='$hs_flag', hs_code='$hs_code', hs_memo='".addslashes($hs_memo)."', hs_log='".addslashes($log)."'", false);
				}
				$SMS->Init(); // 보관하고 있던 결과값을 지웁니다.

				sql_query("update {$g5['sms5_write_table']} set wr_success='$wr_success', wr_failure='$wr_failure', wr_memo='$str_serialize' where wr_no='$wr_no' and wr_renum=0");
			}
		}
*/
		if($_POST['return_url']) {
			Utils::goUrl($_POST['return_url']);
		} else {
			Utils::goUrl('./term_service_sms_management.php?id='.$_POST['id']);
		}
        break;
}
