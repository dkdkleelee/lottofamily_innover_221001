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
</div>
</div>
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
