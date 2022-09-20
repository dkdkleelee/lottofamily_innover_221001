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


//▶ get list data
$pageLimit = $_GET['s_page_limit'] ? $_GET['s_page_limit'] : 20;
$data = $lottoService->getWinNumberList($_GET['page'], $list_url);
?>


<link rel="stylesheet" href="./css/custom.style.css" type="text/css">
<link rel="stylesheet" href="./css/lotto.css" type="text/css">
<link rel="stylesheet" href="./css/paginate.css" type="text/css">
<!-- <link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css" rel="stylesheet" /> -->
<script src="./js/jquery-ui-1.8.7.custom.min.js" ></script>
<script src="./js/jquery.ui.datepicker-ko.js"  charset="utf-8"></script>
<link rel="stylesheet" href="./js/datetimepicker/jquery.datetimepicker.css" type="text/css" />
<script type="text/javascript" src="./js/datetimepicker/jquery.datetimepicker.js" charset="utf-8"></script>

<div class="info_container">
<div class="btitle">
	<div class="btitle_top"></div>
	<div class="btitle_text">A.C.값 분석</div>
	<div class="btitle_locate">&gt; 패밀리 분석실 &gt; A.C.값 분석</div>
	<div class="btitle_line"></div>
</div>
<div class="content_wrap">
	
	<div class="search_box ar">
		<form name="searchForm" method="get" action="?">
				
				<select name="s_inning" id="s_inning" class="frm_input" onChange="document.searchForm.submit()">
            		<option value="">회차선택</option>
                <? for($i=0; $i<count($inning_arr); $i++ ) { ?>
                    <option value="<?=$inning_arr[$i]['inning']?>" <?=($_GET['s_inning'] == $inning_arr[$i]['inning']) ? 'selected' : ''?>><?=$inning_arr[$i]['inning']?>회 </option>
                <? } ?>
            	</select>
				
				<button type="submit" class="as-btn medium blue"><i class="fa fa-search"></i> 검색</button>
		</form>
	</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
		<th width="60px">회차</th>		
		<th>추첨일</th>
		<th>당첨번호</th>
		<th>보너스</th>
		<th width="60px">AC</th>
	</tr>
	<?
	foreach($data['list'] as $row) {

	?>
	<form name="service_use_form_<?=$row['su_no']?>" id="service_use_form_<?=$row['su_no']?>" method="post" action="term_service_use_process.php">
    	<input type="hidden" name="proc" value="updateServiceUse">
    	<input type="hidden" name="su_no" value="<?=$row['su_no']?>">
	<tr>
		<td><?=$row['lw_inning']?></td>
		<td><?=$row['lw_date']?></td>
		<td>
			<img src="/images/ball_<?=sprintf('%02d', $row['lw_num1'])?>.png" width="30px">
			<img src="/images/ball_<?=sprintf('%02d', $row['lw_num2'])?>.png" width="30px">
			<img src="/images/ball_<?=sprintf('%02d', $row['lw_num3'])?>.png" width="30px">
			<img src="/images/ball_<?=sprintf('%02d', $row['lw_num4'])?>.png" width="30px">
			<img src="/images/ball_<?=sprintf('%02d', $row['lw_num5'])?>.png" width="30px">
			<img src="/images/ball_<?=sprintf('%02d', $row['lw_num6'])?>.png" width="30px">
		</td>
		<td>
			<img src="/images/ball_<?=sprintf('%02d', $row['lw_num7'])?>.png" width="30px">
		</td>
		<td><?=$lotto->acFilter(array($row['lw_num1'],$row['lw_num2'],$row['lw_num3'],$row['lw_num4'],$row['lw_num5'],$row['lw_num6']))?></td>
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



<?php
include_once("../../tail.php");
?>