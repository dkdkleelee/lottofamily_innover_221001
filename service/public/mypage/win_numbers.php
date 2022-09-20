<?php
include_once("./_common.php");

$cur = 1;
include_once("../../../head_05.php");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Lotto;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\TermService;

$param1 = Utils::getParameters(array('page'));
$list_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$param1;

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
$_GET['type'] = 'extractor';	// 추출기 추출번호만(사용자 등록번호 제외)
$lottoService = new LottoService();
$data = $lottoService->getWinResultList($_GET['page'], $list_url);

$accumulated_result = $lottoService->getAccumulatedWinRecords($_SESSION['ss_mb_id']);

?>

<link rel="stylesheet" href="../css/custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/lotto.css" type="text/css">
<link rel="stylesheet" href="../css/paginate.css" type="text/css">
<script src="../js/jquery-ui-1.8.7.custom.min.js" ></script>
<script src="../js/jquery.ui.datepicker-ko.js"  charset="utf-8"></script>
<link rel="stylesheet" href="../js/datetimepicker/jquery.datetimepicker.css" type="text/css" />
<script type="text/javascript" src="../js/datetimepicker/jquery.datetimepicker.js" charset="utf-8"></script>

<div class="info_container">
<div class="btitle">
	<div class="btitle_top"></div>
	<div class="btitle_text">회차별 당첨내역</div>
	<div class="btitle_locate">&gt; 마이페이지&gt; 당첨내역</div>
	<div class="btitle_line"></div>
</div>
<div class="content_wrap">
<?

	
	
?>
	<div>
		 <h5 class="title"><i class="fa fa-asterisk fa-lg"></i> 누적 당첨내역</h5>
	</div>
	<table width="100%" border="0" cellpadding="0" class="tb02">
	<?php for($i=1; $i<=5; $i++) { ?>
		<th width="5%"><?=$i?>등</th>
		<td width="15%" style="text-align:right">
			<div style="padding:3px;border-bottom:1px solid #e8e8e8"><?=number_format($accumulated_result[$i]['cnt'])?>회</div>
			<div style="padding:3px;color:#3366cc"><?=number_format($accumulated_result[$i]['prize'])?>원</div>
		</td>
	<?php } ?>
	</table>
	<br />
	<div>
		 <h5 class="title"><i class="fa fa-asterisk fa-lg"></i> <?=$_GET['s_inning']?>회차 당첨내역</h5>
	</div>
	<div id="s_anchor" class="search_box ar">
		<form name="searchForm" method="get" action="?#s_anchor">
			<input type="hidden" name="id" id="id" value="<?=$_GET['id']?>">
			<input type="hidden" name="s_service_use" id="s_service_use" value="<?=$_GET['s_service_use']?>">
			<input type="hidden" name="sc" id="sc" value="<?=$_GET['sc']?>">
			<input type="hidden" name="sv" id="sv" value="<?=$_GET['sv']?>">
			<input type="hidden" name="m" value="<?=$_GET['m']?>">
			<select name="s_inning" id="s_inning" class="frm_input" onChange="document.searchForm.submit()">
				<option value="">회차선택</option>
			<? for($i=0; $i<count($inning_arr); $i++ ) { ?>
				<option value="<?=$inning_arr[$i]['inning']?>" <?=($_GET['s_inning'] == $inning_arr[$i]['inning']) ? 'selected' : ''?>><?=$inning_arr[$i]['inning']?>회</option>
			<? } ?>
			</select>
			<button type="button" class="as-btn medium white" id="next"><i class="fa fa-chevron-left"></i> 이전</button>
			<button type="button" class="as-btn medium white" id="prev" >다음 <i class="fa fa-chevron-right"></i></button>
			
		</form>
	</div>
	
	<br />
	<table width="100%" border="0" cellpadding="0" class="tb02">
	<tr>
		<th style="width:10%">회차</th>
		<th style="width:30%">번호</th>
		<th style="width:10%">구분</th>
		<th style="width:15%">당첨금액</th>
		<th style="width:10%">추첨결과</th>
	</tr>
	<?
		foreach($data['list'] as $row) {
			$array_prize_idx = array(1 => "lw_1st_prize_ea", 2 => "lw_2nd_prize_ea", 3 => "lw_3rd_prize_ea", 4 => "lw_4th_prize_ea", 5 => "lw_5th_prize_ea");
			
	?>
	<tr>
		<th><?=$row['lwr_inning']?>회</th>
		<td style="text-align:center">
			<img src="/images/ball_<?=sprintf('%02d', $row['lwr_num1'])?>.png" width="30px">
			<img src="/images/ball_<?=sprintf('%02d', $row['lwr_num2'])?>.png" width="30px">
			<img src="/images/ball_<?=sprintf('%02d', $row['lwr_num3'])?>.png" width="30px">
			<img src="/images/ball_<?=sprintf('%02d', $row['lwr_num4'])?>.png" width="30px">
			<img src="/images/ball_<?=sprintf('%02d', $row['lwr_num5'])?>.png" width="30px">
			<img src="/images/ball_<?=sprintf('%02d', $row['lwr_num6'])?>.png" width="30px">
		</td>
		<td style="text-align:center"><?=$lottoService->getNumberType()[$row['le_type']]?></td>
		<td style="text-align:center"><?=number_format($row[$array_prize_idx[$row['lwr_grade']]])?>원</td>
		<td style="text-align:center"><?=($row['lwr_grade']) ? $row['lwr_grade']."등" : '--'?></td>
	</tr>
	<? } ?>
	</table>
	<p />

	<div class="paginate wrapper paging_box">
			<?=$data['link']?>
	</div>

	<? if (!isset($data['link'])) { ?>
	<br><br><br><br><br><br><br><br><br><br><br><br>
	<? } ?>
		

</div>
</div>
</div>
<script>
$(document).ready(function() {
	$('.l-ball').on('click', function() {

		var name = $(this).attr('data-name');
		var num = "";

		var selected_str = $('#'+name+'_numbers').val();
		selected_arr = selected_str.split(',');

		if($(this).hasClass("checked")) {
			$(this).removeClass("checked");
		} else {
			if(selected_arr.length < 6) {
				$(this).addClass("checked");
			} else {
				alert("6개만 선택 가능합니다.");
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

function addNumbers() {
	// 번호 아이디 지정발급 ( 발급일시 le_issued_date는 문자발송시 값 설정)
	var f = document.addNumberForm;
	var numbers = f.selected_numbers.value;
	var num_arr = numbers.split(',');
	if(num_arr.length != 6) {
		alert("6개 숫자를 선택해 주세요.");
		return false;
	}

	f.submit();
}

$("#next").click(function() {
  var nextElement = $('#s_inning > option:selected').next('option');
  if ($(nextElement).val() > 0) {
    $('#s_inning > option:selected').removeAttr('selected').next('option').attr('selected', 'selected');

	document.searchForm.submit();
  }
});

$("#prev").click(function() {
  var nextElement = $('#s_inning > option:selected').prev('option');
  if ($(nextElement).val() > 0) {
    $('#s_inning > option:selected').removeAttr('selected').prev('option').attr('selected', 'selected');

	document.searchForm.submit();
  }
});

</script>
<?php
include_once("../../../tail.php");
?>
