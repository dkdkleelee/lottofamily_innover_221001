<?php
include_once("./_common.php");

$cur = 4;
include_once("../../head_05.php");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\TermService;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Member\User;
use Acesoft\LottoApp\Member\Auth;

Auth::checkAuth();

$_GET['sg_no'] = !$_GET['sg_no'] ? "1" : $_GET['sg_no'];

// termservice
$termService = new TermService();

$products = $termService->getServiceList();

foreach($products as $key => $value) {
	$product_arr[$value['sg_no']][] = $value;
	
}


// product type
$product_type = $termService->getTermServiceGrade();


//▶ 설정정보 인출
$lottoServiceConfig = new LottoServiceConfig();
$data = $lottoServiceConfig->getConfig();
$extract_count = unserialize($data['lc_extract_count']);
$user_extract_count = unserialize($data['lc_user_extract_count']);

add_javascript(G5_POSTCODE_JS, 0);    //다음 주소 js
?>

<link type="text/css" href="./css/form.css" rel="stylesheet" />
<link type="text/css" href="./css/custom.style.css" rel="stylesheet" />
<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css" rel="stylesheet" />
<script src="./js/jquery-ui-1.8.7.custom.min.js" ></script>
<script src="./js/jquery.ui.datepicker-ko.js" ></script>
<script type="text/javascript" src="./js/HuskyEZCreator.js" charset="utf-8"></script>
<script src="./js/ace.common.js" ></script>
<div id="content">
<div class="btitle">
	<div class="btitle_top"></div>
	<div class="btitle_text">정회원 신청</div>
	<div class="btitle_locate">&gt; 홈 &gt; 정회원 신청</div>
	<div class="btitle_line"></div>
</div>

<form name="service_buy_form" method="post" action="./service_buy_process.php" onSubmit="return checkForm(this)">
<input type="hidden" name="proc" value="addServiceBuy">
<?
//foreach($product_type as $sg_no => $sg_title) {
?>
	<!-- <div style="clear:both" class="product_title"><?=$sg_title?></div> -->
	<ul >
<?	foreach($products as $key => $value) { ?>

	<? if ($value['sc_name']=='pg2') continue; ?>

	<? if ($value['sc_name']=='마스터'){ ?>

	<li class="pdt_item">
                        <ul>
                                        <li>
                                                        <div class="use_ttl">
                                                                        <ul>
                                                                                        <li class="it_term"><?=$value['sc_name']?> (Master) &nbsp&nbsp</li>
                                                                                        <li class="it_combi"><span>최고 40조합</span></li>
                                                                                        <? if($value['sc_soldout'] == '1') { ?>
                                                                                        <li class="it_buy_btn it_buy_soldout_btn"><a href="javascript:void(0);" class="purchase_btn">매진</a></li>
                                                                                        <? } else { ?>
                                                                                        <li class="it_buy_btn"><a href="javascript:joinService('<?=$value['sc_no']?>');void(0);" class="purchase_btn">구매하기</a></li>
                                                                                        <? } ?>
                                                                        </ul>
                                                        </div>
                                        </li>
                        </ul>
                        <ul>
                                        <li>
                                                        <div id="item-price-<?=$value['sc_no']?>">
                                                                <font> 담당자 협의 </font>
                                                        </div>
                                                        <div>기간 : ** <?=$termService->config['term_name'][$value['sc_term_type']]?></div>
                                                        <div><?=$value['sc_detail1']?></div>
                                                        <!-- <div>가입혜택 : 매주 최고 <?=$extract_count[$sg_no]?>조합씩 <?=$value['sc_term']?> <?=$termService->config['term_name'][$value['sc_term_type']]?>간 분석번호를<br> 문자로 받으실 수 있습니다</div> -->
                                                        <div>
                                                                <strong></strong><br>

                                                        </div>
                                                        <? if($value['sc_soldout'] == '1') { ?>
                                                        <div class="copy_soldout"><img src="./images/copy_sold_out.png"></div>
                                                        <? } ?>
                                        </li>
                        </ul>
        </li>

	<? } else if ($value['sc_name']=='VIP제로') { ?>

	<? } else { ?>

	<li class="pdt_item">
			<ul>
					<li>
							<div class="use_ttl">
									<ul>
											<li class="it_title"><?=$value['sc_name']?> ( 
												<? if ($product_type[$value['sg_no']]=='챌린져') { ?>
												Challenger
												<? } else if ($product_type[$value['sg_no']]=='다이아') { ?>
												Diamond
												<? } ?>
											 )</li>
											<li class="it_term"><font><?=$value['sc_term']?> <?=$termService->config['term_name'][$value['sc_term_type']]?></font> 이용권</li>
											<li class="it_combi"><span>최고 <?=$extract_count[$value['sg_no']]?>조합</span></li>
											<? if($value['sc_soldout'] == '1') { ?>
											<li class="it_buy_btn it_buy_soldout_btn"><a href="javascript:void(0);" class="purchase_btn">매진</a></li>
											<? } else { ?>
											<li class="it_buy_btn"><a href="javascript:joinService('<?=$value['sc_no']?>');void(0);" class="purchase_btn">구매하기</a></li>
											<? } ?>
									</ul>
							</div>
					</li>
			</ul>
			<ul>
					<li>
							<div id="item-price-<?=$value['sc_no']?>">
						<? if ($product_type[$value['sg_no']]=='다이아') { ?>
								<font>담당자 협의</font>
						<? } else { ?>
								<font><?=number_format($value['sc_price'])?></font>원 <span>(VAT 포함)</span>
						<? } ?>
							</div>
							<div>기간 : <?=$value['sc_term']?> <?=$termService->config['term_name'][$value['sc_term_type']]?></div>
							<div><?=$value['sc_detail1']?></div>
							<!-- <div>가입혜택 : 매주 최고 <?=$extract_count[$sg_no]?>조합씩 <?=$value['sc_term']?> <?=$termService->config['term_name'][$value['sc_term_type']]?>간 분석번호를<br> 문자로 받으실 수 있습니다</div> -->
							<div>
								<strong></strong><br>
								
							</div>
							<? if($value['sc_soldout'] == '1') { ?>
							<div class="copy_soldout"><img src="./images/copy_sold_out.png"></div>
							<? } ?>
					</li>
			</ul>
	</li>

	<? } ?>

<? 
	} 
?>
	</ul>
<?
//}
?>


	<br>
	
	<div style="clear:both"></div>
	

	
</form>
    	
</div>




<script language="Javascript">
<!--

function joinService(no) {
	window.open("./service_buy_step1.php?no="+no,"service_buy_step1", "width=800px, height=600px, scrollbars=yes");
}

//-->
</script>
<?php
include_once("../../tail.php");
?>
