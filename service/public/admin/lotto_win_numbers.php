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


//▶ 당첨 inning data
$inning_arr = $lottoService->getWinNumberInningGroups();


//▶ get list data
$data = $lottoService->getWinNumberList($_GET['page'], $list_url);

// 당첨확인
//$lottoService->checkResult($inning_arr[0]['inning'], true);


// 크론발송 테스트
//$lottoService->setNumbersToUser();

//$member_arr = $lottoService->db->arrayBuilder()->get($lottoService->tb['Member'], null, '*');


$g5['title'] = "추첨번호관리";
include_once(G5_ADMIN_PATH."/admin.head.php");
?>
<link rel="stylesheet" href="../css/custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/admin.custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/lotto.css" type="text/css">
<link rel="stylesheet" href="../css/paginate.css" type="text/css">
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
		 <span class="sub_title"><i class="fa fa fa-plus fa-lg fa-lg f_blue"></i> 회차별 추첨번호목록</span>
	</div>
	<div class="search_box ar">
		<form name="searchForm" method="get" action="?">
				
				<select name="s_inning" id="s_inning" class="frm_input" onChange="document.searchForm.submit()">
            		<option value="">회차선택</option>
                <? for($i=0; $i<count($inning_arr); $i++ ) { ?>
                    <option value="<?=$inning_arr[$i]['inning']?>" <?=($_GET['s_inning'] == $inning_arr[$i]['inning']) ? 'selected' : ''?>><?=$inning_arr[$i]['inning']?>회 추첨번호</option>
                <? } ?>
            	</select>
				
				<button type="submit" class="as-btn medium white"><i class="fa fa-search"></i> 검색</button>
				<button type="button" class="as-btn medium green" onClick="getWinNumbers()"><i class="fa fa-search"></i> 다음회차 추첨번호 확인</button>
		</form>
	</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
		<th width="40px">회차</th>
		<th>추첨일</th>
		<th>추첨번호</th>
		<th>추첨번호변경</th>
		<th>1등</th>
		<th>2등</th>
		<th>3등</th>
		<th>4등</th>
		<th>5등</th>
		<th width="180px">변경</th>
	</tr>
	<?
	foreach($data['list'] as $row) {

	?>
	<form name="service_use_form_<?=$row['lw_no']?>" id="service_use_form_<?=$row['lw_no']?>" method="post" action="lotto_win_numbers_process.php">
    	<input type="hidden" name="proc" value="modifyWinNumbers">
    	<input type="hidden" name="lw_no" value="<?=$row['lw_no']?>">
	<tr>
		<td><?=$row['lw_inning']?></td>
		<td><?=$row['lw_date']?></td>
		<td>
			<img src="../images/balls/ball_<?=$row['lw_num1']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['lw_num2']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['lw_num3']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['lw_num4']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['lw_num5']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['lw_num6']?>.png" width="30px"> + <img src="../images/balls/ball_<?=$row['lw_num7']?>.png" width="30px">
		</td>
		<td>
			<input type="text" class="frm_input" name="numbers[]" style="width:30px;text-align:center" value="<?=$row['lw_num1']?>" required>
			<input type="text" class="frm_input" name="numbers[]" style="width:30px;text-align:center" value="<?=$row['lw_num2']?>" required>
			<input type="text" class="frm_input" name="numbers[]" style="width:30px;text-align:center" value="<?=$row['lw_num3']?>" required>
			<input type="text" class="frm_input" name="numbers[]" style="width:30px;text-align:center" value="<?=$row['lw_num4']?>" required>
			<input type="text" class="frm_input" name="numbers[]" style="width:30px;text-align:center" value="<?=$row['lw_num5']?>" required>
			<input type="text" class="frm_input" name="numbers[]" style="width:30px;text-align:center" value="<?=$row['lw_num6']?>" required> + 
			<input type="text" class="frm_input" name="numbers[]" style="width:30px;text-align:center" value="<?=$row['lw_num7']?>" required>
		</td>
		<td><?=number_format($row['lw_1st_count'])?><br />(<?=number_format($row['lw_1st_prize_ea'])?>)</td>
		<td><?=number_format($row['lw_2nd_count'])?><br />(<?=number_format($row['lw_2nd_prize_ea'])?>)</td>
		<td><?=number_format($row['lw_3rd_count'])?><br />(<?=number_format($row['lw_3rd_prize_ea'])?>)</td>
		<td><?=number_format($row['lw_4th_count'])?><br />(<?=number_format($row['lw_4th_prize_ea'])?>)</td>
		<td><?=number_format($row['lw_5th_count'])?><br />(<?=number_format($row['lw_5th_prize_ea'])?>)</td>
		<td>
			<button type="button" class="as-btn small green" onClick="modifyData('<?=$row['lw_no']?>');"><i class="fa fa-refresh"></i> 수정</button>
			<button type="button" class="as-btn small green" onClick="reCheckWinNumbers('<?=$row['lw_inning']?>', '<?=$row['lw_no']?>');"><i class="fa fa-refresh"></i> 다시가져오기</button>
		</td>
	</tr>
	</form>
	<? }?>
	</table>
	<p />
	
	<div class="paginate wrapper paging_box">
			<?=$data['link']?>	
	</div>
</div>
</div>
<form name="getWinNumbersForm" method="post" action="./lotto_win_numbers_process.php?<?=$param?>">
<input type="hidden" name="proc" value="getNextWinNumbers">
<input type="hidden" name="no" value="">
</form>
<form name="reCheckWinNumbersForm" method="post" action="./lotto_win_numbers_process.php?<?=$param?>">
<input type="hidden" name="proc" value="reCheckWinNumbers">
<input type="hidden" name="lw_no" value="">
<input type="hidden" name="inning" value="">
</form>
<form name="deleteForm" method="post" action="./lotto_win_numbers.php?<?=$param?>">
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
});

function checkForm() {
	var f = document.write_form;

    if (f.onsubmit && !f.onsubmit()) {
		return false;
	}
}

function getWinNumbers(inning='') {
	var f = document.getWinNumbersForm;

	if(inning) {
		f.no.value = inning;
	}

	f.submit();
}

function reCheckWinNumbers(inning, no) {
	var f = document.reCheckWinNumbersForm;

	if(inning && no) {
		f.inning.value = inning;
		f.lw_no.value = no;
	}

	f.submit();
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

function manageService(id) {
	window.open("./term_service_management.php?id="+id, '', 'width=650, height=600, scrollbar-y=yes');
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
//-->
</script>
<?
include_once (G5_ADMIN_PATH."/admin.tail.php");
?>