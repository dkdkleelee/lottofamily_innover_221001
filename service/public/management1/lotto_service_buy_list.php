<?
define('G5_IS_MANAGER', true);
$sub_menu = "200200";

include_once("./_common.php");

$g5['title'] =  '<i class="fa fa-won"></i> 승인리스트';
include_once(G5_MANAGER1_PATH."/admin.head.php");

use \Acesoft\Common\Utils;
use \Acesoft\LottoApp\Member\User;
use \Acesoft\LottoApp\TermService;

// 소속팀정보만
$_GET['s_mg_no'] = $member['mg_no'];

//▶ pagination url
$param1 = Utils::getParameters(array('page','s_ca_no'))."&s_ca_no[]=".@array_pop(array_filter($_GET['s_ca_no']));
$list_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$param1;


// term service
$termService = new TermService();


//▶ get list data
$data = $termService->getTermServiceBuyList($_GET['page'], $list_url);

// 회원정보
$member_arr = $termService->db->arrayBuilder()->get($termService->tb['Member'], null, '*');

// 서비스
$data_service = $termService->getServiceList();

$cfg = $termService->getServiceConfig();
$accounts = explode("\r\n", $cfg['tdc_accounts']);

// TM목록
$user = new User();
$tm_list = $user->getTMList();
for($i=0; $i<count($tm_list); $i++) {
	$tm_arr[$tm_list[$i]['mb_id']] = $tm_list[$i]['mb_name'];
}


?>

<link rel="stylesheet" href="../css/custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/admin.custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/paginate.css" type="text/css">
<link rel="stylesheet" href="../js/datetimepicker/jquery.datetimepicker.css" type="text/css" />
<script type="text/javascript" src="../js/datetimepicker/jquery.datetimepicker.js" charset="utf-8"></script>
<div class="info_container">

<div class="content_wrap">
	
	<div class="search_box ar">
		<form name="searchForm" method="get" action="?">
			<input type="hidden" name="proc">
			<label>기간조회 : <input type="text" name="s_date" class="frm_input datetimepicker" value="<?=$_GET['s_date']?>" autocomplete="off"> ~ <input type="text" name="e_date" class="frm_input datetimepicker" value="<?=$_GET['e_date']?>" autocomplete="off"></label>

