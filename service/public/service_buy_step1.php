<?php
include_once("./_common.php");

$cur = 4;

use Acesoft\Common\Utils;
use Acesoft\LottoApp\TermService;
use Acesoft\LottoApp\Member\User;
use Acesoft\LottoApp\Member\Auth;

add_javascript(G5_POSTCODE_JS, 0);    //다음 주소 js

Auth::checkAuth();

// termservice
$termService = new TermService();

$_GET['type'] = $_GET['type'] ? $_GET['type'] : 'check';
$_GET['sg_no'] = !$_GET['sg_no'] ? "1" : $_GET['sg_no'];

// 서비스 환경설정
$cfg = $termService->getServiceConfig();
$accounts = explode("\r\n", $cfg['tdc_accounts']);

$product = $termService->getService($_GET['no']);

$mb = get_member($_SESSION['ss_mb_id']);

?>
<link rel="stylesheet" href="/css/default.css">
<link type="text/css" href="./css/custom.style.css" rel="stylesheet" />
<link type="text/css" href="./termService/css/form.css" rel="stylesheet" />
<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css" rel="stylesheet" />
<script src="/js/jquery-1.8.3.min.js"></script>
<script src="./js/jquery-ui-1.8.7.custom.min.js" ></script>
<script src="./js/jquery.ui.datepicker-ko.js" ></script>
<style>

.dataTable td { text-align:left;padding-left:10px }
</style>

