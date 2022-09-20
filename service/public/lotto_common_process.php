<?php
include_once("./_common.php");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Lotto;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\LottoWinRecords;
use Acesoft\LottoApp\TermService;
use Acesoft\LottoApp\Member\User;
use Acesoft\Common\Message;

error_log ($_POST['proc'], 3, "/var/log/httpd/error_log");
//echo '<script>console.log("test")</script>';

switch($_POST['proc']) {

	case 'getWinData':
			
		$data = $lottoService->getWinData($_POST['inning']);

		error_log ('test2', 3, "/var/log/httpd/error_log");

		if($data['lw_inning']) {
		
?>		
		<div class="title_area">
			<div class="th_arr">
				<a class="th_prev" href="javascript:getWinData('<?=$data['lw_inning']-1?>');" title="이전회차"><img src="/images/main_lotto_arr_l.gif"></a>
				<a class="th_next" href="javascript:getWinData('<?=$data['lw_inning']+1?>');" title="다음회차"><img src="/images/main_lotto_arr_r.gif"></a>
			</div>
			<div class="th_select">
				<span class="num"><?=$data['lw_inning']?><b>회</b></span>
			</div>
			<h2 class="box_tit">당첨번호</h2>
			<div class="data"><?=$data['lw_data']?> 추첨</div>
		</div>
		<div class="ball_box">
			<span class="ball"><img src="/images/ball_<?=sprintf('%02d', $data['lw_num1'])?>.png"></span>
			<span class="ball"><img src="/images/ball_<?=sprintf('%02d', $data['lw_num2'])?>.png"></span>
			<span class="ball"><img src="/images/ball_<?=sprintf('%02d', $data['lw_num3'])?>.png"></span>
			<span class="ball"><img src="/images/ball_<?=sprintf('%02d', $data['lw_num4'])?>.png"></span>
			<span class="ball"><img src="/images/ball_<?=sprintf('%02d', $data['lw_num5'])?>.png"></span>
			<span class="ball"><img src="/images/ball_<?=sprintf('%02d', $data['lw_num6'])?>.png"></span>
			<span class="ball"><img src="/images/ball_+.png"></span>
			<span class="ball"><img src="/images/ball_<?=sprintf('%02d', $data['lw_num7'])?>.png"></span>
		</div>
		<div class="result_tbl">
		<table class="table_st1 table_st">
				<colgroup>
					<col style="width: 80px;">
					<col style="width: 180px;">
					<col style="width: 160px;">
					<col style="width: auto;">
				</colgroup>
				<thead>
					<tr>
						<th>순위</th>
						<th>총당첨금액</th>
						<th>1인당 당첨금액</th>
						<th>당첨자수</th>
						<!-- <th>당첨기준</th> -->
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><span class="rank rk1">1등</span></td>
						<td class="rt"><?=Utils::getKorCount($data['lw_1st_prize_tot'])?>원</td>
						<td class="rt"><?=Utils::getKorCount($data['lw_1st_prize_ea'])?>원</td>
						<td class="rt"><?=$data['lw_1st_count']?>명</td>
						<!-- <td class="rt">당첨번호 6개 숫자일치</td> -->
					</tr>
					<tr>
						<td><span class="rank rk2">2등</span></td>
						<td class="rt"><?=Utils::getKorCount($data['lw_2nd_prize_tot'])?>원</td>
						<td class="rt"><?=Utils::getKorCount($data['lw_2nd_prize_ea'])?>원</td>
						<td class="rt"><?=number_format($data['lw_2nd_count'])?>명</td>
						<!-- <td class="rt">당첨번호 5개, +보너스 숫자일치</td> -->
					</tr>
					<tr>
						<td><span class="rank rk3">3등</span></td>
						<td class="rt"><?=Utils::getKorCount($data['lw_3rd_prize_tot'])?>원</td>
						<td class="rt"><?=Utils::getKorCount($data['lw_3rd_prize_ea'])?>원</td>
						<td class="rt"><?=number_format($data['lw_3rd_count'])?>명</td>
						<!-- <td class="rt">당첨번호 5개 숫자일치</td> -->
					</tr>
					<tr>
						<td><span class="rank rk4">4등</span></td>
						<td class="rt"><?=Utils::getKorCount($data['lw_4th_prize_tot'])?>원</td>
						<td class="rt"> <?=Utils::getKorCount($data['lw_4th_prize_ea'])?>원</td>
						<td class="rt"><?=number_format($data['lw_4th_count'])?>명</td>
						<!-- <td class="rt">당첨번호 4개 숫자일치</td> -->
					</tr>
					<tr>
						<td><span class="rank rk5">5등</span></td>
						<td class="rt"><?=Utils::getKorCount($data['lw_5th_prize_tot'])?>원</td>
						<td class="rt"><?=Utils::getKorCount($data['lw_5th_prize_ea'])?>원</td>
						<td class="rt"><?=number_format($data['lw_5th_count'])?>명</td>
						<!-- <td class="rt">당첨번호 3개 숫자일치</td> -->
					</tr>
				</tbody>
			</table>
		</div>
<?
		} else {
			echo "fail";
		}

		break;

	case 'getWinResult':

		// 당첨결과
		if($_POST['inning']) {
			$win_data = $lottoService->getWinData($_POST['inning']);
			$result = $lottoService->getWinResult($_POST['inning']);
			$result = array_slice((array)$result, 0, 3, true);

			// 수동등록 결과
			$lottoWinRecords = new LottoWinRecords();
			$m_win_data = $lottoWinRecords->getWinRecord($_POST['inning']);


?>
			<div class="report_tbl_head">
				<table class="table_st2 table_st">
					<colgroup>
						<col style="width: 60px;">
						<col style="width: 118px;">
						<col style="width: auto;">
					</colgroup>
					<thead>
						<tr>
							<th>순위</th>
							<th>당첨조합</th>
							<th class="rt">총 당첨금액</th>
						</tr>
					</thead>
				</table>
			</div>

			<table class="table_st2 table_st">
			<colgroup>
			<col style="width: 52px;">
			<col style="width: 90px;">
			<col style="width: auto;">
			</colgroup>
			<tbody>
			<?// foreach((array)$result as $key => $value) { ?>
			<?
				$array_prize_idx = array(1 => "lw_1st_prize_ea", 2 => "lw_2nd_prize_ea", 3 => "lw_3rd_prize_ea", 4 => "lw_4th_prize_ea", 5 => "lw_5th_prize_ea");
				for($i=1;$i<=3; $i++) {
			
			?>
			<!-- <tr>
			<td><span class="rank rk<?=$key?>"><?=$key?>등</span></td>
			<td><?=number_format($value['cnt'])?>명</td>
			<td class="rt"><?=number_format($value['prize_tot'])?>원</td>
			</tr> -->
			<tr>
				<td><span class="rank rk<?=$i?>"><?=$i?>등</span></td>
				<!-- <td style="text-align:center"><?=$m_win_data['wr_'.$i.'grade_num'] ? number_format($m_win_data['wr_'.$i.'grade_num']) : number_format($result[$i]['cnt'])?>조합</td> -->
				<td style="text-align:center"><?=$m_win_data['wr_'.$i.'grade_num'] ? number_format($m_win_data['wr_'.$i.'grade_num']) : 0 ?>조합</td>



                                <? if($m_win_data['wr_'.$i.'grade_num']) { ?>
                                <td class="rt"><?=number_format($m_win_data['wr_'.$i.'grade_num']*$win_data[$array_prize_idx[$i]])?>원</td>
                                <? } else { ?>
                                <td class="rt">0원</td>
                                <? }?>


				<!--<? if($m_win_data['wr_'.$i.'grade_num']) { ?>
				<td class="rt"><?=number_format($m_win_data['wr_'.$i.'grade_num']*$win_data[$array_prize_idx[$i]])?>원</td>
				<? } else { ?>
				<td class="rt"><?=$result[$i]['prize_tot'] ? number_format($result[$i]['prize_tot']) : number_format($win_data[$array_prize_idx[$i]])?>원</td>
				<? }?> -->

			</tr>
			<? } ?>
			</tbody>
			</table>
<?

		} else {
			echo "fail";
		}
		break;

	// 미확인 예약 가져오기
	case 'checkUnCheckedAlert':
		$user = new User();

		//▶ get list data
		$data = $user->getCurrentAlerts($_POST['mb_id']);
		echo json_encode($data);

		break;

	case 'getUnsentNoty' :
		$message = new Message();
		$data = $message->getUnSentMessage($_POST['mb_id'], 'noty');

		echo json_encode($data);

		break;

	case 'setSentNoty' :
		$message = new Message();
		$data = $message->setSentMessage($_POST['id']);

		break;


	// 지정번호 추출
	case 'extractInNumbers' :

		//▶ 설정정보 인출
		$lottoServiceConfig = new LottoServiceConfig();
		$config = $lottoServiceConfig->getConfig();

		// 등급별 발급제한 수
		$ext_limit = unserialize($config['lc_user_extract_count']);

		// 이번회차
		$cur_inning = $config['lc_cur_inning']+1;

		// 이용중서비스
		$termService = new TermService();
		$_GET['s_mb_id'] = $_SESSION['ss_mb_id'];
		$row = $termService->getMyService($_SESSION['ss_mb_id']);

		$sg_no = $row[0]['sg_no'];
		if($row[0]['leftDays'] == 0 || !$sg_no) {
			$sg_no = '0';
		}

		$my_limit = $ext_limit[$sg_no];
		
		// 이번회차 이미 추출한 번호 수
		$lottoService = new LottoService();

		$user_fixed_rows = $lottoService->getMyNumbers($cur_inning, $_SESSION['ss_mb_id'], 'user_fixed');
		$user_exclude_rows = $lottoService->getMyNumbers($cur_inning, $_SESSION['ss_mb_id'], 'user_exclude');



		$total_count = count($user_fixed_rows)+count($user_exclude_rows);
		$left_count = $my_limit - $total_count > 0 ? $my_limit - $total_count : 0;

		$num_array = explode(',', $_POST['numbers']);

		$num = array();
		$num['left_count'] = $left_count;

		if($left_count > 0 && $_SESSION['ss_mb_id']) {
			$lotto = new Lotto();

			$config['lc_include_numbers'] = $_POST['numbers'];
			$config['lc_include_rate'] = 100;

			if(count($num_array) > 3) {
				$config['lc_uoddEven_use'] = 0;
				$config['lc_exclude_win_num'] = '3';
				$config['lc_ac_num_use'] = 0;
			}

			sleep(rand(2, 4));

			$lotto->setConfig($config);

			$num['numbers'] = $lotto->generateNumbers();
			if(count($num['numbers']) == 6) {

				// 번호입력
				$lottoService->addNumber($num['numbers'], $_SESSION['ss_mb_id'], 'user_fixed');
				
				$num['result'] = "ok";
			} else {
				$num['result'] = "fail";
			}

			echo json_encode($num);
		} else {
			$num['result'] = "full";
			echo json_encode($num);
		}

		break;

	// 제외번호 추출
	case 'extractOutNumbers' :

		//▶ 설정정보 인출
		$lottoServiceConfig = new LottoServiceConfig();
		$config = $lottoServiceConfig->getConfig();

		// 등급별 발급제한 수
		$ext_limit = unserialize($config['lc_user_extract_count']);

		// 이번회차
		$cur_inning = $config['lc_cur_inning']+1;

		// 이용중서비스
		$termService = new TermService();
		$_GET['s_mb_id'] = $_SESSION['ss_mb_id'];
		$row = $termService->getMyService($_SESSION['ss_mb_id']);

		$sg_no = $row[0]['sg_no'];
		if($row[0]['leftDays'] == 0 || !$sg_no) {
			$sg_no = '0';
		}

		
		$my_limit = $ext_limit[$sg_no];
		
		// 이번회차 이미 추출한 번호 수
		$lottoService = new LottoService();

		$user_fixed_rows = $lottoService->getMyNumbers($cur_inning, $_SESSION['ss_mb_id'], 'user_fixed');
		$user_exclude_rows = $lottoService->getMyNumbers($cur_inning, $_SESSION['ss_mb_id'], 'user_exclude');

		$total_count = count($user_fixed_rows)+count($user_exclude_rows);
		$left_count = $my_limit - $total_count > 0 ? $my_limit - $total_count : 0;

		$num_array = explode(',', $_POST['numbers']);

		$num = array();
		$num['left_count'] = $left_count;

		if($left_count > 0 && $_SESSION['ss_mb_id']) {
			$lotto = new Lotto();

			$config['lc_exclude_numbers'] = $_POST['numbers'];
			$config['lc_exclude_rate'] = 100;

			if(count($num_array) > 3) {
				$config['lc_uoddEven_use'] = 0;
				$config['lc_exclude_win_num'] = '3';
				$config['lc_ac_num_use'] = 0;
			}

			sleep(rand(2, 4));

			$lotto->setConfig($config);

			$num['numbers'] = $lotto->generateNumbers();
			if(count($num['numbers']) == 6) {

				// 번호입력
				$lottoService->addNumber($num['numbers'], $_SESSION['ss_mb_id'], 'user_exclude');
				
				$num['result'] = "ok";
			} else {
				$num['result'] = "fail";
			}

			echo json_encode($num);
		} else {
			$num['result'] = "full";
			echo json_encode($num);
		}

		break;


}
