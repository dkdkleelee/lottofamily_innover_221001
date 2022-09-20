<?php
define('G5_IS_MANAGER', true);
$sub_menu = "100100";

include_once("./_common.php");

$g5['title'] = '<i class="fa fa-pause"></i> 이용정지회원 회원관리';
include_once (G5_MANAGER1_PATH.'/admin.head.php');


use \Acesoft\Common\Utils;
use \Acesoft\LottoApp\TermService;
use \Acesoft\LottoApp\LottoService;
use \Acesoft\LottoApp\Member\User;
use \Acesoft\LottoApp\LottoServiceConfig;

// 서비스
$termService = new TermService();

// set param and paging url
$param =  Utils::getParameters(array('nocache','sc','sv','r_msg'));
$param1 = Utils::getParameters(array('page','nocache','r_msg'));
$param2 =  Utils::getParameters(array('nocache','r_msg'));
$return_url = "http://".$_SERVER[HTTP_HOST].$_SERVER[PHP_SELF]."?".$param2;
$list_url = "http://".$_SERVER[HTTP_HOST].$_SERVER[PHP_SELF]."?".$param1;


// 목록
$user = new User();
//if(!$_GET['sc'] || !$_GET['sv']) $_GET['s_tm'] = $_SESSION['ss_mb_id'];

//등수검색 2019-07-25
//$_GET['s_lwr_grade'] = 5;
// 기본조건
$_GET['s_distributed'] = true;
$_GET['s_status'] = 2; // 이용정지회원

$user_list = $user->getUserListWithServiceForTM($_GET['page'], $list_url);

// 서비스이용회원 수
$serviceCount = $termService->getServiceUseCount();

// TM목록
$tm_list = $user->getTMList();
for($i=0; $i<count($tm_list); $i++) {
	$tm_arr[$tm_list[$i]['mb_id']] = $tm_list[$i]['mb_name'];
}

$tot_count = $user->getUserTotalCount($_GET['s_tm_id']);

$channel_group = $user->getChannelGroup($_GET['s_tm_id']);


//$lottoService = new LottoService();
//$lastWinners = $lottoService->getLastWinner($_SESSION['ss_mb_id']);

?>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="../css/custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/admin.custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/lotto.css" type="text/css">
<script src="../js/jquery-ui-1.8.7.custom.min.js" ></script>
<script src="../js/jquery.ui.datepicker-ko.js"  charset="utf-8"></script>
<link rel="stylesheet" href="../js/datetimepicker/jquery.datetimepicker.css" type="text/css" />
<script type="text/javascript" src="../js/datetimepicker/jquery.datetimepicker.js" charset="utf-8"></script>
<style>
.mem-count {
	list-style:none;
	padding: 7px;
}

.mem-count li {
	float: left;
	border: 1px solid #d6d6d6;
	padding: 3px 15px;
	text-align: center;
	border-right:0px;
}

.mem-count li:last-child {
    border-right:1px solid #d6d6d6;
}

