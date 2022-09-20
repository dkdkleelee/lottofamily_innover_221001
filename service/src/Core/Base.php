<?php 
namespace Acesoft\Core;

use \Acesoft\Core\DB;

Class Base
{
	protected $config;
	public $config_default;
	public $tb;
	public $db;
	public $site_conf;

	public function __construct()
	{
        $this->config = require(dirname(__FILE__)."/../../config/config.php");
		//$this->db = DB::getInstance();

		// table
		$this->tb = $this->config['table'];
		$this->site_conf = $this->config['site_conf'];

		if (session_status() == PHP_SESSION_NONE) {
			$this->start_session();
		}

		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			//echo $this->checkKey();
		}

		define('DEBUG', true);

		if(DEBUG == true)
		{
			error_reporting(E_ALL & ~E_NOTICE); 
			
			//error_reporting(E_ALL);

			ini_set("display_errors", 1);
		}
		else
		{
			error_reporting(0);
		}

	}

	public function checkKey() {
		$keyfile = file(dirname(__FILE__)."/../../config/key.enc");
		$domain = str_ireplace('www.', '', $_SERVER['SERVER_NAME']);

		if(!password_verify($domain."@".$_SERVER['SERVER_ADDR'], $keyfile[0])) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, 
				"http://license.webparty.kr/?d=".$_SERVER['SERVER_NAME']."&i=".$_SERVER['SERVER_ADDR']
			);
			$content = curl_exec($ch);

			echo "Configuration failed.";

			exit;
		}
	}

	public function start_session() {

		//▶ Session start
		header('P3P: CP="ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC"');
		header("Cache-Control: no-cache, must-revalidate");
		session_save_path($this->config['session_path']);
		session_cache_limiter("no-cache, must-revalidate");


		ini_set("session.cache_expire", 180); // 세션 캐쉬 보관시간 (분)
		ini_set("session.gc_maxlifetime", 10800); // session data의 garbage collection 존재 기간을 지정 (초)
		ini_set("session.gc_probability", 1); // session.gc_probability는 session.gc_divisor와 연계하여 gc(쓰레기 수거) 루틴의 시작 확률을 관리합니다. 기본값은 1입니다. 자세한 내용은 session.gc_divisor를 참고하십시오.
		ini_set("session.gc_divisor", 100); // session.gc_divisor는 session.gc_probability와 결합하여 각 세션 초기화 시에 gc(쓰레기 수거) 프로세스를 시작할 확률을 정의합니다. 확률은 gc_probability/gc_divisor를 사용하여 계산합니다. 즉, 1/100은 각 요청시에 GC 프로세스를 시작할 확률이 1%입니다. session.gc_divisor의 기본값은 100입니다.


		session_set_cookie_params(0,'/');
		session_start();
	}

	public function getAppPublicUrl() {
        	return $this->config['app_public_url'];
    	}

	public function getAppLibPath() {
        	return $this->config['app_lib_path'];
    	}

	public function getUploadPath() {
        	return $this->config['upload_path'];
    	}

	public function getUploadUrl() {
		return $this->config['upload_url'];
	}

	public function getUserType() {
		return $this->config['user_type'];
	}

	public function getCompanyType() {
		return $this->config['company_type'];
	}

	public function getCompanyDetailType() {
		return $this->config['company_detail_type'];
	}

    	public function getApplyStatus() {
        	return $this->config['apply_status'];
    	}

	public function getApplyStatusColor() {
        	return $this->config['apply_status_color'];
    	}

	public function getApplyIngStatusColor() {
        	return $this->config['apply_ing_status_color'];
    	}

	public function getLectureStatusColor() {
        	return $this->config['lecture_status_color'];
    	}

	public function getCompleteStatus() {
		return $this->config['complete_status'];
	}

	public function getPassStatus() {
		return $this->config['pass_status'];
	}

	public function getPassStatusColor() {
		return $this->config['pass_status_color'];
	}

	public function getCompleteStatusColor() {
		return $this->config['complete_status_color'];
	}

	public function getPayMethod() {
        	return $this->config['pay_method'];
    	}

	public function getBannerPosition() {
		return $this->config['banner_common'];
	}

	public function getCategoryBannerPosition() {
		return $this->config['banner_category'];
	}

	public function getLogType() {
		return $this->config['log_type'];
	}

	public function getAdvType() {
		return $this->config['adv_type'];
	}

	public function getAdvGrade() {
		return $this->config['adv_grade'];
	}

	public function getAdvSlot() {
		return $this->config['adv_slot'];
	}

	public function getBannerSize() {
		return $this->config['banner_size'];
	}

	public function getDeliveryType() {
		return $this->config['delivery_type'];
	}

	public function getPageTitle($page_name) {
		return ($this->config['page_title'][$page_name]) ? $this->config['page_title'][$page_name] : '기타페이지';
	}

	public function getRefererSearchKey($domain) {
		return ($this->config['referer_search_key'][$domain]) ? $this->config['referer_search_key'][$domain] : '';
	}

	public function getNumberType() {
		return $this->config['number_type'];
	}

	public function getConsultStatus() {
		return $this->config['consult_status'];
	}

	public function getPGInfo() {
		return $this->config['pg_info'];
	}

	public function getPGConfig($type) {
		return $this->config['pg'][$type];
	}

}


?>
