<?
define('G5_IS_ADMIN', true);

$sub_menu = "500100";
include_once("./_common.php");
include_once(G5_SMS5_PATH.'/sms5.lib.php');

auth_check($auth[$sub_menu], 'r');

use \Acesoft\Common\Utils;
use \Acesoft\Common\Message;
use \Acesoft\LottoApp\LottoServiceConfig;
use \Acesoft\LottoApp\TermService;

$param =  Utils::getParameters(array('nocache'));
$return_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$param;

//▶ 설정정보 인출
$lottoServiceConfig = new LottoServiceConfig();
$data = $lottoServiceConfig->getConfig($_GET['s_sg_no'] ?? '0');


//▶ 서비스정보 인출
$termService = new TermService();
$serviceGrade = $termService->service_grade;

$g5['title'] = "환경설정";
include_once(G5_ADMIN_PATH."/admin.head.php");

$include_numbers = explode(",", $data['lc_include_numbers']);
$exclude_numbers = explode(",", $data['lc_exclude_numbers']);

$extract_count = unserialize($data['lc_extract_count']);
$user_extract_count = unserialize($data['lc_user_extract_count']);
//$send_weekdays = explode(",", $data['lc_send_weekdays']);

$send_win_result = explode(",", $data['lc_send_win_result']);

// 2020. 1. 2.
$extractors_arr = $termService->service_grade_extractor;

?>


<link rel="stylesheet" href="../css/custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/admin.custom.style.css" type="text/css">
<link rel="stylesheet" href="../js/datetimepicker/jquery.datetimepicker.css" type="text/css">
<link rel="stylesheet" href="../css/lotto.css" type="text/css">
<!-- <link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css" rel="stylesheet" /> -->

<!-- jQuery -->
<!-- <script type="text/javascript" src="../js/lib/js/jquery-1.8.3.js"></script> -->
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<!-- /jQuery -->


<!-- jQuery datetimepicker -->
<script type="text/javascript" src="../js/datetimepicker/jquery.datetimepicker.js"></script>
<!-- <script type="text/javascript" src="<?=$_url['solution_root']?>/lib/js/jquery.ui.datepicker-ko.js"></script> -->
<!-- /jQuery datetimepicker -->


<div class="search_box ar" class="frm_input"><!-- 2020. 1. 2. -->
<form name="search_form" method="get" action="">
	추출기 선택: <select name="s_sg_no" onChange="document.search_form.submit()">
		<option value="">기본추출기</option>
	<?php foreach($serviceGrade as $key => $value) { ?>
		<option value="<?=$key?>" <?=$key == $_GET['s_sg_no'] ? 'selected' : ''?>><?=$value?>추출기</option>
	<?php } ?>
	</select>
</form>
</div>

