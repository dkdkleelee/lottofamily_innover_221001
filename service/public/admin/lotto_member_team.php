<?
define('G5_IS_ADMIN', true);
$sub_menu = "200120";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'r');

use Acesoft\Common\Utils;
use Acesoft\LottoApp\TermService;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\LottoWinRecords;
use Acesoft\LottoApp\Member\User;
use Acesoft\LottoApp\Member\Group;


//▶ pagination url
$param1 = Utils::getParameters(array('page','s_ca_no'))."&s_ca_no[]=".@array_pop(array_filter($_GET['s_ca_no']));
$param2 = Utils::getParameters(array('nocache'));
$list_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$param1;
$return_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$param2;


// lotto config
$lottoServiceConfig = new LottoServiceConfig();
$data_config = $lottoServiceConfig->getConfig();

//▶ get list data
$group = new Group();

$data = $group->getList($_GET['page'], $list_url);



$g5['title'] = '<i class="fa fa-group"></i> 팀관리';
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
		 <span class="sub_title"><i class="fa fa fa-plus fa-lg fa-lg f_blue"></i> 팀 등록관리</span>
	</div>
	<form name="group_add_form" id="group_add_form" method="post" action="lotto_member_group_process.php">
	<input type="hidden" name="proc" value="addGroup">
	<input type="hidden" name="return_url" value="<?=$return_url?>">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
	<tr>
		<td>
			팀 이름
		</td>
		<td><input type="text" name="mg_name" class="frm_input" style="width:90%" value="" placeholder="팀 이름" required></td>
		
		<td>
			 <button type="submit" class="as-btn small green"><i class="fa fa-check"></i> 등록</button>
		</td>
	</tr>
	</form>
	</table>
	<br />
	<div class="search_box ar">
		<form name="searchForm" method="get" action="?">
			
			<input type="text" name="s_mg_name" class="frm_input">
			
			<button type="submit" class="as-btn medium white"><i class="fa fa-search"></i> 검색</button>
		</form>
	</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
		<th width="40px">번호</th>
		<th>팀이름</th>
		<th>등록팀원 수</th>
		<th width="180px">변경</th>
	</tr>
	<?
	foreach($data['list'] as $row) {

	?>
	<form name="team_list_form" id="team_list_form" method="post" action="lotto_member_group_process.php">
    	<input type="hidden" name="proc" value="updateGroup">
    	<input type="hidden" name="mg_no" value="<?=$row['mg_no']?>">
		<input type="hidden" name="return_url" value="<?=$return_url?>">
	<tr>
		<td><?=$data['idx']--?></td>
		<td><input type="text" name="mg_name" class="frm_input" value="<?=$row['mg_name']?>"></td>
		
		<td><?=number_format($row['member_count'])?>명</td>
		<td>
			<button type="submit" class="as-btn small green"><i class="fa fa-check"></i> 수정</button>
			 <button type="button" class="as-btn small green" onClick="deleteData('<?=$row['mg_no']?>');"><i class="fa fa-wrench"></i> 삭제</button>
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
<form name="deleteForm" method="post" action="./lotto_member_group_process.php?<?=$param?>">
<input type="hidden" name="proc" value="deleteGroup">
<input type="hidden" name="mg_no" value="">
<input type="hidden" name="return_url" value="<?=$return_url?>">
</form>
<script type="text/javascript">
<!--

function addData() {
    var f = $('#group_add_form');
    $(f).submit();
    
}

function deleteData(no) {

	var f = document.deleteForm;
	if(confirm("데이터를 삭제합니다.\n삭제하신데이터는 복구가 불가능합니다.\n계속하시겠습니까?")) {
		f.mg_no.value = no;
		f.submit();
	}

}
//-->
</script>
<?
include_once (G5_ADMIN_PATH."/admin.tail.php");
?>