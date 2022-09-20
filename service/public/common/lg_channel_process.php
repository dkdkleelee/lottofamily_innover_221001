<?php
include_once("./_common.php");

header("Content-Type:text/html;charset=utf-8");

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Lotto;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\TermService;
use Acesoft\Common\Message;

$req_dump = print_r($_REQUEST, TRUE);
//file_put_contents('/home/lottofamily/www/data/log/channel.log', $req_dump.PHP_EOL , FILE_APPEND | LOCK_EX);


switch($_REQUEST['proc']) {

	case 'addChannelMemberAdin' :
		//▶ 설정정보 인출
		$lottoServiceConfig = new LottoServiceConfig();
		$data = $lottoServiceConfig->getConfig();

		//$send_weekday = $data['lc_send_weekdays'] ? $data['lc_send_weekdays'] : 2;
		//$send_weekday = mt_rand(1, 5);
		$send_weekday = '3'; // 미지정회원 목요일 고정

		if($_REQUEST['hp'] && $_REQUEST['channel']) {
		//if($_REQUEST['hp'] && $_REQUEST['name'] ) {

			$tmp = explode("-", $_REQUEST['hp']);

			$mb_id = str_replace("-", "", $_REQUEST['hp']);
			$mb_id = str_replace(",", "", $mb_id);

			$mb_hp = $_REQUEST['hp'];
			$mb_password = substr($mb_id, -4);
//			$mb_name = iconv('euc-kr','utf-8', $_REQUEST['name']);
			$mb_name = ($_REQUEST['name']) ? $_REQUEST['name'] : $mb_id;
			
			$mb_nick = substr($mb_id, 0, 8)."****";

			$mb_channel = $_REQUEST['channel'];
			$mb_channel_referer = $_SERVER['HTTP_REFERER'];

			$row = sql_fetch(" select * from g5_member where mb_id = '{$mb_id}' ");


			if(!$row['mb_id'] && $mb_id) {

				$sql = " insert into {$g5['member_table']}
					set mb_id = '{$mb_id}',
						 mb_password = '".get_encrypt_string($mb_password)."',
						 mb_name = '{$mb_hp}',
						 mb_nick = '{$mb_nick}',
						 mb_nick_date = '".G5_TIME_YMD."',
						 mb_today_login = '".G5_TIME_YMDHIS."',
						 mb_hp = '".$mb_hp."',
						 mb_datetime = '".G5_TIME_YMDHIS."',
						 mb_ip = '{$_SERVER['REMOTE_ADDR']}',
						 mb_level = '{$config['cf_register_level']}',
						 mb_recommend = '{$mb_recommend}',
						 mb_login_ip = '{$_SERVER['REMOTE_ADDR']}',
						 mb_mailling = '{$mb_mailling}',
						 mb_channel = '{$mb_channel}',
						 mb_channel_referer = '{$mb_channel_referer}',
						 mb_media = '{$_REQUEST['media']}',
						 mb_sms = '0',
						 mb_extract_weekday = '".$send_weekday."',
						 mb_update_date = NOW(),
						 mb_open_date = '".G5_TIME_YMD."'";

				sql_query($sql);

				echo "ok";
			} else {
				echo "duplication";
			}

		}

		break;

	case 'addChannelMemberGonplan' :
		//▶ 설정정보 인출
		$lottoServiceConfig = new LottoServiceConfig();
		$data = $lottoServiceConfig->getConfig();

		//$send_weekday = $data['lc_send_weekdays'] ? $data['lc_send_weekdays'] : 2;
		//$send_weekday = mt_rand(1, 5);
		$send_weekday = '3'; // 미지정회원 목요일 고정

		if($_REQUEST['hp'] && $_REQUEST['channel']) {
		//if($_REQUEST['hp'] && $_REQUEST['name'] ) {

			$tmp = explode("-", $_REQUEST['hp']);

			$mb_id = str_replace("-", "", $_REQUEST['hp']);
			$mb_id = str_replace(",", "", $mb_id);

			$mb_hp = $_REQUEST['hp'];
			$mb_password = substr($mb_id, -4);
//			$mb_name = iconv('euc-kr','utf-8', $_REQUEST['name']);
			$mb_name = ($_REQUEST['name']) ? $_REQUEST['name'] : $mb_id;
			
			$mb_nick = substr($mb_id, 0, 8)."****";

			$mb_channel = $_REQUEST['channel'];
			$mb_channel_referer = $_SERVER['HTTP_REFERER'];

			$row = sql_fetch(" select * from g5_member where mb_id = '{$mb_id}' OR mb_hp = '{$mb_id}' ");


			if(!$row['mb_id'] && $mb_id) {

				$sql = " insert into {$g5['member_table']}
					set mb_id = '{$mb_id}',
						 mb_password = '".get_encrypt_string($mb_password)."',
						 mb_name = '{$mb_hp}',
						 mb_nick = '{$mb_nick}',
						 mb_nick_date = '".G5_TIME_YMD."',
						 mb_today_login = '".G5_TIME_YMDHIS."',
						 mb_hp = '".$mb_hp."',
						 mb_datetime = '".G5_TIME_YMDHIS."',
						 mb_ip = '{$_SERVER['REMOTE_ADDR']}',
						 mb_level = '{$config['cf_register_level']}',
						 mb_recommend = '{$mb_recommend}',
						 mb_login_ip = '{$_SERVER['REMOTE_ADDR']}',
						 mb_mailling = '{$mb_mailling}',
						 mb_channel = '{$mb_channel}',
						 mb_channel_referer = '{$mb_channel_referer}',
						 mb_media = '{$_REQUEST['media']}',
						 mb_sms = '0',
						 mb_extract_weekday = '".$send_weekday."',
						 mb_update_date = NOW(),
						 mb_open_date = '".G5_TIME_YMD."'";

				sql_query($sql);

				echo "ok";
			} else {
				echo "duplication";
			}

		}

		break;


        case 'addChannelMemberDUni' :
                //▶ 설정정보 인출
                $lottoServiceConfig = new LottoServiceConfig();
                $data = $lottoServiceConfig->getConfig();

                //$send_weekday = $data['lc_send_weekdays'] ? $data['lc_send_weekdays'] : 4;
                //$send_weekday = mt_rand(1, 5);
                $send_weekday = '4'; // 미지정회원 목요일 고정

                if($_REQUEST['hp'] && $_REQUEST['channel']) {
                //if($_REQUEST['hp'] && $_REQUEST['name'] ) {

                        $tmp = explode("-", $_REQUEST['hp']);

                        $mb_id = str_replace("-", "", $_REQUEST['hp']);
                        $mb_id = str_replace(",", "", $mb_id);

                        $mb_hp = $_REQUEST['hp'];
                        $mb_password = substr($mb_id, -4);
//                      $mb_name = iconv('euc-kr','utf-8', $_REQUEST['name']);
                        $mb_name = ($_REQUEST['name']) ? $_REQUEST['name'] : $mb_id;

                        $mb_nick = substr($mb_id, 0, 8)."****";

                        $mb_channel = $_REQUEST['channel'];
                        $mb_channel_referer = $_SERVER['HTTP_REFERER'];

                        $row = sql_fetch(" select * from g5_member where mb_id = '{$mb_id}' OR mb_hp = '{$mb_id}'");


                        if(!$row['mb_id'] && $mb_id) {

                                $sql = " insert into {$g5['member_table']}
                                        set mb_id = '{$mb_id}',
                                                 mb_password = '".get_encrypt_string($mb_password)."',
                                                 mb_name = '{$mb_hp}',
                                                 mb_nick = '{$mb_nick}',
                                                 mb_nick_date = '".G5_TIME_YMD."',
                                                 mb_today_login = '".G5_TIME_YMDHIS."',
                                                 mb_hp = '".$mb_hp."',
                                                 mb_datetime = '".G5_TIME_YMDHIS."',
                                                 mb_ip = '{$_SERVER['REMOTE_ADDR']}',
                                                 mb_level = '{$config['cf_register_level']}',
                                                 mb_recommend = '{$mb_recommend}',
                                                 mb_login_ip = '{$_SERVER['REMOTE_ADDR']}',
                                                 mb_mailling = '{$mb_mailling}',
                                                 mb_channel = '{$mb_channel}',
                                                 mb_channel_referer = '{$mb_channel_referer}',
                                                 mb_media = '{$_REQUEST['media']}',
                                                 mb_sms = '0',
                                                 mb_extract_weekday = '".$send_weekday."',
                                                 mb_update_date = NOW(),
                                                 mb_open_date = '".G5_TIME_YMD."'";

                                sql_query($sql);

                                echo "ok";
                        } else {
                                echo "duplication";
                        }

                }

                break;

	case 'addChannelMemberBosung' :
                //▶ 설정정보 인출
                $lottoServiceConfig = new LottoServiceConfig();
                $data = $lottoServiceConfig->getConfig();

                //$send_weekday = $data['lc_send_weekdays'] ? $data['lc_send_weekdays'] : 4;
                //$send_weekday = mt_rand(1, 5);
                $send_weekday = '3'; // 미지정회원 목요일 고정

                if($_REQUEST['hp'] && $_REQUEST['channel']) {
                //if($_REQUEST['hp'] && $_REQUEST['name'] ) {

                        $tmp = explode("-", $_REQUEST['hp']);

                        $mb_id = str_replace("-", "", $_REQUEST['hp']);
                        $mb_id = str_replace(",", "", $mb_id);

                        $mb_hp = $_REQUEST['hp'];
                        $mb_password = substr($mb_id, -4);
//                      $mb_name = iconv('euc-kr','utf-8', $_REQUEST['name']);
                        $mb_name = ($_REQUEST['name']) ? $_REQUEST['name'] : $mb_id;

                        $mb_nick = substr($mb_id, 0, 8)."****";

                        $mb_channel = $_REQUEST['channel'];
                        $mb_channel_referer = $_SERVER['HTTP_REFERER'];

                        $row = sql_fetch(" select * from g5_member where mb_id = '{$mb_id}' ");


		if(!$row['mb_id'] && $mb_id) {

                                $sql = " insert into {$g5['member_table']}
                                        set mb_id = '{$mb_id}',
                                                 mb_password = '".get_encrypt_string($mb_password)."',
                                                 mb_name = '{$mb_name}',
                                                 mb_nick = '{$mb_nick}',
                                                 mb_nick_date = '".G5_TIME_YMD."',
                                                 mb_today_login = '".G5_TIME_YMDHIS."',
                                                 mb_hp = '".$mb_hp."',
                                                 mb_datetime = '".G5_TIME_YMDHIS."',
                                                 mb_ip = '{$_SERVER['REMOTE_ADDR']}',
                                                 mb_level = '{$config['cf_register_level']}',
                                                 mb_recommend = '{$mb_recommend}',
                                                 mb_login_ip = '{$_SERVER['REMOTE_ADDR']}',
                                                 mb_mailling = '{$mb_mailling}',
                                                 mb_channel = '{$mb_channel}',
                                                 mb_channel_referer = '{$mb_channel_referer}',
                                                 mb_media = '{$_REQUEST['media']}',
                                                 mb_sms = '0',
                                                 mb_extract_weekday = '".$send_weekday."',
                                                 mb_update_date = NOW(),
                                                 mb_open_date = '".G5_TIME_YMD."'";

                                sql_query($sql);

                                echo "ok";
                        } else {
                                echo "duplication";
                        }

                }

                break;

        case 'addChannelMemberAdfun' :
                //▶ 설정정보 인출
                $lottoServiceConfig = new LottoServiceConfig();
                $data = $lottoServiceConfig->getConfig();

                //$send_weekday = $data['lc_send_weekdays'] ? $data['lc_send_weekdays'] : 4;
                //$send_weekday = mt_rand(1, 5);
                $send_weekday = '3'; // 미지정회원 수요일 고정

                if($_REQUEST['hp'] && $_REQUEST['channel']) {
                //if($_REQUEST['hp'] && $_REQUEST['name'] ) {

                        $tmp = explode("-", $_REQUEST['hp']);

                        $mb_id = str_replace("-", "", $_REQUEST['hp']);
                        $mb_id = str_replace(",", "", $mb_id);

                        $mb_hp = $_REQUEST['hp'];
                        $mb_password = substr($mb_id, -4);
//                      $mb_name = iconv('euc-kr','utf-8', $_REQUEST['name']);
                        $mb_name = ($_REQUEST['name']) ? $_REQUEST['name'] : $mb_id;

                        $mb_nick = substr($mb_id, 0, 8)."****";

                        $mb_channel = $_REQUEST['channel'];
                        $mb_channel_referer = $_SERVER['HTTP_REFERER'];

                        $row = sql_fetch(" select * from g5_member where mb_id = '{$mb_id}' ");


                        if(!$row['mb_id'] && $mb_id) {

                                $sql = " insert into {$g5['member_table']}
                                        set mb_id = '{$mb_id}',
                                                 mb_password = '".get_encrypt_string($mb_password)."',
                                                 mb_name = '{$mb_hp}',
                                                 mb_nick = '{$mb_nick}',
                                                 mb_nick_date = '".G5_TIME_YMD."',
                                                 mb_today_login = '".G5_TIME_YMDHIS."',
                                                 mb_hp = '".$mb_hp."',
                                                 mb_datetime = '".G5_TIME_YMDHIS."',
                                                 mb_ip = '{$_SERVER['REMOTE_ADDR']}',
                                                 mb_level = '{$config['cf_register_level']}',
                                                 mb_recommend = '{$mb_recommend}',
                                                 mb_login_ip = '{$_SERVER['REMOTE_ADDR']}',
                                                 mb_mailling = '{$mb_mailling}',
                                                 mb_channel = '{$mb_channel}',
                                                 mb_channel_referer = '{$mb_channel_referer}',
                                                 mb_media = '{$_REQUEST['media']}',
                                                 mb_sms = '0',
                                                 mb_extract_weekday = '".$send_weekday."',
                                                 mb_update_date = NOW(),
                                                 mb_open_date = '".G5_TIME_YMD."'";

                                sql_query($sql);

                                echo "ok";
                        } else {
                                echo "duplication";
                        }

                }

                break;


}
/*

호출URL : http://lottofamily.co.kr/service/public/common/lg_channel_process.php
호출방식 : POST
인코딩 :   utf-8
이름파라미터 : name
전화번호파라미터 : hp

* 추가 필수 파라메터
채널명파라미터 : channel=BONG
미디어 : media=
기타파라미터 : proc=addChannelMemberBong


*/
