<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

$imgwidth = "246"; //표시할 이미지의 가로사이즈
$imgheight = "170"; //표시할 이미지의 세로사이즈

?>
<link rel="stylesheet" href="<?php echo $latest_skin_url ?>/style_gallery.css">
<link rel="stylesheet" href="<?php echo $latest_skin_url ?>/jquery.bxslider.css">
<script type="text/javascript" src="<?php echo $latest_skin_url ?>/jquery.bxslider.js"></script>

<script type="text/javascript">
// 메인 갤러리 슬라이드 적용
$(document).ready(function(){
  $('.slider1').bxSlider({
    auto:true,
	slideWidth: 246,
    minSlides: 3,
    maxSlides: 3,
    slideMargin: 10
  });
});


</script>

<!-- <?php echo $bo_subject; ?> 최신글 시작 { -->
<div class="gy">
	<div class="slider1">
	<?php for ($i=0; $i<count($list); $i++) { ?>
    	<div class="slide sbox">
    	<a nohref="<?php echo $list[$i]['href'] ?>">
    	<?php                
       		$thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $imgwidth, $imgheight);    					            
       		if($thumb['src']) {
           		$img_content = '<img src="'.$thumb['src'].'" alt="'.$list[$i]['subject'].'" width=246 height=170>';
        		} else {
           		$img_content = 'NO IMAGE';
        		}                
         	echo $img_content;												               
       ?>
       </a>
    		<div class="gy_cotent"><strong><a href="<?php echo $list[$i]['href'] ?>"><?php echo cut_str($list[$i]['subject'], 80, "..") ?></a></strong>
        	</div>
        </div>
    <?php } ?>
    <? if (count($list) == 0) { //게시물이 없을 때 ?>
    게시물이 없습니다.
    <? } ?>
    </div>   
</div>
<!-- } <?php echo $bo_subject; ?> 최신글 끝 -->