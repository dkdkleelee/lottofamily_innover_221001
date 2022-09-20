<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$outlogin_skin_url.'/style.css">', 0);
?>
<ul class="util">
<li><a href="/" class="box">홈</a></li>
<li><a href="<?php echo G5_BBS_URL ?>/login.php" class="box">로그인</a></li>
<li><a href="<?php echo G5_BBS_URL ?>/register.php" class="box">회원가입</a></li>
</ul>