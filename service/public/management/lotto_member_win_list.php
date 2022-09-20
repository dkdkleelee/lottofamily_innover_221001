<?
define('G5_IS_ADMIN', true);
define('NO_CACHE', true);
$sub_menu = "400100";
include_once("./_common.php");

use Acesoft\Core\DB;
use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\TermService;

$param1 = Utils::getParameters(array('page'));
$list_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$param1;

// lotto service config
$lottoServiceConfig = new LottoServiceConfig();
$data_config = $lottoServiceConfig->getConfig();

// lotto service
$lottoService = new LottoService();

//▶ 추출번호 inning data
$inning_arr = $lottoService->getExtractNumberInningGroups();
$_GET['s_inning'] = $_GET['s_inning'] ? $_GET['s_inning']  : $inning_arr[0]['inning'];;


$_GET['s_mb_id'] = $_GET['id'];
$_GET['type'] = 'extractor';	// 추출기 추출번호만(사용자 등록번호 제외)
$lottoService = new LottoService();
$data = $lottoService->getIssuedNumbersInfotList($_GET['page'], $list_url);

// 당첨결과
$win_data = $lottoService->getWinData($_GET['s_inning']);
$win_array = array($win_data['lw_num1'], $win_data['lw_num2'], $win_data['lw_num3'], $win_data['lw_num4'], $win_data['lw_num5'], $win_data['lw_num6']);

// 누적당첨금액
$lottoService = new LottoService();
$accumulated_result = $lottoService->getAccumulatedWinRecords($_GET['s_mb_id'], $_GET['s_inning']);

// 최근 SMS목록
$_GET['mb_id'] = $_GET['id'];
$data_sms = $lottoService->getSmsHistoryList($_GET['page']);


$mb = get_member($_GET['id']);

$title = "당첨내역 조회";
include_once(G5_PATH.'/head.sub.php');
?>
<link rel="stylesheet" href="../css/lotto.css" type="text/css">
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
	<div>
		<ul id="tab">
			<li><a href="./lotto_member_win_list.php?<?=$param1?>" class="tab_on">당첨내역 조회</a></li>
			<li><a href="./lotto_member_extract_management.php?<?=$param1?>" class="">번호발급 조회/관리</a></li>
		</ul>
	</div>
	<div style="clear:both"></div>
	<div>
		 <span class="sub_title"><i class="fa fa-plus fa-lg"></i> 회원기본정보</span>
	</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
    
	<tr>
		
		<th style="width:20%">아이디</th>
		<td style="width:30%"><?=$mb['mb_id']?></td>
		<th style="width:20%">이름</th>
		<td style="width:30%"><?=$mb['mb_name']?></td>
	</tr>
	<tr>
		
		<th>휴대전화번호</th>
		<td><?=$mb['mb_hp']?></td>
		<th>가입일</th>
		<td><?=$mb['mb_datetime']?></td>
	</tr>
	</table>

	
	<br />
	<div>
		 <h5 class="title"><i class="fa fa-plus fa-lg"></i> <?=$_GET['s_inning']?>회차 당첨내역</h5>
	</div>
	<div id="s_anchor" class="search_box ar">
		<form name="searchForm" method="get" action="?">
			<input type="hidden" name="id" id="id" value="<?=$_GET['id']?>">
			<input type="hidden" name="s_service_use" id="s_service_use" value="<?=$_GET['s_service_use']?>">
			<input type="hidden" name="sc" id="sc" value="<?=$_GET['sc']?>">
			<input type="hidden" name="sv" id="sv" value="<?=$_GET['sv']?>">
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
	<br />
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tb02">
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
		<th><?=$row['le_inning']?>회</th>
		<td style="text-align:center">
			<img src="/images/ball_<?=sprintf('%02d', $row['le_num1'])?>.png" width="30px" style="<?=!in_array($row['le_num1'], $win_array) ? 'filter: opacity(0.2)' : ''?>">
			<img src="/images/ball_<?=sprintf('%02d', $row['le_num2'])?>.png" width="30px" style="<?=!in_array($row['le_num2'], $win_array) ? 'filter: opacity(0.2)' : ''?>">
			<img src="/images/ball_<?=sprintf('%02d', $row['le_num3'])?>.png" width="30px" style="<?=!in_array($row['le_num3'], $win_array) ? 'filter: opacity(0.2)' : ''?>">
			<img src="/images/ball_<?=sprintf('%02d', $row['le_num4'])?>.png" width="30px" style="<?=!in_array($row['le_num4'], $win_array) ? 'filter: opacity(0.2)' : ''?>">
			<img src="/images/ball_<?=sprintf('%02d', $row['le_num5'])?>.png" width="30px" style="<?=!in_array($row['le_num5'], $win_array) ? 'filter: opacity(0.2)' : ''?>">
			<img src="/images/ball_<?=sprintf('%02d', $row['le_num6'])?>.png" width="30px" style="<?=!in_array($row['le_num6'], $win_array) ? 'filter: opacity(0.2)' : ''?>">
			<? if($row['le_result_grade'] == '2') { ?>
			+ <img src="/images/ball_<?=sprintf('%02d', $win_data['lw_num7'])?>.png" width="30px">
			<? } ?>
		</td>
		<td style="text-align:center"><?=$lottoService->getNumberType()[$row['le_type']]?></td>
		<td style="text-align:center"><?=number_format($row[$array_prize_idx[$row['le_result_grade']]])?>원</td>
		<td style="text-align:center"><?=($row['le_result_grade']) ? $row['le_result_grade']."등" : '--'?></td>
	</tr>
	<? } ?>
	</table>

	<div class="paginate wrapper paging_box">
			<?=$data['link']?>	
	</div>

	<br />
	<div>
		 <span class="sub_title"><i class="fa fa-plus fa-lg"></i> SMS발송</span>
	</div>
	<form name="send_sms_form" method="post" action="./lotto_member_process.php">
	<input type="hidden" name="proc" value="sendSMS">
	<input type="hidden" name="id" value="<?=$_GET['id']?>">
	<input type="hidden" name="name" value="<?=$mb['mb_name']?>">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
    
	<tr>
		<th style="width:20%">휴대전화</th>
		<td style="width:80%;text-align:left;padding:5px"><input type="text" name="hp" class="frm_input" value="<?=$mb['mb_hp']?>"></td>
	</tr>
	<tr>
		<th>내용</th>
		<td style="text-align:left;padding:5px"><textarea name="content" id="msg_content" style="width:95%;height:100px" onKeyUp="checkSMSLen(this)"></textarea><br />
			<span id="byteInfo">0</span>byte</td>
	</tr>
	</table>
	<div class="btn_box ac">
		<button type="button" class="as-btn small green" onClick="sendSMS();"><i class="fa fa-send"></i> 보내기</button>
	</div>
	</form>
	<br />
	<div>
		 <span class="sub_title"><i class="fa fa-plus fa-lg"></i> 최근 SMS발송현황</span>
	</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
    
	<tr>
		<th width="120px">전송일시</th>
		<th width="">내용</th>
		<th width="50px">결과</th>
		<th width="80px">재발송</th>
	</tr>
	<?
		foreach((array)$data_sms['list'] as $row) {
			
	?>

		<tr>
        <td>
            <?php echo date('Y-m-d H:i', strtotime($row['hs_datetime']))?>
        </td>
		<td style="text-align:left;padding:3px;">
    		<span title="<?php echo $row['wr_message']?>" style="vertical-align:top;" id="msg_<?=$row['wr_no'
			]?>"><?php echo $row['wr_message']?></span>
		</td>
		<td class="td_boolean" title="<?php echo $row['hs_memo']?>"><?php echo $row['hs_flag']?'<span style="color:#9933ff">성공</span>':'<span style="color:#ff3333">실패</span>'?></td>
		<td class="td_boolean" title="<?php echo $row['hs_memo']?>"><button type="button" class="as-btn small blue" onclick="reSendNumbers(<?=$row['wr_no'
			]?>)">재발송</button></td>
	</tr>
	</form>
	<? }?>
	
	</table>
	<div class="paging_box ac">
			<?=$page_link?>
	</div>

