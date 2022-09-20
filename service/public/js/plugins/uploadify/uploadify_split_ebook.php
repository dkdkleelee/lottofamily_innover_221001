<?php
// include autoload
require_once dirname(__FILE__)."/../../../../vendor/autoload.php";

use \Intervention\Image\ImageManager;

/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

// initiate image manager
$manager = new ImageManager(array('driver' => 'gd'));

// Define a destination
$targetFolder = "/ns/data/tmp"; // Relative to the root

$verifyToken = md5('unique_salt' . $_POST['timestamp']);

if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	//$targetPath = iconv("UTF-8", "EUC-KR",$_SERVER['DOCUMENT_ROOT'] . $targetFolder);
	//$targetFile = iconv("UTF-8", "EUC-KR", rtrim($targetPath,'/') . '/') . iconv("UTF-8", "EUC-KR",$_FILES['Filedata']['name']);
	
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
	$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];
	
	// Validate the file type
	$fileTypes = array('jpg','jpeg','gif','png','gif','pdf','xls','doc','hwp'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	
	if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
		move_uploaded_file($tempFile,$targetFile);
		//$fileinfo['fullpath'] = iconv("EUC-KR", "UTF-8",$targetFile);
		// 이미지 쪼개기 및 리사이즈
		$image = $manager->make($targetFile);
		$width = $image->width();
		$height = $image->height();

		if($width > 1200) {
			$half = (int)($width/2);

			// 왼쪽페이지
			$image = $manager->make($targetFile);
			$pageLeft = rtrim($targetPath,'/') . '/l_' . $_FILES['Filedata']['name'];
			$image->crop($half, $height, 0, 0)->save($pageLeft);
			$fileinfo[0] = $pageLeft."::".$targetFolder."/l_".$_FILES['Filedata']['name'];


			// 오른쪽페이지
			$image = $manager->make($targetFile);
			$pageRight = rtrim($targetPath,'/') . '/r_' . $_FILES['Filedata']['name'];
			$image->crop($half, $height, $half+1, 0)->save($pageRight);
			$fileinfo[1] = $pageRight."::".$targetFolder."/r_".$_FILES['Filedata']['name'];

			
			

			echo implode("|", $fileinfo);

		} else {

			$fileinfo['fullpath'] = $targetFile;
			$fileinfo['urlPath'] = $targetFolder."/".$_FILES['Filedata']['name'];

			echo $fileinfo['fullpath']."::".$fileinfo['urlPath'];
		}
	} else {
		echo 'Invalid file type.';
	}
}
?>