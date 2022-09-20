<?php
/**
 * Created by PhpStorm.
 * User: acerunner
 * Date: 16. 2. 11.
 * Time: 오후 1:52
 */

namespace Acesoft\LottoApp\Member;

use \Acesoft\Core\Base as Base;
use \Acesoft\Core\DB as DB;
use \Acesoft\LottoApp\User as User;
use \Acesoft\Common\Utils as Utils;
use \PHPMailer;

class Mail extends Base {

    public $db;
    private $category;
	private $mail_table;

    public function __construct()
    {
        parent::__construct();
        $this->db = DB::getInstance();
		$this->mail_table = $this->table['Mail'];
    }


    public function getMailList($page, $url) {

        $page = $page > 0 ? $page : 1;

        if($_GET['sc'] && $_GET['sv']) {
            $this->db->where($_GET['sc'], '%'.$_GET['sv'].'%', 'LIKE');
        }

        $this->db->pageLimit = 5;
        $this->db->orderBy('ma_time');
        $list = $this->db->arraybuilder()->paginate($this->mail_table, $page);


        $link = Utils::getPagination($this->db->totalPages, $page, $url);

        $data['list'] = $list;
        $data['link'] = $link;
		$data['total_count'] = $this->db->totalCount;
		$data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
		$data['total_pages'] = $this->db->totalPages;
		$data['page'] = $page;

        return $data;
    }

	public function addMail() {

		
		$data = Array (
			'ma_subject' => $_POST['ma_subject'],
			'ma_content' => $_POST['ma_content'],
			'ma_time' => $this->db->NOW(),
			'ma_ip' => $_SERVER['REMOTE_ADDR']
        );

		
        $id = $this->db->insert($this->table['Mail'], $data);

	}

	public function modifyMail() {

		$data = Array (
			'ma_subject' => $_POST['ma_subject'],
			'ma_content' => $_POST['ma_content'],
			'ma_ip' => $_SERVER['REMOTE_ADDR']
        );

		$this->db->where('ma_id', $_POST['id']);
        $id = $this->db->update($this->table['Mail'], $data);

	}

	public function deleteMail() {

		$this->db->where('ma_id', $_POST['id']);
        $id = $this->db->delete($this->table['Mail']);
	}


	public static function getMail($id) {

		$db = DB::getInstance();

		$db->where("ma_id", $id);
		$data = $db->arraybuilder()->getOne("wowbay_mail");

		return $data;

	}

	function testMail($id, $mb_id) {
		
		$member = (new User())->getUser($mb_id);

		
		$name = get_text($member['mb_name']);
		$nick = $member['mb_nick'];
		$mb_id = $member['mb_id'];
		$email = $member['mb_email'];
		$member['mb_email'] = "invaderx@naver.com";
		
		$ma = $this->db->rawQueryOne("select ma_subject, ma_content from wowbay_mail where ma_id = '{$id}'");

		$subject = Utils::getContent($ma['ma_subject']);

		$content = Utils::getContent($ma['ma_content']);
		$content = preg_replace("/{이름}/", $name, $content);
		$content = preg_replace("/{닉네임}/", $nick, $content);
		$content = preg_replace("/{회원아이디}/", $mb_id, $content);
		$content = preg_replace("/{이메일}/", $email, $content);

		$mb_md5 = md5($member['mb_id'].$member['mb_email'].$member['mb_datetime']);

		$content = $content . '<p>더 이상 정보 수신을 원치 않으시면 [<a href="'.$this->getAppPublicUrl().'/email_stop.php?mb_id='.$mb_id.'&amp;mb_md5='.$mb_md5.'" target="_blank">수신거부</a>] 해 주십시오.</p>';

		$this->sendMail($config['cf_title'], $member['mb_email'], $member['mb_email'], $subject, $content, 1);

		$data['name'] = $name;
		$data['email'] = $email;

		return $data;

	}


	public function sendMail($fname, $fmail, $to, $subject, $content, $type=0, $file="", $cc="", $bcc="") {
		
		if ($type != 1)
			$content = nl2br($content);

		$mail = new PHPMailer(); // defaults to using php "mail()"
		if (defined('G5_SMTP') && G5_SMTP) {
			$mail->IsSMTP(); // telling the class to use SMTP
			$mail->Host = G5_SMTP; // SMTP server
			if(defined('G5_SMTP_PORT') && G5_SMTP_PORT)
				$mail->Port = G5_SMTP_PORT;
		}
		$mail->CharSet = 'UTF-8';
		$mail->From = $fmail;
		$mail->FromName = $fname;
		$mail->Subject = $subject;
		$mail->AltBody = ""; // optional, comment out and test
		$mail->msgHTML($content);
		$mail->addAddress($to);
		if ($cc)
			$mail->addCC($cc);
		if ($bcc)
			$mail->addBCC($bcc);
		//print_r2($file); exit;
		if ($file != "") {
			foreach ($file as $f) {
				$mail->addAttachment($f['path'], $f['name']);
			}
		}
		return $mail->send();
	}

	// 파일을 첨부함
	public function attachFile($filename, $tmp_name)
	{
		// 서버에 업로드 되는 파일은 확장자를 주지 않는다. (보안 취약점)
		$dest_file = G5_DATA_PATH.'/tmp/'.str_replace('/', '_', $tmp_name);
		move_uploaded_file($tmp_name, $dest_file);
		$tmpfile = array("name" => $filename, "path" => $dest_file);
		return $tmpfile;
	}

	// 문의메일 발송시 함께 받기 위한 관리자 정보
	public function getAdminMailInfo() {
		$admin = User::getUser('admin');

		$add_user['admin'] = array(
				'mb_id' => $admin['mb_id'],
				'mb_email' => $admin['mb_email'],
				'mb_hp' => $admin['mb_hp'],
				'mb_com_name' => $admin['mb_com_name'],
				'mb_mailling' => 1
			);

		return $add_user;
	}

}