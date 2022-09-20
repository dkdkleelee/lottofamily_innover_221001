<?php
define('G5_IS_ADMIN', true);
$sub_menu = "200250";
/**
 * Created by PhpStorm.
 * User: acerunner
 * Date: 16. 2. 14.
 * Time: 오후 5:46
 */

include_once("./_common.php");

use \Acesoft\Common\Utils;
use \Acesoft\LottoApp\TermService;
use \Acesoft\LottoApp\Member\User;
use \Acesoft\LottoApp\Member\Group;
use \Acesoft\LottoApp\LottoService;
use \Acesoft\LottoApp\LottoServiceConfig;

// 서비스
$termService = new TermService();


// set param and paging url
$param =  Utils::getParameters(array('nocache','sc','sv'));
$param1 = Utils::getParameters(array('page','nocache'));
$param2 =  Utils::getParameters(array('nocache','r_msg'));
$return_url = "http://".$_SERVER[HTTP_HOST].$_SERVER[PHP_SELF]."?".$param2;
$list_url = "http://".$_SERVER[HTTP_HOST].$_SERVER[PHP_SELF]."?".$param1;


// 목록
$user = new User();

// 기본조건
$_GET['s_distributed'] = true;
$_GET['s_status'] = 1; // 정상회원

// 서비스이용회원 수
$serviceCount = $termService->getServiceUseCount();

// 기한초과카드정보 마스킹
$termService->setMaskToCardInfo();

// TM목록
$tm_list = $user->getTMList();

$channel_group = $user->getChannelGroup('', "mb_tm_id IS NOT NULL AND mb_tm_id <> ''");

$media_group = $user->getMediaGroup('', $_GET['s_mb_channel'], "mb_tm_id IS NOT NULL AND mb_tm_id <> ''");

// 그룹정보
$group = new Group();
$group_arr = $group->getGroupArr();

//$pageLimit = 20;
$lottoService = new LottoService();
$inning_arr = $lottoService->getWinNumberInningGroups();


//$tot_count = $user->getUserWithNoLoginCount($inning_arr[0]['inning']+1, 3);
$user_list = $user->getUserListWithNoLogin($_GET['page'], $list_url, $inning_arr[0]['inning']+1, 3, $pageLimit);

auth_check($auth[$sub_menu], 'w');

$g5['title'] = '<i class="fa fa-play"></i> 3X';
include_once(G5_ADMIN_PATH."/admin.head.php");

?>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="../css/custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/admin.custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/lotto.css" type="text/css">
<!-- sparkline -->
<script src="../js/plugins/peity-master/jquery.peity.min.js"></script>
<script src="../js/jquery-ui-1.8.7.custom.min.js" ></script>
<script src="../js/jquery.ui.datepicker-ko.js"  charset="utf-8"></script>
<link rel="stylesheet" href="../js/datetimepicker/jquery.datetimepicker.css" type="text/css" />
<script type="text/javascript" src="../js/datetimepicker/jquery.datetimepicker.js" charset="utf-8"></script>
<!-- <script src="https://unpkg.com/popper.js@1"></script>
<script src="https://unpkg.com/tippy.js@4"></script> -->
<style>
.mem-count {
	list-style:none;
}

.mem-count li {
	float: left;
	border: 1px solid #d6d6d6;
	width: 120px;
	text-align: center;
	border-right:0px;
}

.mem-count li  > a {
	line-height: 35px;
}

.mem-count li:last-child {
    border-right:1px solid #d6d6d6;
}

.mem-count li.on, li.on a {
    background: #2d9acf;
    color: #ffffff;
    font-weight: bold;
    border: 1px solid #2d9acf;
}
</style>
<script>
/*
$.fn.peity.defaults.pie = {
  delimiter: null,
  fill: [ "#fff4dd","#ff9900", "#ffd592"],
  height: null,
  radius: 15,
  width: null
}

  $.fn.peity.defaults.donut = {
  delimiter: null,
  fill: [ "#fff4dd","#ff9900", "#ffd592"],
  height: null,
  radius: 15,
  width: null
}*/
</script>
<div class="info_container">

    <div class="content_wrap">
    <div>