.mem-count li.on, ul.mem-count li.on a {
    background: #2d9acf;
    color: #ffffff;
    font-weight: bold;
    border: 1px solid #2d9acf;
}
</style>

	<!-- <div class="btitle">
		<div class="btitle_top"></div>
		<div class="btitle_text">고객관리</div>
		<div class="btitle_locate">&gt; TM &gt; 고객관리</div>
		<div class="btitle_line"></div>
	</div> -->

    <div class="content_wrap">
    
    <div class="search_box ar" style="width:100%">
        <form name="searchForm" method="get" action="?">
			<input type="hidden" name="proc">
			<div class="list-info">
				<ul class="mem-count">
					<li class="<?=($_GET['s_sg_no'] == '') ? 'on' : ''?>"><a href="<?=$_SERVER['PHP_SELF']?>?s_sg_no=">총 <strong><?=number_format($serviceCount['total'])?></strong>명회원</a></li>
					<? foreach($termService->service_grade as $key => $value) { if($value == "마스터" || $value == "다이아") continue; ?>
					<li class="<?=($_GET['s_sg_no'] == $key) ? 'on' : ''?>"><a href="<?=$_SERVER['PHP_SELF']?>?s_sg_no=<?=$key?>"><?=$value?> (<?=$serviceCount[$value]?>명)</a></li>
					<? } ?>
					<li class="<?=($_GET['s_sg_no'] == 'normal') ? 'on' : ''?>"><a href="<?=$_SERVER['PHP_SELF']?>?s_sg_no=normal">일반 (<?=$serviceCount['일반']?>명)</a></li>
				</ul>
			</div>
			<!-- <select name="s_mb_channel" class="frm_input">
				<option value="">광고채널</option>
					<option value="" <?=$_GET['s_mb_channel'] == '' ? 'selected' : ''?>>전체</option>
				<? 
					for($i=0; $i<count($channel_group); $i++) { 
						if(trim($channel_group[$i]['mb_channel']) != '') {
				?>
					<option value="<?=$channel_group[$i]['mb_channel']?>" <?=$channel_group[$i]['mb_channel'] == $_GET['s_mb_channel'] ? 'selected' : ''?>><?=$channel_group[$i]['mb_channel']?>(<?=$channel_group[$i]['cnt']?>)</option>
				<? 
						} else {
				?>
					<option value="common" <?=$_GET['s_mb_channel'] == 'common' ? 'selected' : ''?>>일반(<?=$channel_group[$i]['cnt']?>)</option>
				<?
						}
					}
				?>
			</select> -->
			<div style="margin-bottom:5px">
				
				<select name="s_order" class="frm_input" onChange="document.searchForm.submit()">
					<option value="">정렬기준(기본)</option>
					<option value="1" <?=$_GET['s_order'] == '1' ? 'selected' : ''?>>가입순</option>
					<option value="2" <?=$_GET['s_order'] == '2' ? 'selected' : ''?>>최근접속순</option>
				</select>
			</div>
			<div style="margin-bottom:5px">
				<input type="text" name="s_rd_date" class="frm_input datetimepicker" value="<?=$_GET['s_rd_date']?>" autocomplete="off" style="width:80px"> ~ <input type="text" name="e_rd_date" class="frm_input datetimepicker" value="<?=$_GET['e_rd_date']?>" autocomplete="off" style="width:80px">
				<select name="s_mb_cousult_status" class="frm_input">
					<option value="">상담상태조회</option>
					<? foreach($user->getConsultStatus() as $key => $value) { ?>
					<option value="<?=$key?>" <?=$_GET['s_mb_cousult_status'] == $key ? 'selected' : ''?>><?=$value?></option>
					<? } ?>
				</select>
			</div>
			
			<select name="s_mb_charger_tm" class="frm_input">
				<option value="">담당TM조회</option>
				<? for($i=0; $i<count($tm_list); $i++) { ?>
					<option value="<?=$tm_list[$i]['mb_id']?>" <?=$tm_list[$i]['mb_id'] == $_GET['s_mb_charger_tm'] ? 'selected' : ''?>><?=$tm_list[$i]['mb_id']?>(<?=$tm_list[$i]['mb_name']?>)</option>
				<? }?>
			</select>
			<select name="s_sg_no" id="sido" class="frm_input" onChange="document.searchForm.submit()">
				<option value="">서비스전체</option>
				<? foreach($termService->service_grade as $key => $value) { ?>
				<option value="<?=$key?>" <?=($_GET['s_sg_no'] == $key) ? 'selected' : ''?>><?=$value?> (<?=$serviceCount[$value]?>명)</option>
				<? } ?>
				<option value="normal" <?=($_GET['s_sg_no'] == 'normal') ? 'selected' : ''?>>일반 (<?=$serviceCount['일반']?>명)</option>
			</select>
			<select name="s_lwr_grade" class="frm_input" onChange="document.searchForm.submit()">
				<option value="">지난주당첨</option>
				<option value="1" <?=($_GET['s_lwr_grade']=='1') ? 'selected' : '';?>>1등</option>
				<option value="2" <?=($_GET['s_lwr_grade']=='2') ? 'selected' : '';?>>2등</option>
				<option value="3" <?=($_GET['s_lwr_grade']=='3') ? 'selected' : '';?>>3등</option>
				<option value="4" <?=($_GET['s_lwr_grade']=='4') ? 'selected' : '';?>>4등</option>
				<option value="5" <?=($_GET['s_lwr_grade']=='5') ? 'selected' : '';?>>5등</option>
			</select>
            <select name="sc" class="frm_input">
                <option value="mb_name" <?=($_GET['sc']=='mb_name') ? 'selected' : '';?>>이름(가입자명)</option>
				<option value="mo_memo" <?=($_GET['sc']=='mo_memo') ? 'selected' : '';?>>메모</option>
				<option value="a.mb_id" <?=($_GET['sc']=='a.mb_id') ? 'selected' : '';?>>회원아이디</option>
				<option value="a.mb_hp" <?=($_GET['sc']=='a.mb_hp') ? 'selected' : '';?>>휴대전화</option>
				<option value="a.mb_nick" <?=($_GET['sc']=='a.mb_nick') ? 'selected' : '';?>>닉네임</option>
            </select>
            <input type="text" class="frm_input" style="width:130px" name="sv" value="<?=$_GET['sv']?>">
			<button class="as-btn medium white" onclick="document.searchForm.submit();void(0);"><i class="fa fa-search"></i> 검색</button>
        </form>
    </div>
	<div class="flex m-5">
		<div class="flex-1">
			<button type="button" class="as-btn small green" onclick="updateSelectedRow()"><i class="fa fa-refresh"></i> 선택수정</button>
			<button type="button" class="as-btn small blue" onclick="retireUsers()"><i class="fa fa-stop"></i> 선택회원 탈퇴</button>
		</div>
		<div class="flex-3">
			<button class="as-btn small blue" onclick="showManageWin('');"><i class="fa fa-plus"></i> 회원등록</button>
		</div>
	</div>
	<form name="list_form">
	<input type="hidden" name="proc" value="updateChargerTmSelectedRows">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
    <tr>
		<th width="60px"><label><input type="checkbox" name="check_all" id="check_all" value="1">선택</label></th>
		<th width="60px">번호</th>
		<th width="60px">상태</th>
		<th width="60px">구분</th>
        <th width="130px">이름(아이디)</th>
		<th width="100px">당첨(지난주)</th>
		<th width="150px">이용중 서비스</th>
        <!-- <th width="90px">SMS수신요일</th>
		<th width="80px">SMS수신</th> -->
		<th width="100px">휴대폰</th>
		<th width="90px">담당TM</th>
		<th width="80px">상담이력</th>
		<th width="200px">메모</th>
        <th width="130px">마지막로그인<!-- /업데이트 --></th>
        <th width="180px">변경</th>
    </tr>
    <?
	//▶ 등급별 발급설정갯수
	$lottoServiceConfig = new LottoServiceConfig();
	$serviceConfig = $lottoServiceConfig->getConfig();
	$extract_count_per_grade = unserialize($serviceConfig['lc_extract_count']);

    foreach ($user_list['list'] as $row) {
		$row['mb_tel'] = Utils::arrangeTelNumber($row['mb_tel']);
		$row['mb_hp'] = Utils::arrangeHPNumber($row['mb_hp']);
		$row_service = $termService->getMemberServiceUse($row['mb_id']);

		$service_use = "<ul>";
		for($i=0; $i<count($row_service); $i++) {
			$service_use .= "<li>".$row_service[$i]['sg_name']." <br /> [".substr($row_service[$i]['su_enddate'],0,10)." 까지] </li>";
		}
		$service_use .= "<ul>";

		$extract_per_week = strtotime($row_service[0]['su_enddate']) >= time()  ? $extract_count_per_grade[$row_service[0]['sg_no']] : $extract_count_per_grade[0];
		$extract_per_week = $row['mb_extract_per_week'] ? $row['mb_extract_per_week'] : $extract_per_week;

		$row_memo = $user->getLatestMemo($row['mb_id']);

    ?>
    <tr id="pos_<?=$row['mb_no']?>" class="<? if(count($row_service) > 0) { ?>on<? } ?>">
		<td>
			<input type="checkbox" name="chk[]" value="<?=$row['mb_id']?>">
		</td>
        <td>
			<?=$user_list['idx']--?>
		</td>
		<td>
			<?
				switch($row['mb_status']) {
					case '1': echo "<span style='color:#669933'>이용중</span>"; break;
					case '2': echo "<span style='color:#ff33cc'>이용중지</span>"; break;
					case '3': echo "<span style='color:#ff6600'>탈퇴</span>"; break;
				}
			?>
		</td>
		<td>
			<? if(count($row_service) > 0) { ?>
			<button type="button" class=" as-btn tiny red" style="color:white"><?=$row_service[0]['sg_name']?></button>
			<? } ?>
		</td>
        <td><?=$row['mb_name']?> (<?=$row['mb_id']?>)</td>
		<td>
			
			
			<?
				if($row['win_records']) {
					$winRecords = explode(",", $row['win_records']);
					$win = array_count_values($winRecords);

					$i=0;
					foreach($win as $key => $value) {
			?>
						<span style="color:<?=$key < 4 ? '#ff9966' : '#6633cc'?>;display:block"><?=$key?>등(<?=$value?>개)</span>
			<?
						$i++;
					}
				}
			
			?>
			
		</td>
		<td>
			<?=$service_use?>
		</td>
		<!-- <td><?=$termService->config['weekdays_name'][$row['mb_extract_weekday']]?></td>
		<td><?=($row['mb_sms'] ? '수신 ('.number_format($extract_per_week).'개)' : '수신안함')?></td> -->
        <td><?=$row['mb_hp']?></td>
		<td>
			<?=$row['mb_tm_id'] ? $tm_arr[$row['mb_tm_id']]  : '미지정'?>
		</td>
		<td><?=($row['mb_cousult_status']) ? $row['mb_cousult_status'] : ($row_memo[0]['mo_memo']) ? $row['mb_cousult_status'] : '상담이전'?></td>
		<td class="al pl5" <?=($row_memo[0]['mo_memo']) ? 'data-tooltip="<span style=\'font-weight:bold\'>['.$row['mb_update_date']."]</span><br />".nl2br($row_memo[0]['mo_memo']).'"' : ''?>><?=($row_memo[0]['mo_memo']) ? '<span >'.cut_str($row_memo[0]['mo_memo'],45)."</span><br /><span style='font-weight:bold'>[".$row['mb_update_date']."]</span>"  : '상담이전'?></td>
        <td>
			<?=$row['mb_today_login'] && $row['mb_today_login'] != "0000-00-00 00:00:00" ? date('Y.m.d', strtotime($row['mb_today_login'])).'(<span class="fwb fcg">'.$row['lastLoginDays'].'일전</span>)' : '--'?><br />
			<!-- <?=date('Y.m.d', strtotime($row['mb_datetime']))?> /  --><!-- <?=$row['mb_datetime'] ? date('Y.m.d', strtotime($row['mb_datetime'])) : '--'?> <br /> -->
		</td>
        <td>
			<button type="button" class="as-btn small blue" onclick="setCurrent('<?=$row['mb_no']?>');showManageWin('<?=$row['mb_id']?>');">
				<i class="fa fa-eye"></i> 관리
			</button>
			<!-- <button type="button" class="as-btn small green" onclick="setCurrent('<?=$row['mb_no']?>');location.href='./lotto_member_regist.php?<?=$param?>&mb_id=<?=$row['mb_id']?>&url=<?=urlencode($return_url)?>'">
				<i class="fa fa-gear"></i> 수정
			</button> -->
			<? if($row['mb_status'] == '2') { ?>
			<button type="button" class="as-btn small blue" onclick="startUser('<?=$row['mb_id']?>');">
				<i class="fa fa-play"></i> 복원
			</button>
			<? } ?>
        </td>
    </tr>
    <? } ?>
    </table>
	</form>
	<div class="flex m-5">
		<div class="flex-1">
			<button type="button" class="as-btn small green" onclick="updateSelectedRow()"><i class="fa fa-refresh"></i> 선택수정</button>
			<button type="button" class="as-btn small blue" onclick="retireUsers()"><i class="fa fa-stop"></i> 선택회원 탈퇴</button>
		</div>
		<div class="flex-3">
			<button class="as-btn small blue" onclick="showManageWin('');"><i class="fa fa-plus"></i> 회원등록</button>
		</div>
	</div>
	
	<div class="paginate wrapper paging_box">
		<ul>
			<?=$user_list['link']?>
		</ul>
	</div>
	
	<style>
		.dataTable tr:nth-child(even) td {
			border-bottom: 1px solid #e9e9e9;
		}

		.dataTable tbody td {
			border: 1px dotted #eeeeee;
		}

		
	</style>


