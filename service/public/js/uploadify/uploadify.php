<?php
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

// Define a destination
$targetFolder = "/ace_solution/data/tmp"; // Relative to the root

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
		$fileinfo['fullpath'] = $targetFile;
		$fileinfo['urlPath'] = $targetFolder."/".$_FILES['Filedata']['name'];

		echo $fileinfo['fullpath']."::".$fileinfo['urlPath'];
	} else {
		echo 'Invalid file type.';
	}
}
?>