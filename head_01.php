<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
if(defined('G5_THEME_PATH')) {
    require_once(G5_THEME_PATH.'/head.php');
    return;
}
if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/head.php');
    return;
}
include_once(G5_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');
?>
<? include "inc_top.php" ?>
<div class="subVisual">
<div class="subWrap">
    <div id="stitle">
		<img src="/images/main_visual_04.jpg" style="width:2000px;min-width:1200px">
	</div>
</div>
</div>
<div id="sub">
	<div id="left">
		<div class="lnb">    
		<ul class="four">
		<li><a href="javascript:m1s1()"<?=$_GET['co_id'] == 'company' ? 'class="on"' : ''?>>스팟필터 분석시스템 소개       </a></li>
		<li><a href="javascript:m1s2()"<?=$_GET['co_id'] == 'company2' ? 'class="on"' : ''?>>챌린저</a></li>
		<li><a href="javascript:m1s3()"<?=$_GET['co_id'] == 'company3' ? 'class="on"' : ''?>>다이아</a></li>
		<li><a href="javascript:m1s4()"<?=$_GET['co_id'] == 'company4' ? 'class="on"' : ''?>>마스터</a></li>
		</ul>
	</div>

</div>
<div id="content">
