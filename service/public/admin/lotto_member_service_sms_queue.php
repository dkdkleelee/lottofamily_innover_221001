<?
define('G5_IS_ADMIN', true);
$sub_menu = "500100";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'r');

use Acesoft\Common\Utils;
use Acesoft\Common\Message;
use Acesoft\LottoApp\TermService;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\Member\User;


//▶ pagination url
$param1 = Utils::getParameters(array('page','s_ca_no'))."&s_ca_no[]=".@array_pop(array_filter($_GET['s_ca_no']));
$list_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$param1;


// term service
$message = new Message();


//▶ get list data
$data = $message->getMessageList($_GET['page'], $list_url);



$g5['title'] = "메세지대기열관리";
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
		 <span class="sub_title"><i class="fa fa fa-plus fa-lg fa-lg f_blue"></i> SMS전송관리</span>
	</div>
	<div>
		 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> '로또서비스관리 > 기본환경설정'에서 자동발송을 체크하지 않으면 수동발송으로 현재 페이지에 발송될 SMS목록이 생성됩니다.</span>
		 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> '메세지선택' 후 '선택메세지승인'버튼을 누르시면 잠시 후 메세지가 발송됩니다. </span>
	</div>
	<div class="search_box ar">
		<form name="searchForm" method="get" action="?">
			<select name="s_sent" id="s_sent" class="frm_input" onChange="document.searchForm.submit()">
				<option value="">발송상태</option>
				<option value="1" <?=$_GET['s_sent'] == '1' ? 'selected' : ''?>>미발송</option>
				<option value="2" <?=$_GET['s_sent'] == '2' ? 'selected' : ''?>>발송</option>
			</select>
			<select name="s_ok" id="s_ok" class="frm_input" onChange="document.searchForm.submit()">
				<option value="">전체</option>
				<option value="0" <?=$_GET['s_ok'] == '0' ? 'selected' : ''?>>미승인</option>
				<option value="1" <?=$_GET['s_ok'] == '1' ? 'selected' : ''?>>승인</option>
			</select>
			<select name="sc" class="frm_input">
				<option value="mb_name" <?=($_GET['sc']=='a.mb_name') ? 'selected' : '';?>>이름</option>
				<option value="c.mb_id" <?=($_GET['sc']=='c.mb_id') ? 'selected' : '';?>>아이디</option>
			</select>
			<input type="text" name="sv" class="frm_input" value="<?=$_GET['sv']?>">
			<button type="submit" class="as-btn medium white"><i class="fa fa-search"></i> 검색</button>
		</form>
	</div>
	<form name="message_list_form" id="message_list_form" method="post" action="lotto_member_service_process.php">
    	<input type="hidden" name="proc" value="updateConfirmStatus">
    	<input type="hidden" name="status" value="">
		<input type="hidden" name="return_url" value="<?=$list_url?>">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
		<th width="70px"><label><input type="checkbox" name="check_all" id="check_all">전체선택</label></th>
		<th width="40px">번호</th>
		<th>이름(아이디)</th>
		<th>휴대폰</th>
		<th>제목</th>
		<th style="width:400px">내용(SMS)</th>
		<th style="width:60px">승인</th>
		<th>등록일</th>
		<th>발송일</th>
		<th width="80px">변경</th>
	</tr>
	<?
	foreach($data['list'] as $row) {
	?>
	
	<tr>
		<td><input type="checkbox" name="chk[]" value="<?=$row['id']?>"> </td>
		<td><?=$data['idx']--?></td>
		<td><?=$row['mb_name']?>(<?=$row['mb_id']?>)</td>
		<td><?=$row['mb_hp']?></td>
		<td>
			<?=$row['title']?>
		</td>
        <td data-tooltip="<?=nl2br($row['message'])?>">
           <?=Utils::textCut($row['message'], 60)?>
		</td>
		<td>
		   <?=($row['confirm'] == '1') ? '<span style="color:blue">승인</span>' : '<span style="color:red">미승인</span>' ?>
		</td>
		<td><?=$row['created_at']?>&nbsp;</td>
		<td><?=($row['sent_at']) ? $row['sent_at'] : '--'?>&nbsp;</td>
		<td>
			 <button type="button" class="as-btn small green" onClick="manageService('<?=$row['mb_id']?>');"><i class="fa fa-wrench"></i> 관리</button>
		</td>
	</tr>
	
	<? }?>
	</table>
	<p />
	</form>
	<div class="btn_box ar">
		<button type="button" class="as-btn small green" onclick="changeMessageConfirmStatus('1');"><i class="fa fa-check"></i> 선택 메세지승인</button>
		<button type="button" class="as-btn small green" onclick="changeMessageConfirmStatus('0');"><i class="fa fa-close"></i> 선택 메세지비승인</button>
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

function changeMessageConfirmStatus(status) {

	if($('input[name^="chk["]:checked').length == 0) {
		alert("변경하실 메세지를 선택해 주세요.");
		return false;
	}

	if(confirm("선택된 메세지들을 "+(status == '1' ? '승인' : '미승인')+"상태로 변경하시겠습니까?")) {
		var f = document.message_list_form;
		f.status.value = status;
		f.submit();
	}
}

function manageService(id) {
	var f = document.searchForm;
	//var s_service_use = f.s_service_use.value;
	//var s_inning = f.s_inning.value;
	//var sc = f.sc.value;
	//var sv = f.sv.value;
	//window.open("./term_service_management.php?id="+id+"&s_service_use="+s_service_use+"&sc="+sc+"&sv="+sv, '', 'width=1200, height=800, scrollbar-y=yes');
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

$(document).ready(function() {


	$('[data-tooltip]').hover(function(){
		$('<div class="div-tooltip"></div>').html($(this).attr('data-tooltip')).appendTo('body').fadeIn('fast');
	}, function() { 
		$('.div-tooltip').remove();
	}).mousemove(function(e) {
		$('.div-tooltip').css({ top: e.pageY + 10, left:  e.pageX + 20 })
	});



});
//-->
</script>
<?
include_once (G5_ADMIN_PATH."/admin.tail.php");
?>