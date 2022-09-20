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
		<li style="width:16% !important"><a href="javascript:m5()" <?=$_GET['m'] == '' ? 'class="on"' : ''?>>이번주발급번호</a></li>
		<li style="width:16% !important"><a href="javascript:m5s1()" <?=$_GET['m'] == '1' ? 'class="on"' : ''?>>정회원정보</a></li>
		<li style="width:16% !important"><a href="javascript:m5s2()" <?=$_GET['m'] == '2' ? 'class="on"' : ''?>>발급번호관리</a></li>
		<li style="width:16% !important"><a href="javascript:m5s3()" <?=$_GET['m'] == '3' ? 'class="on"' : ''?>>당첨내역조회</a></li>
		<li style="width:16% !important"><a href="javascript:m5s4()" <?=$_GET['m'] == '4' ? 'class="on"' : ''?>>패밀리정회원 신청</a></li>
		<!--li style="width:16% !important"><a href="javascript:m5s5()" <?=$_GET['m'] == '5' ? 'class="on"' : ''?>>당첨시뮬레이터</a></li-->
		</ul>
	</div>

</div>
<div id="content">
