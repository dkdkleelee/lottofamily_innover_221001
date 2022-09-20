<?php

namespace Acesoft\LottoApp;

use \Acesoft\Core\Base;
use \Acesoft\Core\DB;
use \Acesoft\LottoApp\LottoServiceConfig;
use \GuzzleHttp\Client;
use \Symfony\Component\DomCrawler\Crawler;

class Lotto /*extends Base*/
{

	public $db;
	public $sericeConfig;

	public $filters;
	public $include_numbers;
	public $exclude_numbers;
	public $include_rate, $exclude_rate;
	public $odd_num, $even_num;
	public $permit_continue_num;
	public $permit_ac_num;
	public $exclude_pre_win_num;

	public $exclude_prewin_array;

	public function __construct() {
		/*parent::__construct();*/
		$this->db = DB::getInstance();

		// 서비스 환경설정
		$this->serviceConfig = new LottoServiceConfig();
		$this->next_inning = $this->serviceConfig->getConfig()['lc_cur_inning']+1;

	}

	public function setConfig($config) {

		
		$this->setIncludeNumbers($config['lc_include_numbers'], $config['lc_include_rate']);
		$this->setExcludeNumbers($config['lc_exclude_numbers'], $config['lc_exclude_rate']);

		if($config['lc_uoddEven_use']) {
			$this->setOddEvenRate($config['lc_odd_rate'], $config['lc_even_rate']);
		} else {
			$this->setOddEvenRate();
		}

		$this->setContinueNum($config['lc_permit_continue_num']);

		// A/C사용여부
		if($config['lc_ac_num_use']) {
			$this->setACnumber($config['lc_permit_ac_num']);
		}


		$this->setExcludePreWinNumber($config['lc_exclude_win_num']);

		$this->exclude_prewin_array = $this->exceptPreWinNumbers();

	}


	public function setIncludeNumbers($include_numbers, $rate) {

		if(!is_array($include_numbers)) $include_numbers = explode(',', $include_numbers);
		$this->include_numbers = array_filter($include_numbers);
		$this->include_rate = $rate;
	}

	public function setExcludeNumbers($exclude_numbers, $rate) {

		if(!is_array($exclude_numbers)) $exclude_numbers = explode(',', $exclude_numbers);
		$this->exclude_numbers = array_filter($exclude_numbers);
		$this->exclude_rate = $rate;
	}

	public function setContinueNum($num) {
		$this->permit_continue_num = $num;
	}

	public function setOddEvenRate($odd_num='', $even_num='') {
		$this->odd_num = $odd_num;
		$this->even_num = $even_num;
	}

	public function setACnumber($num) {
		$this->permit_ac_num = $num;
	}

	public function setExcludePreWinNumber($num) {
		$this->exclude_pre_win_num = $num;
	}

	public function addFilter($type, $data) 
	{
		$this->filters[$type] = $data;
	}

	public function exceptPreWinNumbers() {

		if($this->exclude_pre_win_num) {

			$this->db->orderBy("lw_inning", "DESC");
			$rows = $this->db->arrayBuilder()->get($this->tb['LottoWinNumbers'], $this->exclude_pre_win_num, "md5(concat(lw_num1, lw_num2, lw_num3, lw_num4, lw_num5, lw_num6)) as h");

			return $rows;
		} else {
			return array();
		}
	}

