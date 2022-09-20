<?

// 파일업로드
function uploadFile($srcName, $destFile, $maxSize=10485760, $allows=array('jpg','jpeg','gif','png','bmp','pdf','hwp','doc','ppt','txt','xls')) {

	$regExp = "/[(\.".implode(")|(\.", $allows).")]$/i";

	$result['upload'] = false;
	if (preg_match($regExp, $_FILES[$srcName][name])) {
		// 아이콘 용량이 설정값보다 이하만 업로드 가능

		if ($_FILES[$srcName][size] <= $maxSize) 
		{
			// 중복파일명 이름변경
			if(is_file($destFile)) {
				$tmpFileName = basename($destFile);
				$tmpFileDir = str_replace($tmpFileName, "", $destFile);
				$tmpFileNameArr = explode(".", $tmpFileName);

				$fileCount = 1;
				while(is_file($destFile)) {
					$destFile = $tmpFileDir.$tmpFileNameArr[0]."(".$fileCount.").".$tmpFileNameArr[count($tmpFileNameArr)-1];
					$fileCount++;
				}
			}

			move_uploaded_file($_FILES[$srcName][tmp_name], $destFile);
			chmod($destFile, 0606);
			$result['upload'] = true;

		} else {
			$result['message'] .= "\\n[".$_FILES[$srcName][name]."] 업로드 용량을 초과하였습니다.";
		}
	} else {
		$result['message'] .= "\\n[".$_FILES[$srcName][name]."] 업로드가 불가능한 확장자 입니다.";
	}

	$result['destFile'] = $destFile;

	return $result;
}

function makeThumbs($oriPath, $oriFileName, $thmWidth="", $thmHeight="", $thmAlt="") {
	global $g4, $board_skin_path;

	$errorFilePrt = "<img src='$board_skin_path/images/noimage.gif' width='$thmWidth' height='$thmHeight' border=0 title='이미지 없음'>";

	$oriFile = $oriPath . "/" . $oriFileName;
	if (is_file($oriFile) == false) return $errorFilePrt; // 원본 부재

	$thmPath = $oriPath . "/thumbs";
	$thmFile = $thmPath . "/thum".$thmWidth."_" . $oriFileName;

	$oriSize = getimagesize($oriFile);
	$oriWidth = $oriSize[0];
	$oriHeight = $oriSize[1];
	$oriType = $oriSize[2];

	if ($oriType > 3) return $errorFilePrt; // 원본 이미지 타입 오류

	$oriRate = $oriWidth / $oriHeight;

	if ($thmWidth == "" && $thmHeight == "") return $errorFilePrt; // 썸네일 사이즈 미지정

	if ($thmWidth == "") $thmWidth = $thmHeight * $oriRate;
	if ($thmHeight == "") $thmHeight = $thmWidth / $oriRate;

	$widthRate = $thmWidth / $oriWidth;
	$heightRate = $thmHeight / $oriHeight;

	$oriFilePrt = "<img src=\"".urlencode($oriFile)."\" width=\"{$oriWidth}\" height=\"{$oriHeight}\" border=\"0\" alt=\"{$thmAlt}\" />";

	if ($widthRate >= 1 && $heightRate >= 1) { // 리사이징 불필요
		return $oriFilePrt;
	}
	
	if(is_file($thmFile))
		$oldSize = getimagesize($thmFile);
	
	if (($oldSize[0] == $thmWidth && $oldSize[1] == $thmHeight) && file_exists($thmFile)) { // 썸네일 유무
		$fp = fopen($thmFile, "r");
		$fstat = fstat($fp);
		$thmFileTime = $fstat['ctime'];
		fclose($fp);

		$fp = fopen($oriFile, "r");
		$fstat = fstat($fp);
		$oriFileTime = $fstat['ctime'];
		fclose($fp);

		if ($thmFileTime > $oriFileTime) { // 썸네일 갱신 불필요
			$thmSize = getimagesize($thmFile);
			$thmFilePrt = "<img src=\"".urlencode($thmFile)."\" width=\"{$thmSize[0]}\" height=\"{$thmSize[1]}\" border=\"0\" alt=\"{$thmAlt}\" />";
			return $thmFilePrt;
		} else {
			@unlink($thmFile);
		}
	}

	@mkdir($thmPath);
	@chmod($thmPath, 0707);

	if ($widthRate < $heightRate) {
		$tempWidth = (int)($oriWidth * $heightRate);
		$tempHeight = $thmHeight;
	} else {
		$tempWidth = $thmWidth;
		$tempHeight = (int)($oriHeight * $widthRate);
	}

	if ($tempWidth == "") $tempWidth = $thmWidth;
	if ($tempHeight == "") $tempHeight = $thmHeight;

	switch($oriType) {
		case(1) :
			if(function_exists('imagecreateFromGif')) $tempImage = imagecreateFromGif($oriFile);
			break;
		case(2) :
			if(function_exists('imagecreateFromJpeg')) $tempImage = imagecreateFromJpeg($oriFile);
			break;
		case(3) :
			if(function_exists('imagecreateFromPng')) $tempImage = imagecreateFromPng($oriFile);
			break;
	}

	if ($tempImage) {
		if (function_exists('imagecreatetruecolor')) {
			$tempCanvas = imagecreatetruecolor($thmWidth, $thmHeight);
		} else {
			$tempCanvas = imagecreate($thmWidth, $thmHeight);
		}

		if (function_exists('imagecopyresampled')) {
			imagecopyresampled($tempCanvas, $tempImage, 0, 0, 0, 0, $tempWidth, $tempHeight, ImageSX($tempImage), ImageSY($tempImage));
		} else {
			imagecopyresized($tempCanvas, $tempImage, 0, 0, 0, 0, $tempWidth, $tempHeight, ImageSX($tempImage), ImageSY($tempImage));
		}
		ImageDestroy($tempImage);
		ImageJpeg($tempCanvas, $thmFile, 100);
		ImageDestroy($tempCanvas);
		unset($tempImage, $tempCanvas);
	}

	$thmFilePrt = "<img src=\"".urlencode($thmFile)."\" width=\"{$thmWidth}\" height=\"{$thmHeight}\" border=\"0\" alt=\"{$thmAlt}\" />";

	return $thmFilePrt;
}


function getExtIcon($ext) {

	switch(strtolower($ext)) {
		case 'doc': $icon = "ico_doc.gif"; break;
		case 'xls': $icon = "ico_excel.gif"; break;
		case 'hwp': $icon = "ico_hwp.gif"; break;
		case 'jpg': $icon = "ico_jpg.gif"; break;
		case 'jpeg': $icon = "ico_jpg.gif"; break;
		case 'pdf': $icon = "ico_pdf.gif"; break;
		case 'ppt': $icon = "ico_ppt.gif"; break;
		default: $icon = "ico_txt.gif"; break;
	}

	return $icon;

}


function paintSearchWord($str, $word) {
	return str_replace($word, "<span style='background-color:#99ff99'>$word</span>", $str);
}

function getParameters($except_array=array()) {
	$temp_param = array_merge($_POST, $_GET);
	foreach($temp_param as $key => $value) {
		if(!in_array($key, $except_array)) {
			$param[] = $key."=".$value;
		}
	}
	$param = (count($param) > 0) ? implode("&", $param) : "";
	return $param;
}
?>