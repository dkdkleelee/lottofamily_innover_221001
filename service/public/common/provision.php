<?
include_once('./_common.php');


use Acesoft\Common\Utils;
use Acesoft\LottoApp\TermService;
use Acesoft\LottoApp\Member\User;


// termservice
$termService = new TermService();

// 서비스 환경설정
$cfg = $termService->getServiceConfig();
?>
<html>
<head>
<title>이용약관</title>
<meta name="viewport" content="width=device-width, initial-scale=0.7, maximum-scale=1" />
<link type="text/css" href="../css/custom.style.css" rel="stylesheet" />
<script language="Javascript" src="/template.js"></script>
<script language="Javascript" src="/url.js"></script>
<LINK REL="StyleSheet" HREF="/template.css" type="text/css">
</head>
<body>
<table border="0" cellspacing="0" cellpadding="0" width="700">
<tr>
<td><a href="javascript:window.close()"><img src="/images/popup_service.gif" width="700" height="80" /></a></td>
</tr>
<tr>
<td background="/images/popup_bg.gif">
    <table width="95%" cellpadding="4" cellspacing="0" bgcolor=#EEEEEE align=center>
        <tr> 
            <td align="center" valign="top"><textarea style="width: 98%;height:460px" rows=32 readonly class=ed><?=($cfg['tdc_provision'])?></textarea></td>
        </tr>
    </table>
	<form name="agreeForm" method="post" action="./common_process.php">
	<input type="hidden" name="proc" value="agreeProvision">
	<input type="hidden" name="sn" value="<?=$_GET['sn']?>">
	<div style="width:100%;text-align:center;padding-top:30px">
		<button type="button" class="as-btn small red" onclick="window.close()"><i class="fa fa-close"></i> 동의하지 않음</button>
		<button type="submit" class="as-btn small blue"><i class="fa fa-check"></i> 동의함</button>
	</div>
	</form>
</td>
</tr>
<tr>
<td><img src="/images/popup_bottom.gif" /></td>
</tr>
</table>
</body>
</html>