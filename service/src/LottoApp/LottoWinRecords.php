<?php

namespace Acesoft\LottoApp;

use \Acesoft\Core\Base;
use \Acesoft\Core\DB;
use \Acesoft\LottoApp\LottoServiceConfig;
use \Acesoft\LottoApp\Lotto;
use \Acesoft\LottoApp\TermService;
use \Acesoft\LottoApp\Member\User;
use \Acesoft\Common\Utils;
use \Acesoft\Common\Message;
use \GuzzleHttp\Client;
use \Symfony\Component\DomCrawler\Crawler;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class LottoWinRecords extends Base
{
	var $lottoServiceConfig;
	var $lotto;
	var $message;

	function __construct() {
    	parent::__construct();
		$this->db = DB::getInstance();

		$this->lottoServiceConfig = new LottoServiceConfig();
    }

	function getList($page=1, $url='') {
		global $pageLimit;

        $pageLimit = $pageLimit ? $pageLimit : 20;

        $page = $page > 0 ? $page : 1;

		if($_GET['sc'] && $_GET['sv']) {
			$this->db->where($_GET['sc']." LIKE '%".$_GET['sv']."%'");
		}

		if($_GET['s_wr_inning']) $this->db->where('wr_inning', $_GET['s_wr_inning']);

		//▶ page rows
		$this->db->pageLimit = $pageLimit;

		//▶ order block
		$this->db->orderBy('wr_inning', 'DESC');

		$list = $this->db->arraybuilder()->paginate($this->tb['LottoWinRecords'], $page, "*");

		$link = Utils::getPagination($this->db->totalPages, $page, $url);

		$data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
		$data['list'] = $list;
		$data['link'] = $link;
		$data['total_count'] = $this->db->totalCount;
		$data['total_pages'] = $this->db->totalPages;
		$data['page'] = $page;
		
		return $data;
	}

	function getWinRecord($inning) {
		$this->db->where('wr_inning', $inning);
		$row = $this->db->getOne($this->tb['LottoWinRecords']);

		return $row;
	}

	function addWinRecord() {

		$row = $this->getWinRecord($_POST['wr_inning']);


		if($row['wr_inning']) {
			Utils::goUrl('', '이미등록된 회차입니다.');
			exit;
		}




		$data = Array(
				'wr_inning' => $_POST['wr_inning'],
				'wr_1grade_num' => $_POST['wr_1grade_num'],
				'wr_2grade_num' => $_POST['wr_2grade_num'],
				'wr_3grade_num' => $_POST['wr_3grade_num'],
				'wr_4grade_num' => $_POST['wr_4grade_num']
			);



		$this->db->insert($this->tb['LottoWinRecords'], $data);

	}

	function modifyWinRecord() {
		$data = Array(
				'wr_1grade_num' => $_POST['wr_1grade_num'],
				'wr_2grade_num' => $_POST['wr_2grade_num'],
				'wr_3grade_num' => $_POST['wr_3grade_num'],
				'wr_4grade_num' => $_POST['wr_4grade_num']
			);

		$this->db->where('wr_inning', $_POST['wr_inning']);
		$this->db->update($this->tb['LottoWinRecords'], $data);
	}

	function deleteWinRecord($inning) {
		$this->db->where('wr_inning', $inning);
		return $this->db->delete($this->tb['LottoWinRecords']);
	}

	function addZeroRecord($inning) {
		
		if($row['wr_inning']) {
			exit;
		}
		
		$data = Array(
                                'wr_inning' => $inning,
                                'wr_1grade_num' => 0,
                                'wr_2grade_num' => 0,
                                'wr_3grade_num' => 0,
                                'wr_4grade_num' => 0
                        );



                $this->db->insert($this->tb['LottoWinRecords'], $data);
	}
}
