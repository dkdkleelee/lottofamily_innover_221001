<?php
define('G5_IS_ADMIN', true);
$sub_menu = "200100";
/**
 * Created by PhpStorm.
 * User: acerunner
 * Date: 16. 2. 14.
 * Time: 오후 5:46
 */

include_once("./_common.php");

use \Acesoft\Common\Utils;
use \Acesoft\LottoApp\Member\User;
use \Acesoft\LottoApp\TermService;
use \Acesoft\LottoApp\LottoServiceConfig;
use \Acesoft\LottoApp\LottoService;

// set param and paging url
$param = Utils::getParameters();
$param1 = Utils::getParameters(array('page','url'));
$return_url = "http://".$_SERVER[HTTP_HOST].$_SERVER[PHP_SELF]."?".$param1;
$pay_return_url = "http://".$_SERVER[HTTP_HOST].$_SERVER[PHP_SELF]."?";

// 유져
$user = new User();
$data = $user->getUser($_GET['mb_id']);

$tmp_interest = explode(',', $data['mb_interest']);

auth_check($auth[$sub_menu], 'w');

// 수정시 전처리
if($_GET['mb_id']) {
	// 전화번호 - 구분 없는경우
	$data['mb_tel'] = Utils::arrangeTelNumber($data['mb_tel']);
	$data['mb_fax'] = Utils::arrangeTelNumber($data['mb_fax']);
	$data['mb_hp'] = Utils::arrangeHPNumber($data['mb_hp']);

}

// 이용중서비스
$termService = new TermService();
$_GET['s_sg_no'] = '';
$_GET['s_mb_id'] = $data['mb_id'] ? $data['mb_id'] : '-';
$data_service = $termService->getTermServiceUseList();



//▶ 등급별 발급설정갯수
$lottoServiceConfig = new LottoServiceConfig();
$serviceConfig = $lottoServiceConfig->getConfig();

$extract_count_per_grade = unserialize($serviceConfig['lc_extract_count']);

// 누적당첨금액
$lottoService = new LottoService();
$accumulated_result = $lottoService->getAccumulatedWinRecords($_GET['s_mb_id']);

// 서비스목록
$termService = new TermService();
$service_list = $termService->getServiceList();


// 기한초과카드정보 마스킹
$termService->setMaskToCardInfo();

// 구매신청리스트
$_GET['s_mb_id'] = trim($data['mb_id']);
$pageLimit = 5;
$data_buy = $termService->getTermServiceBuyList($_GET['page'], $list_url);


$cfg = $termService->getServiceConfig();
$accounts = explode("\r\n", $cfg['tdc_accounts']);

if($_GET['url']) {
	$_SESSION['from_url'] = $_GET['url'];
}

$g5['title'] = $_GET['mb_id'] ? "회원 정보 수정" : "회원 등록";
include_once(G5_ADMIN_PATH."/admin.head.php");

?>

<link rel="stylesheet" href="../css/custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/admin.custom.style.css" type="text/css">
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script src="../js/plugins/jquery-number/jquery.number.js"></script>
<script src="../js/common.js"></script>
<script src="../js/jquery.ui.datepicker-ko.js"  charset="utf-8"></script>
<link rel="stylesheet" href="../js/datetimepicker/jquery.datetimepicker.css" type="text/css" />
<script type="text/javascript" src="../js/datetimepicker/jquery.datetimepicker.js" charset="utf-8"></script>