	public function generateNumbers() 
	{

		// default number pool
		$numbers = range(1, 45);

		// 추출자릿수
		$number_to = 6;

		// 지정숫자
		if (rand(1,100)<=$this->include_rate) {
			if(is_array($this->include_numbers)) {
				$pre_numbers = $this->include_numbers;
				$numbers = array_diff($numbers, $pre_numbers);

				// 지정숫자갯수만큼 빼고 추출
				$number_to = $number_to - count($this->include_numbers);
			}
		}

		// 제외숫자
		if (rand(1,100)<=$this->exclude_rate) {
			if(is_array($this->exclude_numbers)) {
				$excluce_numbers = $this->exclude_numbers;

				$numbers = array_diff($numbers, $excluce_numbers);
				
				
			}
		}


		$not_ok = true;
		$max_loop = 10000;
		while($not_ok && $max_loop > 0) {
			// 추출
			shuffle($numbers);
			$e_numbers = array_slice($numbers, 0, $number_to);

			

			// 지정숫자추가
			if(is_array($pre_numbers)) {
				$e_numbers = array_merge($pre_numbers, $e_numbers);
			}
			// sort
			sort($e_numbers);

			//////////////////
			//echo "<pre>";
			//echo print_r($e_numbers);
			//echo "</pre>";
			//////////////////

			////////////////////////////////////////////////////////////////////////////////////////////////
			//	filter check
			////////////////	
			$h = md5($e_numbers[0].$e_numbers[1].$e_numbers[2].$e_numbers[3].$e_numbers[4].$e_numbers[5]);


			// 이전당첨번호 제외
			if(is_array($this->exclude_prewin_array)) {
				if(!in_array($h, $this->exclude_prewin_array)) {
					$not_ok = false;
				} else {
					$not_ok = true;
					//echo "pre win number check fail<br />";
				}
			}

			// 중복체크
			

			// 짝홀
			
			if($not_ok === false && ($this->odd_num || $this->even_num)) {
				$oddEvenResult = $this->oddEvenFilter($e_numbers);
				//echo "oddeven : ".$this->odd_num."  ".$this->even_num."<br />";
				if($oddEvenResult['odd'] == $this->odd_num && $oddEvenResult['even'] == $this->even_num) {
					$not_ok = false;
				} else {
					$not_ok = true;
					//echo "odd even number check fail<br />";
				}
			}

			// 연속수
			
			if($not_ok === false && $this->permit_continue_num) {
				$continutyResult = $this->continuityFilter($e_numbers);
				//echo "con@@@ : $continutyResult";
				if($continutyResult <= $this->permit_continue_num) {
					$not_ok = false;
				} else {
					$not_ok = true;
					//echo "pre continuty check fail<br />";
				}
			}

			// ac필터
			
			if($not_ok === false && $this->permit_ac_num) {
				$acResult = $this->acFilter($e_numbers);
				//echo "AC@@@ : $acResult";
				if($acResult >= $this->permit_ac_num) {
					$not_ok = false;
				} else {
					$not_ok = true;
					//echo "pre ac filter check fail<br />";
				}
			}

			// 이번회차 중복체크
			if($not_ok === false) {
				
				if(!$this->inningDupberFilter($e_numbers)) {
					$not_ok = false;// echo "inningDupberFilter ok";
				} else {
					$not_ok = true;
					//echo "inningDupberFilter check fail<br />";
				}
			}
			
			//echo "<br />continuity max: ".$this->continuityFilter($numbers);
			//echo "<br />ac value: ".$this->acFilter($numbers);
			//echo "<br />odd even value: ".$this->oddEvenFilter($numbers);
			
			$max_loop--;
		}
		/*
		echo "<pre>$max_loop";
		echo print_r($e_numbers);
		echo "</pre>";
		*/

		return $e_numbers;
	}

	public function generateNumbersOld() 
	{

		// default number pool
		$numbers = range(1, 45);

		// 추출자릿수
		$number_to = 6;

		// 지정숫자
		if (rand(1,100)<=$this->include_rate) {
			if(is_array($this->include_numbers)) {
				$pre_numbers = $this->include_numbers;
				$numbers = array_diff($numbers, $pre_numbers);
//echo "include<br>";
				// 지정숫자갯수만큼 빼고 추출
				//$number_to = $number_to - count($this->include_numbers);
			}
		}


		// 제외숫자
		if (rand(1,100)<=$this->exclude_rate) {
			if(is_array($this->exclude_numbers)) {
				$excluce_numbers = $this->exclude_numbers;

				$numbers = array_diff($numbers, $excluce_numbers);
				
				
			}
		}

		$not_ok = true;
		$max_loop = 5000;
		while($not_ok && $max_loop > 0) {
			// 추출
			shuffle($numbers);
			$e_numbers = array_slice($numbers, 0, $number_to);

			// 지정숫자추가, 지정된 숫자들 중 max 3개
			if(is_array($pre_numbers)) {
				shuffle($pre_numbers);
				$pre_numbers = array_slice($pre_numbers, 1, rand(1,4));
				$e_numbers = array_merge($pre_numbers, $e_numbers);
				$e_numbers = array_slice($e_numbers, 0, $number_to);
			}
			// sort
			sort($e_numbers);


			//////////////////
			//echo "<pre>";
			//echo print_r($e_numbers);
			//echo "</pre>";
			//////////////////

			////////////////////////////////////////////////////////////////////////////////////////////////
			//	filter check
			////////////////	
			$h = md5($e_numbers[0].$e_numbers[1].$e_numbers[2].$e_numbers[3].$e_numbers[4].$e_numbers[5]);


			// 이전당첨번호 제외
			if(is_array($this->exclude_prewin_array)) {
				if(!in_array($h, $this->exclude_prewin_array)) {
					$not_ok = false; //echo "exclude ok";
				} else {
					$not_ok = true;
					//echo "pre win number check fail<br />";
				}
			}

			// 중복체크
			

			// 짝홀
			
			if($not_ok === false && ($this->odd_num || $this->even_num)) {
				$oddEvenResult = $this->oddEvenFilter($e_numbers);
				//echo "oddeven : ".$this->odd_num."  ".$this->even_num."<br />";
				if($oddEvenResult['odd'] == $this->odd_num && $oddEvenResult['even'] == $this->even_num) {
					$not_ok = false; //echo "oddEvenFilter ok";
				} else {
					$not_ok = true;
					//echo "odd even number check fail<br />";
				}
			}

			// 연속수
			if($not_ok === false && $this->permit_continue_num) {
				$continutyResult = $this->continuityFilter($e_numbers);
				//echo $e_numbers[0]." ".$e_numbers[1]." ".$e_numbers[2]." ".$e_numbers[3]." ".$e_numbers[4]." ".$e_numbers[5]." "."con@@@ : $continutyResult"."<br>";
				if($continutyResult <= $this->permit_continue_num) {
					$not_ok = false;// echo "continuityFilter ok";
				} else {
					$not_ok = true;
					$max_loop--;
					
					//echo "pre continuty check fail<br />";
					
				}
			}

			// ac필터
			
			if($not_ok === false && $this->permit_ac_num) {
				$acResult = $this->acFilter($e_numbers);
				//echo "AC@@@ : $acResult";
				if($acResult >= $this->permit_ac_num) {
					$not_ok = false;// echo "acFilter ok";
				} else {
					$not_ok = true;
					//echo "pre ac filter check fail<br />";
				}
			}

			// 이번회차 중복체크
			if($not_ok === false) { 
				if(!$this->inningDupberFilter($e_numbers)) {
					$not_ok = false;// echo "inningDupberFilter ok";
				} else {
					$not_ok = true;
					//echo "inningDupberFilter check fail<br />";
				}
			}

			//echo "<br />continuity max: ".$this->continuityFilter($numbers);
			//echo "<br />ac value: ".$this->acFilter($numbers);
			//echo "<br />odd even value: ".$this->oddEvenFilter($numbers);
			
			$max_loop--;
		}
		/*
		echo "<pre>$max_loop";
		echo print_r($e_numbers);
		echo "</pre>";
		*/

		return $e_numbers;
	}


