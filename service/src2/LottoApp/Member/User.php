<?php
/**
 * Created by PhpStorm.
 * User: acerunner
 * Date: 16. 2. 11.
 * Time: 오후 1:52
 */

namespace Acesoft\LottoApp\Member;


use \Acesoft\Core\Base as Base;
use \Acesoft\Core\DB as DB;
use \Acesoft\Common\Utils as Utils;
use \Acesoft\Common\Message;
use \Acesoft\LottoApp\Member\Auth as Auth;
use \Acesoft\LottoApp\TermService;
use \Acesoft\LottoApp\LottoService;
use \Acesoft\LottoApp\LottoServiceConfig;
use \Intervention\Image\ImageManager;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class User /*extends Base*/ {

    public $db;
	private static $s_tb;

    public function __construct()
    {
        /*parent::__construct();*/
        $this->db = DB::getInstance();

		self::$s_tb = $this->tb;
    }

	

	// ** 사용자정보 가져오기
	public function getUser($id='', $type=null) {

		if($id) {
			$this->db->where("a.mb_id", $id);

			$this->db->join($this->tb['TermServiceUse']." as b", "a.mb_id=b.mb_id", "LEFT");
			$data = $this->db->arraybuilder()->getOne($this->tb['Member']." as a"," a.*, DATEDIFF(now(), mb_today_login) as lastLoginDays, b.su_no, b.sg_no, b.su_enddate, b.su_pausedate, b.su_startdate, 
				IF(DATEDIFF(su_enddate, now()) > 0, DATEDIFF(su_enddate, now()), 0) as leftDays");

			if(!$data['sg_no'] || $data['leftDays'] == 0) $data['sg_no'] = 0;
		} 

		return $data;
	}

	public function getUserTotalCount($s_tm='') {
		// tm회원의 경우

		if($s_tm) $this->db->where('mb_tm_id', $_GET['s_tm']);
		if($_GET['s_status']) {
			$this->db->where('mb_status', $_GET['s_status']);
		}
		if($_GET['s_not_distributed']) {
			$this->db->where("mb_tm_id IS NULL OR mb_tm_id=''");
		}

		if($_GET['s_distributed']) {
			$this->db->where("mb_tm_id IS NOT NULL AND mb_tm_id <> ''");
		}

		$row = $this->db->arraybuilder()->getOne($this->tb['Member'], "count(*) as cnt");
		return $row['cnt'];
	}

	public function getUserWithNoLoginCount($le_inning, $lwr_grade) {
                // tm회원의 경우
		if ($lwr_grade != 4) {
			$this->db->where("mb_today_login < le_issued_date AND e.sg_no IS NULL AND cnt = 0 AND cnt_inning != 0 AND NOT mb_cousult_status IN ('정지') AND mb_status = 1");
		}
		else {
			$this->db->where("mb_today_login < le_issued_date AND e.sg_no IS NULL AND cnt <= 1 AND cnt_inning != 0 AND NOT mb_cousult_status IN ('정지') AND mb_status = 1");
		}
		$this->db->join("(SELECT mb_id, le_issued_date, COUNT(IF(le_inning = ".$le_inning.", mb_id, null)) as cnt_inning, COUNT(IF(le_result_grade = ".$lwr_grade.", mb_id, null)) as cnt FROM ".$this->tb['LottoNumbers']." GROUP BY mb_id) d", "a.mb_id = d.mb_id", "LEFT");
                $row = $this->db->arraybuilder()->getOne("(SELECT mb_id, mb_today_login, mb_cousult_status, mb_status FROM ".$this->tb['Member'].") a LEFT JOIN ".$this->tb['TermServiceUse']." as e ON(a.mb_id = e.mb_id)", "count(*) as cnts");
                return $row['cnts'];
        }

	/*
        public function getUserWithNoLoginWinCount($s_tm='') {
                // tm회원의 경우

                $this->db->where("b.sg_no IS NULL AND c > 0 AND d.mb_today_login < DATE_ADD(str_to_date((select lw_date from lotto_win_numbers order by lw_inning desc limit 1), '%Y-%m-%d'), INTERVAL 1245 MINUTE)");

                $row = $this->db->arraybuilder()->getOne("(SELECT mb_id, COUNT(if(le_result_grade = 1 OR le_result_grade = 2 OR le_result_grade = 3, mb_id, null)) as c FROM ".$this->tb['LottoNumbers']." GROUP BY mb_id) a LEFT JOIN ".$this->tb['TermServiceUse']." b ON(a.mb_id = b.mb_id) LEFT JOIN ".$this->tb['Member']." as d ON(a.mb_id = d.mb_id)", "count(*) as cnt");
                return $row['cnt'];
        }
	*/


    public function getUserList($page=1, $url='') {
		global $pageLimit;

        $page = $page > 0 ? $page : 1;


		if($_GET['s_mb_level']) {
			$this->db->where('mb_level', $_GET['s_mb_level']);
		}

		if($_GET['sc'] && $_GET['sv']) {
            $this->db->where($_GET['sc'], '%'.$_GET['sv'].'%', 'like');
        }

        $this->db->pageLimit = $pageLimit ? $pageLimit : 20;
        $this->db->orderBy('mb_datetime', 'DESC');
        $list = $this->db->arraybuilder()->withTotalCount()->paginate($this->tb['Member']." as a", $page);

        $link = Utils::getPagination($this->db->totalPages, $page, $url);

		$data['total_count'] = $this->db->totalCount;
		$data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
        $data['list'] = $list;
        $data['link'] = $link;

        return $data;
    }

	public function getUserListWithService($page=1, $url='') {
		global $pageLimit;

        $page = $page > 0 ? $page : 1;

		$lottoServiceConfig = new LottoServiceConfig();
		$serviceConfig = $lottoServiceConfig->getConfig();

		if($_GET['s_status']) {
			$this->db->where('mb_status', $_GET['s_status']);
		}

		if($_GET['s_mb_level']) {
			$this->db->where('mb_level', $this->db->escape($_GET['s_mb_level']));
		}

		if($_GET['s_tm']) {
			$this->db->where("mb_tm_id", $this->db->escape($_GET['s_tm']));
		}

		if($_GET['s_not_distributed']) {
			$this->db->where(" (mb_level < 3 && (mb_tm_id IS NULL OR mb_tm_id='')) ");
		}

		if($_GET['s_distributed']) {
			$this->db->where(" ((mb_tm_id IS NOT NULL AND mb_tm_id <> '') OR mb_level > 2) ");
		}

		if($_GET['s_sms'] != '') {
			$this->db->where("mb_sms", $_GET['s_sms']);
		}

		if($_GET['s_sg_no']) {
			if($_GET['s_sg_no'] == 'normal') {
				$this->db->where('b.sg_no IS NULL');
			} else {
				$this->db->where('b.sg_no', $this->db->escape($_GET['s_sg_no']));
			}
		} else {
			$join_group = "group by mb_id";
		}

		if($_GET['s_mb_channel']) {
			if($_GET['s_mb_channel'] == 'common') {
				$this->db->where("(a.mb_channel IS NULL OR a.mb_channel = '')");
			} else {
				$this->db->where('a.mb_channel', $this->db->escape($_GET['s_mb_channel']));
			}
		}

		if($_GET['s_mb_extract_weekday'] != '') {
			$this->db->where('mb_extract_weekday', $_GET['s_mb_extract_weekday']);
		}

		if($_GET['s_mb_media']) {
			if($_GET['s_mb_media'] == 'common') {
				$this->db->where("(a.mb_media IS NULL OR a.mb_media = '')");
			} else {
		$list = $this->db->arraybuilder()->withTotalCount()->paginate($this->tb['Member']." as a", $page, "a.*, IF(DATEDIFF(su_enddate, now()) > 0, DATEDIFF(su_enddate, now()), 0) as leftDays, DATEDIFF(now(), mb_today_login) as lastLoginDays,  IF(su_pausedate is NULL, 0, 1) as paused, GROUP_CONCAT(c.lwr_grade SEPARATOR ',') as win_records, d.*");
		$this->db->where('a.mb_media', $this->db->escape($_GET['s_mb_media']));
			}
		}

		if($_GET['s_date']) {
			$this->db->where("DATE_FORMAT(a.mb_datetime, '%Y-%m-%d') >= DATE_FORMAT('".$this->db->escape($_GET['s_date'])."', '%Y-%m-%d')");
		}


		if($_GET['e_date']) {
			$this->db->where("DATE_FORMAT(a.mb_datetime, '%Y-%m-%d') <= DATE_FORMAT('".$this->db->escape($_GET['e_date'])."', '%Y-%m-%d')");
		}

		if($_GET['s_rd_date']) {
			$this->db->where("DATE_FORMAT(a.mb_distribute_date, '%Y-%m-%d') >= DATE_FORMAT('".$this->db->escape($_GET['s_rd_date'])."', '%Y-%m-%d')");

		}

		if($_GET['e_rd_date']) {
			$this->db->where("DATE_FORMAT(a.mb_distribute_date, '%Y-%m-%d') <= DATE_FORMAT('".$this->db->escape($_GET['e_rd_date'])."', '%Y-%m-%d')");
		}


		// 담당TM
		if($_GET['s_mb_charger_tm'] != '') {
			if($_GET['s_mb_charger_tm'] == 'na') {
				$this->db->where("(a.mb_tm_id IS NULL OR a.mb_tm_id = '')");
			} else {
				$this->db->where('a.mb_tm_id', $_GET['s_mb_charger_tm']);
			}
		}

		// 상태
		if($_GET['s_mb_cousult_status']) {
			if($_GET['s_mb_cousult_status'] == 'na') {
				$this->db->where("(a.mb_cousult_status IS NULL OR a.mb_cousult_status = '')");
			} else {
				$this->db->where('a.mb_cousult_status', $_GET['s_mb_cousult_status']);
			}
		}

		// 등수
		if($_GET['s_lwr_grade']) {
			$this->db->where("c.lwr_grade='".$_GET['s_lwr_grade']."'"); // test 2019-07-25 invaderx 등수검색
		}

		if($_GET['sc'] && $_GET['sv']) {
			switch($_GET['sc']) {

				case 'mo_memo' :
					// 메모검색 조인
					$this->db->join("(SELECT m.mb_id, m.mo_memo FROM ".$this->tb['Memo']." as m WHERE mo_memo LIKE '%".$_GET['sv']."%') as d", "d.mb_id=a.mb_id", "LEFT");
					$this->db->where("d.mb_id is not NULL");
					break;

				default:
		            $this->db->where("REPLACE(".$_GET['sc'].",'-','')", '%'.str_replace('-','',$_GET['sv']).'%', 'like');
			}
        }



		$this->db->join("(SELECT x.mb_id, x.su_enddate, x.su_pausedate, x.su_no, x.sg_no, y.sg_name FROM ".$this->tb['TermServiceUse']." as x LEFT JOIN ".$this->tb['TermServiceGrade']." as y ON(x.sg_no=y.sg_no) WHERE su_enddate >= NOW() $join_group) as b", "a.mb_id=b.mb_id", "LEFT");

		// 당첨결과 조인
		//$this->db->join("(SELECT w.mb_id, w.lwr_grade, w.lwr_inning FROM ".$this->tb['LottoNumbersWin']." as w WHERE lwr_inning='".$serviceConfig['lc_cur_inning']."') as c", "c.mb_id=a.mb_id", "LEFT");
		$this->db->join($this->tb['LottoNumbersWin']." as c", "c.mb_id=a.mb_id AND c.lwr_inning='".$serviceConfig['lc_cur_inning']."'", "LEFT");


		// 그룹조인
		$this->db->join($this->tb['Group']." as d", "a.mg_no=d.mg_no", "LEFT");

        //$this->db->pageLimit = $pageLimit ? $pageLimit : 20;
		$this->db->pageLimit = $_GET['s_pageLimit'] ? $_GET['s_pageLimit'] : 20;
		$this->db->groupBy('a.mb_id');

		switch($_GET['s_order']) {
			case '1':
				$this->db->orderBy('mb_datetime', 'DESC');
				break;

			case '2':
				$this->db->orderBy('mb_today_login', 'DESC');

				break;

			default:
				$this->db->orderBy('mb_update_date', 'DESC');
				$this->db->orderBy('mb_datetime', 'DESC');
				break;
		}

        $list = $this->db->arraybuilder()->withTotalCount()->paginate($this->tb['Member']." as a", $page, "a.*, IF(DATEDIFF(su_enddate, now()) > 0, DATEDIFF(su_enddate, now()), 0) as leftDays, DATEDIFF(now(), mb_today_login) as lastLoginDays,  IF(su_pausedate is NULL, 0, 1) as paused, GROUP_CONCAT(c.lwr_grade SEPARATOR ',') as win_records, d.*");

        $link = Utils::getPagination($this->db->totalPages, $page, $url);

		$data['total_count'] = $this->db->totalCount;
		$data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
        $data['list'] = $list;
        $data['link'] = $link;

        return $data;
    }

	public function getUserListWithServiceForTM($page=1, $url='') {
		global $pageLimit;

        $page = $page > 0 ? $page : 1;

		$lottoServiceConfig = new LottoServiceConfig();
		$serviceConfig = $lottoServiceConfig->getConfig();

		if($_GET['s_status']) {
			$this->db->where('mb_status', $_GET['s_status']);
		}

		if($_GET['s_mb_level']) {
			$this->db->where('mb_level', $this->db->escape($_GET['s_mb_level']));
		}

		if($_GET['s_mb_charger_tm']) {
			$this->db->where("mb_tm_id", $_GET['s_mb_charger_tm']);
		}

		if($_GET['s_mb_channel']) {
			if($_GET['s_mb_channel'] == 'common') {
				$this->db->where("(a.mb_channel IS NULL OR a.mb_channel = '')");
			} else {
				$this->db->where('a.mb_channel', $this->db->escape($_GET['s_mb_channel']));
			}
		}

		if($_GET['s_mb_extract_weekday'] != '') {
			$this->db->where('mb_extract_weekday', $_GET['s_mb_extract_weekday']);
		}

		// 상태
		if($_GET['s_mb_cousult_status']) {
			if($_GET['s_mb_cousult_status'] == 'na') {
				$this->db->where("(a.mb_cousult_status IS NULL OR a.mb_cousult_status = '')");
			} else {
				$this->db->where('a.mb_cousult_status', $this->db->escape($_GET['s_mb_cousult_status']));
			}
		}

		if($_GET['s_distributed']) {
			$this->db->where("mb_tm_id IS NOT NULL AND mb_tm_id <> ''");
		}


		if($_GET['s_date']) {
			$this->db->where("DATE_FORMAT(a.mb_datetime, '%Y-%m-%d') >= DATE_FORMAT('".$this->db->escape($_GET['s_date'])."', '%Y-%m-%d')");
		}

		if($_GET['e_date']) {
			$this->db->where("DATE_FORMAT(a.mb_datetime, '%Y-%m-%d') <= DATE_FORMAT('".$this->db->escape($_GET['e_date'])."', '%Y-%m-%d')");
		}

		if($_GET['s_rd_date']) {

			$this->db->where("DATE_FORMAT(a.mb_distribute_date, '%Y-%m-%d') >= DATE_FORMAT('".$this->db->escape($_GET['s_rd_date'])."', '%Y-%m-%d')");
		}

		if($_GET['e_rd_date']) {
			$this->db->where("DATE_FORMAT(a.mb_distribute_date, '%Y-%m-%d') <= DATE_FORMAT('".$this->db->escape($_GET['e_rd_date'])."', '%Y-%m-%d')");
		}

		

		if(isset($_GET['s_sg_no']) && $_GET['s_sg_no']) {
			if($_GET['s_sg_no'] == 'normal') {
				$this->db->where('b.sg_no IS NULL');
			} else {
				$this->db->where('b.sg_no', $_GET['s_sg_no']);
			}
		} else {
			$join_group = "group by mb_id";
		}

		if($_GET['sc'] && $_GET['sv']) {
			switch($_GET['sc']) {

				case 'mo_memo' :
					// 메모검색 조인
					$this->db->join("(SELECT m.mb_id, m.mo_memo FROM ".$this->tb['Memo']." as m WHERE mo_memo LIKE '%".$this->db->escape($_GET['sv'])."%') as d", "d.mb_id=a.mb_id", "LEFT");
					$this->db->where("d.mb_id is not NULL");
					break;

				default:
					$this->db->where("REPLACE(".$this->db->escape($_GET['sc']).",'-','')", '%'.str_replace('-','',$_GET['sv']).'%', 'like');
			}
            
        } else {
			//$this->db->where("mb_tm_id", $_SESSION['ss_mb_id']);
		}

		// 상위레벨 제한
		$this->db->where("mb_level < 3");


		if($_GET['s_lwr_grade']) {
			$this->db->where("c.lwr_grade='".$_GET['s_lwr_grade']."'"); // test 2019-07-25 invaderx 등수검색
		}

		$this->db->join("(SELECT x.mb_id, x.su_enddate, x.su_pausedate, x.su_no, x.sg_no, y.sg_name FROM ".$this->tb['TermServiceUse']." as x LEFT JOIN ".$this->tb['TermServiceGrade']." as y ON(x.sg_no=y.sg_no) WHERE su_enddate >= NOW() $join_group) as b", "a.mb_id=b.mb_id", "LEFT");

		// 당첨결과 조인
		//$this->db->join("(SELECT w.mb_id, w.lwr_grade, w.lwr_inning FROM ".$this->tb['LottoNumbersWin']." as w WHERE lwr_inning='".$serviceConfig['lc_cur_inning']."') as c", "c.mb_id=a.mb_id", "LEFT");
		$this->db->join($this->tb['LottoNumbersWin']." as c", "c.mb_id=a.mb_id AND c.lwr_inning='".$serviceConfig['lc_cur_inning']."'", "LEFT");

	$this->db->pageLimit = $_GET['s_pageLimit'] ? $_GET['s_pageLimit'] : 20;
        //$this->db->pageLimit = $pageLimit ? $pageLimit : 20;
		$this->db->groupBy('a.mb_id');
        switch($_GET['s_order']) {
			case '1':
				$this->db->orderBy('mb_datetime', 'DESC');
				break;

			case '2':
				$this->db->orderBy('mb_today_login', 'DESC');
				break;

			default:
				$this->db->orderBy('mb_update_date', 'DESC');
				$this->db->orderBy('mb_datetime', 'DESC');
				break;
		}
        $list = $this->db->arraybuilder()->withTotalCount()->paginate($this->tb['Member']." as a", $page, "a.*, IF(DATEDIFF(su_enddate, now()) > 0, DATEDIFF(su_enddate, now()), 0) as leftDays, IF(su_pausedate is NULL, 0, 1) as paused, DATEDIFF(now(), mb_today_login) as lastLoginDays, GROUP_CONCAT(c.lwr_grade SEPARATOR ',') as win_records");


        $link = Utils::getPagination($this->db->totalPages, $page, $url);

		$data['total_count'] = $this->db->totalCount;
		$data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
        $data['list'] = $list;
        $data['link'] = $link;

        return $data;
    }

//미확인회원 3등이상 미당첨

public function getUserListWithNoLogin ($page=1, $url='', $le_inning, $lwr_grade) {
                //global $pageLimit;

        $page = $page > 0 ? $page : 1;

                $lottoServiceConfig = new LottoServiceConfig();
                $serviceConfig = $lottoServiceConfig->getConfig();
		
		/*
                if($_GET['s_status']) {
                        $this->db->where('mb_status', $_GET['s_status']);
                }
		*/

                if($_GET['s_mb_level']) {
                        $this->db->where('mb_level', $this->db->escape($_GET['s_mb_level']));
                }

                if($_GET['s_tm']) {
                        $this->db->where("mb_tm_id", $this->db->escape($_GET['s_tm']));
                }
		/*
                if($_GET['s_not_distributed']) {
                        $this->db->where(" (mb_level < 3 && (mb_tm_id IS NULL OR mb_tm_id='')) ");
                }

                if($_GET['s_distributed']) {
                        $this->db->where(" ((mb_tm_id IS NOT NULL AND mb_tm_id <> '') OR mb_level > 2) ");
                }
                */

                if($_GET['s_sms'] != '') {
                        $this->db->where("mb_sms", $_GET['s_sms']);
                }

		/*
                if($_GET['s_sg_no']) {
                        if($_GET['s_sg_no'] == 'normal') {
                                $this->db->where('b.sg_no IS NULL');
                        //$this->db->where("c.le_result_grade <= 3 AND c.le_result_grade >= 1");
                }
                        } else {
     if($_GET['s_nologin']) {
                        $this->db->where('b.sg_no IS NULL OR (NOT c.lwr_grade IN (1,2,3))');
                           $this->db->where('b.sg_no', $this->db->escape($_GET['s_sg_no']));
                        }
                } else {
                        $join_group = "group by mb_id";
                }
		*/

		//if($_GET['s_nologin']) {
		if ($lwr_grade != 4) {
			$this->db->where("(mb_today_login < le_issued_date OR le_issued_date IS NULL) AND c.cnt = 0 AND e.cnt_inning != 0 AND b.sg_no IS NULL AND NOT a.mb_cousult_status IN ('정지') AND a.mb_status = 1");
		}
		else {
			$this->db->where("(mb_today_login < le_issued_date OR le_issued_date IS NULL) AND c.cnt <= 1 AND e.cnt_inning != 0 AND b.sg_no IS NULL AND NOT a.mb_cousult_status IN ('정지') AND a.mb_status = 1");
		}
		//$this->db->where('b.sg_no IS NULL OR (NOT c.lwr_grade IN (1,2,3))');
			//$this->db->where("c.le_result_grade <= 3 AND c.le_result_grade >= 1");	
		//}

	        if($_GET['s_mb_channel']) {
                        if($_GET['s_mb_channel'] == 'common') {
                                $this->db->where("(a.mb_channel IS NULL OR a.mb_channel = '')");
                        } else {
                                $this->db->where('a.mb_channel', $this->db->escape($_GET['s_mb_channel']));
                        }
                }

                if($_GET['s_mb_extract_weekday'] != '') {
                        $this->db->where('mb_extract_weekday', $_GET['s_mb_extract_weekday']);
                }

                if($_GET['s_mb_media']) {
                        if($_GET['s_mb_media'] == 'common') {
                                $this->db->where("(a.mb_media IS NULL OR a.mb_media = '')");
                        } else {
                                $this->db->where('a.mb_media', $this->db->escape($_GET['s_mb_media']));
                        }
                }

                if($_GET['s_date']) {
                        $this->db->where("DATE_FORMAT(a.mb_datetime, '%Y-%m-%d') >= DATE_FORMAT('".$this->db->escape($_GET['s_date'])."', '%Y-%m-%d')");
                }

                if($_GET['e_date']) {
                        $this->db->where("DATE_FORMAT(a.mb_datetime, '%Y-%m-%d') <= DATE_FORMAT('".$this->db->escape($_GET['e_date'])."', '%Y-%m-%d')");
                }

                if($_GET['s_rd_date']) {
                        $this->db->where("DATE_FORMAT(a.mb_distribute_date, '%Y-%m-%d') >= DATE_FORMAT('".$this->db->escape($_GET['s_rd_date'])."', '%Y-%m-%d')");
                }

                if($_GET['e_rd_date']) {
                        $this->db->where("DATE_FORMAT(a.mb_distribute_date, '%Y-%m-%d') <= DATE_FORMAT('".$this->db->escape($_GET['e_rd_date'])."', '%Y-%m-%d')");
                }


                // 담당TM
                if($_GET['s_mb_charger_tm'] != '') {
                        if($_GET['s_mb_charger_tm'] == 'na') {
                                $this->db->where("(a.mb_tm_id IS NULL OR a.mb_tm_id = '')");
                        } else {
                                $this->db->where('a.mb_tm_id', $_GET['s_mb_charger_tm']);
                        }
                }

                // 상태
                if($_GET['s_mb_cousult_status']) {
                        if($_GET['s_mb_cousult_status'] == 'na') {
                                $this->db->where("(a.mb_cousult_status IS NULL OR a.mb_cousult_status = '')");
                        } else {
                                $this->db->where('a.mb_cousult_status', $_GET['s_mb_cousult_status']);
                        }
                }

                // 등수
                //if($_GET['s_lwr_grade']) {
                //        $this->db->where("c.lwr_grade='".$_GET['s_lwr_grade']."'"); // test 2019-07-25 invaderx 등수검색
                //}

                if($_GET['sc'] && $_GET['sv']) {
                        switch($_GET['sc']) {

                                case 'mo_memo' :
                                        // 메모검색 조인
                                        $this->db->join("(SELECT m.mb_id, m.mo_memo FROM ".$this->tb['Memo']." as m WHERE mo_memo LIKE '%".$_GET['sv']."%') as d", "d.mb_id=a.mb_id", "LEFT");
                                        $this->db->where("d.mb_id is not NULL");
                                        break;

                                default:
                            $this->db->where("REPLACE(".$_GET['sc'].",'-','')", '%'.str_replace('-','',$_GET['sv']).'%', 'like');
                        }
        }



                $this->db->join("(SELECT x.mb_id, x.su_enddate, x.su_pausedate, x.su_no, x.sg_no, y.sg_name FROM ".$this->tb['TermServiceUse']." as x LEFT JOIN ".$this->tb['TermServiceGrade']." as y ON(x.sg_no=y.sg_no) WHERE su_enddate >= NOW() $join_group) as b", "a.mb_id=b.mb_id", "LEFT");

                // 당첨결과 조인
                //$this->db->join("(SELECT w.mb_id, w.lwr_grade, w.lwr_inning FROM ".$this->tb['LottoNumbersWin']." as w WHERE lwr_inning='".$serviceConfig['lc_cur_inning']."') as c", "c.mb_id=a.mb_id", "LEFT");
                $this->db->join("(SELECT mb_id, COUNT(IF(le_result_grade = ".$lwr_grade.", mb_id, null)) as cnt FROM ".$this->tb['LottoNumbers']." GROUP BY mb_id) as c", "c.mb_id=a.mb_id", "LEFT");
		
                // 그룹조인
                $this->db->join($this->tb['Group']." as d", "a.mg_no=d.mg_no", "LEFT");

		$this->db->pageLimit = $_GET['s_pageLimit'] ? $_GET['s_pageLimit'] : 20;

        //$this->db->pageLimit = $pageLimit ? $pageLimit : 20;
                $this->db->groupBy('a.mb_id');

                switch($_GET['s_order']) {
                        case '1':
                                $this->db->orderBy('mb_datetime', 'DESC');
                                break;

                        case '2':
                                $this->db->orderBy('mb_today_login', 'DESC');
                                break;

                        default:
                                $this->db->orderBy('mb_update_date', 'DESC');
                                $this->db->orderBy('mb_datetime', 'DESC');
                                break;
                }

	$this->db->join("(SELECT mb_id, le_issued_date, le_result_grade, COUNT(IF(le_inning = ".$le_inning.", mb_id, null)) as cnt_inning, COUNT(IF(le_result_grade = ".$lwr_grade.", mb_id, null)) as cnt FROM ".$this->tb['LottoNumbers']." where le_inning = ".$le_inning." GROUP BY mb_id) as e", "a.mb_id=e.mb_id", "LEFT");

	//$this->db->join("SELECT COUNT(IF(le_result_grade = ".$this->tb['TermServiceUse']." as f", "a.mb_id = f.mb_id", "LEFT");

        $list = $this->db->arraybuilder()->withTotalCount()->paginate($this->tb['Member']." as a", $page, "a.*, IF(DATEDIFF(b.su_enddate, now()) > 0, DATEDIFF(b.su_enddate, now()), 0) as leftDays, DATEDIFF(now(), mb_today_login) as lastLoginDays,  IF(b.su_pausedate is NULL, 0, 1) as paused, GROUP_CONCAT(e.le_result_grade SEPARATOR ',') as win_records, d.*");

        $link = Utils::getPagination($this->db->totalPages, $page, $url);

                $data['total_count'] = $this->db->totalCount;
                $data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
        $data['list'] = $list;
        $data['link'] = $link;

        return $data;
    }

	public function getTMList() {
		
		$this->db->where("mb_level >= 3");
		$this->db->where('mb_status', 1);
		$list = $this->db->arraybuilder()->get($this->tb['Member']." as a", null, '*');
		return $list;
	}


	public static function getMailingUser() {

		$db = DB::getInstance();

		if(is_array($_POST['member_type'])) {
			if(in_array('1', $_POST['member_type'])) {
				$where[] = "mb_type='company'";
				//$db->orwhere("mb_type", "company");
			}

			if(in_array('2', $_POST['member_type'])) {
				$where[] = "mb_type='individual'";
				//$db->orwhere("mb_type", "individual");
			}

			if(in_array('3', $_POST['member_type'])) {
				$where[] = "mb_mailling_member='1'";
				//$db->orwhere("mb_mailling_member", "1");
			} else {
				$db->where("mb_mailling_member", "0");
			}

			if(count($where)) {
				$db->where("(".implode(" OR ", $where).")");
			}
		}

		if(is_array($_POST['interest'])) {
			for($i=0; $i<count($_POST['interest']); $i++) {
				$where_interest[] = "FIND_IN_SET(".$_POST['interest'][$i].", mb_interest) > 0";
				
			}

			if(count($where_interest)) {
				$db->where("(".implode(" OR ", $where_interest).")");
			}
		}

		if($_POST['s_mb_com_detail_sort']) {
			$db->where("mb_com_detail_sort", $_POST['s_mb_com_detail_sort']);
		}

		if($_POST['agree_type'] == '1') {
			$db->where("mb_mailling", "1");
		}

		$db->where("mb_email <> ''");

		$db->groupBy('mb_email');

		$list = $db->get($this->tb['Member']);

		return $list;

	}

	public function addUser() {

		if($_POST['mb_mailling_member'] == '1') {
			$mb_id = Utils::getRandomString();
		} else {
			if(!$_POST['mb_id']) {
				Utils::goUrl("아이디를 입력해 주세요.");
			} else {
				$mb_id = $_POST['mb_id'];
				
			}
		}

		if($_POST['mb_addr1']) {
			$geo_location = \Acesoft\Common\Area::getGeoLocation($_POST['mb_addr1']);

			$mb_lat = $geo_location['lat'];
			$mb_lng = $geo_location['lng'];
		}

		$query = " INSERT INTO ".$this->tb['Member']."
							set
								mb_id = '".$mb_id."',
								mg_no = '".$_POST['mg_no']."',
								mb_password=password('".$_POST['mb_password']."'),
								mb_name = '".$_POST['mb_name']."',
								mb_email = '".$_POST['mb_email']."',
								mb_hp = '".$_POST['mb_hp1']."-".$_POST['mb_hp2']."-".$_POST['mb_hp3']."',
								
								mb_tel = '".$_POST['mb_tel1']."-".$_POST['mb_tel2']."-".$_POST['mb_tel3']."',
								mb_zip1 = '".substr(trim($_POST['mb_zip']), 0, 3)."',
								mb_zip2 = '".substr(trim($_POST['mb_zip']), 3)."',
								mb_addr1 = '".$_POST['mb_addr1']."',
								mb_addr2 = '".$_POST['mb_addr2']."',
								mb_addr3 = '".$_POST['mb_addr3']."',
								mb_addr_jibeon = '".$_POST['mb_addr_jibeon']."',
								mb_birth = '".$_POST['mb_birth']."',
								mb_memo = '".$_POST['mb_memo']."',
								mb_level = '".$_POST['mb_level']."',
								mb_extract_weekday = '".$_POST['mb_extract_weekday']."',
								mb_tm_id = '".$_POST['mb_tm_id']."',
								mb_cousult_status = '".$_POST['mb_cousult_status']."',
								mb_sms = '".$_POST['mb_sms']."',
								mb_datetime = NOW(),
								mb_update_date=NOW()";

		$this->db->rawQuery($query);

	}

	
	public function modifyUser() {


		$user = $this->getUser($_POST['mb_id']);
//		if(trim($_POST['mb_memo']) && trim($user['mb_memo']) != trim($_POST['mb_memo'])) {
//			$_POST['mb_memo'] = $_POST['mb_memo'].PHP_EOL.date("Y-m-d H:i:s").'['.$_SESSION['ss_mb_id'].']'.PHP_EOL."--------------------------------".PHP_EOL.PHP_EOL;
//		}

/*		별도 분리
		if($_POST['mo_memo']) {
			$this->addMemo($user['mb_id'], $_POST['mo_memo'], $_POST['mo_schedule'], $_POST['mo_schedule_datetime']);

		}
*/

		$data = Array (
			'mg_no' => $_POST['mg_no'],
			'mb_level' => $_POST['mb_level'],
			'mb_name' => $_POST['mb_name'],
			'mb_email' => $_POST['mb_email'],
			'mb_hp' => $_POST['mb_hp1']."-".$_POST['mb_hp2']."-".$_POST['mb_hp3'],
			'mb_tel' => $_POST['mb_tel1']."-".$_POST['mb_tel2']."-".$_POST['mb_tel3'],
			
			'mb_zip1' => substr(trim($_POST['mb_zip']), 0, 3),
			'mb_zip2' => substr(trim($_POST['mb_zip']), 3),
			'mb_addr1' => $_POST['mb_addr1'],
			'mb_addr2' => $_POST['mb_addr2'],
			'mb_addr3' => $_POST['mb_addr3'],
			'mb_addr_jibeon' => $_POST['mb_addr_jibeon'],
			'mb_mailling' => $_POST['mb_mailling'],
			'mb_sms' => $_POST['mb_sms'],
			'mb_extract_weekday' => $_POST['mb_extract_weekday'],
			'mb_memo' => $_POST['mb_memo'],
			'mb_birth' => $_POST['mb_birth'],
			'mb_tm_id' => $_POST['mb_tm_id'],
			'mb_cousult_status' => $_POST['mb_cousult_status']
//			'mb_update_date' => $this->db->now()
        );

		// 패스워드
		if(trim($_POST['mb_password'])) {
			
			$password = $this->db->rawQueryOne("SELECT password('".$_POST['mb_password']."') as new");
			if($user['mb_password'] != $password['new']) {
				$data['mb_password'] = $password['new'];
			}
		}

	

		// 사업자등록증
		// 업로드경로지정
        $dist_path = $this->config['upload_path'] . "/cert";
		if($_FILES['mb_com_cert_copy']['size'] > 0) {
            if (!is_dir($dist_path)) mkdir($dist_path, 0707);
			$file = $_POST['mb_id'] . "_certificat_" . basename($_FILES['mb_com_cert_copy']['name']);
            $uploaded_file = Utils::uploadFile('mb_com_cert_copy', $dist_path . "/"  . $file);

            $data['mb_com_cert_copy'] = basename($uploaded_file['destFile']);

			if(is_file($dist_path."/".$data_ori['mb_com_cert_copy'])) {
				@unlink($dist_path."/".$data_ori['mb_com_cert_copy']);
			}
        }

		$this->db->where('mb_id', $_POST['mb_id']);
        $id = $this->db->update($this->tb['Member'], $data);

	}

	// 담당TM선택 업데이트
	public function updateChargerTmSelectedRows($ids, $values, $changed) {

		for($i=0; $i<count($ids); $i++) {

			$data = array();

			$this->db->where('mb_id', $ids[$i]);

			$data['mb_tm_id'] = $values[$ids[$i]] && $values[$ids[$i]] != 'na' ? $values[$ids[$i]] : NULL;
			if($changed[$ids[$i]] == '1') {
				$data['mb_distribute_date'] = $this->db->NOW();
				$data['mb_update_date'] = $this->db->NOW();
			}

			$this->db->update($this->tb['Member'], $data);
		}
	}

	public function updateTM($mb_id, $mb_tm_id) {

		$this->db->where('mb_id', $mb_id);

		$data = Array(
						"mb_tm_id" => ($mb_tm_id && $mb_tm_id != 'na' ? $mb_tm_id : NULL),
						'mb_update_date' => $this->db->NOW(),
						"mb_distribute_date" => $this->db->NOW()
				);

		$this->db->update($this->tb['Member'], $data);

	}

	public function updateMemoTM($mb_id, $mb_tm_id) {

		$this->db->where('mb_id', $mb_id);

		$data = Array(
						"mb_tm_id" => ($mb_tm_id && $mb_tm_id != 'na' ? $mb_tm_id : NULL),
						'mb_update_date' => $this->db->NOW()
				);

		$this->db->update($this->tb['Member'], $data);

	}


	public function updateExtractDate($mb_id, $mb_extract_weekday) {
		$data = Array(
				'mb_extract_weekday' => $mb_extract_weekday
		);
		$this->db->where('mb_id', $mb_id);
        $this->db->update($this->tb['Member'], $data);

	}

	// 이용중지
    public function stopUser($id) {
        $data = Array (
			'mb_leave_date' => '',
            'mb_sms' => '0',
			'mb_status' => '2'
        );

        $this->db->where('mb_id', $id);
        $this->db->update($this->tb['Member'], $data);

		$termService = new TermService();
		$row = $termService->getMemberServiceUse($id);

		for($i=0; $i<count($row); $i++) {
			$termService->stopService($row[$i]['su_no']);
		}
    }

	// 이용재개
	public function startUser($id) {
		$data = Array (
            'mb_leave_date' => '',
			'mb_status' => '1'
        );

		$this->db->where('mb_id', $id);
        $this->db->update($this->tb['Member'], $data);

		$termService = new TermService();
		$row = $termService->getMemberServiceUse($id);
		for($i=0; $i<count($row); $i++) {
			$termService->resumeService($row[$i]['su_no']);
		}
	}

	
	// 회원탈퇴
    public function unsubscribeUser($id) {

		if(!$id) return false;

        $data = Array (
      	      'mb_leave_date' => date('Y-m-d H:i:s'),
			'mb_sms' => '0',
			'mb_status' => '3'
        );

        $this->db->where('mb_id', $id);
        $this->db->update($this->tb['Member'], $data);

		$termService = new TermService();
		$row = $termService->getMemberServiceUse($id);

		for($i=0; $i<count($row); $i++) {
			$termService->stopService($row[$i]['su_no']);
		}
    }
    
    public function SetNumberNoLogin($id, $le_inning, $score, $num1, $num2, $num3, $num4, $num5, $num6, $num7) {


	if (!$id) { echo "버그가 발생하였습니다. 관리자에게 문의해주세요.<br>\n"; return false; }

	if (!$le_inning) { echo "버그가 발생했습니다. 관리자에게 문의해주세요.<br>\n"; return false; }
	if (!$num1 || $num1 < 1 || $num1 > 45 || !is_numeric($num1)) { echo "not number 1 ball<br>\n"; return false; }
	if (!$num2 || $num2 < 1 || $num2 > 45 || !is_numeric($num2)) { echo "not number 2 ball<br>\n"; return false; }
	if (!$num3 || $num3 < 1 || $num3 > 45 || !is_numeric($num3)) { echo "not number 3 ball<br>\n"; return false; }
	if (!$num4 || $num4 < 1 || $num4 > 45 || !is_numeric($num4)) { echo "not number 4 ball<br>\n"; return false; }
	if (!$num5 || $num5 < 1 || $num5 > 45 || !is_numeric($num5)) { echo "not number 5 ball<br>\n"; return false; }
	if (!$num6 || $num6 < 1 || $num6 > 45 || !is_numeric($num6)) { echo "not nubmer 6 ball<br>\n"; return false; }
if (!$num7 || $num7 < 1 || $num7 > 45 || !is_numeric($num7)) { echo "not number 7 ball<br>\n"; return false; }
	
	$this->db->where("mb_id = '".$id."' AND le_result_grade = 0 AND le_inning = '".$le_inning."' ORDER BY le_no DESC");
	$result = $this->db->arraybuilder()->getOne($this->tb["LottoNumbers"], "le_no");

	//당첨 조합 만들기
	//$this->db->where("lw_inning = ".$le_inning);
	//$win_numbers = $this->db->arraybuilder()->getOne($this->tb['LottoWinNumbers'], "lw_num1, lw_num2, lw_num3, lw_num4, lw_num5, lw_num6, lw_num7");

	//$string = $win_numbers['lw_num1'].'v'.$win_numbers['lw_num2'].'v'.$win_numbers['lw_num3'].'v'.$win_numbers['lw_num4'].'v'.$win_numbers['lw_num5'].'v'.$win_numbers['lw_num6'];
	//echo $string;

	$win_numbers2 = array();
	array_push($win_numbers2, $num1);//$win_numbers['lw_num1']);
	array_push($win_numbers2, $num2);//$win_numbers['lw_num2']);
	array_push($win_numbers2, $num3);//$win_numbers['lw_num3']);
        array_push($win_numbers2, $num4);//$win_numbers['lw_num4']);
	array_push($win_numbers2, $num5);//$win_numbers['lw_num5']);
        array_push($win_numbers2, $num6);//$win_numbers['lw_num6']);

	//$i_array = range(1,45);
	$i_nonarray = array();
	for($i=1; $i<=45; $i++) {
		if ($i==$num1 || $i==$num2 || $i==$num3 || $i==$num4 || $i==$num5 || $i==$num6 || $i==$num7)
			continue;
		array_push($i_nonarray, $i);
	}

	$win_numbers3 = $win_numbers2;

	$random_number = array();
	for ($i=0; $i<4; $i++) {
		$index = rand(0,5-$i);
		$num = $win_numbers3[$index];
		array_push($random_number, $num);
		array_splice($win_numbers3, $index, 1);
		//unset($i_array[$num-1]);
	}	

	if ($score == 3) {
		$index = rand(0,1);
		$num = $win_numbers3[$index];
		array_push($random_number, $num);
                //array_splice($win_numbers3, $index, 1);
                //unset($i_array[$num-1]);
		//unset($i_array[$win_numbers3[0]-1]);
		//unset($i_array[$num7-1]);
		//array_filter($i_array);
		//array_push($random_number, $i_array[rand(0,37)]);
		array_push($random_number, $i_nonarray[rand(0,37)]);
	}

	else if ($score == 4) {
                //unset($i_array[$win_numbers3[0]-1]);
                //unset($i_array[$win_numbers3[1]-1]);
                //array_splice($i_array, $num7-1, 1);
		//$index = rand(0,37);
		//$num = $i_array[$index];
                //array_push($random_number, $num);
		//array_splice($i_array, $index, 1);
		//array_push($random_number, $i_array[rand(0,36)]);

		$index1 = rand(0,37);
		$index2 = $index1 + rand(1,37);
		if ($index2 > 37) $index2 = $index2 - 38;
		array_push($random_number, $i_nonarray[$index1]);
		array_push($random_number, $i_nonarray[$index2]);
	}

	sort($random_number);

	//$string	= $random_number[0].'v'.$random_number[1].'v'.$random_number[2].'v'.$random_number[3].'v'.$random_number[4].'v'.$random_number[5];
	//echo $string;

	$termService = new TermService();

	$termService->db->arrayBuilder()->rawQuery("UPDATE ".$this->tb['LottoNumbers']." SET le_num1 = ".$random_number[0].", le_num2 = ".$random_number[1].", le_num3 = ".$random_number[2].", le_num4 = ".$random_number[3].", le_num5 = ".$random_number[4].", le_num6 = ".$random_number[5]." WHERE le_no = '".$result['le_no']."'");	
	
	$lottoService = new LottoService();
	$lottoService->checkResult($le_inning, $result['le_no']);

	$this->updateExtractDate($id, $score);

	//$mb = $this->getUser($id);

	//$message = new Message();
	//$title = "[".$this->site_conf['name']."] ".$mb['mb_name']."회원님 ".$le_inning."회차 ".$score."등 당첨 축하드립니다.";
	//$content = $mb['mb_name']."회원님 ".$le_inning."회차 ".$score."등 당첨 축하드립니다.\n";
	//$message->addMessage($id, $title, $content, "sms");
	
	//echo $row_win['lw_inning'];
	echo "작업완료ID: ".$id."<br>\n";
	
    }
	// 회원삭제
    public function deleteUser($id) {
        
        $this->db->where('mb_id', $id);
        $this->db->delete($this->tb['Member'], $data);
    }

	public function updateExtractNum($mb_id, $num) {
		$data = Array(
				'mb_extract_per_week' => $num
			);

		$this->db->where('mb_id', $mb_id);
        $this->db->update($this->tb['Member'], $data);
	}

	public function addMemo($mb_id, $memo, $schedule, $schedule_datetime) {


		$is_schedule = ($schedule == 1 && $schedule_datetime) ? 1 : 0;

		$data = array(
						'mb_id' => $mb_id,
						'mo_mb_id' => $_SESSION['ss_mb_id'],
						'mo_memo' => $memo,
						'mo_schedule' => $is_schedule,
						'mo_schedule_datetime' => $schedule_datetime,
						'mo_datetime' => $this->db->NOW()
				);

		$this->db->insert($this->tb['Memo'], $data);

		// 회원 업데이트 시간 적용

		
		$data = Array (
            'mb_update_date' => $this->db->NOW()
        );

		$user = $this->getUser($mb_id);
		if(!$user['mb_cousult_status']) {
			$data['mb_cousult_status'] = '상담중';
		}

        $this->db->where('mb_id', $mb_id);
        $this->db->update($this->tb['Member'], $data);
	}

	public function deleteMemo($no) {
		$this->db->where('mo_no', $no);
		$this->db->delete($this->tb['Memo']);
	}

	public function deleteMemoById($mb_id) {

		// 메모초기화
		$this->db->where('mb_id', $mb_id);
		$this->db->delete($this->tb['Memo']);

		// 상담상태초기화
		$data['mb_cousult_status'] = '';
        $this->db->where('mb_id', $mb_id);
        $this->db->update($this->tb['Member'], $data);
	}

	public function getMemoList($mb_id) {
		$this->db->where('a.mb_id', $mb_id);
		$this->db->orderBy('mo_no', 'desc');
		$this->db->join($this->tb['Member']." as b", "a.mo_mb_id=b.mb_id", "LEFT");
		$rows = $this->db->get($this->tb['Memo']." as a", null, '*');

		return $rows;
	}

	public function getLatestMemo($mb_id) {
		$this->db->where('a.mb_id', $mb_id);
		$this->db->orderBy('mo_no', 'desc');
		$this->db->join($this->tb['Member']." as b", "a.mo_mb_id=b.mb_id", "LEFT");
		$rows = $this->db->get($this->tb['Memo']." as a", 1, '*');

		return $rows;
	}


	public function getAlertMemoList($tm_id='', $page=1, $url='') {

		global $pageLimit;

        $page = $page > 0 ? $page : 1;


		if($tm_id) {
			$this->db->where('mo_mb_id', $this->db->escape($tm_id));
		}

		if($_GET['s_date']) {
			$this->db->where("DATE_FORMAT(mo_schedule_datetime, '%Y-%m-%d ') >= DATE_FORMAT('".$this->db->escape($_GET['s_date'])."', '%Y-%m-%d')");
		}

		if($_GET['get_current_alert']) {
			$this->db->where("mo_schedule_datetime >= date_sub(now(), interval 30 minute) && mo_schedule_datetime <= NOW()");
		}

		if($_GET['s_status']) {
			if($_GET['s_status'] == 'N') {
				$this->db->where('mo_schedule_done_datetime IS NULL');
			} else {
				$this->db->where('mo_schedule_done_datetime IS NOT NULL');
			}
		}

		if($_GET['s_mb_cousult_status']) {
			$this->db->where('mb_cousult_status', $_GET['s_mb_cousult_status']);
		}

		if($_GET['sc'] && $_GET['sv']) {
            $this->db->where($this->db->escape($_GET['sc']), '%'.$this->db->escape($_GET['sv']).'%', 'like');
        }

		$this->db->where('mo_schedule', '1');

		$this->db->join($this->tb['Member']." as b", "a.mb_id=b.mb_id", "LEFT");

        $this->db->pageLimit = $pageLimit ? $pageLimit : 20;

        $this->db->orderBy('mo_schedule_datetime', 'ASC');
        $list = $this->db->arraybuilder()->withTotalCount()->paginate($this->tb['Memo']." as a", $page);

        $link = Utils::getPagination($this->db->totalPages, $page, $url);

		$data['total_count'] = $this->db->totalCount;
		$data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
        $data['list'] = $list;
        $data['link'] = $link;

        return $data;
	}

	// 스케쥴 캘린더용 데이터
	public function getAlertMemos($tm_id='', $done='0', $y='', $m='', $d='') {
		if($tm_id) {
			$this->db->where('mo_mb_id', $tm_id);
		}

		if($y) {
			$alert_date = $y;
			if($m) {
				$alert_date .= '-'.sprintf('%02d', $m);
				if($d) {
					$alert_date .= '-'.sprintf('%02d', $d);
				}
			}

			$this->db->where("mo_schedule_datetime like '".$alert_date."%'");
		}

		$this->db->where('mo_schedule', '1');

		if($done) {
			$this->db->where('mo_schedule_done_datetime IS NOT NULL');
		} else {
			$this->db->where('mo_schedule_done_datetime IS NULL');
		}

		$data = $this->db->get($this->tb['Memo'], null, "*, date_format(mo_schedule_datetime,'%Y-%m-%d') as d");

		return $data;

	}

	// 알림팝업용
	public function getCurrentAlerts($tm_id='') {
		if($tm_id) $this->db->where('mo_mb_id', $tm_id);
		$this->db->where('mo_schedule', '1');
		$this->db->where('mo_schedule_done_datetime IS NULL');
		$this->db->where("mo_schedule_datetime >= date_sub(now(), interval 24 hour) && mo_schedule_datetime <= NOW()");
		$this->db->orderBy('mo_schedule_datetime', 'ASC');
        $rows = $this->db->arraybuilder()->get($this->tb['Memo']." as a", null, '*');

		return $rows;
	}

	// 알림 완료처리
	public function updateMemoDone($mo_no) {
		if($mo_no) {
			$this->db->where('mo_no', $mo_no);
			$rows = $this->db->getOne($this->tb['Memo'], null, '*');

			$this->db->where('mo_no', $mo_no);
			$this->db->update($this->tb['Memo'], array('mo_schedule_done_datetime' => $this->db->NOW()));

			return $rows['mb_id'];
		} else {
			return false;
		}
	}


	// 회원 엑셀 다운로드
	public function excelDownload($blank=false) {
		global $pageLimit;

		ini_set("memory_limit" , -1);

		$pageLimit = 9999999;
		$data = $this->getUserListWithService();
		$list = $data['list'];

		// 엑셀파일 작성
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$spreadsheet->getActiveSheet()->getPageSetup()
				->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
		$spreadsheet->getActiveSheet()->getPageSetup()
				->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

		//$spreadsheet->getActiveSheet()->getPageSetup()->setFitToPage(true);

		$spreadsheet->getDefaultStyle()->getFont()->setName('맑은 고딕');

		// 이렇게 해줘야 페이지브레이크가 먹음
		$spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
		$spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);

		// zoom level
		$spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(85);
		

		$spreadsheet->getActiveSheet()->setShowGridlines(true);

		$user_type_arr = $this->getUserType();
		foreach($user_type_arr as $key => $value) {
			$user_type[] = $key.":".$value;
		}

		$user_type = implode(", ", $user_type);

		


		$termService = new TermService();

		if($blank === false) {

			// 셀 헤더생성
			$spreadsheet->setActiveSheetIndex(0)
				->setCellValue("A1", "번호(수정불가, 등록시 '추가'입력)")
				->setCellValue("B1", "회원유형(".$user_type.")")
				->setCellValue("C1", "이용서비스")
				->setCellValue("D1", "서비스만료일")
				->setCellValue("E1", "누적결제액")
				->setCellValue("F1", "아이디")
				->setCellValue("G1", "패스워드(평문)")
				->setCellValue("H1", "이름")
				->setCellValue("I1", "생년월일")
				->setCellValue("J1", "이메일")
				->setCellValue("K1", "전화")
				->setCellValue("L1", "휴대전화")
				->setCellValue("M1", "TM id")
				->setCellValue("N1", "상담이력")
				->setCellValue("O1", "메일수신(1,0)")
				->setCellValue("P1", "SMS수신(1,0)")
				->setCellValue("Q1", "등록일자")
				->setCellValue("R1", "수정일")
				->setCellValue("S1", "광고채널")
				->setCellValue("T1", "광고매체");



			for($i=0; $i<count($list); $i++) {
				$row_service = $termService->getMemberServiceUse($list[$i]['mb_id']);

				$service_use = "";
				$service_enddate = "";
				for($j=0; $j<count($row_service); $j++) {
					$service_use = $row_service[$j]['sg_name'];
					$service_enddate = substr($row_service[$j]['su_enddate'],0,10);
				}

				$paid = $termService->getMemberServiceBuy($list[$i]['mb_id']);

				$spreadsheet->setActiveSheetIndex(0)
				->setCellValue("A".($i+2), $list[$i]['mb_no'])
				->setCellValue("B".($i+2), $list[$i]['mb_level'])
				->setCellValue("C".($i+2), $service_use)
				->setCellValue("D".($i+2), $service_enddate)
				->setCellValue("E".($i+2), number_format($paid[0]['total_pay']))
				->setCellValue("F".($i+2), $list[$i]['mb_id'])
				->setCellValue("G".($i+2), '보안')
				->setCellValue("H".($i+2), $list[$i]['mb_name'])
				->setCellValue("I".($i+2), $list[$i]['mb_birth'])
				->setCellValue("J".($i+2), $list[$i]['mb_email'])
				->setCellValue("K".($i+2), $list[$i]['mb_tel'])
				->setCellValue("L".($i+2), $list[$i]['mb_hp'])
				->setCellValue("M".($i+2), $list[$i]['mb_tm_id'])
				->setCellValue("N".($i+2), ($list[$i]['mb_cousult_status']) ? $list[$i]['mb_cousult_status'] : '상담이전')
				->setCellValue("O".($i+2), $list[$i]['mb_mailling'])
				->setCellValue("P".($i+2), $list[$i]['mb_sms'])
				->setCellValue("Q".($i+2), $list[$i]['mb_datetime'])
				->setCellValue("R".($i+2), $list[$i]['mb_update_date'])
				->setCellValue("S".($i+2), $list[$i]['mb_channel'])
				->setCellValue("T".($i+2), $list[$i]['mb_media']);
			}



			$spreadsheet->setActiveSheetIndex(0);

			header('Content-Type: application/vnd.ms-excel');
			//header('Content-Disposition: attachment;filename='.date('Y-m-d').'_회원정보.xls');
			header('Content-Disposition: attachment; filename="'.iconv('UTF-8','CP949',date('Y-m-d').'_회원정보.xls'). '"');
			header('Cache-Control: max-age=0');
		} else {

			// 셀 헤더생성
			$spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A1", "번호(등록시 '추가'입력)")
            ->setCellValue("B1", "회원유형(".$user_type.")")
            ->setCellValue("C1", "이름")
            ->setCellValue("D1", "휴대전화")
			->setCellValue("E1", "전화")
			->setCellValue("F1", "이메일")
			->setCellValue("G1", "TM id(지정하실 TM의 id입력)")
			->setCellValue("H1", "추출요일(월:1, 화:2, 수:3, 목:4, 금:5)")
			->setCellValue("I1", "채널명(예시 - adinc)");

			for($i=0; $i<50; $i++) {
				
				$spreadsheet->setActiveSheetIndex(0)
				->setCellValue("A".($i+2), '추가')
				->setCellValue("B".($i+2), '2')
				->setCellValue("C".($i+2), '')
				->setCellValue("D".($i+2), '')
				->setCellValue("E".($i+2), '')
				->setCellValue("F".($i+2), '')
				->setCellValue("G".($i+2), '')
				->setCellValue("H".($i+2), '4')
				->setCellValue("I".($i+2), '');
			}


			$spreadsheet->setActiveSheetIndex(0);

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename="'.iconv('UTF-8','CP949','회원정보_엑셀등록양식.xls'). '"');
			header('Cache-Control: max-age=0');
		}
	 
		$spreadsheet->getActiveSheet()
			->getHeaderFooter()->setOddFooter('&R&F Page &P / &N');
		$spreadsheet->getActiveSheet()
			->getHeaderFooter()->setEvenFooter('&R&F Page &P / &N');

		//$spreadsheet->getActiveSheet()->getSheetView()->setView(\PhpOffice\PhpSpreadsheet\Worksheet\SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW);
		
		//$spreadsheet->getActiveSheet()->setBreak( 'A5' , \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW );
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xls");
		//$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	 
		exit;
	}

	public function excelNewUserUpload() {
		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES['upfile']['tmp_name']);
		$rows = $spreadsheet->getActiveSheet()->toArray(null, true, true);


		$countgap = 10; // 몇건씩 보낼지 설정
		$sleepsec = 200;  // 천분의 몇초간 쉴지 설정
		$cnt = 0;
		$update_cnt = 0;
		$insert_cnt = 0;


		for($i=1; $i<count($rows); $i++) {

			$data_row = $rows[$i];
			if(trim($rows[$i][3])) { // 휴대전화가 있는데이터만

				$process_type = "";

				

				$mb_id = str_replace("-", "", $data_row[3]);
				$mb_password = substr($mb_id, -4);
				$mb_name = $data_row[2] ? $data_row[2] : $mb_id;
				$mb_hp = $mb_id;
				$mb_nick = substr($mb_id, 0, 8)."****";

				$mb_datetime = date('Y-m-d H:i:s');

				
				$tmp = $this->db->rawQueryOne("SELECT mb_hp FROM ".$this->tb['Member']." WHERE mb_hp='".$mb_hp."'");

				if(!$tmp['mb_id']) { // 아이디 중복체크


					// 회원등록
					$query = " INSERT INTO ".$this->tb['Member']."
								set
									mb_id = '".$mb_id."',
									mb_password = '".get_encrypt_string($mb_password)."',
									mb_level = '".$data_row[1]."',
									mb_name = '".$mb_name."',
									mb_nick = '".$mb_nick."',
									mb_hp = '".$mb_hp."',
									mb_tel = '".$data_row[4]."',
									mb_email = '".$data_row[5]."',
									mb_sms = '0',
									mb_tm_id = '".$data_row[6]."',
									mb_extract_weekday = '".$data_row[7]."',
									mb_channel = '".$data_row['8']."',
									mb_datetime = '".$mb_datetime."',
									mb_today_login = '".$mb_datetime."',
									mb_update_date='".$mb_datetime."'";

					$this->db->rawQuery($query);


					$process_type = " [입력] ";
					$insert_cnt++;

				} else {
					$process_type = " [중복전화] ".$tmp['mb_hp'];
				}

				echo "<script> $('#content').append('$data_row[3]: $data_row[2] / $data_row[8].............$process_type<br>'); </script>\n";

			}


			$cnt++;

			$percent = floor(($cnt/(count($rows)-1) )* 100);

			

			if($percent%1 == 0 && $percent_pre != $percent) {
				$percent_pre = $percent;
				echo "<script> progressBar(".$percent.", $('#progressBar')); </script>\n";
			}
			//echo "<script> $('#content').animate({ scrollTop: $(#content).height() }, 'slow'); </script>\n";
			if($percent == 100) echo "<script> $('#ok_close').show(); </script>\n";

			echo str_repeat(' ',1024*64);

			
			flush();
			ob_flush();
			ob_end_flush();
			usleep($sleepsec);


			if ($cnt % $countgap == 0)
			{
				echo "<script> $('#content').animate({ scrollTop: $('#content').prop('scrollHeight') }, 'fast'); </script>\n";
			}
		

		}

		echo "<script> $('#content').append('등록: 총".number_format($insert_cnt)."건 / 업데이트: 총".number_format($update_cnt)."건<br>'); </script>\n";
	}


	public function excelUpload() {
		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES['upfile']['tmp_name']);
		$rows = $spreadsheet->getActiveSheet()->toArray(null, true, true);


		$countgap = 10; // 몇건씩 보낼지 설정
		$sleepsec = 200;  // 천분의 몇초간 쉴지 설정
		$cnt = 0;
		$update_cnt = 0;
		$insert_cnt = 0;
		for($i=1; $i<count($rows); $i++) {
			$data_row = $rows[$i];

			$process_type = "";
			if($data_row[0] && $data_row[2] && $data_row[0] != '추가') { // 회원 업데이트
				
				$query = " UPDATE ".$this->tb['Member']."
							set
								 mb_level = '".$data_row[1]."',
								 mb_name = '".$data_row[4]."',
								 mb_com_name = '".$data_row[13]."',
								 mb_com_division = '".$data_row[14]."',
								 mb_com_position = '".$data_row[15]."',
								 mb_com_tel = '".$data_row[16]."',
								 mb_com_fax = '".$data_row[17]."',
								 mb_com_email = '".$data_row[18]."',
								 mb_email = '".$data_row[5]."',
								 mb_hp = '".$data_row[7]."',
								 mb_fax = '".$data_row[8]."',
								 mb_tel = '".$data_row[6]."',
								 mb_zip1 = '".substr(trim($data_row[9]), 0, 3)."',
								 mb_zip2 = '".substr(trim($data_row[9]), 3)."',
								 mb_addr1 = '".$data_row[10]."',
								 mb_addr2 = '".$data_row[11]."',
								 mb_addr3 = '".$data_row[12]."',
								 mb_mailling = '".$data_row[19]."',
								 mb_sms = '".$data_row[20]."',
								 mb_update_date=NOW(),
								 mb_today_login=NOW() WHERE mb_no='".$data_row[0]."' AND mb_id='".$data_row[2]."'";


				$this->db->rawQuery($query);


				$process_type = " [수정] ";
				$update_cnt++;
			} else {

				
				if(!$data_row[2]) {
					$mb_id = Utils::getRandomString();
					$mb_mailling_member = "1";
				} else {
					$mb_id = $data_row[2];

					// 패스워드가 없으면 아이디와 동일
					$mb_password = $data_row[3] ? "PASSWORD('".trim($data_row[3])."')" : "PASSWORD('".$mb_id."')";
					$mb_mailling_member = "0";

					if($data_row[1] == 'company') {
						$member_cols = "mb_level='3',";
					} else {
						$member_cols = "mb_level='2',";
					}
				}
				
				$tmp = $this->db->rawQueryOne("SELECT mb_id FROM ".$this->tb['Member']." WHERE mb_id='".$mb_id."'");

				if(!$tmp['mb_id']) { // 아이디 중복체크

					// 회원등록
					$query = " INSERT INTO ".$this->tb['Member']."
								set
									mb_id = '".$mb_id."',
									mb_password = ".$mb_password.",
									mb_level = '".$data_row[1]."',
									mb_name = '".$data_row[4]."',
									mb_birth = '".$data_row[5]."',
									mb_com_name = '".$data_row[14]."',
									mb_com_division = '".$data_row[15]."',
									mb_com_position = '".$data_row[16]."',
									mb_com_tel = '".$data_row[17]."',
									mb_com_fax = '".$data_row[18]."',
									mb_com_email = '".$data_row[19]."',
									mb_email = '".$data_row[6]."',
									mb_hp = '".$data_row[8]."',
									mb_fax = '".$data_row[9]."',
									mb_tel = '',
									mb_zip1 = '".substr(trim($data_row[10]), 0, 3)."',
									mb_zip2 = '".substr(trim($data_row[10]), 3)."',
									mb_addr1 = '".$data_row[11]."',
									mb_addr2 = '".$data_row[12]."',
									mb_addr3 = '".$data_row[13]."',
									mb_mailling = '".$data_row[20]."',
									mb_sms = '".$data_row[21]."',
									mb_channel = '".$data_row[7]."',
									mb_datetime = NOW(),
									mb_today_login=NOW(),
									mb_update_date=NOW()";


					$this->db->rawQuery($query);


					$process_type = " [입력] ";
					$insert_cnt++;

				} else {
					$process_type = " [중복아이디] ".$tmp['mb_id'];
				}
			}

			$cnt++;

			$percent = floor(($cnt/(count($rows)-1) )* 100);

			echo "<script> $('#content').append('$data_row[2]: $data_row[3] / $data_row[8].............$process_type<br>'); </script>\n";

			if($percent%1 == 0 && $percent_pre != $percent) {
				$percent_pre = $percent;
				echo "<script> progressBar(".$percent.", $('#progressBar')); </script>\n";
			}
			//echo "<script> $('#content').animate({ scrollTop: $(#content).height() }, 'slow'); </script>\n";
			if($percent == 100) echo "<script> $('#ok_close').show(); </script>\n";

			echo str_repeat(' ',1024*64);

			
			flush();
			ob_flush();
			ob_end_flush();
			usleep($sleepsec);


			if ($cnt % $countgap == 0)
			{
				echo "<script> $('#content').animate({ scrollTop: $('#content').prop('scrollHeight') }, 'fast'); </script>\n";
			}

		}

		echo "<script> $('#content').append('등록: 총".number_format($insert_cnt)."건 / 업데이트: 총".number_format($update_cnt)."건<br>'); </script>\n";
	}


	public function getChannelGroup($tm_id = '', $add_condition='') {

		if($tm_id) $this->db->where("mb_tm_id", $tm_id);
		$this->db->groupBy("mb_channel");
		if($add_condition) $this->db->where($add_condition);
		$rows = $this->db->get($this->tb['Member'], null, " count(*) as cnt, IF(mb_channel IS NULL OR mb_channel = '', '', mb_channel) as mb_channel");

		return $rows;
	}

	public function getMediaGroup($tm_id = '', $s_channel, $add_condition='') {
		if($tm_id) $this->db->where("mb_tm_id", $tm_id);
		if($s_channel) $this->db->where("mb_channel", $s_channel);
		if($add_condition) $this->db->where($add_condition);

		$this->db->groupBy("mb_media_u");
		$rows = $this->db->get($this->tb['Member'], null, " count(*) as cnt, IF(mb_media IS NULL OR mb_media = '', '', mb_media) as mb_media_u");

		return $rows;
	}

	public function getNotAssignedMember($sdate='', $edate='') {

		// 해당일의 전체회원 수
		if($sdate && $edate) {
			$this->db->where("(DATE_FORMAT(mb_datetime, '%Y-%m-%d') >= DATE_FORMAT('$sdate', '%Y-%m-%d') AND DATE_FORMAT(mb_datetime, '%Y-%m-%d') <= DATE_FORMAT('$edate', '%Y-%m-%d'))");
		}
		$rows_total = $this->db->get($this->tb['Member'], null, "*");

		// 미분배회원 조회
		if($sdate && $edate) {
			$this->db->where("(DATE_FORMAT(mb_datetime, '%Y-%m-%d') >= DATE_FORMAT('$sdate', '%Y-%m-%d') AND DATE_FORMAT(mb_datetime, '%Y-%m-%d') <= DATE_FORMAT('$edate', '%Y-%m-%d'))");
		}
		$this->db->where("(mb_tm_id is NULL OR mb_tm_id='')");
		$rows_not_distributed = $this->db->get($this->tb['Member'], null, "mb_id, mb_name, mb_tm_id");


		$data['total'] = $rows_total;
		$data['not_distributed'] = $rows_not_distributed;

		return $data;
	}


	public function getMembersToDistribute($sdate='', $edate='') {

		// 해당일의 전체회원 수
	
		if($sdate && $edate) {
			$this->db->where("(DATE_FORMAT(mb_datetime, '%Y-%m-%d') >= DATE_FORMAT('$sdate', '%Y-%m-%d') AND DATE_FORMAT(mb_datetime, '%Y-%m-%d') <= DATE_FORMAT('$edate', '%Y-%m-%d'))");
			$this->db->where("(mb_level < 3)");
			$rows_total = $this->db->get($this->tb['Member'], null, "mb_id, mb_name, mb_tm_id");

			
			$this->db->where("(DATE_FORMAT(mb_datetime, '%Y-%m-%d') >= DATE_FORMAT('$sdate', '%Y-%m-%d') AND DATE_FORMAT(mb_datetime, '%Y-%m-%d') <= DATE_FORMAT('$edate', '%Y-%m-%d'))");
			$this->db->where("(mb_level < 3)");


			if($_POST['channel']) {
				$channel = explode(",", $_POST['channel']);
				if(array_search('common', $channel) !== false) {
					$this->db->where("(mb_channel IN ('".implode("','", $channel)."') OR (mb_channel is NULL OR mb_channel=''))");
				} else {
					$this->db->where("mb_channel IN ('".implode("','", $channel)."')");
				}
			}

			if($_POST['media']) {
				$media = explode(",", $_POST['media']);
				if(array_search('common', $media) !== false) {
					$this->db->where("(mb_media IN ('".implode("','", $media)."') OR (mb_media is NULL OR mb_media=''))");
				} else {
					$this->db->where("mb_media IN ('".implode("','", $media)."')");
				}
			}

			if($_POST['status']) {
				$status = explode(",", $_POST['status']);
				if(array_search('na', $status) !== false) {
					$this->db->where("(mb_cousult_status IN ('".implode("','", $status)."') OR mb_cousult_status is NULL)");
				} else {
					$this->db->where("mb_cousult_status IN ('".implode("','", $status)."')");
				}
			}

			if($_POST['type'] =='redistributeMemberToTM') {
				$this->db->where("(mb_tm_id is not NULL AND mb_tm_id != '')");
				if($_POST['tm_source_ids']) {
					$tm_source_ids = explode(",", $_POST['tm_source_ids']);
					$this->db->where("mb_tm_id IN ('".implode("','", $tm_source_ids)."')");
				} 
			} else { 
				$this->db->where("(mb_tm_id is NULL OR mb_tm_id='')");
			}
			$rows = $this->db->get($this->tb['Member'], null, "mb_id, mb_tm_id");

			$rows_not_distributed = array();
			for($i=0; $i<count($rows); $i++) {
				if($rows['mb_tm_id'] == '' || $rows['mb_tm_id'] == 'NULL') {
					$rows_not_distributed[] = $rows;
				}
			}

			$data['total'] = $rows_total;
			$data['not_distributed'] = $rows;
		}
		return $data;
	}


	public function distributeMemberToTM() {

		$check_num = ($_POST['num'] > 0 && ($_POST['num']*count($_POST['tm_ids']) <= $_POST['total_number'])) ? true : false; 

		if($_POST['s_date'] && $_POST['e_date'] && $check_num) {


			for($i=0; $i<count($_POST['tm_ids']); $i++) {
				$this->db->where("(DATE_FORMAT(mb_datetime, '%Y-%m-%d') >= DATE_FORMAT('".$_POST['s_date']."', '%Y-%m-%d') AND DATE_FORMAT(mb_datetime, '%Y-%m-%d') <= DATE_FORMAT('".$_POST['e_date']."', '%Y-%m-%d'))");
				$this->db->where("(mb_tm_id is NULL OR mb_tm_id='')");
				$this->db->where("(mb_level < 3)");

				if($_POST['channel']) {
					$channel = explode(",", $_POST['channel']);
					if(array_search('common', $channel) !== false) {
						$this->db->where("(mb_channel IN ('".implode("','", $channel)."') OR (mb_channel is NULL OR mb_channel=''))");
					} else {
						$this->db->where("mb_channel IN ('".implode("','", $channel)."')");
					}
				}

				if($_POST['media']) {
					$media = explode(",", $_POST['media']);
					if(array_search('common', $media) !== false) {
						$this->db->where("(mb_media IN ('".implode("','", $media)."') OR (mb_media is NULL OR mb_media=''))");
					} else {
						$this->db->where("mb_media IN ('".implode("','", $media)."')");
					}
				}

				$rows = $this->db->get($this->tb['Member'], $_POST['num'], '*');

				if(count($rows) > $_POST['total_number']) {
					return false;
				}

				for($j=0; $j<$_POST['num']; $j++) {
					$data = array(
								'mb_tm_id' => $_POST['tm_ids'][$i],
								'mb_update_date' => $this->db->now(),
								'mb_distribute_date' => $this->db->now()
					);

					$this->db->where('mb_id', $rows[$j]['mb_id']);
					$this->db->update ($this->tb['Member'], $data);

					//echo $this->db->getLastQuery();
				}

			}

			return true;
		} else {
			return false;
		}

	}

	public function getMemberToRedistribute($sdate='', $edate='') {


		$data['total'] = 0;
		$data['not_distributed'] = 0;

		if($sdate && $edate && $_POST['tm_source_ids']) {
			$this->db->where("(DATE_FORMAT(mb_datetime, '%Y-%m-%d') >= DATE_FORMAT('$sdate', '%Y-%m-%d') AND DATE_FORMAT(mb_datetime, '%Y-%m-%d') <= DATE_FORMAT('$edate', '%Y-%m-%d'))");
			$this->db->where("(mb_level < 3)");
			$tm_source_ids = explode(",", $_POST['tm_source_ids']);
			$this->db->where("mb_tm_id IN ('".implode("','", $tm_source_ids)."')");
			$rows_total = $this->db->get($this->tb['Member'], null, "mb_id, mb_name, mb_tm_id");


			// 대상검색
			$tm_source_ids = explode(",", $_POST['tm_source_ids']);
			$this->db->where("mb_tm_id IN ('".implode("','", $tm_source_ids)."')");

			$this->db->where("(DATE_FORMAT(mb_datetime, '%Y-%m-%d') >= DATE_FORMAT('$sdate', '%Y-%m-%d') AND DATE_FORMAT(mb_datetime, '%Y-%m-%d') <= DATE_FORMAT('$edate', '%Y-%m-%d'))");
			$this->db->where("(mb_level < 3)");

			if($_POST['channel']) {
				$channel = explode(",", $_POST['channel']);
				if(array_search('common', $channel) !== false) {
					$this->db->where("(mb_channel IN ('".implode("','", $channel)."') OR (mb_channel is NULL OR mb_channel=''))");
				} else {
					$this->db->where("mb_channel IN ('".implode("','", $channel)."')");
				}
			}

			if($_POST['media']) {
				$media = explode(",", $_POST['media']);
				if(array_search('common', $media) !== false) {
					$this->db->where("(mb_media IN ('".implode("','", $media)."') OR (mb_media is NULL OR mb_media=''))");
				} else {
					$this->db->where("mb_media IN ('".implode("','", $media)."')");
				}
			}

			if($_POST['status']) {
				$status = explode(",",$_POST['status']);
				if(array_search('na', $status) !== false) {
					$status[] = '';
					$this->db->where("(mb_cousult_status IN ('".implode("','", $status)."') OR mb_cousult_status is NULL)");
				} else {
					$this->db->where("mb_cousult_status IN ('".implode("','", $status)."')");
				}
			}

			
			$rows = $this->db->get($this->tb['Member'], null, "mb_id, mb_name, mb_tm_id");

			$rows_not_distributed = array();
			for($i=0; $i<count($rows); $i++) {
				if($rows['mb_tm_id'] == '' || $rows['mb_tm_id'] == 'NULL') {
					$rows_not_distributed[] = $rows;
				}
			}

			$data['total'] = $rows_total;
			$data['not_distributed'] = $rows;
		}

		return $data;
	}

	public function redistributeMemberToTM() {

		$check_num = ($_POST['num'] > 0 && ($_POST['num']*count($_POST['tm_ids']) <= $_POST['total_number'])) ? true : false; 

		if($_POST['s_date'] && $_POST['e_date'] && $check_num && is_array($_POST['tm_source_ids'])) {
			for($i=0; $i<count($_POST['tm_ids']); $i++) {
				$this->db->where("(DATE_FORMAT(mb_datetime, '%Y-%m-%d') >= DATE_FORMAT('".$_POST['s_date']."', '%Y-%m-%d') AND DATE_FORMAT(mb_datetime, '%Y-%m-%d') <= DATE_FORMAT('".$_POST['e_date']."', '%Y-%m-%d'))");
				$this->db->where("mb_tm_id IN ('".implode("','", $_POST['tm_source_ids'])."')");
				$this->db->where("(mb_level < 3)");
				$this->db->where("(mb_tm_id is not NULL AND mb_tm_id != '')");

				if($_POST['channel']) {
					$channel = explode(",", $_POST['channel']);
					if(array_search('common', $channel) !== false) {
						$this->db->where("(mb_channel IN ('".implode("','", $channel)."') OR (mb_channel is NULL OR mb_channel=''))");
					} else {
						$this->db->where("mb_channel IN ('".implode("','", $channel)."')");
					}
				}

				if($_POST['media']) {
					$media = explode(",", $_POST['media']);
					if(array_search('common', $media) !== false) {
						$this->db->where("(mb_media IN ('".implode("','", $media)."') OR (mb_media is NULL OR mb_media=''))");
					} else {
						$this->db->where("mb_media IN ('".implode("','", $media)."')");
					}
				}

				if($_POST['status']) {
					if(array_search('na', $_POST['status']) !== false) {
						$_POST['status'][] = '';
						$this->db->where("(mb_cousult_status IN ('".implode("','", $_POST['status'])."') OR mb_cousult_status is NULL)");
					} else {
						$this->db->where("mb_cousult_status IN ('".implode("','", $_POST['status'])."')");
					}
				}

				$rows = $this->db->get($this->tb['Member'], $_POST['num'], '*');

				if(count($rows) > $_POST['total_number']) {
					return false;
				}


				for($j=0; $j<$_POST['num']; $j++) {
					$data = array(
								'mb_tm_id' => $_POST['tm_ids'][$i],
								'mb_update_date' => $this->db->now(),
								'mb_distribute_date' => $this->db->now()
					);

					if($_POST['clear_status']) {
						$data['mb_cousult_status'] = '';
						$data['mb_consult_status_pre'] = $rows[$j]['mb_cousult_status'];
					}

					$this->db->where('mb_id', $rows[$j]['mb_id']);
					$this->db->update ($this->tb['Member'], $data);

					// 메모삭제
					if($_POST['clear_memo']) {
						$this->db->rawQuery("UPDATE ".$this->tb['Memo']." SET mo_del='1' WHERE mb_id='".$rows[$j]['mb_id']."'");
					}

				}
			}

			return true;
		} else {
			return false;
		}

	}

	// 상담상태 변경
	public function updateConstulStatus($mb_id, $status) {

		$data['mb_cousult_status'] = $status;
		$this->db->where('mb_id', $mb_id);
		$this->db->update($this->tb['Member'], $data);
		
		
	}

	public function changeExtractWeekday($mb_id, $weekday) {

		$data['mb_extract_weekday'] = $weekday;
		$this->db->where('mb_id', $mb_id);
		$this->db->update($this->tb['Member'], $data);
	}
}
