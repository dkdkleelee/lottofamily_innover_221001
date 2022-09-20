<?
define('G5_IS_ADMIN', true);
define('NO_CACHE', true);
$sub_menu = "400100";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'r');

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\TermService;

$param1 = Utils::getParameters(array('page'));

// term service
$termService = new TermService();


$mb = get_member($_GET['id']);
$send_weekdays = explode(",", $mb['mb_1']);

// 이용중서비스
$_GET['s_mb_id'] = $_GET['id'];
$data = $termService->getTermServiceUseList();

// 최근 SMS목록
$_GET['mb_id'] = $_GET['id'];
$lottoService = new LottoService();
$data_sms = $lottoService->getSmsHistoryList($_GET['page']);


$title = "회원 서비스 관리";
include_once(G5_PATH.'/head.sub.php');
?>

<link rel="stylesheet" href="../css/custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/admin.custom.style.css" type="text/css">
<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css" rel="stylesheet" />
<script src="../js/jquery-ui-1.8.7.custom.min.js" ></script>
<script src="../js/jquery.ui.datepicker-ko.js"  charset="utf-8"></script>
<link rel="stylesheet" href="../js/datetimepicker/jquery.datetimepicker.css" type="text/css" />
<script type="text/javascript" src="../js/datetimepicker/jquery.datetimepicker.js" charset="utf-8"></script>
<div class="info_container">

<div>
	<span class="btitle"><i class="fa fa-folder-open-o"></i> <?=$title?></span>
</div>
<div class="content_wrap">
	<div>
		<ul id="tab">
			<li><a href="./term_service_management.php?<?=$param1?>" class="tab_on">서비스 기본정보</a></li>
			<li><a href="./term_service_sms_management.php?<?=$param1?>">SMS전송</a></li>
			<!-- <li><a href="javascript:m6s2s3()"></a></li> -->
		</ul>
	</div>
	<div style="clear:both"></div>
	<div>
		 <span class="help_text"><i class="fa fa-plus fa-lg"></i> 회원기본정보</span>
	</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
    
	<tr>
		
		<th style="width:20%">아이디</th>
		<td style="width:30%"><?=$mb['mb_id']?></td>
		<th style="width:20%">이름</th>
		<td style="width:30%"><?=$mb['mb_name']?></td>
	</tr>
	<tr>
		
		<th>휴대전화번호</th>
		<td><?=$mb['mb_hp']?></td>
		<th>가입일</th>
		<td><?=$mb['mb_datetime']?></td>
	</tr>
	</table>
	<br />
	<div style="clear:both"></div>
	<div>
		 <span class="help_text"><i class="fa fa-plus fa-lg"></i> 서비스이용현황</span>
	</div>
	
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
    
	<tr>
		
		<th width="100px">서비스구분</th>
		<th width="120px">기간</th>
		<th width="120px">기간구분</th>
		<th width="120px">변경</th>
	</tr>
<?
	foreach($data['list'] as $row) {
?>
	<form name="service_use_form_<?=$row['su_no']?>" id="service_use_form_<?=$row['su_no']?>" method="post" action="term_service_use_process.php">
    	<input type="hidden" name="proc" value="updateServiceUse">
    	<input type="hidden" name="su_no" value="<?=$row['su_no']?>">
		<input type="hidden" name="sg_no" value="<?=$row['sg_no']?>">
		<tr>
			<td>
				<?=$termService->service_grade[$row['sg_no']]?>
			</td>
			<td>
			   <input type="text" class="frm_input datetimepicker" name="su_enddate" value="<?=$row['su_enddate']?>" style="width:150px">
			</td>
			
			<td>
				<?=$row['expired'] ? '서비스 만료' : '서비스 이용중'?>&nbsp;
			</td>
			<td>
				<button type="button" class="as-btn small green" onclick="modifyData('<?=$row['su_no']?>');void(0);"><i class="fa fa-refresh"></i> 수정</button>
				<button type="button" class="as-btn small red" onclick="deleteData('<?=$row['sc_no']?>')"><i class="fa fa-cancel"></i> 삭제</button>
			</td>
		</tr>
	</form>
<? } ?>
	</table>
	<br />
	<div style="clear:both"></div>
	<div>
		 <span class="help_text"><i class="fa fa-plus fa-lg"></i> 서비스이용현황</span>
	</div>
	<form name="service_use_form_<?=$mb['mb_id']?>" id="user_form_<?=$mb['mb_id']?>" method="post" action="lotto_extract_numbers_process.php">
    <input type="hidden" name="proc" value="updateExtractDate">
	<input type="hidden" name="mb_id" value="<?=$mb['mb_id']?>">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
		<th width="100px">발송요일</th>
		<td>
			<? for($i=1; $i<6; $i++) { ?>
			<label><input type="radio" name="mb_extract_weekday" value="<?=$i?>" <?=$mb['mb_extract_weekday'] == $i ? 'checked' : ''?>><?=$termService->config['weekdays_name'][$i]?>&nbsp;&nbsp;&nbsp;</label>
			<? } ?>
		</td>
		<td>
			<button type="button" class="as-btn small green" onclick="document.service_use_form_<?=$mb['mb_id']?>.submit();void(0);"><i class="fa fa-refresh"></i> 수정</button>
		</td>
	</tr>
	</table>
	</form>
	<br />
	<div>
		 <span class="help_text"><i class="fa fa-plus fa-lg"></i> 최근 SMS발송현황</span>
	</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
    
	<tr>
		<th width="120px">전송일시</th>
		<th width="">내용</th>
		<th width="50px">결과</th>
	</tr>
	<?	foreach((array)$data_sms['list'] as $row) { ?>
	
		<tr>
        <td>
            <?php echo date('Y-m-d H:i', strtotime($row['hs_datetime']))?>
        </td>
		<td style="text-align:left;padding:3px;">
    		<span title="<?php echo $row['wr_message']?>"><?php echo $row['wr_message']?></span>
		</td>
		<td class="td_boolean" title="<?php echo $row['hs_memo']?>"><?php echo $res['hs_flag']?'성공':'실패'?></td>
		
	</tr>
	</form>
	<? }?>
	
	</table>

</div>
<form name="deleteForm" method="post" action="./term_service_config_process.php?<?=$param?>">
<input type="hidden" name="proc" value="deleteTermServiceConfig">
<input type="hidden" name="no" value="">
</form>
<script type="text/javascript">
<!--

function modifyData(no) {
    
    var f = $('#service_use_form_'+no);
    $(f).submit();
    
}

function deleteData(no) {

	var f = document.deleteForm;
	if(confirm("데이터를 삭제합니다.\n삭제하신데이터는 복구가 불가능합니다.\n계속하시겠습니까?")) {
		f.no.value = no;
		f.submit();
	}

}

function memoWin(no) {
	window.open("./ma_request_memo.php?type=ap_no&idx="+no, '', 'width=550, height=600');
}
function deleteData(no) {

	var f = document.deleteForm;
	if(confirm("데이터를 삭제합니다.\n삭제하신데이터는 복구가 불가능합니다.\n계속하시겠습니까?")) {
		f.no.value = no;
		f.submit();
	}

}

$(document).ready(function() {
	
    $('.datetimepicker').datetimepicker({
					format:'Y-m-d H:i:s',
					changeMonth: true,
					changeYear: true,
					showButtonPanel: true,
					showMonthAfterYear: true,
					yearRange: 'c-0:c+10',
					minDate: 0,
					lang: 'kr',
					onChange: function(date) {
						
					}
				});


});
//-->
</script>
