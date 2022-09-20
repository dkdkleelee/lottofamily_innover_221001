<?php

return [

	'site_conf' => [
		'name' => '로또패밀리'
	],

	'connections' => [
		'db' => [
			'host' => 'localhost',
			'database' => 'lottofamily',
			'username' => 'lottofamily',
			'password' => '1234qwer@@',
			'charset' => 'utf8',
			'prefix' => ''
		],
	],

	'table' => [
		'TermServiceDefaultConfig' => 'ace_term_service_default_config',
		'TermServiceConfig' => 'ace_term_service_config',
		'TermServiceGrade' => 'ace_term_service_grade',
		'TermServiceBuy' => 'ace_term_service_buy',
		'TermServiceUse' => 'ace_term_service_use',
		'LottoServiceConfig' => 'lotto_config',
		'LottoServiceExtractorConfig' => 'lotto_extractor_config',
		'LottoWinNumbers' => 'lotto_win_numbers',
		'LottoNumbers'	 => 'lotto_numbers',
		'LottoNumbersWin'	 => 'lotto_numbers_win',
		'LottoWinResult' => 'lotto_win_result',
		'LottoWinRecords' => 'lotto_win_records',
		'Member' => 'g5_member',
		'Group' =>'lotto_member_group',
		'Msg' => 'Message',
		'Msg_queue' => 'Message_queue',
		'SMSHistory' => 'sms5_history',
		'SMSWrite' => 'sms5_write',
		'Memo' => 'lotto_memo'
	],

	'app_public_url' => "http://".$_SERVER['SERVER_NAME']."/service/public",
	'app_lib_path' =>  dirname(__FILE__)."/../lib",
	'app_public_path' => dirname(__FILE__)."/../public",
	'session_path' => dirname(__FILE__)."/../../data/session",
    'upload_path' => dirname(__FILE__)."/../../data",
	'upload_url' => "http://".$_SERVER['SERVER_NAME']."/data",
/*
    'user_type' => [
        '1' => '비회원',
        '2' => '일반회원',
		'3' => 'TM회원',
		'4' => '회원4',
		'5' => '회원5',
		'6' => '회원6',
		'7' => '회원7',
		'8' => '회원8',
		'9' => '회원9',
		'10' => '관리자'
    ],
*/
	'user_type' => [
        '2' => '일반회원',
		'3' => '2차TM회원',
		'4' => '1차TM회원',
		'10' => '관리자'
    ],

	'consult_status' => [
        'na' => '상담이전',
		'상담중' => '상담중',
		'가망' => '가망',
		'부재' => '부재',
		'정지' => '정지',
		'무통' => '무통'
    ],

	'term_name' => [
		'day' => '일',
		'week' => '주',
		'month' => '개월'
	],

	'number_type' => [
		'extractor' => '분석번호',
		'admin' => '관리자지정번호',
		'user' => '사용자지정번호',
		'user_fixed' => '고정수추출',
		'user_exclude' => '제외수추출'
	],

    'pay_method' => [
		'agency' => '카드(결제대행)',
        'account' => '무통장입금',
        'manual' => '수기결제',
	'pg2' => 'pg2'
    ],

	'pg_info' => [
		'id' => 'lottofamily',
		'pay_url' => 'http://211.117.60.153:46776/futuresufPG.jsp'
	],


	'pg' => [

		'SHCard' => [
			'id' => 'abcpp',
			'api_key' => '*855498d97c915b6789835139d51a50db',
			'pay_url' => 'https://www.shvan.kr/api.php'

		],

		'SHHP' => [
			'id' => 'abcpp',
			'api_key' => '*855498d97c915b6789835139d51a50db',
			'pay_url' => 'https://www.shvan.kr/api.php'
		],

		'futureSurf' => [
			'id' => 'lottofamily',
			'pay_url' => 'http://211.117.60.153:46776/futuresufPG.jsp'
		],

		'payUp' => [
			'id' => 'abcpp',
			'domain' => 'https://cp.payup.co.kr',
			'pay_url' => '/v2/api/payment/abcpp/keyin2.do',
			'cancel_url' => '/v2/api/payment/abcpp/cancel2.do',
			'apiCertKey' => '2dee66265d5c469ab75817351adff58f'
		],
		'PG2' => 'PG2'
/*
		'payUp' => [ // test
			'id' => 'pluginTest',
			'domain' => 'http://dev.payup.co.kr',
			'pay_url' => '/v2/api/payment/pluginTest/keyin2.do',
			'cancel_url' => '/v2/api/payment/pluginTest/cancel2.do',
			'apiCertKey' => '2498049d60574814af6ac627891d82c7'
		]
*/
	],


    'apply_status' => [
        '신청접수' => '신청접수',
        '신청완료' => '신청완료',
		'신청취소' => '신청취소'
    ],

	'apply_status_color' => [
		'신청접수' => '#b7b7b7',
		'신청완료' => '#0066ff',
		'신청취소' => '#ff3333'
	],

	'weekdays_name' => ['일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일'],

	'apply_ing_status_color' => [
		'접수대기중' => '#b7b7b7',
		'접수중' => '#0066ff',
		'접수종료' => '#ff3333'
	],

	'lecture_status_color' => [
		'수강대기중' => '#b7b7b7',
		'수강중' => '#0066ff',
		'수강종료' => '#ff3333'
	],


	'banner_common' => [
		'main' => '메인배너',
		'main_bottom' => '메인하단 롤링배너',
		'right' => '우측배너(출력시 5px씩 떨어져 출력됨)',
		'left' => '좌측배너(출력시 5px씩 떨어져 출력됨)',
		'login' => '로그인배너',
		'info_top' => '정보자료실 상단배너',
		'info_left' => '정보자료실 좌측하단배너'
	],

	'banner_category' => [
		'category2' => '2차 카테고리 상단 배너',
		'best_brand' => '카테고리 배스트브랜드',
		'popular_product' => '카테고리 인기상품'
	],
// 가로x세로x세로가변(r)x대표상품(p)
	'banner_size' => [
		'main' => '590x331',
		'main_bottom' => '172x47',
		'right' => '100x120xr',
		'left' => '100x120xr',
		'login' => '450x350',
		'category2' => '980x80xr',
		'best_brand' => '140x180xxc',
		'popular_product' => '140x150xxp',
		'info_top' => '772x80xr',
		'info_left' => '185x100xr'
	],

	'log_type' => [
		'pdtn' => '신제품',
		'pdt' => '제품',
		'ctg' => '온라인카다로그',
		'mzn' => '온라인메거진',
		'com' => '회사소개',
		'sch' => '인기검색어',
		'r_email' => '이메일문의',
		'r_tel' => '전화문의',
		'r_sms' => 'SMS문의'
	],

	'page_title' => [
		'index.php' => '메인페이지',
		'lecture_detail.php' => '과정상세',
		'lecture_main.php' => '과정메인',
		'lecture_detail_schedule.php' => '일정상세',
		'login.php' => '로그인페이지',
		'audit_passed_company_list.php' => '인증업체검색',
		'mypage_lecture_history.php' => '마이페이지 > 수강이력내역',
		'mypage_lecture_status.php' => '마이페이지 > 수강신청내역',
		'board.php' => '게시판',
		
		'password_lost.php' => '아이디/패스워드 찾기',
		'register.php' => '회원가입(약관동의)',
		'login_check.php' => '로그인',
		
		'register_form.php' => '일반회원 가입페이지',
		'password_lost_process.php' => '아이디/패스워드찾기',
		'content.php' => '컨텐츠페이지'
		

	],

	'adv_type' => [
		'com' => '회사',
		'product' => '제품'
	],

	'adv_grade' => [
		'primium' => '프리미엄',
		'focus' => '포커스'
	],

	'adv_slot' => [
		'1' => '1',
		'2' => '2',
		'3' => '3'
	],

	'referer_search_key' => [
		'google.com' => 'q',
		'google.co.kr' => 'q',
		'naver.com' => 'query',
		'daum.net' => 'q'
	]
];
