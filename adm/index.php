<?php
include_once('./_common.php');

use \Acesoft\Common\Utils;
use \Acesoft\Common\Message;
use \Acesoft\LottoApp\LottoService;

$g5['title'] = '관리자메인';
include_once ('./admin.head.php');

$new_member_rows = 5;
$new_point_rows = 5;
$new_write_rows = 5;

$sql_common = " from {$g5['member_table']} ";

$sql_search = " where (1) ";

if ($is_admin != 'super')
    $sql_search .= " and mb_level <= '{$member['mb_level']}' ";

if (!$sst) {
    $sst = "mb_datetime";
    $sod = "desc";
}

$sql_order = " order by {$sst} {$sod} ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

// 탈퇴회원수
$sql = " select count(*) as cnt {$sql_common} {$sql_search} and mb_leave_date <> '' {$sql_order} ";
$row = sql_fetch($sql);
$leave_count = $row['cnt'];

// 차단회원수
$sql = " select count(*) as cnt {$sql_common} {$sql_search} and mb_intercept_date <> '' {$sql_order} ";
$row = sql_fetch($sql);
$intercept_count = $row['cnt'];

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$new_member_rows} ";
$result = sql_query($sql);

$colspan = 12;

// 발급현황
$_GET['s_date'] = $_GET['s_date'] ? $_GET['s_date'] : date('Y-m-d');
$s_date = explode("-", $_GET['s_date']);

$_GET['s_term'] = $_GET['s_term'] ? $_GET['s_term'] : 'd';


$data_st['label'] = array();
$data_st['var'] = array();

$lottoService = new LottoService();
$data_list = $lottoService->getIssuedStatistics();

$d_format = $s_date[0]."-".$s_date[1]."-".$s_date[2]." ";
$d_format_pre = date("Y-m-d ", strtotime($_GET['s_date']." -1 day"));
$cur_last =  24;
$pre_last =  24;
$cur_legend = $_GET['s_date']."일";
$pre_legend = date("Y-m-d", strtotime($_GET['s_date']." -1 day"))."일";
$unit = "시";


foreach($data_list['cur'] as $row) {
	$data_st_tmp['label'][$row['g_date']] = "                 ".$row['rdate'];
	$data_st_tmp['var'][$row['g_date']] = $row['cnt'];
}


for($i=1; $i<=$cur_last; $i++) {
	$data_st_list['label'][] = sprintf('%02d',$i).$unit; //$d_format.(sprintf('%02d',$i)).$unit;
	$data_st_list['var'][] = $data_st_tmp['var'][$d_format.(sprintf('%02d',$i))] ? $data_st_tmp['var'][$d_format.(sprintf('%02d',$i))] : 0;
}

$data_st_pre['label'] = array();
$data_st_pre['var'] = array();
foreach($data_list['pre'] as $row) {
	$tmp = explode("-", $row['rdate']);
	$data_st_pre_tmp['label'][$row['g_date']] = "                 ".$row['rdate'];
	$data_st_pre_tmp['var'][$row['g_date']] = $row['cnt'];
}

for($i=1; $i<=$pre_last; $i++) {
	$data_st_pre_list['label'][] = $d_format_pre.(sprintf('%02d',$i));
	$data_st_pre_list['var'][] = $data_st_pre_tmp['var'][$d_format_pre.(sprintf('%02d',$i))] ? $data_st_pre_tmp['var'][$d_format_pre.(sprintf('%02d',$i))] : 0;
}



// 메세지
$message = new Message();
$message_rows = $message->getRecentMessages('', 10);




