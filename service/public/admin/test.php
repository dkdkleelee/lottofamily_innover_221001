<?php
define('G5_IS_ADMIN', true);
$sub_menu = "200100";
/**
 * Created by PhpStorm.
 * User: acerunner
 * Date: 16. 2. 14.
 * Time: 오후 5:46
 */

include_once("./_common.php");

use \Acesoft\Common\Utils;
use \Acesoft\LottoApp\LottoService;
use \Acesoft\LottoApp\Member\User;



// 서비스
$lottoService = new LottoService();




// test 계정으로만 테스트 되는 메소드
$lottoService->extractTodayNumbers();
//$lottoService->setNumbersToUser();
//$lottoService->setNumbersToUserTEST();