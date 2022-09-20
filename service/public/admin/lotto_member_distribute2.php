<?
define('G5_IS_ADMIN', true);
define('NO_CACHE', true);
$sub_menu = "400100";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'r');

use Acesoft\Common\Utils;
use Acesoft\LottoApp\Member\User;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\TermService;

$param1 = Utils::getParameters(array('page'));


// TM목록
$user = new User();
$tm_list = $user->getTMList();

$channel_group = $user->getChannelGroup();
$media_group = $user->getMediaGroup('', $_GET['s_mb_channel']);

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

$title = "회원 배분";
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
	<form name="distribute_form" id="distribute_form" method="post" action="./lotto_member_process.php">
	
	<h2>대상검색</h2>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
		<th>구분</th>
		<td style="text-align:left;padding-left:5px">
			<label><input type="radio" name="proc" class="proc" value="distributeMemberToTM" checked> 배분</label>
			<label><input type="radio" name="proc" class="proc" value="redistributeMemberToTM"> 회수</label>
		</td>
	</tr>
	<tr>
		<th>가입기간(필수)</th>
		<td style="text-align:left;padding-left:5px"><input type="text" name="s_date" id="s_date" class="frm_input datetimepicker" autocomplete="off"> ~ <input type="text" name="e_date" id="e_date" class="frm_input datetimepicker" autocomplete="off"></td>
	</tr>
	<tr class="redistribute" style="display:none">
		<th style="width:120px">회수대상TM(필수)</th>
		<td style="display:flex;flex-wrap: wrap;">
			<? for($i=0; $i<count($tm_list); $i++) { ?>
				<label style="padding:5px;margin:5px;background:#33ccff;color:white;font-weight:bold;border-radius:5px"><input type="checkbox" name="tm_source_ids[]" value="<?=$tm_list[$i]['mb_id']?>"> <?=$tm_list[$i]['mb_id']?></label>
			<? }?>
		</td>
	</tr>
	<tr class="redistribute" style="display:none">
		<th>상태</th>
		<td style="display:flex;flex-wrap: wrap;text-align:left">
			<? 
				foreach($user->getConsultStatus() as $key => $value) {
			?>
			<label style="padding:3px;margin:3px;"><input type='checkbox' name="status[]" value="<?=$key?>"><?=$value?></label>
			<?
				}
			?>
		</td>
	</tr>
	<tr>
		<th>업체</th>
		<td style="display:flex;flex-wrap: wrap;text-align:left">
			<? 
				for($i=0; $i<count($channel_group); $i++) { 
					if(trim($channel_group[$i]['mb_channel']) != '') {
			?>
				<label style="padding:3px;margin:3px;"><input type="checkbox" name="channel[]" value="<?=$channel_group[$i]['mb_channel']?>" /> <?=$channel_group[$i]['mb_channel']?></label>
			<? 
			
			
					}
				}
			?>
			<label style="padding:3px;margin:3px;"><input type="checkbox" name="channel[]" value="common" /> 일반</label>
		</td>
	</tr>
	<tr>
		<th style="width:120px">매체</th>
		<td style="display:flex;flex-wrap: wrap;text-align:left">
			<? 
				for($i=0; $i<count($media_group); $i++) { 
					if(trim($media_group[$i]['mb_media']) != '') {
			?>
				<label style="padding:3px;margin:3px;"><input type="checkbox" name="media[]" value="<?=$media_group[$i]['mb_media']?>" /> <?=$media_group[$i]['mb_media']?></label>
			<?
					}
				}
			?>
			<label style="padding:3px;margin:3px;"><input type="checkbox" name="media[]" value="common" /> 일반</label>
		</td>
	</tr>
	
	
	
	</table>
	<br /><br />
	<h2>대상선택</h2>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
		<th style="width:120px">배분대상TM</th>
		<td style="display:flex;flex-wrap: wrap;">
			<? for($i=0; $i<count($tm_list); $i++) { ?>
				<label style="padding:5px;margin:5px;background:#33ccff;color:white;font-weight:bold;border-radius:5px"><input type="checkbox" name="tm_ids[]" value="<?=$tm_list[$i]['mb_id']?>"> <?=$tm_list[$i]['mb_id']?></label>
			<? }?>
		</td>
	</tr>
	
	<tr>
		<th>갯수</th>
		<td>해당기간 조회된 회원 수 전체<span id="total">0</span>명 중 <span id="proc_text">미배분</span><input type="text" name="total_number" class="frm_input" id="total_number" style="width:50px;text-align:right" value="0" readonly>명 / <input type="text" name="num" id="num" style="width:50px;text-align:right" class="frm_input" onkeyUp="return checkNumbers()">명씩 배분 <span id="loading_img" style="display:none"><img src="../images/loading.gif" width="15px"></td>
	</tr>
	<tr class="redistribute" style="display:none">
		<th style="">상태초기화</th>
		<td style="text-align:left;padding-left:5px">
			<label><input type="checkbox" name="clear_status" value="1">초기화</label>
		</td>
	</tr>
	<tr class="redistribute" style="display:none">
		<th style="">메모초기화</th>
		<td style="text-align:left;padding-left:5px">
			<label><input type="checkbox" name="clear_memo" value="1">초기화</label>
		</td>
	</tr>
	</table>
	<br />
	<div style="clear:both"></div>
	
	<div class="btn_box ac">
		<button type="button" class="as-btn small green" onclick="doDistribute();void(0);"><i class="fa fa-refresh"></i> 배분하기</button>
		<button type="button" class="as-btn small red" onclick="window.close()"><i class="fa fa-cancel"></i> 닫기</button>
	</div>
	</form>
