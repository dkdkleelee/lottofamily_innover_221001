<?php
define('_INDEX_', true);
include_once('./_common.php');

use Acesoft\Core\DB;
use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\LottoServiceConfig;

// 초기화면 파일 경로 지정 : 이 코드는 가능한 삭제하지 마십시오.
if ($config['cf_include_index']) {
    if (!@include_once($config['cf_include_index'])) {
        die('기본환경 설정에서 초기화면 파일 경로가 잘못 설정되어 있습니다.');
    }
    return; // 이 코드의 아래는 실행을 하지 않습니다.
}

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/index.php');
    return;
}

include_once('./head.php');

if(defined('_INDEX_')) { // index에서만 실행
	include G5_BBS_PATH.'/newwin.inc.php'; // 팝업레이어
}

//▶ 당첨 inning data
// lotto service
$lottoService = new LottoService();
$inning_arr = $lottoService->getExtractNumberInningGroups();

// lotto config
$lottoServiceConfig = new LottoServiceConfig();
$data_config = $lottoServiceConfig->getConfig();


// 1등, 2등 count
$winner_count = $lottoService->getWinnerCount();

include "inc_top.php";

?>

<script language="Javascript" src="jquery.cycle.all.latest.js"></script>
<div style="width:100%;min-width:1200px;padding:57px 0 0 0">
<style>
body { padding:0 !important; margin:0 !important; }
#adRepeat { width:100%;min-width:1000px;text-align:center;margin:0;padding:0}
#nav { margin:0px;height:20px;overflow:hidden;position:absolute;left:0;top:410px;z-index:100;}
#nav div { width:20px;height:20px;float:left;margin:0 0 0 8px;background-image:url('/images/main_visual_off.png');cursor:pointer;}
#nav div.activeSlide { width:20px;height:20px;background-image:url('/images/main_visual_on.png');cursor:pointer }
#nav a:focus { outline: none; }
</style>
<script type="text/javascript">
<!--
$(document).ready(function() {
	
	$('#adRepeat').cycle({
		fx:         'fade',
		timeout:     3500,
		pager:      '#nav',
		pagerEvent: 'mouseover',
		fastOnEvent: true,
		pagerAnchorBuilder: function(index, DOMelemnet) {
			return '<div></div>';
		}
	});
});
//-->
</script>
<div style="position:relative;height:700px;min-width:1200px;width:100%;overflow:hidden">
	<!--div id="nav" style="left:50%;margin-left:-50px;"></div-->
		<div class="adRepeat" style="position:absolute; left:50%;margin-left:-1000px;">
			<ul id="adRepeat" style="min-width:1000px; z-index:99">
				<li><a href="javascript:m1()"><img src="images/rnw/메인2/메인_반응1.png" style="width:2000px;min-width:1200px"></a></a></li>
				<li><a href="/bbs/register.php"><img src="images/rnw/메인2/메인_반응2.png" style="width:2000px;min-width:1200px"></a></li>
				<li><a href="javascript:m1()"><img src="images/rnw/메인2/메인_반응3.png" style="width:2000px;min-width:1200px"></a></li>
			</ul>
		</div>
	</div>
</div>

