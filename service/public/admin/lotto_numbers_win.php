<?
define('G5_IS_ADMIN', true);
$sub_menu = "500100";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'r');

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\LottoServiceConfig;

//▶ pagination url
$param1 = Utils::getParameters(array('page','s_ca_no'))."&s_ca_no[]=".@array_pop(array_filter($_GET['s_ca_no']));
$list_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$param1;

//▶ 설정정보 인출
$lottoServiceConfig = new LottoServiceConfig();
$data_config = $lottoServiceConfig->getConfig();

$include_numbers = explode(",", $data_config['lc_include_numbers']);
$exclude_numbers = explode(",", $data_config['lc_exclude_numbers']);

// lotto service
$lottoService = new LottoService();

//▶ 추출번호 inning data
$inning_arr = $lottoService->getWinResultInningGroups();

if(!$_GET['s_inning']) $_GET['s_inning'] = $inning_arr[0]['inning'];


// 당첨결과
if($data_config['inning'] <= $_GET['s_inning']) {
	$result = $lottoService->getWinResult($_GET['s_inning']);
}

//▶ get list data
$data = $lottoService->getWinResultList($_GET['page'], $list_url);

// get win data
$win_data = $lottoService->getWinData($_GET['s_inning']);

$member_arr = $lottoService->db->arrayBuilder()->get($lottoService->tb['Member'], null, '*');

$g5['title'] = "회차별 당첨조회";
include_once(G5_ADMIN_PATH."/admin.head.php");
?>
<link rel="stylesheet" href="../css/custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/admin.custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/lotto.css" type="text/css">
<!-- <link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css" rel="stylesheet" /> -->
<script src="../js/jquery-ui-1.8.7.custom.min.js" ></script>
<script src="../js/jquery.ui.datepicker-ko.js"  charset="utf-8"></script>
<link rel="stylesheet" href="../js/datetimepicker/jquery.datetimepicker.css" type="text/css" />
<script type="text/javascript" src="../js/datetimepicker/jquery.datetimepicker.js" charset="utf-8"></script>

<div class="info_container">
<!-- <div class="btitle">
	<i class="fa fa-folder-open-o"></i> <?=$title?>