</div>
<form name="deleteForm" method="post" action="./term_service_config_process.php?<?=$param?>">
<input type="hidden" name="proc" value="deleteTermServiceConfig">
<input type="hidden" name="no" value="">
</form>
<script type="text/javascript">
<!--

function reSendNumbers(wr_no) {
	var msg = $("#msg_"+wr_no).text();
	$('#msg_content').val(msg);
	$('#msg_content').trigger('keyup');
}

function checkSMSLen(obj){
	var str = obj.value;
	var str_len = str.length;

	var rbyte = 0;
	var rlen = 0;
	var one_char = "";
	var str2 = "";

	var midByte = 80; //sms
	var maxByte = 1500; // lms

	for(var i=0; i<str_len; i++){
		one_char = str.charAt(i);
		if(escape(one_char).length > 4){
			rbyte += 2;                                         //한글2Byte
		}else{
			rbyte++;                                            //영문 등 나머지 1Byte
		}

		if(rbyte <= maxByte){
			rlen = i+1;                                          //return할 문자열 갯수
		}
	}

	if(rbyte <= midByte) {
		$('#byteInfo').text(rbyte+"byte (SMS)");
	} else if(rbyte <= maxByte) {
		$('#byteInfo').text(rbyte+"byte (LMS)");
	} else {
		alert("한글 "+(maxByte/2)+"자 / 영문 "+maxByte+"자를 초과 입력할 수 없습니다.");
		str2 = str.substr(0,rlen);                                  //문자열 자르기
		obj.value = str2;
	}
/*
	if(rbyte > maxByte){
		alert("한글 "+(maxByte/2)+"자 / 영문 "+maxByte+"자를 초과 입력할 수 없습니다.");
		str2 = str.substr(0,rlen);                                  //문자열 자르기
		obj.value = str2;
		fnChkByte(obj, maxByte);
	}else{
		$('#byteInfo').text(rbyte);
	}
	*/
}


function sendSMS(no) {

	var f = document.send_sms_form;

	if(!f.hp.value) {
		alert("휴대전화번호를 입력해 주세요.");
		f.hp.focus();
		return false;
	}

	if(!f.content.value) {
		alert("메세지를 입력해 주세요.");
		f.content.focus();
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



//-->
</script>