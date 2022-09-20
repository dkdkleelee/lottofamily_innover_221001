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
		<li><a href="javascript:m3s1()" <?=$_GET['bo_table'] == 'photo' ? 'class="on"' : ''?>>패밀리수다방</a></li>
		<!-- <li><a href="javascript:m3s2()" <?=$_GET['bo_table'] == 'photo2' ? 'class="on"' : ''?>>당첨영수증</a></li> -->
		</ul>
	</div>

</div>
<div id="content">