<!--        <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i></span>-->
<!--        <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i></span>-->
    </div>
    <div class="search_box ar">
        <form name="searchForm" method="get" action="?">
			<input type="hidden" name="proc">
			<!--<div>
				<ul class="mem-count">
					<li class="<?=($_GET['s_sg_no'] == 'normal') ? 'on' : ''?>"><a href="<?=$_SERVER['PHP_SELF']?>?s_sg_no=normal&s_nologin=no">총 <strong><?=number_format($tot_count)?></strong>명회원</a></li>
				</ul>
			</div> -->
			<div style="margin-bottom:5px">
				
				<!-- <select name="s_status" class="frm_input" onChange="document.searchForm.submit()">
					<option value="">회원상태</option>
					<option value="1" <?=$_GET['s_status'] == '1' ? 'selected' : ''?>>이용중</option>
					<option value="2" <?=$_GET['s_status'] == '2' ? 'selected' : ''?>>이용중지</option>
					<option value="3" <?=$_GET['s_status'] == '3' ? 'selected' : ''?>>탈퇴</option>
				</select> -->

				<select name="s_pageLimit" class="frm_input" onChange="document.searchForm.submit()">
					<option value="">회원표시수</option>
					<option value="20"> 20명</option>
					<option value="50"> 50명</option>
					<option value="100"> 100명</option>
					<option value="200"> 200명</option>
					<option value="400"> 400명</option>
					<option value="1500"> 1500명</option>
				</select>
				<select name="s_order" class="frm_input" onChange="document.searchForm.submit()">
					<option value="">정렬기준(기본)</option>
					<option value="1" <?=$_GET['s_order'] == '1' ? 'selected' : ''?>>가입순</option>
					<option value="2" <?=$_GET['s_order'] == '2' ? 'selected' : ''?>>최근접속순</option>
				</select>
				<select name="s_mb_level" class="frm_input" onChange="document.searchForm.submit()">
                    <option value="">회원레벨</option>
					<? foreach($user->getUserType() as $key => $value) { ?>
						<option value="<?=$key?>" <?=$_GET['s_mb_level'] == $key ? 'selected' : ''?>><?=$value?></option>
					<? } ?>
                </select>
			</div>

			<div>

                                <div style="margin-bottom:5px">
                                        <label>등록일조회 : <input type="text" name="s_date" class="frm_input datetimepicker" value="<?=$_GET['s_date']?>" autocomplete="off"> ~ <input type="text" name="e_date" class="frm_input datetimepicker" value="<?=$_GET['e_date']?>" autocomplete="off"></label>
                                        <label>배분일조회 : <input type="text" name="s_rd_date" class="frm_input datetimepicker" value="<?=$_GET['s_rd_date']?>" autocomplete="off"> ~ <input type="text" name="e_rd_date" class="frm_input datetimepicker" value="<?=$_GET['e_rd_date']?>" autocomplete="off"></label>
                                </div>


				<div style="margin-bottom:5px">
					
				</div>
				<select name="s_mb_channel" class="frm_input" onChange="document.searchForm.submit()">
					<option value="">광고채널</option>
						<!-- <option value="" <?=$_GET['s_mb_channel'] == '' ? 'selected' : ''?>>전체</option> -->
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
				</select>
				<select name="s_mb_media" class="frm_input" onChange="document.searchForm.submit()">
					<option value="">광고매체</option>
						
					<? 
						for($i=0; $i<count($media_group); $i++) { 
							if(trim($media_group[$i]['mb_media']) != '') {
					?>
						<option value="<?=$media_group[$i]['mb_media']?>" <?=$media_group[$i]['mb_media'] == $_GET['s_mb_media'] ? 'selected' : ''?>><?=$media_group[$i]['mb_media']?>(<?=$media_group[$i]['cnt']?>)</option>
					<? 
							} else {
					?>
						<option value="common" <?=$_GET['s_mb_media'] == 'common' ? 'selected' : ''?>>일반(<?=$media_group[$i]['cnt']?>)</option>
					<?
							}
						}
					?>
				</select>
				<select name="s_mb_charger_tm" class="frm_input">
					<option value="">담당TM조회</option>
						<option value="na" <?=$_GET['s_mb_charger_tm'] == 'na' ? 'selected' : ''?>>미지정</option>
					<? for($i=0; $i<count($tm_list); $i++) { ?>
						<option value="<?=$tm_list[$i]['mb_id']?>" <?=$tm_list[$i]['mb_id'] == $_GET['s_mb_charger_tm'] ? 'selected' : ''?>><?=$tm_list[$i]['mb_id']?>(<?=$tm_list[$i]['mb_name']?>)</option>
					<? }?>
				</select>
				<select name="s_mb_extract_weekday" class="frm_input">
					<option value="">발급요일</option>
					<option value="1" <?=$_GET['s_mb_extract_weekday'] == '1' ? 'selected' : ''?>>월</option>
					<option value="2" <?=$_GET['s_mb_extract_weekday'] == '2' ? 'selected' : ''?>>화</option>
					<option value="3" <?=$_GET['s_mb_extract_weekday'] == '3' ? 'selected' : ''?>>수</option>
					<option value="4" <?=$_GET['s_mb_extract_weekday'] == '4' ? 'selected' : ''?>>목</option>
					<option value="5" <?=$_GET['s_mb_extract_weekday'] == '5' ? 'selected' : ''?>>금</option>
				</select>
				<select name="s_sms" class="frm_input">
					<option value="">SMS수신</option>
					<option value="1" <?=$_GET['s_sms'] == '1' ? 'selected' : ''?>>수신함</option>
					<option value="0" <?=$_GET['s_sms'] == '0' ? 'selected' : ''?>>수신안함</option>
				</select>
				<select name="s_mb_cousult_status" class="frm_input">
					<option value="">상담상태조회</option>
					<? foreach($user->getConsultStatus() as $key => $value) { ?>
					<option value="<?=$key?>" <?=$_GET['s_mb_cousult_status'] == $key ? 'selected' : ''?>><?=$value?></option>
					<? } ?>
				</select>

			</div>
            
            <select name="sc" class="frm_input">
                <option value="mb_name" <?=($_GET['sc']=='mb_name') ? 'selected' : '';?>>이름(가입자명)</option>
				<option value="mo_memo" <?=($_GET['sc']=='mo_memo') ? 'selected' : '';?>>메모</option>
				<option value="a.mb_id" <?=($_GET['sc']=='a.mb_id') ? 'selected' : '';?>>회원아이디</option>
				<option value="a.mb_hp" <?=($_GET['sc']=='a.mb_hp') ? 'selected' : '';?>>휴대전화</option>
				<option value="a.mb_nick" <?=($_GET['sc']=='a.mb_nick') ? 'selected' : '';?>>닉네임</option>
            </select>
            <input type="text" class="frm_input" name="sv" value="<?=$_GET['sv']?>">
			<button class="as-btn medium white" onclick="document.searchForm.submit();void(0);"><i class="fa fa-search"></i> 검색</button>
        </form>
    </div>

	<form name="list_form">
	<input type="hidden" name="proc" value="updateChargerTmSelectedRows">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
    <tr>
		<th width="60px"><label><input type="checkbox" name="check_all" id="check_all" value="1">선택</label></th>
		<th width="60px">번호</th>
		<th width="60px">상태</th>
        <th width="70px">구분</th>
        <th width="130px">이름(아이디)</th>
		<th width="100px">당첨(지난주)</th>
		<th width="150px">이용중 서비스</th>
        <th width="70px">발급요일</th>
		<th width="70px">SMS수신</th>
		<th width="100px">휴대폰</th>
		<th width="120px">담당TM</th>
		<th width="200px">메모</th>
		<th width="80px">상담이력</th>
		<th width="80px">배분일자</th>
        <th width="130px">등록일 / 마지막로그인</th>
        <th width="80px">변경</th>
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
			$service_use .= "<li>".$row_service[$i]['sg_name']." : [".substr($row_service[$i]['su_enddate'],0,10)." 까지] </li>";
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
			
			<button type="button" class=" as-btn tiny <?=$row['mb_level'] == '3' ? 'blue' : 'green'?>" style="color:white"><?=$user->getUserType()[$row['mb_level']]?>
			
			<?php if($row['mb_level'] == '3') { ?>
				<span style="color:#33ffff">[<?=$row['mg_name'] ? $row['mg_name'] : '미지정'?>]</span>
			<?php } ?>
			</button>
			<? if(count($row_service) > 0) { ?>
			<button type="button" class=" as-btn tiny red" style="color:white"><?=$row_service[0]['sg_name']?></button>
			<? } ?>
			<? if($row['mb_channel']) { ?>
				<br /><?=$row['mb_channel']?>
			<? } ?>
			<? if($row['mb_media']) { ?>
				<br /><?=$row['mb_media']?>
			<? } ?>
			
		</td>
        <td>
			<?=$row['mb_name']?> (<?=$row['mb_id']?>)
		</td>
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
		<td><?=$termService->config['weekdays_name'][$row['mb_extract_weekday']]?></td>
		<td><?=($row['mb_sms'] ? '수신 ('.number_format($extract_per_week).'개)' : '수신안함')?></td>
        <td><?=$row['mb_hp']?></td>
        
		<td>
			<select name="mb_charger_tm[<?=$row['mb_id']?>]" class="frm_input" data-old='<?=$row['mb_tm_id']?>' data-no='<?=$row['mb_no']?>' onChange="checkTmChanged(this)">
				<option value="">미지정</option>
			<? for($i=0; $i<count($tm_list); $i++) { ?>
				<option value="<?=$tm_list[$i]['mb_id']?>" <?=$tm_list[$i]['mb_id'] == $row['mb_tm_id'] ? 'selected' : ''?>><?=$tm_list[$i]['mb_name']?></option>
			<? }?>
			</select>
			<input type="hidden" name="tm_changed[<?=$row['mb_id']?>]" id="tm_changed_<?=$row['mb_no']?>" value="">
		</td>
		<td class="al pl5" <?=($row_memo[0]['mo_memo']) ? 'data-tooltip="<span style=\'font-weight:bold\'>['.$row_memo[0]['mb_name'].' / '.$row_memo[0]['mo_datetime']."]</span><br />".nl2br($row_memo[0]['mo_memo']).'"' : ''?>><?=($row_memo[0]['mo_memo']) ? '<span >'.cut_str($row_memo[0]['mo_memo'],25).'</span>' : '상담이전'?></td>
		<td><?=($row['mb_cousult_status']) ? $row['mb_cousult_status'] : ($row_memo[0]['mo_memo']) ? $row['mb_cousult_status'] : '상담이전'?></td>
		
		<td><?=($row['mb_distribute_date']) != "0000-00-00 00:00:00" ? date('Y.m.d', strtotime($row['mb_distribute_date'])) : '--'?></td>
        <td>
			<?=date('Y.m.d', strtotime($row['mb_datetime']))?> <br /> <?=$row['mb_today_login'] && $row['mb_today_login'] != "0000-00-00 00:00:00" ? date('Y.m.d', strtotime($row['mb_today_login'])).'(<span class="fwb fcg">'.$row['lastLoginDays'].'일전</span>)' : '--'?>
		</td>
        <td>
			<button type="button" class="as-btn small blue" onclick="setCurrent('<?=$row['mb_no']?>');showManageWin('<?=$row['mb_id']?>');">
				<i class="fa fa-eye"></i> 관리
			</button>
			<button type="button" class="as-btn small blue" onclick="showSendManageWin('<?=$row['mb_id']?>');">
				<i class="fa fa-eye"></i> 발급관리
			</button>
			<? if($row['mb_status'] == '2' || $row['mb_status'] == '3') { ?>
			<button type="button" class="as-btn small blue" onclick="startUser('<?=$row['mb_id']?>');">
				<i class="fa fa-play"></i> 복원
			</button>
			<? } ?>
			<? if($row['mb_id'] != 'admin') { ?>
				<? if($row['mb_leave_date'] != '') { ?>
				<button type="button" class="as-btn small red" onclick="deleteUser('<?=$row['mb_id']?>')">
					<i class="fa fa-close"></i> 삭제
				</button>
				<? } ?>
			<? } ?>
        </td>
    </tr>
    <? } ?>
    </table>
	</form>

	<div class="flex m-5">
		<div class="flex-1">
		<label style="background-color:#4f95ff;color:#fff;padding:5px 4px"> <?=$inning_arr[0]['inning']+1?>회차</label> 의 당첨번호는 &nbsp
		<td>
                        <input type="text" class="frm_input" id="num1" style="width:30px;text-align:center">&nbsp&nbsp  
                        <input type="text" class="frm_input" id="num2" style="width:30px;text-align:center">&nbsp&nbsp
                        <input type="text" class="frm_input" id="num3" style="width:30px;text-align:center">&nbsp&nbsp
                        <input type="text" class="frm_input" id="num4" style="width:30px;text-align:center">&nbsp&nbsp
                        <input type="text" class="frm_input" id="num5" style="width:30px;text-align:center">&nbsp&nbsp
                        <input type="text" class="frm_input" id="num6" style="width:30px;text-align:center">&nbsp + &nbsp
                        <input type="text" class="frm_input" id="num7" style="width:30px;text-align:center">
                &nbsp입니다.
		</td>
		</div>
	</div>


	<div class="flex m-5">
		<div class="flex-1">
		선택한 회원들의 <label style="background-color:#4f95ff;color:#fff;padding:5px 4px"> <?=$inning_arr[0]['inning']+1?>회차</label>
		 발급된 당첨번호 중에 1개를 <label style="background-color:#6b8225;color:#fff;padding:5px 4px">3등 당첨번호</label> 로 
		<button type="button" class="as-btn small green" onclick="setNumberNoLogin('<?=$inning_arr[0]['inning']+1?>', 3)"><i class="fa fa-refresh"></i> 변경</button> 하고, 당첨문자를 발송합니다.
		</div>
		<div class="flex-3">
			<!-- <button type="button" class="as-btn small green" onclick="distributeMemeberToTM()"><i class="fa fa-sort"></i> 미지정회원배분</button> -->
			<button type="button" class="as-btn small blue" onclick="excel_download();"><i class="fa fa-download"></i> 회원 엑셀다운로드</button>
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
    <form name="unsubscribe_form" method="post" action="./lotto_member_process.php">
        <input type="hidden" name="proc" value="unsubscribeUser">
        <input type="hidden" name="url" value="<?=$return_url?>">
        <input type="hidden" name="id">
    </form>

	<form name="deleteuser_form" method="post" action="./lotto_member_process.php">
        <input type="hidden" name="proc" value="deleteUser">
        <input type="hidden" name="url" value="<?=$return_url?>">
        <input type="hidden" name="id">
    </form>

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