<div class="content_wrap">
	<form name="member_form" id="member_form" method="POST" encType="multipart/form-data" action="./lotto_member_process.php">
	<input type="hidden" name="proc" value="<?=$_GET['mb_id'] ? 'modifyUser' : 'addUser'?>">
	<input type="hidden" name="mb_id" id="mb_id" value="<?=$_GET['mb_id']?>">
	<input type="hidden" name="mb_tm_id" value="<?=$data['mb_tm_id']?>">
	<input type="hidden" name="url" value="<?=$_GET['url']?>">
	<input type="hidden" name="return_url" value="<?=$return_url?>">
	<div class="btitle">
		<div style="padding-left:10px"><i class="fa fa-folder-o"></i> 기본정보</div>
	</div>
	<table align="center" width="100%" border="0" cellspacing="0" cellpadding="0" class="tb02">
	<tr>
		<th width="140">구분</th>
		<td width="40%">
			<select name="mb_level" id="mb_level" class="frm_input">
			<? foreach($user->getUserType() as $key => $value) { ?>
				<option value="<?=$key?>" <?=$data['mb_level'] == $key ? 'selected' : ''?>><?=$value?></option>
			<? } ?>
			</select>
		</td>
		<th width="140">등록일자</th>
		<td>
			<?=$data['mb_datetime']?>
		</td>
	</tr>
	<tr class="mb">
		<th width="140">아이디</th>
		<td>
			<? if($_GET['mb_id']) { ?>
				<?=$data['mb_id']?>
			<? } else { ?>
				<input type="text" name="mb_id" class="frm_input <?=!$_GET['mb_id'] ? 'required' : ''?>" id="reg_mb_id" placeholder="아이디" minlength="3" maxlength="20" autocomplete="false" required>
				<span id="msg_mb_id"></span>
			<? } ?>
		</td>
		<th width="140">패스워드</th>
		<td>
			<input type="password" name="mb_password"  class="frm_input <?=!$_GET['mb_id'] ? 'required' : ''?>" placeholder="패스워드" id="reg_mb_password" autocomplete="new-password" <?=!$_GET['mb_id'] ? 'required' : ''?>>
		</td>
	</tr>
	
	<tr>
		<th width="140">이름(가입자명)</th>
		<td>
			<input type="text" name="mb_name" class="frm_input" value="<?=$data['mb_name']?>" placeholder="이름(가입자명)" required>
		</td>
		
		<th width="140">Email</th>
		<td>
			<input type="text" class="frm_input" name="mb_email" size="30" value="<?=$data['mb_email']?>" placeholder="Email"	>
		</td>
	</tr>
	<tr>
		<th width="140">생년월일</th>
		<td colspan="3">
			<input type="text" name="mb_birth" class="frm_input datepicker" value="<?=$data['mb_birth']?>" placeholder="생년월일" >
		</td>
		
	</tr>
	<tr>
		<th width="140">전화번호</th>
		<td>
			<? $tmp_tel = explode("-", $data['mb_tel']);?>
			<input type="text" name="mb_tel1" size="5" maxlength="4" value="<?php echo $tmp_tel[0] ?>" id="reg_mb_tel1" class="frm_input" maxlength="20" placeholder="전화번호"> -
			<input type="text" name="mb_tel2" size="6" maxlength="4" value="<?php echo $tmp_tel[1] ?>" id="reg_mb_tel2"  class="frm_input" maxlength="20"placeholder="전화번호"> -
			<input type="text" name="mb_tel3" size="6" maxlength="4" value="<?php echo $tmp_tel[2] ?>" id="reg_mb_tel3" class="frm_input" maxlength="20"placeholder="전화번호">
		</td>
		<th width="140">팩스번호</th>
		<td>
			<? $tmp_fax = explode("-", $data['mb_fax']);?>
			<input type="text" name="mb_fax1" size="5" maxlength="4" value="<?php echo $tmp_fax[0] ?>" id="reg_mb_fax1" <?php echo $config['cf_req_fax']?"required":""; ?> class="frm_input <?php echo $config['cf_req_fax']?"required":""; ?>" maxlength="20"> -
			<input type="text" name="mb_fax2" size="6" maxlength="4" value="<?php echo $tmp_fax[1] ?>" id="reg_mb_fax2" <?php echo $config['cf_req_fax']?"required":""; ?> class="frm_input <?php echo $config['cf_req_fax']?"required":""; ?>" maxlength="20"> -
			<input type="text" name="mb_fax3" size="6" maxlength="4" value="<?php echo $tmp_fax[2] ?>" id="reg_mb_fax3" <?php echo $config['cf_req_fax']?"required":""; ?> class="frm_input <?php echo $config['cf_req_fax']?"required":""; ?>" maxlength="20">
		</td>
	</tr>
	
	<tr>
		<th width="140">휴대폰번호</th>
		<td>
			<? $tmp_hp = explode("-", $data['mb_hp']);?>
			<select name="mb_hp1" id="reg_mb_hp1" class="frm_input" style="width:65px">
				<option value="010" <?=$tmp_hp[0] == '010' ? 'selected' : ''?>>010</option>
				<option value="011" <?=$tmp_hp[0] == '011' ? 'selected' : ''?>>011</option>
				<option value="016" <?=$tmp_hp[0] == '016' ? 'selected' : ''?>>016</option>
				<option value="017" <?=$tmp_hp[0] == '017' ? 'selected' : ''?>>017</option>
				<option value="018" <?=$tmp_hp[0] == '018' ? 'selected' : ''?>>018</option>
				<option value="019" <?=$tmp_hp[0] == '019' ? 'selected' : ''?>>019</option>
			</select> -
			<input type="text" name="mb_hp2" value="<?php echo $tmp_hp[1] ?>" id="reg_mb_hp2" class="frm_input"  size="5" maxlength="4"> -
			<input type="text" name="mb_hp3" value="<?php echo $tmp_hp[2] ?>" id="reg_mb_hp3" class="frm_input"  size="5" maxlength="4">

		</td>
		<th width="140">메일수신</th>
		<td>
			<label><input type="checkbox" name="mb_mailling" value="1" id="reg_mb_mailling" <?=($data['mb_mailling'])?'checked':''; ?>> 수신합니다.</label>
		</td>
	</tr>
	<tr>
		
		<th width="140">SMS수신</th>
		<td>
			<label><input type="checkbox" name="mb_sms" value="1" id="reg_mb_sms" <?=($data['mb_sms'])?'checked':''; ?>> 수신합니다.</label>
		</td>
		<th width="140">가입일 / 수정일</th>
		<td colspan="3">
			<?=$data['mb_datetime']?> / <?=$data['mb_update_date']?>
		</td>
	</tr>
	</table>
	<br /><br />
	<div class="btitle">
		<div style="padding-left:10px"><i class="fa fa-folder-o"></i> 추가정보</div>
	</div>


	<div>
		 <h5 class="title" style="padding-left:30px"><i class="fa fa-asterisk fa-lg"></i> 누적 당첨내역</h5>
	</div>
	<div class=" ar">
		<button type="button" class="as-btn small blue" onclick="openUserWinList('<?=$data['mb_id']?>')"><i class="fa fa-search"></i> 발급/당첨내역 확인</button>
	</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tb02">
	<?php for($i=1; $i<=5; $i++) { ?>
		<th width="5%"><?=$i?>등</th>
		<td width="15%" style="text-align:right">
			<div style="padding:3px;border-bottom:1px solid #e8e8e8"><?=number_format($accumulated_result[$i]['cnt'])?>회</div>
			<div style="padding:3px;color:#3366cc"><?=number_format($accumulated_result[$i]['prize'])?>원</div>
		</td>
	<?php } ?>
	</table>
	<br />

	<table align="center" width="100%" border="0" cellspacing="0" cellpadding="0" class="tb02">
	<tr>
		<th width="100px">문자발송요일</th>
		<td>
			<? for($i=1; $i<6; $i++) { ?>
			<label><input type="radio" name="mb_extract_weekday" value="<?=$i?>" <?=$data['mb_extract_weekday'] == $i ? 'checked' : ''?>><?=$termService->config['weekdays_name'][$i]?>&nbsp;&nbsp;&nbsp;</label>
			<? } ?>
		</td>
		<th width="140">주당 평균구매</th>
		<td width="40%">
			<?=number_format($data['mb_average_game_number'])?>게임 구매
		</td>
	</tr>
	<tr>
		<th width="140">상담이력</th>
		<td width="40%">
			<? 
				foreach($user->getConsultStatus() as $key => $value) { 
					if($key != 'na') {
			?>
			<label><input type="radio" name="mb_cousult_status" value="<?=$key?>" <?=$data['mb_cousult_status'] == $key ? 'checked' : ''?>><?=$value?>&nbsp;&nbsp;&nbsp;</label>
			<?	
					}
				}
			?>
		</td>
		<th width="140">추가발급</th>
		<td width="40%">
			추출번호 <input type="text" name="issue_count" id="issue_count" class="frm_input">개  <button type="button" class="as-btn small blue" onclick="addNumbersToQueue()"><i class="fa fa-send"></i> 추가발급</button>
		</td>
	</tr>
	<tr>
		<th width="140">발급갯수 변경</th>
		<td colspan="3">
		   <? // 현재 주당 발급갯수
				$extract_per_week = $data_service['list'][0]['leftDays'] > 0 ? $extract_count_per_grade[$data_service['list'][0]['sg_no']] : $extract_count_per_grade[0];

		   ?>

		   주당 <?=$extract_per_week?>개 /
		   조정 : <select name="mb_extract_per_week" id="mb_extract_per_week" class="frm_input">
				<option value="0" <?=$data['mb_extract_per_week'] == 0 ? 'selected' : ''?>>기본설정에 따름</option>
				<option value="10" <?=$data['mb_extract_per_week'] == 10 ? 'selected' : ''?>>10개/주</option>
				<option value="20" <?=$data['mb_extract_per_week'] == 20 ? 'selected' : ''?>>20개/주</option>
				<option value="30" <?=$data['mb_extract_per_week'] == 30 ? 'selected' : ''?>>30개/주</option>
				<option value="40" <?=$data['mb_extract_per_week'] == 40 ? 'selected' : ''?>>40개/주</option>
				<option value="50" <?=$data['mb_extract_per_week'] == 50 ? 'selected' : ''?>>50개/주</option>
			</select>개
			<button type="button" class=" as-btn small green" style="color:white" onClick="modifyExtractNum('<?=$data['mb_id']?>');">수정</button>
			<br />- 개별적용됩니다.
		</td>
	</tr>
	</table>
	
	<div style="width:100%;text-align:center;padding-bottom:30px">
		<button type="submit" class="as-btn small blue"><i class="fa fa-check"></i> 확인</button>
		<button type="button" class="as-btn small red" onclick="history.back(-1);void(0);"><i class="fa fa-close"></i> 취소</button>
		<button type="button" class="as-btn small green" onclick="location.href='<?=$_SESSION['from_url']?>'"><i class="fa fa-list"></i> 목록</button>
	</div>
	</form>


	<form name="member_form" id="member_form" method="POST" encType="multipart/form-data" action="./lotto_member_process.php">
	<input type="hidden" name="proc" value="addMemo">
	<input type="hidden" name="mb_id" id="mb_id" value="<?=$_GET['mb_id']?>">
	<input type="hidden" name="mb_tm_id" value="<?=$data['mb_tm_id']?>">
	<input type="hidden" name="url" value="<?=$_GET['url']?>">
	<input type="hidden" name="return_url" value="<?=$return_url?>">
	<table align="center" width="100%" border="0" cellspacing="0" cellpadding="0" class="tb02">
	<tr>
		<th width="140px">메모</th>
		<td width="">
			<div style="width:700px;">
				<textarea name="mo_memo" style="width:700px;height:140px"></textarea>
			</div>
			
			<div style="width:500px">
				<label><input type="checkbox" name="mo_schedule" value="1"> 알림등록</label>
				<label>[ 알림일시 등록 : <input type="text" name="mo_schedule_datetime" autocomplete="off" style="width:150px" class="frm_input schedule_datetime" value=""> ]
			</div>
			
			<div style="clear:both"></div>
			<div style="width:100%;height:200px;overflow-y:scroll;border:1px solid #eeeeee;margin-top:5px">
				<ul style="width:100%;">
				<? 
					$rows_memo = $user->getMemoList($data['mb_id']);
					foreach($rows_memo as $key => $value) {
				?>
					<li style="padding:5px;border-bottom:1px dotted #e8e8e8">
						<?=nl2br($value['mo_memo'])?><div style="color:#6666ff"><?=$value['mo_datetime']?> [<?=$value['mo_mb_id']?>] 
						<?=$value['mo_schedule'] ? '<span style="padding-left:50px"><i class="fa fa-bell"></i> '.$value['mo_schedule_datetime']."</span>" : ''?>

						<div style="float:right"><button type="button" class=" as-btn tiny red" style="color:white" onClick="deleteMemo('<?=$value['mo_no']?>');">삭제</button></div>
						</div>
						
					</li>
				<?
					}
				?>
				</ul>
			</div>
		</td>
	</tr>
	</table>
	<br><br />
	<div style="width:100%;text-align:center;padding-bottom:30px">
		<button type="submit" class="as-btn small blue"><i class="fa fa-check"></i> 확인</button>
		<button type="button" class="as-btn small red" onclick="history.back(-1);void(0);"><i class="fa fa-close"></i> 취소</button>
		<button type="button" class="as-btn small green" onclick="location.href='<?=$_SESSION['from_url']?>'"><i class="fa fa-list"></i> 목록</button>
	</div>
	</form>
	<!--// 회원기본정보 및 상담정보 -->

