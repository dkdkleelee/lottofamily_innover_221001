<?php namespace Acesoft\LottoApp;

use \Acesoft\Core\Base as Base;
use \Acesoft\Core\DB as DB;
use \Acesoft\Common\Utils as Utils;
use \Acesoft\Common\ConfigDefault;
use \Acesoft\Common\Message;
use \Acesoft\LottoApp\LottoService;


Class Cron /*extends Base*/
{
	public $db;
	private $user;
	private $category;
	private $product;
	private $bid;
	private $order;
	private $message;

	private $lottoServiceConfig;
	private $lottoService;
	private $lotto;



	public function __construct()
	{
		/*parent::__construct();*/
		$this->db = DB::getInstance();


		// 서비스 설정
		$this->lottoServiceConfig = new LottoServiceConfig();

		// 기본 lotto class
		$this->lotto = new Lotto();

		// lotto service
		$this->lottoService = new LottoService();

		// 
		$this->message = new Message();


		$weekday = date('w');
		$hour = date('H');
		$minute = (int)date('i');

		// 미리 추출
		if($weekday != 0 && $weekday != 6) { // 주중
			if($hour >= 8 && $hour <= 9) { // 8시에서 오후 9시
				if($minute % 10 == 0) { // 매 10분마다
					$this->preExtractor();
				}
			}
		}

		// 일자별 추출
		if($weekday != 0 && $weekday != 6) { // 주중
			if($hour >= 10 && $hour <= 19) { // 10시에서 오후 7시
				$this->checkExtract();
			}
		}

		// 당첨확인
		if($weekday == 0) { // 6 토요일, 0 일요일
			if($hour == 11) { // 10시
				$this->checkWinner();
			}
		}

		// 당첨상세정보 - 일요일
		if($weekday == 0) {
			if($hour == 14) {
				$this->getDetailWinData();
			}
		}

		if($hour == 4 && $minute >= 0 && $minute < 5)
			$this->checkSMSOff();

		// SMS발송 큐 실행
		$this->message->proceedQueue('sms');

	}

	public function preExtractor() {
		$result = $this->lottoService->extractTodayNumbers();
		$this->log("[".date('Y-m-d H:i:s D')."] 오늘발송번호 추출 - ".$result);
	}

	// 번호발급
	public function checkExtract() {
		$result = $this->lottoService->setNumbersToUser();
		$this->log("[".date('Y-m-d H:i:s D')."] 번호추출 체크 - 전체:".$result['member_total']."명 / 발급:".number_format($result['issued_member_count'])."명 / 번호전체:".number_format($result['issued_count_total'])."개");
	}

	// 당첨자확인
	public function checkWinner() {

		// 이미 오늘 업데이트가 됐는지 확인
		$this->db->where("DATE_FORMAT(lw_date,'%Y-%m-%d')='".date('Y-m-d')."'");
		$row_win = $this->db->arrayBuilder()->getOne($this->tb['LottoWinNumbers']);

		// 강제 실행
		//$this->lottoService->checkResult('941', '', true);

		if(!$row_win['lw_no']) {// 업데이트가 안됐을때만 실행
			//$this->lottoService->getNextWinNumbers();
		}
	}

	// 상세정보 가져오기(일요일 정도 업데이트)
	public function getDetailWinData() {

		// 어제 업데이트가 됐는지 확인
		$this->db->orderBy("lw_date", "DESC");
		$row_win = $this->db->arrayBuilder()->getOne($this->tb['LottoWinNumbers']);

		$result = $this->lottoService->reCheckWinNumbers($row_win['lw_inning'], $row_win['lw_no']);
		if($result) {
			$this->log("상세정보 갱신완료");
		} else {
			$this->log("상세정보 갱신실패");
		}
	}

	public function checkSMSOff() {
		
		$this->lottoService->checkSMSOff();

	}

	public function log($txt) {
		file_put_contents('/home/lotto/www/lottofamily/data/log/cron_log.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
		//file_put_contents('/home/lotto/data/log/cron_log.txt', $txt.PHP_EOL);
	}
}
