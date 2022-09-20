<?
define('G5_IS_ADMIN', true);
define('NO_CACHE', true);
$sub_menu = "400100";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'r');

use Acesoft\Common\Utils;
use Acesoft\LottoApp\TermService;


//▶ pagination url
$param1 = Utils::getParameters(array('page','s_ca_no'))."&s_ca_no[]=".@array_pop(array_filter($_GET['s_ca_no']));
$list_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$param1;


// term service
$termService = new TermService();


//▶ default setting
$records_per_page = 300;
$page_per_block = 10;
$table = $termService->tables['term_service_config'];
$columns = "*";
$group = "";


$data = $termService->getTermServiceConfigList($_GET['page'], $list_url);



$g5['title'] = "서비스 목록";
include_once(G5_ADMIN_PATH."/admin.head.php");
?>
<script src="<?=$_url['modules']?>/inauction/js/jquery-tmpl/jquery.tmpl.min.js" ></script>
<script>
function getAddress1(obj, obj2, selValue) {
    
    var sido = $(obj).val();
    
	$.ajax({
		url: "./term_service_config_process.php",
		type: "post",
		data: "proc=getGugun&sido="+sido,
		cache: false,
		async: true,
		dataType: 'json',
		success: function(data) {
			$(obj2).find('option').remove();
			$(obj2).append("<option value='전체'>전체</option>");
			$(data).each(function(index, value) {
				
				$(obj2).append("<option value='"+value+"'>"+value+"</option>");
				
				if(selValue && value == selValue) {
    				$(obj2).val(value).attr("selected", "selected");
                }
    				
    			

			});
			
		}
	});
}

</script>
<link rel="stylesheet" href="../css/custom.style.css" type="text/css">
<link rel="stylesheet" href="../css/admin.custom.style.css" type="text/css">
<div class="info_container">

<div>
	<span class="sub_title"><i class="fa fa-folder-open-o"></i> <?=$title?></span>
</div>
<div class="content_wrap">
	
	<div>
		 <span class="sub_title"><i class="fa fa-plus fa-lg"></i> 상품추가</span>
	</div>
	<div>
		 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 유료서비스 상품을 추가합니다.</span>
		 <span class="help_text"><i class="fa fa-question-circle fa-lg f_blue"></i> 추가하신 상품은 사용자페이지의 서비스 구매 페이지에 나타납니다.</span>
	</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
    
	<tr>
		<th width="40px">번호</th>
		<th width="300px">서비스명</th>
		<th width="300px">설명</th>
		<th width="150px">구분</th>
		<th width="250px">가격</th>
		<th width="250px">할인전가격</th>
		<th width="120px">기간</th>
		<th width="120px">기간구분</th>
		<th width="120px">변경</th>
	</tr>
	
	<form name="add_service_form" method="post" action="./term_service_config_process.php">
    	<input type="hidden" name="proc" value="addTermServiceConfig">
		
		<tr>
        <td>
            추가
        </td>
		<td>
    		
    		<input type="text" name="sc_name" class="frm_input" value="" required style="width:88%">

		</td>
		<td>
    		
    		<input type="text" name="sc_detail1" class="frm_input" value="" required style="width:88%">

		</td>
        <td>
			<select name="sg_no" class="frm_input">
            <? foreach($termService->service_grade  as $key => $value) { ?>
			<option value="<?=$key?>"><?=$value?></option>
			<? } ?>
            </select>
        </td>
		<td>
    		
    		<input type="text" name="sc_price" class="frm_input" required value="" >원

		</td>
		<td>
    		
    		<input type="text" name="sc_pre_discount_price" class="frm_input" required value="" >원

		</td>
		
		
		<td><input type="text" name="sc_term" class="frm_input" class="frm_input" required size="5" value="">&nbsp;</td>
		
		<td>
    		<select name="sc_term_type" class="frm_input">
        		<option value="month">개월</option>
        		<option value="year">년</option>
        		<option value="day">일</option>
    		</select>
        </td>
		<td>
			
			<button type="submit" class="as-btn small blue"><i class="fa fa-plus"></i> 등록</button>
			
		</td>
	
	</tr>
	</form>
	</table>
	<br />
	<div>
		 <span class="sub_title"><i class="fa fa-plus fa-lg"></i> 상품목록</span>
	</div>
	<div class="search_box ar">
		<form name="searchForm" method="get" action="?">
    		구분 :
        	<select name="sg_no" class="frm_input">
				<option value="">선택</option>
        		<? foreach($termService->service_grade  as $key => $value) { ?>
					<option value="<?=$key?>" <?=$key == $_GET['sg_no'] ? 'selected' : ''?>><?=$value?></option>
				<? } ?>
        	</select>
        
        	
			<button type="button" class="as-btn medium white" onclick="document.searchForm.submit();"><i class="fa fa-search"></i> 검색</button>
        	
		</form>
	</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
    
	<tr>
		<th width="40px">번호</th>
		<th width="300px">서비스명</th>
		<th width="300px">설명</th>
		<th width="150px">구분</th>
		<th width="250px">가격</th>
		<th width="250px">할인전가격</th>
		<th width="120px">기간</th>
		<th width="120px">기간구분</th>
		<th width="120px">매진</th>
		<th width="120px">출력순서(큰수먼저)</th>
		<th width="120px">변경</th>
	</tr>
	<?	foreach($data['list'] as $row) { ?>
	<form name="modify_service_form_<?=$row['sc_no']?>" method="post" action="term_service_config_process.php">
    	<input type="hidden" name="proc" value="updateTermServiceConfig">
		<input type="hidden" name="sc_no" value="<?=$row['sc_no']?>">
		<tr>
        <td>
            <?=$i+1?>
        </td>
		<td>
    		<input type="text" name="sc_name" class="frm_input" value="<?=$row['sc_name']?>" style="width:88%">
		</td>
		<td>
    		
    		<input type="text" name="sc_detail1" class="frm_input" value="<?=$row['sc_detail1']?>" required style="width:88%">

		</td>
        <td>
			<select name="sg_no" class="frm_input">
            <? foreach($termService->service_grade  as $key => $value) { ?>
			<option value="<?=$key?>" <?=$row['sg_no'] == $key ? 'selected' : ''?>><?=$value?></option>
			<? } ?>
            </select>
        </td>
		<td>
    		
    		<input type="text" name="sc_price" class="frm_input" value="<?=$row['sc_price']?>" >원 / 월

		</td>
		<td>
    		
    		<input type="text" name="sc_pre_discount_price" class="frm_input" required  value="<?=$row['sc_pre_discount_price']?>" >원

		</td>
		
		<td><input type="text" name="sc_term" class="frm_input" class="frm_input" required size="5" value="<?=$row['sc_term']?>">&nbsp;</td>
		
		<td>
    		<select name="sc_term_type" class="frm_input">
        		<option value="month" <?=$row['sc_term_type'] == 'month' ? 'selected' : ''?>>개월</option>
        		<option value="year" <?=$row['sc_term_type'] == 'year' ? 'selected' : ''?>>년</option>
        		<option value="day" <?=$row['sc_term_type'] == 'day' ? 'selected' : ''?>>일</option>
    		</select>
        </td>
		<td><input type="checkbox" name="sc_soldout" class="frm_input"  size="5" value="1" <?=$row['sc_soldout'] == '1' ? 'checked' : ''?>>&nbsp;</td>
		<td><input type="text" name="sc_order" class="frm_input" required size="5" value="<?=$row['sc_order']?>">&nbsp;</td>
		<td>
			<button type="submit" class="as-btn small green"><i class="fa fa-refresh"></i> 수정</button>
			<button type="button" class="as-btn small red" onclick="deleteData('<?=$row['sc_no']?>')"><i class="fa fa-cancel"></i> 삭제</button>
		</td>
	</tr>
	</form>
	<? }?>
	
	</table>

