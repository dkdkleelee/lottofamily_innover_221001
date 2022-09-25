<?php
include_once("./_common.php");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Lotto;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\TermService;
use Acesoft\Common\Message;


$pg_info = $lotto->getPGInfo();
$pg_url = $pg_info['pay_url'];
$g_code = $pg_info['id'];

if($_SERVER['HTTP_REFERER'] == $pg_info['pay_url']) {

	$_GET['return_cd'] = isset($_GET['?return_cd']) ? $_GET['?return_cd'] : $_GET['return_cd'];


	if($_GET['retun_msg'] == '정상결제' && ($_GET['return_cd'] != '99' && $_GET['return_cd'] == '0') && $_GET['sb_no']) {
		if($_GET['sb_no']) {

			$termService = new TermService();
			$termService->changeBuyStatus($_GET['sb_no'], 'Y');
		}
		$termService->updateAgencyPayResult($_GET['sb_no'], $_GET['retun_msg']);
		$termService->setMaskToCardInfo($_POST['sb_no']);
		$return_msg = '결제가 완료되었습니다. - '.$_GET['retun_msg'];

		//echo "<script>alert('결제가 완료되었습니다. - ".$_GET['retun_msg']."');</script>";
	} else {

		$return_msg = '결제실패하였습니다. - '.$_GET['retun_msg'];
		//echo "<script>alert('결제실패하였습니다. - ".$_GET['retun_msg']."');</script>";
	}

	if($_GET['return_url'] && $_GET['return_url'] != 'close') {
		echo "<script>location.href= './pay_result_done.php?return_msg=".$return_msg."&return_url=".$_GET['return_url']."&mb_id=".$_GET['mb_id']."';</script>";
	} else {
		echo "<script>alert('".$return_msg."');</script>";
		echo "<script>opener.location.reload();</script>\n";
		echo "<script>window.close();</script>";
	}

	$_GET['datetime'] = date('Y-m-d H:i:s');

	// log 기록
	$req_dump = print_r($_GET, TRUE);
//	$req_dump .= print_r($_SERVER, TRUE);


	file_put_contents('/home/lotto/www/lottofamily/data/log/request.log', $req_dump.PHP_EOL , FILE_APPEND | LOCK_EX);
}