<? if($data['mb_id']) { ?>
	<br /><br />
	<div class="btitle">
		<div style="padding-left:10px"><i class="fa fa-folder-o"></i> 서비스이용현황</div>
	</div>
	<div>
		 <h5 class="title" style="padding-left:30px"><i class="fa fa-asterisk fa-lg"></i> 구매신청목록</h5>
	</div>
	
	<div id="addServiceFormContainer">
	<form name="addServiceForm" method="post" action="./term_service_buy_process.php">
	<input type="hidden" name="proc" value="addServiceBuy">
	<input type="hidden" name="mb_id" value="<?=$data['mb_id']?>">
	<input type="hidden" name="tm_id" value="<?=$_SESSION['ss_mb_id']?>">
	<input type="hidden" name="return_url" value="<?=$return_url?>">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
		<th>서비스</th>
		<th>결제방법</th>
		<th >카드정보</th>
		<th >입금자명</th>
		<th>입금계좌</th>
		<th width="100px">등록</th>
	</tr>
	<tr>
		<td>
			<select name="sc_no" class="frm_input" title="서비스" onChange="getServicePrice(this.value);" required>
				<option value="">선택</option>
				<? foreach($service_list as $row) { ?>
				<option value="<?=$row['sc_no']?>" title="(<?=$row['sg_grade']?> / <?=$row['sc_term']?><?=$row['sc_term_type'] == 'month' ? '개월' : '일'?>)"><?=$row['sc_name']?> / <?=number_format($row['sc_price'])?>원 </option>
				<? } ?>
			</select><br />
			금액수정: <input type="text" name="new_price" id="new_price" class="frm_input" placeholder="숫자만 입력" style="width:150px;margin:2px">원
			<br />
			미수금: <input type="text" name="sb_not_paid_price" id="sb_not_paid_price" class="frm_input" placeholder="숫자만 입력" style="width:150px">원
		</td>
		<td>
			<?
				$pay_method_arr = $user->getPayMethod();
			?>
			<select name="pay_method" class="frm_input">
			<? foreach($pay_method_arr as $key => $value) { ?>
				<option value="<?=$key?>"><?=$value?></option>
			<? } ?>
			</select>
		</td>
		<td >
			<input type="text" name="cardno" id="cardno" size="15" maxlength="16" value="" class="form-control frm_input" style="width:200px;" onkeypress="return num_check(event)" placeholder="'-'없이 숫자만 입력하세요"><span id="notice"></span>
					<br />
			(유효기간 : <select name="expmon" class="form-control frm_input">
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
			</select>년)
			할부 :
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
		
		<td >
    		<input type="text" name="payer_name" class="frm_input">
		</td>
		<td>
    		<select name="bank_account" class="frm_input">
			<? for($i=0; $i<count($accounts); $i++) { ?>
				<option value="<?=$accounts[$i]?>"><?=$accounts[$i]?></option>
			<? } ?>
			</select>
		</td>
		
		<td>
			<button type="button" class="as-btn small blue" onclick="addServiceBuy()"><i class="fa fa-check"></i> 신청등록</button>
		</td>
	</tr>
	</table>
	</form>
	</div>

	<br /><br />
	<!-- 서비스 구매신청목록 -->
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
		<th width="40px">번호</th>
		<th width="150px">신청자(아이디)</th>
		<th width="100px">신청일</th>
		<th width="100px">결제일</th>
		<th>서비스</th>
		<th width="180px">카드정보</th>
		
		<th width="60px">신청기간</th>
		<th width="80px">금액</th>
		<th width="100px">결제방법</th>
		<th width="100px">입금자명</th>
		<th>약관동의</th>
		<th width="100px">상태</th>
		<th width="100px">변경</th>
	</tr>
	<?
	foreach($data_buy['list'] as $row) {
    	$sg_limit = $termService->service_grade_limit[$row['sg_no']];
    	$detail = unserialize($row['sb_detail']);
	?>
	<tr>
		<td><?=$data_buy['idx']--?></td>
		<td><?=$row['sb_buyer_name']?>(<?=($row['mb_id']) ? $row['mb_id'] : '<span style="color:red;font-weight:bold">비회원</span>'?>)</td>
		<td><?=$row['sb_regdate']?></td>
		<td><?=$row['sb_paydate']?></td>
		<td style="text-align:left;padding-left:5px">
    		<?=$row['sb_name']?>
		</td>
		<td >
			<? if($row['sb_pay_method'] == 'agency') { ?>
    		<?=$row['sb_ay_cardno']?> <br />[만료일(월/년) : <?=$row['sb_ay_expmon']?> / <?=$row['sb_ay_expyear']?> ] <br />할부 : <?=$row['sb_ay_installment']?>개월
			<? } else { ?>
			--
			<? } ?>
		</td>
		
		<td >
    		<?=$row['sb_term']?><?=$row['sb_term_type'] == 'month' ? '개월' : '일'?>
		</td>
        <td><?=number_format($row['sb_total_price'])?>원<? if($row['sb_pay_method'] == 'agency') { ?> <!-- (할부: <?=$row['sb_ay_installment']?>) --> <? } ?></td>
		<td id="pay_status_<?=$row['sb_no']?>">
    		<?=$termService->config['pay_method'][$row['sb_pay_method']]?>
			<? if($row['sb_pay_method'] == 'agency' && $row['sb_pay_status'] != 'Y') { ?>
				<button type="button" class="as-btn small blue" onclick="agencyPay2('<?=$row['sb_no']?>')"><i class="fa fa-credit-card"></i> 결재진행</button>
			<? } ?>
		</td>
		<td >
    		<?=$row['sb_pay_name']?>
		</td>
		<td id="agree_status_<?=$row['sb_no']?>">
    		<? if($row['sb_agree_provision'] == '1' || $row['sb_pay_status'] == 'Y') { ?>
			<span style="color:#6633ff;font-weight:bold">동의완료</span>
			<? } else { ?>
			<button type="button" class="as-btn small blue" onclick="sendProvisionSms('<?=$row['sb_no']?>')"><i class="fa fa-comment"></i> 약관전송</button>
			<? } ?>

			<? if($row['sb_pay_method'] == 'account' && $row['sb_pay_status'] == 'N') { ?>
			<button type="button" class="as-btn small blue" onclick="sendAccountSms('<?=$row['sb_no']?>')"><i class="fa fa-comment"></i> 계좌정보전송</button>
			<? } ?>
		</td>
		<td>
    		<? if($row['sb_pay_status'] == 'N') { ?>
    		<form name="service_buy_<?=$row['sb_no']?>" method="post" action="./term_service_buy_process.php">
        		<input type="hidden" name="proc" value="changeBuyStatus">
        		<input type="hidden" name="sb_no" value="<?=$row['sb_no']?>">
				<input type="hidden" name="sb_pay_method" value="<?=$row['sb_pay_method']?>">
				<input type="hidden" name="return_url" value="<?=$return_url?>">
        		<select name="sb_pay_status" class="frm_input" onChange="document.service_buy_<?=$row['sb_no']?>.submit()">
            		<option value="N" <?=$row['sb_pay_status'] == 'N' ? 'selected' : ''?>>결제대기</option>
            		<option value="Y" <?=$row['sb_pay_status'] == 'Y' ? 'selected' : ''?>>결제완료</option>
					<option value="N" <?=$row['sb_pay_status'] == 'F' ? 'selected' : ''?>>결제실패</option>
        		</select>
    		</form>
			<? } else if($row['sb_pay_status'] == 'F') { { ?>
			<span style="color:red">결제실패</span>
			<? } ?>
    		<? } else { ?>
			<span style="color:blue">결제완료</span>
			<? } ?>
		</td>
		
		<td>
			<button type="button" class="as-btn small red" onclick="deleteData('<?=$row['sb_no']?>')"><i class="fa fa-close"></i> 삭제</button>
			
		</td>
	</tr>
	<? }?>
	</table>
	<!-- //서비스 구매신청목록 -->
	
	<div>
		 <h5 class="title" style="padding-left:30px"><i class="fa fa-asterisk fa-lg"></i> 서비스이용현황</h5>
	</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tb02">
    
	<tr>
		
		<th width="100px">서비스구분</th>
		<th width="100px">번호 추출</th>
		<th width="120px">종료일</th>
		<th width="120px">잔여일</th>
		<th width="120px">상태</th>
	</tr>
<?
	foreach($data_service['list'] as $row) {

		$row['su_extract_per_week'] = $row['su_extract_per_week'] ? $row['su_extract_per_week'] : $extract_count_per_grade[$row['sg_no']];
?>
	
		<tr>
			<td style="text-align:center">
				<b><?=$termService->service_grade[$row['sg_no']]?></b>
			</td>
			<td style="text-align:center">
			   주당 <?=$extract_count_per_grade[$row['sg_no']]?>개 <!--/
			    조정 : <select name="su_extract_per_week" id="su_extract_per_week" class="frm_input">
					<option value="10" <?=$row['su_extract_per_week'] == 10 ? 'selected' : ''?>>10개/주</option>
					<option value="20" <?=$row['su_extract_per_week'] == 20 ? 'selected' : ''?>>20개/주</option>
					<option value="30" <?=$row['su_extract_per_week'] == 30 ? 'selected' : ''?>>30개/주</option>
					<option value="40" <?=$row['su_extract_per_week'] == 40 ? 'selected' : ''?>>40개/주</option>
					<option value="50" <?=$row['su_extract_per_week'] == 50 ? 'selected' : ''?>>50개/주</option>
				</select>개
				<button type="button" class=" as-btn small green" style="color:white" onClick="modifyExtractNum('<?=$row['su_no']?>');">수정(작업중...)</button> -->
			</td>
			<td style="text-align:center">
			   <?=substr($row['su_enddate'],0,10)?>
			</td>
			<td style="text-align:center">
			   <b><?=$row['leftDays']?></b>일
			</td>
			<td style="text-align:center">
				<?=$row['expired'] ? '서비스 만료' : '서비스 이용중'?>&nbsp;
			</td>
			
		</tr>
<? } ?>
	</table>
	<br /><br />
<? }?>
</div>

