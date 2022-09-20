<?php

include_once("./_common.php");

use \Acesoft\Common\Utils;
use \Acesoft\Lecture\Member\User as User;

include_once("./head.php");
?>
<div class="container_popup">
	<div class="btitle" style="margin-top:0px">
        <span style="">엑셀업로드</span>
    </div>
	<div class="help_text"><i class="fa fa-file-text-o fa-lg f_blue"></i> '엑셀파일'을 다운받아 업로드 가능합니다.</div>
	<div class="help_text"><i class="fa fa-file-text-o fa-lg f_blue"></i> 다운받으신 엑셀파일에 데이터를 저장하여 업로드 해 주세요.</div>
	<!-- <div class="help_text"><i class="fa fa-file-text-o fa-lg f_blue"></i> 기존데이터 업데이트시 첫 필드(번호), 이이디필드의 데이터 수정은 하시면 안됩니다.</div> -->
	<form name="upload_form" id="upload_form" method="post" action="./lotto_member_process.php" encType="multipart/form-data">
		<input type="hidden" name="proc" value="excelNewUserUpload">
		<table align="center" width="100%" border="0" cellspacing="0" cellpadding="0" class="tb02">
		<tr>
			<th width="150">엑셀파일 다운로드</th>
			<td>
				<button type="button" class="as-btn small blue" onClick="location.href='./lotto_member_process.php?proc=downloadExcelTemplate'"><i class="fa fa-download"></i> 엑셀템플릿 다운로드</button>
			</td>
		</tr>
		<tr>
			<th width="150">회원 엑셀 업로드</th>
			<td>
				<input type="file" name="upfile" placeholder="엑셀업로드파일" required>
			</td>
		</tr>
		</table>

		<div style="width:100%;text-align:center;padding-top:30px">
			<button type="submit" class="as-btn small blue"><i class="fa fa-check"></i> 등록하기</button>
			<button type="button" class="as-btn small red" onclick="window.close();void(0);"><i class="fa fa-close"></i> 취소</button>
		</div>
	</form>
</div>
<!-- jQuery-2.1.4 -->
<script src="../js/plugins/jQuery/jQuery-2.1.4.min.js"></script>
<!-- bootstrap validator -->
<script src="../js/plugins/bootstrap-validator/validator.min.js"></script>
<script>
$(document).ready(function() {
	$('#upload_form').validator().on('submit', function (e) {
		if (e.isDefaultPrevented()) {
			// handle the invalid form...
			if(!$('#upfile').val()) {
				alert("파일을 등록해 주세요.");
			}
		} else {
			
			//oEditors.getById["detail"].exec("UPDATE_CONTENTS_FIELD", []);
		}
	})
});
</script>