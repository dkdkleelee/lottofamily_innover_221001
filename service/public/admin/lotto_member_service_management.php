<?
define('G5_IS_ADMIN', true);
$sub_menu = "500100";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'r');

use Acesoft\Common\Utils;
use Acesoft\LottoApp\TermService;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\Member\User;


//▶ pagination url
$param1 = Utils::getParameters(array('page','s_ca_no'))."&s_ca_no[]=".@array_pop(array_filter($_GET['s_ca_no']));
$list_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$param1;


// term service
$termService = new TermService();

// lotto service
$lottoService = new LottoService();


//▶ get list data
$data = $lottoService->getServiceUserList($_GET['page'], $list_url);


//▶ 추출번호 inning data
$inning_arr = $lottoService->getExtractNumberInningGroups();

if(!$_GET['s_inning']) $_GET['s_inning'] = $inning_arr[0]['inning'];
//$member_arr = $termService->db->arrayBuilder()->get($termService->tb['Member'], null, '*');


$g5['title'] = "회원서비스관리";
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
		 <span class="sub_title"><i class="fa fa fa-plus fa-lg fa-lg f_blue"></i> 회원서비스관리</span>
	</div>
	<div class="search_box ar">
		<form name="searchForm" method="get" action="?">
			<select name="s_service_use" id="s_service_use" class="frm_input" onChange="document.searchForm.submit()">
				<option value="">전체</option>
				<option value="1" <?=$_GET['s_service_use'] == '1' ? 'selected' : ''?>>유료회원</option>
				<option value="2" <?=$_GET['s_service_use'] == '2' ? 'selected' : ''?>>무료회원</option>
			</select>
			<!-- <select name="s_inning" id="s_inning" class="frm_input" onChange="document.searchForm.submit()">
				<option value="">회차선택</option>
			<? for($i=0; $i<count($inning_arr); $i++ ) { ?>
				<option value="<?=$inning_arr[$i]['inning']?>" <?=($_GET['s_inning'] == $inning_arr[$i]['inning']) ? 'selected' : ''?>><?=$inning_arr[$i]['inning']?>회</option>
			<? } ?>
            </select> -->
			<select name="sc" class="frm_input">
				<option value="a.mb_name" <?=($_GET['sc']=='a.mb_name') ? 'selected' : '';?>>이름</option>
				<option value="a.mb_id" <?=($_GET['sc']=='a.mb_id') ? 'selected' : '';?>>아이디</option>
			</select>
			<input type="text" name="sv" class="frm_input" value="<?=$_GET['sv']?>">
			<button type="submit" class="as-btn medium white"><i class="fa fa-search"></i> 검색</button>
		</form>
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
		<th width="180px">변경</th>
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
			<?=$termService->service_grade[$row['sg_no']]?>
		</td>
        <td>
           <?=($row['leftDays']) ? $row['su_enddate'] : '일반'?>
		</td>
		<td>
		   <?=($row['leftDays'] > 0) ? $row['leftDays'] : 0 ?>일
		</td>
		<td><?=$row['leftDays'] > 0 ? '서비스 이용중' : '--'?>&nbsp;</td>
		<td>
			 <button type="button" class="as-btn small green" onClick="manageService('<?=$row['mb_id']?>');"><i class="fa fa-wrench"></i> 관리</button>
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
	var f = document.searchForm;
	var s_service_use = f.s_service_use.value;
	//var s_inning = f.s_inning.value;
	var sc = f.sc.value;
	var sv = f.sv.value;
	//window.open("./term_service_sms_management.php?id="+id+"&s_service_use="+s_service_use+"&sc="+sc+"&sv="+sv, 'manage', 'width=750, height=800, scrollbars=yes');
	window.open('./lotto_member_management.php?mb_id='+id, id, 'width=1200,height=800, scrollbars=yes');
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