<form name="agencyPayForm" method="post" action="../common/common_process.php?">
	<input type="hidden" name="proc" value="agencyPay">
	<input type="hidden" name="mb_id" value="<?=$_GET['mb_id']?>">
	<input type="hidden" name="return_url" value="<?=$pay_return_url?>">
	<input type="hidden" name="sb_no" value="">
</form>

<!-- 서비스 구매신청 삭제 -->
<form name="deleteServiceBuyForm" method="post" action="./term_service_buy_process.php?<?=$param?>">
	<input type="hidden" name="proc" value="deleteServiceBuy">
	<input type="hidden" name="return_url" value="<?=$return_url?>">
	<input type="hidden" name="no" value="">
</form>
<!-- 서비스 구매신청 삭제 -->

<form name="deleteMemoForm" method="post" action="./lotto_member_process.php?">
	<input type="hidden" name="proc" value="deleteMemo">
	<input type="hidden" name="return_url" value="<?=$return_url?>">
	<input type="hidden" name="no" value="">
</form>

<form name="addNumbersToQueueForm" method="post" action="../common/common_process.php?">
	<input type="hidden" name="proc" value="addNumbersToQueue">
	<input type="hidden" name="mb_id" value="<?=$_GET['mb_id']?>">
	<input type="hidden" name="issue_count" value="">