?>
<!-- chartjs -->
<link rel="stylesheet" href="../service/css/admin.custom.style.css" type="text/css">
<script src="../service/public/js/plugins/chartjs/Chart.min.js"></script>
<section>
	<div class="btitle">
		<div style="padding-left:10px"><i class="fa fa-folder-o"></i> 시간대별 번호발급현황</div>
	</div>
	
	<br />
	<div style="width:98%;margin-left:20px">
		<canvas id="canvas" style="width:100%;height:200px;"></canvas>
	</div>
	<br /><br />

    <h2>신규가입회원 <?php echo $new_member_rows ?>건 목록</h2>
    <div class="local_desc02 local_desc">
        총회원수 <?php echo number_format($total_count) ?>명 중 차단 <?php echo number_format($intercept_count) ?>명, 탈퇴 : <?php echo number_format($leave_count) ?>명
    </div>

    <div class="tbl_head01 tbl_wrap">
        <table>
        <caption>신규가입회원</caption>
        <thead>
        <tr>
            <th scope="col">회원아이디</th>
            <th scope="col">이름</th>
            <th scope="col">닉네임</th>
            <th scope="col">권한</th>
            <th scope="col">포인트</th>
            <th scope="col">수신</th>
            <th scope="col">공개</th>
            <th scope="col">인증</th>
            <th scope="col">차단</th>
            <th scope="col">그룹</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for ($i=0; $row=sql_fetch_array($result); $i++)
        {
            // 접근가능한 그룹수
            $sql2 = " select count(*) as cnt from {$g5['group_member_table']} where mb_id = '{$row['mb_id']}' ";
            $row2 = sql_fetch($sql2);
            $group = "";
            if ($row2['cnt'])
                $group = '<a href="./boardgroupmember_form.php?mb_id='.$row['mb_id'].'">'.$row2['cnt'].'</a>';

            if ($is_admin == 'group')
            {
                $s_mod = '';
                $s_del = '';
            }
            else
            {
                $s_mod = '<a href="./member_form.php?$qstr&amp;w=u&amp;mb_id='.$row['mb_id'].'">수정</a>';
                $s_del = '<a href="./member_delete.php?'.$qstr.'&amp;w=d&amp;mb_id='.$row['mb_id'].'&amp;url='.$_SERVER['SCRIPT_NAME'].'" onclick="return delete_confirm(this);">삭제</a>';
            }
            $s_grp = '<a href="./boardgroupmember_form.php?mb_id='.$row['mb_id'].'">그룹</a>';

            $leave_date = $row['mb_leave_date'] ? $row['mb_leave_date'] : date("Ymd", G5_SERVER_TIME);
            $intercept_date = $row['mb_intercept_date'] ? $row['mb_intercept_date'] : date("Ymd", G5_SERVER_TIME);

            $mb_nick = get_sideview($row['mb_id'], get_text($row['mb_nick']), $row['mb_email'], $row['mb_homepage']);

            $mb_id = $row['mb_id'];
            if ($row['mb_leave_date'])
                $mb_id = $mb_id;
            else if ($row['mb_intercept_date'])
                $mb_id = $mb_id;

        ?>
        <tr>
            <td class="td_mbid"><?php echo $mb_id ?></td>
            <td class="td_mbname"><?php echo get_text($row['mb_name']); ?></td>
            <td class="td_mbname sv_use"><div><?php echo $mb_nick ?></div></td>
            <td class="td_num"><?php echo $row['mb_level'] ?></td>
            <td><a href="./point_list.php?sfl=mb_id&amp;stx=<?php echo $row['mb_id'] ?>"><?php echo number_format($row['mb_point']) ?></a></td>
            <td class="td_boolean"><?php echo $row['mb_mailling']?'예':'아니오'; ?></td>
            <td class="td_boolean"><?php echo $row['mb_open']?'예':'아니오'; ?></td>
            <td class="td_boolean"><?php echo preg_match('/[1-9]/', $row['mb_email_certify'])?'예':'아니오'; ?></td>
            <td class="td_boolean"><?php echo $row['mb_intercept_date']?'예':'아니오'; ?></td>
            <td class="td_category"><?php echo $group ?></td>
        </tr>
        <?php
            }
        if ($i == 0)
            echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
        ?>
        </tbody>
        </table>
    </div>

    <div class="btn_list03 btn_list">
        <a href="./member_list.php">회원 전체보기</a>
    </div>

</section>