<div class="info_container">
	<div class="btitle">
		<i class="fa fa-folder-open-o"></i> 추출기 필터설정
	</div>
	<div class="content_wrap">
		<div>
			 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 자동추출기의 기본 필터값을 설정합니다.</span>
			 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 설정된 필터값은 자동추출시 적용됩니다.</span>
			 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 필터값설정시 경우의수가 너무 작아지면 추출시간이 상당히 길어질 수 있으니 필히 확인하시어 지정하시기 바랍니다.</span>
		</div>
		
		<form name="extractor_config_form" method="post" action="./lotto_config_process.php" enctype="multipart/form-data">
		<input type="hidden" name="proc" value="updateServiceExtractorConfig">
		<input type="hidden" name="return_url" value="<?=$return_url?>">
		<input type="hidden" name="sg_no" value="<?=$_GET['s_sg_no']?>">
		<input type="hidden" name="lc_exclude_numbers" id="exclude_numbers" value="<?=$data['lc_exclude_numbers']?>">
		<input type="hidden" name="lc_include_numbers" id="include_numbers" value="<?=$data['lc_include_numbers']?>">

		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="tb02">
		<tr>
			<th width="120px">제외수설정</th>
			<td>
				
				<?php for($i=1; $i<46; $i++) { ?>
				<div class="exclude-balls l-ball small <?=in_array($i, $exclude_numbers) ? 'checked' : ''?>" id="exclude_ball_<?=$i?>" data-value="<?=$i?>" data-name="exclude" ><?=$i?></div>
				<?php }?>
				<div style="clear:both"></div>
				<div style="float:right">
				<select name="lc_exclude_rate" class="frm_input lotto-ball">
					<option value="100" <?=$data['lc_exclude_rate'] == '100' ? 'selected' : ''?>>100%</option>
					<option value="80" <?=$data['lc_exclude_rate'] == '80' ? 'selected' : ''?>>80%</option>
					<option value="60" <?=$data['lc_exclude_rate'] == '60' ? 'selected' : ''?>>70%</option>
					<option value="40" <?=$data['lc_exclude_rate'] == '40' ? 'selected' : ''?>>40%</option>
					<option value="20" <?=$data['lc_exclude_rate'] == '20' ? 'selected' : ''?>>20%</option>
					<option value="10" <?=$data['lc_exclude_rate'] == '10' ? 'selected' : ''?>>10%</option>
				</select> 확률로 적용
				</div>
			</td>
			<th width="120px">지정수설정</th>
			<td>
				<?php for($i=1; $i<46; $i++) { ?>
				<div class="include-balls l-ball small <?=in_array($i, $include_numbers) ? 'checked' : ''?>" id="include_ball_<?=$i?>" data-value="<?=$i?>" data-name="include"><?=$i?></div>
				<?php }?>
				<div style="clear:both"></div>
				<div style="float:right">
				<select name="lc_include_rate" class="frm_input lotto-ball">
					<option value="100" <?=$data['lc_include_rate'] == '100' ? 'selected' : ''?>>100%</option>
					<option value="80" <?=$data['lc_include_rate'] == '80' ? 'selected' : ''?>>80%</option>
					<option value="60" <?=$data['lc_include_rate'] == '60' ? 'selected' : ''?>>70%</option>
					<option value="40" <?=$data['lc_include_rate'] == '40' ? 'selected' : ''?>>40%</option>
					<option value="20" <?=$data['lc_include_rate'] == '20' ? 'selected' : ''?>>20%</option>
					<option value="10" <?=$data['lc_include_rate'] == '10' ? 'selected' : ''?>>10%</option>
				</select> 확률로 적용
				</div>
			</td>
		</tr>
		<tr>
			
			
		</tr>
		<tr>
			<th width="120px">연번 허용설정</th>
			<td>
				<select name="lc_permit_continue_num" class="frm_input">
				<? for($i=2; $i<5; $i++) { ?>
					<option value="<?=$i?>" <?=$data['lc_permit_continue_num'] == $i ? 'selected' : ''?>><?=$i?></option>
				<? } ?>
				</select>개 연속번호 허용
			</td>
			<th width="120px">당첨번호 제외</th>
			<td>
				지난 
				<select name="lc_exclude_win_num" class="frm_input">
				<? for($i=1; $i<21; $i++) { ?>
					<option value="<?=$i?>" <?=$data['lc_exclude_win_num'] == $i ? 'selected' : ''?>><?=$i?></option>
				<? } ?>
				</select>회 당첨번호 제외
			</td>
		</tr>
		<tr>
			<th width="120px">AC(산술복잡도)설정</th>
			<td>
				<select name="lc_permit_ac_num" class="frm_input">
				<? for($i=1; $i<10; $i++) { ?>
					<option value="<?=$i?>" <?=$data['lc_permit_ac_num'] == $i ? 'selected' : ''?>><?=$i?></option>
				<? } ?>
				</select>이상만 허용
				[<label><input type="checkbox" name="lc_ac_num_use" value="1" <?=$data['lc_ac_num_use'] == '1' ? 'checked' : ''?>> 사용</label>]
			</td>
			<th width="120px">홀짝비율설정</th>
			<td>
				홀수 
				<select name="lc_odd_rate" id="odd_combo" class="frm_input odd-even" data-oppsit="even_combo">
				<? for($i=0; $i<7; $i++) { ?>
					<option value="<?=$i?>" <?=$data['lc_odd_rate'] == $i ? 'selected' : ''?>><?=$i?></option>
				<? } ?>
				</select> / 
				짝수
				<select name="lc_even_rate" id="even_combo" class="frm_input odd-even" data-oppsit="odd_combo">
				<? for($i=0; $i<7; $i++) { ?>
					<option value="<?=$i?>" <?=$data['lc_even_rate'] == $i ? 'selected' : ''?>><?=$i?></option>
				<? } ?>
				</select>
				[<label><input type="checkbox" name="lc_uoddEven_use" value="1" <?=$data['lc_uoddEven_use'] == '1' ? 'checked' : ''?>> 사용</label>]
			</td>
		</tr>
		</table>
		<div style="width:100%;text-align:center;padding-top:30px">
			<button type="button" class="as-btn small green" onClick="checkExtractorConfigForm();void(0);"><i class="fa fa-refresh"></i> 수정</button>
			<button type="button" class="as-btn small red" onclick="history.back(-1);"><i class="fa fa-close"></i> 취소</button>
		</div>
		<br /><br />
		</form>
	</div>
	<div class="btitle">
		<i class="fa fa-folder-open-o"></i> 추출설정
	</div>
	
	
	<div class="content_wrap">
	<div>
		 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 서비스등급별 자동추출 갯수 및 사용자가 직접 추출기에서 추출할 수 있는 번호 갯수를 지정합니다.</span>
		 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 'SMS 발송요일'은 해당 회원가입시 회원에게 기본을 설정되는 발송요일이며, 추후 마이페이지에서 회원이 수정할 수 있습니다.</span>
		 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> '당첨번호발송 SMS설정'은 설정된 등수 당첨시에만 당첨 SMS를 발송하게 됩니다.</span>
		 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 각 발송의 '자동발송'을 체크하지 않으시면, 자동발송되지 않으며 '로또서비스관리 > SMS 메세지 큐 관리'에서 승인하셔야 발송이 됩니다.</span>
	</div>
	
	<form name="config_form" method="post" action="./lotto_config_process.php" enctype="multipart/form-data">
	<input type="hidden" name="proc" value="updateServiceConfig">
	<input type="hidden" name="return_url" value="<?=$return_url?>">
	
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="tb02">
	<tr>
		<th width="120px">등급별 주당<br />자동 발급갯수</th>
		<td>
			<? foreach($serviceGrade as $key => $row) { ?>
			<div style="padding:2px"><span style="display:inline-block;width:80px;text-align:right"><?=$row?></span> : <input type="text" name="lc_extract_count[<?=$key?>]" class="frm_input" style="width:50px" value="<?=$extract_count[$key]?>">개</div>
			<? }?>
			<div style="padding:2px"><span style="display:inline-block;width:80px;text-align:right">일반</span> : <input type="text" name="lc_extract_count[0]" class="frm_input" style="width:50px" value="<?=$extract_count[0]?>">개</div>
		</td>
		<th width="120px">등급별 주당<br />사용자지정 발급갯수</th>
		<td>
			<? foreach($serviceGrade as $key => $row) { ?>
			<div style="padding:2px"><span style="display:inline-block;width:80px;text-align:right"><?=$row?></span> : <input type="text" name="lc_user_extract_count[<?=$key?>]" class="frm_input" style="width:50px" value="<?=$user_extract_count[$key]?>">개</div>
			<? }?>
			<div style="padding:2px"><span style="display:inline-block;width:80px;text-align:right">일반</span> : <input type="text" name="lc_user_extract_count[0]" class="frm_input" style="width:50px" value="<?=$user_extract_count[0]?>">개</div>
		</td>
	</tr>
	
	<tr>
		<th width="120px">발급SMS발송 요일</th>
		<td>
			<? for($i=1; $i<6; $i++) { ?>
			<label><input type="radio" name="lc_send_weekdays" value="<?=$i?>" <?=($i==$data['lc_send_weekdays']) ? 'checked' : ''?>><?=$termService->config['weekdays_name'][$i]?>&nbsp;&nbsp;</label>
			<? } ?>
			( <label><input type="checkbox" name="lc_numbers_sms_auto" value="1" <?=$data['lc_numbers_sms_auto'] == '1' ? 'checked' : '' ?>> 발급번호 SMS자동발송 - 회원설정에 'SMS수신' 체크된 회원만 자동SMS발송 </label> )
		</td>
		<th width="120px">당첨SMS발송설정</th>
		<td>
			<? for($i=1; $i<6; $i++) { ?>
			<label><input type="checkbox" name="lc_send_win_result[]" value="<?=$i?>" <?=in_array($i, $send_win_result) ? 'checked' : ''?>><?=$i?>등</label>
			<? } ?>
			( <label><input type="checkbox" name="lc_winner_sms_auto" value="1" <?=$data['lc_winner_sms_auto'] == '1' ? 'checked' : '' ?>> 당첨축하 SMS자동발송</label> )
		</td>
	</tr>
	<tr>
		
	</tr>
	</table>

	<br /><br /><br />
	

	<div style="width:100%;text-align:center;padding-top:30px">
		<button type="button" class="as-btn small green" onClick="checkConfigForm();void(0);"><i class="fa fa-refresh"></i> 수정</button>
		<button type="button" class="as-btn small red" onclick="history.back(-1);"><i class="fa fa-close"></i> 취소</button>
	</div>
	</form>