	public function continuityFilter($numbers) {
		$continue_count = 1;
		$continue_max_count = 1;
		for($i=1; $i<count($numbers); $i++) {
			if(($numbers[$i-1]+1) == $numbers[$i]) {
				$continue_count++;
				if($continue_max_count < $continue_count) $continue_max_count = $continue_count;
			} else {
				$continue_count = 1;
			}
		}

		return $continue_max_count;
	}

	public function acFilter($numbers) {
		$ac_array = array();
		for($a=0;$a<5;$a++) { 
			for($b=$a+1;$b<6;$b++) { 
				$k=$numbers[$b] - $numbers[$a]; 
				$ac_array[$k]=1; 
			}
		}

		//중복없이 발생한 AC조합갯수를 계산한다.(5개~20개 나옵니다) 
		$ac_count=0; 
		foreach($ac_array as $key => $value) {
			if($value > 0) { 
				$ac_count++; 
			}
		} 
		$ac_value = $ac_count - ( 6 - 1); 

		return $ac_value;
	}

	public function sumFilter($numbers) {
		$sum = array_sum($numbers);

		return $sum;
	}


	public function oddEvenFilter($numbers) {
		// 짝,홀 비율확인
		$odd_count = 0;
		$even_count = 0;
		//if($this->odd_num && $this->even_num) {
			for($i=0; $i<6; $i++) {
				if($numbers[$i]%2 == '1') {
					$odd_count++;
				} else {
					$even_count++;
				}
			}
		//}

		$result['odd'] = $odd_count;
		$result['even'] = $even_count;

		return $result;
	}

	public function inningDupberFilter($numbers) {
		$this->db->where('le_inning', $this->next_inning);
		$this->db->where("le_serial_num", md5($numbers[0].$numbers[1].$numbers[2].$numbers[3].$numbers[4].$numbers[5]));
		//$this->db->orderBy("le_inning", "DESC");
		$rows = $this->db->arrayBuilder()->getOne($this->tb['LottoNumbers'], "le_serial_num");

		if($rows['le_serial_num']) {
			return true;
		} else {
			return false;
		}

	}

	public function retriveWinData($inning='') {

		// 임시고정(자동)
		$inning='';

		//error_log('\naa', 3, "/opt/apache/logs/error_log");

		if(!$inning) {
			$row = $this->db->getOne($this->tb['LottoWinNumbers'],'COALESCE(max(lw_inning), 0)+1 as cur_inning');

			error_log($row['cur_inning'], 3, "/opt/apache/logs/error_log");

			$inning = $row['cur_inning'];

			//error_log($inning, 3, "/opt/apache/logs/error_log");			

		} else {
			$this->db->where('lw_inning', $inning);
			$row = $this->db->getOne($this->tb['LottoWinNumbers'],'*');

			//error_log($row['lw_inning'], 3, "/opt/apache/logs/error_log");

			if($row['lw_inning']) {
				return $row['lw_no'];
			}
		}

		$win_data = $this->getWinData($inning);

		return $win_data;

	}

