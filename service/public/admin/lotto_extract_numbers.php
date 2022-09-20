	<?
define('G5_IS_ADMIN', true);
$sub_menu = "500100";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'r');

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\TermService;

// 서비스
$termService = new TermService();
$termServiceGrade = $termService->getTermServiceGrade();

//▶ pagination url
$param1 = Utils::getParameters(array('page','s_ca_no'));
$param2 = Utils::getParameters(array('nocache'));
$list_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$param1;
$return_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$param2;

//▶ 설정정보 인출
$lottoServiceConfig = new LottoServiceConfig();
$data_config = $lottoServiceConfig->getConfig($_GET['s_sg_no'] ?? '0');

$include_numbers = explode(",", $data_config['lc_include_numbers']);
$exclude_numbers = explode(",", $data_config['lc_exclude_numbers']);

// lotto service
$lottoService = new LottoService();

//▶ 추출번호 inning data
$inning_arr = $lottoService->getExtractNumberInningGroups();

if(!$_GET['s_inning']) $_GET['s_inning'] = $inning_arr[0]['inning'];


// 당첨결과
if($data_config['inning'] <= $_GET['s_inning']) {
	$result = $lottoService->getWinResult($_GET['s_inning']);
}


//▶ get list data
$data = $lottoService->getExtractNumberList($_GET['page'], $list_url);


// get win data
$win_data = $lottoService->getWinData($_GET['s_inning']);

// 회원에게 번호발급(cron 에서 주기적실행)
//$lottoService->setNumbersToUser();

$member_arr = $lottoService->db->arrayBuilder()->get($lottoService->tb['Member'], null, '*');

$g5['title'] = "회차별 추출번호관리";
include_once(G5_ADMIN_PATH."/admin.head.php");
?>
<link rel="stylesheet" href="../css/custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/admin.custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/lotto.css" type="text/css">
<script src="../js/jquery-ui-1.8.7.custom.min.js" ></script>
<script src="../js/jquery.ui.datepicker-ko.js"  charset="utf-8"></script>
<link rel="stylesheet" href="../js/datetimepicker/jquery.datetimepicker.css" type="text/css" />
<script type="text/javascript" src="../js/datetimepicker/jquery.datetimepicker.js" charset="utf-8"></script>

<div class="search_box ar" class="frm_input"><!-- 2020. 1. 2. -->
<form name="search_form" method="get" action="">
	추출기 선택: <select name="s_sg_no" onChange="document.search_form.submit()">
		<option value="">기본추출기</option>
	<?php foreach($termServiceGrade as $key => $value) { ?>
		<option value="<?=$key?>" <?=$key == $_GET['s_sg_no'] ? 'selected' : ''?>><?=$value?>추출기</option>
	<?php } ?>
	</select>
</form>
</div>
<div class="info_container">
<!-- <div class="btitle">
	<i class="fa fa-folder-open-o"></i> 서비스관리
