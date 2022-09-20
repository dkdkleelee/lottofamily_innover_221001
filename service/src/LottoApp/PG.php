<?php namespace Acesoft\LottoApp;

use \Acesoft\Core\Base as Base;
use \Acesoft\Core\DB as DB;
use \Acesoft\Common\Utils as Utils;
use \Acesoft\Common\ConfigDefault;
use \Acesoft\Common\Message;
use \Acesoft\LottoApp\LottoService;
use \Acesoft\LottoApp\TermService;



Class PG extends Base
{
	public $db;
	private $user;


	private $lottoServiceConfig;
	private $lottoService;
	private $lotto;

	private $serviceBuy;
	private $pgName;
	private $pgInfo;



	public function __construct()
	{
		parent::__construct();
		$this->db = DB::getInstance();


		// 서비스 설정
		$this->lottoServiceConfig = new LottoServiceConfig();

		// 기본 lotto class
		$this->lotto = new Lotto();

		// lotto service
		$this->lottoService = new LottoService();

		

	}

	public function pay($pg_name, $sb_no) {

		if(!$pg_name || !$sb_no) {
			echo "결제정보 오류.";
			exit;
		}

		if ($pg_name == "SHCard2") {
			$this->pgName = "SHCard";
		}
		else { $this->pgName = $pg_name; }
		
		$this->pgInfo = $this->getPGConfig($this->pgName);

		$termService = new TermService();

		$this->serviceBuy = $termService->getServiceBuy($sb_no);

		$payMethodName = $pg_name."Pay";
		$return = $this->{$payMethodName}();
	}


	public function cancel($pg_name, $sb_no) {
		if(!$pg_name || !$sb_no) {
			echo "결제정보 오류.";
			exit;
		}

		
		if ($pg_name == "SHCard2") {
                        $this->pgName = "SHCard";
                }
		else { $this->pgName = $pg_name; }

		$this->pgInfo = $this->getPGConfig($this->pgName);

		$termService = new TermService();

		$this->serviceBuy = $termService->getServiceBuy($sb_no);

		$payMethodName = $pg_name."Cancel";
		$return = $this->{$payMethodName}();
	}

        public function PG2Pay() {

                $timestamp = date('YmdHis');

                $mb = get_member($this->serviceBuy['mb_id']);

                $param = array(
                                "api_key" => '',
                                "mode" => 'card',
                                "mb_id" => '',
                                "amount" => $this->serviceBuy['sb_price'],
                                "interesttype" => '1',
                                "cardno" => $this->serviceBuy['sb_ay_cardno'],
                                "expdt" => substr($this->serviceBuy['sb_ay_expyear'],2).$this->serviceBuy['sb_ay_expmon'],
                                "installment" => sprintf("%02d", $this->serviceBuy['sb_ay_installment']),
                                "goodname" => (String)$this->serviceBuy['sb_no']."_".$this->serviceBuy['sc_name'],
                                "ordername" => $this->serviceBuy['sb_buyer_name'],
                                "phoneno" => str_replace("-", "", $mb['mb_hp'])
                );


                $redirect_url = "http://".$_SERVER[HTTP_HOST]."/service/public/common/PG2_result_process.php";
?>
        <html>
        <head></head>
        <body onLoad="document.payForm.submit()">
        <form name="payForm" method="post" action="<?=$redirect_url?>">
                <input type="hidden" name="pg" value="<?=$this->pgName?>">
                <input type="hidden" name="timestamp" value="<?=$timestamp?>">
                <input type="hidden" name="sb_no" value="<?=$this->serviceBuy['sb_no']?>">
                <input type="hidden" name="mb_id" value="<?=$this->serviceBuy['mb_id']?>">
        </form>
        </body>
        </html>
<?
        }

	// futureSurf 결제
	public function futureSurfPay() {
		
		$pg_url = $this->pgInfo['pay_url'];
		$g_code = $this->pgInfo['id'];
		$redirect_url = "http://".$_SERVER[HTTP_HOST]."/service/public/common/pay_result_process.php?pg=".$this->pgName."&sb_no=".$this->serviceBuy['sb_no']."&mb_id=".$this->serviceBuy['mb_id']."&return_url=".$_POST['return_url']."&";

		$param = array(
				'g_code'   => $g_code,
				'amt'   => $this->serviceBuy['sb_price'],
				'cardNo' => $this->serviceBuy['sb_ay_cardno'],
				'c_month' => $this->serviceBuy['sb_ay_expmon'],
				'c_year' => substr($this->serviceBuy['sb_ay_expyear'],2),
				'h_month' => sprintf('%02d', $this->serviceBuy['sb_ay_installment']),
				'g_tel' => $this->serviceBuy['sb_buyer_hp'],
				'c_name' => $this->serviceBuy['sb_buyer_name'],
				'c_ma' => $g_code,
				'redirect_url' => $redirect_url
			);

?>
	<html>
	<head></head>
	<body onLoad="document.payForm.submit()">
	<form name="payForm" method="post" action="<?=$this->pgInfo['pay_url']?>">
<?
	foreach($param as $key => $value) {
?>
		<input type="hidden" name="<?=$key?>" value="<?=$value?>">
<?
	}
?>
	</form>
	</body>
	</html>
<?
	}

	// payup 결제
	/*
		"orderNumber":"TEST_00001",
		"cardNo":"9490191412341234",
		"expireMonth":"05",
		"amount":"1004",
		"quota":"0",
		“birthday”:”890302”,
		“cardPw”:”00”,
		"itemName":"TEST 상품",
		"userName":"최용수",
		"mobileNumber":"01031889881",
		"userEmail":"test@payup.co.kr",
		"signature":"ff1728354abe2f78a651f3dca3e75b88a6
		93ff7a6e398b4f5f464d370a6f4464",
		"timestamp":"20180212000000",
		"expireYear":"19"
	*/
	public function payUpPay() {

		$timestamp = date('YmdHis');
		$signature = hash("sha256", $this->pgInfo['id']."|".$this->serviceBuy['sb_no']."|".$this->serviceBuy['sb_price']."|".$this->pgInfo['apiCertKey']."|".$timestamp);
		$param = array(
				"orderNumber" => (String)$this->serviceBuy['sb_no'],
				"cardNo" => $this->serviceBuy['sb_ay_cardno'],
				"expireMonth" => $this->serviceBuy['sb_ay_expmon'],
				"expireYear" => substr($this->serviceBuy['sb_ay_expyear'],2),
				"amount" => $this->serviceBuy['sb_price'],
				"quota" => (String)$this->serviceBuy['sb_ay_installment'],
				"birthday" => $this->serviceBuy['sb_ay_birth'],
				"cardPw" => $this->serviceBuy['sb_ay_f2code'],
				"itemName" => $this->serviceBuy['sc_name'],
				"userName" => $this->serviceBuy['sb_buyer_name'],
				"mobileNumber" => '',
				"userEmail" => '',
				"signature" => $signature,
				"timestamp" => $timestamp
				
		);

		$result = $this->postJson($this->pgInfo['domain'].$this->pgInfo['pay_url'], $param);

		$redirect_url = "http://".$_SERVER[HTTP_HOST]."/service/public/common/payup_pay_result_process.php";
		
		$result = json_decode($result);

?>
	<html>
	<head></head>
	<body onLoad="document.payForm.submit()">
	<form name="payForm" method="post" action="<?=$redirect_url?>">
		<input type="hidden" name="pg" value="<?=$this->pgName?>">
		<input type="hidden" name="timestamp" value="<?=$timestamp?>">
		<input type="hidden" name="signature" value="<?=$signature?>">
		<input type="hidden" name="sb_no" value="<?=$this->serviceBuy['sb_no']?>">
		<input type="hidden" name="mb_id" value="<?=$this->serviceBuy['mb_id']?>">
<?
	foreach($result as $key => $value) {
?>
		<input type="hidden" name="<?=$key?>" value="<?=$value?>">
<?
	}
?>
	</form>
	</body>
	</html>
<?

	}


	// payup 결제취소
	public function payUpCancel() {
		$signature = hash("sha256", $this->pgInfo['id']."|".$this->serviceBuy['sb_ay_transaction_id']."|".$this->pgInfo['apiCertKey']);

		$param = array(
					"signature" => $signature,
					"merchantId" => $this->pgInfo['id'],
					"transactionId" => $this->serviceBuy['sb_ay_transaction_id']
		);

		$result = $this->postJson($this->pgInfo['domain'].$this->pgInfo['cancel_url'], $param);

		$redirect_url = "http://".$_SERVER[HTTP_HOST]."/service/public/common/payup_cancel_result_process.php";

		$result = json_decode($result);
?>
	<html>
	<head></head>
	<body onLoad="document.payForm.submit()">
	<form name="payForm" method="post" action="<?=$redirect_url?>">
		<input type="hidden" name="pg" value="<?=$this->pgName?>">
		<input type="hidden" name="timestamp" value="<?=$timestamp?>">
		<input type="hidden" name="signature" value="<?=$signature?>">
		<input type="hidden" name="sb_no" value="<?=$this->serviceBuy['sb_no']?>">
		<input type="hidden" name="mb_id" value="<?=$this->serviceBuy['mb_id']?>">
<?
	foreach($result as $key => $value) {
?>
		<input type="hidden" name="<?=$key?>" value="<?=$value?>">
<?
	}
?>
	</form>
	</body>
	</html>
<?

	}

	// 서현정보통신 카드연동
	public function SHCardPay() {

		$timestamp = date('YmdHis');
		$signature = hash("sha256", $this->pgInfo['id']."|".$this->serviceBuy['sb_no']."|".$this->serviceBuy['sb_price']."|".$this->pgInfo['apiCertKey']."|".$timestamp);

		$mb = get_member($this->serviceBuy['mb_id']);

		$param = array(
				"api_key" => $this->pgInfo['api_key'],
				"mode" => 'card',
				"mb_id" => $this->pgInfo['id'],
				"amount" => $this->serviceBuy['sb_price'],
				"interesttype" => '1',
				"cardno" => $this->serviceBuy['sb_ay_cardno'],
				"expdt" => substr($this->serviceBuy['sb_ay_expyear'],2).$this->serviceBuy['sb_ay_expmon'],
				"installment" => sprintf("%02d", $this->serviceBuy['sb_ay_installment']),
				"goodname" => (String)$this->serviceBuy['sb_no']."_".$this->serviceBuy['sc_name'],
				"ordername" => $this->serviceBuy['sb_buyer_name'],
				"phoneno" => str_replace("-", "", $mb['mb_hp'])
		);

		//error_log($this->pgInfo['pay_url'], 3, "/opt/apache/logs/error_log");

		$result = $this->post($this->pgInfo['pay_url'], $param);
		
		$redirect_url = "http://".$_SERVER[HTTP_HOST]."/service/public/common/sh_pay_result_process.php";

		$result = json_decode($result);

?>
	<html>
	<head></head>
	<body onLoad="document.payForm.submit()">
	<form name="payForm" method="post" action="<?=$redirect_url?>">
		<input type="hidden" name="pg" value="<?=$this->pgName?>">
		<input type="hidden" name="timestamp" value="<?=$timestamp?>">
		<input type="hidden" name="signature" value="<?=$signature?>">
		<input type="hidden" name="sb_no" value="<?=$this->serviceBuy['sb_no']?>">
		<input type="hidden" name="mb_id" value="<?=$this->serviceBuy['mb_id']?>">
<?
	foreach($result as $key => $value) {
?>
		<input type="hidden" name="<?=$key?>" value="<?=$value?>">
<?
	}
?>
	</form>
	</body>
	</html>
<?
	}

	        // 서현정보통신 카드연동
        public function SHCard2Pay() {

                $timestamp = date('YmdHis');
                $signature = hash("sha256", "okxogns|".$this->serviceBuy['sb_no']."|".$this->serviceBuy['sb_price']."|*91bc83481a63ff89180c6125aab06dd7|".$timestamp);

                $mb = get_member($this->serviceBuy['mb_id']);

                $param = array(
                                "api_key" => "*91bc83481a63ff89180c6125aab06dd7",
                                "mode" => 'card',
                                "mb_id" => "okxogns",
                                "amount" => $this->serviceBuy['sb_price'],
                                "interesttype" => '1',
                                "cardno" => $this->serviceBuy['sb_ay_cardno'],
                                "expdt" => substr($this->serviceBuy['sb_ay_expyear'],2).$this->serviceBuy['sb_ay_expmon'],
                                "installment" => sprintf("%02d", $this->serviceBuy['sb_ay_installment']),
                                "goodname" => (String)$this->serviceBuy['sb_no']."_".$this->serviceBuy['sc_name'],
                                "ordername" => $this->serviceBuy['sb_buyer_name'],
                                "phoneno" => str_replace("-", "", $mb['mb_hp'])
                );

		//error_log($this->pgInfo['pay_url'], 3, "/opt/apache/logs/error_log");

                $result = $this->post($this->pgInfo['pay_url'], $param);

                $redirect_url = "http://".$_SERVER[HTTP_HOST]."/service/public/common/sh_pay_result_process2.php";

                $result = json_decode($result);

?>
        <html>
        <head></head>
        <body onLoad="document.payForm.submit()">
        <form name="payForm" method="post" action="<?=$redirect_url?>">
                <input type="hidden" name="pg" value="<?=$this->pgName?>">
                <input type="hidden" name="timestamp" value="<?=$timestamp?>">
                <input type="hidden" name="signature" value="<?=$signature?>">
                <input type="hidden" name="sb_no" value="<?=$this->serviceBuy['sb_no']?>">
                <input type="hidden" name="mb_id" value="<?=$this->serviceBuy['mb_id']?>">
<?
        foreach($result as $key => $value) {
?>
                <input type="hidden" name="<?=$key?>" value="<?=$value?>">
<?
        }
?>
        </form>
        </body>
        </html>
<?
        }

	public function SHHPPay() {

		$timestamp = date('YmdHis');
		$signature = hash("sha256", $this->pgInfo['id']."|".$this->serviceBuy['sb_no']."|".$this->serviceBuy['sb_price']."|".$this->pgInfo['apiCertKey']."|".$timestamp);

		$mb = get_member($this->serviceBuy['mb_id']);

		$param = array(
				"api_key" => $this->pgInfo['api_key'],
				"mode" => 'sms',
				"mb_id" => $this->pgInfo['id'],
				"amount" => $this->serviceBuy['sb_price'],
				"goodname" => (String)$this->serviceBuy['sb_no']."_".$this->serviceBuy['sc_name'],
				"ordername" => $this->serviceBuy['sb_buyer_name']
		);


		//$result = $this->post($this->pgInfo['pay_url'], $param);



		//$result = json_decode($result);



		//$redirect_url = "http://".$_SERVER[HTTP_HOST]."/service/public/common/sh_pay_result_process.php";
		//exit;
?>
	<html>
	<head></head>
	<body onLoad="document.payForm.submit()">
	<form name="payForm" method="post" action="<?=$this->pgInfo['pay_url']?>">
	<? foreach($param as $key => $value) { ?>
		<input type="hidden" name="<?=$key?>" value="<?=$value?>">
	<? } ?>
	</form>
	</body>
	</html>
<?
	}


	
	public function post($url, $data) {
		$ch = curl_init();

		foreach($data as $key => $value) {
			$param[] = $key."=".$value;
		}

		$param = implode("&", $param);
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		//curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_URL, $url);               //URL 지정하기
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환 
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);      //connection timeout 10초 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);       //POST data
		curl_setopt($ch, CURLOPT_POST, true);              //true시 post 전송 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_ENCODING, "UTF-8" );

		//curl_setopt ($ch, CURLOPT_SSLVERSION,3);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 


		# Send request.
		$result = curl_exec($ch);
		
		if (curl_error($ch)) { 
			exit('CURL Error('.curl_errno( $ch ).') '.curl_error($ch)); 
		}

		$header  = curl_getinfo( $ch );

		curl_close($ch);

		return $result;
	}


	public function postJson($url, $data) {
		$ch = curl_init( $url );
		# Setup request to send json via POST.
		$payload = json_encode( $data );

		curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		# Return response instead of printing.
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); 
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
		# Send request.
		$result = curl_exec($ch);

		$err     = curl_errno( $ch );
		$errmsg  = curl_error( $ch );
		$header  = curl_getinfo( $ch );

		curl_close($ch);

		return $result;
	}

}
