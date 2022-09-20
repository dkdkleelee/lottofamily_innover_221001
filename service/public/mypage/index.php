<?php
include_once("./_common.php");

$cur = 1;
include_once("../../../head_05.php");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Lotto;
use Acesoft\LottoApp\TermService;

$param1 = Utils::getParameters(array('page'));

$lottoServiceConfig = new LottoServiceConfig();
$serviceConfig = $lottoServiceConfig->getConfig();

//▶ 등급별 발급설정갯수
$extract_count_per_grade = unserialize($serviceConfig['lc_extract_count']);

// term service
$termService = new TermService();


$mb = get_member($_SESSION['ss_mb_id']);
$send_weekdays = explode(",", $_SESSION['ss_mb_id']);

// 이용중서비스
$_GET['s_mb_id'] = $_SESSION['ss_mb_id'];
$data = $termService->getTermServiceUseList();

// 최근 SMS목록
$data_sms = $termService->db->arrayBuilder()->rawQuery("SELECT * from {$g5['sms5_history_table']} as a left join {$g5['sms5_write_table']} as b USING(wr_no) where mb_id='".$_SESSION['ss_mb_id']."' ORDER BY a.hs_datetime DESC limit 5", 56);

?>

<link rel="stylesheet" href="../css/custom.style.css" type="text/css">
<!-- <link rel="stylesheet" href="../css/admin.custom.style.css" type="text/css"> -->
<link rel="stylesheet" href="../css/lotto.css" type="text/css">
<link rel="stylesheet" href="../css/paginate.css" type="text/css">
<!-- <link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css" rel="stylesheet" /> -->
<script src="../js/jquery-ui-1.8.7.custom.min.js" ></script>
<script src="../js/jquery.ui.datepicker-ko.js"  charset="utf-8"></script>
<link rel="stylesheet" href="../js/datetimepicker/jquery.datetimepicker.css" type="text/css" />
<script type="text/javascript" src="../js/datetimepicker/jquery.datetimepicker.js" charset="utf-8"></script>

<div class="info_container">
<div class="btitle">
	<div class="btitle_top"></div>
	<div class="btitle_text">마이페이지</div>
	<div class="btitle_locate">&gt; 마이페이지</div>
	<div class="btitle_line"></div>
</div>
<div class="content_wrap">
	<div>
		 <h5 class="title"><i class="fa fa-asterisk fa-lg"></i> 회원기본정보</h5>
	</div>
	<form name="updateMemberForm" id="updateMemberForm" method="POST" action="./mypage_process.php">
	<table width="100%" border="0" cellpadding="0" class="tb-mypage-01">
	<tr>
		
		<th style="width:15%">아이디</th>
		<td style="width:35%"><?=$mb['mb_id']?></td>
	</tr>
	<tr>
		<th style="width:15%">이름</th>
		<td style="width:35%"><?=$mb['mb_name']?></td>
	</tr>
	<tr>
		
		<th>휴대전화번호</th>
		<td><?=$mb['mb_hp']?></td>
	</tr>
	<tr>
		<th>가입일</th>
		<td><?=$mb['mb_datetime']?></td>
	</tr>
	<!-- <tr>
		<th>패스워드 수정</th>
		<td><input type="password" name="password" id="password" class="frm_input"></td>
		<th>패스워드 확인</th>
		<td>
			<input type="password" name="password_re" id="password_re" class="frm_input">
			<button type="button" class="as-btn small green" onclick="updatePassword();void(0);"><i class="fa fa-refresh"></i> 패스워드갱신</button>
		</td>
	</tr> -->
	</table>
	</form>

	<!-- <div>
		 <h5 class="title"><i class="fa fa-asterisk fa-lg"></i> 서비스이용현황</h5>
	</div>
	<form name="service_config_form" id="user_form" method="post" action="mypage_process.php">
    <input type="hidden" name="proc" value="updateExtractDate">
	<input type="hidden" name="mb_id" value="<?=$mb['mb_id']?>">
	<table width="100%" border="0" cellpadding="0" class="tb-mypage">
	<tr>
		<th width="100px">문자발송요일</th>
		<td>
			<? for($i=1; $i<6; $i++) { ?>
			<label><input type="radio" name="mb_extract_weekday" value="<?=$i?>" <?=$mb['mb_extract_weekday'] == $i ? 'checked' : ''?>><?=$termService->config['weekdays_name'][$i]?>&nbsp;&nbsp;&nbsp;</label>
			<? } ?>
		</td>
		<td style="text-align:center">
			<button type="button" class="as-btn small green" onclick="document.service_config_form.submit();void(0);"><i class="fa fa-refresh"></i> 수정</button>
		</td>
	</tr>
	</table>
	</form> -->

	<div style="clear:both"></div>
	<br />
	<div>
		 <h5 class="title"><i class="fa fa-asterisk fa-lg"></i> 서비스이용현황</h5>
	</div>
	
	<table width="100%" border="0" cellpadding="0" class="tb-mypage">
    
	<tr>
		
		<th width="100px">서비스구분</th>
		<th width="100px">분석번호 추출</th>
		<th width="120px">종료일</th>
		<th width="120px">잔여일</th>
		<th width="120px">상태</th>
		<th width="120px">연장</th>
	</tr>
<?
	foreach($data['list'] as $row) {
?>
	<form name="service_use_form_<?=$row['su_no']?>" id="service_use_form_<?=$row['su_no']?>" method="post" action="term_service_use_process.php">
    	<input type="hidden" name="proc" value="updateServiceUse">
    	<input type="hidden" name="su_no" value="<?=$row['su_no']?>">
		<input type="hidden" name="sg_no" value="<?=$row['sg_no']?>">
		<tr>
			<td style="text-align:center">
				<?=$termService->service_grade[$row['sg_no']]?>
			</td>
			<td style="text-align:center">
			   <?=$extract_count_per_grade[$row['sg_no']]?>개
			</td>
			<td style="text-align:center">
			   <?=substr($row['su_enddate'],0,10)?>
			</td>
			<td style="text-align:center">
			   <?=$row['leftDays']?>일
			</td>
			<td style="text-align:center">
				<?=$row['expired'] ? '서비스 만료' : '서비스 이용중'?>&nbsp;
			</td>
			<td style="text-align:center">
				<button type="button" class="as-btn small green" onclick="m5s4();void(0);"><i class="fa fa-refresh"></i> 연장</button>
			</td>
		</tr>
	</form>
<? } ?>
		<tr>
			<td style="text-align:center" colspan="6">
				<button type="button" class="as-btn small green" onclick="m5s4();void(0);"><i class="fa fa-check"></i> 신청하기</button>
			</td>
		</tr>
<? if(count($data['list']) == 0) { ?>
	
<? } ?>
	</table>
	<br />
	<div style="clear:both"></div>
	
	
</div>
</div>
</div>
<script>
function updatePassword() {
	
}
</script>
<?php
include_once("../../../tail.php");
?>