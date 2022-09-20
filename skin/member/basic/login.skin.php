<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style2.css">', 0);
?>
<? include "../head_07.php" ?>
<!-- 로그인 시작 { -->
<div id="mb_login" class="mbskin">
    <h1>로그인</h1>

    <form name="flogin" action="<?php echo $login_action_url ?>" onsubmit="return flogin_submit(this);" method="post">
    <input type="hidden" name="url" value="<?php echo $login_url ?>">

    <div id="yd_member_login">
        <fieldset>
            <legend>회원로그인</legend>
            <div class="ipt">
                <input type="text" name="mb_id" size="20" maxLength="20" placeholder="아이디" title="아이디">
            </div>
            <div class="ipt">
                <input type="password" name="mb_password" size="20" maxLength="20" placeholder="비밀번호" title="비밀번호">
            </div>
            <div id="login">
                <input type="submit" value="로그인" class="submit">
            </div>
            <div class="bottom">
                <a href="./register.php" class="register">회원가입</a>
                <a class="au">&nbsp;|&nbsp;</a>
                <a href="<?php echo G5_BBS_URL ?>/password_lost.php" target="_blank" id="login_password_lost" class="idpw">아이디 비밀번호 찾기</a>
                <span class="right">
                <input type="checkbox" name="auto_login" id="login_auto_login">
                <label for="login_auto_login">자동로그인</label>
                </span>
            </div>
        </fieldset>

        <? /* <aside id="login_info">
            <h2>회원로그인 안내</h2>
            <p>
                회원아이디 및 비밀번호가 기억 안나실 때는 아이디/비밀번호 찾기를 이용하십시오.<br>
                아직 회원이 아니시라면 회원으로 가입 후 이용해 주십시오.
            </p>
            <div>
                <a href="<?php echo G5_BBS_URL ?>/password_lost.php" target="_blank" id="login_password_lost" class="btn02">아이디 비밀번호 찾기</a>
                <a href="./register.php" class="btn01">회원 가입</a>
            </div>
        </aside>

        <div class="btn_confirm">
            <a href="<?php echo G5_URL ?>/">메인으로 돌아가기</a>
        </div>
        */ ?>
    </div>
    </form>

</div>

<script>
$(function(){
    $("#login_auto_login").click(function(){
        if (this.checked) {
            this.checked = confirm("자동로그인을 사용하시면 다음부터 회원아이디와 비밀번호를 입력하실 필요가 없습니다.\n\n공공장소에서는 개인정보가 유출될 수 있으니 사용을 자제하여 주십시오.\n\n자동로그인을 사용하시겠습니까?");
        }
    });
});

function flogin_submit(f)
{
    return true;
}
</script>
<!-- } 로그인 끝 -->

<?
include_once ('./_tail.php');
?>