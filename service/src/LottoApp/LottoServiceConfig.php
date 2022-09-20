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
		

		$extract_count = serialize($_POST['lc_extract_count']);
		$user_extract_count = serialize($_POST['lc_user_extract_count']);
		$send_send_win_result = implode(",", $_POST['lc_send_win_result']);

		$data = Array(
			/*
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
			*/
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

	public function updateExtractorConfig() {


		$this->db->where('sg_no', $_POST['sg_no']);
		$row = $this->db->getOne($this->tb['LottoServiceExtractorConfig']);

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
			'lc_update_date' => $this->db->NOW()
        );

		if($row['lec_no']) {
			$this->db->where('sg_no', $_POST['sg_no']);
			$this->db->update($this->tb['LottoServiceExtractorConfig'], $data);
		} else {
			$data['sg_no'] = $_POST['sg_no'];
			$this->db->insert($this->tb['LottoServiceExtractorConfig'], $data);
		}

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

	// 2020. 1. 2.
    public function getConfig($sg_no='0') {

		// 기본설정 회차, 발송설정관련만
        $data = $this->db->arraybuilder()->getOne($this->tb['LottoServiceConfig']);

		// 추출관련정보
		$this->db->where('sg_no', $sg_no);
		$data_extractor = $this->db->arraybuilder()->getOne($this->tb['LottoServiceExtractorConfig']);
		
		if(is_array($data_extractor)) {
			$data = array_merge($data, $data_extractor);
		}

        return $data;
    }

}