<div class="main_lotto_bg">
<div class="main_lotto">
	<div style="float:left;padding:0 30px 0 0">
    <img src="/images/main_cs2.jpg" usemap="#cs">
    <map name="cs">
      <area shape="rect" coords="22,377,250,423" href="javascript:m5s4()">
    </map>
    </div>
	<div id="result_area" class="box_st1 main_lottoResult">
		
		<div class="title_area">
			<div class="th_arr">
				<a class="th_prev" href="/?search[where][num]=820" title="이전회차"><img src="/images/main_lotto_arr_l.gif"></a>
				<a class="th_next" href="/?search[where][num]=" title="다음회차"><img src="/images/main_lotto_arr_r.gif"></a>
			</div>
			<div class="th_select">
				<span class="num">931<b>회</b></span>
			</div>
			<h2 class="box_tit">당첨번호</h2>
			<div class="data">2021.05.01 추첨</div>
		</div>
		<div class="ball_box">
			<span class="ball"><img src="/images/ball_01.png"></span>
			<span class="ball"><img src="/images/ball_12.png"></span>
			<span class="ball"><img src="/images/ball_13.png"></span>
			<span class="ball"><img src="/images/ball_24.png"></span>
			<span class="ball"><img src="/images/ball_29.png"></span>
			<span class="ball"><img src="/images/ball_24.png"></span>
			<span class="ball"><img src="/images/ball_+.png"></span>
			<span class="ball"><img src="/images/ball_16.png"></span>
		</div>
		<div class="result_tbl">
			<table class="table_st1 table_st">
				<colgroup>
					<col style="width: 80px;">
					<col style="width: 180px;">
					<col style="width: 160px;">
					<col style="width: auto;">
				</colgroup>
				<thead>
					<tr>
						<th>순위</th>
						<th>총당첨금액</th>
						<th>1인당 당첨금액</th>
						<th>당첨자수</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><span class="rank rk1">1등</span></td>
						<td class="rt">--원</td>
						<td class="rt">--원</td>
						<td class="rt">--명</td>
					</tr>
					<tr>
						<td><span class="rank rk2">2등</span></td>
						<td class="rt">--원</td>
						<td class="rt">--원</td>
						<td class="rt">--명</td>
					</tr>
					<tr>
						<td><span class="rank rk3">3등</span></td>
						<td class="rt">--원</td>
						<td class="rt">--원</td>
						<td class="rt">--명</td>
					</tr>
					<tr>
						<td><span class="rank rk4">4등</span></td>
						<td class="rt">--만원</td>
						<td class="rt">--만원</td>
						<td class="rt">--명</td>
					</tr>
					<tr>
						<td><span class="rank rk5">5등</span></td>
						<td class="rt">84억 3677만원</td>
						<td class="rt">5,000원</td>
						<td class="rt">1,687,355명</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	

  <div class="main_Report">
		<h2 class="box_tit" style="    margin-bottom: 20px;">당첨현황
			<div>
			<input type="hidden" name="winResult" id="win_result_inning" value="<?=$data_config['lc_cur_inning']?>"><?=$data_config['lc_cur_inning']?>회 당첨결과&nbsp;&nbsp;
			<!-- <select class="Report" name="winResult" id="win_result_inning" onChange="getWinResult(this.value)">
				<?
					for($i=0; $i<count($inning_arr); $i++ ) { 
						if($inning_arr[$i]['inning'] <= $data_config['lc_cur_inning']) {
				?>
                    <option value="<?=$inning_arr[$i]['inning']?>" <?=($_GET['s_inning'] == $inning_arr[$i]['inning']) ? 'selected' : ''?>><?=$inning_arr[$i]['inning']?>회 당첨결과</option>
                <? 
						}
					}
				?>
			</select> -->
			</div>
		</h2>
		<div id="win_result_area" style="min-height:190px">
			<div class="report_tbl_head">
				<table class="table_st2 table_st">
					<colgroup>
						<col style="width: 60px;">
						<col style="width: 118px;">
						<col style="width: auto;">
					</colgroup>
					<thead>
						<tr>
							<th>순위</th>
							<th>당첨조합</th>
							<th class="rt">총 당첨금액</th>
						</tr>
					</thead>
				</table>
			</div>
			<!--
			<table class="table_st2 table_st">
			<colgroup>
			<col style="width: 72px;">
			<col style="width: 100px;">
			<col style="width: auto;">
			</colgroup>
			<tbody>
			<tr>
			<td><span class="rank rk1">1등</span></td>
			<td>0명</td>
			<td class="rt">원</td>
			</tr>
			<tr>
			<td><span class="rank rk2">2등</span></td>
			<td>2명</td>
			<td class="rt">1억 1167만 3840원</td>
			</tr>
			<tr>
			<td><span class="rank rk3">3등</span></td>
			<td>76명</td>
			<td class="rt">1억 1114만 2096원</td>
			</tr>
			</tbody>
			</table>-->
		</div>
		<div style="clear:both"></div>
		<!-- <iframe width="270" height="212" src="/inc_counsel2.htm" frameborder=0 scrolling=no allowtransparency="true"></iframe> -->
        <div style="padding:10px 0 0 0">
        <iframe width="270" height="212" src="/inc_counsel3.htm" frameborder=0 scrolling=no allowtransparency="true"></iframe>
        </div>
		
	</div>
