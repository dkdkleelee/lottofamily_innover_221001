<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

$imgwidth = 150; //표시할 이미지의 가로사이즈
$imgheight = 90; //표시할 이미지의 세로사이즈
?>

<style>
#gallery { position:relative; padding:0;width:338px}
#gallery .img_set{width:<?php echo $imgwidth ?>px; height:<?php echo $imgheight ?>px;}
#gallery .img_left{border:none}
#gallery .subject_set{width:150px; height:38px;text-align:left;z-index:1; bottom:0; left:0;float:left;}
#gallery .subject_set .sub_title{color:#353535;padding:0px 0 0 0;font-size:14px;line-height:30px;text-align:center}
#gallery .subject_set .sub_title span{color:#353535}
#gallery .subject_set .sub_content{color:#353535;font-size:12px;padding:8px 0 0;}


#gallery ul {list-style:none;clear:both;margin:0;padding:0;}
#gallery li{float:left;list-style:none;text-decoration:none;padding:10px 28px 0 0;}
#gallery li:nth-child(even){float:left;list-style:none;text-decoration:none;padding:10px 0 0 0;}
.subject_set  a:link, a:visited {color:#353535;text-decoration:none}
.subject_set  a:hover, a:focus, a:active {color:#353535;text-decoration:none}

</style>
<div id="gallery">
	<ul>
	<?php for ($i=0; $i<count($list); $i++) { ?>	
		<li>
			<div class="img_set">
				<a href="<?php echo $list[$i]['href'] ?>">
					<?php                
					$thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $imgwidth, $imgheight);    					            
					if($thumb['src']) {
					$img_content = '<img class="img_left" src="'.$thumb['src'].'" alt="'.$list[$i]['subject'].'" width="'.$imgwidth.'" height="'.$imgheight.'">';
					} else {
					$img_content = 'NO IMAGE';
					}                
					echo $img_content;												               
					?>
				</a>
			</div>
			<div class="subject_set">
				<div class="sub_title"><a href="<?php echo $list[$i]['href'] ?>"><?php echo cut_str($list[$i]['subject'], 23, "..") ?></a></div>
			</div>
		</li>
	<?php } ?>
	</ul>
</div>
<div style="clear:both;"></div>
