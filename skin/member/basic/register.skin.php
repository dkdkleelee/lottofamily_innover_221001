<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
add_stylesheet('<link rel="stylesheet" href="/service/public/css/custom.style.css">', 0);
?>
<? include "../head_07.php" ?>
<!-- 회원가입약관 동의 시작 { -->
<div class="mbskin">
    <form  name="fregister" id="fregister" action="<?php echo $register_action_url ?>" onsubmit="return fregister_submit(this);" method="POST" autocomplete="off">
	<input type="hidden" name="auth_hp">
    <p>회원가입약관 및 개인정보처리방침안내의 내용에 동의하셔야 회원가입 하실 수 있습니다.</p>

    <section id="fregister_term">
        <h2>회원가입약관</h2>
        <textarea readonly><?php echo get_text($config['cf_stipulation']) ?></textarea>
        <fieldset class="fregister_agree">
            <label for="agree11">회원가입약관의 내용에 동의합니다.</label>
            <input type="checkbox" name="agree" value="1" id="agree11">
        </fieldset>
    </section>

    <section id="fregister_private">
        <h2>개인정보처리방침안내</h2>
        <div class="tbl_head01 tbl_wrap">
            <table>
                <caption>개인정보처리방침안내</caption>
                <thead>
                <tr>
                    <th>목적</th>
                    <th>항목</th>
                    <th>보유기간</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>이용자 식별 및 본인여부 확인</td>
                    <td>아이디, 이름, 비밀번호</td>
                    <td>회원 탈퇴 시까지</td>
                </tr>
                <tr>
                    <td>고객서비스 이용에 관한 통지,<br>CS대응을 위한 이용자 식별</td>
                    <td>연락처 (이메일, 휴대전화번호)</td>
                    <td>회원 탈퇴 시까지</td>
                </tr>
                </tbody>
            </table>
        </div>
        <fieldset class="fregister_agree">
            <label for="agree21">개인정보처리방침안내의 내용에 동의합니다.</label>
            <input type="checkbox" name="agree2" value="1" id="agree21">
        </fieldset>
    </section>
	<section>
		<table class="tb02" width="100%">
                <caption>휴대전화인증</caption>
                <thead>
                <tr>
                    <th>휴대폰번호(회원아이디)</th>
                    <td class="hp-auth-request" style="text-align:center">
						<select name="hp1" id="hp1" class="frm_input mh">
							<option value="010">010</option>
							<option value="011">011</option>
							<option value="016">016</option>
							<option value="017">017</option>
							<option value="018">018</option>
							<option value="019">019</option>
						</select> -
						<input type="text" name="hp2" id="hp2" maxlength="4" size="4" class="frm_input mh"> -
						<input type="text" name="hp3" id="hp3" maxlength="4" size="4" class="frm_input mh">
						<button type="button" class="as-btn medium blue" onClick="getHpAuth();"><i class="fa fa-secure"></i> 인증번호 받기</button>
					</td>
					<td class="hp-auth-confirm" style="display:none;text-align:center">
						<input type="text" name="auth_code" id="auth_code" maxlength="4" size="4" class="frm_input mh" placeholder="인증코드">
						<button type="button" class="as-btn medium blue" onClick="getHpAuthConfirm();"><i class="fa fa-check"></i> 무료회원가입</button>
					</td>
                </tr>
                </thead>
                <tbody>
		</table>
	</section>

    <div class="btn_confirm">
        <!-- <input type="submit" class="btn_submit" value="회원가입"> -->
    </div>

    </form>

    <script>
	function getHpAuth() {
		var f = document.fregister;

		if (!f.agree.checked) {
            alert("회원가입약관의 내용에 동의하셔야 회원가입 하실 수 있습니다.");
            f.agree.focus();
            return false;
        }

        if (!f.agree2.checked) {
            alert("개인정보처리방침안내의 내용에 동의하셔야 회원가입 하실 수 있습니다.");
            f.agree2.focus();
            return false;
        }

		var hp = '';

		if(!$('#hp2').val()) {
			alert("휴대전화번호를 입력해 주세요.");
			$('#hp2').focus();
			return false;
		}

		if(!$('#hp3').val()) {
			alert("휴대전화번호를 입력해 주세요.");
			$('#hp3').focus();
			return false;
		}

		hp = $('#hp1').val()+"-"+$('#hp2').val()+"-"+$('#hp3').val();

		$.ajax({
		   type: "POST",
		   url: '/bbs/ajax.mb_hp_auth.php',
		   
		   data: {'proc' : 'getCode', 'hp' : hp},
		   success: function(data)
		   {
				if(data == 'ok') {
					$('.hp-auth-request').hide();
					$('.hp-auth-confirm').show();
					alert("휴대전화로 전송된 인증코드를 입력해주세요.");
				} else {
					alert(data);
				}
		   }
		 });
	}

	function getHpAuthConfirm() {
		var code = $('#auth_code').val();
		if(!code) {
			$('#auth_code').focus();
			alert("SMS로 전송된 인증코드를 입력해 주세요.");
			return false;
		}

		$.ajax({
		   type: "POST",
		   url: '/bbs/ajax.mb_hp_auth.php',
		   
		   data: {'proc' : 'checkCode', 'code' : code},
		   success: function(data)
		   {
				if(data == 'ok') {
					alert("인증이 완료되었습니다.");
					document.fregister.submit();
					//$('.hp-auth-confirm').hide();
				} else {
					alert(data);
				}
		   }
		 });


		
	}


    function fregister_submit(f)
    {
        if (!f.agree.checked) {
            alert("회원가입약관의 내용에 동의하셔야 회원가입 하실 수 있습니다.");
            f.agree.focus();
            return false;
        }

        if (!f.agree2.checked) {
            alert("개인정보처리방침안내의 내용에 동의하셔야 회원가입 하실 수 있습니다.");
            f.agree2.focus();
            return false;
        }

        return true;
    }
    </script>
</div>
<!-- } 회원가입 약관 동의 끝 -->
<? include "../tail.php" ?>