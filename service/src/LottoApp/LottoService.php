<?php

namespace Acesoft\LottoApp;

use \Acesoft\Core\Base;
use \Acesoft\Core\DB;
use \Acesoft\LottoApp\LottoServiceConfig;
use \Acesoft\LottoApp\LottoWinRecords;
use \Acesoft\LottoApp\Lotto;
use \Acesoft\LottoApp\TermService;
use \Acesoft\LottoApp\Member\User;
use \Acesoft\Common\Utils;
use \Acesoft\Common\Message;
use \GuzzleHttp\Client;
use \Symfony\Component\DomCrawler\Crawler;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Writer\Xlsx;


/*
// set message
		$code = sprintf("AC%07d", $auction_row['id']);
		$title = "[미미옥션/".date('Y-m-d H:i')."] ".$code." 주문이 배송완료되었습니다.";
		$title = "[미미옥션/".date('Y-m-d H:i')."] ".$code." 경매에 낙찰되셧습니다. 구매절차를 진행해 주시기 바랍니다.";
		$content = "[".$code."]".$auction_row['name']."경매 낙찰을 축하드립니다.<br /> '마이페이지'에서 구매절차를 진행해 주시기 바랍니다.";
		$this->message->addMessage($winner_row['mb_id'], $title, $content, 'noty,email,sms');
*/

/*
3개월 이전 추출번호 삭제쿼리 2020-02-29 사용
delete  FROM `lotto_numbers` WHERE `le_datetime` < DATE_SUB(now(), INTERVAL 3 MONTH)

*/

class LottoService extends Base
{
	var $lottoServiceConfig;
	var $lotto;
	var $message;

	function __construct() {
    	parent::__construct();
		$this->db = DB::getInstance();

		$this->lottoServiceConfig = new LottoServiceConfig();
		$this->lotto = new Lotto();
		$this->message = new Message();
    }

	function getServiceUserList($page=1, $url='') {
		global $pageLimit;

        $pageLimit = $pageLimit ? $pageLimit : 20;

        $page = $page > 0 ? $page : 1;

		if($_GET['sc'] && $_GET['sv']) {
			$this->db->where($_GET['sc']." LIKE '%".$_GET['sv']."%'");
		}

		
		switch($_GET['s_service_use']) {
			case '1' : // 유료
				$this->db->where("IF(DATEDIFF(su_enddate, now()) > 0, DATEDIFF(su_enddate, now()), 0) > 0");
				break;
			case '2' : // 무료
				$this->db->where("IF(DATEDIFF(su_enddate, now()) > 0, DATEDIFF(su_enddate, now()), 0) = 0");
				break;
		}
		

		//▶ page rows
		$this->db->pageLimit = $pageLimit;

		//▶ order block
		$this->db->orderBy('su_no', 'ASC');

		$this->db->join($this->tb['TermServiceUse']." as b", "a.mb_id=b.mb_id", "LEFT");

		$list = $this->db->arraybuilder()->paginate($this->tb['Member']." as a", $page, "a.*, b.su_no, b.sg_no, b.su_enddate, IF(DATEDIFF(su_enddate, now()) > 0, DATEDIFF(su_enddate, now()), 0) as leftDays");

		$link = Utils::getPagination($this->db->totalPages, $page, $url);

		$data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
		$data['list'] = $list;
		$data['link'] = $link;
		$data['total_count'] = $this->db->totalCount;
		$data['total_pages'] = $this->db->totalPages;
		$data['page'] = $page;
		
		return $data;
	}

	function addNumber($numbers, $mb_id, $type='extractor') {
		// 관리자 추출설정 및 서비스설정
		$serviceConfig = $this->lottoServiceConfig->getConfig();

		// 다음회차
		$next_inning = $serviceConfig['lc_cur_inning']+1;

		$data = Array(
				'mb_id' => $mb_id,
				'le_inning' => $next_inning,
				'le_num1' => $numbers[0],
				'le_num2' => $numbers[1],
				'le_num3' => $numbers[2],
				'le_num4' => $numbers[3],
				'le_num5' => $numbers[4],
				'le_num6' => $numbers[5],
				'le_serial_num' => md5($numbers[0].$numbers[1].$numbers[2].$numbers[3].$numbers[4].$numbers[5]),
				'le_type' => $type,
				'le_datetime' => $this->db->NOW(),
				'le_issued_date' => $this->db->NOW()
			);

		// 사용자지정번호는 발송안함(바로발급)
		if($type != 'extractor') {
			$data['le_issued_date']= $this->db->NOW();
		}

		$this->db->insert($this->tb['LottoNumbers'], $data);

	}

	function updateNumbers($no, $numbers) {
		sort($numbers);
		$data = Array(
				
				'le_num1' => $numbers[0],
				'le_num2' => $numbers[1],
				'le_num3' => $numbers[2],
				'le_num4' => $numbers[3],
				'le_num5' => $numbers[4],
				'le_num6' => $numbers[5],
				'le_serial_num' => md5($numbers[0].$numbers[1].$numbers[2].$numbers[3].$numbers[4].$numbers[5])
				
			);

		$this->db->where('le_no', $no);
		$this->db->update($this->tb['LottoNumbers'], $data);

		// 당첨체크
		$this->db->where('le_no', $no);
		$row = $this->db->arrayBuilder()->getOne($this->tb['LottoNumbers']);

		// 당첨확인
		$this->checkResult($row['le_inning'], $no); 
	}

	function deleteNumbers($no) {
		// 추출데이터 삭제
		$this->db->where('le_no', $no);
		$this->db->delete($this->tb['LottoNumbers']);

		// 당첨데이터 삭제
		$this->db->where('le_no', $no);
		$this->db->delete($this->tb['LottoNumbersWin']);
		//echo $this->db->getLastQuery();

	}

	function addSelectedNumbersToQueue($mb_id, $nos) {

		$user = new User();
		$mb = $user->getUser($mb_id);

		$this->db->where('le_no', $nos, "IN");
		$list = $this->db->get($this->tb['LottoNumbers'], null, "*");

		// 등급코드별 등급
		$termService = new TermService();
		$grade_arr = $termService->getTermServiceGrade();

		//▶ 이번회차
		$serviceConfig = $this->lottoServiceConfig->getConfig();
		$this_inning = $serviceConfig['lc_cur_inning']+1;

		$title = "[".$this->site_conf['name']."/".$this_inning."] ".$mb['mb_id']."(".$mb['mb_name'].")님 ".$grade_arr[$mb['sg_no']]." 발급번호 ".count($nos)."개";
		//$content = "[".$this->site_conf['name']."/".$this_inning."]\n";
		$content = "안녕하세요 ".$mb['mb_name']."님 \n".$this_inning."회차 ".$grade_arr[$mb['sg_no']]." 발급번호 입니다.\n";
		for($i=0; $i<count($list); $i++) {
			$content .= ($i+1).") ".$list[$i]['le_num1']." ".$list[$i]['le_num2']." ".$list[$i]['le_num3']." ".$list[$i]['le_num4']." ".$list[$i]['le_num5']." ".$list[$i]['le_num6']."\n";

			$ids[] = $list[$i]['le_no'];
		}

		//$content .= "\n회원님에게 좋은결과를 보여드릴 수 있도록 노력하는 ".$this->site_conf['name']."가 되겠습니다.\n";
		$content .= "\n회원님에게 좋은결과를 보여드릴 수 있도록 노력하는 패밀리가 되겠습니다.\n";
		//$content .= "-".$this->site_conf['name']."-";
		$content .= "-패밀리-";

		// 해당번호 발급상태로 업데이트
		$this->db->where('le_no', $ids, 'IN');
		$this->db->update($this->tb['LottoNumbers'], ['le_issued_date' => $this->db->NOW(), 'le_send_sms' => '1']);

		// 메세지큐 등록
		$this->message->addMessage($mb_id, $title, $content, 'sms,noty', $serviceConfig['lc_numbers_sms_auto']);
		
	}

	function addNumbersToQueue($mb_id, $issue_count=0, $send_msg=1) {

		if($issue_count < 1) {
			return false;
		}

		$user = new User();
		$mb = $user->getUser($mb_id);

		// 등급코드별 등급
		$termService = new TermService();
		$grade_arr = $termService->getTermServiceGrade();

		//▶ 이번회차
		$serviceConfig = $this->lottoServiceConfig->getConfig();
		$this_inning = $serviceConfig['lc_cur_inning']+1;


		/// 추가 번호 더 추출 2018-10-29 //////////////////
		$this->extractNumbers('', '', $issue_count*10, $mb['sg_no']);
		///////////////////////////////////////////////

		$this->db->where('le_inning', $this_inning);
		$this->db->where('le_type', 'extractor');
		$this->db->where('mb_id', '');
		$this->db->where('sg_no', $mb['sg_no']);
		$this->db->where('le_issued_date', '0000-00-00 00:00:00');
		$this->db->orderBy('le_no');
		$list = $this->db->get($this->tb['LottoNumbers'], $issue_count);

		$title = "[".$this->site_conf['name']."/".$this_inning."] ".$mb['mb_id']."(".$mb['mb_name'].")님 ".$grade_arr[$mb['sg_no']]." 발급번호 ".$issue_count."개";


		//$content = "[".$this->site_conf['name']."/".$this_inning."]\n";
		$content = "안녕하세요 ".$list[$i]['mb_name']."님 \n".$this_inning."회차 ".$grade_arr[$mb['sg_no']]." 발급번호 입니다.\n";
		for($i=0; $i<count($list); $i++) {
			$content .= ($i+1).") ".$list[$i]['le_num1']." ".$list[$i]['le_num2']." ".$list[$i]['le_num3']." ".$list[$i]['le_num4']." ".$list[$i]['le_num5']." ".$list[$i]['le_num6']."\n";

			$ids[] = $list[$i]['le_no'];
		}

		//$content .= "\n회원님에게 좋은결과를 보여드릴 수 있도록 노력하는 ".$this->site_conf['name']."가 되겠습니다.\n";
		$content .= "\n회원님에게 좋은결과를 보여드릴 수 있도록 노력하는 패밀리가 되겠습니다.\n";
		//$content .= "-".$this->site_conf['name']."-";
		$content .= "-패밀리-";

		/// 해당번호 발급상태로 업데이트
		$this->db->where('le_no', $ids, 'IN');
		$this->db->update($this->tb['LottoNumbers'], ['mb_id' => $mb_id, 'le_issued_date' => $this->db->NOW(), 'le_send_sms' => $send_msg]);

		// 메세지큐 등록
		if($send_msg == '1') {
			$this->message->addMessage($mb_id, $title, $content, 'sms,noty', $serviceConfig['lc_numbers_sms_auto']);
		}
		
	}

