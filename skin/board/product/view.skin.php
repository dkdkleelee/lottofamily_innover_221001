<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>

<script src="<?php echo G5_JS_URL; ?>/viewimageresize.js"></script>
<script src="<?=$board_skin_url?>/js/jquery.cycle2.min.js"></script>
<!-- 게시물 읽기 시작 { -->
<!-- <div id="bo_v_table"><?php echo $board['bo_subject']; ?></div> -->

<article id="bo_v" style="width:<?php echo $width; ?>">
    <!-- 게시물 상단 버튼 시작 { -->
    <div id="bo_v_top">
        <?php
        ob_start();
         ?>
        <?php if ($prev_href || $next_href) { ?>
        <ul class="bo_v_nb">
            <?php if ($prev_href) { ?><li><a href="<?php echo $prev_href ?>" class="btn_b01">이전글</a></li><?php } ?>
            <?php if ($next_href) { ?><li><a href="<?php echo $next_href ?>" class="btn_b01">다음글</a></li><?php } ?>
        </ul>
        <?php } ?>

        <ul class="bo_v_com">
            <?php if ($update_href) { ?><li><a href="<?php echo $update_href ?>" class="btn_b01">수정</a></li><?php } ?>
            <?php if ($delete_href) { ?><li><a href="<?php echo $delete_href ?>" class="btn_b01" onclick="del(this.href); return false;">삭제</a></li><?php } ?>
            <?php if ($copy_href) { ?><li><a href="<?php echo $copy_href ?>" class="btn_admin" onclick="board_move(this.href); return false;">복사</a></li><?php } ?>
            <?php if ($move_href) { ?><li><a href="<?php echo $move_href ?>" class="btn_admin" onclick="board_move(this.href); return false;">이동</a></li><?php } ?>
            <?php if ($search_href) { ?><li><a href="<?php echo $search_href ?>" class="btn_b01">검색</a></li><?php } ?>
            <li><a href="<?php echo $list_href ?>" class="btn_b01">목록</a></li>
            <?php if ($reply_href) { ?><li><a href="<?php echo $reply_href ?>" class="btn_b01">답변</a></li><?php } ?>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02">글쓰기</a></li><?php } ?>
        </ul>
        <?php
        $link_buttons = ob_get_contents();
        ob_end_flush();
         ?>
    </div>
    <!-- } 게시물 상단 버튼 끝 -->

    <section id="bo_v_atc">
        <h2 id="bo_v_atc_title">본문</h2>
			<div>
	
				<div class="td_web_img" style="float:left; text-align:center;border:0px solid #c3c3c3;">
				
				
				<!-- 포트폴리오 썸네일 -->
				<div class="bthum" style="position:relative;float:left;padding:0 100px 0 0">
					<a href="#" style="position:absolute;top:115px;z-index:999;left:0px;display:block" class="previous slidesjs-navigation"><img src="/images/btn_prev.png" alt="" title=""></a>
					<a href="#" style="position:absolute;top:115px;z-index:999;left:310px;display:block" class="next slidesjs-navigation"><img src="/images/btn_next.png" alt="" title=""></a>
					<div class="cycle-slideshow" style="width:350px;overflow:hidden;border:1px solid #e8e8e8"
						data-cycle-slides="> img"
						data-cycle-fx=fade
						data-cycle-timeout=2000
						data-cycle-pause-on-hover="true"
						data-cycle-pager="#adv-custom-pager"
						data-cycle-prev=".previous"
						data-cycle-next=".next"
						data-cycle-pager-template="<a href='#'><span style='margin-left:0x;padding:0px;border:1px solid #e8e8e8;float:left;width:80px;height:60px;overflow:hidden;'><img class='cycle-thumb' src='{{src}}' width='80' height='60'></span></a>"
						>
					
					<?php
					// 파일 출력
					$v_img_count = count($view['file']);
					if($v_img_count) {
						
						for ($i=0; $i<=count($view['file']); $i++) {
							if ($view['file'][$i]['file']) {
								echo "<img width='350' src='".$view['file'][$i]['path']."/".$view['file'][$i]['file']."'>";
								
							}
						}
					}
					 ?>
					</div>
					<!-- empty element for pager links -->
					<div id=adv-custom-pager class="center external" style="margin-top:5px;margin-bottom:10px"></div>
				</div>
				
				
				
				<?php
				/*
				// 파일 출력
				$v_img_count = count($view['file']);
				if($v_img_count) {
					echo "<div id=\"bo_v_img\">\n";
							echo get_view_thumbnail($view['file'][0]['view'], 400);
					}
					echo "</div>\n";
				*/
				 ?>
				<div width="100%" id="bo_v_atc_content" style="float:right">
					<table width="450" class="table">
						<tr>
							<td colspan="2" class="title">
								<?php echo $view['subject'] ?>
								<span class="desc"><?php echo $view['ca_name'] ?></span>
							</td>
						</tr>
						<tr>
							<td colspan="2" style="padding:20px 0 30PX 0"><?php echo nl2br($view[wr_1])?></td>
						</tr>
						<? if($view['wr_2']) { ?>
						<tr>
							<th>모델명</th>
							<td><?php echo nl2br($view[wr_2])?></td>
						</tr>
						<? } ?>
						<!--
						<? if($view['wr_3']) { ?>
						<tr>
							<th>Size</th>
							<td><?php echo nl2br($view[wr_3])?></td>
						</tr>
						<? } ?>
						<? if($view['wr_4']) { ?>
						<tr>
							<th>Color Temperature</th>
							<td><?php echo nl2br($view[wr_4])?></td>
						</tr>
						<? } ?>
						<? if($view['wr_5']) { ?>
						<tr>
							<th>Illumination angle</th>
							<td><?php echo nl2br($view[wr_5])?></td>
						</tr>
						<? } ?>
						<? if($view['wr_6']) { ?>
						<tr>
							<th>Center illuminance</th>
							<td><?php echo nl2br($view[wr_6])?></td>
						</tr>
						<? } ?>
						-->
					</table>

				</div>
			</div>
		<div style="clear:both"></div>

		<div class="info_tab_wrap">
			<div class="info_tab">
				<ul>
					<li class="info_item on"><a href="javascript:info(0)"><div class="active">상세설명</div></a></li>
				</ul>
			</div>
		</div>
        <!-- 본문 내용 시작 { -->
        <div id="bo_v_con"><?php echo get_view_thumbnail($view['content']); ?></div>
        <?php//echo $view['rich_content']; // {이미지:0} 과 같은 코드를 사용할 경우 ?>
        <!-- } 본문 내용 끝 -->
		</div>

        <?php if ($is_signature) { ?><p><?php echo $signature ?></p><?php } ?>

        <!-- 스크랩 추천 비추천 시작 { -->
        <?php if ($scrap_href || $good_href || $nogood_href) { ?>
        <div id="bo_v_act">
            <?php if ($scrap_href) { ?><a href="<?php echo $scrap_href;  ?>" target="_blank" class="btn_b01" onclick="win_scrap(this.href); return false;">스크랩</a><?php } ?>
            <?php if ($good_href) { ?>
            <span class="bo_v_act_gng">
                <a href="<?php echo $good_href.'&amp;'.$qstr ?>" id="good_button" class="btn_b01">추천 <strong><?php echo number_format($view['wr_good']) ?></strong></a>
                <b id="bo_v_act_good"></b>
            </span>
            <?php } ?>
            <?php if ($nogood_href) { ?>
            <span class="bo_v_act_gng">
                <a href="<?php echo $nogood_href.'&amp;'.$qstr ?>" id="nogood_button" class="btn_b01">비추천  <strong><?php echo number_format($view['wr_nogood']) ?></strong></a>
                <b id="bo_v_act_nogood"></b>
            </span>
            <?php } ?>
        </div>
        <?php } else {
            if($board['bo_use_good'] || $board['bo_use_nogood']) {
        ?>
        <div id="bo_v_act">
            <?php if($board['bo_use_good']) { ?><span>추천 <strong><?php echo number_format($view['wr_good']) ?></strong></span><?php } ?>
            <?php if($board['bo_use_nogood']) { ?><span>비추천 <strong><?php echo number_format($view['wr_nogood']) ?></strong></span><?php } ?>
        </div>
        <?php
            }
        }
        ?>
        <!-- } 스크랩 추천 비추천 끝 -->
    </section>

    <?php
    include_once(G5_SNS_PATH."/view.sns.skin.php");
    ?>

    <?php
    // 코멘트 입출력
    include_once(G5_BBS_PATH.'/view_comment.php');
     ?>

    <!-- 링크 버튼 시작 { -->
    <div id="bo_v_bot">
        <?php echo $link_buttons ?>
    </div>
    <!-- } 링크 버튼 끝 -->

</article>
<!-- } 게시판 읽기 끝 -->

