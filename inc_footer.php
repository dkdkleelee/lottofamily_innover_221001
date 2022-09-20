</div>
<!--div style="clear:both"></div-->
<?php
if (!defined('_INDEX_')) {
    echo "";
}
?>
<script>
    function advice_submit(f) {
        if (!f.wr_9.checked) {
            alert("개인정보 수집 및 이용에 동의하셔야 신청 가능합니다.");
            f.wr_9.focus();
            return false;
        }

        if (!f.phone2.value || !f.phone3.value) {
            alert("연락처를 입력해주세요.");
            return false;
        }

        f.submit();
    }

    function animate_button() {
        $('#ani_button').removeClass('animated tada delay-2s').addClass('animated tada delay-2s');
    }
</script>

<div id="footer">
    <!--div class="footer_counsel">
        <div class="footer_counselForm">
            <form name="counselBottomForm" method="post" action="./bbs/write_update.php" style="margin:0px;">
                <input type=hidden name=null>
                <input type=hidden name=w value="<?= $w ?>">
                <input type="hidden" name="wr_subject" value="빠른상담 : (<?= date('Y년 m월 d일 h시 i분', time()) ?>)">
                <input type="hidden" name="wr_content" value="빠른상담내용">
                <input type=hidden name=bo_table value="counsel">
                <input type=hidden name=wr_5 value="">
                <input type=hidden name=wr_8 value="상담예약">
                <div class="bottom_counsel">
                    <div class="counselLt2">
                        <h2>휴대폰번호</h2>
                    </div>
                    <div class="counselRt2">
                        <select class="phone1" name="phone1" id="phone1">
                            <option value="010">010</option>
                            <option value="011">011</option>
                            <option value="016">016</option>
                            <option value="017">017</option>
                            <option value="018">018</option>
                            <option value="019">019</option>
                        </select>
                        -
                        <input class="phone2" type="text" maxlength="4" id="phone2" name="phone2">
                        -
                        <input class="phone3" type="text" maxlength="4" id="phone3" name="phone3">
                        <p class="privacy"><input type="checkbox" name="wr_9" id="privacy" value="agree"> <a style="cursor:pointer" onclick="window.open('/privacy.htm', 'search', 'scrollbars=yes, width=720,height=600')">개인정보수집
                                및 이용에 동의</a></p>
                    </div>
                    <div class="counselBtn2">
                        <a href="javascript:advice_submit(document.counselBottomForm);animate_button();void(0);"><img id="ani_button" src="/images/main_bottom_btn.png" class="animated tada delay-2s"></a>
                    </div>

                </div>
        </div>
        </form>
    </div-->
    <div class="footer_wrap">
        <div class="footer_logo">
            <img src="/images/main_bottom_info1.png">
        </div>
        <div class="footer_copy">
            <div class="footer_nav">
                <a href="#" onclick="window.open('/service.htm', 'search', 'scrollbars=yes, width=720,height=600')">이용약관</a><span class="footer_l2">|</span><a href="#" onclick="window.open('/privacy.htm', 'search', 'scrollbars=yes, width=720,height=600')">개인정보취급방침</a>
            </div>

            <!-- 상호 : 클래식컴퍼니<span class="footer_l2">|</span>주소 : 인천광역시 남동구 남동대로730 5층<span class="footer_l2">|</span>전화 : -->
            상호 : 파크컴퍼니<span class="footer_l2">|</span>주소 : 인천광역시 남동구 간석동 111-5(도시타워)<span class="footer_l2">|</span>전화 :
            1688-7551 <span class="footer_l2">|</span><!-- 팩스 : <span class="footer_l2">|</span>-->Email :
            parkcompany1004@naver.com<br>
            사업자등록번호 : 515-26-01044<span class="footer_l2">|</span>대표 : 김태훈<span class="footer_l2">|</span>통신판매업신고번호 : 제
            2020-인천남동구-2543<br>
            COPYRIGHT 2020ⓒ lottofamily.co.kr. ALL RIGHTS RESERVED.
            <div class="footer_copy2">
                - 문자발송서비스(SMS,MMS)는 전산오류(핸드폰기종/스팸설정/통신사사정)으로 문자 미전송될 수 있습니다. 본서비스 이용자는 매주 전송하는 서비스 최종 확인은<br>
                로또패밀리 사이트 - 마이 페이지에서 확인해야 할 의무가 있습니다. 문자 미전송과 관련하여 로또패밀리는 어떠한 법적책임도 지지 않음을 알려드립니다.<br>
                - 로또패밀리 분석시스템은 과거데이터를 이용한 분석정보를 제공하고 있으며, 기대하는 이익을 얻지 못해서 발생한 손해 등에 대한 최종책임은 서비스 이용자 본인에게 있습니다.
            </div>

        </div>
        <!--
	<div class="footer_top">
		<a href="#"><img src="/images/main_footer_top.png"></a>
	</div>
	-->
    </div>
