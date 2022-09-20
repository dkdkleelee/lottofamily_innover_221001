<?
/*

CREATE TABLE `shop_area` (
  `ca_no` int(10) NOT NULL auto_increment,
  `ca_parentNo` int(10) default '0',
  `ca_title` varchar(100) default NULL,
  `ca_order` int(10) default NULL,
  PRIMARY KEY  (`ca_no`),
  KEY `ca_no` (`ca_no`)
) TYPE=MyISAM AUTO_INCREMENT=10 ;

*/

function getCategoryList($parentNo='0', $categoryTable) {
	global $db;

	$db->query("SELECT *, LENGTH(ca_no) as len FROM $categoryTable WHERE ca_parentNo='".$parentNo."' ORDER BY ca_order ",25);

	while($row = $db->fetch(25)) {
		$categoryList .= ($categoryList) ? ",".$row['ca_no'] : $row['ca_no'];
		$tmpCategoryList = getCategoryList($row['ca_no'], $categoryTable);
		if($tmpCategoryList != '') {
			$categoryList .= ($categoryList) ? ",".$tmpCategoryList : $tmpCategoryList;
		}
	}

	return $categoryList;
}


function getCategoryParents($parentNo, $categoryTable) {
	global $db;

	$db->query("SELECT * FROM $categoryTable WHERE ca_no='".$parentNo."'",24);

	if($row = $db->fetch(24)) {
		$parents[] = $row;
		$parentNo = $row['ca_parentNo'];
		$tmpParent = getCategoryParents($parentNo, $categoryTable);
		if($parentNo > 0) {
			for($i=0; $i<count($tmpParent); $i++) {
				array_push($parents, $tmpParent[$i]);
			}
		}
	}
	if(is_array($parents)) $parents = array_reverse($parents);

	return $parents;
}

function getCategoryChild($caNo, $categoryTable) {
	global $db;

	$db->query("SELECT * FROM  $categoryTable WHERE ca_parentNo='".$caNo."'",15);
	while($row = $db->fetch(15)) {
		$childs[] = $row;
	}
	return $childs;
}

function getAllCategoryArr($categoryTable, $depth="1") {
	global $db;

	$where = " WHERE ca_depth='".$depth."'";
	$db->query("SELECT *, LENGTH(ca_no) as len FROM $categoryTable $where ORDER BY ca_order ",26);

	while($row = $db->fetch(26)) {
		$categoryListArr[$row['ca_no']] = $row;
	}

	return $categoryListArr;
}


?>