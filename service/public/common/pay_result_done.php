<?php
include_once("./_common.php");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Lotto;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\TermService;
use Acesoft\Common\Message;


// 결과처리
echo "<script>alert('".$_GET['return_msg']."');</script>";

if($_GET['return_url'] && $_GET['return_url'] != 'close') {

	echo "<script>location.href= '".$_GET['return_url']."&mb_id=".$_GET['mb_id']."';</script>";
} else {
	echo "<script>window.close();</script>";
}