<script>
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

	$('[data-tooltip]').hover(function(){
		$('<div class="div-tooltip"></div>').html($(this).attr('data-tooltip')).appendTo('body').fadeIn('fast');
	}, function() { 
		$('.div-tooltip').remove();
	}).mousemove(function(e) {
		$('.div-tooltip').css({ top: e.pageY + 10, left:  e.pageX + 20 })
	});

});

function showManageWin(id) {
	window.open('./lotto_member_management.php?mb_id='+id, '', 'width=1200,height=800, scrollbars=yes');
}

function setCurrent(no) {
	sessionStorage.mb_no = no;
}

$(window).scroll(function () {
    //set scroll position in session storage
    sessionStorage.scrollPos = $(window).scrollTop();
});
var init = function () {
    //get scroll position in session storage
    //$(window).scrollTop(sessionStorage.scrollPos || 0);
	//if(sessionStorage.mb_no) $('#pos_'+sessionStorage.mb_no+' > td').css('background-color', '#66ccff');

	$('.on > td').css('background-color', '#66ccff');
};
window.onload = init;


function stopUsers() {
	var nos = '';
	$('input[name="chk[]"]:checked').each(function() {
		nos += nos ? ','+$(this).val() : $(this).val();
	});

	if(!nos) {
		alert("이용중지할 회원을 선택해 주세요.");
		return false;
	}

	if(confirm("선택한 회원들을 이용중지 처리 하시겠습니까?")) {
		$.ajax({
			type: "POST",
			url: "./lotto_member_process.php",
			dataType: "text",
			data: {
				proc:"stopUsers",
				ids:nos
			},
			success: function(data) {
				if(data == 'success') {
					alert("처리완료");
					document.location.reload();
				} else {
					alert("처리실패");
					document.location.reload();
				}
			}
		});
	}
}

