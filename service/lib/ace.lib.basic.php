<?


function getList($page, $table, $records_per_page, $where='', $order='', $group='', $columns='*') {
	global $db, $_table, $total_record, $total_page, $record_num;
	$page = ($page) ? $page : 1;
	$db->query("SELECT ".$columns." FROM ".$table." ".$where." ".$group, 0);
	$total_record = $db->numrows(0);

	$total_page = ceil($total_record/$records_per_page);
	$offset = ($page-1)*$records_per_page;
	$record_num = $total_record-$offset;
	$db->query("SELECT ".$columns." FROM ".$table." ".$where." ".$group." ".$order." LIMIT ".$offset.", ".$records_per_page, 0);
}


function getPaging($cur_page, $list_url, $page_per_block='5', $page_param_name='page') {
	global $total_page;

	$cur_page = ($cur_page) ? $cur_page : 1;
	$total_block = ceil($total_page/$page_per_block);
	$cur_block = ceil($cur_page/$page_per_block);
	$start_num = ($cur_block > 1) ? (($cur_block-1)*$page_per_block)+1 : 1;
	$last_num = ($start_num+$page_per_block-1 < $total_page) ? $start_num+$page_per_block-1 : $total_page;

	if($cur_block > 1) $page_link = "<a href='".$list_url."&".$page_param_name."=".($start_num-1)."' class='page_num'><span  class='page_num'>이전".$page_per_block."개</span></a>";
	if($cur_block > 1) $page_link .= "<a href='".$list_url."&".$page_param_name."=1'><span  class='page_num'>1</span></a>..";
	

	for($i=$start_num; $i<=$last_num; $i++) {
		if($i == $cur_page) {
			$page_link .= "<span  class='page_cur'>".$i."</span>";
		} else {
			$page_link .= "<a href='".$list_url."&".$page_param_name."=".$i."' class='page_num'><span >".$i."</span></a>";
		}
	}

	if($cur_block < $total_block) $page_link .= "...<span><a  class='page_num' href='".$list_url."&".$page_param_name."=".$total_page."'>".$total_page."</a></span>";
	if($cur_block < $total_block) $page_link .= "<a class='page_num' href='".$list_url."&".$page_param_name."=".($last_num+1)."'><span >다음".$page_per_block."개</span></a>";
	return $page_link;
}

function getParameters($except_array=array(), $type='except') {
	$temp_param = array_merge($_POST, $_GET);
	foreach($temp_param as $key => $value) {
		if($type=='except') {
			if(!in_array($key, $except_array)) {
				$param[] = $key."=".$value;
			}
		} else {
			if(in_array($key, $except_array)) {
				$param[] = $key."=".$value;
			}
		}
	}
	$param = (count($param) > 0) ? implode("&", $param) : "";
	return $param;
}


function table_exists($tablename) {
	global $db, $_conf;

	$db->listTables($_conf['db']['db'], 1);
	while($table = $db->fetch(1)) {
		if($table[0] == $tablename) {
			return true;
		}
	}
	return false;
}


// type = 1 : value => $value, index => $value
// type = 2 : value => $value, index => $key
function getOptions($array, $match='', $type='0') {
	$num = 0;
	foreach($array as $key => $value) {
		
		if($type == '1') {
			$selected = ($key == $match) ? "selected" : "";
			$options .= "<option value=\"".$key."\" ".$selected.">".$value."</option>";
		} else if($type == '2') {
			$selected = ($value == $match) ? "selected" : "";
			$options .= "<option value=\"".$value."\" ".$selected.">".$value."</option>";
		} else {
			$selected = ($value == $match) ? "selected" : "";
			$options .= "<option value=\"".$value."\" ".$selected.">".$key."</option>";
		}
		$num++;
	}
	return $options;
}

