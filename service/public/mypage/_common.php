<?
define(G5_PATH, "../../../");

include_once (G5_PATH."common.php");

require __DIR__."/../../vendor/autoload.php";

use Acesoft\Common\Utils;
use Acesoft\LottoApp\Member\Auth;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Lotto;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\TermService;

$lotto = new Lotto();
$lottoService = new LottoService();

Auth::checkAuth();
?>