<!-- 담당TM조회 -->
<select name="s_mb_charger_tm" class="frm_input">
                                        <option value="">담당TM조회</option>
                                                <option value="na" <?=$_GET['s_mb_charger_tm'] == 'na' ? 'selected' : ''?>>미지정</option>
                                        <?=($_GET['s_pay_status']=='') ? 'selected' : '';?>
                                        <? for($i=0; $i<count($tm_list); $i++) { ?>
                                                <option value="<?=$tm_list[$i]['mb_id']?>" <?=$tm_list[$i]['mb_id'] == $_GET['s_mb_charger_tm'] ? 'selected' : ''?>><?=$tm_list[$i]['mb_id']?>(<?=$tm_list[$i]['mb_name']?>)</option>
                                        <? }?>
                                </select>

			<select name="sc" class="frm_input">
				<option value="b.mb_nick" <?=($_GET['sc']=='b.mb_nick') ? 'selected' : '';?>>닉네임</option>
				<option value="a.mb_id" <?=($_GET['sc']=='a.mb_id') ? 'selected' : '';?>>신청자 아이디</option>
			</select>
			<input type="text" name="sv" class="frm_input" value="<?=$_GET['sv']?>">
			<button class="as-btn medium white" onclick="document.searchForm.submit();void(0);"><i class="fa fa-search"></i> 검색</button>
		</form>
	</div>
	<div>
		 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 유료서비스 신청 내역입니다. 입금확인으로 변경하시면 해당 서비스가 회원에게 적용됩니다.</span>
	</div>
	<!-- <div>
		<div class="search_box ar">
			<button type="button" class="as-btn small blue" onclick="excel_download();"><i class="fa fa-download"></i> 검색 엑셀다운로드</button>
		</div>
	</div> -->
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr>
		<th width="40px">번호</th>
		<th width="130px">신청자(아이디)</th>
		<th width="130px">담당자(아이디)</th>
		<!-- <th width="70px">소속팀</th> -->
		<th width="100px">신청일</th>
		<th width="100px">결제일</th>
		<th>서비스</th>
		<th width="180px">카드정보</th>
		<th width="120px">휴대폰</th>
		<th width="60px">신청기간</th>
		<th width="150px">전체금액</th>
		<th width="150px">결제금액</th>
		<!-- <th width="150px">미수금</th> -->
		<th width="100px">결제방법</th>
		<th width="100px">입금자명</th>
		<th width="100px">상태</th>
		<? if ($is_admin) { ?> <th width="100px">변경</th> <? } ?>
	</tr>
	<?
	foreach($data['list'] as $row) {
    	$sg_limit = $termService->service_grade_limit[$row['sg_no']];
    	$detail = unserialize($row['sb_detail']);
	?>
	<tr>
		<td><?=$data['idx']--?></td>
		<td><a href="javascript:manageService('<?=$row['mb_id']?>');void(0);"><?=$row['sb_buyer_name']?>(<?=($row['mb_id']) ? $row['mb_id'] : '<span style="color:red;font-weight:bold">비회원</span>'?>)</a></td>
		<td><?=$tm_arr[$row['sb_tm_id']]?>(<?=$row['sb_tm_id']?>)</td>
		<!-- <td>
			<?=$row['mg_name'] ? '<span style="color:#339966">'.$row['mg_name'].'</span>' : '<span style="color:#ff9933">미지정</span>'?></span>
		</td> -->
		<td><?=$row['sb_regdate']?></td>
		<td><?=$row['sb_paydate']?></td>
		<td style="text-align:left;padding-left:5px">
    		<?=$row['sb_name']?>
		</td>
		<td >
			<? if($row['sb_pay_method'] == 'agency') { ?>
    		<?=$row['sb_ay_cardno']?> <br />[만료일(월/년) : <?=$row['sb_ay_expmon']?> / <?=$row['sb_ay_expyear']?> ]
			<? } else { ?>
			--
			<? } ?>
		</td>
		<td >
    		<?=$row['sb_buyer_hp']?>
		</td>
		<td >
    		<?=$row['sb_term']?><?=$row['sb_term_type'] == 'month' ? '개월' : '일'?>
		</td>
        <td><?=number_format($row['sb_total_price'])?>원<? if($row['sb_pay_method'] == 'agency') { ?> <!-- (할부: <?=$row['sb_ay_installment']?>) --> <? } ?></td>
		<td><?=number_format($row['sb_price'])?>원</td>
		<!-- <td><?=number_format($row['sb_not_paid_price'])?>원</td> -->
		<td >
    		<?=$termService->config['pay_method'][$row['sb_pay_method']]?>
		</td>
		<td >
    		<?=$row['sb_pay_name']?>
		</td>
		<td>
    		<? if($row['sb_pay_status'] == 'N') { ?>
    		<!-- <form name="service_buy_<?=$row['sb_no']?>" method="post" action="./term_service_buy_process.php">
        		<input type="hidden" name="proc" value="changeBuyStatus">
        		<input type="hidden" name="sb_no" value="<?=$row['sb_no']?>">
				<input type="hidden" name="sb_pay_method" value="<?=$row['sb_pay_method']?>">
        		<select name="sb_pay_status" class="frm_input" onChange="document.service_buy_<?=$row['sb_no']?>.submit()">
            		<option value="N" <?=$row['sb_pay_status'] == 'N' ? 'selected' : ''?>>결제대기</option>
            		<option value="Y" <?=$row['sb_pay_status'] == 'Y' ? 'selected' : ''?>>결제완료</option>
					<option value="N" <?=$row['sb_pay_status'] == 'F' ? 'selected' : ''?>>결제실패</option>
        		</select>
    		</form> -->
			<? } else if($row['sb_pay_status'] == 'F') { ?>
			<span style="color:red">결제실패</span>
			<? } else if($row['sb_pay_status'] == 'C') { ?>
			<span style="color:red" title="<?=$row['sb_canceldate']?>">결제취소</span>
    		<? } else { ?>
			<span style="color:blue">결제완료</span>
			<? } ?>
		</td>
		
		 <? if ($is_admin) { ?>
		<td>
			<button type="button" class="as-btn small red" onclick="deleteData('<?=$row['sb_no']?>')"><i class="fa fa-close"></i> 삭제</button>
			
		</td> <? } ?>
	</tr>
	<? }?>
	</table>
	<p />

	<div class="paginate wrapper paging_box">
			<?=$data['link']?>	
	</div>
</div>
</div>
<form name="deleteForm" method="post" action="./term_service_buy_process.php?<?=$param?>">
<input type="hidden" name="proc" value="deleteServiceBuy">
<input type="hidden" name="no" value="">
</form>
<script type="text/javascript">
<!--

