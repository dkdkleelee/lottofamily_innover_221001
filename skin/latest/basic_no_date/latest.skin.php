<?
if (!defined("_GNUBOARD_")) exit; 
?>
<style>
.latest {position:relative;left:20px}
.latest li {height:56px;width:325px;float:right;right:40px;position:relative;border-bottom:1px solid #dddddd;background:url('./skin/latest/basic/img/bt.gif') 0 25px no-repeat;text-indent:15px;line-height:56px}
.latest li a{font-size:14px;color:#909090}
.latest li p{font-size:12px; color:#909090;float:right;}
</style>
<div class="latest">
<ul>
<? for ($i=0; $i<count($list); $i++) { ?>
<li>
			<?
            echo "<a href='{$list[$i]['href']}'>";
            if ($list[$i]['is_notice'])
                echo "<strong>{$list[$i]['subject']}</strong>";
            else
                echo "{$list[$i]['subject']}";
            echo "</a>";
            ?>

</li>
<? } ?>
<? if (count($list) == 0) { ?><? } ?>
</ul>
</div>