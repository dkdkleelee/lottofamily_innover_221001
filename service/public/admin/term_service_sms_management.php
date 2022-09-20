<?
define('G5_IS_ADMIN', true);
define('NO_CACHE', true);
$sub_menu = "400100";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'r');

use Acesoft\Core\DB;
use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\TermService;

$param1 = Utils::getParameters(array('page'));
$list_url = "http://".$_SERVER[HTTP_HOST].$_SERVER[PHP_SELF]."?".$param1;

// lotto service config
$lottoServiceConfig = new LottoServiceConfig();
$data_config = $lottoServiceConfig->getConfig();

// lotto service
$lottoService = new LottoService();

//▶ 추출번호 inning data
$inning_arr = $lottoService->getExtractNumberInningGroups();

$_GET['s_inning'] = $_GET['s_inning'] ? $_GET['s_inning']  : $inning_arr[0]['inning'];


// 최근 SMS목록
$_GET['mb_id'] = $_GET['id'];
$data_sms = $lottoService->getSmsHistoryList($_GET['page']);


$mb = get_member($_GET['id']);

$title = "회원 서비스 관리";
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
			<li><a href="./term_service_management.php?<?=$param1?>">서비스 기본정보</a></li>
			<li><a href="./term_service_sms_management.php?<?=$param1?>" class="tab_on">SMS전송</a></li>
			<!-- <li><a href="javascript:m6s2s3()"></a></li> -->
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
<?

	
	
	$lottoService->db->where("le_inning", $_GET['s_inning']);
	$lottoService->db->where("mb_id", $_GET['id']);
	$extracted_number_list = $lottoService->db->arraybuilder()->get($lottoService->tb['LottoNumbers'], null, "*");
	$idx = count($extracted_number_list);
?>
	<div>
		 <span class="sub_title"><i class="fa fa-plus fa-lg"></i> <?=$_GET['s_inning']?>회차 발급번호</span>
	</div>
	<div class="search_box ar">
		<form name="searchForm" method="get" action="?">
			<input type="hidden" name="id" id="id" value="<?=$_GET['id']?>">
			<input type="hidden" name="s_service_use" id="s_service_use" value="<?=$_GET['s_service_use']?>">
			<input type="hidden" name="sc" id="sc" value="<?=$_GET['sc']?>">
			<input type="hidden" name="sv" id="sv" value="<?=$_GET['sv']?>">
			<select name="s_inning" id="s_inning" class="frm_input" onChange="document.searchForm.submit()">
				<option value="">회차선택</option>
				<!-- <option value="<?=$inning_arr[0]['inning']+1?>" <?=($_GET['s_inning'] == $inning_arr[0]['inning']+1) ? 'selected' : ''?>><?=$inning_arr[0]['inning']+1?>회 추출번호</option> -->
			<? for($i=0; $i<count($inning_arr); $i++ ) { ?>
				<option value="<?=$inning_arr[$i]['inning']?>" <?=($_GET['s_inning'] == $inning_arr[$i]['inning']) ? 'selected' : ''?>><?=$inning_arr[$i]['inning']?>회</option>
			<? } ?>
			</select>
		</form>
	</div>
	<form name="addNumberForm" method="post" action="./lotto_extract_numbers_process.php">
	<input type="hidden" name="proc" value="addNumber">
	<input type="hidden" name="selected_numbers" id="selected_numbers">
	<input type="hidden" name="mb_id" value="<?=$_GET['id']?>">
	<input type="hidden" name="return_url" value="<?=$list_url?>">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tb02">
	<tr>
		<th style="width:10%">추가발급</th>
		<td>
			<?php for($i=1; $i<46; $i++) { ?>
			<div class="selected-balls l-ball small <?=in_array($i, (array)$exclude_numbers) ? 'checked' : ''?>" id="selected_ball_<?=$i?>" data-value="<?=$i?>" data-name="selected" ><?=$i?></div>
			<?php }?>
		</td>
		<th style="width:10%"><button type="button" class="as-btn small green" onClick="addNumber();"><i class="fa fa-send"></i> 선택번호발급</button></th>
	</tr>
	</table>
	</form>
	
	<br />
	<form name="addSelectedNumbersToQueueForm" method="post" action="./lotto_extract_numbers_process.php">
	<input type="hidden" name="proc" value="addSelectedNumbersToQueue">
	<input type="hidden" name="mb_id" value="<?=$_GET['id']?>">
	<input type="hidden" name="return_url" value="<?=$list_url?>">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
		<th style="width:80px">선택</th>
		<th style="width:100px">번호</th>
		<th style="width:10%">회차</th>
		<th style="width:30%">번호</th>
		<th style="width:10%">구분</th>
		<th style="width:15%">생성일시</th>
		<th style="width:15%">발급일시</th>
		<th style="width:10%">추첨결과</th>
	</tr>
	<? foreach($extracted_number_list as $row) { ?>
	<tr>
		<th><?if($row['le_send_sms'] == '0') { ?><input type="checkbox" name="ids[]" value="<?=$row['le_no']?>"><? } ?></th>
		<th><?=$idx--?></th>
		<th><?=$row['le_inning']?>회</th>
		<td>
			<img src="../images/balls/ball_<?=$row['le_num1']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['le_num2']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['le_num3']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['le_num4']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['le_num5']?>.png" width="30px">
			<img src="../images/balls/ball_<?=$row['le_num6']?>.png" width="30px">
		</td>
		<td><?=$lottoService->getNumberType()[$row['le_type']]?></td>
		<td><?=$row['le_datetime']?></td>
		<td><?=$row['le_issued_date']?></td>
		<td><?=($row['le_result_grade']) ? $row['le_result_grade'] : '--'?></td>
	</tr>
	<? } ?>
	</table>
	<div class="btn_box ar">
		<button type="button" class="as-btn small green" onClick="addSelectedNumbersToQueue();"><i class="fa fa-send"></i> 선택번호SMS발송</button>
	</div>
	</form>
	<br />
	<div>
		 <span class="sub_title"><i class="fa fa-plus fa-lg"></i> SMS발송</span>
	</div>
	<form name="send_sms_form" method="post" action="./term_service_use_process.php">
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
		<td style="text-align:left;padding:5px"><textarea name="content" style="width:95%;height:100px" onKeyUp="checkSMSLen(this)"></textarea><br />
			<span id="byteInfo">0</span>byte</td>
	</tr>
	</table>
	<div class="btn_box ac">
		<button type="submit" class="as-btn small green" onClick="sendSMS();"><i class="fa fa-send"></i> 보내기</button>
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
	</tr>
	<?
		foreach((array)$data_sms['list'] as $row) {
			
	?>

		<tr>
        <td>
            <?php echo date('Y-m-d H:i', strtotime($row['hs_datetime']))?>
        </td>
		<td style="text-align:left;padding:3px;">
    		<span title="<?php echo $row['wr_message']?>" style="vertical-align:top;"><?php echo $row['wr_message']?></span>
		</td>
		<td class="td_boolean" title="<?php echo $row['hs_memo']?>"><?php echo $row['hs_flag']?'<span style="color:#9933ff">성공</span>':'<span style="color:#ff3333">실패</span>'?></td>
		
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

function addNumber() {
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

function addSelectedNumbersToQueue() {
	var f = document.addSelectedNumbersToQueueForm;
	var checked_cnt = 0;
	$('input[name="ids[]"]').each(function() {
		if($(this).prop('checked')) {
			checked_cnt++;
		}
	})

	if(checked_cnt == 0) {
		alert("전송하실 번호를 하나이상 선택해 주세요.");
		return false;
	}

	f.submit();

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

//-->
</script>