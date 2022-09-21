<?php
/**
 * Created by PhpStorm.
 * User: acerunner
 * Date: 16. 2. 1.
 * Time: 오전 10:49
 */

namespace Acesoft\Common;

use \Acesoft\Core\Base as Base;
use \Acesoft\Core\DB as DB;
use \Acesoft\LottoApp\Member\User;
use \Acesoft\LottoApp\Member\Mail;
use \Acesoft\LottoApp\TermService;

class Message extends Base {

    public $db;
	private $user;
	public $mail;


    /**
     * @param $table
     */
    public function __construct() {
        parent::__construct();
        $this->db = DB::getInstance();

		$this->user = new User();
		$this->mail = new Mail();
    }

	function getMessageList($page=1, $url='') {
		global $pageLimit;

        $pageLimit = $pageLimit ? $pageLimit : 20;

        $page = $page > 0 ? $page : 1;

		if($_GET['sc'] && $_GET['sv']) {
			$this->db->where($_GET['sc']." LIKE '%".$_GET['sv']."%'");
		}

		if($_GET['s_ok'] != '') {
			$this->db->where('confirm', $_GET['s_ok']);
		}

		if($_GET['s_sent'] == '1') {
			$this->db->where('a.sent_at is NULL');
		} else if($_GET['s_sent'] == '2') {
			$this->db->where('a.sent_at is not NULL');
		}

		if($_GET['s_mb_id']) {
			$this->db->where("b.mb_id", $_GET['s_mb_id']);
		}

		//▶ page rows
		$this->db->pageLimit = $pageLimit;

		//▶ order block
		$this->db->orderBy('b.created_at', 'DESC');

		//$this->db->where("yearweek(b.created_at)=yearweek(now())");
		$this->db->where("DATE_FORMAT(b.created_at,'%Y-%m-%d') >= DATE_FORMAT(DATE_SUB(now(), INTERVAL 7 DAY),'%Y-%m-%d')");

		$this->db->where("a.type", "sms");

		$this->db->join($this->tb['Msg']." as b", "a.message_id=b.id", "LEFT");
		$this->db->join($this->tb['Member']." as c", "b.mb_id=c.mb_id", "LEFT");

		$list = $this->db->arraybuilder()->paginate($this->tb['Msg_queue']." as a", $page, "a.*,a.message_id, b.message,  b.title, c.mb_id, c.mb_name, c.mb_hp");

		$link = Utils::getPagination($this->db->totalPages, $page, $url);

		$data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
		$data['list'] = $list;
		$data['link'] = $link;
		$data['total_count'] = $this->db->totalCount;
		$data['total_pages'] = $this->db->totalPages;
		$data['page'] = $page;
		
		return $data;
	}

	// type : noty, email, sms
    public function addMessage($mb_id, $title, $message='', $type='noty,email,sms', $confirm='1') {
		$data = array(
						'mb_id' => $mb_id,
						'title' => $title,
						'message' => $message,
						'created_at' => $this->db->NOW()
				);

		
		$id = $this->db->insert($this->tb['Msg'], $data);

		$type_arr = explode(",", $type);
		for($i=0; $i<count($type_arr); $i++) {
			$type_confirm = ($type == 'noty') ? '1' : $confirm;
			$data = array(
							'message_id' => $id,
							'type' => trim($type_arr[$i]),
							'confirm' => $type_confirm,
							'created_at' => $this->db->NOW()
					);

			$this->db->insert($this->tb['Msg_queue'], $data);
		}

        return $list;
    }

	public function getUnSentMessage($mb_id='', $type='', $year_week='1') {
		if($mb_id) $this->db->where('b.mb_id', $mb_id);
		if($type) $this->db->where('a.type', $type);
		$this->db->where('a.confirm', '1'); // 승인메세지만
		$this->db->where("yearweek(b.created_at)=yearweek(now())");
		$this->db->where('a.sent_at is NULL');
		$this->db->orderBy('b.created_at', 'ASC');
		$this->db->join($this->tb['Msg']." as b", "a.message_id=b.id", "LEFT");
		$data = $this->db->get($this->tb['Msg_queue']." as a", null, 'a.id, a.type, b.mb_id, b.title, b.message');

		return $data;
	}

