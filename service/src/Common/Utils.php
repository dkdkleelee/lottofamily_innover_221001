<?php
/**
 * Created by PhpStorm.
 * User: acerunner
 * Date: 16. 2. 1.
 * Time: 오전 11:36
 */

namespace Acesoft\Common;

use \MysqliDb;
use \Acesoft\Core\DB as DB;

class Utils {


	public static function getList($page, $table, $records_per_page, $where='', $order='', $group='', $columns='*') {
		$db = DB::getNewInstance();

		$page = ($page) ? $page : 1;
		
		$db->arrayBuilder()->rawQuery("SELECT ".$columns." FROM ".$table." ".$where." ".$group);
		$total_record = $db->count;

		$total_page = ceil($total_record/$records_per_page);
		$offset = ($page-1)*$records_per_page;
		$record_num = $total_record-$offset;
		$list = $db->arrayBuilder()->rawQuery("SELECT ".$columns." FROM ".$table." ".$where." ".$group." ".$order." LIMIT ".$offset.", ".$records_per_page);
//echo "<!-- "."SELECT ".$columns." FROM ".$table." ".$where." ".$group." ".$order." LIMIT ".$offset.", ".$records_per_page." -->";
		$data['list'] = $list;
		$data['total_count'] = $total_record;
		$data['total_pages'] = $total_page;
		$data['idx'] = $page == 1 ? $db->totalCount : $db->totalCount - ($records_per_page*($page-1));

		return $data;
	}

    /**
     * @param $total_page
     * @param $cur_page
     * @param string $list_url
     * @param string $page_per_block
     * @param string $page_param_name
     * @return string
     */
    public static function getPagination($total_page, $cur_page, $list_url='', $page_per_block='10', $page_param_name='page')
    {

            if(!$list_url) $list_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['DOCUMENT_URI']."?";

            $cur_page = ($cur_page) ? $cur_page : 1;
            $total_block = ceil($total_page/$page_per_block);
            $cur_block = ceil($cur_page/$page_per_block);
            $start_num = ($cur_block > 1) ? (($cur_block-1)*$page_per_block)+1 : 1;
            $last_num = ($start_num+$page_per_block-1 < $total_page) ? $start_num+$page_per_block-1 : $total_page;

            if($cur_block > 1) $page_link = "<li><a href='".$list_url."&".$page_param_name."=".($start_num-1)."' class=''><span  class=''>이전".$page_per_block."개</span></a></li>";
            if($cur_block > 1) $page_link .= "<li><a href='".$list_url."&".$page_param_name."=1'><span  class=''>1</span></a></li>..";


            for($i=$start_num; $i<=$last_num; $i++) {
                if($i == $cur_page) {
                    $page_link .= "<li><a class='active'>".$i."</a></li>";
                } else {
                    $page_link .= "<li><a href='".$list_url."&".$page_param_name."=".$i."' class=''><span >".$i."</span></a></li>";
                }
            }

            if($cur_block < $total_block) $page_link .= "<li>...<span><a  class='' href='".$list_url."&".$page_param_name."=".$total_page."'>".$total_page."</a></span></li>";
            if($cur_block < $total_block) $page_link .= "<li><a class='' href='".$list_url."&".$page_param_name."=".($last_num+1)."'><span >다음".$page_per_block."개</span></a></li>";
            return $page_link;

    }

	/**
     * @param $total_page
     * @param $cur_page
     * @param string $list_url
     * @param string $page_per_block
     * @param string $page_param_name
     * @return string
     */
    public static function getBasicPagination($total_page, $cur_page, $list_url='', $page_per_block='10', $page_param_name='page')
    {

            if(!$list_url) $list_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['DOCUMENT_URI']."?";

            $cur_page = ($cur_page) ? $cur_page : 1;
            $total_block = ceil($total_page/$page_per_block);
            $cur_block = ceil($cur_page/$page_per_block);
            $start_num = ($cur_block > 1) ? (($cur_block-1)*$page_per_block)+1 : 1;
            $last_num = ($start_num+$page_per_block-1 < $total_page) ? $start_num+$page_per_block-1 : $total_page;

            if($cur_block > 1) $page_link = "<li><a href='".$list_url."&".$page_param_name."=".($start_num-1)."' class=''><span  class=''>이전".$page_per_block."개</span></a></li>";
            if($cur_block > 1) $page_link .= "<li><a href='".$list_url."&".$page_param_name."=1'><span  class=''>1</span></a></li>..";


            for($i=$start_num; $i<=$last_num; $i++) {
                if($i == $cur_page) {
                    $page_link .= "<li><a class='active'>".$i."</a></li>";
                } else {
                    $page_link .= "<li><a href='".$list_url."&".$page_param_name."=".$i."' class=''><span >".$i."</span></a></li>";
                }
            }

            if($cur_block < $total_block) $page_link .= "<li>...<span><a  class='' href='".$list_url."&".$page_param_name."=".$total_page."'>".$total_page."</a></span></li>";
            if($cur_block < $total_block) $page_link .= "<li><a class='' href='".$list_url."&".$page_param_name."=".($last_num+1)."'><span >다음".$page_per_block."개</span></a></li>";
            return $page_link;

    }