<section>
	<h2>메세지전송현황</h2>

	<div class="tbl_head01 tbl_wrap">
	<table>
		<thead>
		<tr>
			<th width="120px">등록일시</th>
			<th width="120px">전송일시</th>
			<th width="120px">유형</th>
			<th width="220px">대상</th>
			<th>제목/내용</th>
		</tr>
		</thead>
		
		<? 
			if(count($message_rows) > 0) {
				foreach($message_rows as $row) {
				
		?>
			<tr>
				<td class="td_datetime" rowspan="2"><?=$row['created_at']?></td>
				<td class="td_datetime" rowspan="2"><?=$row['sent_at'] ? $row['sent_at'] : '전송대기'?></td>
				<td class="td_datetime" rowspan="2" style="width:50px"><?=$row['type']?></td>
				<td class="td_mbname" rowspan="2" style="text-align:center;padding-left:5px">
					<?=$row['mb_id']?><br /><?=$row['mb_com_name'] ? $row['mb_com_name'] : $row['mb_name']?>
				</td>
				<td style="text-align:left;padding-left:5px;font-weight:bold">
					<?=Utils::textCut($row['title'], 80)?>
				</td>
				
			</tr>
			<tr>
				
				<td style="text-align:left;padding-left:5px">
					<?=Utils::textCut($row['message'], 160)?>
				</td>
			</tr>
		<?
				}
			} else {
		?>
			<tr>
				<td colspan="6"><font style="font-size:11px">메세지가 존재하지 않습니다. </font></td>
			</tr>
		<?	} ?>
		</tbody>
	</table>
	</div>
</section>
<br /><br />

</section>

<?php
$sql_common = " from {$g5['board_new_table']} a, {$g5['board_table']} b, {$g5['group_table']} c where a.bo_table = b.bo_table and b.gr_id = c.gr_id ";

if ($gr_id)
    $sql_common .= " and b.gr_id = '$gr_id' ";
if ($view) {
    if ($view == 'w')
        $sql_common .= " and a.wr_id = a.wr_parent ";
    else if ($view == 'c')
        $sql_common .= " and a.wr_id <> a.wr_parent ";
}
$sql_order = " order by a.bn_id desc ";

$sql = " select count(*) as cnt {$sql_common} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$colspan = 5;
?>

<section>
    <h2>최근게시물</h2>

    <div class="tbl_head01 tbl_wrap">
        <table>
        <caption>최근게시물</caption>
        <thead>
        <tr>
            <th scope="col">그룹</th>
            <th scope="col">게시판</th>
            <th scope="col">제목</th>
            <th scope="col">이름</th>
            <th scope="col">일시</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $sql = " select a.*, b.bo_subject, c.gr_subject, c.gr_id {$sql_common} {$sql_order} limit {$new_write_rows} ";
        $result = sql_query($sql);
        for ($i=0; $row=sql_fetch_array($result); $i++)
        {
            $tmp_write_table = $g5['write_prefix'] . $row['bo_table'];

            if ($row['wr_id'] == $row['wr_parent']) // 원글
            {
                $comment = "";
                $comment_link = "";
                $row2 = sql_fetch(" select * from $tmp_write_table where wr_id = '{$row['wr_id']}' ");

                $name = get_sideview($row2['mb_id'], get_text(cut_str($row2['wr_name'], $config['cf_cut_name'])), $row2['wr_email'], $row2['wr_homepage']);
                // 당일인 경우 시간으로 표시함
                $datetime = substr($row2['wr_datetime'],0,10);
                $datetime2 = $row2['wr_datetime'];
                if ($datetime == G5_TIME_YMD)
                    $datetime2 = substr($datetime2,11,5);
                else
                    $datetime2 = substr($datetime2,5,5);

            }
            else // 코멘트
            {
                $comment = '댓글. ';
                $comment_link = '#c_'.$row['wr_id'];
                $row2 = sql_fetch(" select * from {$tmp_write_table} where wr_id = '{$row['wr_parent']}' ");
                $row3 = sql_fetch(" select mb_id, wr_name, wr_email, wr_homepage, wr_datetime from {$tmp_write_table} where wr_id = '{$row['wr_id']}' ");

                $name = get_sideview($row3['mb_id'], get_text(cut_str($row3['wr_name'], $config['cf_cut_name'])), $row3['wr_email'], $row3['wr_homepage']);
                // 당일인 경우 시간으로 표시함
                $datetime = substr($row3['wr_datetime'],0,10);
                $datetime2 = $row3['wr_datetime'];
                if ($datetime == G5_TIME_YMD)
                    $datetime2 = substr($datetime2,11,5);
                else
                    $datetime2 = substr($datetime2,5,5);
            }
        ?>

        <tr>
            <td class="td_category"><a href="<?php echo G5_BBS_URL ?>/new.php?gr_id=<?php echo $row['gr_id'] ?>"><?php echo cut_str($row['gr_subject'],10) ?></a></td>
            <td class="td_category"><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $row['bo_table'] ?>"><?php echo cut_str($row['bo_subject'],20) ?></a></td>
            <td><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $row['bo_table'] ?>&amp;wr_id=<?php echo $row2['wr_id'] ?><?php echo $comment_link ?>"><?php echo $comment ?><?php echo conv_subject($row2['wr_subject'], 100) ?></a></td>
            <td class="td_mbname"><div><?php echo $name ?></div></td>
            <td class="td_datetime"><?php echo $datetime ?></td>
        </tr>

        <?php
        }
        if ($i == 0)
            echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
        ?>
        </tbody>
        </table>
    </div>

    <div class="btn_list03 btn_list">
        <a href="<?php echo G5_BBS_URL ?>/new.php">최근게시물 더보기</a>
    </div>
