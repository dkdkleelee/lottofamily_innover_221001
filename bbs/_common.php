<?php
include_once('../common.php');

require __DIR__."/../service/vendor/autoload.php";


use Acesoft\LottoApp\TermService;

if($bo_table == 'photo') {
	$termService = new TermService();
	$row = $termService->getMyService($_SESSION['ss_mb_id']);

	if(!$row[0]['leftDays'] || $row[0]['leftDays'] < 1) {
		$board['bo_write_level'] = 3;
	}
}
?>
