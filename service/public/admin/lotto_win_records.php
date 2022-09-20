<?
define('G5_IS_ADMIN', true);
$sub_menu = "500100";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'r');

use Acesoft\Common\Utils;
use Acesoft\LottoApp\TermService;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\LottoWinRecords;
use Acesoft\LottoApp\Member\User;


//▶ pagination url
$param1 = Utils::getParameters(array('page','s_ca_no'))."&s_ca_no[]=".@array_pop(array_filter($_GET['s_ca_no']));
$list_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$param1;

// lotto config
$lottoServiceConfig = new LottoServiceConfig();
$data_config = $lottoServiceConfig->getConfig();

//▶ get list data
$lottoWinRecords = new LottoWinRecords();

$data = $lottoWinRecords->getList($_GET['page'], $list_url);



//▶ 추출번호 inning data
$lottoService = new LottoService();
$inning_arr = $lottoService->getExtractNumberInningGroups();

if(!$_GET['s_inning']) $_GET['s_inning'] = $inning_arr[0]['inning'];
//$member_arr = $termService->db->arrayBuilder()->get($termService->tb['Member'], null, '*');


$g5['title'] = "당첨기록관리";
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
		 <span class="sub_title"><i class="fa fa fa-plus fa-lg fa-lg f_blue"></i> 당첨기록 수동등록관리</span>
	</div>
	<form name="win_records_form_<?=$row['wr_inning']?>" id="win_records_form_<?=$row['wr_inning']?>" method="post" action="lotto_win_records_process.php">
    	<input type="hidden" name="proc" value="addWinRecord">
    	<input type="hidden" name="su_no" value="<?=$row['su_no']?>">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
	<tr>
		<td>
			<select name="wr_inning" id="wr_inning" class="frm_input" required>
				<option value="">회차선택</option>
			<?
				for($i=0; $i<count($inning_arr); $i++ ) { 
					if($inning_arr[$i]['inning'] <= $data_config['lc_cur_inning']) {
			?>
				<option value="<?=$inning_arr[$i]['inning']?>" ><?=$inning_arr[$i]['inning']?>회</option>
			<? 
					}
				}
			?>
            </select>
		</td>
		<td><input type="text" name="wr_1grade_num" class="frm_input" value="<?=$row['wr_1grade_num']?>" placeholder="1등당첨자 수"></td>
		<!-- <td><input type="text" name="wr_1grade_prize" class="frm_input" value="<?=$row['wr_1grade_prize']?>" placeholder="1등당첨금액"></td> -->
		<td><input type="text" name="wr_2grade_num" class="frm_input" value="<?=$row['wr_2grade_num']?>" placeholder="2등당첨자 수"></td>
		<!-- <td><input type="text" name="wr_2grade_prize" class="frm_input" value="<?=$row['wr_2grade_prize']?>" placeholder="2등당첨금액"></td> -->
        <td><input type="text" name="wr_3grade_num" class="frm_input" value="<?=$row['wr_3grade_num']?>" placeholder="3등당첨자 수"></td>
		<!-- <td><input type="text" name="wr_3grade_prize" class="frm_input" value="<?=$row['wr_3grade_prize']?>" placeholder="3등당첨금액"></td> -->
		<td><input type="text" name="wr_4grade_num" class="frm_input" value="<?=$row['wr_4grade_num']?>" placeholder="4등당첨자 수"></td>
		<!-- <td><input type="text" name="wr_4grade_prize" class="frm_input" value="<?=$row['wr_4grade_prize']?>" placeholder="4등당첨금액"></td> -->
		<td>
			 <button type="submit" class="as-btn small green"><i class="fa fa-check"></i> 등록</button>
		</td>
	</tr>
	</form>
	</table>
	<br />
	<div class="search_box ar">
		<form name="searchForm" method="get" action="?">
			
			<select name="s_wr_inning" id="s_wr_inning" class="frm_input" onChange="document.searchForm.submit()">
				<option value="">회차선택</option>
			<? for($i=0; $i<count($inning_arr); $i++ ) { ?>
				<option value="<?=$inning_arr[$i]['inning']?>" <?=($_GET['s_wr_inning'] == $inning_arr[$i]['inning']) ? 'selected' : ''?>><?=$inning_arr[$i]['inning']?>회</option>
			<? } ?>
            </select>
			
			<button type="submit" class="as-btn medium white"><i class="fa fa-search"></i> 검색</button>
		</form>
	</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
		<th width="40px">회차</th>
		<th>1등</th>
		<!-- <th>1등금액</th> -->
		<th>2등</th>
		<!-- <th>2등금액</th> -->
		<th>3등</th>
		<!-- <th>3등금액</th> -->
		<th>4등</th>
		<!-- <th>4등금액</th> -->
		<th width="180px">변경</th>
	</tr>
	<?
	foreach($data['list'] as $row) {

	?>
	<form name="win_records_form_<?=$row['wr_inning']?>" id="win_records_form_<?=$row['wr_inning']?>" method="post" action="lotto_win_records_process.php">
    	<input type="hidden" name="proc" value="modifyWinRecord">
    	<input type="hidden" name="wr_inning" value="<?=$row['wr_inning']?>">
	<tr>
		<td><?=$row['wr_inning']?></td>
		<td><input type="text" name="wr_1grade_num" class="frm_input" value="<?=$row['wr_1grade_num']?>"></td>
		<!-- <td><input type="text" name="wr_1grade_prize" class="frm_input" value="<?=$row['wr_1grade_prize']?>"></td> -->
		<td><input type="text" name="wr_2grade_num" class="frm_input" value="<?=$row['wr_2grade_num']?>"></td>
		<!-- <td><input type="text" name="wr_2grade_prize" class="frm_input" value="<?=$row['wr_2grade_prize']?>"></td> -->
        <td><input type="text" name="wr_3grade_num" class="frm_input" value="<?=$row['wr_3grade_num']?>"></td>
		<!-- <td><input type="text" name="wr_3grade_prize" class="frm_input" value="<?=$row['wr_3grade_prize']?>"></td> -->
		<td><input type="text" name="wr_4grade_num" class="frm_input" value="<?=$row['wr_4grade_num']?>"></td>
		<!-- <td><input type="text" name="wr_4grade_prize" class="frm_input" value="<?=$row['wr_4grade_prize']?>"></td> -->
		<td>
			<button type="submit" class="as-btn small green"><i class="fa fa-check"></i> 수정</button>
			 <button type="button" class="as-btn small green" onClick="deleteData('<?=$row['wr_inning']?>');"><i class="fa fa-wrench"></i> 삭제</button>
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
<form name="deleteForm" method="post" action="./lotto_win_records_process.php?<?=$param?>">
<input type="hidden" name="proc" value="deleteWinRecord">
<input type="hidden" name="wr_inning" value="">
</form>
<script type="text/javascript">
<!--

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
		f.wr_inning.value = no;
		f.submit();
	}

}
//-->
</script>
<?
include_once (G5_ADMIN_PATH."/admin.tail.php");
?>