</div> -->
<div class="content_wrap">
    <div>
		 <span class="sub_title"><i class="fa fa fa-plus fa-lg fa-lg f_blue"></i> 번호추출</span>
	</div>
	<div>
		 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 관리자가 수동으로 지정된 필터를 이용해 지정한 갯수만큼 추출이 가능합니다.</span>
		 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 한번에 추출하는 번호는 '10000'개를 초과하지 않게 설정하시기 바랍니다.</span>
		 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 필터값설정시 경우의수가 너무 작아지면 추출시간이 상당히 길어질 수 있으니 필히 확인하시어 지정하시기 바랍니다.</span>
		 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 제공되는 필터는 일반적인 기본 분석방법들로 당첨확률과는 무관하게 제공되는 기능입니다.</span>
	</div>
	<form name="extract_form" id="extract_numbers" method="post" action="./lotto_extract_numbers_process.php">
    <input type="hidden" name="proc" value="extractNumbers">
	<input type="hidden" name="lc_exclude_numbers" id="exclude_numbers" value="<?=$data_config['lc_exclude_numbers']?>">
	<input type="hidden" name="lc_include_numbers" id="include_numbers" value="<?=$data_config['lc_include_numbers']?>">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tb02">
	<tr>
		
		<th>제외숫자</th>
		<td>
			<?php for($i=1; $i<46; $i++) { ?>
			<div class="exclude-balls l-ball small <?=in_array($i, $exclude_numbers) ? 'checked' : ''?>" id="exclude_ball_<?=$i?>" data-value="<?=$i?>" data-name="exclude" ><?=$i?></div>
			<?php }?>
			<div style="clear:both"></div>
			<div style="float:right">
			<select name="lc_exclude_rate" class="frm_input lotto-ball">
				<option value="100" <?=$data_config['lc_exclude_rate'] == '100' ? 'selected' : ''?>>100%</option>
				<option value="80" <?=$data_config['lc_exclude_rate'] == '80' ? 'selected' : ''?>>80%</option>
				<option value="60" <?=$data_config['lc_exclude_rate'] == '60' ? 'selected' : ''?>>70%</option>
				<option value="40" <?=$data_config['lc_exclude_rate'] == '40' ? 'selected' : ''?>>40%</option>
				<option value="20" <?=$data_config['lc_exclude_rate'] == '20' ? 'selected' : ''?>>20%</option>
				<option value="10" <?=$data_config['lc_exclude_rate'] == '10' ? 'selected' : ''?>>10%</option>
			</select> 확률로 적용
			</div>
		</td>

		<th>지정숫자</th>
		<td>
			<?php for($i=1; $i<46; $i++) { ?>
			<div class="include-balls l-ball small <?=in_array($i, $include_numbers) ? 'checked' : ''?>" id="include_ball_<?=$i?>" data-value="<?=$i?>" data-name="include"><?=$i?></div>
			<?php }?>
			<div style="clear:both"></div>
			<div style="float:right">
			<select name="lc_include_rate" class="frm_input lotto-ball">
				<option value="100" <?=$data_config['lc_include_rate'] == '100' ? 'selected' : ''?>>100%</option>
				<option value="80" <?=$data_config['lc_include_rate'] == '80' ? 'selected' : ''?>>80%</option>
				<option value="60" <?=$data_config['lc_include_rate'] == '60' ? 'selected' : ''?>>70%</option>
				<option value="40" <?=$data_config['lc_include_rate'] == '40' ? 'selected' : ''?>>40%</option>
				<option value="20" <?=$data_config['lc_include_rate'] == '20' ? 'selected' : ''?>>20%</option>
				<option value="10" <?=$data_config['lc_include_rate'] == '10' ? 'selected' : ''?>>10%</option>
			</select> 확률로 적용
			</div>
		</td>
	</tr>
	<tr>
		<th>연번허용</th>
		<td>
			<select name="lc_permit_continue_num" class="frm_input">
			<? for($i=1; $i<5; $i++) { ?>
				<option value="<?=$i?>" <?=$data_config['lc_permit_continue_num'] == $i ? 'selected' : ''?>><?=$i?></option>
			<? } ?>
			</select>개 연속번호 허용
		</td>
		<th>당첨번호 제외</th>
		<td>
			지난 
			<select name="lc_exclude_win_num" class="frm_input">
			<? for($i=1; $i<21; $i++) { ?>
				<option value="<?=$i?>" <?=$data_config['lc_exclude_win_num'] == $i ? 'selected' : ''?>><?=$i?></option>
			<? } ?>
			</select>회 당첨번호 제외
		</td>
	</tr>
	<tr>
		<th width="120px">AC(산술복잡도)설정</th>
		<td>
			<select name="lc_permit_ac_num" class="frm_input">
			<? for($i=1; $i<10; $i++) { ?>
				<option value="<?=$i?>" <?=$data_config['lc_permit_ac_num'] == $i ? 'selected' : ''?>><?=$i?></option>
			<? } ?>
			</select>이상만 허용
			[<label><input type="checkbox" name="lc_ac_num_use" value="1" <?=$data_config['lc_ac_num_use'] == '1' ? 'checked' : ''?>> 사용</label>]
		</td>
		<th width="120px">홀짝비율설정</th>
		<td>
			홀수 
			<select name="lc_odd_rate" id="odd_combo" class="frm_input odd-even" data-oppsit="even_combo">
			<? for($i=0; $i<7; $i++) { ?>
				<option value="<?=$i?>" <?=$data_config['lc_odd_rate'] == $i ? 'selected' : ''?>><?=$i?></option>
			<? } ?>
			</select> / 
			짝수
			<select name="lc_even_rate" id="even_combo" class="frm_input odd-even" data-oppsit="odd_combo">
			<? for($i=0; $i<7; $i++) { ?>
				<option value="<?=$i?>" <?=$data_config['lc_even_rate'] == $i ? 'selected' : ''?>><?=$i?></option>
			<? } ?>
			</select>
			[<label><input type="checkbox" name="lc_uoddEven_use" value="1" <?=$data_config['lc_uoddEven_use'] == '1' ? 'checked' : ''?>> 사용</label>]
		</td>
	</tr>
	<tr>
		<th>생성번호 갯수</th>
		<td colspan="3"><input type="text" name="extract_number_count" class="frm_input" value="5000"> </td>
	</tr>
    </table>
	<div class="btn-box ac">
		<button type="submit" class="as-btn small blue"><i class="fa fa-search"></i> 추출</button>
	</div>
	</form>
	<br /><br />
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
		 <span class="sub_title"><i class="fa fa fa-plus fa-lg fa-lg f_blue"></i> 추출번호목록</span>
	</div>
	
	<div id="pn_anchor" class="search_box ar">
		<form name="searchForm" method="get" action="?#pn_anchor">
			<select name="s_issued" id="s_issued" class="frm_input" onChange="document.searchForm.submit()">
				<option value="">발급전체</option>
				<option value="issued" <?=$_GET['s_issued'] == 'issued' ? 'selected' : ''?>>발급번호</option>
				<option value="not_issued" <?=$_GET['s_issued'] == 'not_issued' ? 'selected' : ''?>>미발급번호</option>
			</select>
			<select name="s_sg_no" id="sido" class="frm_input" onChange="document.searchForm.submit()">
				<option value="">서비스전체</option>
				<? foreach($termService->service_grade as $key => $value) { ?>
				<option value="<?=$key?>" <?=($_GET['s_sg_no'] == $key) ? 'selected' : ''?>><?=$value?> (<?=$serviceCount[$value]?>명)</option>
				<? } ?>
				<option value="normal" <?=($_GET['s_sg_no'] == 'normal') ? 'selected' : ''?>>일반 (<?=$serviceCount['일반']?>명)</option>
			</select>

			<select name="s_result" id="s_result" class="frm_input" onChange="document.searchForm.submit()">
				<option value="">등수전체</option>
				<option value="0" <?=$_GET['s_result'] == '0' ? 'selected' : ''?>>미당첨</option>
				<option value="1" <?=$_GET['s_result'] == '1' ? 'selected' : ''?>>1등당첨</option>
				<option value="2" <?=$_GET['s_result'] == '2' ? 'selected' : ''?>>2등당첨</option>
				<option value="3" <?=$_GET['s_result'] == '3' ? 'selected' : ''?>>3등당첨</option>
				<option value="4" <?=$_GET['s_result'] == '4' ? 'selected' : ''?>>4등당첨</option>
				<option value="5" <?=$_GET['s_result'] == '5' ? 'selected' : ''?>>5등당첨</option>
			</select>


			<select name="s_inning" id="s_inning" class="frm_input" onChange="document.searchForm.submit()">
				<option value="">회차선택</option>
				<!-- <option value="<?=$inning_arr[0]['inning']+1?>" <?=($_GET['s_inning'] == $inning_arr[0]['inning']+1) ? 'selected' : ''?>><?=$inning_arr[0]['inning']+1?>회 추출번호</option> -->
			<? for($i=0; $i<count($inning_arr); $i++ ) { ?>
				<option value="<?=$inning_arr[$i]['inning']?>" <?=($_GET['s_inning'] == $inning_arr[$i]['inning']) ? 'selected' : ''?>><?=$inning_arr[$i]['inning']?>회 추출번호</option>
			<? } ?>
			</select>
			<button type="button" class="as-btn medium white" id="next"><i class="fa fa-chevron-left"></i> 이전</button>
			<button type="button" class="as-btn medium white" id="prev" >다음 <i class="fa fa-chevron-right"></i></button><br />
			<select name="sc" class="frm_input">
                <option value="mb_name" <?=($_GET['sc']=='mb_name') ? 'selected' : '';?>>이름(가입자명)</option>
				<option value="mb_id" <?=($_GET['sc']=='mb_id') ? 'selected' : '';?>>회원아이디</option>
				<option value="mb_hp" <?=($_GET['sc']=='mb_hp') ? 'selected' : '';?>>휴대전화</option>
            </select>
            <input type="text" class="frm_input" style="width:130px" name="sv" value="<?=$_GET['sv']?>">
			
			<button type="submit" class="as-btn medium blue"><i class="fa fa-search"></i> 검색</button>
		</form>
	</div>
	<div class="list-count">총 <?=number_format($data['total_count'])?>개</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
		<th width="40px">번호</th>
		<th>회차</th>
		<th>추출일자 / 최종접속</th>
		<th>추출 번호</th>
		<th>추출 번호</th>
		<th>A/C</th>
		<th>번호합</th>
		<th>구분</th>
		<th>당첨결과</th>
		<th>서비스</th>
		<th>SMS발송</th>
		<th>발급회원</th>
		<th width="240px">변경</th>
	</tr>
	<?
	foreach($data['list'] as $row) {
		$row_service = $termService->getMemberServiceUse($row['mb_id']);
	?>
	<form name="numbers_form_<?=$row['le_no']?>" id="numbers_form_<?=$row['le_no']?>" method="post" action="lotto_extract_numbers_process.php">
    	<input type="hidden" name="proc" value="updateNumbers">
    	<input type="hidden" name="le_no" value="<?=$row['le_no']?>">
		<input type="hidden" name="return_url" value="<?=$return_url?>">
	<tr>
		<td><?=$data['idx']--?></td>
		<td><?=$row['le_inning']?>회</td>
		<td>
			<?=$row['le_datetime']?> / <?=$row['mb_today_login']?>
		</td>
		<td>
			<img src="../images/balls/ball_<?=$row['le_num1']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['le_num2']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['le_num3']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['le_num4']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['le_num5']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['le_num6']?>.png" width="30px">
		</td>
		<td>
			<input type="text" class="frm_input" name="numbers[]" style="width:30px;text-align:center" value="<?=$row['le_num1']?>">
			<input type="text" class="frm_input" name="numbers[]" style="width:30px;text-align:center" value="<?=$row['le_num2']?>">
			<input type="text" class="frm_input" name="numbers[]" style="width:30px;text-align:center" value="<?=$row['le_num3']?>">
			<input type="text" class="frm_input" name="numbers[]" style="width:30px;text-align:center" value="<?=$row['le_num4']?>">
			<input type="text" class="frm_input" name="numbers[]" style="width:30px;text-align:center" value="<?=$row['le_num5']?>">
			<input type="text" class="frm_input" name="numbers[]" style="width:30px;text-align:center" value="<?=$row['le_num6']?>">
		</td>
		<td>
    		<?=$lotto->acFilter(array($row['le_num1'],$row['le_num2'],$row['le_num3'],$row['le_num4'],$row['le_num5'],$row['le_num6']))?>
		</td>
		<td>
    		<?=$lotto->sumFilter(array($row['le_num1'],$row['le_num2'],$row['le_num3'],$row['le_num4'],$row['le_num5'],$row['le_num6']))?>
		</td>
		<td style="text-align:center"><?=$lottoService->getNumberType()[$row['le_type']]?></td>
		<td><?=($row['le_result_grade']) ? $row['le_result_grade'] : '--'?></td>
		<td><?=($row['sg_no']) ? $termServiceGrade[$row['sg_no']] : "무료"?></td>
		<td><?=($row['le_send_sms'] == '1') ? '<span class="blue">발송</span>' : '<span class="red">미발송</span>'?></td>
		<td>
			<? if($row['mb_id']) { ?>
				<?=$row['mb_name']?>(<?=$row['mb_id']?>)
				<? if($row['varified'] == '1' || $row['le_send_sms'] == '1') { ?>
					<button type="button" class="as-btn tiny blue" title="확인"><i class="fa fa-check"></i></button>
				<? } else { ?>
					<button type="button" class="as-btn tiny red" title="미확인"><i class="fa fa-minus"></i></button>
				<? } ?>
				
			<? } else { ?>
				미발급
			<? } ?>
		</td>
		<td>
			<!-- <span class="button medium strong"><a href="javascript:memoWin('<?=$row['su_no']?>');void(0);">(<? //$auction->getMemoNum('su_no', $row['su_no'])?>) 메모</a></span> -->
			<? if($row['mb_id']) { ?>
				<button type="button" class="as-btn small green" onClick="manageService('<?=$row['mb_id']?>');"><i class="fa fa-wrench"></i> 관리</button>
			<? } else { ?>
			
			<? } ?>
			<button type="button" class="as-btn small green" onClick="modifyData('<?=$row['le_no']?>');"><i class="fa fa-refresh"></i> 수정</button>
			<button type="button" class="as-btn small red" onclick="deleteData('<?=$row['le_no']?>')"><i class="fa fa-close"></i> 삭제</button>
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
<form name="deleteForm" method="post" action="./lotto_extract_numbers_process.php?<?=$param?>">
<input type="hidden" name="proc" value="deleteNumbers">
<input type="hidden" name="le_no" value="">
<input type="hidden" name="return_url" value="<?=$list_url?>">
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

function manageService(id) {
	window.open("./lotto_member_management.php?mb_id="+id, '', 'width=1200, height=800, scrollbars=yes');
}
function memoWin(no) {
	window.open("./ia_request_memo.php?type=su_no&idx="+no, '', 'width=550, height=600');
}



function addData() {
    var f = $('#service_add_form');
    $(f).submit();
    
}

function modifyData(no) {
    
    var f = $('#numbers_form_'+no);
    $(f).submit();
    
}

function deleteData(no) {

	var f = document.deleteForm;
	if(confirm("데이터를 삭제합니다.\n삭제하신데이터는 복구가 불가능합니다.\n계속하시겠습니까?")) {
		f.le_no.value = no;
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