function showSendManageWin(id) {
	window.open('./lotto_member_extract_management.php?id='+id, '', 'width=900,height=800, scrollbars=yes');
}
/*
function setNumberNoLogin(id) {
    var f = document.unsubscribe_form;
    if(!id) {
        return false;
    } else {
        if(confirm("선택회원을 탈퇴처리 하시겠습니까?")) {
            f.id.value = id;
            f.submit();
        }
    }
}
*/

function deleteUser(id) {
    var f = document.deleteuser_form;
    if(!id) {
        return false;
    } else {
        if(confirm("선택회원을 삭제 하시겠습니까?")) {
            f.id.value = id;
            f.submit();
        }
    }
}

$(document).ready(function() {
	$('.member-auth').on('click', function() {
		var id = $(this).attr('data-id');
		var status = $(this).hasClass('red') ? '2' : '1';
		var btn = $(this);
		$.post("./lotto_member_process.php", { proc:'memberAuth', id: id, status: status}).done(function(result) {

			if(status == 2) {
				$(btn).removeClass('red').addClass('green');
				$(btn).text('인증회원');
				alert("인증하였습니다.");
			} else {
				$(btn).removeClass('green').addClass('red');
				$(btn).text('비인증회원');
				alert("인증취소하였습니다.");
			}
		});
	});


	$('.member-adult').on('click', function() {
		var id = $(this).attr('data-id');
		var status = $(this).hasClass('red') ? '1' : '0';
		var btn = $(this);
		$.post("./lecture_member_process.php", { proc:'memberAdult', id: id, status: status}).done(function(result) {

			if(status == 1) {
				$(btn).removeClass('red').addClass('green');
				$(btn).text('성인인증회원');
				alert("성인회원으로 상태를 변경하였습니다.");
			} else {
				$(btn).removeClass('green').addClass('red');
				$(btn).text('비인증회원');
				alert("미성년회원으로 상태를 변경하였습니다.");
			}
		});
	});
});

