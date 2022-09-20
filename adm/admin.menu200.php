<?php
$menu['menu200'] = array (
    array('200000', '회원관리', '/service/public/admin/lotto_member.php', 'member'),
	//array('200120', '<i class="fa fa-group"></i> 팀관리', '/service/public/admin/lotto_member_team.php', 'mb_list'),
    array('200100', '<i class="fa fa-play"></i> 정상회원관리', '/service/public/admin/lotto_member.php', 'mb_list'),
	array('200210', '<i class="fa fa-pause"></i> 이용중지회원관리', '/service/public/admin/lotto_stopped_member.php', 'mb_list'),
	array('200220', '<i class="fa fa-stop"></i> 탈퇴회원관리', '/service/public/admin/lotto_retired_member.php', 'mb_list'),
	array('200110', '미지정회원관리', '/service/public/admin/lotto_new_member.php', 'mb_list'),
	array('200150', '알림리스트', '/service/public/admin/lotto_member_schedule_list.php', 'mb_list'),
	array('200250', '3X', '/service/public/admin/lotto_member_custom1.php', 'mb_list', 1),
	array('200260', '4X', '/service/public/admin/lotto_member_custom2.php', 'mb_list', 1),
    array('200300', '회원메일발송', G5_ADMIN_URL.'/mail_list.php', 'mb_mail'),
    array('200800', '접속자집계', G5_ADMIN_URL.'/visit_list.php', 'mb_visit', 1),
    array('200810', '접속자검색', G5_ADMIN_URL.'/visit_search.php', 'mb_search', 1),
    array('200820', '접속자로그삭제', G5_ADMIN_URL.'/visit_delete.php', 'mb_delete', 1)
    //array('200200', '포인트관리', G5_ADMIN_URL.'/point_list.php', 'mb_point'),
    //array('200900', '투표관리', G5_ADMIN_URL.'/poll_list.php', 'mb_poll')
);
?>