</div> -->
<div class="content_wrap">
    <div>
		 <span class="sub_title"><i class="fa fa fa-plus fa-lg fa-lg f_blue"></i> <?=$_GET['s_inning']?>회 당첨번호</span>
	</div>

	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tb02">
	<tr>
		<th width="150px">추첨일</th>
		<td>
			<?=$win_data['lw_date']?>
		</td>
		<th width="150px">당첨번호</th>
		<td style="padding-left:30px;text-align:left">
		<? if($win_data['lw_no']) { ?>
			<img src="../images/balls/ball_<?=$win_data['lw_num1']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$win_data['lw_num2']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$win_data['lw_num3']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$win_data['lw_num4']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$win_data['lw_num5']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$win_data['lw_num6']?>.png" width="30px"> + <img src="../images/balls/ball_<?=$win_data['lw_num7']?>.png" width="30px">
		<? } else { ?>
			--
		<? } ?>
		</td>
	</tr>
	</table>
	<br /><br />
	<div>
		 <span class="sub_title"><i class="fa fa fa-plus fa-lg fa-lg f_blue"></i> <?=$_GET['s_inning']?>회 당첨내역</span>
	</div>

	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tb02">
	<tr>
	<?php for($i=1; $i<=5; $i++) { ?>
		<th width="10%"><?=$i?>등</th>
		<td width="10%">
			<div style="padding:5px;border-bottom:1px solid #e8e8e8"><?=number_format($result[$i]['cnt'])?>명</div>
			<div style="padding:5px;"><b><?=number_format($result[$i]['prize_tot'])?>원</b></div>
		</td>
	<?php } ?>
	</tr>
	</table>
	<br /><br />
	<div>
		 <span class="sub_title"><i class="fa fa fa-plus fa-lg fa-lg f_blue"></i> 당첨번호목록</span>
	</div>
	<div id="pn_anchor" class="search_box ar">
		<form name="searchForm" method="get" action="?#pn_anchor">
				<input type="hidden" name="proc">
				<select name="s_inning" id="s_inning" class="frm_input" onChange="document.searchForm.submit()">
            		<option value="">회차선택</option>
					
                <? for($i=0; $i<count($inning_arr); $i++ ) { ?>
                    <option value="<?=$inning_arr[$i]['inning']?>" <?=($_GET['s_inning'] == $inning_arr[$i]['inning']) ? 'selected' : ''?>><?=$inning_arr[$i]['inning']?>회 당첨번호</option>
                <? } ?>
            	</select>
				<button type="button" class="as-btn medium white" id="next"><i class="fa fa-chevron-left"></i> 이전</button>
				<button type="button" class="as-btn medium white" id="prev" >다음 <i class="fa fa-chevron-right"></i></button>
				<!-- <button type="submit" class="as-btn small white"><i class="fa fa-search"></i> 검색</button> -->
		</form>
	</div>
	<div class="list-count">총 <?=number_format($data['total_count'])?>개</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
		<th width="40px">번호</th>
		<th>회차</th>
		<th>추출일자</th>
		<th>추출 번호</th>
		<th>A/C</th>
		<th>당첨결과</th>
		<th>발급회원</th>
		<th width="180px">관리</th>
	</tr>
	<?
	foreach($data['list'] as $row) {

	?>
	<form name="service_use_form_<?=$row['su_no']?>" id="service_use_form_<?=$row['su_no']?>" method="post" action="term_service_use_process.php">
    	<input type="hidden" name="proc" value="updateServiceUse">
    	<input type="hidden" name="su_no" value="<?=$row['su_no']?>">
	<tr>
		<td><?=$data['idx']--?></td>
		<td><?=$row['lwr_inning']?>회</td>
		<td>
			<?=$row['lwr_datetime']?>
		</td>
		<td>
			<img src="../images/balls/ball_<?=$row['lwr_num1']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['lwr_num2']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['lwr_num3']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['lwr_num4']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['lwr_num5']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['lwr_num6']?>.png" width="30px">
		</td>
		<td>
    		<?=$lotto->acFilter(array($row['lwr_num1'],$row['lwr_num2'],$row['lwr_num3'],$row['lwr_num4'],$row['lwr_num5'],$row['lwr_num6']))?>
		</td>
		<td><?=($row['lwr_grade']) ? $row['lwr_grade'] : '--'?>등</td>
		<td>
			<? if($row['mb_id']) { ?>
				<?=$row['mb_name']?>(<?=$row['mb_id']?>)
			<? } else { ?>
				미발급
			<? } ?>
		</td>
		<td>
			<? if($row['mb_id']) { ?>
				<button type="button" class="as-btn small green" onClick="manageService('<?=$row['mb_id']?>');"><i class="fa fa-wrench"></i> 관리</button>
			<? } else { ?>
				--
			<? } ?>
		</td>
	</tr>
	</form>
	<? }?>
	</table>
	<p />
	<div class="btn_box ar">
		<button type="button" class="as-btn small blue" onclick="excel_download();"><i class="fa fa-download"></i> 엑셀다운로드</button>
	</div>
	<div class="paginate wrapper paging_box">
			<?=$data['link']?>	
	</div>
</div>
</div>
<form name="deleteForm" method="post" action="./term_service_use_process.php?<?=$param?>">
<input type="hidden" name="proc" value="deleteServiceUse">
<input type="hidden" name="no" value="">
</form>
<script type="text/javascript">
<!--

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

function checkForm() {
	var f = document.write_form;

    if (f.onsubmit && !f.onsubmit()) {
		return false;
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

function excel_download() {
	var f = document.searchForm;
	f.proc.value = "downloadWinnerExcel";
	f.action = "./lotto_win_numbers_process.php";
	f.submit();
	f.proc.value = "";
	f.action = "";
}

function manageService(id) {
	window.open("./term_service_sms_management.php?id="+id, 'manage', 'width=750, height=800, scrollbars=yes');
}
function memoWin(no) {
	window.open("./ia_request_memo.php?type=su_no&idx="+no, '', 'width=550, height=600');
}



function addData() {
    var f = $('#service_add_form');
    $(f).submit();
    
}

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
//-->
</script>
<?
include_once (G5_ADMIN_PATH."/admin.tail.php");
?>