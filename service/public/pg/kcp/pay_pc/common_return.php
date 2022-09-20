<?
    /* ============================================================================== */
    /* =   PAGE : 공통 통보 PAGE                                                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   연동시 오류가 발생하는 경우 아래의 주소로 접속하셔서 확인하시기 바랍니다.= */
    /* =   접속 주소 : http://kcp.co.kr/technique.requestcode.do                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2016   NHN KCP Inc.   All Rights Reserverd.               = */
    /* ============================================================================== */
?>
<?
    /* ============================================================================== */
    /* =   01. 공통 통보 페이지 설명(필독!!)                                        = */
    /* = -------------------------------------------------------------------------- = */
    /* =   공통 통보 페이지에서는,                                                  = */
    /* =   가상계좌 입금 통보 데이터와 모바일안심결제 통보 데이터 등을 KCP를 통해   = */
    /* =   실시간으로 통보 받을 수 있습니다.                                        = */
    /* =                                                                            = */
    /* =   common_return 페이지는 이러한 통보 데이터를 받기 위한 샘플 페이지        = */
    /* =   입니다. 현재의 페이지를 업체에 맞게 수정하신 후, 아래 사항을 참고하셔서  = */
    /* =   KCP 관리자 페이지에 등록해 주시기 바랍니다.                              = */
    /* =                                                                            = */
    /* =   등록 방법은 다음과 같습니다.                                             = */
    /* =  - KCP 관리자페이지(admin.kcp.co.kr)에 로그인 합니다.                      = */
    /* =  - [쇼핑몰 관리] -> [정보변경] -> [공통 URL 정보] -> [공통 URL 변경 후]에  = */
    /* =    결과값은 전송받을 가맹점 URL을 입력합니다.                              = */
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   02. 공통 통보 데이터 받기                                                = */
    /* = -------------------------------------------------------------------------- = */
    $site_cd      = $_POST [ "site_cd"  ];                 // 사이트 코드
    $tno          = $_POST [ "tno"      ];                 // KCP 거래번호
    $order_no     = $_POST [ "order_no" ];                 // 주문번호
    $tx_cd        = $_POST [ "tx_cd"    ];                 // 업무처리 구분 코드
    $tx_tm        = $_POST [ "tx_tm"    ];                 // 업무처리 완료 시간
    /* = -------------------------------------------------------------------------- = */
    $ipgm_name    = "";                                    // 주문자명
    $remitter     = "";                                    // 입금자명
    $ipgm_mnyx    = "";                                    // 입금 금액
    $bank_code    = "";                                    // 은행코드
    $account      = "";                                    // 가상계좌 입금계좌번호
    $op_cd        = "";                                    // 처리구분 코드
    $noti_id      = "";                                    // 통보 아이디
    /* = -------------------------------------------------------------------------- = */
	$refund_nm    = "";                                    // 환불계좌주명
    $refund_mny   = "";                                    // 환불금액
    $bank_code    = "";                                    // 은행코드
    /* = -------------------------------------------------------------------------- = */
    $st_cd        = "";                                    // 구매확인 코드
    $can_msg      = "";                                    // 구매취소 사유
    /* = -------------------------------------------------------------------------- = */
    $waybill_no   = "";                                    // 운송장 번호
    $waybill_corp = "";                                    // 택배 업체명
    /* = -------------------------------------------------------------------------- = */
    $cash_a_no    = "";                                    // 현금영수증 승인번호
    $cash_a_dt    = "";                                    // 현금영수증 승인시간
	$cash_no      = "";                                    // 현금영수증 거래번호

    /* = -------------------------------------------------------------------------- = */
    /* =   02-1. 가상계좌 입금 통보 데이터 받기                                     = */
    /* = -------------------------------------------------------------------------- = */
    if ( $tx_cd == "TX00" )
    {
        $ipgm_name = $_POST[ "ipgm_name" ];                // 주문자명
        $remitter  = $_POST[ "remitter"  ];                // 입금자명
        $ipgm_mnyx = $_POST[ "ipgm_mnyx" ];                // 입금 금액
        $bank_code = $_POST[ "bank_code" ];                // 은행코드
        $account   = $_POST[ "account"   ];                // 가상계좌 입금계좌번호
        $op_cd     = $_POST[ "op_cd"     ];                // 처리구분 코드
        $noti_id   = $_POST[ "noti_id"   ];                // 통보 아이디
        $cash_a_no = $_POST[ "cash_a_no" ];                // 현금영수증 승인번호
        $cash_a_dt = $_POST[ "cash_a_dt" ];                // 현금영수증 승인시간
		$cash_no   = $_POST[ "cash_no"   ];                // 현금영수증 거래번호
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   02-2. 가상계좌 환불 통보 데이터 받기                                     = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX01" )
    {
        $refund_nm  = $_POST[ "refund_nm"  ];               // 환불계좌주명
        $refund_mny = $_POST[ "refund_mny" ];               // 환불금액
        $bank_code  = $_POST[ "bank_code"  ];               // 은행코드
    }
    /* = -------------------------------------------------------------------------- = */
    /* =   02-3. 구매확인/구매취소 통보 데이터 받기                                  = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX02" )

        $st_cd = $_POST[ "st_cd" ];                         // 구매확인 코드

        if ( $st_cd = "N"  )                                // 구매확인 상태가 구매취소인 경우
        {
            $can_msg = $_POST[ "can_msg"   ];               // 구매취소 사유
        }

    /* = -------------------------------------------------------------------------- = */
    /* =   02-4. 배송시작 통보 데이터 받기                                           = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX03" )
	{

        $waybill_no   = $_POST[ "waybill_no"   ];           // 운송장 번호
        $waybill_corp = $_POST[ "waybill_corp" ];           // 택배 업체명
	}

    /* ============================================================================== */
    /* =   03. 공통 통보 결과를 업체 자체적으로 DB 처리 작업하시는 부분입니다.      = */
    /* = -------------------------------------------------------------------------- = */
    /* =   통보 결과를 DB 작업 하는 과정에서 정상적으로 통보된 건에 대해 DB 작업에  = */
    /* =   실패하여 DB update 가 완료되지 않은 경우, 결과를 재통보 받을 수 있는     = */
    /* =   프로세스가 구성되어 있습니다.                                            = */
    /* =                                                                            = */
    /* =   * DB update가 정상적으로 완료된 경우                                     = */
    /* =   하단의 [04. result 값 세팅 하기] 에서 result 값의 value값을 0000으로     = */
    /* =   설정해 주시기 바랍니다.                                                  = */
    /* =                                                                            = */
    /* =   * DB update가 실패한 경우                                                = */
    /* =   하단의 [04. result 값 세팅 하기] 에서 result 값의 value값을 0000이외의   = */
    /* =   값으로 설정해 주시기 바랍니다.                                           = */
    /* = -------------------------------------------------------------------------- = */

    /* = -------------------------------------------------------------------------- = */
    /* =   03-1. 가상계좌 입금 통보 데이터 DB 처리 작업 부분                        = */
    /* = -------------------------------------------------------------------------- = */
    if ( $tx_cd == "TX00" )
    {
    }
	/* = -------------------------------------------------------------------------- = */
    /* =   03-2. 가상계좌 환불 통보 데이터 DB 처리 작업 부분                        = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX01" )
    {
    }
    /* = -------------------------------------------------------------------------- = */
    /* =   03-3. 구매확인/구매취소 통보 데이터 DB 처리 작업 부분                    = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX02" )
    {
    }
    /* = -------------------------------------------------------------------------- = */
    /* =   03-4. 배송시작 통보 데이터 DB 처리 작업 부분                             = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX03" )
    {
    }
    /* = -------------------------------------------------------------------------- = */
    /* =   03-5. 정산보류 통보 데이터 DB 처리 작업 부분                             = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX04" )
    {
    }
    /* = -------------------------------------------------------------------------- = */
    /* =   03-6. 즉시취소 통보 데이터 DB 처리 작업 부분                             = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX05" )
    {
    }
    /* = -------------------------------------------------------------------------- = */
    /* =   03-7. 취소 통보 데이터 DB 처리 작업 부분                                 = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX06" )
    {
    }
    /* = -------------------------------------------------------------------------- = */
    /* =   03-8. 발급계좌해지 통보 데이터 DB 처리 작업 부분                         = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX07" )
    {
    }
    /* = -------------------------------------------------------------------------- = */
    /* =   03-9. 모바일안심결제 통보 데이터 DB 처리 작업 부분                       = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX08" )
    {
    }
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   04. result 값 세팅 하기                                                  = */
    /* ============================================================================== */
?>
<html><body><form><input type="hidden" name="result" value="0000"></form></body></html>