</div>
<!--div class="main_middle">
<img src="/images/rnw/메인/메인_필터시스템.png?v=1" usemap="#middle">
<map name="middle">
  <area shape="rect" coords="-5,98,377,294" href="javascript:m1s2()" onfocus="blur()">
  <area shape="rect" coords="410,100,792,292" href="javascript:m1s3()" onfocus="blur()">
  <area shape="rect" coords="819,104,1201,296" href="javascript:m1s4()" onfocus="blur()">
</map>
</div-->
</div>
<!--div class="row4">
	<div class="row4Review" style="display:none">
		<div class="ReviewLt">
		<a href="javascript:m3()"><h2>실시간 당첨자 게시판</h2></a>
		<p>당첨된 회원들의 당첨인증샷을<br>공개합니다</p>
		</div>
		<div class="ReviewRt">
		<? //echo latest("slide", "photo", 6, 20);  ?>
		</div>
	</div>
	<div style="clear:both"></div>
	<div style="margin:0 auto;width:1202px"-->
	<? /* ?>
	<div class="row4Photo">
		<div class="row4PhotoWrap">
		<a href="javascript:m3s2()"><h2>당첨영수증<p><img src="/images/main_pr_more.gif"></p></h2></a>
		<div>
		<? echo latest("simple_gallery", "photo2", 4, 10);  ?>
		</div>
		</div>
	</div>
	<? */ ?>


	<!--div class="row4Board"-->
		<? //echo latest("cast", "cast", 1, 30);  ?>
	<!--/div>
	<div class="row4Board2">
		<a href="javascript:m6s1()"><h2>공지사항<p><img src="/images/main_pr_more.gif"></p></h2></a>
		<div class="row4latest"-->
		<? //echo latest("basic", "notice", 4, 20);  ?>
		<!--/div>
	</div>
	<div class="row4Board2">
		<div class="row4PhotoWrap" style="margin-left:50px">
		<a href="javascript:m3s1()"><h2>패밀리수다방<p><img src="/images/main_pr_more.gif"></p></h2></a>
		<div-->
		
		<? // echo latest("basic_no_date", "photo", 4, 30);  ?>
	
		<!--img src="/images/main_saju.jpg" usemap="#main_saju">
        <map name="main_saju">
          <area shape="rect" coords="25,124,158,156" href="/unse/today_unse.php" onfocus="">
          <area shape="rect" coords="189,123,319,154" href="/unse/lotto_unse.php" onfocus="">
        </map>
		
		</div-->
		</div>
	</div>
	</div>
</div>

<!--div class="row5">
	<div class="row5btn">
	<img src="/images/main_quick.gif" usemap="#quick">
    <map name="quick">
      <area shape="rect" coords="0,0,381,148" href="javascript:m1()" onfocus="blur()">
      <area shape="rect" coords="410,0,791,156" href="javascript:m1s2()" onfocus="blur()">
      <area shape="rect" coords="823,-5,1204,151" href="javascript:m6s2()" onfocus="blur()">
    </map>
  </div>
</div-->



</div>
</div>

