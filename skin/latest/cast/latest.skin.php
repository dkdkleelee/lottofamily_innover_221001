<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

global $lottoServiceConfig;
$serviceConfig = $lottoServiceConfig->getConfig();

$imgwidth = 350; //표시할 이미지의 가로사이즈
$imgheight = 181; //표시할 이미지의 세로사이즈
?>
<a href="<?php for ($i=0; $i<count($list); $i++) { ?><?php echo $list[$i]['wr_link1'] ?><?php } ?>" target="_cast"><h2>추첨방송<p><img src="/images/main_pr_more.gif"></p></h2></a>
<div class="row4latest">
<style>
#cast { position:relative; padding:0;width:338px}
#cast .img_set{width:<?php echo $imgwidth ?>px; height:<?php echo $imgheight ?>px;background:#000}
#cast .img_set img{opacity:0.5}
#cast .img_set :after {
	content: '';
	width: 53px; height: 54px;
	position: absolute;
	top: 77px;
	left: 134px;
	z-index: 10;
	display: inline-block;
	background: url(./skin/latest/cast/img/play.png) no-repeat;
}
#cast .img_left{border:none}
#cast .subject_set{padding:10px 0 0 0;text-align:left;z-index:1; bottom:0; left:0;float:left;}
#cast .subject_set .sub_title{color:#353535;padding:0px 0 0 0;font-size:14px;line-height:30px;text-align:center}
#cast .subject_set .sub_title span{color:#353535}
#cast .subject_set .sub_content{color:#353535;font-size:12px;padding:8px 0 0;}


#cast ul {list-style:none;clear:both;margin:0;padding:0;}
#cast li{float:left;list-style:none;text-decoration:none;padding:10px 28px 0 0;}
#cast li:nth-child(even){float:left;list-style:none;text-decoration:none;padding:10px 0 0 0;}
.subject_set  a:link, a:visited {color:#353535;text-decoration:none}
.subject_set  a:hover, a:focus, a:active {color:#353535;text-decoration:none}

</style>
<div id="cast">
	<ul>
	<?php for ($i=0; $i<count($list); $i++) { ?>	
		<li>
			<div class="img_set">
				<a href="<?php echo $list[$i]['wr_link1'] ?>" target="_cast">
					<img src="./skin/latest/cast/img/cast.jpg" width="<?php echo $imgwidth ?>" height="<?php echo $imgheight ?>">
				</a>
			</div>
			<div class="subject_set">
				<!-- <div class="sub_title"><a href="<?php echo $list[$i]['wr_link1'] ?>" target="_cast"><?php echo cut_str($list[$i]['subject'], 40, "..") ?></a></div> -->
				<div class="sub_title"><a href="<?php echo $list[$i]['wr_link1'] ?>" target="_cast">[로또 <?=$serviceConfig['lc_cur_inning']?>회] 추첨방송 다시보기</a></div>
			</div>
		</li>
	<?php } ?>
	</ul>
</div>
<div style="clear:both;"></div>
</div>