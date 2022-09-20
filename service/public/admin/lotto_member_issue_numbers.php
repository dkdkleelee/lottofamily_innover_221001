<?
define('G5_IS_ADMIN', true);
define('NO_CACHE', true);
$sub_menu = "400100";
include_once("./_common.php");


// 사용되지 않는 페이지
// invaderx 2018-10-17
// 발급번호 조회용으로 나중에 사용할지도 모름


auth_check($auth[$sub_menu], 'r');

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

$_GET['s_mb_id'] = $_GET['id'];
$_GET['type'] = 'extractor';	// 추출기 추출번호만(사용자 등록번호 제외)
//▶ get list data
$data = $lottoService->getExtractNumberList($_GET['page'], $list_url);


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
			<li><a href="./lotto_member_win_list.php?<?=$param1?>" class="">당첨내역 조회</a></li>
			<li><a href="./lotto_member_issue_numbers.php?<?=$param1?>" class="tab_on">번호발급</a></li>
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
		 <h5 class="title"><i class="fa fa-plus fa-lg"></i> <?=$_GET['s_inning']?>추출번호 목록</h5>
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
	
	<br />
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tb02">
	<tr>
		<th style="width:10%">회차</th>
		<th style="width:30%">번호</th>
		<th style="width:10%">구분</th>
		<th style="width:15%">발급</th>
		<th style="width:10%">추첨결과</th>
	</tr>
	<?
		foreach($data['list'] as $row) {
			
	?>
	<tr>
		<th><?=$row['le_inning']?>회</th>
		<td style="text-align:center">
			<img src="/images/ball_<?=sprintf('%02d', $row['le_num1'])?>.png" width="20px">
			<img src="/images/ball_<?=sprintf('%02d', $row['le_num2'])?>.png" width="20px">
			<img src="/images/ball_<?=sprintf('%02d', $row['le_num3'])?>.png" width="20px">
			<img src="/images/ball_<?=sprintf('%02d', $row['le_num4'])?>.png" width="20px">
			<img src="/images/ball_<?=sprintf('%02d', $row['le_num5'])?>.png" width="20px">
			<img src="/images/ball_<?=sprintf('%02d', $row['le_num6'])?>.png" width="20px">
		</td>
		<td style="text-align:center"><?=$lottoService->getNumberType()[$row['le_type']]?></td>
		<td style="text-align:center"><?=$row['mb_id'] ? $row['mb_id'] : '미발급'?></td>
		<td style="text-align:center"><?=($row['lwr_grade']) ? $row['lwr_grade']."등" : '--'?></td>
	</tr>
	<? } ?>
	</table>

	<div class="paginate wrapper paging_box">
			<?=$data['link']?>	
	</div>

</div>

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