    /**
     * @param array $except_array
     * @param string $type
     * @return array|string
     */
    public static function getParameters($except_array=array(), $type='except')
    {
		array_push($except_array, array('nocache'));

		if(empty($_POST) && empty($_GET)) {
		//if(count($_POST) == 0 && count($_GET) == 0) {
			return null;
		}

        $temp_param = array_merge($_POST, $_GET);

		$param = array();

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



    /**
     * @param $srcName
     * @param $destFile
     * @param int $maxSize
     * @param array $allows
     * @return mixed
     */
    public static function uploadFile($srcName, $destFile, $maxSize=50485760, $allows=array('jpg','jpeg','gif','png','bmp','pdf','hwp','doc','ppt','pptx','txt','xls','xlsx'))
    {

        $regExp = "/[(\.".implode(")|(\.", $allows).")]$/i";

        $result['upload'] = false;
        if (preg_match($regExp, $_FILES[$srcName]['name'])) {
            // 아이콘 용량이 설정값보다 이하만 업로드 가능

            if ($_FILES[$srcName]['size'] <= $maxSize)
            {
                // 중복파일명 이름변경
                if(is_file($destFile)) {
                    $tmpFileName = basename($destFile); //$_FILES[$srcName][name];
                    $tmpFileDir = str_replace($tmpFileName, "", $destFile);
                    $tmpFileNameArr = explode(".", $tmpFileName);

                    $fileCount = 1;
                    while(is_file($destFile)) {
                        $tmpFileName = $tmpFileNameArr[0]."(".$fileCount.").".$tmpFileNameArr[count($tmpFileNameArr)-1];
                        $destFile = $tmpFileDir."/".$tmpFileName;
                        $fileCount++;
                    }
                }

                move_uploaded_file($_FILES[$srcName]['tmp_name'], $destFile);
                chmod($destFile, 0606);
                $result['upload'] = true;

				$result['oriFile'] = $_FILES[$srcName]['name'];
				$result['type'] = @getimagesize($destFile)[2];
				$result['size'] = $_FILES[$srcName]['size'];
				$result['ext'] = strtolower(array_pop(explode('.', $_FILES[$srcName]['name'])));
            } else {
                $result['message'] .= "\\n[".$_FILES[$srcName]['name']."] 업로드 용량을 초과하였습니다.";
            }
        } else {
            $result['message'] .= "\\n[".$_FILES[$srcName]['name']."] 업로드가 불가능한 확장자 입니다.";
        }

        $result['destFile'] = $destFile;

        return $result;
    }


    public static function goUrl($url='', $msg='',$parent='0')
    {
        if($msg) echo "<script>alert('".$msg."');</script>\n";
        if($parent) echo "<script>opener.location.reload();</script>\n";
        if($url) {
            echo "<script>location.href='".$url."&nocache='+ (new Date()).getTime();</script>\n";
        } else {
            echo "<script>history.back(-1);</script>\n";
        }

        exit;
    }

	public static function closeWin($msg, $parent='0') {
		if($msg) echo "<script>alert('".$msg."');</script>\n";
        if($parent) echo "<script>opener.location.reload();</script>\n";
		echo "<script>window.close();</script>\n";
	}

    public static function message($msg, $type)
    {
        if($msg) echo "<script>alert('".$msg."');</script>\n";
        switch($type) {
            case 'close': echo "<script>window.close();</script>\n";
        }
    }

	public static function getRandomString($length=10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public static function arrangeTelNumber($num) {
		$ddd = array('02','031','032','033','041','042','043','051','052','053','054 ','055','061','062','063','064');
		if(trim($num) && strpos($num, "-") === false) {
			foreach($ddd as $value) {
				if($tail = strstr($num, $value)) break;
			}
			$tel = $value;
			$tel_tmp = str_replace($tel, '', $num);
			$tel .= "-".(strlen($tel_tmp) == 7 ? substr($tel_tmp, 0, 3)."-".substr($tel_tmp, 3, 4) : substr($tel_tmp, 0, 4)."-".substr($tel_tmp, 4, 4));
			$num = $tel;
		}

		return $num;
	}

	public static function arrangeHPNumber($num) {

		$not_found = true;
		$ddd = array('010','011','019','018','016','017');
		if(trim($num) && strpos($num, "-") === false) {
			foreach($ddd as $value) {
				if($tail = strstr($num, $value)) {
					$not_found = false;
					break;
				}
			}

			if($not_found) return $num;
			$tel = $value;
			$tel_tmp = str_replace($tel, '', $num);
			$tel .= "-".(strlen($tel_tmp) == 7 ? substr($tel_tmp, 0, 3)."-".substr($tel_tmp, 3, 4) : substr($tel_tmp, 0, 4)."-".substr($tel_tmp, 4, 4));
			$num = $tel;
		}

		return $num;
	}

	public static function arrangeComNumber($num) {
		if(trim($num) && strpos($num, "-") === false) {
			$num = substr($num, 0, 3)."-".substr($num, 3, 2)."-".substr($num, 5, 5);
		}

		return $num;
	}

	public static function textCut($str, $len, $suffix = '...') {

		$arr_str = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
		$str_len = count($arr_str);

		if ($str_len >= $len) {
			$slice_str = array_slice($arr_str, 0, $len);
			$str = join("", $slice_str);

			return $str . ($str_len > $len ? $suffix : '');
		} else {
			$str = join("", $arr_str);
			return $str;
		}
		
	}

	public static function getContent($content) {
		return stripslashes($content);
	}

	// 유료업체, 상품 뷰에따른 orderBy 
	// $type = pdt, ctg
	public static function getOrderBy($type) {
		$db = DB::getNewInstance();

		switch($type) {

			case 'pdt':
				$db->where('vl_type', 'pdt');
				$db->where('WEEKOFYEAR(vl_datetime) = WEEKOFYEAR(NOW())-1');
				$db->groupBy('vl_type_id');
				$list_cnt = $db->arrayBuilder()->get('View_log', null, 'SUM(vl_count) as v_cnt, vl_type_id');

				
				break;

			case 'ctg': 

				break;
		}
	}

	public static function in_array_r($needle, $haystack, $strict = false) {
		foreach ($haystack as $item) {
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
				return true;
			}
		}

		return false;
	}


	public static function isAnimatedGif($filename) {
		return (bool)preg_match('#(\x00\x21\xF9\x04.{4}\x00\x2C.*){2,}#s', file_get_contents($filename));
	}

	public static function str_rev_ip($str, $pos=3, $mask='♡') { 
		global $is_admin; 

		$ar=explode(".",$str); 
		if (!$is_admin) $ar[4 - $pos] = $mask; 
		return "$ar[3].$ar[2].$ar[1].$ar[0]"; 
	}

	public static function getKorCount($mny, $st=0) {
		
		//숫자를 4단위로 한글 단위를 붙인다.
		//num_to_han_s('123456789') -> 1억2345만6789 
		//num_to_han_s('123456789',4) -> 1억2345만
		//num_to_han_s('123456789',6) -> 1억2345만 //무조건 4단위로 끊음
		$j2 = array("","만","억","조","경"); // 단위의 한글발음 (조 다음으로 계속 추가 가능)
		$arr=array();
		$m=strlen($mny);
		for($i=0;$i<$m;$i++){
			$arr[]=$mny{$i};
		}
		$arr = array_reverse($arr);
		$arrj1 = array();
		$arrj2 = array();
		for($i=0,$m=count($arr);$i<$m;$i++){
		//  $arrj1[] = $j1[$i%4]; 
			$arrj2[] = $j2[floor($i/4)];
		}
		$cu = '';
		$mstr = '';
		$st = floor($st/4)*4;
		for($i=$st,$m=count($arr);$i<$m;$i++){
			$t = $arr[$i];
			if($cu != $arrj2[$i]){
				$cu = $arrj2[$i];
				$t.=$cu;
			}
			$mstr = $t.$mstr;
		}
		return($mstr); 
	}

	public static function convertSize($size) {
		$unit=array('b','kb','mb','gb','tb','pb');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}


	// 수동추출 차단시간
	public static function isShutdown() {
		$weekday = date('w');
		$hour = date('H');

		if($weekday == '6' && $hour > 20) {
			return true;
		} else if($weekday == '0') {
			return true;
		}

		return false;
	}
}