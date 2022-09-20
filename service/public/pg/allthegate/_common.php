<?

define(G5_PATH, "../../../../../");
//$g4_path = "../../../board"; // common.php 의 상대 경로

include_once (G5_PATH."/common.php");

//▶ include lib files
include_once("../../../../../ace_solution/lib/ace.class.default.php");
include_once("../../../../../ace_solution/modules/inauction/lib/AceInAuction.class.php");


$auction = new AceInAuction();

?>