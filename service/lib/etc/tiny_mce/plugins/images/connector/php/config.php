<?php
include_once("../../../../../../../lib/ace.class.default.php");
$ace = new AceSolution("../../../../../../../");

$userFolder = $_SESSION['ss_mb_id'] ? $_SESSION['ss_mb_id'] : "guest";
//$userRootDir = ($userRootDir) ? $userRootDir : $_dir['root']."/ace_solution/data/editor_images";
//$replaceDir = ($replaceDir) ? $replaceDir:"";

//$workDir = ($cdir) ? checkUrl($cdir) : $userRootDir;   //작업 디렉토리... 레지스트리에 등록
//$workDir = $workDir."/".$userFolder;

//$_SESSION['tiny_image_manager_path'] = $_SERVER['DOCUMENT_ROOT']."/".$workDir;

//Site root dir
define('DIR_ROOT', $_SERVER['DOCUMENT_ROOT']);
//Images dir (root relative)
define('DIR_IMAGES', "/board/ace_solution/data/editor_images/".$userFolder);
//Files dir (root relative)
define('DIR_FILES', "../../../../../data/editor_images/".$userFolder);

if(!is_dir(DIR_ROOT.DIR_IMAGES)) {
	mkdir(DIR_ROOT.DIR_IMAGES, 0707, true);
	@chmod(DIR_ROOT.DIR_IMAGES, 0707);
}

//Width and height of resized image
define('WIDTH_TO_LINK', 500);
define('HEIGHT_TO_LINK', 500);

//Additional attributes class and rel
define('CLASS_LINK', 'lightview');
define('REL_LINK', 'lightbox');

date_default_timezone_set('Asia/Yekaterinburg');
?>
