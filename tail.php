<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(defined('G5_THEME_PATH')) {
    require_once(G5_THEME_PATH.'/tail.php');
    return;
}

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/tail.php');
    return;
}
?>
<!--/div>
</div-->


<? include "inc_footer.php" ?>
<?php
if(G5_USE_MOBILE && !G5_IS_MOBILE) {
    $seq = 0;
    $p = parse_url(G5_URL);
    $href = $p['scheme'].'://'.$p['host'].$_SERVER['PHP_SELF'];
    if($_SERVER['QUERY_STRING']) {
        $sep = '?';
        foreach($_GET as $key=>$val) {
            if($key == 'device')
                continue;
 
            $href .= $sep.$key.'='.strip_tags($val);
            $sep = '&amp;';
            $seq++;
        }
    }
    if($seq)
        $href .= '&amp;device=mobile';
    else
        $href .= '?device=mobile';
?>
<?php if(is_mobile()){ //pc에서는 모바일 보기 버튼 표시안함. ?> 
<a href="<?php echo $href; ?>" id="device_change">모바일 버전으로 보기</a> 
<?php } ?>  
 
<?php
}
 
if ($config['cf_analytics']) {
    echo $config['cf_analytics'];
}
?>

<!-- <script type="text/javascript" src="/service/public/js/plugins/noty-2.4.1/js/noty/packaged/jquery.noty.packaged.js"></script>  -->
<link  rel="stylesheet" href="/service/public/js/plugins/noty-v3/lib/noty.css">
<link  rel="stylesheet" href="/service/public/js/plugins/noty-v3/lib/themes/metroui.css">
<script type="text/javascript" src="/service/public/js/plugins/noty-v3/lib/noty.js"></script> 
<script type="text/javascript" src="/service/public/js/plugins/jquery-play-sound/jquery.playSound.js"></script> 
<script>

$(document).ready(function() {

<?php
// 쪽지를 받았나?
if ($member['mb_memo_call']) {
    $mb = get_member($member['mb_memo_call'], "mb_nick");
    sql_query(" update {$g5['member_table']} set mb_memo_call = '' where mb_id = '{$member['mb_id']}' ");

?>

	showNoty('<?=$member['mb_memo_call']?>님으로부터 메세지가 도착했습니다.');

<? } ?>

<? if($_SESSION['ss_mb_id'] && $member['mb_level'] < 3) { ?>
	/*
	var delayTime = 2000;
	(function poll(){
		setTimeout(function(){
			$.ajax({
					url: "/service/public/lotto_common_process.php",
					type: "post",
					data: {
							proc: 'getUnsentNoty',
							mb_id : '<?=$_SESSION['ss_mb_id']?>'
					},
					success: function(data){ 
						$(data).each(function(idx, val) {

							showNoty(val.title, val.id);
						});
					
						delayTime += (delayTime < 30000) ? 3000 : 0;
						poll();
					},
					dataType: "json"
			});
		}, delayTime);
	})();
*/

<? } ?>

});
</script>

<?php
include_once(G5_PATH."/tail.sub.php");
?>