<div style="position:relative;height:944px;min-width:1200px;width:100%;overflow:hidden">
        <!--div id="nav" style="left:50%;margin-left:-50px;"></div-->
                <div class="adRepeat" style="position:absolute; left:50%;margin-left:-1000px;">
                        <ul id="adRepeat" style="min-width:1000px; z-index:99">
                                <li><img src="images/rnw/메인2/메인_필터시스템2.png" style="width:2000px;min-width:1200px" usemap="#cs1">
				<map name="cs1">
					<area shape="rect" coords="915,270,1094,330" href="javascript:m1()">
				</map>
				</li>
                        </ul>
                </div>
        </div>
</div>

<div style="position:relative;height:526px;min-width:1200px;width:100%;overflow:hidden">
        <!--div id="nav" style="left:50%;margin-left:-50px;"></div-->
                <div class="adRepeat" style="position:absolute; left:50%;margin-left:-1000px;">
                        <ul id="adRepeat" style="min-width:1000px; z-index:99">
                                <li><img src="images/rnw/메인2/검증된-필터-시스템_210613.png" style="width:2000px;min-width:1200px" usemap="#cs2">
				<map name="cs2">
                                        <area shape="rect" coords="715,220,894,270" href="javascript:m1()">
                                </map>

				</li>
                        </ul>
                </div>
        </div>
</div>


<div style="position:relative;height:815px;min-width:1200px;width:100%;overflow:hidden">
        <!--div id="nav" style="left:50%;margin-left:-50px;"></div-->
                <div class="adRepeat" style="position:absolute; left:50%;margin-left:-1000px;">
                        <ul id="adRepeat" style="min-width:1000px; z-index:99">
                                <li><img src="images/rnw/메인/메인_멤버십서비스4.png" style="width:2000px;min-width:1200px" usemap="#cs3">
				<map name="cs3">
                                        <area shape="rect" coords="550,350,690,410" href="javascript:m1()">
					<area shape="rect" coords="805,670,974,720" href="javascript:m1()">
					<area shape="rect" coords="1070,350,1210,410" href="javascript:m1()">
                                </map>

				</li>
                        </ul>
                </div>
        </div>
</div>


<div style="position:relative;height:263px;min-width:1200px;width:100%;overflow:hidden">
                <div class="adRepeat" style="position:absolute; left:50%;margin-left:-1000px;">
                        <ul id="adRepeat" style="min-width:1000px; z-index:99">
                                <li><img src="images/rnw/메인2/메인_하단.png" style="width:2000px;min-width:1200px" usemap="#cs4">
				<map name="cs4">
                                        <area shape="rect" coords="445,170,645,230" href="javascript:m1()">
                                </map>

				</li>
                        </ul>
                </div>
        </div>
</div>


<script>

function getWinData(inning) {

	inning = (inning === undefined) ? '' : inning;

	$.ajax({
		url: "/service/public/lotto_common_process.php",
		type: "post",
		data: "proc=getWinData&inning="+inning,
		cache: false,
		
		dataType: 'html',
		success: function(data) {
			if(data != 'fail') {
				$('#result_area').html(data);
			} else {
				alert("데이터가 없습니다.");
			}
		}
	});
}

function getWinResult(inning) {

	inning = (inning === undefined) ? '' : inning;

	if(!inning) inning = $('#win_result_inning').val();
	$.ajax({
		url: "/service/public/lotto_common_process.php",
		type: "post",
		data: "proc=getWinResult&inning="+inning,
		cache: false,
		
		dataType: 'html',
		success: function(data) {
			if(data != 'fail') {
				//alert(inning);
				$('#win_result_area').html(data);
			} else {
				alert("데이터가 없습니다.");
			}
		}
	});
}

$(document).ready(function() {
	getWinData();
	getWinResult();
});

</script>
<script type="text/javascript" charset="UTF-8" src="//t1.daumcdn.net/adfit/static/kp.js"></script>
<script type="text/javascript">

      kakaoPixel('1521222272653701644').pageView();

</script>
<?php
include_once(G5_PATH."/tail.php");
?>