	function extractNumbers($config='', $mb_id='', $count=1, $sg_no='0', $dup='0') {

		$lotto = new Lotto();

		// 관리자 추출설정 및 서비스설정
		$serviceConfig = $this->lottoServiceConfig->getConfig($sg_no);
		if(!$config) {
			$config = $serviceConfig;
		}

		$lotto->setConfig($config);

		// 다음회차
		$next_inning = $serviceConfig['lc_cur_inning']+1;


		$data = array();
		$idx = 0;
		//ob_implicit_flush(1);
		for($i=0; $i<$count; $i++) {
			// 추출
			$numbers = $lotto->generateNumbers();

			$data[$idx] = Array(
				'mb_id' => $mb_id,
				'sg_no' => $sg_no,
				'le_inning' => $next_inning,
				'le_num1' => $numbers[0],
				'le_num2' => $numbers[1],
				'le_num3' => $numbers[2],
				'le_num4' => $numbers[3],
				'le_num5' => $numbers[4],
				'le_num6' => $numbers[5],
				'le_serial_num' => md5($numbers[0].$numbers[1].$numbers[2].$numbers[3].$numbers[4].$numbers[5]),
				'le_type' => 'extractor',
				'le_datetime' => $this->db->NOW()
			);
			
			//echo $idx."<br />";
			

			if(($i > 0 &&$i % 5000 == 0) || ($i == $count-1)) {
			//	echo Utils::convertSize(memory_get_usage())."<br />";
				$this->db->insertMulti($this->tb['LottoNumbers'], $data);
				$cnt[$sg_no] += count($data);
			//	echo Utils::convertSize(memory_get_usage())."<br />";
			//	echo "a: ".$i."<br />";
				$data = array();
				$idx = 0;
			} else {
				$idx++;
			}
		}
/*
		if($dup == 0) {
			// 중복제거
			$this->db->rawQuery("DELETE t1 FROM lotto_numbers t1, lotto_numbers t2 WHERE t1.le_inning='".$next_inning."' AND t2.le_inning='".$next_inning."' AND le_type='extractor' AND (t1.le_no > t2.le_no AND t1.mb_id = '') AND  t1.le_serial_num = t2.le_serial_num");
			$deleted_rows = $this->db->count;
			//echo "deleted_rows: ".$deleted_rows."<br />";
			if($deleted_rows > 0) {
				$this->extractNumbers('', '', $deleted_rows, $sg_no, 1);
			}
		}
*/		
	}

	public function getNextWinNumbers($inning='') {

		//error_log('\ntt', 3, "/opt/apache/logs/error_log");	

		$win_data = $this->lotto->retriveWinData($inning);

		//error_log($win_data['basic']['drwNo'], 3, "/opt/apache/logs/error_log");
		//error_log($win_data['basic']['drwNo1'], 3, "/opt/apache/logs/error_log");
		//error_log($win_data['basic']['drwNo2'], 3, "/opt/apache/logs/error_log");
		//error_log($win_data['basic']['drwNo3'], 3, "/opt/apache/logs/error_log");
		//error_log($win_data['basic']['drwNo4'], 3, "/opt/apache/logs/error_log");
		//error_log($win_data['basic']['drwNo5'], 3, "/opt/apache/logs/error_log");
		//error_log($win_data['basic']['drwNo6'], 3, "/opt/apache/logs/error_log");
		//error_log($win_data['basic']['bnusNo'], 3, "/opt/apache/logs/error_log");


		if($win_data['basic']['drwNo'] && $win_data['basic']['drwtNo1'] && $win_data['basic']['drwtNo2'] && $win_data['basic']['drwtNo3'] && $win_data['basic']['drwtNo4'] && $win_data['basic']['drwtNo5'] && $win_data['basic']['drwtNo6'] && $win_data['basic']['bnusNo']) {
			$data = Array (
				'lw_inning' => $win_data['basic']['drwNo'],
				'lw_date' => $win_data['basic']['drwNoDate'],
				'lw_num1' => $win_data['basic']['drwtNo1'],
				'lw_num2' => $win_data['basic']['drwtNo2'],
				'lw_num3' => $win_data['basic']['drwtNo3'],
				'lw_num4' => $win_data['basic']['drwtNo4'],
				'lw_num5' => $win_data['basic']['drwtNo5'],
				'lw_num6' => $win_data['basic']['drwtNo6'],
				'lw_num7' =>  $win_data['basic']['bnusNo'],
				'lw_1st_count' =>  $win_data['detail']['win'][0][2],
				'lw_2nd_count' =>  $win_data['detail']['win'][1][2],
				'lw_3rd_count' =>  $win_data['detail']['win'][2][2],
				'lw_4th_count' =>  $win_data['detail']['win'][3][2],
				'lw_5th_count' =>  $win_data['detail']['win'][4][2],
				'lw_tot_prize' =>  $win_data['basic']['totSellamnt'],
				'lw_1st_prize_tot' =>  $win_data['detail']['win'][0][1],
				'lw_1st_prize_ea' =>  $win_data['detail']['win'][0][3],
				'lw_2nd_prize_tot' =>  $win_data['detail']['win'][1][1],
				'lw_2nd_prize_ea' =>  $win_data['detail']['win'][1][3],
				'lw_3rd_prize_tot' =>  $win_data['detail']['win'][2][1],
				'lw_3rd_prize_ea' =>  $win_data['detail']['win'][2][3],
				'lw_4th_prize_tot' =>  $win_data['detail']['win'][3][1],
				'lw_4th_prize_ea' =>  $win_data['detail']['win'][3][3],
				'lw_5th_prize_tot' =>  $win_data['detail']['win'][4][1],
				'lw_5th_prize_ea' =>  $win_data['detail']['win'][4][3],
				'lw_datetime' => $this->db->NOW()
			);

			//error_log('\nbb', 3, "/opt/apache/logs/error_log");

			$id = $this->db->insert($this->tb['LottoWinNumbers'], $data);

			// 현재 이닝 업데이트
			$this->lottoServiceConfig->updateInning($win_data['basic']['drwNo']);

			// 당첨결과 확인
			$this->checkResult($win_data['basic']['drwNo']);

			//$this->setWinNumbersZero($win_data['basic']['drwNo']);

		} else {
			$id = "fail";
		}

		return $id;
	}

	public function setWinNumbersZero($inning) {

		$lottoWinRecords = new LottoWinRecords();        
		
                $row = $lottoWinRecords->getWinRecord($inning);

                if($row['wr_inning']) {

                        $data = Array(
                                        'wr_1grade_num' => '0',
                                        'wr_2grade_num' => '0',
                                        'wr_3grade_num' => '0',
                                        'wr_4grade_num' => '0'
                                );

                        $this->db->where('wr_inning', $inning);
                        $this->db->update($this->tb['LottoWinRecords'], $data);

                        exit;
                }

                $data = Array(
                                'wr_inning' => $inning,
                                'wr_1grade_num' => '0',
                                'wr_2grade_num' => '0',
                                'wr_3grade_num' => '0',
                                'wr_4grade_num' => '0'
                        );


                $this->db->insert($this->tb['LottoWinRecords'], $data);

	}

	public function reCheckWinNumbers($inning, $no) {

		if($inning && $no) {
			$win_data = $this->lotto->getWinData($inning);

			if($win_data['basic']['drwNo'] && $win_data['basic']['drwtNo1'] && $win_data['basic']['drwtNo2'] && $win_data['basic']['drwtNo3'] && $win_data['basic']['drwtNo4'] && $win_data['basic']['drwtNo5'] && $win_data['basic']['drwtNo6'] && $win_data['basic']['bnusNo']) {
				$data = Array (
					'lw_inning' => $win_data['basic']['drwNo'],
					'lw_date' => $win_data['basic']['drwNoDate'],
					'lw_num1' => $win_data['basic']['drwtNo1'],
					'lw_num2' => $win_data['basic']['drwtNo2'],
					'lw_num3' => $win_data['basic']['drwtNo3'],
					'lw_num4' => $win_data['basic']['drwtNo4'],
					'lw_num5' => $win_data['basic']['drwtNo5'],
					'lw_num6' => $win_data['basic']['drwtNo6'],
					'lw_num7' =>  $win_data['basic']['bnusNo'],
					'lw_1st_count' =>  $win_data['detail']['win'][0][2],
					'lw_2nd_count' =>  $win_data['detail']['win'][1][2],
					'lw_3rd_count' =>  $win_data['detail']['win'][2][2],
					'lw_4th_count' =>  $win_data['detail']['win'][3][2],
					'lw_5th_count' =>  $win_data['detail']['win'][4][2],
					'lw_tot_prize' =>  $win_data['basic']['totSellamnt'],
					'lw_1st_prize_tot' =>  $win_data['detail']['win'][0][1],
					'lw_1st_prize_ea' =>  $win_data['detail']['win'][0][3],
					'lw_2nd_prize_tot' =>  $win_data['detail']['win'][1][1],
					'lw_2nd_prize_ea' =>  $win_data['detail']['win'][1][3],
					'lw_3rd_prize_tot' =>  $win_data['detail']['win'][2][1],
					'lw_3rd_prize_ea' =>  $win_data['detail']['win'][2][3],
					'lw_4th_prize_tot' =>  $win_data['detail']['win'][3][1],
					'lw_4th_prize_ea' =>  $win_data['detail']['win'][3][3],
					'lw_5th_prize_tot' =>  $win_data['detail']['win'][4][1],
					'lw_5th_prize_ea' =>  $win_data['detail']['win'][4][3],
					'lw_datetime' => $this->db->NOW()
				);

				$this->db->where('lw_no', $no);
				$this->db->update($this->tb['LottoWinNumbers'], $data);

				return true;
			}
		}

		return false;
	}

