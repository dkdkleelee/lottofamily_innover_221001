<?php
include_once("./_common.php");

$cur = 4;
include_once("../../head_04.php");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\LottoServiceConfig;

//▶ pagination url
$param1 = Utils::getParameters(array('page','s_ca_no'))."&s_ca_no[]=".@array_pop(array_filter($_GET['s_ca_no']));
$list_url = "http://".$_SERVER[HTTP_HOST].$_SERVER[PHP_SELF]."?".$param1;

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
<style>
.tb02 td { line-height:17px }
</style>
<div class="info_container">
<div class="btitle">
	<div class="btitle_top"></div>
	<div class="btitle_text">패턴분석</div>
	<div class="btitle_locate">&gt; 패밀리 분석실 &gt; 패턴분석</div>
	<div class="btitle_line"></div>
</div>
<div class="content_wrap">
	
	<div class="search_box ar">
		<form name="searchForm" method="get" action="?">
				
				<!-- <select name="s_from_inning" id="s_from_inning" class="frm_input">
            		<option value="">회차선택</option>
                <? for($i=0; $i<count($inning_arr); $i++ ) { ?>
                    <option value="<?=$inning_arr[$i]['inning']?>" <?=($_GET['s_from_inning'] == $inning_arr[$i]['inning']) ? 'selected' : ''?>><?=$inning_arr[$i]['inning']?>회</option>
                <? } ?>
            	</select>&nbsp;기준&nbsp;
				<select name="s_page_limit" id="s_page_limit" class="frm_input">
					<option value="20" <?=$_GET['s_page_limit'] == '20' ? 'selected' : ''?>>20회</option>
					<option value="30" <?=$_GET['s_page_limit'] == '30' ? 'selected' : ''?>>30회</option>
					<option value="40" <?=$_GET['s_page_limit'] == '40' ? 'selected' : ''?>>40회</option>
					<option value="50" <?=$_GET['s_page_limit'] == '50' ? 'selected' : ''?>>50회</option>
					<option value="100" <?=$_GET['s_page_limit'] == '100' ? 'selected' : ''?>>100회</option>
				</select>
				
				<button type="submit" class="as-btn medium blue"><i class="fa fa-search"></i> 검색</button> -->
		</form>
	</div>
	<div class="btn_box ar">
		<button type="button" onclick="$('.pattern').toggle()" class="as-btn small white">패턴만보기</button>
	</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tb02">
	<tr>
	<?
	$col = 1;
	foreach($data['list'] as $row) {
		$num_str = $row['lw_num1'].",".$row['lw_num2'].",".$row['lw_num3'].",".$row['lw_num4'].",".$row['lw_num5'].",".$row['lw_num6'];
		$num_arr = explode(",", $num_str);

		if($col%4 == 1) echo "</tr><tr>";
	?>

		<td class="col" style="vertical-align:top">
			<div class="title"><?=$row['lw_inning']?>회<!-- (<?=$row['lw_date']?>) --></div>
			<div class="colbox" style="height:364px;">
			<table class="pattern" style="width:150px;height:234px;margin:0px auto" cellpadding="0px" cellspacing="5px">
				<tr>
			<? 
				for($i=1; $i<46; $i++) { 
					if($i%5 == 1) echo "</tr><tr>";
			?>
					<td style="border-radius: 10px;padding:8px 1px 8px 1px;border:1px solid #e8e8e8;width:15px" class="<?=in_array($i, $num_arr) ? 'picked' : ''?><?=$i == $row['lw_num7'] ? 'picked-bonus' : '' ?>"><?=$i?></td>
			<?	} ?>
				</tr>
			</table>
			</div>
			<canvas id="cv_<?=$row['lw_inning']?>" class='canvas'></canvas>
			<script>
				var numbers = [<?=$num_str?>];

				var c=document.getElementById("cv_<?=$row['lw_inning']?>");
				var position_x_inc = (c.width/6)/2;
				var position_y_inc = (c.height/9)/2;
				var ctx=c.getContext("2d");
				ctx.beginPath();
				for(var i=1; i<numbers.length; i++) {

					var fx_num = numbers[i-1]/5 > 1 && numbers[i-1] > 5 ? parseInt(numbers[i-1]%5) > 0 ? parseInt(numbers[i-1]%5) : 5 : numbers[i-1];
					var fy_num = numbers[i-1]/5 > 1 && numbers[i-1] > 5 ? Math.ceil(numbers[i-1]/5) : 1;
					var fx = (fx_num > 1) ? fx_num * (position_x_inc*2)-position_x_inc : fx_num * position_x_inc;
					var fy = (fy_num > 1) ? fy_num * (position_y_inc*2)-position_y_inc : fy_num * position_y_inc;

					var tx_num = numbers[i]/5 > 1 && numbers[i] > 5 ? parseInt(numbers[i]%5) > 0 ? parseInt(numbers[i]%5) : 5 : numbers[i];
					var ty_num = numbers[i]/5 > 1 && numbers[i] > 5 ? Math.ceil(numbers[i]/5) : 1;
					var tx = (tx_num > 1) ? tx_num * (position_x_inc*2)-position_x_inc : tx_num * position_x_inc;
					var ty = (ty_num > 1) ? ty_num * (position_y_inc*2)-position_y_inc : ty_num * position_y_inc;
					
					
					//ctx.arc(fx,fy,15,0, 2*Math.PI);
					ctx.fillStyle = "orange";
					ctx.fill();

					ctx.moveTo(fx,fy);
					ctx.lineTo(tx,ty);

				}
				ctx.lineCap = 'round';
				ctx.strokeStyle = 'gray';
				ctx.stroke();
			</script>
		</td>

	<?
		$col++;
	}
	?>
	</tr>
	</table>
	<p />

	<div class="paginate wrapper paging_box paginate-light">
			<?=$data['link']?>	
	</div>
</div>
</div>




<?php
include_once("../../tail.php");
?>