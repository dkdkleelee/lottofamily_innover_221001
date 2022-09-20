<?php
include_once("./_common.php");

$cur = 4;
include_once("../../head_04.php");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\LottoServiceConfig;

//▶ pagination url
$param1 = Utils::getParameters(array('page','s_ca_no'))."&s_ca_no[]=".@array_pop(array_filter($_GET['s_ca_no']));
$list_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$param1;

//▶ 설정정보 인출
$lottoServiceConfig = new LottoServiceConfig();
$data_config = $lottoServiceConfig->getConfig();

$include_numbers = explode(",", $data_config['lc_include_numbers']);
$exclude_numbers = explode(",", $data_config['lc_exclude_numbers']);

// lotto service
$lottoService = new LottoService();


//▶ 당첨 inning data
$inning_arr = $lottoService->getWinNumberInningGroups();
if(!$_GET['s_from_inning']) $_GET['s_from_inning'] = $inning_arr[0]['inning'];

//▶ get list data
$pageLimit = $_GET['s_page_limit'] ? $_GET['s_page_limit'] : 5;
$data = $lottoService->getWinNumberList($_GET['page'], $list_url);

// 미출현번호

$left_numbers = range(1, 45);
foreach($data['list'] as $row) {
	$tmp = array($row['lw_num1'],$row['lw_num2'],$row['lw_num3'],$row['lw_num4'],$row['lw_num5'],$row['lw_num6']);

	$left_numbers = array_diff($left_numbers, $tmp);

}

?>


<link rel="stylesheet" href="./css/custom.style.css" type="text/css">
<!-- <link rel="stylesheet" href="./css/admin.custom.style.css" type="text/css"> -->
<link rel="stylesheet" href="./css/lotto.css" type="text/css">
<link rel="stylesheet" href="./css/paginate.css" type="text/css">
<!-- <link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css" rel="stylesheet" /> -->
<script src="./js/jquery-ui-1.8.7.custom.min.js" ></script>
<script src="./js/jquery.ui.datepicker-ko.js"  charset="utf-8"></script>
<link rel="stylesheet" href="./js/datetimepicker/jquery.datetimepicker.css" type="text/css" />
<script type="text/javascript" src="./js/datetimepicker/jquery.datetimepicker.js" charset="utf-8"></script>
<script src="./js/plugins/Chartjs2/Chart.bundle.js"></script>
<script src="./js/plugins/Chartjs2/samples/utils.js"></script>
<div class="info_container">
<div class="btitle">
	<div class="btitle_top"></div>
	<div class="btitle_text">기간별 미출현번호</div>
	<div class="btitle_locate">&gt; 패밀리 분석실 &gt; 기간별 미출현번호</div>
	<div class="btitle_line"></div>
</div>
<div class="content_wrap">
	
	<div class="search_box ar">
		<form name="searchForm" method="get" action="?">
				
				<select name="s_from_inning" id="s_from_inning" class="frm_input">
            		<option value="">회차선택</option>
                <? for($i=0; $i<count($inning_arr); $i++ ) { ?>
                    <option value="<?=$inning_arr[$i]['inning']?>" <?=($_GET['s_from_inning'] == $inning_arr[$i]['inning']) ? 'selected' : ''?>><?=$inning_arr[$i]['inning']?>회</option>
                <? } ?>
            	</select>&nbsp;기준&nbsp;
				<select name="s_page_limit" id="s_page_limit" class="frm_input">
					<option value="5" <?=$_GET['s_page_limit'] == '5' ? 'selected' : ''?>>5회</option>
					<option value="10" <?=$_GET['s_page_limit'] == '10' ? 'selected' : ''?>>10회</option>
					<option value="15" <?=$_GET['s_page_limit'] == '15' ? 'selected' : ''?>>15회</option>
				</select>
				
				<button type="submit" class="as-btn medium blue"><i class="fa fa-search"></i> 검색</button>
		</form>
	</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
		<th>번호대</th>
		
		<th>미출현 번호목록</th>
	</tr>
	<tr>
		<td>1 ~ 10번</td>
		<td>
			<? 
				foreach($left_numbers as $val) {
					if($val <= 10) {
			?>
				<img src="/images/ball_<?=sprintf('%02d', $val)?>.png" width="30px">
			<?
					}
				}
			?>
		</td>
	</tr>
	<tr>
		<td>11 ~ 20번</td>
		<td>
			<? 
				foreach($left_numbers as $val) {
					if($val > 10 && $val <= 20) {
			?>
				<img src="/images/ball_<?=sprintf('%02d', $val)?>.png" width="30px">
			<?
					}
				}
			?>
		</td>
	</tr>
	<tr>
		<td>21 ~ 30번</td>
		<td>
			<? 
				foreach($left_numbers as $val) {
					if($val > 20 && $val <= 30) {
			?>
				<img src="/images/ball_<?=sprintf('%02d', $val)?>.png" width="30px">
			<?
					}
				}
			?>
		</td>
	</tr>
	<tr>
		<td>31 ~ 40번</td>
		<td>
			<? 
				foreach($left_numbers as $val) {
					if($val > 30 && $val <= 40) {
			?>
				<img src="/images/ball_<?=sprintf('%02d', $val)?>.png" width="30px">
			<?
					}
				}
			?>
		</td>
	</tr>
	<tr>
		<td>41 ~ 45번</td>
		<td>
			<? 
				foreach($left_numbers as $val) {
					if($val > 40 && $val <= 45) {
			?>
				<img src="/images/ball_<?=sprintf('%02d', $val)?>.png" width="30px">
			<?
					}
				}
			?>
		</td>
	</tr>
	</table>
	<p />
	
	<!-- <div class="paginate wrapper paging_box">
			<?=$data['link']?>	
	</div> -->
</div>
</div>



<?php
include_once("../../tail.php");
?>