</div>


<script type="text/javascript">

$(document).ready(function() {
	$('.l-ball').on('click', function() {
		if($(this).hasClass("checked")) {
			$(this).removeClass("checked");
		} else {
			$(this).addClass("checked");
		}

		var name = $(this).attr('data-name');

		var cur_balls = "";
		var oppsit_balls = "";
		var num = "";

		if(name == "include") {
			cur_balls = "include";
			opp_balls = "exclude";
		} else {
			cur_balls = "exclude";
			opp_balls = "include";
		}
	

		$('.'+cur_balls+'-balls').each(function() {
			var cur_num = "";
			if($(this).hasClass('checked')) {
				cur_num = $(this).attr('data-value')
				num += (num != "") ? ","+ cur_num : cur_num;

				checkOppositBalls(opp_balls, cur_num)
			}
		});

		$('#'+name+'_numbers').val(num);
		
	});


	function checkOppositBalls(type, num) { console.log('#'+type+'_ball_'+num);
		if($('#'+type+'_ball_'+num).hasClass('checked')) {
			$('#'+type+'_ball_'+num).removeClass('checked');
		}

		var num = "";
		$('.'+type+'-balls').each(function() {
			var cur_num = "";
			if($(this).hasClass('checked')) {
				cur_num = $(this).attr('data-value')
				num += (num != "") ? ","+ cur_num : cur_num;

			}
		});

		$('#'+type+'_numbers').val(num);
	}

	$('.odd-even').on('change', function() {
		var val = $(this).val();
		var opposit_id = $(this).attr('data-oppsit');

		$('#'+opposit_id).val(6-val).attr('selected', true);

	});
});

function checkExtractorConfigForm() {
	var f = document.extractor_config_form;

    if (f.onsubmit && !f.onsubmit()) {
		return false;
	}

	f.submit();
}

function checkConfigForm() {
	var f = document.config_form;

    if (f.onsubmit && !f.onsubmit()) {
		return false;
	}


	f.submit();
}
/*
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
*/
</script>

<?
include_once (G5_ADMIN_PATH."/admin.tail.php");
?>
