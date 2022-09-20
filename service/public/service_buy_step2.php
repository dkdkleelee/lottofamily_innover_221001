<?php
include_once("./_common.php");

$cur = 4;

if(!$_SESSION['sb_no']) alert_close("잘못된 접근입니다.");

add_javascript(G5_POSTCODE_JS, 0);    //다음 주소 js


//▶ pagination url
$param = getParameters(array('sg_no'));
$page_url = "service_buy_step1.php?".$param;

$_GET['sg_no'] = !$_GET['sg_no'] ? "1" : $_GET['sg_no'];

// 서비스 환경설정
$cfg = $termService->getServiceConfig();
$accounts = explode("\r\n", $cfg['ic_account']);

// termservice
$termService = new AceTermService();

$product_buy = $termService->getServiceBuy($_SESSION['sb_no']);


$product = $termService->getService($product_buy['sc_no']);


include_once($_dir['root']."head.sub.php");
?>
<link rel="stylesheet" href="/css/default.css">
<link type="text/css" href="<?=$_url['solution_root']?>/css/custom.style.css" rel="stylesheet" />
<link type="text/css" href="<?=$_url['modules']?>/termService/css/form.css" rel="stylesheet" />
<link type="text/css" href="/skin/member/basic/style.css" rel="stylesheet" />
<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css" rel="stylesheet" />
<script src="/js/jquery-1.8.3.min.js"></script>
<script src="<?=$_url['lib']?>/js/jquery-ui-1.8.7.custom.min.js" ></script>
<script src="<?=$_url['lib']?>/js/jquery.ui.datepicker-ko.js" ></script>
<script src="<?php echo G5_JS_URL ?>/jquery.register_form.js"></script>
<style>

.dataTable td { text-align:left;padding-left:10px }
</style>


