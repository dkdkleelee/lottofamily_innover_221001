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
use Acesoft\Common\Utils;
use Acesoft\LottoApp\LottoServiceConfig;
use Acesoft\LottoApp\Lotto;
use Acesoft\LottoApp\LottoService;
use Acesoft\LottoApp\TermService;

$lottoService = new LottoService();

$accumulated_result = $lottoService->getAccumulatedWinRecords();
for($i=1; $i<=5; $i++) {
	$acc_result['cnt'] += $accumulated_result[$i]['cnt'];
	$acc_result['prize'] += $accumulated_result[$i]['prize'];
}

?> 

<div id="header"> 

	<div style="position:relative;width:1200px;margin:0 auto;height:80px;line-height:80px">
<!--		<div id="sns">
		<ul>
		<li>
			<div class="hlt">
			<img src="/images/main_top_tel.gif"><p>가입문의<br><span>1688-7551</span></p>
			</div>		
		</li>
		</ul>
		</div>--!>
		<div class="logo"><a href="/"><img src="/images/rnw/로또패밀리_로고.png" width="260"></a></div>
		<div class="login">
		<?=outlogin("basic");?>
		</div>		
	</div>
</div>
<script type="text/javascript" src="/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="/gnb.js"></script>
<div class="gnbLay">
<div style="position:relative;width:1200px;margin:0 auto;height:80px;line-height:80px;left:120px">
<div class="gnbWrap">
	<div class="gnb">
	<ul>
		<li class="m1">
		<a href="javascript:m1()" class="oneDep">패밀리 시스템</a>
		<p class="subDep">								
		<a href="javascript:m1s1()">스팟필터시스템 소개</a>
		<a href="javascript:m1s2()">챌린져</a>
		<a href="javascript:m1s3()">다이아</a>
		<a href="javascript:m1s4()">마스터</a>
		</p>
		</li>
	
		<li class="m2">
		<a href="javascript:m2()" class="oneDep">패밀리 셀프조합</a>
		<p class="subDep">								
		<a href="javascript:m2s1()">고정수필터셀프조합</a>
		<a href="javascript:m2s2()">제외수필터셀프조합</a>
		</p>
		</li>

		<li class="m3">
		<a href="javascript:m4()" class="oneDep">스팟 필터</a>
		<p class="subDep">	
		
		<a href="javascript:m4s2()">미출현번호분석</a>
		<a href="javascript:m4s3()">숫자합분석</a>
		<a href="javascript:m4s4()">숫자패턴분석</a>
		<a href="javascript:m4s5()">A.C값분석</a>
		<a href="javascript:m4s1()">E/O분석</a>
		</p>
		</li>

		

		<li class="m5">
		<a href="javascript:m5()" class="oneDep">마이페이지</a>
		<p class="subDep">	
		<a href="javascript:m5s1()">마이페이지</a>
		<a href="javascript:m5s2()">추출번호관리</a>
		<a href="javascript:m5s3()">내당첨내역조회</a>
		<a href="javascript:m5s4()">정회원신청</a>
		</p>
		</li>
		<li class="m6">
		<a href="javascript:m6()" class="oneDep">고객센터</a>
		<p class="subDep">	
		<a href="javascript:m6s1()">공지사항</a>
		
		</p>
		</li>
		</ul>
	</div> </div>
</div>

<div class="subDbg" style="display:">
<!-- <canvas id="top_cv" style="position:absolute;width:100%;height:300px;z-inde:-100;"></canvas> -->
</div> 
</div>