	public function modifyWinNumbers($numbers, $id) {

		if(is_array($numbers)) {
			$data = array(
						'lw_num1' => $numbers[0],
						'lw_num2' => $numbers[1],
						'lw_num3' => $numbers[2],
						'lw_num4' => $numbers[3],
						'lw_num5' => $numbers[4],
						'lw_num6' => $numbers[5],
						'lw_num7' => $numbers[6]
					);

			$this->db->where('lw_no', $id);
			$this->db->update($this->tb['LottoWinNumbers'], $data);

		}
	}

	// 당첨확인
	public function checkResult($inning='', $le_no='', $recheck=false) {

		//ini_set('memory_limit','512M');
		ini_set("memory_limit" , -1);

		$row_win = $this->getWinData($inning);

		$this->db->where("a.lwr_inning", $row_win['lw_inning']);
		$this->db->join($this->tb['TermServiceUse']." as b", "a.mb_id = b.mb_id", "LEFT");
		$row = $this->db->arrayBuilder()->getOne($this->tb['LottoNumbersWin']." as a", "count(a.lwr_no) as cnt");
		//$row = $this->db->arrayBuilder()->getOne($this->tb['LottoNumbersWin'], "count(lwr_no) as cnt");
		
		//error_log('\ntest', 3, "/opt/apache/logs/error_log");
		//error_log($recheck, 3, "/opt/apache/logs/error_log");
		//error_log($row_win['lw_inning'], 3, "/opt/apache/logs/error_log");
		//error_log($row['cnt'], 3, "/opt/apache/logs/error_log");
		//error_log($row_win['lw_num1'], 3, "/opt/apache/logs/error_log");


		// 기존체크값이 없거나 강제 재확인시
		if($row_win['lw_inning'] && ($row['cnt'] == 0 || $le_no != '' || $recheck === true)) {

			$columns .= "( (IF(le_num1 = ".$row_win['lw_num1'].", 1, 0) +
						IF(le_num2 = ".$row_win['lw_num1'].", 1, 0) +
						IF(le_num3 = ".$row_win['lw_num1'].", 1, 0) +
						IF(le_num4 = ".$row_win['lw_num1'].", 1, 0) +
						IF(le_num5 = ".$row_win['lw_num1'].", 1, 0) +
						IF(le_num6 = ".$row_win['lw_num1'].", 1, 0) ) + ";

			$columns .= "(IF(le_num1 = ".$row_win['lw_num2'].", 1, 0) +
						IF(le_num2 = ".$row_win['lw_num2'].", 1, 0) +
						IF(le_num3 = ".$row_win['lw_num2'].", 1, 0) +
						IF(le_num4 = ".$row_win['lw_num2'].", 1, 0) +
						IF(le_num5 = ".$row_win['lw_num2'].", 1, 0) +
						IF(le_num6 = ".$row_win['lw_num2'].", 1, 0) ) + ";