<div id="container_popup">

	<div class="help_text"><i class="fa fa-arrow-circle-right fa-lg f_blue"></i> 서비스 회원가입</div>
	<form name="service_join_form" method="post" action="./service_buy_process.php">
	<input type="hidden" name="proc" value="joinUser">
	<input type="hidden" name="sb_no" value="<?=$_SESSION['sb_no']?>">
	<div class="tbl_frm01 tbl_wrap">
		<table class="dataTable" cellpadding="0px" cellspacing="0px" style="width:99%">
        <caption>약관동의</caption>
		<tbody>
        <tr>
			<td>
				<p>회원가입약관 및 개인정보처리방침안내의 내용에 동의하셔야 회원가입 하실 수 있습니다.</p>
				<br />

				<section id="fregister_term">
					<h2>회원가입약관</h2>
					<textarea readonly><?php echo get_text($config['cf_stipulation']) ?></textarea>
					<fieldset class="fregister_agree">
						<label for="agree11">회원가입약관의 내용에 동의합니다.</label>
						<input type="checkbox" name="agree" value="1" id="agree11">
					</fieldset>
				</section>
				<br />
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
			</td>
		</tr>
		</table>
	</div>
	<div class="tbl_frm01 tbl_wrap">
		<table class="dataTable" cellpadding="0px" cellspacing="0px" style="width:99%">
        <caption>사이트 이용정보 입력</caption>
        <tbody>
        <tr>
            <th scope="row"><label for="reg_mb_id">아이디<strong class="sound_only">필수</strong></label></th>
            <td>
                <span class="frm_info">영문자, 숫자, _ 만 입력 가능. 최소 3자이상 입력하세요.</span>
                <input type="text" name="mb_id" value="<?php echo $member['mb_id'] ?>" id="reg_mb_id" <?php echo $required ?> <?php echo $readonly ?> class="frm_input <?php echo $required ?> <?php echo $readonly ?>" minlength="3" maxlength="20">
                <span id="msg_mb_id"></span>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="reg_mb_password">비밀번호<strong class="sound_only">필수</strong></label></th>
            <td><input type="password" name="mb_password" id="reg_mb_password" <?php echo $required ?> class="frm_input <?php echo $required ?>" minlength="3" maxlength="20"></td>
        </tr>
        <tr>
            <th scope="row"><label for="reg_mb_password_re">비밀번호 확인<strong class="sound_only">필수</strong></label></th>
            <td><input type="password" name="mb_password_re" id="reg_mb_password_re" <?php echo $required ?> class="frm_input <?php echo $required ?>" minlength="3" maxlength="20"></td>
        </tr>
        
        <tr>
            <th scope="row"><label for="reg_mb_name">이름<strong class="sound_only">필수</strong></label></th>
            <td>
                <input type="text" id="reg_mb_name" name="mb_name" value="<?php echo get_text($product_buy['sb_buyer_name']) ?>" <?php echo $required ?> <?php echo $readonly;?> class="frm_input"> 
            </td>
        </tr>
        <?php if ($req_nick) {  ?>
        <tr>
            <th scope="row"><label for="reg_mb_nick">닉네임<strong class="sound_only">필수</strong></label></th>
            <td>
                <span class="frm_info">
                    공백없이 한글,영문,숫자만 입력 가능 (한글2자, 영문4자 이상)<br>
                    닉네임을 바꾸시면 앞으로 <?php echo (int)$config['cf_nick_modify'] ?>일 이내에는 변경 할 수 없습니다.
                </span>
                <input type="hidden" name="mb_nick_default" value="<?php echo isset($member['mb_nick'])?get_text($member['mb_nick']):''; ?>">
                <input type="text" name="mb_nick" value="<?php echo isset($member['mb_nick'])?get_text($member['mb_nick']):''; ?>" id="reg_mb_nick" required class="frm_input required nospace" size="10" maxlength="20">
                <span id="msg_mb_nick"></span>
            </td>
        </tr>
        <?php }  ?>
		<tr>
            <th scope="row"><label for="reg_mb_hp">휴대폰번호<?php if ($config['cf_req_hp']) { ?><strong class="sound_only">필수</strong><?php } ?></label></th>
            <td>
                <input type="text" name="mb_hp" value="<?php echo get_text($product_buy['sb_buyer_hp']) ?>" id="reg_mb_hp" <?php echo ($config['cf_req_hp'])?"required":""; ?> class="frm_input <?php echo ($config['cf_req_hp'])?"required":""; ?>" maxlength="20">
                <?php if ($config['cf_cert_use'] && $config['cf_cert_hp']) { ?>
                <input type="hidden" name="old_mb_hp" value="<?php echo get_text($member['mb_hp']) ?>">
                <?php } ?>
            </td>
        </tr>
			
		</tbody>
	</table>
	<div class="btn_box ac">
		<a href="javascript:checkForm();void(0);" class="btn_b02" style="width: 80%;">승인요청</a>
    	
	</div>
	</div>
	</form>

	

</div>


<script language="Javascript">

function num_check(evt){
	var code = evt.which?evt.which:event.keyCode;
	if(code < 48 || code > 57){
		return false;
	}
}

function checkForm(f) {

	var f = document.service_join_form;

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

	// 회원아이디 검사
	var msg = reg_mb_id_check();
	if (msg) {
		alert(msg);
		f.mb_id.select();
		return false;
	}


	if (f.mb_password.value.length < 3) {
		alert("비밀번호를 3글자 이상 입력하십시오.");
		f.mb_password.focus();
		return false;
	}
	

	if (f.mb_password.value != f.mb_password_re.value) {
		alert("비밀번호가 같지 않습니다.");
		f.mb_password_re.focus();
		return false;
	}

	if (f.mb_password.value.length > 0) {
		if (f.mb_password_re.value.length < 3) {
			alert("비밀번호를 3글자 이상 입력하십시오.");
			f.mb_password_re.focus();
			return false;
		}
	}

	// 이름 검사
	if (f.mb_name.value.length < 1) {
		alert("이름을 입력하십시오.");
		f.mb_name.focus();
		return false;
	}

	if(!f.mb_hp.value) {
		alert("휴대폰번호를 입력해 주세요.");
		f.mb_hp.focus();
		return false;
	}

    f.submit();
	
}


</script>
