<?
define('G5_IS_ADMIN', true);
$sub_menu = "400100";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'r');

use Acesoft\LottoApp\TermService;

//▶ 서비스정보 인출
$termService = new TermService();

$data = $termService->getDefaultConfig();

$g5['title'] = "서비스기본설정";
include_once(G5_ADMIN_PATH."/admin.head.php");
?>


<link rel="stylesheet" href="../css/custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/admin.custom.style.css" type="text/css">
<link rel="stylesheet" href="../js/datetimepicker/jquery.datetimepicker.css" type="text/css">
<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css" rel="stylesheet" />

<!-- jQuery -->
<script type="text/javascript" src="<?=$_url['solution_root']?>/lib/js/jquery-1.8.3.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<!-- /jQuery -->


<!-- jQuery datetimepicker -->
<script type="text/javascript" src="../js/datetimepicker/jquery.datetimepicker.js"></script>
<!-- <script type="text/javascript" src="<?=$_url['solution_root']?>/lib/js/jquery.ui.datepicker-ko.js"></script> -->
<!-- /jQuery datetimepicker -->
<style>
.l-ball {
	border:0px solid black;
	padding: 3px;
	margin: 5px;
	width:18px;
	height:30px;
	float:left;
	text-align:center;
	text-align: center;
    line-height: 30px;
    border-radius: 10px;
	background:#eeeeee;
	font-weight:bold;
	cursor:pointer;
}

.l-ball.checked {
	background:#e8321f;
	color:#ffffff;
}


</style>

<form name="write_form" method="post" action="./term_service_default_config_process.php" enctype="multipart/form-data">
<input type="hidden" name="proc" value="updateServiceDefaultConfig">
<div class="info_container">
	
	<div class="btitle">
		<i class="fa fa-folder-open-o"></i> 기본설정
	</div>
	<div class="content_wrap">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="tb02">
	<tr>
		<th width="120px">서비스약관설정<br/>결제및 환불관련</th>
		<td><textarea style="width:100%;height:300px" name="tdc_provision" placeholder=""><?=stripslashes($data['tdc_provision'])?></textarea></td>
	</tr>
	<tr>
		<th width="120px">서비스약관설정<br/>사용자구매페이지</th>
		<td><textarea style="width:100%;height:300px" name="tdc_provision_user" placeholder=""><?=stripslashes($data['tdc_provision_user'])?></textarea></td>
	</tr>
	<tr>
		<th width="120px">은행계좌<br/>(엔터로 구분입력)</th>
		<td><textarea style="width:100%;height:100px" name="tdc_accounts" placeholder=""><?=stripslashes($data['tdc_accounts'])?></textarea></td>
	</tr>
	</table>
	
	<br /><br /><br />
	

	<div style="width:100%;text-align:center;padding-top:30px">
		<button type="button" class="as-btn small green" onClick="checkForm();void(0);"><i class="fa fa-refresh"></i> 수정</button>
		<button type="button" class="as-btn small red" onclick="history.back(-1);"><i class="fa fa-close"></i> 취소</button>
	</div>
	</form>
</div>


<script type="text/javascript">


function checkForm() {
	var f = document.write_form;

    if (f.onsubmit && !f.onsubmit()) {
		return false;
	}


	f.submit();
}

$(document).ready(function() {
	$.datepicker.setDefaults($.datepicker.regional['ko']);
	$('.datePicker').datepicker({
					'dateFormat':'yy-mm-dd',
					changeMonth: true,
					changeYear: true,
					showButtonPanel: true,
					showMonthAfterYear: true,
					yearRange: 'c-0:c+10',
					minDate: 0,
					onChange: function(date) {
						
					}
				});

});

$(document).ready(function() {
	//$.datetimepicker.setDefaults($.datetimepicker.regional['ko']);
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
</script>

<?
include_once (G5_ADMIN_PATH."/admin.tail.php");
?>
