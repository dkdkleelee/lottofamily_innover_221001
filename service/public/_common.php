<?
define(G5_PATH, "../../");

include_once (G5_PATH."common.php");

//error_log ($_POST['proc'], 3, "/var/log/httpd/error_log");
//echo '<script>console.log("test")</script>';

require dirname(__FILE__)."/../vendor/autoload.php";


use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Lotto;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\TermService;

$lotto = new Lotto();
//error_log ($_POST['proc'], 3, "/var/log/httpd/error_log");
//echo '<script>console.log("test")</script>';

$lottoService = new LottoService();


//error_log ($_POST['proc'], 3, "/var/log/httpd/error_log");
//echo '<script>console.log("test")</script>';

?>