	public function setSentMessage($id) {
		$this->db->where('id', $id);
		$this->db->update($this->tb['Msg_queue'], array('sent_at' => $this->db->NOW()));
	}

	public function setConfirmStatus($status, $ids_arr) {
		$this->db->where('sent_at is NULL');
		$this->db->where('id', $ids_arr, 'IN');
		$this->db->update($this->tb['Msg_queue'], array('confirm' => $status));
	}



	public function deleteMessage($id) {
		$this->db->where('id', $id);
		$this->db->delete($this->tb['Msg']);
	}


	public function proceedQueue($type) {
		$rows = $this->getUnSentMessage('', $type);
		$typeFunction = 'send'.ucfirst($type);
		for($i=0; $i<count($rows); $i++) {
			$this->$typeFunction($rows[$i]);
			$this->setSentMessage($rows[$i]['id']);
		}
	}

	public function getRecentMessages($mb_id, $limit=5) {
		if($mb_id) {
			$this->db->where('mb_id', $mb_id);
		} else {
			$this->db->join($this->tb['Msg_queue']." as c", "a.id=c.message_id", "LEFT");
			$this->db->join($this->tb['Member']." as b", "a.mb_id=b.mb_id", "LEFT");
		}
		$this->db->orderBy('a.id', 'DESC');
		$rows = $this->db->get($this->tb['Msg']." as a", $limit, '*');

		return $rows;
	}


	protected function sendEmail($msg) {
		//global $config;

		$config = $this->db->rawQueryOne(" select * from lotto_config ");

		$member_row = $this->user->getUser($msg['mb_id']);

		// email
		if($member_row['mb_mailling']) {
			$user_name = $member_row['mb_com_name'] ? $member_row['mb_com_name'] : $member_row['mb_name'];

			// 메일 템플릿 가져오기
			$content = Utils::getContent($this->user->config_default['cf_order_mail_form']);

			$content_tmp = preg_replace("/\{회원명\}/", $user_name, $content);
			$content_tmp = preg_replace("/{내용}/", $msg['message'], $content_tmp);
			$content_tmp = preg_replace("/{수신자}/", $user_name, $content_tmp);
			$content_tmp = preg_replace("/{발신자}/", '와우베이', $content_tmp);
			$content_tmp = preg_replace("/{발신자이메일}/", $config['cf_admin_email'], $content_tmp);
			$content_tmp = preg_replace("/{발송일}/", date('Y-m-d H:i:s'), $content_tmp);

			// 수신거부 항목
			$key = md5($member_row['mb_id'].$member_row['mb_email'].$member_row['mb_datetime']);
			$content_tmp = $content_tmp . "<hr size=0><p><span style='font-size:9pt; font-familye:굴림'>▶ 더 이상 정보 수신을 원치 않으시면 [<a href='".$this->getAppPublicUrl()."/user/email_stop.php?id=".$member_row['mb_id']."&amp;key={$key}' target='_blank'>수신거부</a>] 해 주십시오.</span></p>";

			$result =  $this->mail->sendMail($user_name, $config['cf_admin_email'], $member_row['mb_email'], $msg['title'], $content_tmp, 1, $file);
		}
	}