$(document).ready(function() {
	
    $('.datetimepicker').datetimepicker({
					format:'Y-m-d',
					changeMonth: true,
					changeYear: true,
					timepicker:false,
					showButtonPanel: true,
					showMonthAfterYear: true,

					yearRange: 'c-10:c+10',
					minDate: '1970/01/02',
					lang: 'kr',
					onChange: function(date) {
						
					}
				});


});

function addServiceBuy() {
	var f = document.addServiceForm;
	var type = f.pay_method.value;

	if (f.onsubmit && !f.onsubmit()) {
		return false;
	}

	if(type == 'agency') {
		if(!validatecardnumber(f.cardno.value)) {
			alert("유효한 신용카드 번호가 아닙니다.");
			return false;
		}
		f.submit();
	} else {
		if(!f.payer_name.value) {
			alert("입금자명을 입력해주세요.");
			f.payer_name.focus();
			return false;
		}
		f.submit();

	}
}

function memoWin(no) {
	window.open("./ma_request_memo.php?type=ap_no&idx="+no, '', 'width=550, height=600');
}
function deleteData(no) {

	var f = document.deleteForm;
	if(confirm("데이터를 삭제합니다.\n삭제하신데이터는 복구가 불가능합니다.\n계속하시겠습니까?")) {
		f.no.value = no;
		f.submit();
	}

}



function num_check(evt){
	var code = evt.which?evt.which:event.keyCode;
	if(code < 48 || code > 57){
		return false;
	}
}

//Extra Validation with the Luhn Algorithm
function luhn(cardnumber) {
	//카드 번호 숫자들로 배열 생성
   
	/*
	var getdigits = /\d/g;
	var digits = [];
	while(match = getdigits.exec(cardnumber)){
		digits.push(parseInt(match[0], 10));
	}
	*/
	var digits = cardnumber.split('');
	for (var i = 0; i < digits.length; i++) {
		digits[i] = parseInt(digits[i], 10);
	}  

	//그 배열을 대상으로 룬 알고리즘 실행
	var sum = 0;
	var alt = false;
	for (var i = digits.length - 1; i >= 0 ; i-- ) {
		if (alt) {
			digits[i] *= 2;
			if(digits[i] > 9) {
				digits[i] -= 9;
			}
		}
		sum += digits[i];
		alt = !alt;
	}
	//결국 카드 번호는 잘못 되었음이 밝혀짐
	if(sum % 10 == 0) {
		return true; //document.getElementById("notice").innerHTML += '; 룬 검사 성공';
	} else {
		return false; //document.getElementById("notice").innerHTML += '; 룬 검사 실패';
	}

}

function validatecardnumber(cardnumber) {
   
	//빈칸과 대시 제거
	cardnumber = cardnumber.replace(/[ -]/g,'');

	//카드 번호가 유효한지 검사
	//정규식이 캡처 그룹들 중 하나에 들어있는 숫자를 캡처
	var match = /^(?:(94[0-9]{14})|(4[0-9]{12}(?:[0-9]{3})?)|(5[1-5][0-9]{14})|(6(?:011|5[0-9]{2})[0-9]{12})|(3[47][0-9]{13})|(3(?:0[0-5]|[68][0-9])[0-9]{11})|((?:2131|1800|35[0-9]{3})[0-9]{11}))$/.exec(cardnumber);
   
	if(match) {

		//정규식 캡처 그룹과 같은 순서로 카드 종류 나열
		var types = ['BC', 'Visa', 'MasterCard', 'Discover', 'American Express', 'Diners Club', 'JCB'];

		//일치되는 캡처 그룹 검색
		//일치부 배열의 0번째 요소 (전체 일치부중 첫 일치부)를 건너뜀
		for(var i = 1; i < match.length; i++) {
			if(match[i]) {
				//해당 그룹에 대한 카드 종류를 표시
				document.getElementById('notice').innerHTML = types[i-1];
				break;
			}
		}

		if(luhn(cardnumber)) {
			return true;
		} else {
			return false;
		}

	} else {
		document.getElementById('notice').innerHTML = '(잘못된 카드 번호)';
		return false;
	}
}

function manageService(id) {
	window.open("./lotto_member_management.php?mb_id="+id, '', 'width=1200, height=800, scrollbars=yes');
}

function excel_download() {
	var f = document.searchForm;
	f.proc.value = "downloadServiceBuyExcel";
	f.action = "./term_service_buy_process.php";
	f.submit();
	f.proc.value = "";
	f.action = "";
}
//-->
</script>
<?
include_once (G5_MANAGER1_PATH."/admin.tail.php");
?>
