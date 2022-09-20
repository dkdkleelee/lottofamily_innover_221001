<?php
/**
 * Created by PhpStorm.
 * User: acerunner
 * Date: 16. 2. 1.
 * Time: 오후 1:19
 */

/*
error_reporting(E_ALL);
ini_set("display_errors", 1);
*/
include_once("./_common.php");

use \Acesoft\Common\Utils as Utils;
use \Acesoft\LottoApp\Member\User as User;
use \Apfelbox\FileDownload\FileDownload;
use \PHPExcel as PHPExcel;


if($_POST['mb_id'] == 'tm5' || $_GET['mb_id'] == 'tm5') {
	echo "<pre>";
	echo print_r($_POST);
	echo "</pre>";
	echo "<pre>";
	echo print_r($_GET);
	echo "</pre>";
	
}

$user = new User();

auth_check($auth[$sub_menu], 'w');


switch($_POST['proc']) {

    case 'addUser':
        $user->addUser();

		Utils::closeWin("등록 완료", '1');
        Utils::goUrl(str_replace("mb_id=", "mb_id=".$_POST['mb_id'], $_POST['return_url']), "등록 완료 하였습니다.", '1');

        break;

    case 'modifyUser':

        $user->modifyUser($_POST['id']);
        Utils::goUrl($_POST['return_url'], "수정 완료 하였습니다.");

        break;

	case 'addMemo':

		$user->addMemo($_POST['mb_id'], $_POST['mo_memo'], $_POST['mo_schedule'], $_POST['mo_schedule_datetime']);
		$user->updateMemoTM($_POST['mb_id'], $_SESSION['ss_mb_id'], $_POST['mo_memo']);
		$user->uploadSimul($_POST['mb_id'], $_FILES['simul_img']);

		Utils::goUrl($_POST['return_url']);

        break;

	case 'addUserWithDetail':
		$user->addUserWithDetail();

		 Utils::goUrl($_POST['url'], "등록 완료 하였습니다.");

        break;

	 case 'modifyUserWithDetail':

        $user->modifyUserWithDetail($_POST['id']);


        Utils::goUrl($_POST['url'], "수정 완료 하였습니다.");

        break;

	// addService
	case 'addService':

		$termService = new TermService();
		$service = $termService->getService($_POST['sc_no']);

		break;

	case 'deleteSimul':
		
		$user->deleteSimul($_POST['mb_id']);
	
		Utils::goUrl($_POST['return_url'], "삭제 완료하였습니다.");
		break;

    case 'deleteUser':

        $user->deleteUser($_POST['id']);
        Utils::goUrl($_POST['url']);

        break;

	case 'deleteSelectedUsers':

		$ids = explode(",", $_POST['ids']);

		if(count($ids) < 400) {
			for($i=0; $i<count($ids); $i++) {
				if($ids[$i]) {
					$user->deleteUser($ids[$i]);
				}
			}

			echo "success";
		} else {
			echo "fail";
		}

		break;

	case 'deleteMemo':

        $user->deleteMemo($_POST['no']);
        Utils::goUrl($_POST['return_url']);

        break;

	
	case 'updateChargerTmSelectedRows':
		$user->updateChargerTmSelectedRows($_POST['chk'], $_POST['mb_charger_tm'], $_POST['tm_changed']);

		echo "success";

		break;

	case 'changeChargerTMs':

		if($mb_tm_id) {
			$ids = explode(",", $_POST['ids']);
			for($i=0; $i<count($ids); $i++) {
				if($ids[$i]) {
					$mb_id = ($ids[$i] == 'na') ? '' : trim($ids[$i]);
					//echo $mb_id."   ".$_POST['mb_tm_id']."<br />";
					$user->updateTM($mb_id, $_POST['mb_tm_id']);
				}
			}
		}

		echo "success";

		break;

	case 'changeConsultStatus':
		if($_POST['consult_status']) {
			$ids = explode(",", $_POST['ids']);
			for($i=0; $i<count($ids); $i++) {
				if($ids[$i]) {
					$_POST['consult_status'] = ($_POST['consult_status'] == 'na') ? '' : trim($_POST['consult_status']);
					//echo $mb_id."   ".$_POST['consult_status']."<br />";

					$user->updateConstulStatus(trim($ids[$i]), $_POST['consult_status']);
				}
			}
		}

		echo "success";

		break;

	case 'changeExtractWeekday':
		if ($_POST['extract_weekday']) {
			$ids = explode(",", $_POST['ids']);
			for($i=0; $i<count($ids); $i++) {
				if($ids[$i]) {
					$user->changeExtractWeekday(trim($ids[$i]), $_POST['extract_weekday']);
				}

			}
		}

		echo "success";

		break;

	case 'initMemo':
		$ids = explode(",", $_POST['ids']);

		for($i=0; $i<count($ids); $i++) {
			if($ids[$i]) {
				$user->deleteMemoById($ids[$i]);
			}
		}
		echo "success";

		break;

	// 회원별 지정발급갯수
	case 'modifyExtractNum':
		$user->updateExtractNum($_POST['mb_id'], $_POST['num']);

		break;

	case 'excelNewUserUpload':

		// progress skin
		include_once("./lotto_member_excel_upload_screen.php");
		echo str_repeat(' ',1024*64);
		flush();
		ob_flush();
		ob_end_flush();

		$user->excelNewUserUpload();

		break;

    case 'unsubscribeUser' :
        $user->unsubscribeUser($_POST['id']);

        Utils::goUrl($_POST['url'], "탈퇴처리 하였습니다.");
        break;

	// 업체 관리자 추가정보 업데이트
	case 'updateCompanyAdditionalInfo':
		$user->updateCompanyAdditionalInfo();

		Utils::goUrl($_POST['url'], "수정 완료 하였습니다.");
		
        break;

	// 업체 상세정보 업데이트
	case 'updateCompanyDetail':
		$user->updateCompanyDetail();

		Utils::goUrl($_POST['url'], "수정 완료 하였습니다.");
        break;


	case "deleteFiles" :
        $files = explode("::",$_POST['files']);

        for($i=0; $i<count($files); $i++) {
            if(is_file($files[$i])) {
                @unlink($files[$i]);
            }
        }

        break;


	case "memberAuth" :
		$user->updateMemberAuth($_POST['id'], $_POST['status']);

		break;

	case "memberAdult" :
		$user->updateMemberAdult($_POST['id'], $_POST['status']);

		break;

	case "getNotAssignedMember":
		$data = $user->getNotAssignedMember($_POST['sdate'], $_POST['edate']);
		
		echo json_encode($data);
		break;

	case "getMembersToAssign":
		if($_POST['type'] == 'distributeMemberToTM') {
			$data = $user->getMembersToDistribute($_POST['sdate'], $_POST['edate']);
		} else if($_POST['type'] == 'redistributeMemberToTM'){
			$data = $user->getMemberToRedistribute($_POST['sdate'], $_POST['edate']);
		}
		
		echo json_encode($data);
		break;

	case "distributeMemberToTM":
		if($user->distributeMemberToTM()) {
			Utils::closeWin('배분완료', true);
		} else {
			Utils::goUrl('',"설정오류");
		}

		break;

	case "redistributeMemberToTM":
		if($user->redistributeMemberToTM()) {
			Utils::closeWin('배분완료', true);
		} else {
			Utils::goUrl('',"설정오류");
		}

		break;

	case "updateMemoDone":
		$mb_id = $user->updateMemoDone($_POST['mo_no']);

		if($mb_id) {
			Utils::goUrl("./lotto_member_regist.php?mb_id=".$mb_id);
		} else {
			Utils::goUrl('',"오류");
		}

		break;

	case "stopUsers":
		$ids = explode(",", $_POST['ids']);

		for($i=0; $i<count($ids); $i++) {
			$user->stopUser($ids[$i]);
		}

		echo "success";

		break;

	case "retireUsers":
		$ids = explode(",", $_POST['ids']);

		for($i=0; $i<count($ids); $i++) {
			$user->unsubscribeUser($ids[$i]);
		}

		echo "success";

		break;
	
	case "startUser":
		$user->startUser($_POST['mb_id']);

		echo "success";

		break;
	case "setNumberNoLogin":
		$ids = explode(",", $_POST['ids']);	
		$le_inning = $_POST['le_inning'];		
		$score = $_POST['score'];
		$num1 = $_POST['lw_num1'];
		$num2 = $_POST['lw_num2'];
		$num3 = $_POST['lw_num3'];
		$num4 = $_POST['lw_num4'];
		$num5 = $_POST['lw_num5'];
		$num6 = $_POST['lw_num6'];
		$num7 = $_POST['lw_num7'];


		for($i=0; $i<count($ids); $i++) {
			$user->SetNumberNoLogin($ids[$i], $le_inning, $score, $num1, $num2, $num3, $num4, $num5, $num6, $num7);
		}
		
		//echo "fail";
		echo "success";
		
		break;
}




switch($_GET['proc']) {
	case 'downloadMemberExcel':
		$user->excelDownload();

		break;

	case 'downloadExcelTemplate':
		$user->excelDownload(true); // true시 템플릿만 받는다

/*
		$fileDownload = FileDownload::createFromFilePath($user->getUploadPath()."/template_files/mailling_member_template.xls");
		$fileDownload->sendDownload("mailling_member_template.xls");
*/

		break;

}
