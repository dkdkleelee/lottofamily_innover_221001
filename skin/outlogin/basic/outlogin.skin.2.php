<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$outlogin_skin_url.'/style.css">', 0);
?>
<ul class="util_in">
<li><a href="/" class="box">홈</a></li>
<li><?=$nick?></li>
<? if($member['mb_level'] == '3') {?>
<li><a href="/service/public/management/lotto_member.php" class="box">TM관리자</a></li>
<? } else if ($member['mb_level'] == '4') { ?>
<li><a href="/service/public/management1/lotto_member.php" class="box">TM관리자</a></li>
<? } ?>
<li><a href="<?php echo G5_BBS_URL ?>/logout.php" class="box">로그아웃</a></li>
<li><a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=register_form.php" class="box">정보수정</a></li>
<? if ( ($is_admin == "super" || $member['mb_id'] == "admin") || $is_auth) { ?>
<li><a href="/service/public/admin/lotto_member.php" target=_blank class=box>관리자모드</a></li>
<? } ?>
</ul>
<script>
// 탈퇴의 경우 아래 코드를 연동하시면 됩니다.
function member_leave()
{
    if (confirm("정말 회원에서 탈퇴 하시겠습니까?"))
        location.href = "<?php echo G5_BBS_URL ?>/member_confirm.php?url=member_leave.php";
}
</script>
<!-- } 로그인 후 아웃로그인 끝 -->
