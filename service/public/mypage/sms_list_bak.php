<?php
include_once("./_common.php");

$cur = 1;
include_once("../../../head_05.php");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Lotto;
use Acesoft\LottoApp\TermService;
use Acesoft\Common\Message;

//▶ pagination url
$param1 = Utils::getParameters(array('page'));
$list_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$param1;


$lottoServiceConfig = new LottoServiceConfig();
$serviceConfig = $lottoServiceConfig->getConfig();

//▶ 등급별 발급설정갯수
$extract_count_per_grade = unserialize($serviceConfig['lc_extract_count']);

// term service
$termService = new TermService();


$mb = get_member($_SESSION['ss_mb_id']);
$send_weekdays = explode(",", $_SESSION['ss_mb_id']);

// 이용중서비스
$_GET['s_mb_id'] = $_SESSION['ss_mb_id'];
$data = $termService->getTermServiceUseList();

// 최근 SMS목록
//▶ get list data
$message = new Message();
$data = $message->getMessageList($_GET['page'], $list_url);
$data_sms = $data['list'];


?>

<link rel="stylesheet" href="../css/custom.style.css" type="text/css">
<!-- <link rel="stylesheet" href="../css/admin.custom.style.css" type="text/css"> -->
<link rel="stylesheet" href="../css/lotto.css" type="text/css">
<link rel="stylesheet" href="../css/paginate.css" type="text/css">
<!-- <link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css" rel="stylesheet" /> -->
<script src="../js/jquery-ui-1.8.7.custom.min.js" ></script>
<script src="../js/jquery.ui.datepicker-ko.js"  charset="utf-8"></script>
<link rel="stylesheet" href="../js/datetimepicker/jquery.datetimepicker.css" type="text/css" />
<script type="text/javascript" src="../js/datetimepicker/jquery.datetimepicker.js" charset="utf-8"></script>

<div class="info_container">
	<div class="btitle">
		<div class="btitle_top"></div>
		<div class="btitle_text">SMS발급내역</div>
		<div class="btitle_locate">&gt; 마이페이지 &gt; SMS발급내역</div>
		<div class="btitle_line"></div>
	</div>
	<div class="content_wrap">
		<div>
			 <h5 class="title"><i class="fa fa-asterisk fa-lg"></i> 최근 SMS발송현황</h5>
		</div>
		<table width="100%" border="0" cellpadding="0" class="tb-mypage">
		<tr>
			<th width="120px">전송일시</th>
			<th width="">내용</th>
			<th width="50px">결과</th>
		</tr>
		<?	foreach((array)$data_sms as $row) { ?>
			<tr>
			<td>
				<?php echo date('Y-m-d H:i', strtotime($row['sent_at']))?>
			</td>
			<td style="text-align:left;padding:3px;">
				<span title="<?php echo $row['message']?>"><?php echo $row['message']?></span>
			</td>
			<td class="td_boolean" title="<?php echo $row['sent_at']?>" style="text-align:center"><?php echo $row['sent_at'] ? '성공' : '실패'?></td>
		</tr>
		</form>
		<? }?>
		
		</table>
		<div class="paginate wrapper paging_box">
			<?=$data['link']?>	
		</div>
	</div>
</div>


<?php
include_once("../../../tail.php");
?>