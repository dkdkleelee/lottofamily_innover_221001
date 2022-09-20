<?
[table]
member				=	g5_member
_memo                           =   ace_memo

default_config			   		=	ace_default_config
term_service_grade			    =   ace_term_service_grade
term_service_config				=	ace_term_service_config
term_service_buy				=	ace_term_service_buy
term_service_use				=	ace_term_service_use



[vars]

R = "#ff0000";
P = "#6666cc";
D = "#33cc00";
C = "#3333cc";


[auction_status]

1 = 상담중
2 = 상담완료
3 = 대기중
4 = 접수중
5 = 접수마감
6 = 완료
7 = 취소

[term_name]
month = 개월
year = 년
day = 일

[service_type]
1 = 엘리트패밀리
2 = 로얄패밀리


[auction_status_color]
1 = "#ff3300"
2 = "#0099cc"
3 = "#c400ff"
4 = "#c2b430"
5 = "#3399cc"
6 = "#036e2a"
7 = "#615304"
8 = "#654437"


[pay_method]
account							=	무통장입금
card							=	카드결제
iche							=	실시간이체
hp								=	휴대폰결제
virtual							=	가상계좌결제
agency							=	대행결제-카드


[plugin]
pay_plugin					= allthegate			;플러그인 디렉토리명
pay_result_file				= pt_pay_ok.php		;결제완료페이지

?>