<?
// 
function makeThumbs($oriPath, $oriFileName, $thmWidth="", $thmHeight="", $thmAlt="", $blur="0", $src="0") {
	global $g4, $board_skin_path;

	ini_set("memory_limit", '150M');

	if($src == "0") {
		$errorFilePrt = "<img src='$board_skin_path/images/noimage.gif' width='$thmWidth' height='$thmHeight' border=0 title='�̹��� ����'>";
	} else {
		$errorFilePrt = "$board_skin_path/images/noimage.gif";
	}
	//$errorFilePrt = "<img src='$board_skin_path/images/noimage.gif' width='$thmWidth' height='$thmHeight' border=0 title='�̹��� ����'>";

	$oriFile = $oriPath . "/" . $oriFileName;
	if (is_file($oriFile) == false) return $errorFilePrt; // ���� ����

	$thmPath = $oriPath . "/thumbs";
	$thmFile = $thmPath . "/thum".$thmWidth."_".$blur."_".$oriFileName;

	$oriSize = getimagesize($oriFile);
	$oriWidth = $oriSize[0];
	$oriHeight = $oriSize[1];
	$oriType = $oriSize[2];

	if ($oriType > 3) return $errorFilePrt; // ���� �̹��� Ÿ�� ����

	$oriRate = $oriWidth / $oriHeight;

	if ($thmWidth == "" && $thmHeight == "") return $errorFilePrt; // ����� ������ ������

	if ($thmWidth == "") $thmWidth = $thmHeight * $oriRate;
	if ($thmHeight == "") $thmHeight = $thmWidth / $oriRate;

	$widthRate = $thmWidth / $oriWidth;
	$heightRate = $thmHeight / $oriHeight;

	$oriFilePrt = "<img src=\"".$oriFile."\" width=\"{$oriWidth}\" height=\"{$oriHeight}\" border=\"0\" alt=\"{$thmAlt}\" />";

	if ($widthRate >= 1 && $heightRate >= 1) { // ������¡ ���ʿ�
		if($src == "0") {
			return $oriFilePrt;
		} else {
			return $oriFile;
		}
	}
	
	if(is_file($thmFile))
		$oldSize = getimagesize($thmFile);
	
	if (($oldSize[0] == $thmWidth && $oldSize[1] == $thmHeight) && file_exists($thmFile)) { // ����� ����
		$fp = fopen($thmFile, "r");
		$fstat = fstat($fp);
		$thmFileTime = $fstat['ctime'];
		fclose($fp);

		$fp = fopen($oriFile, "r");
		$fstat = fstat($fp);
		$oriFileTime = $fstat['ctime'];
		fclose($fp);

		if ($thmFileTime > $oriFileTime) { // ����� ���� ���ʿ�
			$thmSize = getimagesize($thmFile);
			$thmFilePrt = "<img src=\"".$thmFile."\" width=\"{$thmSize[0]}\" height=\"{$thmSize[1]}\" border=\"0\" alt=\"{$thmAlt}\" />";
			if($src == "0") {
				return $thmFilePrt;
			} else {
				return $thmFile;
			}
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

		//blur
		/*
		for($i=0; $i<$blur; $i++) {
			imagefilter($tempImage, IMG_FILTER_GAUSSIAN_BLUR);
		}*/
		for($i=0; $i<$blur; $i++) {
			$gaussian = array(array(1.0, 2.0, 1.0), array(2.0, 4.0, 2.0), array(1.0, 2.0, 1.0));
			imageconvolution($tempCanvas, $gaussian, 16, 0);
		}

		ImageDestroy($tempImage);
		ImageJpeg($tempCanvas, $thmFile, 100);
		ImageDestroy($tempCanvas);
		unset($tempImage, $tempCanvas);
	}

	$thmFilePrt = "<img src=\"".$thmFile."\" width=\"{$thmWidth}\" height=\"{$thmHeight}\" border=\"0\" alt=\"{$thmAlt}\" />";

	if($src == "0") {
		return $thmFilePrt;
	} else {
		return $thmFile;
	}
}

function watermark($source, $watermark_src, $location='c', $transparency='100') {

	ini_set('memory_limit', '128M');

	list($srcW,$srcH,$img_type,$total_size,$bit,$img_mine) = @getImageSize($source);

	// �ش� �̹��� Ÿ�Կ� �°� GD���� �̿� Image Create
	if($img_type==1){ 
		$photo = imagecreatefromgif($source);//���� �̹���: gif 
	}else if($img_type==2){ 
		$photo = imagecreatefromjpeg($source);//���� �̹���: jpg 
	}else if($img_type==3){ 
		$photo = imagecreatefrompng($source);//���� �̹���: png 
	}

	$watermark = imagecreatefrompng($watermark_src);
	$watermark_width = imagesx($watermark); 
	$watermark_height = imagesy($watermark);

	$simg_W = $srcW;
	$simg_H = $srcH;

	 //location of the watermark on the source image 
	if($location == 'c') {
		$dest_x = ($simg_W - $watermark_width) / 2;
		$dest_y = ($simg_H - $watermark_height) / 2;
		#���߾�
	} else if($location == 'lt') {
		$dest_x = 0;
		$dest_y = 0;
		#�»��
	} else if($location == 'lb') {
		$dest_x = 0;
		$dest_y = ($simg_H - $watermark_height);
		#���ϴ�
	} else if($location == 'rt') {
		$dest_x = ($simg_W - $watermark_width);
		$dest_y = 0;
		#����
	} else if($location == 'rb') {
		$dest_x = ($simg_W - $watermark_width);
		$dest_y = ($simg_H - $watermark_height);
		#���ϴ�
	}

	$ol =  imagecreate($simg_W, $simg_H);
	$white = imagecolorallocate($ol, 255, 255, 255);
	imagefill($ol, 0, 0, $white);
	imagecopyresized($ol, $watermark, 0, 0, 0, 0, $simg_W, $simg_H,$srcW, $srcH);
	imageColorTransparent($ol, $white);

	//���������� �̹����� ���͸�ũ �̹����� ��ģ��.
	imagecopymerge($photo, $ol, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, $transparency);

	//output the image
	if($img_type==1){ 
		imagegif($photo, $source);//���� �̹���: gif 
	}else if($img_type==2){ 
		imagejpeg($photo, $source);//���� �̹���: jpg 
	}else if($img_type==3){ 
		imagepng($photo, $source);//���� �̹���: png 
	}

	imagedestroy($photo);
	imagedestroy($watermark);
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