	protected function sendSms($msg) {
		global $g5;

		$config = $this->db->rawQueryOne(" select * from g5_config ");
		$sms5 = $this->db->rawQueryOne("select * from sms5_config ");

		$member_row = $this->user->getUser($msg['mb_id']);

		if(1 || $member_row['mb_sms']) { // 메세지는 SMS 수신여부와 상관없이 보냄
/*
			$SMS = new \SMS5;
			$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], 1); //$config['cf_icode_server_port']
		
			//$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
*/			$reply = str_replace('-', '', trim($sms5['cf_phone']));

			$list[0]['hp'] = str_replace('-', '', trim($member_row['mb_hp']));
			$list[0]['name'] = $member_row['mb_name'];
			$list[0]['mb_id'] = $member_row['mb_id'];
			$dest[] = str_replace('-', '', $member_row['mb_hp']);


//aligo send api
// $sms_url = "https://apis.aligo.in/send/"; // 전송요청 URL
// $sms['user_id'] = "okxogns"; // SMS 아이디
// $sms['key'] = "knfmfn2frv6a0qk8j2d3y6va1394kvcb";//인증키
// /******************** 인증정보 ********************/
// /******************** 전송정보 ********************/
// $_POST['msg'] = $msg['message']; // 메세지 내용
// $_POST['receiver'] = $member_row['mb_hp']; // 수신번호
// $_POST['destination'] = ''; // 수신인 %고객
// $_POST['sender'] = '1688-7551'; // 발신번호
// $_POST['rdate'] = ''; // 예약일자 - 20161004 : 2016-10-04일기준
// $_POST['rtime'] = ''; // 예약시간 - 1930 : 오후 7시30분
// $_POST['testmode_yn'] = ''; // Y 인경우 실제문자 전송X , 자동취소(환불) 처리
// /******************** 전송정보 ********************/

// $sms['msg'] = stripslashes($_POST['msg']);
// $sms['receiver'] = $_POST['receiver'];
// $sms['destination'] = $_POST['destination'];
// $sms['sender'] = $_POST['sender'];
// $sms['rdate'] = $_POST['rdate'];
// $sms['rtime'] = $_POST['rtime'];
// $sms['testmode_yn'] = empty($_POST['testmode_yn']) ? '' : $_POST['testmode_yn'];
// // 이미지 전송시
// if(!empty($_POST['image'])) {
//         if(file_exists($_POST['image'])) {
//                 $tmpFile = explode('/',$_POST['image']);
//                 $str_filename = $tmpFile[sizeof($tmpFile)-1];
//                 $tmp_filetype = 'image/jpeg';
//                 $sms['image'] = '@'.$_POST['image'].';filename='.$str_filename. ';type='.$tmp_filetype;
//         }
// }
// /*****/

// $host_info = explode("/", $sms_url);
// $port = $host_info[0] == 'https:' ? 443 : 80;

// $oCurl = curl_init();

// curl_setopt($oCurl, CURLOPT_PORT, $port);
// curl_setopt($oCurl, CURLOPT_URL, $sms_url);
// curl_setopt($oCurl, CURLOPT_POST, 1);
// curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt($oCurl, CURLOPT_POSTFIELDS, $sms);
// curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
// $ret = curl_exec($oCurl);
// curl_close($oCurl);
//aligo EOS


//cafe24 SERVICE
$weekday = date('w');

// $sms_url = "https://sslsms.cafe24.com/sms_sender.php"; // 전송요청 URL
// $sms['user_id'] = "abcpp4067"; // SMS 아이디
// $sms['secure'] = "56d24d3e24d4e3f4cd8c8fcf83e69b2d";//인증키

$_POST['msg'] = $msg['message']; // 메세지 내용
//$_POST['receiver'] = $member_row['mb_hp']; // 수신번호

$_POST['receiver'] = '010-3535-1555';
$_POST['destination'] = ''; // 수신인 %고객
$_POST['sender'] = '1688-7551'; // 발신번호
$_POST['rdate'] = ''; // 예약일자 - 20161004 : 2016-10-04일기준
$_POST['rtime'] = ''; // 예약시간 - 1930 : 오후 7시30분
$_POST['testmode_yn'] = ''; // Y 인경우 실제문자 전송X , 자동취소(환불) 처리

//sms split
//$sPhoneArray = explode("-", $member_row['mb_hp']);
// $sPhoneArray = explode("-", '010-3535-1555');
// $sPhone1 = $sPhoneArray[0];
// $sPhone2 = $sPhoneArray[1];
// $sPhone3 = $sPhoneArray[2];

/******************** 전송정보 ********************/
$sms['msg'] = stripslashes($_POST['msg']);
$sms['rphone'] = $_POST['receiver'];
$sms['sphone1'] = '1688';
$sms['sphone2'] = '7551';
$sms['sphone3'] = '';
$sms['rdate'] = '';
$sms['rtime'] = '';
//$sms['mode'] = base64_encode("1"); // base64 사용시 반드시 모드값을 1로 주셔야 합니다.
$sms['testflag'] = "Y";

if($weekday == 0) { // 6 토요일, 0 일요일
	$sms['smsType'] = '';
}else {
	$sms['smsType'] = 'L';
	$sms['title '] =  '-패밀리 추천번호-';
	$sms['subject'] =  '-패밀리 추천번호-';
}


/*****/

$oCurl = curl_init();
$url =  "https://sslsms.cafe24.com/sms_sender.php";
$sms['user_id'] = "abcpp4067"; // SMS 아이디
$sms['secure'] = "56d24d3e24d4e3f4cd8c8fcf83e69b2d"; // 인증키
curl_setopt($oCurl, CURLOPT_URL, $url);
curl_setopt($oCurl, CURLOPT_POST, 1);
curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($oCurl, CURLOPT_POSTFIELDS, $sms);
curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, 0);
$ret = curl_exec($oCurl);
echo $ret;
curl_close($oCurl);



			//$hp = str_replace('-','', trim($member_row['mb_hp']));
			//$list[]['bk_hp'] =  $hp;

			//$result = $SMS->Add2($list, $reply, '', '', $msg['title'], '', count($list));

			/*$result = $SMS->Add($dest, $reply, '', '', '', $msg['message'], '', count($dest));


			if($result) {
				$result = $SMS->Send();
				//$result = $SMS->Result;

				if($result) {*/
					$row = sql_fetch("select max(wr_no) as wr_no from {$g5['sms5_write_table']}");
					if ($row)
						$wr_no = $row['wr_no'] + 1;
					else
						$wr_no = 1;

				$termService = new TermService();

				$termService->SaveSendSMS($wr_no, $_POST['sender'], $msg['message'], '0000-00-00 00:00:00', 1, 1);

        			$termService->OKSendSMS($wr_no, $member_row['mb_id'], $member_row['mb_name'], $member_row['mb_hp'], "문자가 발송되었습니다.");


/*
					$this->db->rawQuery("insert into {$g5['sms5_write_table']} set wr_no='$wr_no', wr_renum=0, wr_reply='$reply', wr_message='".$msg['message']."', wr_booking='$wr_booking', wr_total='1', wr_datetime='".G5_TIME_YMDHIS."'");

					$wr_success = 1;//0;
					$wr_failure = 0;//0;
					$count      = 1;//0;
*/
/*
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


						$this->db->rawQuery("insert into {$g5['sms5_history_table']} set wr_no='$wr_no', wr_renum=0, bg_no='{$row['bg_no']}', mb_id='".$row['mb_id']."', bk_no='{$row['bk_no']}', hs_name='".addslashes($row['name'])."', hs_hp='{$row['hp']}', hs_datetime='".G5_TIME_YMDHIS."', hs_flag='$hs_flag', hs_code='$hs_code', hs_memo='".addslashes($hs_memo)."', hs_log='".addslashes($log)."'");
					}
					$SMS->Init(); // 보관하고 있던 결과값을 지웁니다.
*/
					//$this->db->rawQuery("update {$g5['sms5_write_table']} set wr_success='$wr_success', wr_failure='$wr_failure', wr_memo='$str_serialize' where wr_no='$wr_no' and wr_renum=0");


/*				}

				foreach ($SMS->Result as $result) {
					list($phone, $code) = explode(":", $result);
				}
				*/
			//}
/*
$this->db->rawQuery("insert into {$g5['sms5_history_table']} set wr_no='$wr_no', wr_renum=0, bg_no='{$row['bg_no']}', mb_id='".$row['mb_id']."', bk_no='{$row['bk_no']}', hs_name='".addslashes($row['name'])."', hs_hp='{$row['hp']}', hs_datetime='".G5_TIME_YMDHIS."', hs_flag='$hs_flag', hs_code='0', hs_memo='문자를 발송하였습니다.', hs_log=''");
*/
		}

	}
	
}