function retireUsers() {
	var nos = '';
	$('input[name="chk[]"]:checked').each(function() {
		nos += nos ? ','+$(this).val() : $(this).val();
	});

	if(!nos) {
		alert("탈퇴할 회원을 선택해 주세요.");
		return false;
	}

	if(confirm("선택한 회원들을 탈퇴 처리 하시겠습니까?")) {
		$.ajax({
			type: "POST",
			url: "./lotto_member_process.php",
			dataType: "text",
			data: {
				proc:"retireUsers",
				ids:nos
			},
			success: function(data) {
				if(data == 'success') {
					alert("처리완료");
					document.location.reload();
				} else {
					alert("처리실패");
					document.location.reload();
				}
			}
		});
	}
}

function startUser(id) {
	

	if(confirm("선택한 회원을 이용중으로 변경 하시겠습니까?")) {
		$.ajax({
			type: "POST",
			url: "./lotto_member_process.php",
			dataType: "text",
			data: {
				proc:"startUser",
				mb_id:id
			},
			success: function(data) {
				if(data == 'success') {
					alert("처리완료");
					document.location.reload();
				} else {
					alert("처리실패");
					document.location.reload();
				}
			}
		});
	}
}
</script>
<?php
include_once(G5_MANAGER1_PATH.'/admin.tail.php');
?>
