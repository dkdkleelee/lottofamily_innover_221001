<?php

namespace Acesoft\LottoApp;

use \Acesoft\Core\Base;
use \Acesoft\Core\DB;
use \Acesoft\Common\Utils;
use \Acesorf\LottoApp\ServiceConfig;
use \GuzzleHttp\Client;
use \Symfony\Component\DomCrawler\Crawler;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TermService  extends Base {

	var $tables;
	var $service_type;
	var $service_limit;
	var $config;
	var $svc_config;

	function __construct() {
    	parent::__construct();
		$this->db = DB::getInstance();

        $this->db->orderBy('sg_no');
		$rows = $this->db->get($this->tb['TermServiceGrade'], null, '*');
        
        foreach($rows as $row) {
            $this->service_grade[$row['sg_no']] = $row['sg_name'];
			$this->service_grade_extractor[$row['sg_no']] = $row['sg_extractor'];
            $sg_limit = unserialize($row['sg_limit']);
            $this->service_grade_limit[$row['sg_no']] = $sg_limit;
            
        }
    }

	function getDefaultConfig() {
		$data = $this->db->arrayBuilder()->getOne($this->tb['TermServiceDefaultConfig']);

		return $data;
	}

	function updateTermServiceDefaultConfig() {
		$data = array(
						'tdc_accounts' => $_POST['tdc_accounts'],
						'tdc_provision' => $_POST['tdc_provision'],
						'tdc_provision_user' => $_POST['tdc_provision_user']
				);

		$this->db->update($this->tb['TermServiceDefaultConfig'], $data);
	}

	function getTermServiceConfigList($page=1, $url='') {

        global $pageLimit;

        $pageLimit = $pageLimit ? $pageLimit : 20;

        $page = $page > 0 ? $page : 1;

		if($_GET['sg_no']) {
			$this->db->where("sg_no", $_GET['sg_no']);
		}

		if($_GET['sc_name']) {
			$this->db->where("sc_name LIKE '%".$_GET['sc_name']."%'");
		}

		//▶ page rows
		$this->db->pageLimit = $pageLimit;

		//▶ order block
		$this->db->orderBy('sc_order', 'DESC');
		//$this->db->orderBy('sg_no', 'ASC');

        $list = $this->db->arraybuilder()->paginate($this->tb['TermServiceConfig'], $page, "*");


        $link = Utils::getPagination($this->db->totalPages, $page, $url);

		$data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
        $data['list'] = $list;
        $data['link'] = $link;
		$data['total_count'] = $this->db->totalCount;
		$data['total_pages'] = $this->db->totalPages;
		$data['page'] = $page;
        return $data;
	}

	function getTermServiceBuyList($page=1, $url='') {
		global $pageLimit;

        $pageLimit = $pageLimit ? $pageLimit : 20;

        $page = $page > 0 ? $page : 1;

		if($_GET['sc'] && $_GET['sv']) {
			$this->db->where($_GET['sc']." LIKE '%".$_GET['sv']."%'");
		}

		if($_GET['s_mb_id']) {
			$this->db->where("a.mb_id like '%".$_GET['s_mb_id']."%'");
			$page = 1;
		}

		if($_GET['s_date']) {
			$this->db->where("DATE_FORMAT(a.sb_paydate, '%Y-%m-%d') >= DATE_FORMAT('".$_GET['s_date']."', '%Y-%m-%d')");
		}

		if($_GET['e_date']) {
			$this->db->where("DATE_FORMAT(a.sb_paydate, '%Y-%m-%d') <= DATE_FORMAT('".$_GET['e_date']."', '%Y-%m-%d')");
		}

		if($_GET['s_pay_status']) {
			$this->db->where('a.sb_pay_status', $_GET['s_pay_status']);
		}

		if($_GET['s_mg_no']) {
			if($_GET['s_mg_no'] == 'na') {
				$this->db->where("(c.mg_no IS NULL OR c.mg_no = '')");
			} else {
				$this->db->where('c.mg_no', $_GET['s_mg_no']);
			}
		}

		//▶ page rows
		$this->db->pageLimit = $pageLimit;

		//▶ order block
		$this->db->orderBy('sb_no', 'DESC');

		$this->db->join($this->tb['Member']." as b", "a.mb_id=b.mb_id", "LEFT");
		$this->db->join($this->tb['Member']." as c", "a.sb_tm_id=c.mb_id", "LEFT");
		$this->db->join($this->tb['Group']." as d", "c.mg_no=d.mg_no", "LEFT");
		
		$list = $this->db->arraybuilder()->paginate($this->tb['TermServiceBuy']." as a ", $page, "a.*, b.mb_name, c.mg_no as mg_no, d.mg_name");
        $link = Utils::getPagination($this->db->totalPages, $page, $url);

		$data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
        $data['list'] = $list;
        $data['link'] = $link;
		$data['total_count'] = $this->db->totalCount;
		$data['total_pages'] = $this->db->totalPages;
		$data['page'] = $page;

		return $data;
	}

	function getTermServiceUseList($page=1, $url='') {
		global $pageLimit;

        $pageLimit = $pageLimit ? $pageLimit : 20;

        $page = $page > 0 ? $page : 1;

		if($_GET['sc'] && $_GET['sv']) {
			$this->db->where($_GET['sc']." LIKE '%".$_GET['sv']."%'");
		}

		if($_GET['s_mb_id']) {
			$this->db->where('a.mb_id', $_GET['s_mb_id']);
		}

		if($_GET['s_sg_no']) {
			$this->db->where('a.sg_no', $_GET['s_sg_no']);
		}

		//▶ page rows
		$this->db->pageLimit = $pageLimit;

		//▶ order block
		$this->db->orderBy('su_no', 'DESC');

		$this->db->join($this->tb['Member']." as b", "a.mb_id=b.mb_id", "LEFT");

		$list = $this->db->arraybuilder()->paginate($this->tb['TermServiceUse']." as a ", $page, "a.*, b.*, IF(DATEDIFF(su_enddate, now()) > 0, DATEDIFF(su_enddate, now()), 0) as leftDays");
        $link = Utils::getPagination($this->db->totalPages, $page, $url);

		$data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
        $data['list'] = $list;
        $data['link'] = $link;
		$data['total_count'] = $this->db->totalCount;
		$data['total_pages'] = $this->db->totalPages;
		$data['page'] = $page;

		return $data;
	}

	public function getMyService($mb_id) {

		if(!$mb_id) return;
		$this->db->where('a.mb_id', $mb_id);
		$this->db->orderBy('su_no', 'DESC');
		$this->db->join($this->tb['Member']." as b", "a.mb_id=b.mb_id", "LEFT");
		$row = $this->db->arraybuilder()->get($this->tb['TermServiceUse']." as a ", $page, "a.*, b.*, IF(DATEDIFF(su_enddate, now()) > 0, DATEDIFF(su_enddate, now()), 0) as leftDays");

		return $row;
	}

	public function getServiceUseCount() {
		
/*
		$this->db->where('a.su_enddate >= NOW()');
		$this->db->where("a.sg_no = '5'");
		$row = $this->db->getOne($this->tb['TermServiceUse']." as a",'count(*) as cnt');

		
		$this->db->where('a.su_enddate >= NOW()');
		$this->db->where("a.sg_no = '4'");
		$row = $this->db->getOne($this->tb['TermServiceUse']." as a",'count(*) as cnt');
*/
		$service_grades = $this->service_grade;
		$service_grades['normal'] = '일반';

		$total = 0;
		foreach($service_grades as $key => $value) {
			
			if($key == 'normal') { // 서비스 미사용자
				$this->db->where("b.sg_no IS NULL");
			} else {
				$this->db->where("b.sg_no = '".$key."'");
			}

			if($_GET['s_status']) {
				$this->db->where('mb_status', $_GET['s_status']);
			}

			if($_GET['s_not_distributed']) {
				$this->db->where("mb_tm_id IS NULL OR mb_tm_id=''");
			}

			if($_GET['s_distributed']) {
				$this->db->where("mb_tm_id IS NOT NULL AND mb_tm_id <> ''");
			}

			// tm회원의 경우
			if($_GET['s_tm']) $this->db->where('mb_tm_id', $_GET['s_tm']);
			$this->db->where('mb_level', '2');

			$this->db->join("(SELECT mb_id, su_enddate, su_no, sg_no FROM ".$this->tb['TermServiceUse']." WHERE su_enddate >= NOW()) as b", "a.mb_id=b.mb_id", "LEFT");
			$row = $this->db->getOne($this->tb['Member']." as a",'count(*) as cnt');

			$data[$value] = $row['cnt'];
			$data['total'] += $row['cnt'];
		}
		
		return $data;

	}

	// 기본서비스환경설정
	function updateServiceConfig() {

		$row = $this->db->arraybuilder()->getOne($this->tb['TermServiceConfig'],'*');
		

		$sql = (is_array($row)) ? "UPDATE " : "INSERT INTO ";

		$data['ic_account'] = $_POST['ic_account'];

		if(is_array($row)) {
			$this->db->update($this->tb['TermServiceConfig'], $data);
		} else {
			$this->db->insert($this->tb['TermServiceConfig'], $data);
		}

		

		return true;
	}

	function getServiceConfig() {
		return $this->getDefaultConfig();
	}
		

	function addTermServiceConfig() {
		
		
		$sc_view_limit = serialize($_POST['sc_view_limit']);


		$this->db->rawQuery("INSERT INTO ".$this->tb['TermServiceConfig']." SET sg_no='".$_POST['sg_no']."',
		                                                                sc_name='".$_POST['sc_name']."',
																		sc_detail1='".$_POST['sc_detail1']."',
																		sc_detail2='".$_POST['sc_detail2']."',
		                                                                sc_price='".$_POST['sc_price']."',
																		sc_pre_discount_price='".$_POST['sc_pre_discount_price']."',
		                                                                sc_term='".$_POST['sc_term']."',
		                                                                sc_term_type='".$_POST['sc_term_type']."'");
	}
	


	function modifyTermServiceConfig() {
		


        $sc_view_limit = serialize($_POST['sc_view_limit']);

		$this->db->rawQuery("UPDATE ".$this->tb['TermServiceConfig']." SET  sg_no='".$_POST['sg_no']."',
		                                                                sc_name='".$_POST['sc_name']."',
																		sc_detail1='".$_POST['sc_detail1']."',
																		sc_detail2='".$_POST['sc_detail2']."',
		                                                                sc_price='".$_POST['sc_price']."',
		                                                                sc_pre_discount_price='".$_POST['sc_pre_discount_price']."',
		                                                                sc_term='".$_POST['sc_term']."',
																		sc_soldout='".$_POST['sc_soldout']."',
																		sc_order='".$_POST['sc_order']."',
		                                                                sc_term_type='".$_POST['sc_term_type']."' WHERE sc_no='".$_POST['sc_no']."'");
	}
	
	// 등급 정보수정
	function modifyTermServiceGrade() {

    	
    	$st_limit = serialize($_POST['sg_limit']);
    	
    	$this->db->rawQuery("UPDATE ".$this->tb['TermServiceGrade']." SET 
		                                                                sg_name='".$_POST['sg_name']."',
		                                                                sg_limit='".$st_limit."' WHERE sg_no='".$_POST['sg_no']."'");
    	
	}
	
	
	function getServiceList() {

    	
		$this->db->orderBy("sc_order", "DESC");
    	$rows = $this->db->arrayBuilder()->get($this->tb['TermServiceConfig'], null, "*");
    	foreach($rows as $row) {
        	$row['sg_grade'] = $this->service_grade[$row['sg_no']];
        	$row['sg_grade_limit'] = $this->service_grade_limit[$row['sg_no']];
        	$list[] = $row;
    	}

    	return $list;
	}

	function getService($no) {

    	
		$this->db->where("sc_no", $no);

		$this->db->join($this->tb['TermServiceGrade']." as b", "a.sg_no=b.sg_no", "LEFT");
    	$row = $this->db->arrayBuilder()->getOne($this->tb['TermServiceConfig']." as a ","*");

    	return $row;
	}

	function deleteTermServiceConfig($sc_no) {
		global $db;

		$this->db->where("sc_no", $sc_no);
		$this->db->delete($this->tb['TermServiceConfig']);
	}

	function addServiceBuy() {
		global $db;
		
		
		$this->db->where("sc_no", $_POST['sc_no']);
        $row = $this->db->arrayBuilder()->getOne($this->tb['TermServiceConfig'], "*");

		// 수정가격이 넘어오면 가격을 수정
		if($_POST['new_price'] >= 1000 && $_POST['new_price'] != $row['sc_price']) {
			$row['sc_price'] = $_POST['new_price'];
		}

		if($_POST['sb_not_paid_price']) {
			$sb_price_total = $row['sc_price']+$_POST['sb_not_paid_price'];
		} else {
			$sb_price_total = $row['sc_price'];
		}

		$data = array(
						'mb_id' => $_POST['mb_id'],
						'sb_tm_id' => $_POST['tm_id'],
						'sc_no' => $row['sc_no'],
						'sg_no' => $row['sg_no'],
						'sb_name' => $row['sc_name'],
						'sb_price' => $row['sc_price'],
						'sb_total_price' => $sb_price_total,
						'sb_not_paid_price' => $_POST['sb_not_paid_price'],
						'sb_term' => $row['sc_term'],
						'sb_term_type' => $row['sc_term_type'],
						'sb_buyer_name' => $_POST['ordername'],
						'sb_buyer_hp' => $_POST['phoneno'],
						'sb_ay_cardno' => $_POST['cardno'],
						'sb_ay_birth' => $_POST['sb_ay_birth'],
						'sb_ay_f2code' => $_POST['sb_ay_f2code'],
						'sb_ay_expmon' => $_POST['expmon'],
						'sb_ay_expyear' => $_POST['expyear'],
						'sb_ay_installment' => $_POST['installment'],
						'sb_pay_method' => $_POST['pay_method'],
						'sb_pay_name' => $_POST['payer_name'],
						'sb_bank_account' => $_POST['bank_account'],
						'sb_pay_status' =>'N',
						'sb_agree_provision' => ($_POST['agree'] ? '1' : '0'),
						'sb_regdate' => $this->db->NOW()
				);

		$id = $this->db->insert($this->tb['TermServiceBuy'], $data);

		return $id;
	}

	function updateServiceBuy($no) {
		$this->db->rawQuery("UPDATE ".$this->tb['TermServiceBuy']." SET sb_ay_cardno='".$_POST['cardno']."',
																	 sb_ay_expmon='".$_POST['expmon']."',
																	 sb_ay_expyear='".$_POST['expyear']."',
																	 sb_ay_installment='".$_POST['installment']."',
																	 sb_ordername='".$_POST['installment'].",
																	 sb_phoneno='".$_POST['phoneno']." WHERE sb_no='".$no."'");
	}

	function setMaskToCardInfo($sb_no='') {

		if($sb_no) {
			
			$this->db->rawQuery("UPDATE ".$this->tb['TermServiceBuy']." SET sb_ay_cardno=CONCAT(SUBSTR(`sb_ay_cardno`, 1, 6), REPEAT('*',  6), SUBSTR(`sb_ay_cardno`, 13)),
																	 sb_ay_expyear=REPEAT('*',  2), sb_ay_expyear=REPEAT('*',  4), sb_ay_birth='******', sb_ay_f2code='**' WHERE sb_no='".$sb_no."'");
		} else {
			$day = 1; // 삭제기한 1일 이전 데이터 카드정보 마스킹
			$this->db->rawQuery("UPDATE ".$this->tb['TermServiceBuy']." SET sb_ay_cardno=CONCAT(SUBSTR(`sb_ay_cardno`, 1, 6), REPEAT('*',  6), SUBSTR(`sb_ay_cardno`, 13)),
																	 sb_ay_expyear=REPEAT('*',  2), sb_ay_expyear=REPEAT('*',  4), sb_ay_birth='******', sb_ay_f2code='**' WHERE date_add(sb_regdate,INTERVAL ".$day." DAY) < now()");
		}
	}


	function getServiceBuy($no) {

		$this->db->where("sb_no", $no);
    	$this->db->join($this->tb['TermServiceConfig']." as b", "a.sc_no=b.sc_no", "LEFT");
    	$row = $this->db->arrayBuilder()->getOne($this->tb['TermServiceBuy']." as a ","*");

    	return $row;
	}
	
	function changeBuyStatus($sb_no, $status, $pg_name='', $transaction_id='') {

    	if($status == 'Y') { // 승인
        	$this->db->rawQuery("UPDATE ".$this->tb['TermServiceBuy']." SET sb_pay_status='".$status."', sb_paydate=now(), sb_ay_pg='".$pg_name."', sb_ay_transaction_id='".$transaction_id."' WHERE sb_no='".$sb_no."'");

			$this->db->where("sb_no", $sb_no);
    		$row = $this->db->arrayBuilder()->getOne($this->tb['TermServiceBuy'], "*");

			
			$this->addServiceTerm($row['mb_id'], $row['sg_no'], $row['sb_term'], $row['sb_term_type']);
    	} else if($status == 'C') { // 취소
			$this->db->rawQuery("UPDATE ".$this->tb['TermServiceBuy']." SET sb_pay_status='".$status."', sb_canceldate=now() WHERE sb_no='".$sb_no."'");
		}
	}
	
	function deleteServiceBuy($sb_no) {
    	
    	$this->db->where("sb_no", $sb_no);
    	$row = $this->db->getOne($this->tb['TermServiceBuy'], "*");

        if($row['sb_pay_status'] == 'Y') {

    		//$this->subServiceTerm($row['sb_term'], $row['sb_term_type']);
        }
        
		$this->db->where("sb_no", $sb_no);
		$this->db->delete($this->tb['TermServiceBuy']);
	}

	function joinServiceUser() {
		global $db;

		$mb_id = trim($_POST['mb_id']);

		if(!$_POST['mb_id'])
			alert('회원아이디 값이 없습니다. 올바른 방법으로 이용해 주십시오.');

		$mb_password    = trim($_POST['mb_password']);
		$mb_password_re = trim($_POST['mb_password_re']);
		$mb_name        = trim($_POST['mb_name']);
		$mb_nick        = trim($_POST['mb_nick']);
		$mb_hp          = isset($_POST['mb_hp'])            ? trim($_POST['mb_hp'])          : "";

		$mb = $db->fetchRow("SELECT * FROM ".$this->tb['member']." WHERE mb_id='".$mb_id."'");

		if($mb['mb_id']) {
			alert("이미 가입된 회원 아이디 입니다.");
			exit;
		}

		$this->db->rawQuery("INSERT INTO ".$this->tb['member']." SET
										 mb_id = '{$mb_id}',
										 mb_password = '".get_encrypt_string($mb_password)."',
										 mb_name = '{$mb_name}',
										 mb_nick = '{$mb_nick}',
										 mb_nick_date = '".G5_TIME_YMD."',
										 mb_email = '{$mb_email}',
										 mb_homepage = '{$mb_homepage}',
										 mb_hp = '{$mb_hp}',
										 mb_today_login = '".G5_TIME_YMDHIS."',
										 mb_datetime = '".G5_TIME_YMDHIS."',
										 mb_ip = '{$_SERVER['REMOTE_ADDR']}',
										 mb_level = '{$config['cf_register_level']}',
										 mb_recommend = '{$mb_recommend}',
										 mb_login_ip = '{$_SERVER['REMOTE_ADDR']}',
										 mb_mailling = '{$mb_mailling}',
										 mb_sms = '1',
										 mb_open = '0',
										 mb_open_date = '".G5_TIME_YMD."'");

		$this->updateServiceBuyMemberId($_POST['sb_no'], $mb_id);
		$_SESSION['sb_no'] = "";

	}

	function updateServiceBuyMemberId($sb_no, $mb_id) {
		global $db;

		$this->db->rawQuery("UPDATE ".$this->tb['TermServiceBuy']." SET mb_id='".$mb_id."' WHERE sb_no='".$sb_no."' ");
	}

	function getTermServiceGrade() {
		return $this->service_grade;
	}

	function getMemberServiceUse($mb_id) {
		$row = $this->db->arrayBuilder()->rawQuery("SELECT * FROM ".$this->tb['TermServiceUse']." as a left join ".$this->tb['TermServiceGrade']." as b on( a.sg_no=b.sg_no) WHERE mb_id='".$mb_id."' AND (su_enddate >= now()  OR datediff(su_enddate, su_pausedate) > 0) ORDER BY su_enddate DESC");

		return $row;
	}

	function getMemberServiceBuy($mb_id) {
		$row = $this->db->arrayBuilder()->rawQuery("SELECT sum(sb_total_price)  as total_pay FROM ".$this->tb['TermServiceBuy']." WHERE mb_id='".$mb_id."' AND sb_pay_status = 'Y'");

		return $row;
	}
	
	
	/********************************************************************
    *
    *   $term : 숫자
    *   $term_type : month,  day 
    *
    *
    *********************************************************************/
	function addServiceTerm($mb_id, $sg_no, $term=0, $term_type='month') {

		// 구매서비스 이외의 다른 등급서비스 삭제(요청으로 2018. 10. 15.)
		$this->db->where("mb_id", $mb_id);
		$this->db->where("sg_no <> $sg_no");
		$this->db->delete($this->tb['TermServiceUse']);


		// 서비스 추가
    	$this->db->where("mb_id", $mb_id);
		$this->db->where("sg_no", $sg_no);

		$row = $this->db->arrayBuilder()->getOne($this->tb['TermServiceUse'], "su_no, IF(su_enddate > now(), su_enddate, now()) as enddate");
		/*
    	$row = $db->fetchRow("SELECT su_no, IF(su_enddate > now(), su_enddate, now()) as enddate 
    	                        FROM ".$this->tb['TermServiceUse']."
    	                        WHERE mb_id='".$mb_id."' AND sg_no='".$sg_no."'");
    	*/                        
    	      
    	                   

        if($row['enddate'] != '') {

		    $this->db->rawQuery("UPDATE ".$this->tb['TermServiceUse']." SET su_enddate=DATE_ADD('".$row['enddate']."', INTERVAL ".$term." ".$term_type.")
		                                                                 WHERE su_no='".$row['su_no']."'");
		                                                                 
        } else {
            $this->db->rawQuery("INSERT INTO ".$this->tb['TermServiceUse']." SET mb_id='".$mb_id."', sg_no='".$sg_no."', su_startdate=NOW(),
                                                                         su_enddate=DATE_ADD(NOW(), INTERVAL ".$term." ".$term_type.")");
        }
    	
	}
	
	/********************************************************************
    *
    *   $term : 숫자
    *   $term_type : month,  day 
    *
    *
    *********************************************************************/
	function subServiceTerm($su_no, $term=0, $term_type='month') {

		$this->db->where("sg_no", $sg_no);

		$row = $this->db->arrayBuilder()->getOne($this->tb['TermServiceUse'], "SELECT IF(su_enddate > now(), su_enddate, '') as enddate");
    	
		/*
    	$row = $db->fetchRow("SELECT IF(su_enddate > now(), su_enddate, '') as enddate 
    	                        FROM ".$this->tb['TermServiceUse']."
    	                        WHERE su_no='".$su_no."'");
		*/
        if($row['enddate'] != '') {

		    $this->db->rawQuery("UPDATE ".$this->tb['TermServiceUse']." SET su_enddate=DATE_SUB('".$row['enddate']."', INTERVAL ".$term." ".$term_type.")
		                                                                 WHERE su_no='".$su_no."'");
        }
    	
	}
	
	
	//  직접수정
	function updateServiceUse($su_no, $sg_no, $end_date) {

    	
    	$this->db->rawQuery("UPDATE ".$this->tb['TermServiceUse']." SET   sg_no='".$sg_no."',
    	                                                                su_enddate='".$end_date."'
		                                                                 WHERE su_no='".$su_no."'");
    	
	}

	//  종료일 수정
	function updateServiceEndDate($su_no, $end_date) {
    	$this->db->rawQuery("UPDATE ".$this->tb['TermServiceUse']." SET su_enddate='".$end_date."'
		                                                                 WHERE su_no='".$su_no."'");
	}

	//  서비스 이용중지
	function stopService($su_no) {
    	$this->db->rawQuery("UPDATE ".$this->tb['TermServiceUse']." SET su_pausedate=NOW()
		                                                                 WHERE su_no='".$su_no."'");
	}

	// 서비스 이용시작
	function resumeService($su_no) {

		$this->db->where("su_no", $su_no);
		$row = $this->db->getOne($this->tb['TermServiceUse'],"datediff(su_enddate, su_pausedate) as left_days");

		$this->db->rawQuery("UPDATE ".$this->tb['TermServiceUse']." SET su_pausedate=NULL,
																	    su_enddate=date_add(NOW(), INTERVAL ".$row['left_days']." day) 
		                                                                 WHERE su_no='".$su_no."'");
	}
	
	function modifyExtractNum($su_no, $num) {
		$this->db->rawQuery("UPDATE ".$this->tb['TermServiceUse']." SET   su_extract_per_week='".$num."' WHERE su_no='".$su_no."'");
	}
	
	
	// 서비스 이용정보 삭제
	function deleteServiceUse($su_no) {

    	$this->db->rawQuery("DELETE FROM ".$this->tb['TermServiceUse']." WHERE su_no='".$su_no."'");
	}
	
	
	// 서비스 유효성 체크 : 서비스 이용, 기간, 이용갯수
	function checkeServiceUse($user_id) {
    	global $db, $auction;
    	
    	
        $this->db->rawQuery("SELECT * FROM ".$this->tb['TermServiceUse']."
    	                         WHERE mb_id='".$user_id."' AND su_enddate >= now()");
    	
    	
    	$view_limit = array();  
    	$view_limit['use'] = 0;                    
        while($row = $db->fetch(55)) {
            // 서비스 종류별 열람갯수
    		$view_limit['use'] = "1";
 
    		foreach($auction->dft_config['service_name'] as $key => $value) {
        		
        		$view_limit[$key] = $view_limit[$key]+$this->service_grade_limit[$row['sg_no']][$key];
        		$view_limit[$key] = $view_limit[$key] >= 99 ? 99 : $view_limit[$key];
            }
    		
        }
        
        return $view_limit;
        
 	}

	// 대행결제 결과 저장(메세지), 퓨쳐서프
	function updateAgencyPayResult($sb_no, $result_msg) {
		$this->db->where('sb_no', $sb_no);

		$data = array('sb_pay_result' => $result_msg);
		$this->db->update($this->tb['TermServiceBuy'], $data);
	}

	// 결제통계 데이터
	// $data : 2019, 2019-05, 2019-05-04
	function getSellStatistics($date, $tm_id='') {
		/*
		error_reporting(E_ALL);

ini_set("display_errors", 1);
*/

		if($tm_id) $this->db->where("sb_tm_id", $tm_id);
		if($date) $this->db->where("sb_paydate LIKE '".$date."%'");
		$this->db->where("sb_pay_status", 'Y');

		$ft_type = explode("-", $date);
		switch(count($ft_type)) {
			case '1': $format = '%Y'; $data_format = '%Y-%m'; $max = 12; break;
			case '2': $format = '%Y-%m'; $data_format = '%Y-%m-%d'; $max = date('t', strtotime($date."-01")); break;
			case '3': $format = '%Y-%m-%d'; $data_format = '%Y-%m-%d %H'; $max = 24; break;
		}

		$this->db->groupBy("date"); 

		$rows = $this->db->arrayBuilder()->get($this->tb['TermServiceBuy'], null,  "count(sb_no) as cnt, sum(sb_total_price) as price_total, DATE_FORMAT(sb_paydate, '".$data_format."') as date");
		foreach($rows as $row) {
			$tmp_rows[$row['date']]['cnt'] = $row['cnt'];
			$tmp_rows[$row['date']]['price_total'] = $row['price_total'];
		}

		for($i=1; $i<=$max; $i++) {
			$data[$date."-".sprintf('%02d', $i)]['cnt'] = $tmp_rows[$date."-".sprintf('%02d', $i)]['cnt'];
			$data[$date."-".sprintf('%02d', $i)]['price'] = $tmp_rows[$date."-".sprintf('%02d', $i)]['price_total'];
		}

		return $data;
	}


	// 메모등록
	function addRequestMemo() {
		// 메모데이터 입력
		$fields = " am_type = '".$_POST['type']."',
					am_idx = '".$_POST['idx']."',
					am_memo = '".$_POST['am_memo']."',
					am_regdate = NOW()";

		$sql .=  "INSERT INTO ".$this->tb['inauction']['_memo']." SET ".$fields;

		$this->db->query($sql);

	}

	// 메모삭제
	function deleteRequestMemo($am_no) {
		$this->db->query("DELETE FROM ".$this->tb['inauction']['_memo']." WHERE am_no='".$am_no."'");
	}

	// 메모번호 가져오기
	function getMemoNum($type, $idx) {
		$row = $this->db->fetchRow("SELECT count(*) as num FROM ".$this->tb['inauction']['_memo']." WHERE am_type='".$type."' AND am_idx='".$idx."'");
		return $row['num'];
	}


	function downloadServiceBuyExcel() {
		global $pageLimit;

		ini_set("memory_limit" , -1);

		$pageLimit = 9999999;
		$data = $this->getTermServiceBuyList();
		$list = $data['list'];

		// 엑셀파일 작성
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$spreadsheet->getActiveSheet()->getPageSetup()
				->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
		$spreadsheet->getActiveSheet()->getPageSetup()
				->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

		//$spreadsheet->getActiveSheet()->getPageSetup()->setFitToPage(true);

		$spreadsheet->getDefaultStyle()->getFont()->setName('맑은 고딕');

		// 이렇게 해줘야 페이지브레이크가 먹음
		$spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
		$spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);

		// zoom level
		$spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(85);
		$spreadsheet->getActiveSheet()->setShowGridlines(true);

		// 셀 헤더생성
		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue("A1", "번호")
			->setCellValue("B1", "신청자(아이디)")
			->setCellValue("C1", "연락처")
			->setCellValue("D1", "신청일")
			->setCellValue("E1", "결제일")
			->setCellValue("F1", "서비스")
			->setCellValue("G1", "서비스기간")
			->setCellValue("H1", "결제방법")
			->setCellValue("I1", "금액")
			->setCellValue("J1", "입금자명")
			->setCellValue("K1", "상태")
			->setCellValue("L1", "담당자")
			->setCellValue("M1", "소속팀");


		for($i=0; $i<count($list); $i++) {

			if($list[$i]['sb_pay_status'] == 'Y') {
				$status = "결제완료";
    		} else if($list[$i]['sb_pay_status'] == 'N') {
				$status = "결제실패";
    		} else {
				$status = "결제대기";
			}

			$spreadsheet->setActiveSheetIndex(0)
			->setCellValue("A".($i+2), $list[$i]['mb_no'])
			->setCellValue("B".($i+2), $list[$i]['mb_name']."(".$list[$i]['mb_id'].")")
			->setCellValue("C".($i+2), $list[$i]['mb_hp'])
			->setCellValue("D".($i+2), $list[$i]['sb_regdate'])
			->setCellValue("E".($i+2), $list[$i]['sb_paydate'])
			->setCellValue("F".($i+2), $list[$i]['sb_name'])
			->setCellValue("G".($i+2), $list[$i]['sb_term']." ".($list[$i]['sb_term_type'] == 'month' ? '개월' : '일'))
			->setCellValue("H".($i+2), $this->config['pay_method'][$list[$i]['sb_pay_method']])
			->setCellValue("I".($i+2), $list[$i]['sb_total_price'])
			->setCellValue("J".($i+2), $list[$i]['sb_pay_name'])
			->setCellValue("K".($i+2), $status)
			->setCellValue("L".($i+2), $list[$i]['sb_tm_id'])
			->setCellValue("M".($i+2), ($list[$i]['mg_name'] ? $list[$i]['mg_name'] : '미지정'));
		}

		$spreadsheet->setActiveSheetIndex(0);

		$filename = ($_GET['s_date'] || $_GET['e_date']) ? substr($_GET['s_date'], 0, 10)."_".substr($_GET['e_date'], 0, 10) : date('Y-m-d');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="'.iconv('UTF-8','CP949', $filename.'_결제정보.xls'). '"');
		header('Cache-Control: max-age=0');

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xls");
		$writer->save('php://output');

	}

}