<?php
if (!defined('_GNUBOARD_')) exit;

$print_version = defined('G5_YOUNGCART_VER') ? 'YoungCart Version '.G5_YOUNGCART_VER : 'Version '.G5_GNUBOARD_VER;
?>

        <noscript>
            <p>
                귀하께서 사용하시는 브라우저는 현재 <strong>자바스크립트를 사용하지 않음</strong>으로 설정되어 있습니다.<br>
                <strong>자바스크립트를 사용하지 않음</strong>으로 설정하신 경우는 수정이나 삭제시 별도의 경고창이 나오지 않으므로 이점 주의하시기 바랍니다.
            </p>
        </noscript>

    </div>
</div>

<footer id="ft">
    <p>
        <!--iframe src="http://wepas.com/agency/footer_boardadm.htm" width="100%" height="18" frameborder="0"></iframe><br-->
        <a href="#">상단으로</a>
    </p>
</footer>

<!-- <p>실행시간 : <?php echo get_microtime() - $begin_time; ?> -->


<!--script>
var delayTime = 2000;
        (function poll(){
                setTimeout(function(){
                        $.ajax({
                                        url: "/service/public/lotto_common_process.php",
                                        type: "post",
                                        data: {
                                                        proc: 'checkUnCheckedAlert',
                                                        mb_id : '<?=$_SESSION['ss_mb_id']?>'
                                        },
                                        success: function(data){
                                                $(data).each(function(idx, val) {
                                                        //console.log(val);
                                                        window.open('/service/public/admin/lotto_member_management.php?mo_no='+val['mo_no']+'&'+'mb_id='+val['mb_id'],'','width=1000, height=800,scrollbars=yes');
                                                });

                                                delayTime += (delayTime < 10000) ? 3000 : 0;
                                                poll();
                                        },
                                        error: function(request,status,error){
                                                //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                                        },
                                        dataType: "json"
                        });
                }, delayTime);
        })();
</script-->


<script src="<?php echo G5_ADMIN_URL ?>/admin.js?ver=<?php echo G5_JS_VER; ?>"></script>
<script>
$(function(){
    var hide_menu = false;
    var mouse_event = false;
    var oldX = oldY = 0;

    $(document).mousemove(function(e) {
        if(oldX == 0) {
            oldX = e.pageX;
            oldY = e.pageY;
        }

        if(oldX != e.pageX || oldY != e.pageY) {
            mouse_event = true;
        }
    });

    // 주메뉴
    var $gnb = $(".gnb_1dli > a");
    $gnb.mouseover(function() {
        if(mouse_event) {
            $(".gnb_1dli").removeClass("gnb_1dli_over gnb_1dli_over2 gnb_1dli_on");
            $(this).parent().addClass("gnb_1dli_over gnb_1dli_on");
            menu_rearrange($(this).parent());
            hide_menu = false;
        }
    });

    $gnb.mouseout(function() {
        hide_menu = true;
    });

    $(".gnb_2dli").mouseover(function() {
        hide_menu = false;
    });

    $(".gnb_2dli").mouseout(function() {
        hide_menu = true;
    });

    $gnb.focusin(function() {
        $(".gnb_1dli").removeClass("gnb_1dli_over gnb_1dli_over2 gnb_1dli_on");
        $(this).parent().addClass("gnb_1dli_over gnb_1dli_on");
        menu_rearrange($(this).parent());
        hide_menu = false;
    });

    $gnb.focusout(function() {
        hide_menu = true;
    });

    $(".gnb_2da").focusin(function() {
        $(".gnb_1dli").removeClass("gnb_1dli_over gnb_1dli_over2 gnb_1dli_on");
        var $gnb_li = $(this).closest(".gnb_1dli").addClass("gnb_1dli_over gnb_1dli_on");
        menu_rearrange($(this).closest(".gnb_1dli"));
        hide_menu = false;
    });

    $(".gnb_2da").focusout(function() {
        hide_menu = true;
    });

    $('#gnb_1dul>li').bind('mouseleave',function(){
        submenu_hide();
    });

    $(document).bind('click focusin',function(){
        if(hide_menu) {
            submenu_hide();
        }
    });

    // 폰트 리사이즈 쿠키있으면 실행
    var font_resize_act = get_cookie("ck_font_resize_act");
    if(font_resize_act != "") {
        font_resize("container", font_resize_act);
    }
});

function submenu_hide() {
    $(".gnb_1dli").removeClass("gnb_1dli_over gnb_1dli_over2 gnb_1dli_on");
}

function menu_rearrange(el)
{
    var width = $("#gnb_1dul").width();
    var left = w1 = w2 = 0;
    var idx = $(".gnb_1dli").index(el);

    for(i=0; i<=idx; i++) {
        w1 = $(".gnb_1dli:eq("+i+")").outerWidth();
        w2 = $(".gnb_2dli > a:eq("+i+")").outerWidth(true);

        if((left + w2) > width) {
            el.removeClass("gnb_1dli_over").addClass("gnb_1dli_over2");
        }

        left += w1;
    }
}

</script>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>
