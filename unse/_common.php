<?
define(G5_PATH, "../");

include_once (G5_PATH."common.php");

require dirname(__FILE__)."/../service/vendor/autoload.php";

use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Lotto;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\TermService;

$lotto = new Lotto();
$lottoService = new LottoService();
?>