</div>
<script type="text/javascript">
<!--

function checkNumbers() {

	var s_date = $('#s_date').val();
	var e_date = $('#e_date').val();
	if(!$('#s_date').val() || !$('#e_date').val()) {
		alert("기간을 입력해 주세요");
		return false;
	}

	var total_number = $('#total_number').val();
	if(!total_number || total_number == '0') {
		alert("배분가능한 회원이 없습니다.");
		return false;
	}

	if($('input[name="proc"]:checked').val() == 'redistributeMemberToTM') {
		if($('input[name="tm_source_ids[]"]:checked').length == 0) {
			alert("회수 대상을 선택해 주세요.");
			return false;
		}
	}

	var tm_ids = [];
	$('input[name="tm_ids[]"]:checked').each(function(idx, data) {
		tm_ids.push($(data).val());
	});

	if(!tm_ids.length) {
		alert("배분대상을 선택해 주세요.");
		return false;
	}

	var num = $('#num').val();
	if(!num || num == 0) {
		alert("배분갯수를 입력해주세요");
		$('#num').val(Math.floor(total_number/tm_ids.length));
		$('#num').focus();
		return false;
	}

	var each_num = tm_ids.length*num;
	if(each_num > total_number) {
		alert("배분가능한 갯수가 부족합니다.");
		$('#num').val(Math.floor(total_number/tm_ids.length));
		return false;
	}

	return true;
}

function doDistribute() {
	if(checkNumbers()) {
		document.distribute_form.submit();
	}
}

$(document).ready(function() {
	var todayDate = new Date().getDate();
    $('.datetimepicker').datetimepicker({
		format:'Y-m-d',
		timepicker: false,
		changeMonth: true,
		changeYear: true,
		showButtonPanel: true,
		showMonthAfterYear: true,
		yearRange: 'c-5:c+5',
		minDate: new Date(new Date().setDate(todayDate - 360)),
		maxDate: 0,
		lang: 'kr',
		onSelectDate: function(date) {
			searchUsers();
		}
	});


	function searchUsers() {
		var s_date = $('#s_date').val();
		var e_date = $('#e_date').val();

		var type_proc = $('input[name="proc"]:checked').val();

		var channel = $('input[name="channel[]"]:checkbox:checked').map(function(){
			return $(this).val();
		}).get().join(',');

		var media = $('input[name="media[]"]:checkbox:checked').map(function(){
			return $(this).val();
		}).get().join(',');

		var source_tm = $('input[name="tm_source_ids[]"]:checkbox:checked').map(function(){
			return $(this).val();
		}).get().join(',');

		var status = $('input[name="status[]"]:checkbox:checked').map(function(){
			return $(this).val();
		}).get().join(',');

		

		if($('#s_date').val() && $('#e_date').val()) {
			$('#loading_img').show();

			$.ajax({
				type: "POST",
				url: "./lotto_member_process.php",
				data: { proc:'getMembersToAssign', sdate: s_date, edate: e_date, media : media, channel : channel, tm_source_ids : source_tm, status: status, 'type': type_proc},
				cache: false,
				success: function(data) {
					$('#total').text(data['total'].length);
					$('#total_number').val(data['not_distributed'].length); 
					
					$('#loading_img').hide();
				},
				dataType: 'json'
			});
			
		}
	}

	$('#distribute_form').change(function() {
		searchUsers();
	});

	$('.proc').on('click', function() {
		var proc = $(this).val();
		if(proc == 'distributeMemberToTM') {
			$('.redistribute').hide();
			$('input[name="tm_source_ids[]"]').prop('checked', false);
			$('input[name="status[]"]').prop('checked', false);
			$('#proc_text').text('미배분');
			$('#loading_img').hide();
		} else {
			$('.redistribute').show();
			$('#proc_text').text('검색조건');
			$('#loading_img').hide();
		}
	});

});
//-->
</script>