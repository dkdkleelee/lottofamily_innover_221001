<?php
include_once("./_common.php");

$cur = 4;
include_once($_dir['root']."head_05.php");

add_javascript(G5_POSTCODE_JS, 0);    //다음 주소 js


//▶ pagination url
$param = getParameters(array('sg_no'));
$page_url = "ia_service_buy.php?".$param;

$_GET['sg_no'] = !$_GET['sg_no'] ? "1" : $_GET['sg_no'];

// 서비스 환경설정
$cfg = $auction->getServiceConfig();
$accounts = explode("\r\n", $cfg['ic_account']);

// termservice
$termService = new AceTermAreaService($auction->dft_config['table']);

$product_buy = $termService->getServiceBuy($_GET['sb_no']);

// service buy detail
$detail = unserialize($product_buy['sb_detail']);

// product type
$product_type = $termService->getTermServiceGrade();

?>

<link type="text/css" href="<?=$_url['modules']?>/inauction/css/form.css" rel="stylesheet" />
<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css" rel="stylesheet" />
<script src="/js/jquery-1.8.3.min.js"></script>
<script src="<?=$_url['lib']?>/js/jquery-ui-1.8.7.custom.min.js" ></script>
<script src="<?=$_url['lib']?>/js/jquery.ui.datepicker-ko.js" ></script>



<div id="content">
    <div class="btitle">
        <div class="btitle_text2">서비스 결제</div>
        <div class="btitle_locate">홈 &gt; 서비스 결제</div>
    </div>
    <div class="btitle_line"></div>


	
<form name="service_pay_form" method="post" action="./ia_service_buy_process.php">
<input type="hidden" name="proc" value="servicePay">
<input type="hidden" name="sb_no" value="<?=$product_buy['sb_no']?>">
	<div class="selected-service-container">
		<? 
    		for($i=0; $i<count($product_type); $i++) {
    		
    	        if($detail['sg_no'] == $product_type[$i])	
        ?>
		<div class="help_text"><i class="fa fa-file-text-o fa-lg f_blue"></i> <?=$product_type[$i]['sg_name']?></div>
		<table id="selceted-type-container-<?=$product_type[$i]['sg_no']?>" class="selected-items-table dataTable" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:30px">
		<tr>
			<th width="120px">구분</th>
			<th width="130px">시/도</th>
			<th width="200px">구/군</th>
			
			<th width="130px">가격 / 월</th>
		</tr>
		
		<?      
    		for($j=0; $j<count($detail); $j++) {
                if($detail[$j]['sg_no'] == $product_type[$i]['sg_no']) {		
        ?>
		<tr>
			<td><?=$detail[$j]['sg_title']?></td>
			<td><?=$detail[$j]['sido']?></td>
			<td><?=$detail[$j]['gugun']?></td>
			<td><?=number_format($detail[$j]['price'])?></td>
		</tr>

		
		
		<?      }
		    }
        ?>
		</table>
		<? } ?>
	</div>
	<br>
	<div class="total-price-container">
		<table class="dataTable" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:30px">
		<tr>
			<th>개월</th>
			<th width="490px">총 결제금액</th>
		</tr>
		<tr>
			<td>
			<?
				$tmp = explode("/",$termService->service_config['sc_discount_per_month']);
				for($i=0; $i<count($tmp); $i++) {
					$tmp2 = explode(":", $tmp[$i]);
					
					if($tmp2[0] == $product_buy['sb_term']) {
			?>
				<span style="margin-right:10px">
					<input type="radio" class="term_month" onClick="calcTotalPrice()" name="term_month" value="<?=$tmp2[0]?>" data-discount-rate="<?=$tmp2[1]?>" id="term_<?=$i?>" <?=($tmp2[0]==$product_buy['sb_term']) ? 'checked' : ''?>><label for="term_<?=$i?>"><?=$tmp2[0]?>개월 (<?=$tmp2[1]?>%할인)</label>
				</span>

			<?
					}
				}
			?>
			</td>
			<td class="price-text">
				<input type="text" style="width:80px" name="price_per_month" class="price-per-month ar nb" value="<?=number_format($product_buy['sb_price']*$product_buy['sb_term'])?>">원 X
				<input type="text" style="width:20px" name="term" class="term ar nb" value="<?=$product_buy['sb_term']?>">개월 -
				<input type="text" style="width:80px" name="price_discount" class="price-discount ar nb" value="<?=number_format($product_buy['sb_discount_price'])?>">원(할인) =
				<input type="text" style="width:120px" name="price_total" class="price-total ar nb" value="<?=number_format($product_buy['sb_total_price'])?>">원
			</td>
		</tr>
		</table>
	</div>
	<div class="pay-method-container">
    	<div class="help_text"><i class="fa fa-krw fa-lg f_blue"></i>  결제정보</div>
    	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="tb02">
        <tbody>
        <tr>
        	<th width="120px">총 결제금액</th>
        	<td>
            	<span id="disp_pay_price"><?=number_format($product_buy['sb_total_price']+$product_buy['sb_vat'])?></span>원 (VAT 포함)
        	</td>
        </tr>
        <tr>
        	<th width="120px">결제방법</th>
        	<td>
        		<input type="radio" name="pay_method" id="paymethod1" value="card" onclick="$('.account_info').hide()" checked=""><label for="paymethod1">카드</label> 
                <input type="radio" name="pay_method" id="paymethod2" onclick="$('.account_info').hide()" value="virtual"><label for="paymethod2">가상계좌</label> 
                <input type="radio" name="pay_method" id="paymethod3" onclick="$('.account_info').hide()" value="iche"><label for="paymethod3">실시간이체</label> 
                <input type="radio" name="pay_method" id="paymethod4" onclick="$('.account_info').show()" value="account"><label for="paymethod4">무통장</label> 
        	</td>
        </tr>
        <tr class="account_info" style="display: none;">
        	<th width="120px">입금자명</th>
        	<td>
        		<input type="text" name="payer_name" class="input" value="">
        	</td>
        </tr>
        <tr class="account_info" style="display: none;">
        	<th>입금은행</th>
        	<td>
        		<select name="od_bank_account" class="input">
				<? for($i=0; $i<count($accounts); $i++) { ?>
            		<option value="<?=$accounts[$i]?>"><?=$accounts[$i]?></option>
				<? } ?>
        		</select>
        	</td>
        </tr>
        </tbody>
        </table>
	</div>
	<div class="warnning_text"><i class="fa fa-exclamation-triangle fa-lg f_blue"></i> 회원권 이용 승인 후 청약철회가 불가능 합니다. 자세한 내용은 홈페이
지 하단의 이용약관을 확인해 주세요.</div>
	<div class="btn_box ac">
    	<input class="button_mid_blue" type="button" value="구매신청" onClick="checkForm()">&nbsp;&nbsp;&nbsp;
		<input class="button_mid_red" type="button" value="취소" onClick="location.href='./ia_service_buy.php'">
	</div>

	
</form>
<!-- 결제모듈 -->
<? include dirname(__FILE__)."/pg/allthegate/pay.php";?>
</div>


<script language="Javascript">

function checkForm() {
    var f = document.service_pay_form;
        
	if($('input[name="pay_method"]:checked').val() == 'account') {
    	if(!f.payer_name.value) {
    		alert("입금자명을 입력해 주세요.");
    		f.payer_name.focus();
    		return false;
        }

		f.submit();
	} else {
		Pay(frmAGS_pay);
	}
	
	
}


</script>
<?php
include_once($_dir['root']."tail.php");
?>