<div id="container_popup">
	<div class="btitle">
		<div class="btitle_top"></div>
		<div class="btitle_text">서비스신청</div>
		<div class="btitle_locate">&gt; 서비스 신청 및 결제</div>
		<div class="btitle_line"></div>
	</div>

	<ul id="tab">
		<li><a href="?no=<?=$_GET['no']?>&type=acc" class="<?=$_GET['type'] == 'acc' ? 'tab_on' : ''?>">카드결제/무통장입금</a></li>
		<li><a href="?no=<?=$_GET['no']?>&type=check" class="<?=$_GET['type'] == 'check' ? 'tab_on' : ''?>">체크/신용카드결제신청</a></li>
	</ul>

	<? if($_GET['type'] == 'check') { ?>
	
	<form name="service_pay_form" method="post" action="./service_buy_process.php">
	<input type="hidden" name="type" value="manual">
	<input type="hidden" name="proc" value="addServiceBuy">
	<input type="hidden" name="mb_id" value="<?=$_SESSION['ss_mb_id']?>">
	<input type="hidden" name="sc_no" value="<?=$product['sc_no']?>">
	<input type="hidden" name="pay_method" value="agency">
		<table class="dataTable" cellpadding="0px" cellspacing="0px" style="width:100%">
		<colgroup>
			<col style="width:140px">
			<col style="width:auto">
		</colgroup>
		<tbody>
			<tr>
				<th>카드번호</th>
				<td>
					<input type="text" name="cardno" id="cardno" size="15" maxlength="16" value="" class="form-control frm_input" style="width:200px;" onkeypress="return num_check(event)"><span id="notice"></span>
					* '-'없이 숫자만 입력하세요
				</td>
			</tr>
			<tr>
				<th>유효기간</th>
				<td>
					<select name="expmon" class="form-control frm_input">
						<option value="01">01</option>
						<option value="02">02</option>
						<option value="03">03</option>
						<option value="04">04</option>
						<option value="05">05</option>
						<option value="06">06</option>
						<option value="07">07</option>
						<option value="08">08</option>
						<option value="09">09</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
					</select>월/
					<select name="expyear" class="form-control frm_input">
					<? 
						$y = date('Y');
						for($i=$y; $i<=$y+10; $i++) {
					?>
						<option value="<?=$i?>" selected=""><?=substr($i,2)?> </option>
					<?	} ?>
					</select>년					
				</td>
			</tr>
			<tr>
				<th>할부</th>
				<td>
					<input type="hidden" name="halbu" value="">
					<select name="installment" class="form-control frm_input">
						<option value="00" selected="">일시불</option>
						<option value="02">02개월</option>
						<option value="03">03개월</option>
						<option value="04">04개월</option>
						<option value="05">05개월</option>
						<option value="06">06개월</option>
						<option value="07">07개월</option>
						<option value="08">08개월</option>
						<option value="09">09개월</option>
						<option value="10">10개월</option>
						<option value="11">11개월</option>
						<option value="12">12개월</option>
					</select>		
				</td>
			</tr>
			<!-- <tr>
				<th>주민번호 앞자리</th>
				<td>
					<input type="text" name="birth_ym"  size="15" maxlength="6" class="form-control frm_input" style="width:70px;text-align:center" value="" onkeypress="return num_check(event)">&nbsp;&nbsp;&nbsp;&nbsp;* 숫자만 입력하세요.
				</td>
			</tr>
			<tr>
				<th>비밀번호 앞 두자리</th>
				<td>
					<input type="text" name="f2code"  size="15" maxlength="2" class="form-control frm_input" style="width:40px;text-align:center" value="" onkeypress="return num_check(event)">**&nbsp;&nbsp;&nbsp;&nbsp;* 숫자만 입력하세요.
				</td>
			</tr> -->
			<tr>
				<th>승인금액</th>
				<td>
					<input type="text" name="amount"  size="15" maxlength="9" class="form-control frm_input" style="width:200px;text-align:right" value="<?=$product['sc_price']?>" onkeypress="return num_check(event)" readonly="true">원&nbsp;&nbsp;&nbsp;&nbsp;* 숫자만 입력하세요.
				</td>
			</tr>
			<tr>
				<th>상품명</th>
				<td>
					<input type="text" name="goodname" value="<?=$product['sg_name']?>-<?=$product['sc_name']?>" size="30" maxlength="30" class="form-control frm_input" style="width:200px;">*특수문자사용금지
				</td>
			</tr>
			<tr>
				<th>주문번호</th>
				<td>
					<input type="text" name="ordernumber" value="<?=date('Ymdhis').$sb_no?>" size="30" maxlength="50" class="form-control frm_input" style="width:200px;" readonly="">&nbsp;*자동생성처리
				</td>
			</tr>
			<tr>
				<th>주문자명</th>
				<td>
					<input type="text" name="ordername" value="<?=$mb['mb_name']?>" size="30" maxlength="25" class="form-control frm_input" style="width:200px;">&nbsp;*특수문자사용금지
				</td>
			</tr>
			<tr>
				<th>휴대폰</th>
				<td>
					<input type="text" name="phoneno" value="<?=$mb['mb_hp']?>" size="15" maxlength="20" class="form-control frm_input" style="width:200px;" onkeypress="return num_check(event)">&nbsp;* '-'없이 숫자만 입력하세요
				</td>
			</tr>
			
		</tbody>
	</table>
	<div style="margin-top:20px">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="tb02">
        <tbody>
        <tr>
        	<th width="120px">서비스이용약관</th>
        	<td>
            	<div style="border:1px solid #e8e8e8;width:100%;height:80px;overflow-y:scroll;overflow-x:hidden"><?=nl2br(stripslashes($cfg['tdc_provision_user']))?></div>
				<label><input type="checkbox" name="agree" value="1"> 약관동의</label>
        	</td>
        </tr>
		</tbody>
		</table>
	</div>
	<div class="btn_box ac">
		<a href="javascript:checkForm('manual');void(0);" class="btn_b02" style="width: 80%;">승인요청</a>
    	
	</div>
	</div>
	</form>
	<?
		} else { 
			
	?>
	
	<form name="service_pay_form" method="post" action="./service_buy_process.php">
	<input type="hidden" name="type" value="auto">
	<input type="hidden" name="proc" value="addServiceBuy">
	<input type="hidden" name="mb_id" value="<?=$_SESSION['ss_mb_id']?>">
	<input type="hidden" name="from_ajax" value="">
	<input type="hidden" name="sc_no" value="<?=$product['sc_no']?>">
	<div class="pay-method-container">
    	
    	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="tb02">
        <tbody>
        <tr>
        	<th width="120px">총 결제금액</th>
        	<td>
            	<span id="disp_pay_price"><?=number_format($product['sc_price'])?></span>원
        	</td>
        </tr>
        <tr>
        	<th width="120px">결제방법</th>
        	<td>
        		<input type="radio" name="pay_method" id="paymethod1" value="card" onclick="$('.account_info').hide()" disabled><label for="paymethod1">카드</label> 
                <input type="radio" name="pay_method" id="paymethod2" onclick="$('.account_info').hide()" value="virtual" disabled><label for="paymethod2">가상계좌</label> 
                <input type="radio" name="pay_method" id="paymethod3" onclick="$('.account_info').hide()" value="iche" disabled><label for="paymethod3">실시간이체</label> 
                <input type="radio" name="pay_method" id="paymethod4" onclick="$('.account_info').show()" value="account" checked><label for="paymethod4">무통장</label> 
        	</td>
        </tr>
        <tr class="account_info" style="display: none;">
        	<th width="120px">입금자명</th>
        	<td>
        		<input type="text" name="payer_name" class="frm_input" value="">
        	</td>
        </tr>
        <tr class="account_info" style="display: none;">
        	<th>입금은행</th>
        	<td>
        		<select name="bank_account" class="frm_input">
				<? for($i=0; $i<count($accounts); $i++) { ?>
            		<option value="<?=$accounts[$i]?>"><?=$accounts[$i]?></option>
				<? } ?>
        		</select>
        	</td>
        </tr>
		<tr>
			<th>상품명</th>
			<td>
				<input type="text" name="goodname" value="<?=$product['sg_name']?>-<?=$product['sc_name']?>" size="30" maxlength="30" class="form-control frm_input" style="width:200px;">*특수문자사용금지
			</td>
		</tr>
		<tr>
			<th>주문번호</th>
			<td>
				<input type="text" name="ordernumber" value="<?=date('Ymdhis').$sb_no?>" size="30" maxlength="50" class="form-control frm_input" style="width:200px;" readonly="">&nbsp;*자동생성처리
			</td>
		</tr>
		<tr>
			<th>주문자명</th>
			<td>
				<input type="text" name="ordername" value="<?=$mb['mb_name']?>" size="30" maxlength="25" class="form-control frm_input" style="width:200px;">&nbsp;*특수문자사용금지
			</td>
		</tr>
		<tr>
			<th>휴대폰</th>
			<td>
				<input type="text" name="phoneno" value="<?=$mb['mb_hp']?>" size="15" maxlength="20" class="form-control frm_input" style="width:200px;" onkeypress="return num_check(event)">&nbsp;* '-'없이 숫자만 입력하세요
			</td>
		</tr>
        </tbody>
        </table>
	</div>
	<div style="margin-top:20px">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="tb02">
        <tbody>
        <tr>
        	<th width="120px">서비스이용약관</th>
        	<td>
            	<div style="border:1px solid #e8e8e8;width:100%;height:80px;overflow-y:scroll;overflow-x:hidden"><?=nl2br(stripslashes($cfg['tdc_provision_user']))?></div>
				<label><input type="checkbox" name="agree" value="1"> 약관동의</label>
        	</td>
        </tr>
		</tbody>
		</table>
	</div>
	<div class="btn_box ac">
		<a href="javascript:checkForm();void(0);" class="btn_b02" style="width: 80%;">승인요청</a>
	</div>
	</form>
	<!-- 결제모듈 -->
	<? //include dirname(__FILE__)."/pg/kcp/pay_pc/pay.php";?>
	<? include dirname(__FILE__)."/pg/allat/approval.html";?>
	<? } ?>