</form>


<script type="text/javascript">

    var _editor_url = "../js/plugins/smarteditor2";
    var oEditors = [];

    (function($){
        $(document).ready(function() {
            $(".smarteditor2").each( function(index){
                var get_id = $(this).attr("id");

                if( !get_id || $(this).prop("nodeName") != 'TEXTAREA' ) return true;

                nhn.husky.EZCreator.createInIFrame({
                    oAppRef: oEditors,
                    elPlaceHolder: get_id,
                    sSkinURI: _editor_url+"/SmartEditor2Skin.html",
                    htParams : {
                        bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
                        bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
                        bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
                        //aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
                        fOnBeforeUnload : function(){
                            //alert("완료!");
                        }
                    }, //boolean
                    fOnAppLoad : function(){
                        //예제 코드
                        //oEditors.getById["ir1"].exec("PASTE_HTML", ["로딩이 완료된 후에 본문에 삽입되는 text입니다."]);
                    },
                    fCreator: "createSEditor2"
                });

            });
        });
    })(jQuery);
</script>

<script>

function checkForm() {
	var f = document.member_form;
	f.submit();
}

$(document).ready(function() {
	$('#member_form').validator().on('submit', function (e) {
		if (e.isDefaultPrevented()) {
			// handle the invalid form...
			
		} else {
			//oEditors.getById["detail"].exec("UPDATE_CONTENTS_FIELD", []);
		}
	})
	
	
	//$("#mb_contract_price").digits();

	$('#member_form').validator().on('submit', function (e) {
            if (e.isDefaultPrevented()) {
                // handle the invalid form...
            } else {
                oEditors.getById["detail_info"].exec("UPDATE_CONTENTS_FIELD", []);
            }
        });
       
 

	

/*	$('#reg_mb_buy_commission').number( true, 1 );
	$('#reg_mb_sell_commission').number( true, 1 );*/

	//jQuery.datetimepicker.setLocale('kr');
	$('.datepicker').datetimepicker({
		lang: 'kr',
		timepicker:false,
		format:'Y-m-d',
		formatDate:'Y-m-d',
		//minDate:'-1970/01/01', 
		maxDate:'+1970/01/02' // and tommorow is maximum date calendar
	});

	$('.schedule_datetime').datetimepicker({
		lang: 'kr',
		timepicker:true,
		format:'Y-m-d H:i:s',
		formatDate:'Y-m-d H:i:s',
		minDate:'-1970/01/01', 
		step : '30'
		//maxDate:'+1970/01/02' // and tommorow is maximum date calendar
	});

});