</div>
<form name="deleteForm" method="post" action="./term_service_config_process.php?<?=$param?>">
<input type="hidden" name="proc" value="deleteTermServiceConfig">
<input type="hidden" name="no" value="">
</form>
<script type="text/javascript">
<!--

function addData() {
    var f = document.add_service_form;
    
    if(!f.sc_area1.value) {
        alert("시/도를 선택해 주세요.");
        f.sc_area1.focus();
        return false;
    }
    
    if(!f.sc_area2.value) {
        alert("구/군을 선택해 주세요.");
        f.sc_area2.focus();
        return false;
    }
    
    if(!f.sc_price.value) {
        alert("가격을 입력해 주세요.");
        f.sc_price.focus();
        return false;
    }
    
    if(!f.sc_term.value) {
        alert("기간을 입력해 주세요.");
        f.sc_term.focus();
        return false;
    }
    
    f.submit();
}

function modifyData(f) {
    
    if(!f.sc_area1.value) {
        alert("시/도를 선택해 주세요.");
        f.sc_area1.focus();
        return false;
    }
    
    if(!f.sc_area2.value) {
        alert("구/군을 선택해 주세요.");
        f.sc_area2.focus();
        return false;
    }
    
    if(!f.sc_price.value) {
        alert("가격을 입력해 주세요.");
        f.sc_price.focus();
        return false;
    }
    
    if(!f.sc_term.value) {
        alert("기간을 입력해 주세요.");
        f.sc_term.focus();
        return false;
    }
    
    f.submit();
}


function memoWin(no) {
	window.open("./ma_request_memo.php?type=ap_no&idx="+no, '', 'width=550, height=600');
}
function deleteData(no) {

	var f = document.deleteForm;
	if(confirm("데이터를 삭제합니다.\n삭제하신데이터는 복구가 불가능합니다.\n계속하시겠습니까?")) {
		f.no.value = no;
		f.submit();
	}

}
//-->
</script>
<?
include_once (G5_ADMIN_PATH."/admin.tail.php");
?>