<?
define('G5_IS_MANAGER', true);
define(G5_PATH, "../../../");

include_once (G5_PATH."common.php");
include_once(G5_MANAGER_PATH.'/admin.lib.php');

require dirname(__FILE__)."/../../vendor/autoload.php";

use Acesoft\Common\Utils;
use Acesoft\LottoApp\Member\Auth;
use Acesoft\LottoApp\Member\User;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Lotto;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\TermService;

$lotto = new Lotto();
$lottoService = new LottoService();


Auth::checkLogin(3);
?>