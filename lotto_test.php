<?php
exit;
require __DIR__."/service/vendor/autoload.php";


use Acesoft\LottoApp\Lotto;



$lotto = new Lotto();

$data = $lotto->getWinData(938);


echo "<pre>";
echo print_r($data);
echo "</pre>";

/*
$lotto->setIncludeNumbers([1,3]);
$lotto->setExcludeNumbers([45,15]);
$lotto->setOddEvenRate(3,3);
$lotto->addFilter('continuity', '3');
$lotto->addFilter('excludeCombinations', [[2,12,42,23,12,45],[2,12,42,23,12,45]]);


error_reporting(E_ALL);

ini_set("display_errors", 1);


header("Content-Type: text/html; charset=UTF-8");



$lotto->generateNumbers();



// 당첨번호 가져오기
function getWinNumbers($no){

	$url = "http://www.nlotto.co.kr/common.do?method=getLottoNumber&drwNo=".$no;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	$data = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	return ($httpcode>=200 && $httpcode<300) ? json_decode($data) : false;
}

 // http://www.nlotto.co.kr/gameResult.do?method=byWin&drwNo=816

echo "<pre>";
echo print_r($lotto->retriveWinData());
echo "</pre>";
*/