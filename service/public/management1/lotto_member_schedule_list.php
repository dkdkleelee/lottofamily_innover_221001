<?php
define('G5_IS_MANAGER', true);
$sub_menu = "100200";

include_once("./_common.php");

use \Acesoft\Common\Utils as Utils;
use \Acesoft\LottoApp\Member\User as User;

//▶ pagination url
$param1 = Utils::getParameters(array('page','s_ca_no'))."&s_ca_no[]=".@array_pop(array_filter($_GET['s_ca_no']));
$list_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$param1;


// term service
$user = new User();

$_GET['s_date'] = $_GET['s_date'] ? $_GET['s_date'] : date('Y-m-d');

//▶ get list data
$data = $user->getAlertMemoList($_SESSION['ss_mb_id'], $_GET['page'], $list_url);


// 캘린더
$workdays = array();
$type = CAL_GREGORIAN;
$month = $_GET['m'] ? (int)$_GET['m'] : date('n'); // Month ID, 1 through to 12.
$year = $_GET['y'] ? $_GET['y'] : date('Y'); // Year in 4 digit 2009 format.
$day_count = date('t', strtotime($year.'-'.$month.'-01')); // Get the amount of days

//loop through all days
$first_day = '';
for ($i = 1; $i <= $day_count; $i++) {

	$date = $year.'-'.sprintf('%02d', $month).'-'.sprintf('%02d', $i); //format date
	$get_name = date('l', strtotime($date)); //get week day
	$day_name = substr($get_name, 0, 3); // Trim day name to 3 chars

	//if not a weekend add day to array
//	if($day_name != 'Sun' && $day_name != 'Sat'){
		$workdays[$date] = $i;
		$first_day = $first_day ? $first_day : $i;
//	}
}


// pre month ending dates
$cur_time = strtotime($year.'-'.$month.'-'.$first_day); //format date
$pre_time = strtotime("-1 month", $cur_time); //get previous month time

if(date('w', $cur_time) > 0 ) {

	$day_count = date('t', mktime(0, 0, 0, date('m', $pre_time), 1, date('Y', $pre_time)));

	$pre_day_count = date('t', $pre_time); // Get the amount of days
	$first_column = date('w', strtotime($year."-".$month."-1"))-1; // get cur month first day's week number

	for($i=$first_column; $i >= 0; $i--) {
		$tmp_time = strtotime(date('Y', $pre_time).'-'.date('m', $pre_time).'-'.($pre_day_count - $i));
		//$predays[date('Y', $pre_time).'-'.date('m', $pre_time).'-'.($pre_day_count - $i)] = $pre_day_count - $i;
		$predays[] = '';
	}

	if(is_array($predays)) {
		$workdays = array_merge($predays, $workdays);
	}
}
/* 데이터 배열생성*/

$data_undone = $user->getAlertMemos($_SESSION['ss_mb_id'], 0, $year, $month);
$data_done = $user->getAlertMemos($_SESSION['ss_mb_id'], 1, $year, $month);

for($i=0; $i<count($data_undone); $i++) {

	$staus[$data_undone[$i]['d']]['undone']++;
	$status_list[$data_undone[$i]['d']]['undone'][] = array(
																'memo' => $data_undone[$i]['mo_memo'],
																'mb_id' => $data_undone[$i]['mb_id'],
																'mb_name' => $data_undone[$i]['mb_id'],
																'mo_datetime' => $data_undone[$i]['mo_datetime']
														);

}


for($i=0; $i<count($data_done); $i++) {
	$staus[$data_done[$i]['d']]['done']++;
	$status_list[$data_done[$i]['d']]['done'][] = array(
																'memo' => $data_done[$i]['mo_memo'],
																'mb_id' => $data_done[$i]['mb_id'],
																'mb_name' => $data_undone[$i]['mb_id'],
																'mo_datetime' => $data_undone[$i]['mo_datetime']
														);
}


$count = count($workdays)%7 > 0 ? count($workdays)+(7-count($workdays)%7) : count($workdays);

$cur_time = strtotime($year."-".$month."-01");
$pre_time = strtotime("-1 month", $cur_time);
$next_time = strtotime("+1 month", $cur_time);


//$member_arr = $termService->db->arrayBuilder()->get($termService->tb['Member'], null, '*');

$g5['title'] = '<i class="fa fa-bell"></i>  알림목록 관리';
include_once (G5_MANAGER1_PATH.'/admin.head.php');
?>
<link rel="stylesheet" href="../css/custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/admin.custom.style.css" type="text/css">
<!-- <link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css" rel="stylesheet" /> -->
<script src="../js/jquery-ui-1.8.7.custom.min.js" ></script>
<script src="../js/jquery.ui.datepicker-ko.js"  charset="utf-8"></script>
<link rel="stylesheet" href="../js/datetimepicker/jquery.datetimepicker.css" type="text/css" />
<script type="text/javascript" src="../js/datetimepicker/jquery.datetimepicker.js" charset="utf-8"></script>

