<?php
/**
 * Created by PhpStorm.
 * User: acerunner
 * Date: 16. 2. 2.
 * Time: 오후 1:05
 */

namespace Acesoft\LottoApp;


use Acesoft\Core\Base as Base;
use Acesoft\Core\DB as DB;


class LottoServiceConfig extends Base
{
    public $db;

    /**
     * @param $table
     */
    public function __construct()
    {

        parent::__construct();
        $this->db = DB::getInstance();

		
    }


    public function updateConfig()
    {
		/*
        $data = Array(
            'cf_pdt_ad_price_primium' => str_replace(",", "", $_POST['cf_pdt_ad_price_primium']),
            'cf_pdt_ad_price_focus' => str_replace(",", "", $_POST['cf_pdt_ad_price_focus']),
            'cf_com_ad_price_primium' => str_replace(",", "", $_POST['cf_com_ad_price_primium']),
            'cf_com_ad_price_focus' => str_replace(",", "", $_POST['cf_com_ad_price_focus']),
			'cf_pdt_reg_coin' => str_replace(",", "", $_POST['cf_pdt_reg_coin']),
            'cf_ctg_reg_coin' => str_replace(",", "", $_POST['cf_ctg_reg_coin']),
			'cf_accounts' => $_POST['cf_accounts'],
			'cf_manual_detail' => $_POST['cf_manual_detail'],
			'cf_trade_detail' => $_POST['cf_trade_detail'],
			'cf_ad_online_marketing_detail' => $_POST['cf_ad_online_marketing_detail'],
			'cf_ad_offline_marketing_detail' => $_POST['cf_ad_offline_marketing_detail'],
			'cf_update_date' => $this->db->NOW()
        );

        if($_FILES['watermark']['size'] > 0) {

            $dist_path = $this->config['upload_path'] . "/config";
            if (!is_dir($dist_path)) mkdir($dist_path, 0707);
			$file = "watermark".".".strtolower(array_pop(explode('.', $_FILES['watermark']['name'])));
            $uploaded_file = Utils::uploadFile('watermark', $dist_path . "/"  . $file);

            $data['cf_watermarkImage'] = basename($uploaded_file['destFile']);
        }

		if($_FILES['cf_company_manual_file']['size'] > 0) {

            $dist_path = $this->config['upload_path'] . "/config";
            if (!is_dir($dist_path)) mkdir($dist_path, 0707);
			$file = "세이프넷_기업회원_온라인_이용_메뉴얼".".".strtolower(array_pop(explode('.', $_FILES['cf_company_manual_file']['name'])));
            $uploaded_file = Utils::uploadFile('cf_company_manual_file', $dist_path . "/"  . $file);

            $data['cf_company_manual_file'] = basename($uploaded_file['destFile']);
        }

		if($_FILES['cf_user_manual_file']['size'] > 0) {

            $dist_path = $this->config['upload_path'] . "/config";
            if (!is_dir($dist_path)) mkdir($dist_path, 0707);
			$file = "세이프넷_일반회원_온라인_이용_메뉴얼".".".strtolower(array_pop(explode('.', $_FILES['cf_user_manual_file']['name'])));
            $uploaded_file = Utils::uploadFile('cf_user_manual_file', $dist_path . "/"  . $file);

            $data['cf_user_manual_file'] = basename($uploaded_file['destFile']);
        }
		*/

		$extract_count = serialize($_POST['lc_extract_count']);
		$user_extract_count = serialize($_POST['lc_user_extract_count']);
		//$send_weekdays = implode(",", $_POST['lc_send_weekdays']);
		$send_send_win_result = implode(",", $_POST['lc_send_win_result']);

		$data = Array(
            'lc_exclude_numbers' => $_POST['lc_exclude_numbers'],
            'lc_include_numbers' => $_POST['lc_include_numbers'],
            'lc_exclude_rate' => $_POST['lc_exclude_rate'],
			'lc_include_rate' => $_POST['lc_include_rate'],
			'lc_permit_continue_num' => $_POST['lc_permit_continue_num'],
			'lc_exclude_win_num' => $_POST['lc_exclude_win_num'],
			'lc_permit_ac_num' => $_POST['lc_permit_ac_num'],
			'lc_ac_num_use' => $_POST['lc_ac_num_use'],
			'lc_odd_rate' => $_POST['lc_odd_rate'],
			'lc_even_rate' => $_POST['lc_even_rate'],
			'lc_uoddEven_use' => $_POST['lc_uoddEven_use'],
			'lc_extract_count' => $extract_count,
			'lc_user_extract_count' => $user_extract_count,
			'lc_send_weekdays' => $_POST['lc_send_weekdays'],
			'lc_send_win_result' => $send_send_win_result,
			'lc_numbers_sms_auto' => $_POST['lc_numbers_sms_auto'],
			'lc_winner_sms_auto' => $_POST['lc_winner_sms_auto'],
			'lc_update_date' => $this->db->NOW()
        );

        if ($this->db->update($this->tb['LottoServiceConfig'], $data))
            echo $this->db->count . ' records were updated';
        else
            echo 'update failed: ' . $this->db->getLastError();
    }

	// 회원관련설정 업데이트
	public function updateInning($inning)
    {

        $data = Array(
            'lc_cur_inning' => $inning
        );

        if ($this->db->update($this->tb['LottoServiceConfig'], $data))
            echo $this->db->count . ' records were updated';
        else
            echo 'update failed: ' . $this->db->getLastError();

    }

    public function getConfig() {

        $data = $this->db->arraybuilder()->getOne($this->tb['LottoServiceConfig']);
		
        return $data;
    }

}