</div>


<script language="Javascript">

function num_check(evt){
	var code = evt.which?evt.which:event.keyCode;
	if(code < 48 || code > 57){
		return false;
	}
}

//Extra Validation with the Luhn Algorithm
function luhn(cardnumber) {
	//카드 번호 숫자들로 배열 생성
   
	/*
	var getdigits = /\d/g;
	var digits = [];
	while(match = getdigits.exec(cardnumber)){
		digits.push(parseInt(match[0], 10));
	}
	*/
	var digits = cardnumber.split('');
	for (var i = 0; i < digits.length; i++) {
		digits[i] = parseInt(digits[i], 10);
	}  

	//그 배열을 대상으로 룬 알고리즘 실행
	var sum = 0;
	var alt = false;
	for (var i = digits.length - 1; i >= 0 ; i-- ) {
		if (alt) {
			digits[i] *= 2;
			if(digits[i] > 9) {
				digits[i] -= 9;
			}
		}
		sum += digits[i];
		alt = !alt;
	}
	//결국 카드 번호는 잘못 되었음이 밝혀짐
	if(sum % 10 == 0) {
		return true; //document.getElementById("notice").innerHTML += '; 룬 검사 성공';
	} else {
		return false; //document.getElementById("notice").innerHTML += '; 룬 검사 실패';
	}

}

