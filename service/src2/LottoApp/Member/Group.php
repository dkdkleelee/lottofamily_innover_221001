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
use \Acesoft\LottoApp\Member\User;

class Group /*extends Base*/ {

    public $db;
    private $category;

    public function __construct()
    {
        /*parent::__construct();*/
        $this->db = DB::getInstance();

    }

    public function getList($page, $url) {

        $page = $page > 0 ? $page : 1;

        if($_GET['sc'] && $_GET['sv']) {
            $this->db->where($_GET['sc'], '%'.$_GET['sv'].'%', 'LIKE');
        }

        $this->db->pageLimit = 5;
		$this->db->join($this->tb['Member']." as b", "a.mg_no=b.mg_no", "LEFT");
        $this->db->orderBy('created_at');
		$this->db->groupBy('a.mg_no');
        $list = $this->db->arraybuilder()->paginate($this->tb['Group']." as a", $page, "a.*, count(b.mg_no) as member_count");

        $link = Utils::getPagination($this->db->totalPages, $page, $url);

        $data['list'] = $list;
        $data['link'] = $link;
		$data['total_count'] = $this->db->totalCount;
		$data['idx'] = $page == 1 ? $this->db->totalCount : $this->db->totalCount - ($this->db->pageLimit*($page-1));
		$data['total_pages'] = $this->db->totalPages;
		$data['page'] = $page;

        return $data;
    }

	public function getGroupArr() {
		$this->db->orderBy('mg_name', 'ASC');
		$data = $this->db->arrayBuilder()->get($this->tb['Group']);

		return $data;
	}

	public function add() {

		
		$data = Array (
			'mg_name' => $_POST['mg_name'],
			'created_at' => $this->db->NOW(),
			'updated_at' => $this->db->NOW()
        );

		
        $id = $this->db->insert($this->tb['Group'], $data);

	}

	public function update() {

		$data = Array (
			'mg_name' => $_POST['mg_name'],
			'created_at' => $this->db->NOW(),
			'updated_at' => $this->db->NOW()
        );

		$this->db->where('mg_no', $_POST['mg_no']);
        $id = $this->db->update($this->tb['Group'], $data);

	}

	public function delete() {

		$this->db->where('mg_no', $_POST['mg_no']);
        $id = $this->db->delete($this->tb['Group']);

	}
}
