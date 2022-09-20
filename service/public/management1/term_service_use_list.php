<?
//define('G5_IS_ADMIN', true);
$sub_menu = "200200";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'r');

use Acesoft\Common\Utils;
use Acesoft\LottoApp\TermService;

if (!$is_admin) {
	alert('관리자 권한이 있는 계정만 접근 가능합니다.');
	return;
}

//▶ pagination url
$param1 = Utils::getParameters(array('page','s_ca_no'))."&s_ca_no[]=".@array_pop(array_filter($_GET['s_ca_no']));
$list_url = "http://".$_SERVER[HTTP_HOST].$_SERVER[PHP_SELF]."?".$param1;


// term service
$termService = new TermService();


//▶ get list data
$data = $termService->getTermServiceUseList($_GET['page'], $list_url);



$member_arr = $termService->db->arrayBuilder()->get($termService->tb['Member'], null, '*');

$g5['title'] =  "서비스이용관리";
include_once(G5_ADMIN_PATH."/admin.head.php");
?>
<link rel="stylesheet" href="../css/custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/admin.custom.style.css" type="text/css">
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
		 <span class="sub_title"><i class="fa fa fa-plus fa-lg fa-lg f_blue"></i> 고객서비스추가</span>
	</div>
	<div>
		 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 유료서비스를 관리자가 회원에게 직접등록합니다.</span>
	</div>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
		
		<th>이름(아이디)</th>
		<th>서비스종류</th>
		<th>서비스 기간</th>
		<th width="180px">변경</th>
	</tr>
	<form name="service_use_form_<?=$row['su_no']?>" id="service_add_form" method="post" action="./term_service_use_process.php">
    <input type="hidden" name="proc" value="addServiceTerm">
	<tr>
		
		<td>
    		<select name="mb_id" class="frm_input" itemname="회원선택" required>
        		<option value="">선택</option>
        		<? for($i=0; $i<count($member_arr); $i++) { ?>
            		<option value="<?=$member_arr[$i]['mb_id']?>"><?=$member_arr[$i]['mb_nick']?>(<?=$member_arr[$i]['mb_id']?>)</option>
        		<? } ?>
    		</select>
    		</td>
		<td>
    		<select name="sg_no" class="frm_input" itemname="서비스구분" required>
        		<? foreach($termService->service_grade as $key => $value) { ?>
        		<option value="<?=$key?>" <?=($row['sg_no'] == $key) ? 'selected' : ''?>><?=$value?></option>
        		<? } ?>
    		</select>
		</td>
		
        <td>
           <input type="text" name="term" id="term" itemname="기간" class="frm_input" style="width:50px;text-align:right" required>
           		
			<select name="term_type" id="term_type" itemname="일/개월" class="frm_input" required>
           		<option value="day">일</option>
           		<option value="month">월</option>
    		</select>
		</td>
		<td>
			<button type="button" class="as-btn small green" onClick="addData();"><i class="fa fa-plus"></i> 추가</button>
		</td>
	</tr>
	</form>
    </table>
	<br /><br />
	<div>
		 <span class="sub_title"><i class="fa fa fa-plus fa-lg fa-lg f_blue"></i> 서비스를 이용중인 고객목록</span>
	</div>
	
	<div class="search_box ar">
		<form name="searchForm" method="get" action="?">
				
				<select name="s_sg_no" id="sido" class="frm_input" onChange="document.searchForm.submit()">
            		<option value="">선택</option>
					<? foreach($termService->service_grade as $key => $value) { ?>
        			<option value="<?=$key?>" <?=($_GET['s_sg_no'] == $key) ? 'selected' : ''?>><?=$value?></option>
        			<? } ?>
            	</select>
				<select name="sc" class="frm_input">
					<option value="b.mb_name" <?=($_GET['sc']=='b.mb_name') ? 'selected' : '';?>>이름</option>
					<option value="a.mb_id" <?=($_GET['sc']=='a.mb_id') ? 'selected' : '';?>>신청자 아이디</option>
				</select>
				<input type="text" name="sv" class="frm_input" value="<?=$_GET['sv']?>">
				<button type="submit" class="as-btn small white"><i class="fa fa-search"></i> 검색</button>
		</form>
	</div>
	<div>
		 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 유료서비스를 이용중인 고객목록입니다.</span>
		 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 서비스종류 및 종료일을 관리자가 수정 또는 삭제하실 수 있습니다.</span>
	</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
		<th width="40px">번호</th>
		<th>이름(아이디)</th>
		<th>휴대폰</th>
		<th>서비스종류</th>
		<th>서비스종료일</th>
		<th>잔여일</th>
		<th>상태</th>
		<th width="240px">변경</th>
	</tr>
	<?
	foreach($data['list'] as $row) {

	?>
	<form name="service_use_form_<?=$row['su_no']?>" id="service_use_form_<?=$row['su_no']?>" method="post" action="term_service_use_process.php">
    	<input type="hidden" name="proc" value="updateServiceUse">
    	<input type="hidden" name="su_no" value="<?=$row['su_no']?>">
	<tr>
		<td><?=$data['idx']--?></td>
		<td><?=$row['mb_name']?>(<?=$row['mb_id']?>)</td>
		<td><?=$row['mb_hp']?></td>
		<td>
    		<select name="sg_no" class="frm_input">
        		<? foreach($termService->service_grade as $key => $value) { ?>
        		<option value="<?=$key?>" <?=($row['sg_no'] == $key) ? 'selected' : ''?> style="background-color: #007700"><?=$value?></option>
        		<? } ?>
    		</select>
		</td>
        <td>
           <input type="text" class="frm_input datetimepicker" name="su_enddate" value="<?=$row['su_enddate']?>" style="width:150px">
		</td>
		<td>
		   <?=($row['leftDays'] > 0) ? $row['leftDays'] : 0 ?>일
		</td>
		<td><?=$row['expired'] ? '서비스 만료' : '서비스 이용중'?>&nbsp;</td>
		<td>
			<!-- <span class="button medium strong"><a href="javascript:memoWin('<?=$row['su_no']?>');void(0);">(<? //$auction->getMemoNum('su_no', $row['su_no'])?>) 메모</a></span> -->
			 <button type="button" class="as-btn small green" onClick="manageService('<?=$row['mb_id']?>');"><i class="fa fa-wrench"></i> 관리</button>
			<button type="button" class="as-btn small green" onClick="modifyData('<?=$row['su_no']?>');"><i class="fa fa-refresh"></i> 수정</button>
			<button type="button" class="as-btn small red" onclick="deleteData('<?=$row['su_no']?>')"><i class="fa fa-close"></i> 삭제</button>
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


});

function manageService(id) {
	window.open("./term_service_management.php?id="+id, '', 'width=650, height=600, scrollbars=yes');
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