function validatecardnumber(cardnumber) {
   
	//빈칸과 대시 제거
	cardnumber = cardnumber.replace(/[ -]/g,'');

	//카드 번호가 유효한지 검사
	//정규식이 캡처 그룹들 중 하나에 들어있는 숫자를 캡처
	var match = /^(?:(94[0-9]{14})|(4[0-9]{12}(?:[0-9]{3})?)|(5[1-5][0-9]{14})|(6(?:011|5[0-9]{2})[0-9]{12})|(3[47][0-9]{13})|(3(?:0[0-5]|[68][0-9])[0-9]{11})|((?:2131|1800|35[0-9]{3})[0-9]{11}))$/.exec(cardnumber);
   
	if(match) {

		//정규식 캡처 그룹과 같은 순서로 카드 종류 나열
		var types = ['BC', 'Visa', 'MasterCard', 'Discover', 'American Express', 'Diners Club', 'JCB'];

		//일치되는 캡처 그룹 검색
		//일치부 배열의 0번째 요소 (전체 일치부중 첫 일치부)를 건너뜀
		for(var i = 1; i < match.length; i++) {
			if(match[i]) {
				//해당 그룹에 대한 카드 종류를 표시
				document.getElementById('notice').innerHTML = types[i-1];
				break;
			}
		}

		if(luhn(cardnumber)) {
			return true;
		} else {
			return false;
		}

	} else {
		document.getElementById('notice').innerHTML = '(잘못된 카드 번호)';
		return false;
	}
}



function checkForm(f) {

	var f = document.service_pay_form;
	var proc = f.type.value;

	if(!f.agree.checked) {
		alert("서비스이용약관에 동의하셔야 진행 가능합니다.");
		f.agree.focus();
		return false;
	}

	if(!f.ordername.value) {
		alert("주문자명을 입력해 주세요.");
		f.ordername.focus();
		return false;
	}

	if(!f.phoneno.value) {
		alert("주문자 휴대폰번호를 입력해 주세요.");
		f.phoneno.focus();
		return false;
	}

    if(proc == 'auto') {

		// 기본정보셋팅
		var pf = document.fm;

		fm.allat_card_yn.value = 'N';
		fm.allat_bank_yn.value = 'N';
		fm.allat_vbank_yn.value = 'N';
		fm.allat_hp_yn.value = 'N';
		fm.allat_ticket_yn.value = 'N';
		fm.target = "_self";

		switch(f.pay_method.value) {
			case 'card' : fm.allat_card_yn.value = 'Y'; break;
			case 'virtual' : fm.allat_vbank_yn.value = 'Y'; break;
			case 'iche' : fm.allat_bank_yn.value = 'Y'; break;

		}

		
		pf.allat_product_nm.value = f.goodname.value;
		pf.allat_amt.value = '<?=$product['sc_price']?>';
		pf.allat_pmember_id.value = '<?=$_SESSION['ss_mb_id']?>';
		pf.allat_buyer_nm.value = f.ordername.value;
		pf.allat_recp_nm.value = f.ordername.value;
		//pf.buyr_tel2.value = f.phoneno.value;

		if($('input[name="pay_method"]:checked').val() == 'account') {
			if(!f.payer_name.value) {
				alert("입금자명을 입력해 주세요.");
				f.payer_name.focus();
				return false;
			}

			f.submit();
		} else {

			f.from_ajax.value = 'true';
			$.ajax({
			   type: "POST",
			   url: './service_buy_process.php',
			   dataType: 'json',
			   data: $(f).serialize(), // serializes the form's elements.
			   success: function(data)
			   {
				   if(data.sb_no) {
					   pf.allat_product_cd.value = data.sc_no;
					   pf.allat_order_no.value = data.sb_no;
					   //alert(data); // show response from the php script.
					   //jsf__pay(pf); // kcp
					   ftn_approval(pf);
				   } else {
						alert("오류가 발생하였습니다.\n창을 닫고 다시 시도해 주세요.");
				   }
			   }
			 });

		}
	} else {
		/*
		if(!validatecardnumber(f.cardno.value)) {
			alert("유효한 신용카드 번호가 아닙니다.");
			return false;
		}*/
		f.submit();
	}
	
}

$(document).ready(function() {
	$('input[name="pay_method"]:checked').trigger('click');
});


</script>
