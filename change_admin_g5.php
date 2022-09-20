<?
exit;
/*
$g4_path = "./"; // common.php 의 상대 경로
include_once('./_common.php');
include_once(G5_PATH.'/head.sub.php');

$g4['title'] = "관리자 암호 변경";
if ($_POST[db_user] && $_POST[db_pass] && $_POST[admin_id] && $_POST[new_pass]) {

    $connect_db = sql_connect($mysql_host, $_POST[db_user], $_POST[db_pass]);
    if (!$connect_db) {
		alert("DB 유저명과 DB 암호를 확인하세요.");
	}
	else {
		$row=get_member($_POST[admin_id]);
		if (!$row) alert("$_POST[admin_id] 님의 회원정보가 없습니다."); 
		$sql="update $g5[member_table] set mb_password = '".sql_password($_POST[new_pass])."' where mb_id='$_POST[admin_id]'";
		sql_query($sql);
		alert("$_POST[admin_id] 님의 암호가 변경되었습니다.");
	}
}

?>
<center><BR>
관리자 암호 변경 <BR><BR>

<form method=post>
DB유저: <input type=text name=db_user size=10><BR>
DB암호: <input type=password name=db_pass size=10><BR>
아이디: <input type=text name=admin_id size=10><BR>
새암호: <input type=password name=new_pass size=10><BR>
<input type=submit value='암호변경'>
</form>
*/