function excel_download() {
	var f = document.searchForm;
	f.proc.value = "downloadMemberExcel";
	f.action = "./lotto_member_process.php";
	f.submit();
	f.proc.value = "";
	f.action = "";
}

function excel_upload() {
	window.open("./lotto_member_excel_upload.php","member_execel_upload", "width=600, height=450");
}

function updateSelectedRow() {
	var nos = '';
	$('input[name="chk[]"]:checked').each(function() {
		nos += nos ? ','+$(this).val() : $(this).val();
	});

	if(!nos) {
		alert("변경하실 항목(들)을 선택해 주세요.");
		return false;
	}


	if(confirm("선택한 항목들을 변경하시겠습니까?")) {
		$.post("lotto_member_process.php", $('form[name="list_form"]').serialize()).done(function (data) {
			
			if(data == 'success') {
				document.location.reload();
			} else {
				alert("변경실패");
				document.location.reload();
			}
		});
	}
}

function deleteSelectedUsers() {
	var nos = '';
	$('input[name="chk[]"]:checked').each(function() {
		nos += nos ? ','+$(this).val() : $(this).val();
	});

	if(!nos) {
		alert("삭제하실 데이터를 선택해 주세요.");
		return false;
	}


	if(confirm("선택한 회원들을 삭제시겠습니까?\n삭제된 데이터는 복구할 수 없습니다.\n계속하시겠습니까?")) {
		$.ajax({
			type: "POST",
			url: "./lotto_member_process.php",
			dataType: "text",
			data: {
				proc:"deleteSelectedUsers",
				ids:nos
			},
			success: function(data) {
				if(data == 'success') {
					alert("삭제완료");
					document.location.reload();
				} else {
					alert("삭제실패");
					document.location.reload();
				}
			}
		});
	}
}