			$columns .= "(IF(le_num1 = ".$row_win['lw_num3'].", 1, 0) +
						IF(le_num2 = ".$row_win['lw_num3'].", 1, 0) +
						IF(le_num3 = ".$row_win['lw_num3'].", 1, 0) +
						IF(le_num4 = ".$row_win['lw_num3'].", 1, 0) +
						IF(le_num5 = ".$row_win['lw_num3'].", 1, 0) +
						IF(le_num6 = ".$row_win['lw_num3'].", 1, 0) ) + ";

			$columns .= "(IF(le_num1 = ".$row_win['lw_num4'].", 1, 0) +
						IF(le_num2 = ".$row_win['lw_num4'].", 1, 0) +
						IF(le_num3 = ".$row_win['lw_num4'].", 1, 0) +
						IF(le_num4 = ".$row_win['lw_num4'].", 1, 0) +
						IF(le_num5 = ".$row_win['lw_num4'].", 1, 0) +
						IF(le_num6 = ".$row_win['lw_num4'].", 1, 0) ) + ";

			$columns .= "(IF(le_num1 = ".$row_win['lw_num5'].", 1, 0) +
						IF(le_num2 = ".$row_win['lw_num5'].", 1, 0) +
						IF(le_num3 = ".$row_win['lw_num5'].", 1, 0) +
						IF(le_num4 = ".$row_win['lw_num5'].", 1, 0) +
						IF(le_num5 = ".$row_win['lw_num5'].", 1, 0) +
						IF(le_num6 = ".$row_win['lw_num5'].", 1, 0) ) + ";

			$columns .= "(IF(le_num1 = ".$row_win['lw_num6'].", 1, 0) +
						IF(le_num2 = ".$row_win['lw_num6'].", 1, 0) +
						IF(le_num3 = ".$row_win['lw_num6'].", 1, 0) +
						IF(le_num4 = ".$row_win['lw_num6'].", 1, 0) +
						IF(le_num5 = ".$row_win['lw_num6'].", 1, 0) +
						IF(le_num6 = ".$row_win['lw_num6'].", 1, 0) ) ) as tot_sum, ";

			$columns .= "(IF(le_num1 = ".$row_win['lw_num7'].", 1, 0) +
						IF(le_num2 = ".$row_win['lw_num7'].", 1, 0) +
						IF(le_num3 = ".$row_win['lw_num7'].", 1, 0) +
						IF(le_num4 = ".$row_win['lw_num7'].", 1, 0) +
						IF(le_num5 = ".$row_win['lw_num7'].", 1, 0) +
						IF(le_num6 = ".$row_win['lw_num7'].", 1, 0) )  as bonus ";

			$this->db->having("tot_sum > 2");

			if($le_no) $this->db->where("le_no", $le_no);
			$this->db->where('le_inning', $row_win['lw_inning']);
			$row_matches = $this->db->arrayBuilder()->get($this->tb['LottoNumbers'], null, "*, $columns");


			foreach($row_matches as $row) {
				$cur_grade = '';
				switch($row['tot_sum']) {
					
					case '3' : // 5등
						$result['5'][] = $row;
						$result_ids['5'][] = $row['le_no'];
						$cur_grade = 5;
						break;
					case '4' : // 4등
						$result['4'][] = $row;
						$result_ids['4'][] = $row['le_no'];
						$cur_grade = 4;
						break;
					case '5' : // 3, 2등
						if($row['bonus'] == 1) {
							$result['2'][] = $row;
							$result_ids['2'][] = $row['le_no'];
							$cur_grade = 2;
						} else {
							$result['3'][] = $row;
							$result_ids['3'][] = $row['le_no'];
							$cur_grade = 3;
						}
						break;
					case '6' : // 1등
						$result['1'][] = $row;
						$result_ids['1'][] = $row['le_no'];
						$cur_grade = 1;
						break;
				}

				$result_data[] = array(
										'le_no' => $row['le_no'],
										'mb_id' => $row['mb_id'],
										'lwr_grade' => $cur_grade,
										'lwr_inning' => $row['le_inning'],
										'lwr_num1' => $row['le_num1'],
										'lwr_num2' => $row['le_num2'],
										'lwr_num3' => $row['le_num3'],
										'lwr_num4' => $row['le_num4'],
										'lwr_num5' => $row['le_num5'],
										'lwr_num6' => $row['le_num6'],
										'lwr_datetime' => $this->db->NOW()
										
								);

				$result_data_sgno[] = array( 'sg_no' => $row['sg_no'] );

			}

			//error_log(count($result_data_sgno)." ", 3, "/opt/apache/logs/error_log");
			
			if(is_array($result_data)) {

				//error_log(" t ", 3, "/opt/apache/logs/error_log");

				// 기존결과 삭제(재확인의 경우, 단일번호 업데이트시 제외)
				if($le_no == '') {
					$this->db->where("lwr_inning", $row_win['lw_inning']);
					$this->db->delete($this->tb['LottoNumbersWin']);
				} else { // 단일번호 기존등록삭제
					$this->db->where("le_no", $le_no);
					$this->db->where("lwr_inning", $row_win['lw_inning']);
					$this->db->delete($this->tb['LottoNumbersWin']);
				}

				// 결과 입력
				$this->db->insertMulti($this->tb['LottoNumbersWin'], $result_data);


				// 메세지큐 당첨메세지 등록
				//if(count($result_data) == 1 || ($le_no == '' && $recheck === false)) { // 강제재확인 및 번호업데이트가 아닌 최초 확인시에만
				if(($le_no == '' && $recheck === true)) { // 강제재확인 및 번호업데이트가 아닌 최초 확인시에만

					$user = new User();
					$serviceConfig = $this->lottoServiceConfig->getConfig();
					$winner_grades_to_send = explode(',', $serviceConfig['lc_send_win_result']);
					for($i=0; $i<count($result_data); $i++) {

						//if($result_data_sgno[$i]['sg_no']!=0) error_log($result_data_sgno[$i]['sg_no']." ", 3, "/opt/apache/logs/error_log");
						//continue;

						if($result_data[$i]['mb_id']) {
							$mb = $user->getUser($result_data[$i]['mb_id']);

							// 메세지큐 등록
							if(in_array($result_data[$i]['lwr_grade'], $winner_grades_to_send)) {
								$message_type = "sms,noty";
							} else {
								$message_type = "noty";
							}

							$title = "[".$this->site_conf['name']."] ".$mb['mb_name']."회원님 ".$result_data[$i]['lwr_inning']."회차 ".$result_data[$i]['lwr_grade']."등 당첨 축하드립니다.";
							
							$content = "";
							if ($result_data_sgno[$i]['sg_no']==0) $content .= "(광고) ";
							$content .= $this->site_conf['name']."\n";
							$content .= $mb['mb_name']."회원님 ".$result_data[$i]['lwr_inning']."회차 ".$result_data[$i]['lwr_grade']."등 당첨 축하드립니다.";
							if ($result_data_sgno[$i]['sg_no']==0) $content .= "\n\n무료수신거부\n0808782799";
							$this->message->addMessage($result_data[$i]['mb_id'], $title, $content, $message_type, $serviceConfig['lc_winner_sms_auto']);
						}
					}
				}

				// 추출원본데이터 업데이트(당첨 등수업데이트)
				$this->db->rawQuery("UPDATE ".$this->tb['LottoNumbers']." as dest, (SELECT * FROM ".$this->tb['LottoNumbersWin']." WHERE lwr_inning='".$row_win['lw_inning']."') as src SET dest.le_result_grade=src.lwr_grade WHERE dest.le_no=src.le_no");
			} else {
				if($le_no) {

					// 당첨결과목록에서 삭제
					$this->db->where("le_no", $le_no);
					$this->db->where("lwr_inning", $row_win['lw_inning']);
					$this->db->delete($this->tb['LottoNumbersWin']);

					// 당첨결과 초기화
					$this->db->rawQuery("UPDATE ".$this->tb['LottoNumbers']." SET le_result_grade='0' WHERE le_no='".$le_no."'");

				}
			}
		}
	}

	function getWinResult($inning) {

		$win_data = $this->getWinData($inning);

		$this->db->where("lwr_inning", $inning);
		$this->db->groupBy("lwr_grade");
		$this->db->orderBy("lwr_grade", "ASC");
		
		$data = $this->db->arrayBuilder()->get($this->tb['LottoNumbersWin'], null,"lwr_grade, COUNT(lwr_no) as cnt");

		$array_prize_idx = array(1 => "lw_1st_prize_ea", 2 => "lw_2nd_prize_ea", 3 => "lw_3rd_prize_ea", 4 => "lw_4th_prize_ea", 5 => "lw_5th_prize_ea");
		foreach($data as $key => $value) {
			$data_tmp[$value['lwr_grade']]['cnt'] = $value['cnt'];
			$data_tmp[$value['lwr_grade']]['prize_tot'] = $win_data[$array_prize_idx[$value['lwr_grade']]] * $value['cnt'];
		}
		
		return $data_tmp;
	}

	// 회차별 당첨리스트
	function getWinResultList($page=1, $url='') {
		global $pageLimit;

        $pageLimit = $pageLimit ? $pageLimit : 20;

        $page = $page > 0 ? $page : 1;

		if($_GET['s_inning']) {
			$this->db->where("lwr_inning", $_GET['s_inning']);
		}

		if($_GET['mb_name']) {
			$this->db->where("mb_name LIKE '%".$_GET['mb_name']."%'");
		}

		if($_GET['mb_id']) {
			$this->db->where("a.mb_id ='".$_GET['mb_id']."'");
		}

		//▶ page rows
		$this->db->pageLimit = $pageLimit;

		//▶ order block
		$this->db->orderBy('a.mb_id', 'DESC');
		$this->db->orderBy('lwr_grade', 'ASC');

		$this->db->join($this->tb['LottoWinNumbers']." as b ", "a.lwr_inning=b.lw_inning", "LEFT");
		$this->db->join($this->tb['LottoNumbers']." as c ", "a.le_no=c.le_no", "LEFT");
		$this->db->join($this->tb['Member']." as d ", "a.mb_id=d.mb_id", "LEFT");
		$list = $this->db->arraybuilder()->paginate($this->tb['LottoNumbersWin']." as a", $page, "a.*, b.*, c.le_type, d.mb_tm_id, d.mb_hp");

		$link = Utils::getPagination($this->db->totalPages, $page, $url);

		$data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
		$data['list'] = $list;
		$data['link'] = $link;
		$data['total_count'] = $this->db->totalCount;
		$data['total_pages'] = $this->db->totalPages;
		$data['page'] = $page;
		
		return $data;
	}

	// 회차별 발급번호 및 당첨정보
	function getIssuedNumbersInfotList($page=1, $url='') {
		global $pageLimit;

        $pageLimit = $pageLimit ? $pageLimit : 20;

        $page = $page > 0 ? $page : 1;

		if($_GET['s_inning']) {
			$this->db->where("le_inning", $_GET['s_inning']);
		}

		if($_GET['mb_name']) {
			$this->db->where("mb_name LIKE '%".$_GET['mb_name']."%'");
		}

		if($_GET['s_mb_id']) {
			$this->db->where("a.mb_id ='".$_GET['s_mb_id']."'");
		}

		//▶ page rows
		$this->db->pageLimit = $pageLimit;

		//▶ order block
		$this->db->orderBy('a.mb_id', 'DESC');
		$this->db->orderBy('le_result_grade = 0', 'ASC');
		$this->db->orderBy('le_result_grade', 'ASC');

		$this->db->join($this->tb['LottoWinNumbers']." as b ", "a.le_inning=b.lw_inning", "LEFT");
		$list = $this->db->arraybuilder()->paginate($this->tb['LottoNumbers']." as a", $page, "a.*, b.*, a.le_type");

		$link = Utils::getPagination($this->db->totalPages, $page, $url);

		$data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
		$data['list'] = $list;
		$data['link'] = $link;
		$data['total_count'] = $this->db->totalCount;
		$data['total_pages'] = $this->db->totalPages;
		$data['page'] = $page;
		
		return $data;
	}

	public function getWinResultInningGroups() {
		$this->db->groupBy('lwr_inning');
		$this->db->orderBy('lwr_inning', 'desc');
		$list = $this->db->arraybuilder()->get($this->tb['LottoNumbersWin'], null, "lwr_inning as inning");

		return $list;

	}


	function getWinData($inning='') {
		$serviceConfig = $this->lottoServiceConfig->getConfig();
		$inning = $inning ? $inning : $serviceConfig['lc_cur_inning'];
		$this->db->where('lw_inning', $inning);
		$row_win = $this->db->arrayBuilder()->getOne($this->tb['LottoWinNumbers']);

		return $row_win;
	}

	public function addFilter($type, $data) 
	{
		$this->filters[$type] = $data;
	}

	public function getExtractNumberInningGroups() {
		$this->db->groupBy('le_inning');
		$this->db->orderBy('le_inning', 'desc');
		$list = $this->db->arraybuilder()->get($this->tb['LottoNumbers'], null, "le_inning as inning");

		return $list;

	}

	public function getExtractNumberList($page=1, $url='', $pageLimit=20) {

        $page = $page > 0 ? $page : 1;

		if($_GET['s_inning']) {
			$this->db->where("le_inning", $_GET['s_inning']);
		}

		if($_GET['not_issued']) {
			$this->db->where("mb_id", "");
		}

		switch($_GET['s_issued']) {
			case 'issued' : $this->db->where("a.mb_id", "", "<>"); break;
			case 'not_issued' : $this->db->where("a.mb_id", ""); break;
			
		}

		if($_GET['s_result'] != '') {
			$this->db->where('le_result_grade', $_GET['s_result']);
		}

		if($_GET['type']) {
			$this->db->where('le_type', $_GET['type']);
		}

		if($_GET['s_mb_id']) {
			$this->db->where("a.mb_id", $_GET['s_mb_id']);
		}

		if($_GET['sc'] && $_GET['sv']) {
			switch($_GET['sc']) {

				case 'mb_id' :
					$this->db->where("a.mb_id LIKE '%". $_GET['sv']."%'");
					break;
				case 'mb_name' :
					$this->db->where("b.mb_name LIKE '%". $_GET['sv']."%'");
					break;
				case 'mb_hp' :
					$this->db->where("REPLACE(b.mb_hp,'-','')", '%'.str_replace('-','',$_GET['sv']).'%', 'like');
					//$this->db->where("b.mb_hp LIKE '%". $_GET['sv']."%'");
					break;
			}
            
        }

		if($_GET['s_sg_no'] != '') {
			$this->db->where('a.sg_no', $this->db->escape($_GET['s_sg_no']));
		}

		//▶ page rows
		$this->db->pageLimit = $pageLimit;

		//▶ order block
		$this->db->orderBy('le_no', 'DESC');

		$this->db->join($this->tb['Member']." as b ", "a.mb_id=b.mb_id", "LEFT");

		$this->db->join($this->tb['TermServiceUse']." as c ", "a.mb_id=c.mb_id AND su_enddate >= now()", "LEFT");


		//"SELECT * FROM ".$this->tb['TermServiceUse']." as a left join ".$this->tb['TermServiceGrade']." as b on( a.sg_no=b.sg_no) WHERE mb_id='".$mb_id."' AND su_enddate >= now() ORDER BY su_enddate DESC"

		$list = $this->db->arraybuilder()->paginate($this->tb['LottoNumbers']." as a ", $page, "a.*, a.sg_no, c.su_enddate, b.mb_name, b.mb_today_login, IF(a.le_issued_date > b.mb_today_login, 0, 1) as varified");


		$link = Utils::getPagination($this->db->totalPages, $page, $url);

		$data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
		$data['list'] = $list;
		$data['link'] = $link;
		$data['total_count'] = $this->db->totalCount;
		$data['total_pages'] = $this->db->totalPages;
		$data['page'] = $page;
		
		return $data;
	
	}

	public function getExtractNumbers($page=1, $url='', $pageLimit=20) {

        $page = $page > 0 ? $page : 1;

		if($_GET['s_inning']) {
			$this->db->where("le_inning", $_GET['s_inning']);
		}

		if($_GET['not_issued']) {
			$this->db->where("mb_id", "");
		}

		if($_GET['type']) {
			$this->db->where('le_type', $_GET['type']);
		}

		if(isset($_GET['s_sg_no']) && !empty($_GET['s_sg_no'])) {
			$this->db->where('sg_no', $_GET['s_sg_no']);
		} else { echo "aa";
			$this->db->where('sg_no', '0');
		}

		if($_GET['s_mb_id']) {
			$this->db->where("mb_id", $_GET['s_mb_id']);
		}

		//▶ page rows
		$this->db->pageLimit = $pageLimit;

		//▶ order block
		$this->db->orderBy('le_datetime', 'DESC');

		$list = $this->db->arraybuilder()->paginate($this->tb['LottoNumbers'], $page, "*");

		$link = Utils::getPagination($this->db->totalPages, $page, $url);

		$data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
		$data['list'] = $list;
		$data['link'] = $link;
		$data['total_count'] = $this->db->totalCount;
		$data['total_pages'] = $this->db->totalPages;
		$data['page'] = $page;
		
		return $data;
	
	}

	public function getWinNumberInningGroups() {
		$this->db->groupBy('lw_inning');
		$this->db->orderBy('lw_inning', 'desc');
		$list = $this->db->arraybuilder()->get($this->tb['LottoWinNumbers'], null, "lw_inning as inning");

		return $list;

	}

	public function getWinNumberList($page=1, $url='') {
		global $pageLimit;

        $pageLimit = $pageLimit ? $pageLimit : 20;

        $page = $page > 0 ? $page : 1;

		if($_GET['s_inning']) {
			$this->db->where("lw_inning", $_GET['s_inning']);
		}

		if($_GET['s_from_inning']) {
			$this->db->where("lw_inning <= '".$_GET['s_from_inning']."'");
		}

		if($_GET['mb_name']) {
			$this->db->where("mb_name LIKE '%".$_GET['mb_name']."%'");
		}

		//▶ page rows
		$this->db->pageLimit = $pageLimit;

		//▶ order block
		$this->db->orderBy('lw_inning', 'DESC');

		$list = $this->db->arraybuilder()->paginate($this->tb['LottoWinNumbers'], $page, "*");

		$link = Utils::getPagination($this->db->totalPages, $page, $url);

		$data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
		$data['list'] = $list;
		$data['link'] = $link;
		$data['total_count'] = $this->db->totalCount;
		$data['total_pages'] = $this->db->totalPages;
		$data['page'] = $page;
		
		return $data;
	
	}

	function getSmsHistoryList($page=1, $url='') {

        global $pageLimit;

        $pageLimit = $pageLimit ? $pageLimit : 20;

        $page = $page > 0 ? $page : 1;

		if(isset($_GET['mb_id'])) {
			$this->db->where("mb_id", $_GET['mb_id']); 
		}

		if($_GET['mb_name']) {
			$this->db->where("mb_name LIKE '%".$_GET['mb_name']."%'");
		}

		//▶ page rows
		$this->db->pageLimit = $pageLimit;

		//▶ order block
		$this->db->orderBy('hs_datetime', 'DESC');

		$this->db->join($this->tb['SMSWrite']." as b ", "a.wr_no=b.wr_no", "LEFT");

        $list = $this->db->arraybuilder()->paginate($this->tb['SMSHistory']." as a", $page, "*");


        $link = Utils::getPagination($this->db->totalPages, $page, $url);

		$data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
        $data['list'] = $list;
        $data['link'] = $link;
		$data['total_count'] = $this->db->totalCount;
		$data['total_pages'] = $this->db->totalPages;
		$data['page'] = $page;
        return $data;
	}

	// 회원에게 지정된 갯수만큼 발급
	function setNumbersToUserOld() {

		ini_set("memory_limit" , -1);

		// 1. 회원별 발급갯수 조회
		// 관리자 추출설정 및 서비스설정
		$serviceConfig = $this->lottoServiceConfig->getConfig();

		//▶ 등급별 발급설정갯수
		$extract_count_per_grade = unserialize($serviceConfig['lc_extract_count']);

		//▶ 이번회차
		$this_inning = $serviceConfig['lc_cur_inning']+1;

		//▶ 발송요일
		$cur_weekday = date('w');

		// 회원정보 조회
		if($cur_weekday == $serviceConfig['lc_send_weekdays']) { // 지정요일 미지정 유료회원 발급
			$this->db->where("(a.mb_extract_weekday='".$cur_weekday."' OR (DATEDIFF(su_enddate, now()) > 0 AND a.mb_extract_weekday='') )");
		} else {
			$this->db->where("(a.mb_extract_weekday='".$cur_weekday."')");
		}

		$this->db->where(" (b.su_startdate IS NULL OR DATE_FORMAT(b.su_startdate,'%Y-%m-%d') <> DATE_FORMAT(NOW(),'%Y-%m-%d')) "); // 당일등록 서비스 제외
		$this->db->where("b.su_pausedate IS NULL"); // 서비스 정지제외
		$this->db->where("a.mb_status = '1'"); // 이용중

		//$this->db->where("a.mb_sms='1'");
		$this->db->join($this->tb['TermServiceUse']." as b", "a.mb_id=b.mb_id", "LEFT");
		$list = $this->db->arraybuilder()->get($this->tb['Member']." as a ", $page, "a.*, b.su_no, b.sg_no, b.su_enddate, b.su_pausedate, b.su_startdate, 
				IF(DATEDIFF(su_enddate, now()) > 0, DATEDIFF(su_enddate, now()), 0) as leftDays, 
				(select count(*) FROM ".$this->tb['LottoNumbers']." as c WHERE c.mb_id=a.mb_id AND le_inning='".$this_inning."' AND le_type='extractor') as issued_count ");

		$total_count = 0;
		$tmp_count = 0;
		for($i=0; $i<count($list); $i++) {
			
			if($list[$i]['sg_no'] && $list[$i]['leftDays'] > 0) {
				// 유료회원
				// 회원별 지정된 발급수가 있으면 지정발급수로 발급
				if($list[$i]['mb_extract_per_week'] > 0) {
					$tmp_count = $list[$i]['mb_extract_per_week'] - $list[$i]['issued_count'];
				} else {
					$tmp_count = $extract_count_per_grade[$list[$i]['sg_no']] - $list[$i]['issued_count'];
				}
			} else {
				// 무료회원
				if($list[$i]['mb_extract_per_week'] > 0) {
					$tmp_count = $list[$i]['mb_extract_per_week'] - $list[$i]['issued_count'];
				} else {
					$tmp_count = $extract_count_per_grade[0] - $list[$i]['issued_count'];
				}
			}

			$total_count += $tmp_count > 0 ? $tmp_count : 0;
		}

		// 미발급 번호 여분조회
		$pageLimit = $total_count + 100;
		$_GET['s_inning'] = $this_inning;
		$_GET['not_issued'] = true;	// 미발급번호만
		$_GET['type'] = 'extractor';	// 추출기 추출번호만(사용자 등록번호 제외)
		$list_number_not_issued = $this->getExtractNumbers('','', $pageLimit);


		// 여분 부족시 추가 추출
		if($pageLimit > count($list_number_not_issued['list'])) {
			$tmp_cnt = $pageLimit - count($list_number_not_issued['list']);

			//부족한 번호 더 추출
			$this->extractNumbers('', '', $tmp_cnt); //
		}

		// 2. 지정갯수별 발급
		{
			// 전체갯수만큼 갖고오기
			$pageLimit = $total_count + 50;
			$_GET['not_issued'] = true;	// 미발급번호만
			$_GET['type'] = 'extractor';	// 추출기 추출번호만(사용자 등록번호 제외)
			$numbers_list_tmp = $this->getExtractNumbers('','', $pageLimit);
			$list_number_not_issued = $numbers_list_tmp['list'];

			for($i=0; $i<count($list_number_not_issued); $i++) {
				$numbers[$i]['id'] = $list_number_not_issued[$i]['le_no'];
				$numbers[$i]['num'] = $list_number_not_issued[$i]['le_num1'].",".$list_number_not_issued[$i]['le_num2'].",".$list_number_not_issued[$i]['le_num3'].",".$list_number_not_issued[$i]['le_num4'].",".$list_number_not_issued[$i]['le_num5'].",".$list_number_not_issued[$i]['le_num6'];
			}

			// 2-1. 회원별 발급번호 message queue에 등록 - 지정weekday에 해당하는 일시등록(지정일)
			for($i=0; $i<count($list); $i++) {

				//if(!$list[$i]['sg_no']) $list[$i]['sg_no'] = 0; // 일반
				// 지정갯수 가져오기
				//$issue_count = $extract_count_per_grade[$list[$i]['sg_no']] - $list[$i]['issued_count'];


				if($list[$i]['sg_no'] && $list[$i]['leftDays'] > 0) {
					// 유료회원
					// 회원별 지정된 발급수가 있으면 지정발급수로 발급
					if($list[$i]['mb_extract_per_week'] > 0) {
						$issue_count = $list[$i]['mb_extract_per_week'] - $list[$i]['issued_count'];
					} else {
						$issue_count = $extract_count_per_grade[$list[$i]['sg_no']] - $list[$i]['issued_count'];
					}
				} else {
					// 무료회원
					if($list[$i]['mb_extract_per_week'] > 0) {
						
						$issue_count = $list[$i]['mb_extract_per_week'] - $list[$i]['issued_count'];
					} else {
						$issue_count = $extract_count_per_grade[0] - $list[$i]['issued_count'];
					}
				}

				// 유료회원 메세지생성
				$ids = array();
				$title = "[".$this->site_conf['name']."/".$this_inning."] ".$list[$i]['mb_id']."(".$list[$i]['mb_name'].")님 추출번호 ".$issue_count."개";
				//$title = "안녕하세요 ".$list[$i]['mb_id']."(".$list[$i]['mb_name'].")님 \n".$this_inning."회차 추출번호 입니다.";
				//$content = "[".$this->site_conf['name']."/".$this_inning."]\n";
				$content = "안녕하세요 ".$list[$i]['mb_name']."님 \n".$this_inning."회차 추출번호 입니다.\n";
				for($j=0; $j<$issue_count; $j++) {
					$list_number = array_pop($list_number_not_issued);

					$content .= ($j+1).") ".$list_number['le_num1']." ".$list_number['le_num2']." ".$list_number['le_num3']." ".$list_number['le_num4']." ".$list_number['le_num5']." ".$list_number['le_num6']."\n";

					$ids[] = $list_number['le_no'];
				}

				//$content .= "\n회원님에게 좋은결과를 보여드릴 수 있도록 노력하는 로또패밀리가 되겠습니다.\n";
				$content .= "\n회원님에게 좋은결과를 보여드릴 수 있도록 노력하는 패밀리가 되겠습니다.\n";
				//$content .= "-".$this->site_conf['name']."-";
				$content .= "-패밀리-";

				if($issue_count > 0) {
					$result['issued_member_count']++;
					$result['issued_count_total'] += count($ids);

					// 해당번호 발급상태로 업데이트
					$this->db->where('le_no', $ids, 'IN');
					$this->db->update($this->tb['LottoNumbers'], ['mb_id' => $list[$i]['mb_id'], 'le_issued_date' => $this->db->NOW(), 'le_send_sms' => $list[$i]['mb_sms']]);

					// 메세지큐 등록
					// SMS전송허용 회원만
					if($list[$i]['mb_sms'] == '1') {
						$msg_type = 'sms,noty';
					} else {
						$msg_type = 'noty';
					}
					
					$this->message->addMessage($list[$i]['mb_id'], $title, $content, $msg_type, $serviceConfig['lc_numbers_sms_auto']);
					
				}
			}
		}

		$result['member_total'] = count($list);
		return $result;
	}


	/////////////////////////////////////////////////////////////////////////////////
	// pre Extractor
	/////////////////////////////////////////////////////////////////////////////////
	// 회원에게 지정된 갯수만큼 미리 번호발급 ( setNumbersToUser 이전에 미리 발급해둠 )
	function extractTodayNumbers() {

		ini_set("memory_limit" , -1);

		// 1. 회원별 발급갯수 조회
		// 관리자 추출설정 및 서비스설정
		$serviceConfig = $this->lottoServiceConfig->getConfig();

		// 등급정보
		$termService = new TermService();

		//▶ 등급별 발급설정갯수
		$extract_count_per_grade = unserialize($serviceConfig['lc_extract_count']);

		// 등급코드별 등급
		$grade_arr = $termService->getTermServiceGrade();

		//▶ 이번회차
		$this_inning = $serviceConfig['lc_cur_inning']+1;

		//▶ 발송요일
		$cur_weekday = date('w');

		// 회원정보 조회
		if($cur_weekday == $serviceConfig['lc_send_weekdays']) { // 지정요일 미지정 유료회원 발급
			$this->db->where("(a.mb_extract_weekday='".$cur_weekday."' OR (DATEDIFF(su_enddate, now()) > 0 AND a.mb_extract_weekday='') )");
		} else {
			$this->db->where("(a.mb_extract_weekday='".$cur_weekday."')");
		}

		$this->db->where(" (b.su_startdate IS NULL OR DATE_FORMAT(b.su_startdate,'%Y-%m-%d') <> DATE_FORMAT(NOW(),'%Y-%m-%d')) "); // 당일등록 서비스 제외
		$this->db->where("b.su_pausedate IS NULL"); // 서비스 정지제외
		$this->db->where("a.mb_status = '1'"); // 이용중

		//$this->db->where("a.mb_sms='1'");
		$this->db->join($this->tb['TermServiceUse']." as b", "a.mb_id=b.mb_id", "LEFT");
		$list = $this->db->arraybuilder()->get($this->tb['Member']." as a ", $page, "a.*, b.su_no, IF(b.sg_no <> '', b.sg_no, 0) as sg_no, b.su_enddate, b.su_pausedate, b.su_startdate, 
				IF(DATEDIFF(su_enddate, now()) > 0, DATEDIFF(su_enddate, now()), 0) as leftDays, 
				(select count(*) FROM ".$this->tb['LottoNumbers']." as c WHERE c.mb_id=a.mb_id AND le_inning='".$this_inning."' AND le_type='extractor') as issued_count ");


		$total_count= array();
		$tmp_count = array();
		for($i=0; $i<count($list); $i++) {
			
			// 무료회원은 $list[$i]['sg_no'] 값이 '0'고정
			if($list[$i]['sg_no'] && $list[$i]['leftDays'] > 0) {
				// 유료회원
				// 회원별 지정된 발급수가 있으면 지정발급수로 발급
				if($list[$i]['mb_extract_per_week'] > 0) {
					$tmp_count[$list[$i]['sg_no']] = $list[$i]['mb_extract_per_week'] - $list[$i]['issued_count'];
				} else {
					$tmp_count[$list[$i]['sg_no']] = $extract_count_per_grade[$list[$i]['sg_no']] - $list[$i]['issued_count'];
				}
			} else {
				// 무료회원
				if($list[$i]['mb_extract_per_week'] > 0) {
					$tmp_count[$list[$i]['sg_no']] = $list[$i]['mb_extract_per_week'] - $list[$i]['issued_count'];
				} else {
					$tmp_count[$list[$i]['sg_no']] = $extract_count_per_grade[0] - $list[$i]['issued_count'];
				}
			}

			// 회원별 발급갯수 계산 및 저장
			$list[$i]['_to_issue_count'] = $tmp_count[$list[$i]['sg_no']] > 0 ? $tmp_count[$list[$i]['sg_no']] : 0;

			$total_count[$list[$i]['sg_no']] += $tmp_count[$list[$i]['sg_no']];
		}

		// 등급별 추출
		$extra = '';
		foreach($total_count as $sg_no => $num) {

			// 미발급 번호 여분조회
			$pageLimit = $num + 50;
			$_GET['s_inning'] = $this_inning;
			$_GET['not_issued'] = true;	// 미발급번호만
			$_GET['type'] = 'extractor';	// 추출기 추출번호만(사용자 등록번호 제외)
			$_GET['s_sg_no'] = $sg_no; // 해당 등급 번호만
			$list_number_not_issued = $this->getExtractNumbers('','', $pageLimit);

			// 여분 부족시 추가 추출
			if($pageLimit > count($list_number_not_issued['list'])) {
				$tmp_cnt = $pageLimit - count($list_number_not_issued['list']);

				$extra .= ($extra) ? ' / ['.$sg_no.']'.$tmp_cnt : '['.$sg_no.']'.$tmp_cnt;
				//부족한 번호 더 추출
				$this->extractNumbers('', '', $tmp_cnt, $sg_no);
			}
		}


		return $extra;
	}


	/////////////////////////////////////////////////////////////////////////////////
	// new Extractor
	/////////////////////////////////////////////////////////////////////////////////
	// 회원에게 지정된 갯수만큼 발급
	function setNumbersToUser() {

		ini_set("memory_limit" , -1);


		// 1. 회원별 발급갯수 조회
		// 관리자 추출설정 및 서비스설정
		$serviceConfig = $this->lottoServiceConfig->getConfig();

		// 등급정보
		$termService = new TermService();

		//▶ 등급별 발급설정갯수
		$extract_count_per_grade = unserialize($serviceConfig['lc_extract_count']);

		// 등급코드별 등급
		$grade_arr = $termService->getTermServiceGrade();

		//▶ 이번회차
		$this_inning = $serviceConfig['lc_cur_inning']+1;

		//▶ 발송요일
		$cur_weekday = date('w');

		// 회원정보 조회
		if($cur_weekday == $serviceConfig['lc_send_weekdays']) { // 지정요일 미지정 유료회원 발급
			$this->db->where("(a.mb_extract_weekday='".$cur_weekday."' OR (DATEDIFF(su_enddate, now()) > 0 AND a.mb_extract_weekday='') )");
		} else {
			$this->db->where("(a.mb_extract_weekday='".$cur_weekday."')");
		}

		$this->db->where(" (b.su_startdate IS NULL OR DATE_FORMAT(b.su_startdate,'%Y-%m-%d') <> DATE_FORMAT(NOW(),'%Y-%m-%d')) "); // 당일등록 서비스 제외
		$this->db->where("b.su_pausedate IS NULL"); // 서비스 정지제외
		$this->db->where("a.mb_status = '1'"); // 이용중
		$this->db->where("(b.sg_no > 0 or a.mb_datetime > '2022-01-01')"); // 튜닝적용 (전)

		//$this->db->where("a.mb_sms='1'");
		$this->db->join($this->tb['TermServiceUse']." as b", "a.mb_id=b.mb_id AND su_enddate > NOW()", "LEFT");
		
		//튜닝적용
		$this->db->join("(select mb_id, count(*) cnt from lotto_numbers where 1=1 and le_inning = '".$this_inning."' and le_type = 'extractor' group by mb_id) as c", "c.mb_id = a.mb_id", "LEFT");

		//원본
		// $list = $this->db->arraybuilder()->get($this->tb['Member']." as a ", $page, "a.*, b.su_no, IF(b.sg_no <> '', b.sg_no, 0) as sg_no, b.su_enddate, b.su_pausedate, b.su_startdate, 
		// 		IF(DATEDIFF(su_enddate, now()) > 0, DATEDIFF(su_enddate, now()), 0) as leftDays, 
		// 		(select count(*) FROM ".$this->tb['LottoNumbers']." as c WHERE c.mb_id=a.mb_id AND le_inning='".$this_inning."' AND le_type='extractor') as issued_count ");

		//튜닝적용
		$list = $this->db->arraybuilder()->get($this->tb['Member']." as a ", $page, "a.*, b.su_no, IF(b.sg_no <> '', b.sg_no, 0) as sg_no, b.su_enddate, b.su_pausedate, b.su_startdate, 
				IF(DATEDIFF(su_enddate, now()) > 0, DATEDIFF(su_enddate, now()), 0) as leftDays,
				c.cnt as issued_count");
		
		$total_count= array();
		$tmp_count = array();
		for($i=0; $i<count($list); $i++) {
			
			// 무료회원은 $list[$i]['sg_no'] 값이 '0'고정
			if($list[$i]['sg_no'] && $list[$i]['leftDays'] > 0) {
				// 유료회원
				// 회원별 지정된 발급수가 있으면 지정발급수로 발급
				if($list[$i]['mb_extract_per_week'] > 0) {
					$tmp_count[$list[$i]['sg_no']] = $list[$i]['mb_extract_per_week'] - $list[$i]['issued_count'];
				} else {
					$tmp_count[$list[$i]['sg_no']] = $extract_count_per_grade[$list[$i]['sg_no']] - $list[$i]['issued_count'];
				}
			} else {
				// 무료회원
				if($list[$i]['mb_extract_per_week'] > 0) {
					$tmp_count[$list[$i]['sg_no']] = $list[$i]['mb_extract_per_week'] - $list[$i]['issued_count'];
				} else {
					$tmp_count[$list[$i]['sg_no']] = $extract_count_per_grade[0] - $list[$i]['issued_count'];
				}
			}

			// 회원별 발급갯수 계산 및 저장
			$list[$i]['_to_issue_count'] = $tmp_count[$list[$i]['sg_no']] > 0 ? $tmp_count[$list[$i]['sg_no']] : 0;

			$total_count[$list[$i]['sg_no']] += $tmp_count[$list[$i]['sg_no']];
		}

		// 등급별 발송
		foreach($total_count as $sg_no => $num) {
			//DK 에러처리
			if($num <0 ){
				$num = 0;
			}

			// 미발급 번호 여분조회
			$pageLimit = $num + 50;
			$_GET['s_inning'] = $this_inning;
			$_GET['not_issued'] = true;	// 미발급번호만
			$_GET['type'] = 'extractor';	// 추출기 추출번호만(사용자 등록번호 제외)
			$_GET['s_sg_no'] = $sg_no; // 해당 등급 번호만
			$list_number_not_issued = $this->getExtractNumbers('','', $pageLimit);

//echo 's['.$sg_no.']'.$num.'<br />';

			// 여분 부족시 추가 추출
			if($pageLimit > count($list_number_not_issued['list'])) {
				$tmp_cnt = $pageLimit - count($list_number_not_issued['list']);

				//부족한 번호 더 추출
				$this->extractNumbers('', '', $tmp_cnt, $sg_no);

				$list_number_not_issued = $this->getExtractNumbers('','', $pageLimit, $sg_no);
			}

			$list_number_not_issued = $list_number_not_issued['list'];


			// 2-1. 회원별 발급번호 message queue에 등록 - 지정weekday에 해당하는 일시등록(지정일)
			for($i=0; $i<count($list); $i++) {
				if($list[$i]['sg_no'] == $sg_no) {

					// 발급갯수
					$issue_count = $list[$i]['_to_issue_count'];


					// 유료회원 메세지생성
					$ids = array();
					$title = "[".$this->site_conf['name']."/".$this_inning."] ".$list[$i]['mb_id']."(".$list[$i]['mb_name'].")님 ".$this_inning."회차 ".$grade_arr[$sg_no]." 발급번호 ".$issue_count."개";
					$content = "안녕하세요 ".$list[$i]['mb_name']."님 \n".$this_inning."회차 ".$grade_arr[$sg_no]." 발급번호 입니다.\n";

					for($j=0; $j<$issue_count; $j++) {
						$list_number = array_pop($list_number_not_issued);

						$content .= ($j+1).") ".$list_number['le_num1']." ".$list_number['le_num2']." ".$list_number['le_num3']." ".$list_number['le_num4']." ".$list_number['le_num5']." ".$list_number['le_num6']."\n";

						$ids[] = $list_number['le_no'];
					}

					//$content .= "\n회원님에게 좋은결과를 보여드릴 수 있도록 노력하는 ".$this->site_conf['name']."가 되겠습니다.\n";
					$content .= "\n회원님에게 좋은결과를 보여드릴 수 있도록 노력하는 패밀리가 되겠습니다.\n";
					//$content .= "-".$this->site_conf['name']."-";
					$content .= "-패밀리-";

					if($issue_count > 0) {
						$result['issued_member_count']++;
						$result['issued_count_total'] += count($ids);

						// 해당번호 발급상태로 업데이트
						$this->db->where('le_no', $ids, 'IN');
						$this->db->update($this->tb['LottoNumbers'], ['mb_id' => $list[$i]['mb_id'], 'le_issued_date' => $this->db->NOW(), 'le_send_sms' => $list[$i]['mb_sms']]);

						// 메세지큐 등록
						// SMS전송허용 회원만
						if($list[$i]['mb_sms'] == '1') {
							$msg_type = 'sms,noty';
						} else {
							$msg_type = 'noty';
						}
						
						$this->message->addMessage($list[$i]['mb_id'], $title, $content, $msg_type, $serviceConfig['lc_numbers_sms_auto']);
						
					}
				}
			}

		}

		$result['member_total'] = count($list);
		return $result;
	}




	// 관리자메인 통계정보
	function getIssuedStatistics() {
		switch($_GET['s_term']) {
			case 'd':
				$group_col = "DATE_FORMAT(le_issued_date, '%Y-%m-%d %H') as g_date";
				if(!$_GET['s_date']) {
					$this->db->where("DATE_FORMAT(le_issued_date, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')");
				} else {
					$this->db->where("DATE_FORMAT(le_issued_date, '%Y-%m-%d') = DATE_FORMAT('".$_GET['s_date']."', '%Y-%m-%d')");
				}
				break;
			case 'm':
				$group_col = "DATE_FORMAT(le_issued_date, '%Y-%m-%d') as g_date";
				if(!$_GET['s_date']) {
					$this->db->where("DATE_FORMAT(le_issued_date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')");
				} else {
					$this->db->where("DATE_FORMAT(le_issued_date, '%Y-%m') = DATE_FORMAT('".$_GET['s_date']."', '%Y-%m')");
				}
				break;
			
			case 'y':
				$group_col = "DATE_FORMAT(le_issued_date, '%Y-%m') as g_date";
				if(!$_GET['s_date']) {
					$this->db->where("DATE_FORMAT(le_issued_date, '%Y') = DATE_FORMAT(NOW(), '%Y')");
				} else {
					$this->db->where("DATE_FORMAT(le_issued_date, '%Y') = DATE_FORMAT('".$_GET['s_date']."', '%Y')");
				}
				break;
			
			default:
				$group_col = "DATE_FORMAT(le_issued_date, '%Y-%m-%d %H') as g_date";
				if(!$_GET['s_date']) {
					$this->db->where("DATE_FORMAT(le_issued_date, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')");
				} else {
					$this->db->where("DATE_FORMAT(le_issued_date, '%Y-%m-%d') = DATE_FORMAT('".$_GET['s_date']."', '%Y-%m-%d')");
				}
		}

		$col_pol = "DATE_FORMAT(le_issued_date, '%Y-%m-%d') as rdate";
		$this->db->groupBy("g_date");
		$row = $this->db->get($this->tb['LottoNumbers'], null, "$group_col, $col_pol,  count(le_no) as cnt");

		// 지난
		switch($_GET['s_term']) {
			case 'd':
				$group_col = "DATE_FORMAT(le_issued_date, '%Y-%m-%d %H') as g_date";
				
				if(!$_GET['s_date']) {
					$this->db->where("DATE_FORMAT(le_issued_date, '%Y-%m-%d') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 day), '%Y-%m-%d')");
				} else {
					$this->db->where("DATE_FORMAT(le_issued_date, '%Y-%m-%d') = DATE_FORMAT(DATE_SUB('".$_GET['s_date']."', INTERVAL 1 day), '%Y-%m-%d')");
				}
				break;
			
			case 'm':
				$group_col = "DATE_FORMAT(le_issued_date, '%Y-%m-%d') as g_date";
				
				if(!$_GET['s_date']) {
					$this->db->where("DATE_FORMAT(le_issued_date, '%Y-%m') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 month), '%Y-%m')");
				} else {
					$this->db->where("DATE_FORMAT(le_issued_date, '%Y-%m') = DATE_FORMAT(DATE_SUB('".$_GET['s_date']."', INTERVAL 1 month), '%Y-%m')");
				}
				break;
			
			case 'y':
				$group_col = "DATE_FORMAT(le_issued_date, '%Y-%m') as g_date";
				if(!$_GET['s_date']) {
					$this->db->where("DATE_FORMAT(le_issued_date, '%Y') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 year), '%Y')");
				} else {
					$this->db->where("DATE_FORMAT(le_issued_date, '%Y') = DATE_FORMAT(DATE_SUB('".$_GET['s_date']."', INTERVAL 1 year), '%Y')");
				}
				break;
			
			default:
				$group_col = "DATE_FORMAT(le_issued_date, '%Y-%m-%d %H') as g_date";
				
				if(!$_GET['s_date']) {
					$this->db->where("DATE_FORMAT(le_issued_date, '%Y-%m-%d') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 day), '%Y-%m-%d')");
				} else {
					$this->db->where("DATE_FORMAT(le_issued_date, '%Y-%m-%d') = DATE_FORMAT(DATE_SUB('".$_GET['s_date']."', INTERVAL 1 day), '%Y-%m-%d')");
				}
		}

		$col_pol = "DATE_FORMAT(le_issued_date, '%Y-%m-%d') as rdate";

		$this->db->groupBy("g_date");

		$row2 = $this->db->get($this->tb['LottoNumbers'], null, "$group_col, $col_pol,  count(le_no) as cnt");

		$data['cur'] = $row;
		$data['pre'] = $row2;


		return $data;
	}

	function getWinnerCount() {
		$this->db->where("lwr_grade", 1);
		$data = $this->db->arrayBuilder()->getOne($this->tb['LottoNumbersWin'], "count(lwr_no) as cnt");
		$result['1'] = $data['cnt'];
		$this->db->where("lwr_grade", 2);
		$data = $this->db->arrayBuilder()->getOne($this->tb['LottoNumbersWin'], "count(lwr_no) as cnt");
		$result['2'] = $data['cnt'];

		return $result;
	}

	function getAccumulatedWinRecords($mb_id='', $inning='') {

		ini_set('memory_limit','2212M');

		if($mb_id) {
			
			if($inning) $this->db->where("a.lwr_inning='".$inning."'");

			$this->db->where("a.mb_id ='".$mb_id."'");
			$this->db->join($this->tb['LottoWinNumbers']." as b ", "a.lwr_inning=b.lw_inning", "LEFT");
			$list = $this->db->arraybuilder()->get($this->tb['LottoNumbersWin']." as a", null, "a.*, b.*");

			$array_prize_idx = array(1 => "lw_1st_prize_ea", 2 => "lw_2nd_prize_ea", 3 => "lw_3rd_prize_ea", 4 => "lw_4th_prize_ea", 5 => "lw_5th_prize_ea");

			foreach($list as $row) {

				$result[$row['lwr_grade']]['cnt'] += 1;
				$result[$row['lwr_grade']]['prize'] += $row[$array_prize_idx[$row['lwr_grade']]];
			}

			return $result;

		}

		$row = $this->db->getOne($this->tb['LottoWinResult'], '*, yearweek(lr_updated_at, 3) as yw');

		if($row['yw'] < date("YW") && !$mb_id) {

			$this->db->join($this->tb['LottoWinNumbers']." as b ", "a.lwr_inning=b.lw_inning", "LEFT");
			$list = $this->db->arraybuilder()->get($this->tb['LottoNumbersWin']." as a", null, "a.*, b.*");

			$array_prize_idx = array(1 => "lw_1st_prize_ea", 2 => "lw_2nd_prize_ea", 3 => "lw_3rd_prize_ea", 4 => "lw_4th_prize_ea", 5 => "lw_5th_prize_ea");

			foreach($list as $row) {

				$result[$row['lwr_grade']]['cnt'] += 1;
				$result[$row['lwr_grade']]['prize'] += $row[$array_prize_idx[$row['lwr_grade']]];

				$data['lr_'.$row['lwr_grade'].'_count'] += 1;
				$data['lr_'.$row['lwr_grade'].'_prize'] = $row[$array_prize_idx[$row['lwr_grade']]];
			}

			$data['lr_updated_at'] = $this->db->NOW();

			$this->db->delete($this->tb['LottoWinResult']);

			$this->db->insert($this->tb['LottoWinResult'], $data);
			

		} else {
			$result['1']['cnt'] = $row['lr_1_count'];
			$result['1']['prize'] = $row['lr_1_prize'];
			$result['2']['cnt'] = $row['lr_2_count'];
			$result['2']['prize'] = $row['lr_2_prize'];
			$result['3']['cnt'] = $row['lr_3_count'];
			$result['3']['prize'] = $row['lr_3_prize'];
			$result['4']['cnt'] = $row['lr_4_count'];
			$result['4']['prize'] = $row['lr_4_prize'];
			$result['5']['cnt'] = $row['lr_5_count'];
			$result['5']['prize'] = $row['lr_5_prize'];
		}

		return $result;
	}

	// 지난주 당첨자 가져오기
	function getLastWinner($tm_id='', $inning='') {
		$win_data = $this->getWinData($inning);

		//if(!$tm_id) $this->db->where('mb_tm_id', $tm_id);
		$this->db->join($this->tb['Member']." as b", "a.mb_id=b.mb_id", "LEFT");

		$this->db->where("lwr_inning", $win_data['lw_inning']);
		//$this->db->groupBy("lwr_grade");
		$this->db->orderBy("lwr_grade", "ASC");
		
		$data = $this->db->arrayBuilder()->get($this->tb['LottoNumbersWin']." as a", null,"a.*, b.*");

		$array_prize_idx = array(1 => "lw_1st_prize_ea", 2 => "lw_2nd_prize_ea", 3 => "lw_3rd_prize_ea", 4 => "lw_4th_prize_ea", 5 => "lw_5th_prize_ea");
		foreach($data as $key => $value) {
			$data_tmp[$value['mb_id']][$value['lwr_grade']]['cnt']++;
			$data_tmp[$value['mb_id']][$value['lwr_grade']]['tot_prize'] += $win_data[$array_prize_idx[$value['lwr_grade']]];
		}

		return $data_tmp;
	}

	// 내발급번호 가져오기
	function getMyNumbers($inning, $mb_id, $type='') {

		$page = $page > 0 ? $page : 1;

		if($type) {
			$this->db->where('le_type', $type);
		}

		$this->db->where("le_inning", $inning);
		$this->db->where("mb_id", $mb_id);

		//▶ page rows
		$this->db->pageLimit = 999;

		//▶ order block
		$this->db->orderBy('le_datetime', 'DESC');

		$list = $this->db->arraybuilder()->paginate($this->tb['LottoNumbers'], $page, "*");

		return $list;
	}


	// 당첨자 엑셀 다운로드
	function downloadWinnerExcel() {
		global $pageLimit;

		ini_set("memory_limit" , -1);

		$pageLimit = 99999;
		$data = $this->getWinResultList();
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

		$spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A1", "회차")
            ->setCellValue("B1", "추출일자")
			->setCellValue("C1", "추출번호")
			->setCellValue("D1", "당첨결과")
			->setCellValue("E1", "이용서비스")
			->setCellValue("F1", "종료일")
            ->setCellValue("G1", "결제금액")
			->setCellValue("H1", "발급회원")
			->setCellValue("I1", "휴대전화")
            ->setCellValue("J1", "담당TM");

		$termService = new TermService();


		for($i=0; $i<count($list); $i++) {
			$row_service = $termService->getMemberServiceUse($list[$i]['mb_id']);

			$service_use = "";
			$service_enddate = "";
			for($j=0; $j<count($row_service); $j++) {
				$service_use = $row_service[$j]['sg_name'];
				$service_enddate = substr($row_service[$j]['su_enddate'],0,10);
			}

			$paid = $termService->getMemberServiceBuy($list[$i]['mb_id']);

			$ext_nums = implode(",", array($list[$i]['lwr_num1'],$list[$i]['lwr_num2'],$list[$i]['lwr_num3'],$list[$i]['lwr_num4'],$list[$i]['lwr_num5'],$list[$i]['lwr_num6']));

			$spreadsheet->setActiveSheetIndex(0)
			->setCellValue("A".($i+2), $list[$i]['lwr_inning'])
			->setCellValue("B".($i+2), $list[$i]['lwr_datetime'])
			->setCellValue("C".($i+2), $ext_nums)
			->setCellValue("D".($i+2), $list[$i]['lwr_grade'])
			->setCellValue("E".($i+2), $service_use)
			->setCellValue("F".($i+2), $service_enddate)
			->setCellValue("G".($i+2), number_format($paid[0]['total_pay']))
			->setCellValue("H".($i+2), $list[$i]['mb_id'])
			->setCellValue("I".($i+2), $list[$i]['mb_hp'])
			->setCellValue("J".($i+2), $list[$i]['mb_tm_id']);


		}


		$spreadsheet->setActiveSheetIndex(0);

		header('Content-Type: application/vnd.ms-excel');
		//header('Content-Disposition: attachment;filename='.date('Y-m-d').'_회원정보.xls');
		header('Content-Disposition: attachment; filename="'.iconv('UTF-8','CP949',date('Y-m-d').'_'.$_GET['s_inning'].'회_당첨자정보.xls'). '"');
		header('Cache-Control: max-age=0');

		$spreadsheet->getActiveSheet()
			->getHeaderFooter()->setOddFooter('&R&F Page &P / &N');
		$spreadsheet->getActiveSheet()
			->getHeaderFooter()->setEvenFooter('&R&F Page &P / &N');

		//$spreadsheet->getActiveSheet()->getSheetView()->setView(\PhpOffice\PhpSpreadsheet\Worksheet\SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW);
		
		//$spreadsheet->getActiveSheet()->setBreak( 'A5' , \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW );
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xls");
		//$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	 
		exit;
		
	}




	/******************************************************
	* 마이페이지
	*/

	public function getExtractNumbersMypage($page=1, $url='', $pageLimit=20) {

        $page = $page > 0 ? $page : 1;

		if($_GET['s_inning']) {
			$this->db->where("le_inning", $_GET['s_inning']);
		}

		if($_GET['not_issued']) {
			$this->db->where("mb_id", "");
		}

		if($_GET['type']) {
			$this->db->where('le_type', $_GET['type']);
		}

		if(isset($_GET['s_sg_no']) && !empty($_GET['s_sg_no'])) {
			$this->db->where('sg_no', $_GET['s_sg_no']);
		}

		if($_GET['s_mb_id']) {
			$this->db->where("mb_id", $_GET['s_mb_id']);
		}

		//▶ page rows
		$this->db->pageLimit = $pageLimit;

		//▶ order block
		$this->db->orderBy('le_datetime', 'DESC');

		$list = $this->db->arraybuilder()->paginate($this->tb['LottoNumbers'], $page, "*");

		$link = Utils::getPagination($this->db->totalPages, $page, $url);

		$data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
		$data['list'] = $list;
		$data['link'] = $link;
		$data['total_count'] = $this->db->totalCount;
		$data['total_pages'] = $this->db->totalPages;
		$data['page'] = $page;
		
		return $data;
	
	}

	public function checkSMSOff() {
	
		$query = "UPDATE ".$this->tb['Member']." a JOIN (SELECT * FROM ".$this->tb['TermServiceUse']." WHERE ABS(TIMESTAMPDIFF(DAY, NOW(), su_enddate)) < 1) b ON (a.mb_id = b.mb_id) SET a.mb_sms = 0, a.mb_status = 2";

		$this->db->rawQuery($query);
	}
}