<script>
/*
// RequestAnimFrame: a browser API for getting smooth animations
window.requestAnimFrame = (function(){
  return  window.requestAnimationFrame       || 
		  window.webkitRequestAnimationFrame || 
		  window.mozRequestAnimationFrame    || 
		  window.oRequestAnimationFrame      || 
		  window.msRequestAnimationFrame     ||  
		  function( callback ){
			window.setTimeout(callback, 1000 / 60);
		  };
})();

// Initializing the canvas
// I am using native JS here, but you can use jQuery, 
// Mootools or anything you want
var canvas = document.getElementById("top_cv");

// Initialize the context of the canvas
var ctx = canvas.getContext("2d");

// Set the canvas width and height to occupy full window
var W = window.innerWidth, H = window.innerHeight;
canvas.width = W;
canvas.height = H;

// Some variables for later use
var particleCount = 120,
	particles = [],
	minDist = 150,
	dist;

// Function to paint the canvas black
function paintCanvas() {
	// Set the fill color to black
	ctx.fillStyle = "rgba(255,255,255,1)";
	
	// This will create a rectangle of white color from the 
	// top left (0,0) to the bottom right corner (W,H)
	
	ctx.fillRect(0,0,W,H);
}

// Now the idea is to create some particles that will attract
// each other when they come close. We will set a minimum
// distance for it and also draw a line when they come
// close to each other.

// The attraction can be done by increasing their velocity as 
// they reach closer to each other

// Let's make a function that will act as a class for
// our particles.

function Particle() {
	// Position them randomly on the canvas
	// Math.random() generates a random value between 0
	// and 1 so we will need to multiply that with the
	// canvas width and height.
	this.x = Math.random() * W;
	this.y = Math.random() * H;
	
	
	// We would also need some velocity for the particles
	// so that they can move freely across the space
	this.vx = -1 + Math.random() * 2;
	this.vy = -1 + Math.random() * 2;

	// Now the radius of the particles. I want all of 
	// them to be equal in size so no Math.random() here..
	this.radius = 1;
	
	// This is the method that will draw the Particle on the
	// canvas. It is using the basic fillStyle, then we start
	// the path and after we use the `arc` function to 
	// draw our circle. The `arc` function accepts four
	// parameters in which first two depicts the position
	// of the center point of our arc as x and y coordinates.
	// The third value is for radius, then start angle, 
	// end angle and finally a boolean value which decides
	// whether the arc is to be drawn in counter clockwise or 
	// in a clockwise direction. False for clockwise.
	this.draw = function() {
		ctx.fillStyle = "gray";
		ctx.beginPath();
		ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2, false);
		
		// Fill the color to the arc that we just created
		ctx.fill();
	}
}

// Time to push the particles into an array
for(var i = 0; i < particleCount; i++) {
	particles.push(new Particle());
}

// Function to draw everything on the canvas that we'll use when 
// animating the whole scene.
function draw() {
	
	// Call the paintCanvas function here so that our canvas
	// will get re-painted in each next frame
	paintCanvas();
	
	// Call the function that will draw the balls using a loop
	for (var i = 0; i < particles.length; i++) {
		p = particles[i];
		p.draw();
	}
	
	//Finally call the update function
	update();
}

// Give every particle some life
function update() {
	
	// In this function, we are first going to update every
	// particle's position according to their velocities
	for (var i = 0; i < particles.length; i++) {
		p = particles[i];
		
		// Change the velocities
		p.x += p.vx;
		p.y += p.vy
			
		// We don't want to make the particles leave the
		// area, so just change their position when they
		// touch the walls of the window
		if(p.x + p.radius > W) 
			p.x = p.radius;
		
		else if(p.x - p.radius < 0) {
			p.x = W - p.radius;
		}
		
		if(p.y + p.radius > H) 
			p.y = p.radius;
		
		else if(p.y - p.radius < 0) {
			p.y = H - p.radius;
		}
		
		// Now we need to make them attract each other
		// so first, we'll check the distance between
		// them and compare it to the minDist we have
		// already set
		
		// We will need another loop so that each
		// particle can be compared to every other particle
		// except itself
		for(var j = i + 1; j < particles.length; j++) {
			p2 = particles[j];
			distance(p, p2);
		}
	
	}
}

// Distance calculator between two particles
function distance(p1, p2) {
	var dist,
		dx = p1.x - p2.x,
		dy = p1.y - p2.y;
	
	dist = Math.sqrt(dx*dx + dy*dy);
			
	// Draw the line when distance is smaller
	// then the minimum distance
	if(dist <= minDist) {
		
		// Draw the line
		ctx.beginPath();
		ctx.strokeStyle = "rgba(220,220,220,"+ (1.2-dist/minDist) +")";
		ctx.moveTo(p1.x, p1.y);
		ctx.lineTo(p2.x, p2.y);
		ctx.stroke();
		ctx.closePath();
		
		// Some acceleration for the partcles 
		// depending upon their distance
		var ax = dx/500000,
			ay = dy/500000;
		
		// Apply the acceleration on the particles
		p1.vx -= ax;
		p1.vy -= ay;
		
		p2.vx += ax;
		p2.vy += ay;
	}
}

// Start the main animation loop using requestAnimFrame
function animloop() {
	draw();
	requestAnimFrame(animloop);
}

animloop();
*/
</script>