	// 당첨번호 가져오기
	// 2018-12-02 동행으로 업데이트
	public function getWinData($inning=''){

		//error_log('\na', 3, "/opt/apache/logs/error_log");

		$url = "https://www.dhlottery.co.kr/common.do?method=getLottoNumber&drwNo=".$inning;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$result = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		//error_log('\nb', 3, "/opt/apache/logs/error_log");
		//error_log($inning, 3, "/opt/apache/logs/error_log");
		//error_log($httpcode, 3, "/opt/apache/logs/error_log");

		$basic_data = array();

		$basic_data = json_decode($result, true);
		//error_log($basic_data['drwNo1'], 3, "/opt/apache/logs/error_log");


		if($httpcode>=200 && $httpcode<300) {

			//error_log('\nc', 3, "/opt/apache/logs/error_log");
			
			$basic_data = json_decode($result, true);


			if($basic_data['returnValue'] != 'fail') {

				//error_log('\nd', 3, "/opt/apache/logs/error_log");

				if($basic_data['drwNo']) {
					// 상세데이타 가져오기
					$detail_data = $this->getLottoData($inning);

				}

				$data['basic'] = $basic_data;
				$data['detail'] = $detail_data;

				//error_log('\ne', 3, "/opt/apache/logs/error_log");

				return $data;
			} else {

				//error_log('\nf', 3, "/opt/apache/logs/error_log");

				return false;
			}


		} else {

			//error_log('\ng', 3, "/opt/apache/logs/error_log");
			return false;
		}
	}


	// 기타정보 가져오기
	// 2018-12-02 동행으로 업데이트
	public function getLottoData($inning='') {

        $this->protocol = 'https';
        $url = $this->protocol."://www.dhlottery.co.kr/gameResult.do?method=byWin&drwNo=".$inning; 
        $client = new \GuzzleHttp\Client();
        //https 접속시 ssl 오류가 발생하면, ['verify' => false] 옵션을 추가한다.
        $result = $client->request('GET', $url, ['verify' => false]);
        $html = $result->getBody()->getContents();
        $dom = new Crawler(iconv('euc-kr','utf-8',$html));


		// 당첨숫자
		$winner_number_items = $dom->filter('div.nums .win p')->children();

		foreach($winner_number_items as $item) {
			$obj = new Crawler($item);
			if($obj->text()) {
				$num[] = $obj->text();
			}
		}

		// 보너스 숫자
		$num[] = $dom->filter('div.nums .bonus p')->filter('span')->text();
		$data['num'] = $num;

		$win_table = $dom->filter("table.tbl_data tr");
		$row_idx = 0;
		foreach($win_table as $item) {
			$obj = new Crawler($item);

			$win_table_td = $obj->filter("td");
			
			foreach($win_table_td as $item_row) {
				$obj_row = new Crawler($item_row);
				$data_tmp[$row_idx][] = $obj_row->text();
			}
			$row_idx++;
		}

		$idx = 0;
		for($i=1; $i<7; $i++) {
			$data['win'][$idx][] = preg_replace("/\D/", "", $data_tmp[$i][0]);
			$data['win'][$idx][] = preg_replace("/\D/", "", $data_tmp[$i][1]);
			$data['win'][$idx][] = preg_replace("/\D/", "", $data_tmp[$i][2]);
			$data['win'][$idx][] = preg_replace("/\D/", "", $data_tmp[$i][3]);
			$data['win'][$idx][] = $data_tmp[$i][4];
			$data['win'][$idx][] = $data_tmp[$i][5];

			$idx++;
		}

		return $data; 
	}

	public function perfectCombination($add_numbers, $ex_numbers) {
		$arr = array('3','4','5','7','9','30','42','41'); //예상번호 
		$a=count($arr); 
		$b=6; 
		for($i=0; $i<$a-$b+1; $i++){
			for($j=$i+1; $j<$a-$b+2; $j++){
				for($k=$j+1; $k<$a-$b+3; $k++){
					for($l=$k+1; $l<$a-$b+4; $l++){
						for($m=$l+1; $m<$a-$b+5; $m++){
							for($n=$m+1; $n<$a-$b+6; $n++){
								echo $arr[$i]."-".$arr[$j]."-".$arr[$k]."-".$arr[$l]."-".$arr[$m]."-".$arr[$n]."<br>"; 
							} 
						} 
					} 
				} 
			} 
		} 
	}

}