<!-- <div class="btitle">
	<div class="btitle_top"></div>
	<div class="btitle_text">알림리스트</div>
	<div class="btitle_locate">&gt; TM &gt; 알림리스트</div>
	<div class="btitle_line"></div>
</div> -->
<div class="content_wrap">
    <!-- <div>
		 <span class="sub_title"><i class="fa fa fa-plus fa-lg fa-lg f_blue"></i> 스캐쥴관리</span>
	</div> -->
	<div>
		 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 각 날짜에 지정된 숫자는 (처리완료/처리대기)</span>
	</div>
    <div class="calendar-date" id="cal">
			<a href="<?=$_SERVER['PHP_SELF']?>?y=<?=date('Y', $pre_time)?>&m=<?=date('m', $pre_time)?>#cal"><img src="../images/btn_cal_pre.gif" alt="이전달"></a>
			<span class="calendar-year-month"><?=$year."년".$month."월"?></span>
			<a href="<?=$_SERVER['PHP_SELF']?>?y=<?=date('Y', $next_time)?>&m=<?=date('m', $next_time)?>#cal"><img src="../images/btn_cal_next.gif" alt="다음달"></a>
		</div>
		<table cellpadding="0" cellspacing="0" class="calendar-table" width="100%">
		<tr>
			<th>일</th>
			<th>월</th>
			<th>화</th>
			<th>수</th>
			<th>목</th>
			<th>금</th>
			<th>토</th>
		</tr>
		<tr>
		<?
		$i = 0;
		foreach($workdays as $date => $value) {
			
			$g_data = $data[$date]; 
			if ($i > 0 && ($i%7 == 0)) echo '</tr><tr>';

			$profit_tot = 0;

			if($staus[$date]['done'] || $staus[$date]['undone']) {
				$day_status = "[<span style='color:#33cc33'>".number_format($staus[$date]['done'])."</span> / <span style='color:#ff3366'>".number_format($staus[$date]['undone'])."</span>]";
			} else {
				$day_status = "";
			}
		?>
			<td>
				<div class='day-title'><?=$date == date('Y-m-d') ? '<span style="display:inline-block;height: 18px;width:18px;line-height: 130%;background:#76ac41;border-radius:30px;padding:2px;color:#fff;text-align:center">'.$value.'</span>' : $value?> <?=$day_status?></div>
					<ul class="calendar-memo">
				<?
					
						for($l=0; $l<count($status_list[$date]['undone']); $l++) { 
						
				?>
						<li <?=($status_list[$date]['undone'][$l]['memo']) ? 'data-tooltip="<span style=\'font-weight:bold\'>['.$status_list[$date]['undone'][$l]['mb_name'].' / '.$status_list[$date]['undone'][$l]['mo_datetime']."]</span><br />".nl2br($status_list[$date]['undone'][$l]['memo']).'"' : ''?>><a href="javascript:showManageWin('<?=$status_list[$date]['undone'][$l]['mb_id']?>');void(0);"><span class="calendar-memo-item undone"><?=$status_list[$date]['undone'][$l]['memo']?></span></a></li>
				<?
						}
					
				?>

				<?
					
						for($l=0; $l<count($status_list[$date]['done']); $l++) { 
						
				?>
						<li <?=($status_list[$date]['done'][$l]['memo']) ? 'data-tooltip="<span style=\'font-weight:bold\'>['.$status_list[$date]['done'][$l]['mb_name'].' / '.$status_list[$date]['done'][$l]['mo_datetime']."]</span><br />".nl2br($status_list[$date]['done'][$l]['memo']).'"' : ''?>><a href="javascript:showManageWin('<?=$status_list[$date]['done'][$l]['mb_id']?>');void(0)"><span class="calendar-memo-item done"><?=$status_list[$date]['done'][$l]['memo']?></span></a></li>
				<?
						}
					
				?>
					</ul>
				<div style="clear:both"></div>
				<div class="profit-tot"></div>
				
			</td>
		<?
			$i++;
		}
		$empty_count = count($workdays)%7 > 0 ? 7-count($workdays)%7 : 0;
		for($i=0; $i < $empty_count; $i++) {
		?>
			<td></td>
		<?
		}
		?>
		</tr>
		</table>
	</div>
	<br /><br />
	<div>
		 <span class="sub_title"><i class="fa fa fa-plus fa-lg fa-lg f_blue"></i> 알림리스트</span>
	</div>
	
	<div class="search_box ar">
		<form name="searchForm" method="get" action="?">
				<input type="hidden" name="sm" value="<?=$_GET['sm']?>">
				<input type="text" name="s_date" class="frm_input datepicker" value="<?=$_GET['s_date']?>">일부터
				<select name="s_status" class="frm_input">
					<option value="" <?=($_GET['s_status']=='') ? 'selected' : '';?>> 상태전체</option>
					<option value="N" <?=($_GET['s_status']=='N') ? 'selected' : '';?>> 처리이전</option>
					<option value="Y" <?=($_GET['s_status']=='Y') ? 'selected' : '';?>> 처리완료</option>
				</select>
				<select name="s_mb_cousult_status" class="frm_input">
					<option value="">상담상태조회</option>
					<? foreach($user->getConsultStatus() as $key => $value) { ?>
					<option value="<?=$key?>" <?=$_GET['s_mb_cousult_status'] == $key ? 'selected' : ''?>><?=$value?></option>
					<? } ?>
				</select>
				<select name="sc" class="frm_input">
					<option value="b.mb_name" <?=($_GET['sc']=='b.mb_name') ? 'selected' : '';?>>이름</option>
					<option value="a.mb_id" <?=($_GET['sc']=='a.mb_id') ? 'selected' : '';?>> 아이디</option>
					<option value="mo_mb_id" <?=($_GET['sc']=='mo_mb_id') ? 'selected' : '';?>> TM아이디</option>
				</select>
				<input type="text" name="sv" class="frm_input" value="<?=$_GET['sv']?>">
				<button type="submit" class="as-btn medium white"><i class="fa fa-search"></i> 검색</button>
		</form>
	</div>
	<div>
		<span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 알림으로 등록된 메모리스트입니다.</span>
		<span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 미처리 알림목록은 최고 30분 전 알림까지 해당 회원의 관리창 자동 팝업이 실행됩니다.</span>
	</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
		<th style="width:40px">번호</th>
		<th style="width:100px">이름(아이디)</th>
		<th style="width:100px">등록TM</th>
		<th style="width:100px">휴대폰</th>
		<th>메모</th>
		<th style="width:150px">알림일시</th>
		<th style="width:150px">확인일시</th>
		<th style="width:100px">상담상태</th>
		<th style="width:70px">확인</th>
	</tr>
	<?

	
	
	foreach($data['list'] as $row) {

	?>
	<form name="schedule_form_<?=$row['mo_no']?>" id="schedule_form_<?=$row['mo_no']?>" method="post" action="lotto_member_process.php">
    	<input type="hidden" name="proc" value="updateMemoDone">
    	<input type="hidden" name="mo_no" value="<?=$row['mo_no']?>">
	<tr class="<?=($row['mb_cousult_status'] == '예약') ? 'rev' : ''?>">
		<td><?=$data['idx']--?></td>
		<td><?=$row['mb_name']?>(<?=$row['mb_id']?>)</td>
		<td><?=$row['mo_mb_id']?></td>
		<td><?=$row['mb_hp']?></td>
		<td style="text-align:left;padding-left:10px"><?=$row['mo_memo']?></td>
		<td>
    		<?=$row['mo_schedule_datetime']?>
		</td>
        <td>
    		<?=$row['mo_schedule_done_datetime']?>
		</td>
		<td>
		   <?=$row['mb_cousult_status']?>
		</td>
		<td>
			<? if($row['mo_schedule_done_datetime']) { ?>
				<div style="display:inline-block;height: 15px;width:15px;line-height: 110%;background:#76ac41;border-radius:30px;padding:2px;color:#fff"><i class="fa fa-check"></i></div>
			<? } else { ?>
			<button type="button" class="as-btn small green" onClick="confirm_memo('<?=$row['mo_no']?>');"><i class="fa fa-check"></i> 확인</button>
			<? } ?>
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
<form name="deleteForm" method="post" action="./term_service_use_process.php?<?=$param?>">
<input type="hidden" name="proc" value="deleteServiceUse">
<input type="hidden" name="no" value="">
</form>
<script type="text/javascript">
<!--
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

	$('.datepicker').datetimepicker({
					format:'Y-m-d',
					timepicker: false,
					changeMonth: true,
					changeYear: true,
					showButtonPanel: true,
					showMonthAfterYear: true,
					yearRange: 'c-0:c+10',
					
					lang: 'kr',
					onChange: function(date) {
						
					}
				});

	$('[data-tooltip]').hover(function(){
		$('<div class="div-tooltip"></div>').html($(this).attr('data-tooltip')).appendTo('body').fadeIn('fast');
	}, function() { 
		$('.div-tooltip').remove();
	}).mousemove(function(e) {
		$('.div-tooltip').css({ top: e.pageY + 10, left:  e.pageX + 20 })
	});

});



function confirm_memo(no) {

	var f = $('#schedule_form_'+no);
	if(confirm("알림 상태를 처리완료로 변경합니다.\n계속하시겠습니까?")) {

		$.ajax({
			url: "./lotto_member_process.php",
			type: "post",
			data: "proc=updateMemoDone&mo_no="+no,
			cache: false,
			dataType: 'text',
			success: function(data) {
				if(data) {
					window.open('./lotto_member_management.php?mb_id='+data, '', 'width=1200,height=800, scrollbars=yes');
					window.location.reload();
				} else {
					alert("데이터가 없습니다.");
				}
			}
		});

		//f.submit();
	}

}

function showManageWin(id) {
	window.open('./lotto_member_management.php?mb_id='+id, id, 'width=1200,height=800, scrollbars=yes');
}
//-->
</script>
<?php
include_once(G5_MANAGER1_PATH.'/admin.tail.php');
?>
