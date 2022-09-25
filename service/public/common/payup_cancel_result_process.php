<?php
include_once("./_common.php");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Lotto;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\TermService;
use Acesoft\Common\Message;

$termService = new TermService();
$pg_info = $termService->getPGConfig($_POST['pg']);
$pg_url = $pg_info['pay_url'];
$g_code = $pg_info['id'];


// 인증확인
$serviceBuy = $termService->getServiceBuy($sb_no);
$signature = hash("sha256", $pg_info['id']."|".$serviceBuy['sb_ay_transaction_id']."|".$pg_info['apiCertKey']);

if($signature == $_POST['signature']) {

	if($_POST['responseCode'] == "0000" && $_POST['sb_no']) {
		if($_POST['sb_no']) {

			$termService = new TermService();
			$termService->changeBuyStatus($_POST['sb_no'], 'C', $_POST['pg'], $_POST['transactionId']);
		}

		$result = serialize($_POST);
		$termService->updateAgencyPayResult($_POST['sb_no'], $result);

		$return_msg = '취소처리가 완료되었습니다. - '.$_POST['responseMsg'].'\\n !!!!! 등록된 서비스는 확인하시어 취소해 주시기 바랍니다. !!!!!';

	} else {

		$result = serialize($_POST);
		$termService->updateAgencyPayResult($_POST['sb_no'], $result);
		$return_msg = '취소처리 실패하였습니다.. - '.$_POST['responseMsg'];

	}

	if($_POST['return_url'] && $_POST['return_url'] != 'close') {
		echo "<script>location.href= './pay_result_done.php?return_msg=".$return_msg."&return_url=".$_POST['return_url']."&mb_id=".$_POST['mb_id']."';</script>";
	} else {
		echo "<script>alert('".$return_msg."');</script>";
		echo "<script>opener.location.reload();</script>\n";
		echo "<script>window.close();</script>";
	}

	$_POST['datetime'] = date('Y-m-d H:i:s');

	// log 기록
	$req_dump = print_r($_POST, TRUE);


	file_put_contents('/home/lotto/www/lottofamily/data/log/request.log', $req_dump.PHP_EOL , FILE_APPEND | LOCK_EX);
}