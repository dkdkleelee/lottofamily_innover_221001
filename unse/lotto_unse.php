<?php
include_once("./_common.php");

$cur = 4;
include_once("../head_04.php");
include_once("./include/unse_funtion.php");
include_once("./include/form_list.php");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\TermService;

//▶ pagination url
$param1 = Utils::getParameters(array('page','s_ca_no'))."&s_ca_no[]=".@array_pop(array_filter($_GET['s_ca_no']));
$list_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$param1;



if($_SESSION['ss_mb_id'] ) {
	// 이용중서비스
	$termService = new TermService();
	$_GET['s_mb_id'] = $_SESSION['ss_mb_id'];
	$data = $termService->getTermServiceUseList();
	if(!$data['list'][0]['leftDays'] && $member['mb_level'] < 10) {
		alert("챌린저회원 이상만 이용 가능합니다.\\n정회원 신청 후 이용해 주세요.","/service/public/service_buy.php?m=4");
		exit;
	}
} else {
	alert("로그인 후 이용해 주세요.","/bbs/login.php");
}

?>


<link rel="stylesheet" href="../service/public/css/custom.style.css" type="text/css">
<link rel="stylesheet" href="./css/lotto.css" type="text/css">
<link rel="stylesheet" href="./css/paginate.css" type="text/css">
<!-- <link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css" rel="stylesheet" /> -->
<script src="./js/jquery-ui-1.8.7.custom.min.js" ></script>
<script src="./js/jquery.ui.datepicker-ko.js"  charset="utf-8"></script>
<link rel="stylesheet" href="./js/datetimepicker/jquery.datetimepicker.css" type="text/css" />
<script type="text/javascript" src="./js/datetimepicker/jquery.datetimepicker.js" charset="utf-8"></script>
<style>
.dataTable td {
   
    text-align: left;
    background-color: #ffffff;
    padding-left: 10px !important;
}
</style>
<div class="info_container">
	<div class="btitle">
		<div class="btitle_top"></div>
		<div class="btitle_text">로또운세</div>
		<div class="btitle_locate">&gt; 패밀리 분석실 &gt; 로또운세</div>
		<div class="btitle_line"></div>
	</div>
	<div class="content_wrap">

		<div class="unse-title">
			<div class="box-left"><img src="/images/btn_main03.gif"></div>

			<div class="box-right">
			사주로 풀어보는 나의 로또운세!!<br />
			인생역전을 꿈꾸는 당신을 위해 나의 사주에 따른 당첨 행을을 점쳐봅니다.<br />
			오늘도 대박나세요!!
			</div>

		</div>

		<form name="luck"  method=post action="./calc_result.php">
		<input type=hidden name=type_val value=1011>
		
		<table width="100%" border="0" cellpadding="3" cellspacing="1" class="dataTable">
		<tr> 
		  <th width="83" align="center"><strong><font color="#666666">이름</font></strong></th>
		  <td width="108" bgcolor="#FFFFFF"><input type="text" name="user1_name" value="<?=$_name?>" size="10" class="frm_input" required itemname="이름" hangul minlength="2"></td>
		  <th width="63" align="center"><strong><font color="#666666">성별</font></strong></th>
		  <td width="127" bgcolor="#FFFFFF"> 
			<? user_sex("1",$user_member[sex]);?>
		  </td>
		</tr>
		 <tr> 
		  <th rowspan="2" align="center"><strong><font color="#666666">생년월일</font></strong></th>
		  <td colspan="3" bgcolor="#FFFFFF"> 
			<? user_year("1",$user_member[year]);?>
			년 &nbsp;&nbsp; 
			<? user_month("1",$user_member[month]);?>
			월 &nbsp;&nbsp; 
			<? user_day("1",$user_member[day]);?>
			일 &nbsp;&nbsp; 
			<? user_hour("1",$user_member[hour]);?>
			시</td>
		</tr>
		<tr> 
		  <td colspan="3" bgcolor="#FFFFFF"> 
			<? user_cal("1","1",$user_member[BirthOption]);?>
			양 력&nbsp;&nbsp; 
			<? user_cal("1","2",$user_member[BirthOption]);?>
			음 력 /                                            
			<? user_cal2("1","1",$user_member[Yoon]);?>
			평 달&nbsp;&nbsp; 
			<? user_cal2("1","2",$user_member[Yoon]);?>
			윤 달</td>
		</tr>
		<tr> 
		  <th align="center"><strong><font color="#666666">운세날짜 
			</font></strong> </th>
		  <td colspan="3" bgcolor="#FFFFFF">
			<?=date('Y')?>년 <?=date('m')?>월 <?=date('d')?>일
			<input type="hidden" name="target1_year" value="<?=date('Y')?>">
			<input type="hidden" name="target1_month" value="<?=date('m')?>">
			<input type="hidden" name="target1_day" value="<?=date('d')?>">
			<? /*?>
			<? target_year("1",date(Y),"2043",date(y));?>
			년
			<? target_month("1",date(m));?>
			월
			<? target_day("1",date(d));?>
			일
			<? */ ?>
		  </td>
		</tr>
	  </table>
				
	 <div style="padding:10px;text-align:center">
		<!---확인버튼-------->
		<button type="submit" class="as-btn medium blue lt-btn""> 결과보기</button>
	</div>
	</form>
	</div>
</div>
<?php
include_once("../tail.php");
?>