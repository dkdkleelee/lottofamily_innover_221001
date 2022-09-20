<?php
include_once("./_common.php");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Lotto;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\TermService;
use Acesoft\Common\Message;

$termService = new TermService();

// 인증확인
$serviceBuy = $termService->getServiceBuy($sb_no);

if($_POST['pg'] == 'PG2') {
	if($_POST['sb_no']) {

        	$termService = new TermService();
        	$termService->changeBuyStatus($_POST['sb_no'], 'Y', $_POST['pg'], $_POST['uid']);
        }

        $result = serialize($_POST);
        $termService->updateAgencyPayResult($_POST['sb_no'], $result);
        $termService->setMaskToCardInfo($_POST['sb_no']);
        $return_msg = '결제가 완료되었습니다. - '.$_POST['msg'];

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


        file_put_contents('/home/lottofamily/www/data/log/request.log', $req_dump.PHP_EOL , FILE_APPEND | LOCK_EX);

}



