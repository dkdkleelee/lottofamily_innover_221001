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

class Auth /*extends Base*/ {

    public $db;
    private $category;

    public function __construct()
    {
        /*parent::__construct();*/
        $this->db = DB::getInstance();

    }

    public static function checkAuth($login='isLogedIn', $type='', $url='/bbs/login.php?') {
		if($login == 'isLogedIn') {
			if(Auth::checkLogin() === false) {
				Utils::goUrl($url, "로그인 후 이용해주세요.");
			}
		}

		if($type) {
			Auth::checkLogin();
			if(User::getUser($_SESSION['ss_mb_id'])['mb_type'] != $type) {
				$__app = new Base();
				Utils::goUrl("", $__app->getUserType()[$type]."회원만 이용 가능합니다.");
			}
		}
    }

	public static function checkLogin($level=0, $url='/bbs/login.php?') {

		$user = new User();

		if(!$_SESSION['ss_mb_id']) {
			Utils::goUrl($url, "로그인 후 이용해주세요.");
		}

		if($level > 0) {
			if($user->getUser($_SESSION['ss_mb_id'])['mb_level'] < $level) {
				Utils::goUrl('/?', "권한이 없습니다.");
			}
		}

		return true;
	}

	public static function isAdmin($mb_id='') {
		$myInfo = ($mb_id) ? User::getUser($mb_id) : User::getUser($_SESSION['ss_mb_id']);

		if($myInfo['mb_level'] < 10) {
			return false;
		} else {
			return true;
		}
	}

}