<script>
<?php if ($board['bo_download_point'] < 0) { ?>
$(function() {
    $("a.view_file_download").click(function() {
        if(!g5_is_member) {
            alert("다운로드 권한이 없습니다.\n회원이시라면 로그인 후 이용해 보십시오.");
            return false;
        }

        var msg = "파일을 다운로드 하시면 포인트가 차감(<?php echo number_format($board['bo_download_point']) ?>점)됩니다.\n\n포인트는 게시물당 한번만 차감되며 다음에 다시 다운로드 하셔도 중복하여 차감하지 않습니다.\n\n그래도 다운로드 하시겠습니까?";

        if(confirm(msg)) {
            var href = $(this).attr("href")+"&js=on";
            $(this).attr("href", href);

            return true;
        } else {
            return false;
        }
    });
});
<?php } ?>

function board_move(href)
{
    window.open(href, "boardmove", "left=50, top=50, width=500, height=550, scrollbars=1");
}
</script>

<script>
$(function() {
    $("a.view_image").click(function() {
        window.open(this.href, "large_image", "location=yes,links=no,toolbar=no,top=10,left=10,width=10,height=10,resizable=yes,scrollbars=no,status=no");
        return false;
    });

    // 추천, 비추천
    $("#good_button, #nogood_button").click(function() {
        var $tx;
        if(this.id == "good_button")
            $tx = $("#bo_v_act_good");
        else
            $tx = $("#bo_v_act_nogood");

        excute_good(this.href, $(this), $tx);
        return false;
    });

    // 이미지 리사이즈
    $("#bo_v_atc").viewimageresize();
});

function excute_good(href, $el, $tx)
{
    $.post(
        href,
        { js: "on" },
        function(data) {
            if(data.error) {
                alert(data.error);
                return false;
            }

            if(data.count) {
                $el.find("strong").text(number_format(String(data.count)));
                if($tx.attr("id").search("nogood") > -1) {
                    $tx.text("이 글을 비추천하셨습니다.");
                    $tx.fadeIn(200).delay(2500).fadeOut(200);
                } else {
                    $tx.text("이 글을 추천하셨습니다.");
                    $tx.fadeIn(200).delay(2500).fadeOut(200);
                }
            }
        }, "json"
    );
}
</script>
<!-- } 게시글 읽기 끝 -->