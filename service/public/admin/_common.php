<?
define('G5_PATH', "../../../"); // common.php 의 상대 경로

include_once (G5_PATH."common.php");
include_once(G5_ADMIN_PATH."/admin.lib.php");

require dirname(__FILE__)."/../../vendor/autoload.php";

use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Lotto;


$lotto = new Lotto();
/*
//▶ include lib files

include_once("../../../../ace_solution/lib/ace.class.default.php");
include_once("../../../../ace_solution/modules/termService/lib/AceTermService.class.php");


$termService = new AceTermService();

include_once("../../../../ace_solution/lib/ace.class.default.php");
include_once("../../../../ace_solution/modules/lotto/lib/AceLottoService.class.php");
$lottoService = new AceLottoService();
*/
?>