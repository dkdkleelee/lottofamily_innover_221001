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
	<span class="btitle"><a href="./lotto_member_distribute2.php"><i class="fa fa-folder-open-o"></i></a> <?=$title?></span>
</div>
<div class="content_wrap">
	<!-- <div>
		 <span class="help_text"><i class="fa fa-plus fa-lg"></i> 회원배분</span>
	</div> -->
	<form name="distribute_form" method="post" action="./lotto_member_process.php">
	<input type="hidden" name="proc" value="distributeMemberToTM">
	
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
		<th style="width:100px">대상TM</th>
		<td style="display:flex;flex-wrap: wrap;">
			<? for($i=0; $i<count($tm_list); $i++) { ?>
				<label style="padding:5px;margin:5px;background:#33ccff;color:white;font-weight:bold;border-radius:5px"><input type="checkbox" name="tm_ids[]" value="<?=$tm_list[$i]['mb_id']?>"> <?=$tm_list[$i]['mb_id']?></label>
			<? }?>
		</td>
	</tr>
	<tr>
		<th>가입기간</th>
		<td><input type="text" name="s_date" id="s_date" class="frm_input datetimepicker"> ~ <input type="text" name="e_date" id="e_date" class="frm_input datetimepicker"></td>
	</tr>
	<tr>
		<th>갯수</th>
		<td>조회된 회원 수 전체<span id="total">0</span>명 중 미배분<input type="text" name="total_number" class="frm_input" id="total_number" style="width:50px;text-align:right" value="0" readonly>명 / <input type="text" name="num" id="num" style="width:50px;text-align:right" class="frm_input" onkeyUp="return checkNumbers()">명씩 배분</td>
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

	var tm_ids = [];
	$('input[name="tm_ids[]"]:checked').each(function(idx, data) {
		tm_ids.push($(data).val());
	});

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
		minDate: new Date(new Date().setDate(todayDate - 60)),
		maxDate: 0,
		lang: 'kr',
		onSelectDate: function(date) {
			var s_date = $('#s_date').val();
			var e_date = $('#e_date').val();
			if($('#s_date').val() && $('#e_date').val()) {
				$.ajax({
					type: "POST",
					url: "./lotto_member_process.php",
					data: { proc:'getNotAssignedMember', sdate: s_date, edate: e_date},
					success: function(data) {
						$('#total').text(data['total'].length);
						$('#total_number').val(data['not_distributed'].length); 
						console.log(data);
					},
					dataType: 'json'
				});
				
			}
		}
	});


});
//-->
</script>