</script>

<script>
function setMemberForm(obj) {
	if(obj.value == '1') {
		$('.mb').hide();
		$('#reg_mb_id').attr('required', false);
		$('#reg_mb_password').attr('required', false);
	} else {
		$('.mb').show();
		$('#reg_mb_id').attr('required', true);
		$('#reg_mb_password').attr('required', true);
	}
}

$(document).ready(function() {


	$('#reg_mb_id').on('keyup', function() {
		var msg = reg_mb_id_check();
		if (msg) {
			$('#msg_mb_id').html(msg);
		} else {
			$('#msg_mb_id').html('사용 가능한 아이디 입니다.');
		}
	});

	$('#same_info').on('click', function() {

		if($(this).attr('checked')) {
			$('#mb_charger_name').val($('input[name="mb_name"]').val());
			$('#mb_charger_tel').val($('input[name="mb_tel1"]').val()+'-'+$('input[name="mb_tel2"]').val()+'-'+$('input[name="mb_tel3"]').val());
			$('#mb_charger_hp').val($('select[name="mb_hp1"]').val()+'-'+$('input[name="mb_hp2"]').val()+'-'+$('input[name="mb_hp3"]').val());
			$('#mb_charger_fax').val($('input[name="mb_fax1"]').val()+'-'+$('input[name="mb_fax2"]').val()+'-'+$('input[name="mb_fax3"]').val());
			$('#mb_charger_email').val($('input[name="mb_email"]').val());
		} else {
			$('#mb_charger_name').val('');
			$('#mb_charger_tel').val('');
			$('#mb_charger_hp').val('');
			$('#mb_charger_fax').val('');
			$('#mb_charger_email').val('');
		}
	});

	$('.cate_item').on('click', function() {
		if($(this).attr('checked')) {
			$('#cate_selected').append("<li id='" + $(this).val() + "'>" + $(this).next('label').text() + "</li>");
		} else {
			$('#cate_selected').find('#'+$(this).val()).remove();
		}
	});
});