</section>

<?php
$sql_common = " from {$g5['point_table']} ";
$sql_search = " where (1) ";
$sql_order = " order by po_id desc ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$new_point_rows} ";
$result = sql_query($sql);

$colspan = 7;
?>

<section>
    <h2>최근 포인트 발생내역</h2>
    <div class="local_desc02 local_desc">
        전체 <?php echo number_format($total_count) ?> 건 중 <?php echo $new_point_rows ?>건 목록
    </div>

    <div class="tbl_head01 tbl_wrap">
        <table>
        <caption>최근 포인트 발생내역</caption>
        <thead>
        <tr>
            <th scope="col">회원아이디</th>
            <th scope="col">이름</th>
            <th scope="col">닉네임</th>
            <th scope="col">일시</th>
            <th scope="col">포인트 내용</th>
            <th scope="col">포인트</th>
            <th scope="col">포인트합</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $row2['mb_id'] = '';
        for ($i=0; $row=sql_fetch_array($result); $i++)
        {
            if ($row2['mb_id'] != $row['mb_id'])
            {
                $sql2 = " select mb_id, mb_name, mb_nick, mb_email, mb_homepage, mb_point from {$g5['member_table']} where mb_id = '{$row['mb_id']}' ";
                $row2 = sql_fetch($sql2);
            }

            $mb_nick = get_sideview($row['mb_id'], $row2['mb_nick'], $row2['mb_email'], $row2['mb_homepage']);

            $link1 = $link2 = "";
            if (!preg_match("/^\@/", $row['po_rel_table']) && $row['po_rel_table'])
            {
                $link1 = '<a href="'.G5_BBS_URL.'/board.php?bo_table='.$row['po_rel_table'].'&amp;wr_id='.$row['po_rel_id'].'" target="_blank">';
                $link2 = '</a>';
            }
        ?>

        <tr>
            <td class="td_mbid"><a href="./point_list.php?sfl=mb_id&amp;stx=<?php echo $row['mb_id'] ?>"><?php echo $row['mb_id'] ?></a></td>
            <td class="td_mbname"><?php echo get_text($row2['mb_name']); ?></td>
            <td class="td_name sv_use"><div><?php echo $mb_nick ?></div></td>
            <td class="td_datetime"><?php echo $row['po_datetime'] ?></td>
            <td><?php echo $link1.$row['po_content'].$link2 ?></td>
            <td class="td_numbig"><?php echo number_format($row['po_point']) ?></td>
            <td class="td_numbig"><?php echo number_format($row['po_mb_point']) ?></td>
        </tr>

        <?php
        }

        if ($i == 0)
            echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
        ?>
        </tbody>
        </table>
    </div>

    <div class="btn_list03 btn_list">
        <a href="./point_list.php">포인트내역 전체보기</a>
    </div>