// 파일업로드
function uploadFile($srcName, $destFile, $maxSize=10485760, $allows=array('jpg','jpeg','gif','png','bmp','pdf','hwp','doc','ppt','pptx','txt','xls','xlsx')) {

	$regExp = "/[(\.".implode(")|(\.", $allows).")]$/i";

	$result['upload'] = false;
	if (preg_match($regExp, $_FILES[$srcName][name])) {
		// 아이콘 용량이 설정값보다 이하만 업로드 가능

		if ($_FILES[$srcName][size] <= $maxSize) 
		{
			// 중복파일명 이름변경
			if(is_file($destFile)) {
				$tmpFileName = $_FILES[$srcName][name];
				$tmpFileDir = str_replace($tmpFileName, "", $destFile);
				$tmpFileNameArr = explode(".", $tmpFileName);

				$fileCount = 1;
				while(is_file($destFile)) {
    				$tmpFileName = $tmpFileNameArr[0]."(".$fileCount.").".$tmpFileNameArr[count($tmpFileNameArr)-1];
					$destFile = $tmpFileDir."/".$tmpFileName;
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

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

function get_post_max() {
	$val = ini_get('post_max_size');
	$data['size'] = trim($val);
    $data['last'] = $val[strlen($val)-1];

	return $data;
}

function goUrl($url='', $msg='',$parent='0') {
	if($msg) echo "<script>alert('".$msg."');</script>\n";
	if($parent) echo "<script>opener.location.reload();</script>\n";
	if($url) {
		echo "<script>location.href='".$url."';</script>\n";
	} else {
		echo "<script>history.back(-1);</script>\n";
	}

	exit;
}

function message($msg, $type) {
    if($msg) echo "<script>alert('".$msg."');</script>\n";
    switch($type) {
        case 'close': echo "<script>window.close();</script>\n";
    }
}

function textCut($str,$limit,$suffix = '...') {
	$len = strlen($str);
	if($limit && $limit < $len ) {
		$str = substr($str,0,$limit);
		for($i=0,$han=0;$i<$limit; $i++) {
			if(ord($str[$i]) > 127) $han++;
		}
		if($han%2 == 1) $limit--;
		$str = substr($str,0,$limit).$suffix;
	}
	return $str;
}

function getWeekdayName($num) {
	switch($num) {
		case "0": $name = "월"; break;
		case "1": $name = "화"; break;
		case "2": $name = "수"; break;
		case "3": $name = "목"; break;
		case "4": $name = "금"; break;
		case "5": $name = "토"; break;
		case "6": $name = "일"; break;
	}

	return $name;
}

//***************************************************************************************
// 입력 string의 절반을 *로 변경
// 
//
//***************************************************************************************
function hidePartString($str, $head_tail='tail', $char_set='utf-8') {

	$len = mb_strlen($str, $char_set);
	if($len == 0) return "*****";
	$len_half = $len/2;
	$tmpLen = $len_half;
	$hide = "";
	while($tmpLen > 0) {
		$hide .= "*";
		$tmpLen--;
	}

	if($head_tail == "tail") {
		return mb_substr($str, 0, $len_half, $char_set).$hide;
	} else {
		return $hide.mb_substr($str, $len_half, $len, $char_set);
	}
}


//***************************************************************************************
// recursive array의 key, value 인코딩 변경
// $_POST = iconv2("utf-8","euc-kr",$_POST); 
//
//***************************************************************************************
function iconv2( $Current, $Next, $Array , $AutoDetect = true ) { 
	if(!is_array($Array)) return iconv($Current,$Next,$Array); // 이 한줄만 추가되었습니다. 
	$new_array = array(); 
	$Current = strtoupper($Current); 
	$Next = strtoupper($Next); 
	$encode = array($Current,str_replace('//IGNORE','',$Next)); 
	foreach($Array as $key => $val) { 
		if(is_string($key) && (($AutoDetect == true && mb_detect_encoding($key,$encode) == $Current) || $AutoDetect == false)) { 
		$key = iconv($Current, $Next, $key); 
	} 
	if(is_string($val) && (($AutoDetect == true && mb_detect_encoding($val,$encode) == $Current) || $AutoDetect == false)) { 
		$val = iconv($Current, $Next, $val); 
	} 
	if(is_array($val)) $val = iconv2( $Current, $Next, $val, $AutoDetect); 
		$new_array[$key] = $val; 
	} 
	return $new_array; 
}


function py2m2($py) {
	$m2 = ($py*3.3058);
	return sprintf("%.1f",$m2)."㎡";
}

function getShortSido($sido) {
	switch($sido) {
		case "서울특별시" : $sido = "서울"; break;
		case "인천광역시" : $sido = "인천"; break;
		case "경기도" : $sido = "경기"; break;
		case "부산광역시" : $sido = "부산"; break;
		case "경상남도" : $sido = "경남"; break;
		case "경상북도" : $sido = "경북"; break;
		case "전라남도" : $sido = "전남"; break;
		case "전라북도" : $sido = "전북"; break;
		case "대구광역시" : $sido = "대구"; break;
		case "대전광역시" : $sido = "대전"; break;
		case "세종특별자치시" : $sido = "세종"; break;
		case "제주특별자치도" : $sido = "제주도"; break;
	}

	return $sido;
}
?>