// lotto add
function addServiceBuy() {
	var f = document.addServiceForm;
	var type = f.pay_method.value;

	if (f.onsubmit && !f.onsubmit()) {
		return false;
	}

	if(type == 'agency') {
		if(0 && !validatecardnumber(f.cardno.value)) {
			alert("유효한 신용카드 번호가 아닙니다.");
			return false;
		}
		f.submit();
	} else {
		if(!f.payer_name.value) {
			alert("입금자명을 입력해주세요.");
			f.payer_name.focus();
			return false;
		}
		f.submit();

	}
}
function num_check(evt){
	var code = evt.which?evt.which:event.keyCode;
	if(code < 48 || code > 57){
		return false;
	}
}

function deleteData(no) {

	var f = document.deleteServiceBuyForm;
	if(confirm("데이터를 삭제합니다.\n삭제하신데이터는 복구가 불가능합니다.\n계속하시겠습니까?")) {
		f.no.value = no;
		f.submit();
	}

}

function deleteMemo(no) {
	var f = document.deleteMemoForm;
	if(confirm("데이터를 삭제합니다.\n삭제하신데이터는 복구가 불가능합니다.\n계속하시겠습니까?")) {
		f.no.value = no;
		f.submit();
	}
}

function getServicePrice(no) {
	$.ajax({
		url: "../common/common_process.php",
		type: "post",
		data: "proc=getServicePrice&sc_no="+no,
		cache: false,
		dataType: 'html',
		success: function(data) {
			if(data) {
				$('#new_price').val(data);
			}
		}
	});
}

