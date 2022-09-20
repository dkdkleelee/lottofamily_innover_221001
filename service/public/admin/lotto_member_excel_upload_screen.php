<?php
/**
 * Created by PhpStorm.
 * User: acerunner
 * Date: 16. 2. 1.
 * Time: 오후 1:19
 */

include_once("./_common.php");
include_once("./head.php");
?>
<link rel="stylesheet" href="../css/admin.style.css" type="text/css">
<link rel="stylesheet" href="../css/custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/admin.custom.style.css" type="text/css">
<link rel="stylesheet" type="text/css" href="../js/plugins/progressbar/skins/jquery-ui-like/progressbar.css">


<!-- Progressbar -->
<script src="../js/plugins/progressbar/progressbar.js"></script>

<!-- InputMask -->
<script src="../js/plugins/inputmask/inputmask.js"></script>
<script src="../js/plugins/inputmask/inputmask.date.extensions.js"></script>
<script src="../js/plugins/inputmask/inputmask.numeric.extensions.js"></script>
<script src="../js/plugins/inputmask/jquery.inputmask.js"></script>

<!-- common js -->
<script src="../js/common.js"></script>

<style>
	#progressBar {
		margin:0px auto;
		width: 400px !important;
		height: 22px;
	}
	#progressBar div {
		height: 100%;
		color: #fff;
		text-align: right;
		font-size: 12px;
		line-height: 22px;
		width: 0;
	}
	
</style>
<div class="content_wrap">
	<form name="mailling_search_form" id="mailling_search_form" method="post" action="mail_send_selection.php" enctype="multipart/form-data" onSubmit="return checkSearchFrom(this)">
	<input type="hidden" name="id" value="<?=$_GET['id']?>">
	<div class="btitle" style="margin-top:0px">
        <div style="padding-left:10px"><i class="fa fa-folder-o"></i> 회원엑셀 등록/업데이트중...(새로고침 금지)</div>
    </div>
	<div style="padding:10%;padding-top:70px;">
		<div class="jquery-ui-like" style="width:80%" id="progressBar"><div></div></div>
	</div>
	<div id="content" style="width:97%;border:1px solid #e8e8e8;height:120px;overflow-y:auto;padding:10px">
	</div>

	<div style="width:100%;text-align:center;padding-top:30px;display:none" id="ok_close">
		<div style="padding-left:10px"><i class="fa fa-check-circle"></i> 처리완료</div>
		<button class="as-btn small blue" onclick="window.close();void(0);"><i class="fa fa-check"></i> 확인</button>
	</div>
	
</div>

<script>

progressBar(0, $('#progressBar'));

</script>