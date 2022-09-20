<?php

include_once("./_common.php");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoService;

$data = $lottoService->getWinData($_POST['inning']);

echo json_encode($data);

?>
