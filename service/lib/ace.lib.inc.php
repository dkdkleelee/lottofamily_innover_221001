<?
	error_reporting(E_ALL ^ E_NOTICE);

	global $_url, $_dir, $db, $_conf;

	// config 파일 로드
	//$_conf = parse_ini_file(str_replace(basename(__FILE__), '', __FILE__)."config.php", true);
	$_conf = require(dirname(__FILE__)."/../config/config.php");

	// url 정보 생성
	$_url['domain'] = "http://".$_SERVER['SERVER_NAME'];
	$_url['root'] = $_url['domain'].$_conf['info']['path']."/";
	$_url['solution_root'] = $_url['root']."ace_solution/";
	$_url['admin'] = $_url["root"]."admin/";
	$_url['lib'] = $_url['solution_root']."lib/";
	$_url['modules'] = $_url['solution_root']."modules/";
	$_url['upload'] = $_url['solution_root']."data/";
	$_url['module_upload'] = $_url['upload'];
	$_url['editor'] = $_url['lib']."smarteditor2/";
	
	// directory 정보 생성
	$_dir['root'] =  $_SERVER['DOCUMENT_ROOT'].$_conf['info']['path']."/";
	$_dir['root'] =  $_SERVER['DOCUMENT_ROOT']."/";
	$_dir['solution_root'] =  $_SERVER['DOCUMENT_ROOT'].$_conf['info']['path']."/ace_solution/";
	$_dir['admin'] = $_dir['solution_root']."admin/";
	$_dir['conf'] = $_dir['solution_root']."conf/";
	$_dir['lib'] = $_dir['solution_root']."lib";
	$_dir['modules'] = $_dir['solution_root']."modules/";
	$_dir['upload'] = $_dir['solution_root']."data/";
	$_dir['session'] = $_dir['upload']."session/";
	$_dir['cur_module'] = $_dir['modules'].$cur_module."/";
	$_dir['editor'] = $_dir['lib']."smarteditor2";

	// 그누보드
	$_url['board_root'] = "http://".$_SERVER['SERVER_NAME'].$_conf['info']['path'];
	$_url['board_admin'] = $_url['board_root']."adm/";
	$_dir['board_root'] =  $_SERVER['DOCUMENT_ROOT'].$_conf['info']['path']."/";
	$_dir['board_admin'] = $_dir['board_root']."adm/";
	$g4_path = $_dir['board_root'];
	$_dir['session'] = $_dir['board_root']."/data/session/";

	//▶ Session start
//	@header ("P3P : CP=\"ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC\"");
	header("Cache-Control: no-cache, must-revalidate");
	session_save_path($_dir['session']);
	session_cache_limiter("no-cache, must-revalidate");
	session_set_cookie_params(0,'/');
	session_start();

	// 기본 라이브러리 include
	include_once($_dir['lib']."/ace.lib.db.php");
	include_once($_dir['lib']."/ace.lib.basic.php");
	include_once($_dir['lib']."/ace.lib.category.php");
	include_once($_dir['lib']."/ace.lib.image.php");
	include_once($_dir['lib']."/Json.class.php");



	// db객체 생성
	$db = new DB($_conf['connections']['db']['host'], $_conf['connections']['db']['username'], $_conf['connections']['db']['password'], $_conf['connections']['db']['database']);

	$db->query("set names utf8");
?>