function initMemo() {
	var nos = '';
	var tm_id = '';

	tm_id = $('#c_mb_charger_tm').val();

	$('input[name="chk[]"]:checked').each(function() {
		nos += nos ? ','+$(this).val() : $(this).val();
	});


	if(!nos) {
		alert("메모초기화할 회원을 선택해 주세요.");
		return false;
	}


	if(confirm("선택한 회원들의 메모를 메모초기화 하시겠습니까?")) {
		$.ajax({
			type: "POST",
			url: "./lotto_member_process.php",
			dataType: "text",
			data: {
				proc:"initMemo",
				ids:nos
			},
			success: function(data) {
				if(data == 'success') {
					alert("처리완료");
					document.location.reload();
				} else {
					alert("처리실패");
					console.log(data);
					//document.location.reload();
				}
			}
		});
	}

}

function distributeMemeberToTM() {
	window.open("./lotto_member_distribute2.php","member_execel_upload", "width=700, height=650, scrollbars=yes");
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
	//if(sessionStorage.mb_no) $('#pos_'+sessionStorage.mb_no+' > td:first').css('background-color', '#33ffcc');
	$('.on > td').css('background-color', '#66ccff');
};
window.onload = init;

function checkTmChanged(obj) {
	var old = $(obj).attr('data-old');
	var no = $(obj).attr('data-no');
	var cur = $(obj).val();

	if(old != cur) {
		$('#tm_changed_'+no).val('1');
	} else {
		$('#tm_changed_'+no).val('');
	}	
}

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

