<?php
define('_INDEX_', true);
include_once('./_common.php');

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
?>
    <?php
    if(defined('_INDEX_')) { // index에서만 실행
        include G5_BBS_PATH.'/newwin.inc.php'; // 팝업레이어
    }
    ?>
<? include "inc_top.php" ?>

<script language="Javascript" src="jquery.cycle.all.latest.js"></script>
<div style="width:100%;min-width:1200px;">
<style>
body { padding:0 !important; margin:0 !important; }
#adRepeat { width:100%;min-width:1000px;text-align:center;margin:0;padding:0}
#nav { margin:0px;height:20px;overflow:hidden;position:absolute;left:757px;top:0px;z-index:100;}
#nav div { width:20px;height:20px;float:left;margin:0 0 0 8px;background-image:url('/images/main_visual_off.png');cursor:pointer;}
#nav div.activeSlide { width:20px;height:20px;background-image:url('/images/main_visual_on.png');cursor:pointer }
#nav a:focus { outline: none; }
</style>
<script type="text/javascript">
<!--
$(document).ready(function() {
	
	$('#adRepeat').cycle({
		fx:         'fade',
		timeout:     2700,
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
<div style="position:relative;height:380px;min-width:1200px;width:100%;overflow:hidden">
	<div id="nav" style="left:50%;margin-left:-50px;"></div>
		<div class="adRepeat" style="position:absolute; left:50%;margin-left:-1000px;">
			<ul id="adRepeat" style="min-width:1000px; z-index:99">
				<li><img src="images/main_visual_01.jpg" style="width:2000px;min-width:1200px"></li>
				<li><img src="images/main_visual_02.jpg" style="width:2000px;min-width:1200px"></li>
				<li><img src="images/main_visual_03.jpg" style="width:2000px;min-width:1200px"></li>
			</ul>
		</div>
	</div>
</div>
		<div class="viRt">
			<div class="mainlogin">
				<!-- <?=outlogin("basic2");?> -->
			</div>
			<div class="lottoNum">
				<div>
					<p>1등 당첨 배출</p>
					<span>1등 당첨 배출</span>
				</div>
				<div>
					<p>1등 당첨 배출</p>
					<span>1등 당첨 배출</span>
				</div>
			</div>
		</div>

<div class="main_lotto">
	<div class="box_st1 main_lottoResult">
		<div class="title_area">
			<div class="th_arr">
				<a class="th_prev" href="/?search[where][num]=820" title="이전회차"><img src="/images/main_lotto_arr_l.gif"></a>
				<a class="th_next" href="/?search[where][num]=" title="다음회차"><img src="/images/main_lotto_arr_r.gif"></a>
			</div>
			<div class="th_select" cs="">
				<span class="num" csbtn="">821<b>회</b></span>
			</div>
			<h2 class="box_tit">당첨번호</h2>
			<div class="data">2018.08.25 추첨</div>
		</div>
		<div class="ball_box">

		</div>
		<div class="result_tbl">
			<table class="table_st1 table_st">
				<colgroup>
					<col style="width: 80px;">
					<col style="width: 200px;">
					<col style="width: 200px;">
					<col style="width: 135px;">
					<col style="width: auto;">
				</colgroup>
				<thead>
					<tr>
						<th>순위</th>
						<th>총당첨금액</th>
						<th>1인당 당첨금액</th>
						<th>당첨자수</th>
						<th>당첨기준</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><span class="rank rk1">1등</span></td>
						<td class="rt">184억 2618만 3384원</td>
						<td class="rt">13억 1615만 5956원</td>
						<td class="rt">14명</td>
						<td class="rt">당첨번호 6개 숫자일치</td>
					</tr>
					<tr>
						<td><span class="rank rk2">2등</span></td>
						<td class="rt">30억 7103만 600원</td>
						<td class="rt"> 5583만 6920원</td>
						<td class="rt">55명</td>
						<td class="rt">당첨번호 5개, +보너스 숫자일치</td>
					</tr>
					<tr>
						<td><span class="rank rk3">3등</span></td>
						<td class="rt">30억 7103만 1600원</td>
						<td class="rt"> 146만 2396원</td>
						<td class="rt">2,100명</td>
						<td class="rt">당첨번호 5개 숫자일치</td>
					</tr>
					<tr>
						<td><span class="rank rk4">4등</span></td>
						<td class="rt">50억 5410만원</td>
						<td class="rt"> 5만원</td>
						<td class="rt">101,082명</td>
						<td class="rt">당첨번호 4개 숫자일치</td>
					</tr>
					<tr>
						<td><span class="rank rk5">5등</span></td>
						<td class="rt">84억 3677만원</td>
						<td class="rt">5,000원</td>
						<td class="rt">1,687,355명</td>
						<td class="rt">당첨번호 3개 숫자일치</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	

	<div class="box_st1 main_hiReport">
		<h2 class="box_tit">당첨현황</h2>
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
						<th>당첨자</th>
						<th class="rt">총 당첨금액</th>
					</tr>
				</thead>
			</table>
		</div>

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
		</table>
		<div class="main_counsel">
			<div class="counselLt">
				<img src="/images/main_counsel_ico.gif">
				<h2>상담<br>예약</h2>
			</div>
			<div class="counselRt">
					<select class="phone1" name="phone1" id="phone1">
						<option value="010">010</option>
						<option value="011">011</option>
						<option value="016">016</option>
						<option value="017">017</option>
						<option value="018">018</option>
						<option value="019">019</option>
					</select>
					-
					<input class="phone2" type="text" maxlength="4" id="phone2" name="phone2">
					-
					<input class="phone3" type="text" maxlength="4" id="phone3" name="phone3">
					<p class="privacy"><input type="checkbox" id="privacy"> 개인정보정책 동의</p>
					<div class="counselBtn">
					<a href="#" onclick="advice_submit()">상담예약하기</a>
					</div>
			</div>
		</div>
	</div>
</div>
<div class="main_middle">
<img src="/images/main_middle_banner.gif">
</div>
<div class="row4">
	<div class="row4Review">
		<div class="ReviewLt">
		<a href="javascript:m5()"><img src="/images/main_review.png"></a>
		</div>
		<div class="ReviewRt">
		<? echo latest("slide", "review", 6, 20);  ?>
		</div>
	</div>
	<div style="clear:both"></div>
	<div style="margin:0 auto;width:1202px">
	<div class="row4Photo">
		<div class="row4PhotoWrap">
		<a href="javascript:m5s2()"><h2>사진인증 <span>수익인증</span><p><img src="/images/main_pr_more.gif"></p></h2></a>
		<div>
		<? echo latest("simple_gallery", "review2", 4, 10);  ?>
		</div>
		</div>
	</div>
	<div class="row4Board">
		<a href="javascript:m2s5()"><h2>투자전략<p><img src="/images/main_pr_more.gif"></p></h2></a>
		<div class="row4latest">
		<? echo latest("basic", "cast", 4, 20);  ?>
		</div>
	</div>
	<div class="row4Board2">
		<a href="javascript:m2s5()"><h2>무료추천주<p><img src="/images/main_pr_more.gif"></p></h2></a>
		<div class="row4latest">
		<? echo latest("basic", "notice", 4, 20);  ?>
		</div>
	</div>
	</div>
</div>
<div class="row5">
	<div class="row5btn">
	<img src="/images/main_quick.gif" usemap="#quick">
    <map name="quick">
      <area shape="rect" coords="-1,-3,410,154" href="javascript:m3s1()" onfocus="blur()">
      <area shape="rect" coords="439,1,814,153" href="javascript:m4s2()" onfocus="blur()">
      <area shape="rect" coords="-1,178,409,332" href="javascript:m1s1()" onfocus="blur()">
      <area shape="rect" coords="437,180,810,330" href="javascript:m6s2()" onfocus="blur()">
    </map>
  </div>
	<div class="row5info">
	<img src="/images/main_info.gif">
	</div>
</div>

<?php
include_once(G5_PATH."/tail.php");
?>
