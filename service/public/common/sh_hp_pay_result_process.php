<?php
include_once("./_common.php");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Lotto;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\TermService;
use Acesoft\Common\Message;



$termService = new TermService();
$pg_info = $lotto->getPGConfig('SHHP');
$pg_url = $pg_info['pay_url'];
$g_code = $pg_info['id'];


if($_GET['uid']) {

	$param = array(
				"api_key" => $pg_info['api_key'],
				"mode" => 'list',
				"mb_id" => $pg_info['id'],
				"uid" => $_GET['uid']
		);

	$result = post($pg_info['pay_url'], $param);
	$result = json_decode($result, true);

	$tmp = explode("_", $result[0]['subject']);
	$sb_no = $tmp[0];
	
	// 인증확인
	$serviceBuy = $termService->getServiceBuy($sb_no);

	if($sb_no && ($result[0]['pg'] == 'S' && $result[0]['status'] == '승인')) {

		if($sb_no) {

			$termService = new TermService();
			$termService->changeBuyStatus($sb_no, 'Y', 'SHHP', $_GET['uid']);
		}

		
		$termService->updateAgencyPayResult($sb_no, serialize($result));
		$termService->setMaskToCardInfo($sb_no);
		$return_msg = '결제가 완료되었습니다. - '.$result[0]['status'];
	} else {
		
		$termService->updateAgencyPayResult($_POST['sb_no'], serialize($result));
		$return_msg = '결제실패하였습니다. - '.$result[0]['status'];
	}

	
	echo "<script>alert('".$return_msg."\\n F5를 눌러 고객관리창을 새로고침 해주세요');</script>";
	//echo "<script>opener.location.reload();</script>\n";
	
	echo "<script>window.close();</script>";
	

	$_POST['datetime'] = date('Y-m-d H:i:s');

	// log 기록
	$req_dump = print_r($_POST, TRUE);


	file_put_contents('/home/lottofamily/www/data/log/request.log', $req_dump.PHP_EOL , FILE_APPEND | LOCK_EX);

}

echo "<script>window.close();</script>";

function post($url, $data) {
	$ch = curl_init();

	foreach($data as $key => $value) {
		$param[] = $key."=".$value;
	}

	$param = implode("&", $param);
	//curl_setopt($ch, CURLOPT_HEADER, 0); 
	//curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch, CURLOPT_URL, $url);               //URL 지정하기
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환 
	//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);      //connection timeout 10초 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
	curl_setopt($ch, CURLOPT_POSTFIELDS, $param);       //POST data
	curl_setopt($ch, CURLOPT_POST, true);              //true시 post 전송 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_ENCODING, "UTF-8" );

	curl_setopt ($ch, CURLOPT_SSLVERSION,3);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 


	# Send request.
	$result = curl_exec($ch);
	
	if (curl_error($ch)) { 
		exit('CURL Error('.curl_errno( $ch ).') '.curl_error($ch)); 
	}

	$header  = curl_getinfo( $ch );

	curl_close($ch);

	return $result;
}
exit;