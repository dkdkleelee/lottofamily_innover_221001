<?php
include_once("./_common.php");


$cpid = "ltfamily";
$txtime = date("YmdHis");
$tx_id = microtime();

$pcode = trim($pcode);

if($pcode != null){
	$type_var = $pcode;
}else{
	$type_var = $_POST[type_val];
}


if(!$type_var) {
	die("데이터오류");
	exit;
}


$nowTime = time();

$qry_logs = "insert into cp_log_table(nowtime,tx_id,pay_type,order_code,coupon_id,m_id, content_cat,cp_id,user1_name,user1_year,user1_month,user1_day,user1_sex,user1_sol,user1_bool,";
$qry_logs .= "user2_name,user2_year,user2_month,user2_day,user2_sex,user2_sol,user2_bool,";
$qry_logs .= "target1_year,target1_month,target1_day,";
$qry_logs .= "target2_year,target2_month,target2_day,";
$qry_logs .= "option_name1,option_name2,return_url,type_var,msg1,msg2,user1_hour,user2_hour,money,txtime,luck_type) values(";
$qry_logs .= "'$nowTime','$tx_id','$_accountType','$_SESSION[_accountID]','$_SESSION[_couponID]','$_mid','$_COOKIE[contentCatID]','$cpid','$_POST[user1_name]','$_POST[user1_year]','$_POST[user1_month]','$_POST[user1_day]','$_POST[user1_sex]','$_POST[user1_sol]','$_POST[user1_blood]'";
$qry_logs .= ",'$_POST[user2_name]','$_POST[user2_year]','$_POST[user2_month]','$_POST[user2_day]','$_POST[user2_sex]','$_POST[user2_sol]','$_POST[user2_blood]',";
$qry_logs .= "'$_POST[target1_year]','$_POST[target1_month]','$_POST[target1_day]',";
$qry_logs .= "'$_POST[target2_year]','$_POST[target2_month]','$_POST[target2_day]',";
$qry_logs .= "'$_POST[option_name1]','$_POST[option_name2]','$_POST[return_url]','$type_var','$_POST[msg1]','$_POST[msg2]','$_POST[user1_hour]','$_POST[user2_hour]','$contentMoney','$txtime','$luck_type')";

//echo $qry_logs; exit();

$cpid_ok = $cpid;
$tx_id_ok = $tx_id;
$_SESSION['cpid_ok'] = $cpid_ok;
$_SESSION['tx_id_ok'] = $tx_id_ok;
$_SESSION['_accountYN'] = '';
//session_register("tx_id_ok");
//session_unregister("_accountYN");

$check = sql_query($qry_logs);
if($check == 0)
{
	unset($db);
	e_msg("db에러");
}
	//$pay_on = $r_base[0][pay_on];
echo $type_var;
switch(trim($type_var)) {
	case '1011': $url = "./unse_result.php?type=lotto"; break;
	case '1006' : $url = "./unse_result.php?type=today"; break;
}



echo "<html>
		<head>
		<script language=javascript>
		function sub() {
			document.resultForm.method=\"post\";
			document.resultForm.action = \"$url\";
			document.resultForm.submit();
		}

		</script>
	</head>

	<body onLoad=\"javascript:sub()\">
	<form name=resultForm>
	<input type=hidden name=cp_id value=\"$cpid\">
	<input type=hidden name=tx_id value=\"$tx_id\">
	<input type=hidden name=type_var value=\"$type_var\">
	<input type=hidden name=title value=\"$title_oooo\">
	<input type=hidden name=pay_on value=\"$pay_on\">
	</form>

</body>";