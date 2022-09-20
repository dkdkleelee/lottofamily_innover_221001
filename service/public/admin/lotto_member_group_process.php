<?php
$sub_menu = "200100";
/**
 * Created by PhpStorm.
 * Group: acerunner
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
use \Acesoft\LottoApp\Member\Group;
use \Apfelbox\FileDownload\FileDownload;
use \PHPExcel as PHPExcel;



$group = new Group();

auth_check($auth[$sub_menu], 'w');


switch($_POST['proc']) {

    case 'addGroup':
        $group->add();

		Utils::goUrl($_POST['return_url'], "등록 완료 하였습니다.");

        break;

    case 'updateGroup':

        $group->update($_POST['mg_no']);
        Utils::goUrl($_POST['return_url'], "수정 완료 하였습니다.");

        break;


    case 'deleteGroup':

        $group->delete($_POST['mg_no']);
        Utils::goUrl($_POST['return_url']);

        break;
}