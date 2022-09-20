<?php
include_once("./_common.php");

$cur = 4;
include_once("../head_04.php");
include_once("./include/unse_funtion.php");

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

if($_SESSION[play_info] || $_SESSION[data_unse_txt_ok]) {
	$_SESSION['play_info'] = '';
	$_SESSION['data_unse_txt_ok'] = '';
}

$qry_log = "select * from cp_log_table where cp_id = '$_SESSION[cpid_ok]' && tx_id = '$_SESSION[tx_id_ok]'";
//echo $qry_log; exit();
$r_base = sql_fetch($qry_log, true);


$type_var = type_val($r_base[type_var]);

if(!$r_base)
{
	e_msg("db에러 입니다. 잠시후 다시 이용 바랍니다.");
}

$user1_name = $r_base[user1_name];
$user1_year = $r_base[user1_year];
$user1_month = $r_base[user1_month];
$user1_day = $r_base[user1_day];
$user1_sex =$r_base[user1_sex];
$user1_sol = $r_base[user1_sol];
$user1_bool =$r_base[user1_bool];
$user2_name =$r_base[user2_name];
$user2_year =$r_base[user2_year];
$user2_month=$r_base[user2_month];
$user2_day=$r_base[user2_day];
$user2_sex=$r_base[user2_sex];
$user2_sol = $r_base[user2_sol];
$user2_bool=$r_base[user2_bool];
$target1_year=$r_base[target1_year];
$target1_month=$r_base[target1_month];
$target1_day=$r_base[target1_day];
$target2_year=$r_base[target2_year];
$target2_month=$r_base[target2_month];
$target2_day=$r_base[target2_day];
$option_name1=$r_base[option_name1];
$option_name2=$r_base[option_name2];
$user1_hour = $r_base[user1_hour];
$user2_hour = $r_base[user2_hour];
$option_name1 = $r_base[luck_type];

$user1_blood = $user1_bool;
$user2_blood = $user2_bool;



if($r_base[type_var] == "1006") //오늘의 운세
{

	$title = "오늘의 운세";
	$ment_url = "../data/ment/";
	$data_url = "../data/today_unse/wma/";
	$data_txt_url = "./data/today_unse/htm/";
	$number  = unse_nolmal($user1_year,$user1_month,$user1_day,$user1_hour,$user1_sol,$user1_sex,$target1_year,$target1_month,$target1_day);
	$ment_1 = $ment_url."to-ment1.wma";
	$ment_2 = $ment_url."to-ment2.wma";
	$data_ok = $data_url."to".$number.".wma";
	$target_year1 = substr($target1_year,2,2);
	$sub_date= $target_year1.$target1_month.$target1_day;
	//action("name",$user1_name);
	//action("ment",$ment_1);
	//action("date_ok",$sub_date);
	//action("ment",$ment_2);
	action("content",$data_ok);
	$play_info = ramdum($total_check_ok);
	$data_unse_txt_ok = $data_txt_url."to".$number.".htm";

	$_SESSION['play_info'] = $play_info;
	$_SESSION['data_unse_txt_ok'] = $data_unse_txt_ok;
}
else if($r_base[type_var] == "1011") //로또 운세
{
	$title = "로또 운세";
	$ment_url = "./data/ment/";
	$data_url = "./data/lotto/wma/";
	$data_txt_url = "./data/lotto/htm/";
	$target1_year =date(Y);
	$target1_month = date(m);
	$number  = lotto_unse($user1_year,$user1_month,$user1_day,$user1_hour,$user1_sol,$user1_sex,$target1_year,$target1_month);
	$ment_1 = $ment_url."lotto-ment.wma";
	$data_ok = $data_url."lotto".$number.".wma";
	//action("name",$user1_name);
	//action("ment",$ment_1);
	action("content",$data_ok);
	$play_info = ramdum($total_check_ok);
	$data_unse_txt_ok = $data_txt_url."lotto".$number.".htm";
	$_SESSION['play_info'] = $play_info;
	$_SESSION['data_unse_txt_ok'] = $data_unse_txt_ok;
}
?>


<link rel="stylesheet" href="../service/css/custom.style.css" type="text/css">
<link rel="stylesheet" href="./css/lotto.css" type="text/css">
<link rel="stylesheet" href="./css/paginate.css" type="text/css">
<!-- <link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css" rel="stylesheet" /> -->
<script src="./js/unse_common.js" ></script>
<script src="./js/jquery-ui-1.8.7.custom.min.js" ></script>
<script src="./js/jquery.ui.datepicker-ko.js"  charset="utf-8"></script>
<link rel="stylesheet" href="./js/datetimepicker/jquery.datetimepicker.css" type="text/css" />
<script type="text/javascript" src="./js/datetimepicker/jquery.datetimepicker.js" charset="utf-8"></script>

<div class="info_container">
	<div class="btitle">
		<div class="btitle_top"></div>
		<div class="btitle_text"><?=$title?>결과</div>
		<div class="btitle_locate">&gt; 패밀리 분석실 &gt; <?=$title?></div>
		<div class="btitle_line"></div>
	</div>
	<div class="content_wrap">
	<? if($r_base[type_var] == "1011") { ?>
		<div class="unse-title">
			<div class="box-left"><img src="/images/btn_main03.gif"></div>

			<div class="box-right">
			사주로 풀어보는 나의 로또운세!!<br />
			인생역전을 꿈꾸는 당신을 위해 나의 사주에 따른 당첨 행을을 점쳐봅니다.<br />
			오늘도 대박나세요!!
			</div>
		</div>
	<? } else if($r_base[type_var] == "1006") { ?>
		<div class="unse-title">
			<div class="box-left"><img src="/images/btn_main06.gif"></div>

			<div class="box-right">
			두근두근 오늘은 어떤일이 일어날까?<br />
			불운은 피하고! 행운은 잡고!<br />
			오늘하루도 지혜롭게 행복한 하루를 만들어보세요.<br />
			</div>
		</div>
	<? } ?>
		<? include($data_unse_txt_ok)?>


	</div>
</div>
<?php
include_once("../tail.php");
?>