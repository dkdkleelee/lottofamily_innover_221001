<?php
// 이 파일은 새로운 파일 생성시 반드시 포함되어야 함
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

require dirname(__FILE__)."/service/vendor/autoload.php";
use \Acesoft\LottoApp\LottoServiceConfig;

//▶ 등급별 발급설정갯수
$lottoServiceConfig = new LottoServiceConfig();
$serviceConfig = $lottoServiceConfig->getConfig();

if(!defined('G5_IS_ADMIN') && !defined('G5_IS_MANAGER')) $g5['title'] = ($serviceConfig['lc_cur_inning']+1)."회 로또 당첨번호";

// 테마 head.sub.php 파일
if(!defined('G5_IS_ADMIN') && defined('G5_THEME_PATH') && is_file(G5_THEME_PATH.'/head.sub.php')) {
    require_once(G5_THEME_PATH.'/head.sub.php');
    return;
}

$begin_time = get_microtime();

if (!isset($g5['title'])) {
    $g5['title'] = $config['cf_title'];
    $g5_head_title = $g5['title'];
}
else {
    $g5_head_title = $g5['title']; // 상태바에 표시될 제목
    $g5_head_title .= " | ".$config['cf_title'];
}

// 현재 접속자
// 게시판 제목에 ' 포함되면 오류 발생
$g5['lo_location'] = addslashes($g5['title']);
if (!$g5['lo_location'])
    $g5['lo_location'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
$g5['lo_url'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
if (strstr($g5['lo_url'], '/'.G5_ADMIN_DIR.'/') || $is_admin == 'super') $g5['lo_url'] = '';

/*
// 만료된 페이지로 사용하시는 경우
header("Cache-Control: no-cache"); // HTTP/1.1
header("Expires: 0"); // rfc2616 - Section 14.21
header("Pragma: no-cache"); // HTTP/1.0
*/
?>
<!doctype html>
<html lang="ko">
<head>
<!--
######################[ 웹사이트 저작권 명시 ]################################
본 웹사이트는 http://www.wepas.com에서 제작된 웹사이트로서 저작권 보호를 받고 있습니다.
솔루션 구입을 원하시면 http://www.wepas.com에서 문의 하시기 바랍니다.
부분적인 소스(코딩,프로그램소스)를 재조합하여 별도의 스킨을 제작하거나 재판매 하는 행위는 금지합니다.
----------------------------------------------------------------------------
1회구입시 1개 도메인에서만 사용이 가능하며.. 복제하여 허가 없이 사용되는 것은 금지입니다.
----------------------------------------------------------------------------
############################################################################
-->
<meta charset="utf-8">
<?php
if (G5_IS_MOBILE) {
    echo '<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10,user-scalable=yes">'.PHP_EOL;
    echo '<meta name="HandheldFriendly" content="true">'.PHP_EOL;
    echo '<meta name="format-detection" content="telephone=no">'.PHP_EOL;
} else {
    echo '<meta http-equiv="imagetoolbar" content="no">'.PHP_EOL;
    echo '<meta http-equiv="X-UA-Compatible" content="IE=10,chrome=1">'.PHP_EOL;
}

if($config['cf_add_meta'])
    echo $config['cf_add_meta'].PHP_EOL;
?>
<title><?php echo "로또패밀리"; //strip_tags($g5_head_title); ?></title>
<?php
if (defined('G5_IS_ADMIN') || defined('G5_IS_MANAGER')) {
    if(!defined('_THEME_PREVIEW_'))
        echo '<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">'.PHP_EOL;
	add_stylesheet('<link rel="stylesheet" href="'.G5_CSS_URL.'/cover_admin.css?ver=170824">', 20);
    add_javascript('<script src="'.G5_JS_URL.'/cover_admin.js?ver=170824"></script>', 20);
    //echo '<link rel="stylesheet" href="'.G5_CSS_URL.'/cover_admin.css">'.PHP_EOL; // add_stylesheet 함수가 문제있을경우 이 코드로 적용해주세요.
    //echo '<script src="'.G5_JS_URL.'/cover_admin.js?ver=170824"></script>'.PHP_EOL; // add_javascript 함수가 문제있을경우 이 코드로 적용해주세요.
?>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<link rel="canonical" href="http://www.lottofamily.com">
<?
} else {
    echo '<link rel="stylesheet" href="'.G5_CSS_URL.'/'.(G5_IS_MOBILE?'mobile':'default1').'.css">'.PHP_EOL;
}
?>
<!--[if lte IE 8]>
<script src="<?php echo G5_JS_URL ?>/html5.js"></script>
<![endif]-->
<script>
// 자바스크립트에서 사용하는 전역변수 선언
var g5_url       = "<?php echo G5_URL ?>";
var g5_bbs_url   = "<?php echo G5_BBS_URL ?>";
var g5_is_member = "<?php echo isset($is_member)?$is_member:''; ?>";
var g5_is_admin  = "<?php echo isset($is_admin)?$is_admin:''; ?>";
var g5_is_mobile = "<?php echo G5_IS_MOBILE ?>";
var g5_bo_table  = "<?php echo isset($bo_table)?$bo_table:''; ?>";
var g5_sca       = "<?php echo isset($sca)?$sca:''; ?>";
var g5_editor    = "<?php echo ($config['cf_editor'] && $board['bo_use_dhtml_editor'])?$config['cf_editor']:''; ?>";
var g5_cookie_domain = "<?php echo G5_COOKIE_DOMAIN ?>";
<?php if(defined('G5_IS_ADMIN')) { ?>
var g5_admin_url = "<?php echo G5_ADMIN_URL; ?>";
<?php } ?>

<?php if(defined('G5_IS_MANAGER')) { ?>
var g5_manager_url = "<?php echo G5_MANAGER_URL; ?>";
<?php } ?>
</script>
<script src="<?php echo G5_JS_URL ?>/jquery-1.8.3.min.js"></script>
<script src="<?php echo G5_JS_URL ?>/jquery.menu.js"></script>
<script src="<?php echo G5_JS_URL ?>/common.js"></script>
<script src="<?php echo G5_JS_URL ?>/wrest.js"></script>
<script src="<?php echo G5_JS_URL ?>/template.js"></script>
<script src="<?php echo G5_JS_URL ?>/url.js"></script>
<script src="/service/public/js/ace.common.js"></script>
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/service/public/js/plugins/noty-2.4.1/demo/animate.css" />

<?php
if(G5_IS_MOBILE) {
    echo '<script src="'.G5_JS_URL.'/modernizr.custom.70111.js"></script>'.PHP_EOL; // overflow scroll 감지
}
if(!defined('G5_IS_ADMIN') && !defined('G5_IS_MANAGER'))
    echo $config['cf_add_script'];
?>
</head>
<body>