</div>

<?

use \Acesoft\LottoApp\Member\User as User;

if (0 && $member['mb_level'] == 3) {

    $user = new User();

    $_GET['s_date'] = $_GET['s_date'] ? $_GET['s_date'] : date('Y-m-d');

    //▶ get list data
    $data = $user->getAlertMemoList($_SESSION['ss_mb_id'], $_GET['page'], $list_url);

    if (count($data) > 0) {
        ?>
        <div class="sidenav <?= $_SESSION['ss_nav_status'] == 'C' ? 'nav-closed' : '' ?>">
            <div id="close"><?= $_SESSION['ss_nav_status'] == 'C' ? '>' : '<' ?></div>
            <?
                    foreach ($data['list'] as $row) {
                        ?>
                <div><a href="/service/public/management/lotto_member_regist.php?mb_id=<?= $row['mb_id'] ?>"><?= $data['idx']-- ?>)
                        <?= $row['mb_name'] ?>(<?= $row['mb_id'] ?>) <?= $row['mo_schedule_datetime'] ?></a></div>
            <?
                    }
                    ?>
        </div>
    <?
        }
        ?>
    <style>
        /* The sidebar menu */
        .sidenav {
            height: auto;
            /* Full-height: remove this if you want "auto" height */
            width: 350px;
            /* Set the width of the sidebar */
            position: fixed;
            /* Fixed Sidebar (stay in place on scroll) */
            z-index: 9999;
            /* Stay on top */
            top: 200px;
            /* Stay at the top */
            left: 0;
            background-color: #111;
            /* Black */
            border: 1px solid #fff;
            padding-top: 20px;
            border-left: none;
        }

        .sidenav>#close {
            background-color: #111;
            /* Black */
            width: 20px;
            right: -21px;
            position: absolute;
            z-index: 9999;
            /* Stay on top */
            color: #fff;
            text-align: center;
            top: -1px;
            cursor: pointer;
            border: 1px solid #fff;
            border-left: none;
        }

        /* The navigation menu links */
        .sidenav a {
            padding: 6px 8px 6px 16px;
            text-decoration: none;
            font-size: 15px;
            color: #fff;
            display: block;
            width: 350px;
            overflow-x: hidden;
        }

        /* When you mouse over the navigation links, change their color */
        .sidenav a:hover {
            color: #f1f1f1;
        }

        .sidenav.nav-closed {
            left: -351px;
        }
    </style>
    <script>
        $('#close').on('click', function() {

            if ($('.sidenav').hasClass('nav-closed')) {
                $('.sidenav').removeClass('nav-closed');
                $('#close').text('<');
                $.post("/service/public/common/common_process.php", {
                    proc: "naveStatus",
                    status: "O"
                });
            } else {
                $('.sidenav').addClass('nav-closed');
                $('#close').text('>');
                $.post("/service/public/common/common_process.php", {
                    proc: "naveStatus",
                    status: "C"
                });
            }
        });

        <
        ?
        if (count($data['list']) == 0) {
            ?
            >
            $('#close').trigger('click'); <
            ?
        } ? >
    </script>
    </div>
<? } ?>
