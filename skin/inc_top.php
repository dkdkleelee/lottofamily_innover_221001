<?php
// EUC --> UTF 설정후 전송
$subject_con = iconv('euc-kr', 'utf-8', $g4['title']);
/////////////////////////////////////////////////////////////////////////////////////////////
// 현제 페이지 주소 추출
$board_url = $trackback_url;
$current_url = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
// 2010 10 19 수정
// 다수 파라미터 지원 불가로 게시판 주소에서 트래백으로 변경 "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] -> $trackback_url
/////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////

  
/////////////////////////////////////////////////////////////////////////////////////////////
// 트위터
/////////////////////////////////////////////////////////////////////////////////////////////
// URL붙이기 // 일부 시스템에서만 사용
$turl= $subject_con."   ".$current_url;
//URL암호화
$turl = urlencode($turl);
/////////////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////////////////////////////////////
// 페이스북
/////////////////////////////////////////////////////////////////////////////////////////////
$face_url= $current_url;
$face_url = urlencode($face_url);
$face_subject = urlencode($subject_con);
/////////////////////////////////////////////////////////////////////////////////////////////

?> 
<div id="header"> 
	<div style="width:100%;background-color:#f4f4f4;height:45px;line-height:39px;border-bottom:1px solid #e6e6e6">
		<div style="width:1200px;margin:0 auto;">
			<div id="sns">
			<ul>
			<li><a href="javascript:bookmark()"><img src="/images/main_sns_bookmark.png"></a></li>
			<li><a href="http://www.facebook.com/sharer.php?u=<?=$face_url?>&t=<?=$face_subject?>" target="_blank"><img src="/images/main_sns_f.png"></a></li>
			<li><a href="http://twitter.com/home/?status=<?=$turl?>" target="_blank"><img src="/images/main_sns_t.png"></a></li>
			</ul>
			</div>
			<div style="float:right;">
			<?=outlogin("basic");?>
			</div>
		</div>
	</div>
	
	<div class="header_in">
		<div class="hlt">
		<img src="/images/main_top_left.gif"><p>로또 1등조합<br><span>156</span>회 배출!</p>
		</div>
		<div class="logo">
		<a href="/"><img src="/images/main_top_logo.gif"></a>
		</div>
		<div class="hrt">
		<img src="/images/main_top_tel.gif"><p>가입문의<br><span>1234-1234</span></p>
		</div>		
	</div>
</div>
<script type="text/javascript" src="/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="/gnb.js"></script>
<div class="gnbLay">
<div class="gnbWrap">
	<div class="gnb">
	<ul>
		<li class="m1">
		<a href="javascript:m1()" class="oneDep">인텔리전트 시스템</a>
		<p class="subDep">								
		<a href="javascript:m1s1()">소개</a>
		</p>
		</li>
	
		<li class="m2">
		<a href="javascript:m2()" class="oneDep">로또스카이 조합기</a>
		<p class="subDep">								
		<a href="javascript:m2s1()">조합기</a>
		</p>
		</li>

		<li class="m3">
		<a href="javascript:m3()" class="oneDep">당첨결과</a>
		<p class="subDep">	
		<a href="javascript:m3s1()">당첨결과</a>
		</p>
		</li>
		
		<li class="m4">
		<a href="javascript:m4()" class="oneDep">로또스카이 분석실</a>
		<p class="subDep">	
		<a href="javascript:m4s1()">로또스카이 분석실</a>
		</p>
		</li>

		<li class="m5">
		<a href="javascript:m5()" class="oneDep">마이페이지</a>
		<p class="subDep">	
		<a href="javascript:m5s1()">마이페이지</a>
		</p>
		</li>
		<li class="m6">
		<a href="javascript:m6()" class="oneDep">고객센터</a>
		<p class="subDep">	
		<a href="javascript:m6s1()">공지사항</a>
		<a href="javascript:m6s6()">질문과 답변</a>
		</p>
		</li>
		</ul>
	</div>
</div>
<div class="subDbg" style="display:"></div> 
</div>
<div class="hnotice">
	<p><img src="/images/main_top_notice.png">2018년 08월 14일 : 현재 총 584만 4,814개의 조합이 1,260억 6,511만 358원에 당첨되었습니다.</p>
</div>