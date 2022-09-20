<?php
include_once("./_common.php");

$cur = 1;
include_once("../../head_02.php");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Lotto;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\TermService;

$param1 = Utils::getParameters(array('page'));
$list_url = "http://".$_SERVER[HTTP_HOST].$_SERVER[PHP_SELF]."?".$param1;

/*
$lottoServiceConfig = new LottoServiceConfig();
$serviceConfig = $lottoServiceConfig->getConfig();

//▶ 등급별 발급설정갯수
$extract_count_per_grade = unserialize($serviceConfig['lc_extract_count']);
*/

//▶ 추출번호 inning data
$inning_arr = $lottoService->getExtractNumberInningGroups();
if(!$_GET['s_inning']) $_GET['s_inning'] = $inning_arr[0]['inning'];


$mb = get_member($_SESSION['ss_mb_id']);
$send_weekdays = explode(",", $_SESSION['ss_mb_id']);

// 회차별 발급목록
$_GET['mb_id'] = $_SESSION['ss_mb_id'];
$_GET['type'] = 'user_exclude';	// 추출기 추출번호만(사용자 등록번호 제외)
$lottoService = new LottoService();
$data = $lottoService->getExtractNumberList($_GET['page'], $list_url);

?>

<link rel="stylesheet" href="./css/custom.style.css" type="text/css">
<link rel="stylesheet" href="./css/lotto.css" type="text/css">
<link rel="stylesheet" href="./css/paginate.css" type="text/css">
<script src="./js/jquery-ui-1.8.7.custom.min.js" ></script>
<script src="./js/jquery.ui.datepicker-ko.js"  charset="utf-8"></script>
<link rel="stylesheet" href="./js/datetimepicker/jquery.datetimepicker.css" type="text/css" />
<script type="text/javascript" src="./js/datetimepicker/jquery.datetimepicker.js" charset="utf-8"></script>

<div class="info_container">
<div class="btitle">
	<div class="btitle_top"></div>
	<div class="btitle_text"> 제외수 셀프조합기</div>
	<div class="btitle_locate">&gt; 패밀리 조합기&gt; 제외수 셀프조합기</div>
	<div class="btitle_line"></div>
</div>
<div class="content_wrap">

	<div>
		 <h5 class="title"><i class="fa fa-asterisk fa-lg"></i> <?=$_GET['s_inning']?>회차 예측번호 추출</h5>
	</div>

	<form name="addNumberForm" method="post" action="./mypage_process.php">
	<input type="hidden" name="proc" value="addNumber">
	<input type="hidden" name="selected_numbers" id="selected_numbers">
	<input type="hidden" name="mb_id" value="<?=$_SESSION['ss_mb_id']?>">
	<input type="hidden" name="return_url" value="<?=$list_url?>">
	<div class="">
		<p>당첨확률이 낮다고 생각하시는 숫자들을 선택해 주세요.</p>
		<p></p>
	</div>
	<div style="width:100%;text-align:center;background:#e8e8e8;padding-top:50px">
		<div style="border-radius:20px;width:700px;background-color:#ffffff;margin:0px auto">
			<table width="700px" border="0" cellpadding="0">
			<tr>
				<td>
					<!-- <div class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 제외수를 선택해 주세요.</div> -->
					<?php for($i=1; $i<46; $i++) { ?>
						<!-- <div class="l-ball selected-balls small <?=in_array($i, $exclude_numbers) ? 'checked' : ''?>" id="selected_ball_<?=$i?>" data-value="<?=$i?>" data-name="selected" ><?=$i?></div> -->
						<div class="img-ball selected-balls"  id="selected_ball_<?=$i?>" data-value="<?=$i?>" data-name="selected">
							<img src="/images/ball_<?=sprintf('%02d', $i)?>.png" width="30px">
						</div>
					<?php }?>
				</td>
			</tr>
			</table>
		</div>
	<br />
		<? if(!Utils::isShutdown()) { ?>
			<div class="btn-box ac">
				<button type="button" class="as-btn medium blue" onClick="<?=$_SESSION['ss_mb_id'] ? 'extNumbers();' : 'alert(\'로그인 후 이용해 주세요.\');location.href=\'/bbs/login.php\''?>"><i class="fa fa-refresh"></i> 조합생성</button>
			</div>
		<? } else { ?>
			<div style="color:#ff3333;padding:10px 0px">사용자번호등록은 월요일부터 가능합니다.</div>
		<? } ?>
		
	</div>
	</form>
	<br /><br />
	<div style="width:100%;text-align:center;background:#e8e8e8;padding-top:10px;padding-bottom:10px">
		<div id="result_container" style="border-radius:20px;background:#ffffff;height:150px;width:700px;margin:0px auto">
			<div id="loading" style="display:none;text-align:center"><img src="./images/103.gif"></div>
			<div id="result_numbers"  style="text-align:center;padding-top: 35px;">

			</div>
		</div>
	</div>
	<p />

</div>
</div>
</div>
<script>
$(document).ready(function() {
	$('.img-ball').on('click', function() {

		var name = $(this).attr('data-name');
		var num = "";

		var selected_str = $('#'+name+'_numbers').val();
		selected_arr = selected_str.split(',');

		if($(this).hasClass("checked")) {
			$(this).removeClass("checked");
		} else {
			if(selected_arr.length < 10) {
				$(this).addClass("checked");
			} else {
				alert("10개까지 선택 가능합니다.");
			}
		}

		$('.'+name+'-balls').each(function() {
			var cur_num = "";
			if($(this).hasClass('checked')) {
				cur_num = $(this).attr('data-value')
				num += (num != "") ? ","+ cur_num : cur_num;
			}
		});

		$('#'+name+'_numbers').val(num);
		
	});
});

function zeroPad(nr,base){
  var  len = (String(base).length - String(nr).length)+1;
  return len > 0? new Array(len).join('0')+nr : nr;
}

function extNumbers() {
	var numbers = $('#selected_numbers').val();

	if(numbers == '') {
		alert("제외수를 1개이상 선택해 주세요.");
		return false;
	}

	$('#loading').show();
	$.ajax({
		type: "POST",
		url: './lotto_common_process.php',
		dataType: 'json',
		async: true,
		cache:false,
		data: {numbers: numbers, proc: 'extractOutNumbers'} , // serializes the form's elements.
		success: function(data) {

			$('#loading').hide();
			$('#result_numbers').html('');

			if(data['result'] == 'ok') {
				$(data['numbers']).each(function(idx, val) {
					$('#result_numbers').append('<img src="/images/ball_'+zeroPad(val,10)+'.png" width="50px" style="padding:5px">');
				});

				$('#result_numbers').addClass('tada');
				$('#result_numbers').addClass('animated');
			} else if(data['result'] == 'full') {
				$('#result_numbers').html("이번회차 추출가능 갯수를 초과하는 요청입니다.");
				
			} else if(data['result'] == 'fail') {
				$('#result_numbers').html("서버과부하로 지정된 시간안에 생성이 확인되지 않습니다. 잠시 후 다시 시도해주세요");
				
			}
			
		}
	});
}

</script>
<?php
include_once("../../tail.php");
?>