function sendAccountSms(sb_no) {
	$.ajax({
		url: "../common/common_process.php",
		type: "post",
		data: "proc=sendAccountSms&sb_no="+sb_no,
		cache: false,
		dataType: 'html',
		success: function(data) {
			if(data != 'fail') {
				alert("전송하였습니다.");
			} else {
				alert("데이터가 없습니다.");
			}
		}
	});
}

function sendProvisionSms(sb_no) {
	$.ajax({
		url: "./term_service_buy_process.php",
		type: "post",
		data: "proc=sendProvisionSms&sb_no="+sb_no,
		cache: false,
		dataType: 'html',
		success: function(data) {
			if(data != 'fail') {
				alert("전송하였습니다.");
				pull_agree_result(sb_no);
			} else {
				alert("데이터가 없습니다.");
			}
		}
	});
}

function modifyExtractNum(mb_id) {

	var num = $('#mb_extract_per_week').val();

	$.ajax({
		url: "./lotto_member_process.php",
		type: "post",
		data: "proc=modifyExtractNum&mb_id="+mb_id+"&num="+num,
		cache: false,
		dataType: 'html',
		success: function(data) {
			if(data != 'fail') {
				alert("수정하였습니다..");
				pull_agree_result(sb_no);
			} else {
				alert("데이터가 없습니다.");
			}
		}
	});
}

function addNumbersToQueue() {

	var f = addNumbersToQueueForm;
	var issue_count = isNaN(parseInt($('#issue_count').val(), 10)) ? 0 : parseInt($('#issue_count').val(), 10);

	if(issue_count == 0) {
		alert("발급 갯수를 입력해 주세요.");
		$('#issue_count').val('');
		$('#issue_count').focus()
		return false;
	}

	if(issue_count > 20) {
		alert("한번에 20개까지 발송 가능합니다.");
		$('#issue_count').val(20);
		$('#issue_count').focus()
		return false;
	}

	f.issue_count.value = issue_count;
	f.submit();

}

// 결제신청
function agencyPay(sb_no) {

	var f = document.agencyPayForm;
	f.sb_no.value = sb_no;
	f.submit();

}

// 결제신청 - 새창
function agencyPay2(sb_no) {

	var f = document.agencyPayForm;
	f.sb_no.value = sb_no;
	f.return_url.value = "close";

	var op = window.open("about:blank", 'pay_win', 'width=750, height=800, scrollbars=yes');
	f.target = "pay_win";
	f.submit();

}

function openUserWinList(mb_id) {
	window.open("./lotto_member_win_list.php?id="+mb_id, 'win_list', 'width=750, height=800, scrollbars=yes');
}


</script>

<script>


	var delayTime = 2000;
	function pull_agree_result(no){
		setTimeout(function(){
			$.ajax({
					url: "../common/common_process.php",
					type: "post",
					data: {
							proc: 'checkAgreeProvision',
							sb_no : no
					},
					success: function(data){ 
						if(data == '1') {
							$('#agree_status_'+no).html('<span style="color:#6633ff;font-weight:bold">동의완료</span>');
						} else {
							delayTime += (delayTime < 30000) ? 3000 : 0;
							pull_agree_result(no); console.log('test');
						}
					},
					dataType: "json"
			});
		}, delayTime);
	}


</script>

<!-- <script src="/ns/js/jquery.register_form.js"></script> -->

<!-- bootstrap validator -->
<script src="../js/plugins/bootstrap-validator/validator.min.js"></script>

<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<!-- jQuery uploadify -->
<script src="../js/plugins/uploadify/jquery.uploadify.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../js/plugins/uploadify/uploadify.css">
<!-- /jQuery uploadify -->

<script type="text/javascript" src="../js/plugins/smarteditor2/js/HuskyEZCreator.js" charset="utf-8"></script>

<?
include_once (G5_ADMIN_PATH."/admin.tail.php");
?>