function setNumberNoLogin(inning, score) {
	var nos = '';
	$('input[name="chk[]"]:checked').each(function() {
		nos += nos ? ','+$(this).val() : $(this).val();
	});

	if(!nos) {
		alert("당첨번호를 변경할 회원을 선택해주세요.");
		return false;
	}

	if(confirm("선택한 회원들의 발급된 당첨번호 중 1개를 변경하시겠습니까?")) {
		$.ajax({
			type: "POST",
			url: "./lotto_member_process.php",
			dataType: "text",
			data: {
				proc:"setNumberNoLogin",
				ids:nos,
				le_inning:inning,
				score:score,
				lw_num1:document.getElementById("num1").value,
				lw_num2:document.getElementById("num2").value,
				lw_num3:document.getElementById("num3").value,
				lw_num4:document.getElementById("num4").value,
				lw_num5:document.getElementById("num5").value,
				lw_num6:document.getElementById("num6").value,
				lw_num7:document.getElementById("num7").value
			},
			success: function(data) {
				if(data == 'success') {
					alert("처리완료");
					document.location.reload();
				} else if (data == 'fail'){
					alert("처리실패");
					document.location.reload();
				} else {
					alert(data);
					document.location.reload();
				}
			}
		});
	}
}

function changeChargerTMs() {
	var nos = '';
	var tm_id = '';

	tm_id = $('#c_mb_charger_tm').val();

	$('input[name="chk[]"]:checked').each(function() {
		nos += nos ? ','+$(this).val() : $(this).val();
	});

	if(!tm_id) {
		alert("담당TM을 선택해 주세요.");
		return false;
	}

	if(!nos) {
		alert("담당TM을 변경할 회원을 선택해 주세요.");
		return false;
	}

	if(confirm("선택한 회원들의 담당TM을 변경 하시겠습니까?")) {
		$.ajax({
			type: "POST",
			url: "./lotto_member_process.php",
			dataType: "text",
			data: {
				proc:"changeChargerTMs",
				mb_tm_id: tm_id,
				ids:nos
			},
			success: function(data) {
				if(data == 'success') {
					alert("처리완료");
					document.location.reload();
				} else {
					alert("처리실패");
					console.log(data);
					//document.location.reload();
				}
			}
		});
	}
}

function changeConsultStatus() {
	var nos = '';
	var consult_status = '';

	consult_status = $('#c_mb_cousult_status').val();

	$('input[name="chk[]"]:checked').each(function() {
		nos += nos ? ','+$(this).val() : $(this).val();
	});

	if(!consult_status) {
		alert("상담당태를 선택해 주세요.");
		return false;
	}

	if(!nos) {
		alert("상담상태를 변경할 회원을 선택해 주세요.");
		return false;
	}

	if(confirm("선택한 회원들의 상담상태를 변경 하시겠습니까?")) {
		$.ajax({
			type: "POST",
			url: "./lotto_member_process.php",
			dataType: "text",
			data: {
				proc:"changeConsultStatus",
				consult_status: consult_status,
				ids:nos
			},
			success: function(data) {
				if(data == 'success') {
					alert("처리완료");
					document.location.reload();
				} else {
					alert("처리실패");
					console.log(data);
					//document.location.reload();
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
<?
include_once (G5_ADMIN_PATH."/admin.tail.php");
?>