</section>
<script>
var chartOptions = {
		//Boolean - If we should show the scale at all
		showScale: true,
		//Boolean - Whether grid lines are shown across the chart
		scaleShowGridLines: true,
		//String - Colour of the grid lines
		scaleGridLineColor: "rgba(0,0,0,.05)",
		//Number - Width of the grid lines
		scaleGridLineWidth: 1,
		//Boolean - Whether to show horizontal lines (except X axis)
		scaleShowHorizontalLines: true,
		//Boolean - Whether to show vertical lines (except Y axis)
		scaleShowVerticalLines: true,
		//Boolean - Whether the line is curved between points
		bezierCurve: true,
		//Number - Tension of the bezier curve between points
		bezierCurveTension: 0.02,
		//Boolean - Whether to show a dot for each point
		pointDot: true,
		//Number - Radius of each point dot in pixels
		pointDotRadius: 2,
		//Number - Pixel width of point dot stroke
		pointDotStrokeWidth: 1,
		//Number - amount extra to add to the radius to cater for hit detection outside the drawn point
		pointHitDetectionRadius: 20,
		//Boolean - Whether to show a stroke for datasets
		datasetStroke: true,
		//Number - Pixel width of dataset stroke
		datasetStrokeWidth: 2,
		//Boolean - Whether to fill the dataset with a color
		datasetFill: true,
		//tooltipTemplate : "<%if (label){%><%=label%>: <%}%><%= value %>kb",
		//String - A legend template
		legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
		//Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
		maintainAspectRatio: false,
		//Boolean - whether to make the chart responsive to window resizing
		responsive: false,
		scaleOverride: false,
		scaleSteps: null,
		// Number - The value jump in the hard coded scale
		scaleStepWidth: null,
		// Number - The scale starting value
		scaleStartValue: null,
		maintainAspectRatio: true,
		responsive : true,
	  };

	var ChartData = {
		labels: <?=json_encode($data_st_list['label'])?>,
		datasets: [
		  {
			label: "<?=$pre_legend?>",
			fillColor: "rgba(210, 214, 222, 1)",
			strokeColor: "rgba(210, 214, 222, 1)",
			pointColor: "rgba(210, 214, 222, 1)",
			pointStrokeColor: "#c1c7d1",
			pointHighlightFill: "#fff",
			pointHighlightStroke: "rgba(220,220,220,1)",

			data: <?=json_encode($data_st_pre_list['var'])?>
		  },
		  {
			label: "<?=$cur_legend?>",
			fillColor: "rgba(60,141,188,0.6)",
			strokeColor: "rgba(60,141,188,0.8)",
			pointColor: "#3b8bba",
			pointStrokeColor: "rgba(60,141,188,1)",
			pointHighlightFill: "#fff",
			pointHighlightStroke: "rgba(60,141,188,1)",
			data: <?=json_encode($data_st_list['var'])?>
		  }
		]
	  };


	
	window.onload = function(){
		var ctx = document.getElementById("canvas").getContext("2d");
		window.myChart = new Chart(ctx).Line(ChartData, chartOptions);

		//document.getElementById('js-legend').innerHTML = myBar.generateLegend();

		var helpers = Chart.helpers;
		var legendHolder = document.createElement('div');
		legendHolder.innerHTML = myChart.generateLegend(); 
		helpers.each(legendHolder.firstChild.childNodes, function(legendNode, index){
			/*
			helpers.addEvent(legendNode, 'mouseover', function(){ 
				var activeSegment = orderMyChart.segments[index];
				activeSegment.save();
				activeSegment.fillColor = activeSegment.highlightColor;
				orderMyChart.showTooltip([activeSegment]);
				activeSegment.restore();
				
				
			});
			*/
		});

		helpers.addEvent(legendHolder.firstChild, 'mouseout', function(){
			myChart.draw();
		});
		myChart.chart.canvas.parentNode.appendChild(legendHolder.firstChild);
	}


</script>
<?php
include_once ('./admin.tail.php');
?>
