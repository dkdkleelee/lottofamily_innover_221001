<?
####################################음력 양력변환 함수 들어 가는 부분이다.




function sol2lun($sy,$sm,$sd)
/* 양력을 음력으로 변환 */
{
$kk = array (
/*char kk[203][12] = (*/
/* 1841 */    array (1, 2, 4, 1, 1, 2,    1, 2, 1, 2, 2, 1),
              array (2, 2, 1, 2, 1, 1,    2, 1, 2, 1, 2, 1),
              array (2, 2, 2, 1, 2, 1,    4, 1, 2, 1, 2, 1),
	      array (2, 2, 1, 2, 1, 2,    1, 2, 1, 2, 1, 2),
	      array (1, 2, 1, 2, 2, 1,    2, 1, 2, 1, 2, 1),
	      array (2, 1, 2, 1, 5, 2,    1, 2, 2, 1, 2, 1),
	      array (2, 1, 1, 2, 1, 2,    1, 2, 2, 2, 1, 2),
	      array (1, 2, 1, 1, 2, 1,    2, 1, 2, 2, 2, 1),
	      array (2, 1, 2, 3, 2, 1,    2, 1, 2, 1, 2, 2),
	      array (2, 1, 2, 1, 1, 2,    1, 1, 2, 2, 1, 2),
/* 1851 */    array (2, 2, 1, 2, 1, 1,    2, 1, 2, 1, 5, 2),
              array (2, 1, 2, 2, 1, 1,    2, 1, 2, 1, 1, 2),
              array (2, 1, 2, 2, 1, 2,    1, 2, 1, 2, 1, 2),
	      array (1, 2, 1, 2, 1, 2,    5, 2, 1, 2, 1, 2),
	      array (1, 1, 2, 1, 2, 2,    1, 2, 2, 1, 2, 1),
	      array (2, 1, 1, 2, 1, 2,    1, 2, 2, 2, 1, 2),
	      array (1, 2, 1, 1, 5, 2,    1, 2, 1, 2, 2, 2),
	      array (1, 2, 1, 1, 2, 1,    1, 2, 2, 1, 2, 2),
	      array (2, 1, 2, 1, 1, 2,    1, 1, 2, 1, 2, 2),
	      array (2, 1, 6, 1, 1, 2,    1, 1, 2, 1, 2, 2),
/* 1861 */    array (1, 2, 2, 1, 2, 1,    2, 1, 2, 1, 1, 2),
              array (2, 1, 2, 1, 2, 2,    1, 2, 2, 3, 1, 2),
	      array (1, 2, 2, 1, 2, 1,    2, 2, 1, 2, 1, 2),
	      array (1, 1, 2, 1, 2, 1,    2, 2, 1, 2, 2, 1),
	      array (2, 1, 1, 2, 4, 1,    2, 2, 1, 2, 2, 1),
	      array (2, 1, 1, 2, 1, 1,    2, 2, 1, 2, 2, 2),
	      array (1, 2, 1, 1, 2, 1,    1, 2, 1, 2, 2, 2),
	      array (1, 2, 2, 3, 2, 1,    1, 2, 1, 2, 2, 1),
	      array (2, 2, 2, 1, 1, 2,    1, 1, 2, 1, 2, 1),
	      array (2, 2, 2, 1, 2, 1,    2, 1, 1, 5, 2, 1),
/* 1871 */    array (2, 2, 1, 2, 2, 1,    2, 1, 2, 1, 1, 2),
              array (1, 2, 1, 2, 2, 1,    2, 1, 2, 2, 1, 2),
	      array (1, 1, 2, 1, 2, 4,    2, 1, 2, 2, 1, 2),
	      array (1, 1, 2, 1, 2, 1,    2, 1, 2, 2, 2, 1),
	      array (2, 1, 1, 2, 1, 1,    2, 1, 2, 2, 2, 1),
	      array (2, 2, 1, 1, 5, 1,    2, 1, 2, 2, 1, 2),
	      array (2, 2, 1, 1, 2, 1,    1, 2, 1, 2, 1, 2),
	      array (2, 2, 1, 2, 1, 2,    1, 1, 2, 1, 2, 1),
	      array (2, 2, 4, 2, 1, 2,    1, 1, 2, 1, 2, 1),
	      array (2, 1, 2, 2, 1, 2,    2, 1, 2, 1, 1, 2),
/* 1881 */    array (1, 2, 1, 2, 1, 2,    5, 2, 2, 1, 2, 1),
              array (1, 2, 1, 2, 1, 2,    1, 2, 2, 1, 2, 2),
              array (1, 1, 2, 1, 1, 2,    1, 2, 2, 2, 1, 2),
              array (2, 1, 1, 2, 3, 2,    1, 2, 2, 1, 2, 2),
              array (2, 1, 1, 2, 1, 1,    2, 1, 2, 1, 2, 2),
	      array (2, 1, 2, 1, 2, 1,    1, 2, 1, 2, 1, 2),
              array (2, 2, 1, 5, 2, 1,    1, 2, 1, 2, 1, 2),
              array (2, 1, 2, 2, 1, 2,    1, 1, 2, 1, 2, 1),
              array (2, 1, 2, 2, 1, 2,    1, 2, 1, 2, 1, 2),
 	      array (1, 5, 2, 1, 2, 2,    1, 2, 1, 2, 1, 2),
/* 1891 */    array (1, 2, 1, 2, 1, 2,    1, 2, 2, 1, 2, 2),
              array (1, 1, 2, 1, 1, 5,    2, 2, 1, 2, 2, 2),
              array (1, 1, 2, 1, 1, 2,    1, 2, 1, 2, 2, 2),
              array (1, 2, 1, 2, 1, 1,    2, 1, 2, 1, 2, 2),
              array (2, 1, 2, 1, 5, 1,    2, 1, 2, 1, 2, 1),
              array (2, 2, 2, 1, 2, 1,    1, 2, 1, 2, 1, 2),
              array (1, 2, 2, 1, 2, 1,    2, 1, 2, 1, 2, 1),
	      array (2, 1, 5, 2, 2, 1,    2, 1, 2, 1, 2, 1),
              array (2, 1, 2, 1, 2, 1,    2, 2, 1, 2, 1, 2),
              array (1, 2, 1, 1, 2, 1,    2, 5, 2, 2, 1, 2),
/* 1901 */    array (1, 2, 1, 1, 2, 1,    2, 1, 2, 2, 2, 1),
              array (2, 1, 2, 1, 1, 2,    1, 2, 1, 2, 2, 2),
 	      array (1, 2, 1, 2, 3, 2,    1, 1, 2, 2, 1, 2),
              array (2, 2, 1, 2, 1, 1,    2, 1, 1, 2, 2, 1),
              array (2, 2, 1, 2, 2, 1,    1, 2, 1, 2, 1, 2),
              array (1, 2, 2, 4, 1, 2,    1, 2, 1, 2, 1, 2),
              array (1, 2, 1, 2, 1, 2,    2, 1, 2, 1, 2, 1),
              array (2, 1, 1, 2, 2, 1,    2, 1, 2, 2, 1, 2),
              array (1, 5, 1, 2, 1, 2,    1, 2, 2, 2, 1, 2),
 	      array (1, 2, 1, 1, 2, 1,    2, 1, 2, 2, 2, 1),
/* 1911 */    array (2, 1, 2, 1, 1, 5,    1, 2, 2, 1, 2, 2),
              array (2, 1, 2, 1, 1, 2,    1, 1, 2, 2, 1, 2),
 	      array (2, 2, 1, 2, 1, 1,    2, 1, 1, 2, 1, 2),
              array (2, 2, 1, 2, 5, 1,    2, 1, 2, 1, 1, 2),
 	      array (2, 1, 2, 2, 1, 2,    1, 2, 1, 2, 1, 2),
              array (1, 2, 1, 2, 1, 2,    2, 1, 2, 1, 2, 1),
              array (2, 3, 2, 1, 2, 2,    1, 2, 2, 1, 2, 1),
              array (2, 1, 1, 2, 1, 2,    1, 2, 2, 2, 1, 2),
              array (1, 2, 1, 1, 2, 1,    5, 2, 2, 1, 2, 2),
              array (1, 2, 1, 1, 2, 1,    1, 2, 2, 1, 2, 2),
/* 1921 */    array (2, 1, 2, 1, 1, 2,    1, 1, 2, 1, 2, 2),
              array (2, 1, 2, 2, 3, 2,    1, 1, 2, 1, 2, 2),
              array (1, 2, 2, 1, 2, 1,    2, 1, 2, 1, 1, 2),
              array (2, 1, 2, 1, 2, 2,    1, 2, 1, 2, 1, 1),
 	      array (2, 1, 2, 5, 2, 1,    2, 2, 1, 2, 1, 2),
              array (1, 1, 2, 1, 2, 1,    2, 2, 1, 2, 2, 1),
 	      array (2, 1, 1, 2, 1, 2,    1, 2, 2, 1, 2, 2),
              array (1, 5, 1, 2, 1, 1,    2, 2, 1, 2, 2, 2),
              array (1, 2, 1, 1, 2, 1,    1, 2, 1, 2, 2, 2),
              array (1, 2, 2, 1, 1, 5,    1, 2, 1, 2, 2, 1),
/* 1931 */    array (2, 2, 2, 1, 1, 2,    1, 1, 2, 1, 2, 1),
              array (2, 2, 2, 1, 2, 1,    2, 1, 1, 2, 1, 2),
 	      array (1, 2, 2, 1, 6, 1,    2, 1, 2, 1, 1, 2),
              array (1, 2, 1, 2, 2, 1,    2, 2, 1, 2, 1, 2),
              array (1, 1, 2, 1, 2, 1,    2, 2, 1, 2, 2, 1),
              array (2, 1, 4, 1, 2, 1,    2, 1, 2, 2, 2, 1),
 	      array (2, 1, 1, 2, 1, 1,    2, 1, 2, 2, 2, 1),
              array (2, 2, 1, 1, 2, 1,    4, 1, 2, 2, 1, 2),
 	      array (2, 2, 1, 1, 2, 1,    1, 2, 1, 2, 1, 2),
              array (2, 2, 1, 2, 1, 2,    1, 1, 2, 1, 2, 1),
/* 1941 */    array (2, 2, 1, 2, 2, 4,    1, 1, 2, 1, 2, 1),
              array (2, 1, 2, 2, 1, 2,    2, 1, 2, 1, 1, 2),
              array (1, 2, 1, 2, 1, 2,    2, 1, 2, 2, 1, 2),
              array (1, 1, 2, 4, 1, 2,    1, 2, 2, 1, 2, 2),
 	      array (1, 1, 2, 1, 1, 2,    1, 2, 2, 2, 1, 2),
              array (2, 1, 1, 2, 1, 1,    2, 1, 2, 2, 1, 2),
              array (2, 5, 1, 2, 1, 1,    2, 1, 2, 1, 2, 2),
              array (2, 1, 2, 1, 2, 1,    1, 2, 1, 2, 1, 2),
 	      array (2, 2, 1, 2, 1, 2,    3, 2, 1, 2, 1, 2),
              array (2, 1, 2, 2, 1, 2,    1, 1, 2, 1, 2, 1),
/* 1951 */    array (2, 1, 2, 2, 1, 2,    1, 2, 1, 2, 1, 2),
              array (1, 2, 1, 2, 4, 2,    1, 2, 1, 2, 1, 2),
              array (1, 2, 1, 1, 2, 2,    1, 2, 2, 1, 2, 2),
              array (1, 1, 2, 1, 1, 2,    1, 2, 2, 1, 2, 2),
              array (2, 1, 4, 1, 1, 2,    1, 2, 1, 2, 2, 2),
              array (1, 2, 1, 2, 1, 1,    2, 1, 2, 1, 2, 2),
 	      array (2, 1, 2, 1, 2, 1,    1, 5, 2, 1, 2, 2),
              array (1, 2, 2, 1, 2, 1,    1, 2, 1, 2, 1, 2),
              array (1, 2, 2, 1, 2, 1,    2, 1, 2, 1, 2, 1),
              array (2, 1, 2, 1, 2, 5,    2, 1, 2, 1, 2, 1),
/* 1961 */    array (2, 1, 2, 1, 2, 1,    2, 2, 1, 2, 1, 2),
 	      array (1, 2, 1, 1, 2, 1,    2, 2, 1, 2, 2, 1),
              array (2, 1, 2, 3, 2, 1,    2, 1, 2, 2, 2, 1),
              array (2, 1, 2, 1, 1, 2,    1, 2, 1, 2, 2, 2),
              array (1, 2, 1, 2, 1, 1,    2, 1, 1, 2, 2, 1),
              array (2, 2, 5, 2, 1, 1,    2, 1, 1, 2, 2, 1),
              array (2, 2, 1, 2, 2, 1,    1, 2, 1, 2, 1, 2),
              array (1, 2, 2, 1, 2, 1,    5, 2, 1, 2, 1, 2),
 	      array (1, 2, 1, 2, 1, 2,    2, 1, 2, 1, 2, 1),
              array (2, 1, 1, 2, 2, 1,    2, 1, 2, 2, 1, 2),
/* 1971 */    array (1, 2, 1, 1, 5, 2,    1, 2, 2, 2, 1, 2),
 	      array (1, 2, 1, 1, 2, 1,    2, 1, 2, 2, 2, 1),
              array (2, 1, 2, 1, 1, 2,    1, 1, 2, 2, 2, 1),
 	      array (2, 2, 1, 5, 1, 2,    1, 1, 2, 2, 1, 2),
              array (2, 2, 1, 2, 1, 1,    2, 1, 1, 2, 1, 2),
              array (2, 2, 1, 2, 1, 2,    1, 5, 2, 1, 1, 2),
              array (2, 1, 2, 2, 1, 2,    1, 2, 1, 2, 1, 1),
              array (2, 2, 1, 2, 1, 2,    2, 1, 2, 1, 2, 1),
              array (2, 1, 1, 2, 1, 6,    1, 2, 2, 1, 2, 1),
              array (2, 1, 1, 2, 1, 2,    1, 2, 2, 1, 2, 2),
/* 1981 */    array (1, 2, 1, 1, 2, 1,    1, 2, 2, 1, 2, 2),
              array (2, 1, 2, 3, 2, 1,    1, 2, 2, 1, 2, 2),
              array (2, 1, 2, 1, 1, 2,    1, 1, 2, 1, 2, 2),
 	      array (2, 1, 2, 2, 1, 1,    2, 1, 1, 5, 2, 2),
              array (1, 2, 2, 1, 2, 1,    2, 1, 1, 2, 1, 2),
 	      array (1, 2, 2, 1, 2, 2,    1, 2, 1, 2, 1, 1),
              array (2, 1, 2, 2, 1, 5,    2, 2, 1, 2, 1, 2),
              array (1, 1, 2, 1, 2, 1,    2, 2, 1, 2, 2, 1),
              array (2, 1, 1, 2, 1, 2,    1, 2, 2, 1, 2, 2),
              array (1, 2, 1, 1, 5, 1,    2, 1, 2, 2, 2, 2),
/* 1991 */    array (1, 2, 1, 1, 2, 1,    1, 2, 1, 2, 2, 2),
 	      array (1, 2, 2, 1, 1, 2,    1, 1, 2, 1, 2, 2),
              array (1, 2, 5, 2, 1, 2,    1, 1, 2, 1, 2, 1),
              array (2, 2, 2, 1, 2, 1,    2, 1, 1, 2, 1, 2),
              array (1, 2, 2, 1, 2, 2,    1, 5, 2, 1, 1, 2),
 	      array (1, 2, 1, 2, 2, 1,    2, 1, 2, 2, 1, 2),
              array (1, 1, 2, 1, 2, 1,    2, 2, 1, 2, 2, 1),
 	      array (2, 1, 1, 2, 3, 2,    2, 1, 2, 2, 2, 1),
              array (2, 1, 1, 2, 1, 1,    2, 1, 2, 2, 2, 1),
              array (2, 2, 1, 1, 2, 1,    1, 2, 1, 2, 2, 1),
/* 2001 */    array (2, 2, 2, 3, 2, 1,    1, 2, 1, 2, 1, 2),
              array (2, 2, 1, 2, 1, 2,    1, 1, 2, 1, 2, 1),
              array (2, 2, 1, 2, 2, 1,    2, 1, 1, 2, 1, 2),
 	      array (1, 5, 2, 2, 1, 2,    1, 2, 2, 1, 1, 2),
              array (1, 2, 1, 2, 1, 2,    2, 1, 2, 2, 1, 2),
              array (1, 1, 2, 1, 2, 1,    5, 2, 2, 1, 2, 2),
              array (1, 1, 2, 1, 1, 2,    1, 2, 2, 2, 1, 2),
 	      array (2, 1, 1, 2, 1, 1,    2, 1, 2, 2, 1, 2),
              array (2, 2, 1, 1, 5, 1,    2, 1, 2, 1, 2, 2),
 	      array (2, 1, 2, 1, 2, 1,    1, 2, 1, 2, 1, 2),
/* 2011 */    array (2, 1, 2, 2, 1, 2,    1, 1, 2, 1, 2, 1),
              array (2, 1, 6, 2, 1, 2,    1, 1, 2, 1, 2, 1),
              array (2, 1, 2, 2, 1, 2,    1, 2, 1, 2, 1, 2),
              array (1, 2, 1, 2, 1, 2,    1, 2, 5, 2, 1, 2),
              array (1, 2, 1, 1, 2, 1,    2, 2, 2, 1, 2, 2),
 	      array (1, 1, 2, 1, 1, 2,    1, 2, 2, 1, 2, 2),
              array (2, 1, 1, 2, 3, 2,    1, 2, 1, 2, 2, 2),
              array (1, 2, 1, 2, 1, 1,    2, 1, 2, 1, 2, 2),
              array (2, 1, 2, 1, 2, 1,    1, 2, 1, 2, 1, 2),
 	      array (2, 1, 2, 5, 2, 1,    1, 2, 1, 2, 1, 2),
/* 2021 */    array (1, 2, 2, 1, 2, 1,    2, 1, 2, 1, 2, 1),
              array (2, 1, 2, 1, 2, 2,    1, 2, 1, 2, 1, 2),
              array (1, 5, 2, 1, 2, 1,    2, 2, 1, 2, 1, 2),
              array (1, 2, 1, 1, 2, 1,    2, 2, 1, 2, 2, 1),
              array (2, 1, 2, 1, 1, 5,    2, 1, 2, 2, 2, 1),
              array (2, 1, 2, 1, 1, 2,    1, 2, 1, 2, 2, 2),
              array (1, 2, 1, 2, 1, 1,    2, 1, 1, 2, 2, 2),
 	      array (1, 2, 2, 1, 5, 1,    2, 1, 1, 2, 2, 1),
              array (2, 2, 1, 2, 2, 1,    1, 2, 1, 1, 2, 2),
              array (1, 2, 1, 2, 2, 1,    2, 1, 2, 1, 2, 1),
/* 2031 */    array (2, 1, 5, 2, 1, 2,    2, 1, 2, 1, 2, 1),
              array (2, 1, 1, 2, 1, 2,    2, 1, 2, 2, 1, 2),
 	      array (1, 2, 1, 1, 2, 1,    5, 2, 2, 2, 1, 2),
              array (1, 2, 1, 1, 2, 1,    2, 1, 2, 2, 2, 1),
              array (2, 1, 2, 1, 1, 2,    1, 1, 2, 2, 1, 2),
	      array (2, 2, 1, 2, 1, 4,    1, 1, 2, 1, 2, 2),
              array (2, 2, 1, 2, 1, 1,    2, 1, 1, 2, 1, 2),
              array (2, 2, 1, 2, 1, 2,    1, 2, 1, 1, 2, 1),
              array (2, 2, 1, 2, 5, 2,    1, 2, 1, 2, 1, 1),
 	      array (2, 1, 2, 2, 1, 2,    2, 1, 2, 1, 2, 1),
/* 2041 */    array (2, 1, 1, 2, 1, 2,    2, 1, 2, 2, 1, 2),
              array (1, 5, 1, 2, 1, 2,    1, 2, 2, 2, 1, 2),
	      array (1, 2, 1, 1, 2, 1,    1, 2, 2, 1, 2, 2)
);

$gan = array ( "갑","을","병","정","무","기","경","신","임","계" );
$jee = array ( "자","축","인","묘","진","사","오","미","신","유","술","해" );
$ddi = array ( "쥐","소","범","토끼","용","뱀","말","양","원숭이","닭","개","돼지" );
$week = array ( "일","월","화","수","목","금","토" );
$md = array ( 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 );


	$dt = array (
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0
	);

	$td1 = 1840 * 365 + 1840/4 - 1840/100 + 1840/400 + 23;


	febdays($sy);
	$td2 = ($sy-1) * 365 + ($sy-1)/4 - ($sy-1)/100 + ($sy-1)/400 + $sd;
	for($i=0; $i<$sm-1; $i++) $td2 += $md[$i];
	$td = $td2 - $td1 + 1;
	for($i=0; $i<=$sy-1841; $i++) {
		$dt[$i] = 0;
		for($j=0; $j<12; $j++) {
			switch( $kk[$i][$j] ) {
				case 1 : $mm = 29;
					     break;
				case 2 : $mm = 30;
					     break;
				case 3 : $mm = 58;   /* 29+29 */
					     break;
                		case 4 : $mm = 59;   /* 29+30 */
					     break;
                		case 5 : $mm = 59;   /* 30+29 */
					     break;
				case 6 : $mm = 60;   /* 30+30 */
					     break;
			}
			$dt[$i] += $mm;
		}
	}
	$ly = 0;
	while(1) {
		if( $td > $dt[$ly] ) {
			$td -= $dt[$ly];
			$ly++;
		} else break;
	}

$td--;

	$lm = 0;
	$yoon = "";
	while(1) {
		if( $kk[$ly][$lm] <= 2 ) {
			$mm = $kk[$ly][$lm] + 28;
			if( $td > $mm ) {
				$td -= $mm;
				$lm++;
			} else break;
		} else {
			switch( $kk[$ly][$lm] ) {
				case 3 : $m1 = 29;
					 $m2 = 29;
					 break;
				case 4 : $m1 = 29;
					 $m2 = 30;
					 break;
				case 5 : $m1 = 30;
					 $m2 = 29;
					 break;
				case 6 : $m1 = 30;
					 $m2 = 30;
					 break;
			}

			if( $td > $m1 ) {
				$td -= $m1;
				if( $td > $m2 ) {
					$td -= $m2;
					$lm++;
				} else {
					$yoon = "YOON";
					break;
				}
			} else break;
		}
	}
	$ly += 1841;
	$lm += 1;
	$ld = $td+1 /*-1*/;
	$w = $td2 % 7;
	$i = ($td2+4) % 10;
	$j = ($td2+2) % 12;
	$k1 = ($ly+6) % 10;
	$k2 = ($ly+8) % 12;


	if ($lm < 10)	$lm = '0' . $lm;
	if ($ld < 10)	$ld = '0' . $ld;

	return $ly . $lm . $ld;

}

function lun2sol($lyear, $lmonth, $lday)
/* 음력을 양력으로 변환 */
{
$kk = array (
/*char kk[203][12] = (*/
/* 1841 */    array (1, 2, 4, 1, 1, 2,    1, 2, 1, 2, 2, 1),
              array (2, 2, 1, 2, 1, 1,    2, 1, 2, 1, 2, 1),
              array (2, 2, 2, 1, 2, 1,    4, 1, 2, 1, 2, 1),
	      array (2, 2, 1, 2, 1, 2,    1, 2, 1, 2, 1, 2),
	      array (1, 2, 1, 2, 2, 1,    2, 1, 2, 1, 2, 1),
	      array (2, 1, 2, 1, 5, 2,    1, 2, 2, 1, 2, 1),
	      array (2, 1, 1, 2, 1, 2,    1, 2, 2, 2, 1, 2),
	      array (1, 2, 1, 1, 2, 1,    2, 1, 2, 2, 2, 1),
	      array (2, 1, 2, 3, 2, 1,    2, 1, 2, 1, 2, 2),
	      array (2, 1, 2, 1, 1, 2,    1, 1, 2, 2, 1, 2),
/* 1851 */    array (2, 2, 1, 2, 1, 1,    2, 1, 2, 1, 5, 2),
              array (2, 1, 2, 2, 1, 1,    2, 1, 2, 1, 1, 2),
              array (2, 1, 2, 2, 1, 2,    1, 2, 1, 2, 1, 2),
	      array (1, 2, 1, 2, 1, 2,    5, 2, 1, 2, 1, 2),
	      array (1, 1, 2, 1, 2, 2,    1, 2, 2, 1, 2, 1),
	      array (2, 1, 1, 2, 1, 2,    1, 2, 2, 2, 1, 2),
	      array (1, 2, 1, 1, 5, 2,    1, 2, 1, 2, 2, 2),
	      array (1, 2, 1, 1, 2, 1,    1, 2, 2, 1, 2, 2),
	      array (2, 1, 2, 1, 1, 2,    1, 1, 2, 1, 2, 2),
	      array (2, 1, 6, 1, 1, 2,    1, 1, 2, 1, 2, 2),
/* 1861 */    array (1, 2, 2, 1, 2, 1,    2, 1, 2, 1, 1, 2),
              array (2, 1, 2, 1, 2, 2,    1, 2, 2, 3, 1, 2),
	      array (1, 2, 2, 1, 2, 1,    2, 2, 1, 2, 1, 2),
	      array (1, 1, 2, 1, 2, 1,    2, 2, 1, 2, 2, 1),
	      array (2, 1, 1, 2, 4, 1,    2, 2, 1, 2, 2, 1),
	      array (2, 1, 1, 2, 1, 1,    2, 2, 1, 2, 2, 2),
	      array (1, 2, 1, 1, 2, 1,    1, 2, 1, 2, 2, 2),
	      array (1, 2, 2, 3, 2, 1,    1, 2, 1, 2, 2, 1),
	      array (2, 2, 2, 1, 1, 2,    1, 1, 2, 1, 2, 1),
	      array (2, 2, 2, 1, 2, 1,    2, 1, 1, 5, 2, 1),
/* 1871 */    array (2, 2, 1, 2, 2, 1,    2, 1, 2, 1, 1, 2),
              array (1, 2, 1, 2, 2, 1,    2, 1, 2, 2, 1, 2),
	      array (1, 1, 2, 1, 2, 4,    2, 1, 2, 2, 1, 2),
	      array (1, 1, 2, 1, 2, 1,    2, 1, 2, 2, 2, 1),
	      array (2, 1, 1, 2, 1, 1,    2, 1, 2, 2, 2, 1),
	      array (2, 2, 1, 1, 5, 1,    2, 1, 2, 2, 1, 2),
	      array (2, 2, 1, 1, 2, 1,    1, 2, 1, 2, 1, 2),
	      array (2, 2, 1, 2, 1, 2,    1, 1, 2, 1, 2, 1),
	      array (2, 2, 4, 2, 1, 2,    1, 1, 2, 1, 2, 1),
	      array (2, 1, 2, 2, 1, 2,    2, 1, 2, 1, 1, 2),
/* 1881 */    array (1, 2, 1, 2, 1, 2,    5, 2, 2, 1, 2, 1),
              array (1, 2, 1, 2, 1, 2,    1, 2, 2, 1, 2, 2),
              array (1, 1, 2, 1, 1, 2,    1, 2, 2, 2, 1, 2),
              array (2, 1, 1, 2, 3, 2,    1, 2, 2, 1, 2, 2),
              array (2, 1, 1, 2, 1, 1,    2, 1, 2, 1, 2, 2),
	      array (2, 1, 2, 1, 2, 1,    1, 2, 1, 2, 1, 2),
              array (2, 2, 1, 5, 2, 1,    1, 2, 1, 2, 1, 2),
              array (2, 1, 2, 2, 1, 2,    1, 1, 2, 1, 2, 1),
              array (2, 1, 2, 2, 1, 2,    1, 2, 1, 2, 1, 2),
 	      array (1, 5, 2, 1, 2, 2,    1, 2, 1, 2, 1, 2),
/* 1891 */    array (1, 2, 1, 2, 1, 2,    1, 2, 2, 1, 2, 2),
              array (1, 1, 2, 1, 1, 5,    2, 2, 1, 2, 2, 2),
              array (1, 1, 2, 1, 1, 2,    1, 2, 1, 2, 2, 2),
              array (1, 2, 1, 2, 1, 1,    2, 1, 2, 1, 2, 2),
              array (2, 1, 2, 1, 5, 1,    2, 1, 2, 1, 2, 1),
              array (2, 2, 2, 1, 2, 1,    1, 2, 1, 2, 1, 2),
              array (1, 2, 2, 1, 2, 1,    2, 1, 2, 1, 2, 1),
	      array (2, 1, 5, 2, 2, 1,    2, 1, 2, 1, 2, 1),
              array (2, 1, 2, 1, 2, 1,    2, 2, 1, 2, 1, 2),
              array (1, 2, 1, 1, 2, 1,    2, 5, 2, 2, 1, 2),
/* 1901 */    array (1, 2, 1, 1, 2, 1,    2, 1, 2, 2, 2, 1),
              array (2, 1, 2, 1, 1, 2,    1, 2, 1, 2, 2, 2),
 	      array (1, 2, 1, 2, 3, 2,    1, 1, 2, 2, 1, 2),
              array (2, 2, 1, 2, 1, 1,    2, 1, 1, 2, 2, 1),
              array (2, 2, 1, 2, 2, 1,    1, 2, 1, 2, 1, 2),
              array (1, 2, 2, 4, 1, 2,    1, 2, 1, 2, 1, 2),
              array (1, 2, 1, 2, 1, 2,    2, 1, 2, 1, 2, 1),
              array (2, 1, 1, 2, 2, 1,    2, 1, 2, 2, 1, 2),
              array (1, 5, 1, 2, 1, 2,    1, 2, 2, 2, 1, 2),
 	      array (1, 2, 1, 1, 2, 1,    2, 1, 2, 2, 2, 1),
/* 1911 */    array (2, 1, 2, 1, 1, 5,    1, 2, 2, 1, 2, 2),
              array (2, 1, 2, 1, 1, 2,    1, 1, 2, 2, 1, 2),
 	      array (2, 2, 1, 2, 1, 1,    2, 1, 1, 2, 1, 2),
              array (2, 2, 1, 2, 5, 1,    2, 1, 2, 1, 1, 2),
 	      array (2, 1, 2, 2, 1, 2,    1, 2, 1, 2, 1, 2),
              array (1, 2, 1, 2, 1, 2,    2, 1, 2, 1, 2, 1),
              array (2, 3, 2, 1, 2, 2,    1, 2, 2, 1, 2, 1),
              array (2, 1, 1, 2, 1, 2,    1, 2, 2, 2, 1, 2),
              array (1, 2, 1, 1, 2, 1,    5, 2, 2, 1, 2, 2),
              array (1, 2, 1, 1, 2, 1,    1, 2, 2, 1, 2, 2),
/* 1921 */    array (2, 1, 2, 1, 1, 2,    1, 1, 2, 1, 2, 2),
              array (2, 1, 2, 2, 3, 2,    1, 1, 2, 1, 2, 2),
              array (1, 2, 2, 1, 2, 1,    2, 1, 2, 1, 1, 2),
              array (2, 1, 2, 1, 2, 2,    1, 2, 1, 2, 1, 1),
 	      array (2, 1, 2, 5, 2, 1,    2, 2, 1, 2, 1, 2),
              array (1, 1, 2, 1, 2, 1,    2, 2, 1, 2, 2, 1),
 	      array (2, 1, 1, 2, 1, 2,    1, 2, 2, 1, 2, 2),
              array (1, 5, 1, 2, 1, 1,    2, 2, 1, 2, 2, 2),
              array (1, 2, 1, 1, 2, 1,    1, 2, 1, 2, 2, 2),
              array (1, 2, 2, 1, 1, 5,    1, 2, 1, 2, 2, 1),
/* 1931 */    array (2, 2, 2, 1, 1, 2,    1, 1, 2, 1, 2, 1),
              array (2, 2, 2, 1, 2, 1,    2, 1, 1, 2, 1, 2),
 	      array (1, 2, 2, 1, 6, 1,    2, 1, 2, 1, 1, 2),
              array (1, 2, 1, 2, 2, 1,    2, 2, 1, 2, 1, 2),
              array (1, 1, 2, 1, 2, 1,    2, 2, 1, 2, 2, 1),
              array (2, 1, 4, 1, 2, 1,    2, 1, 2, 2, 2, 1),
 	      array (2, 1, 1, 2, 1, 1,    2, 1, 2, 2, 2, 1),
              array (2, 2, 1, 1, 2, 1,    4, 1, 2, 2, 1, 2),
 	      array (2, 2, 1, 1, 2, 1,    1, 2, 1, 2, 1, 2),
              array (2, 2, 1, 2, 1, 2,    1, 1, 2, 1, 2, 1),
/* 1941 */    array (2, 2, 1, 2, 2, 4,    1, 1, 2, 1, 2, 1),
              array (2, 1, 2, 2, 1, 2,    2, 1, 2, 1, 1, 2),
              array (1, 2, 1, 2, 1, 2,    2, 1, 2, 2, 1, 2),
              array (1, 1, 2, 4, 1, 2,    1, 2, 2, 1, 2, 2),
 	      array (1, 1, 2, 1, 1, 2,    1, 2, 2, 2, 1, 2),
              array (2, 1, 1, 2, 1, 1,    2, 1, 2, 2, 1, 2),
              array (2, 5, 1, 2, 1, 1,    2, 1, 2, 1, 2, 2),
              array (2, 1, 2, 1, 2, 1,    1, 2, 1, 2, 1, 2),
 	      array (2, 2, 1, 2, 1, 2,    3, 2, 1, 2, 1, 2),
              array (2, 1, 2, 2, 1, 2,    1, 1, 2, 1, 2, 1),
/* 1951 */    array (2, 1, 2, 2, 1, 2,    1, 2, 1, 2, 1, 2),
              array (1, 2, 1, 2, 4, 2,    1, 2, 1, 2, 1, 2),
              array (1, 2, 1, 1, 2, 2,    1, 2, 2, 1, 2, 2),
              array (1, 1, 2, 1, 1, 2,    1, 2, 2, 1, 2, 2),
              array (2, 1, 4, 1, 1, 2,    1, 2, 1, 2, 2, 2),
              array (1, 2, 1, 2, 1, 1,    2, 1, 2, 1, 2, 2),
 	      array (2, 1, 2, 1, 2, 1,    1, 5, 2, 1, 2, 2),
              array (1, 2, 2, 1, 2, 1,    1, 2, 1, 2, 1, 2),
              array (1, 2, 2, 1, 2, 1,    2, 1, 2, 1, 2, 1),
              array (2, 1, 2, 1, 2, 5,    2, 1, 2, 1, 2, 1),
/* 1961 */    array (2, 1, 2, 1, 2, 1,    2, 2, 1, 2, 1, 2),
 	      array (1, 2, 1, 1, 2, 1,    2, 2, 1, 2, 2, 1),
              array (2, 1, 2, 3, 2, 1,    2, 1, 2, 2, 2, 1),
              array (2, 1, 2, 1, 1, 2,    1, 2, 1, 2, 2, 2),
              array (1, 2, 1, 2, 1, 1,    2, 1, 1, 2, 2, 1),
              array (2, 2, 5, 2, 1, 1,    2, 1, 1, 2, 2, 1),
              array (2, 2, 1, 2, 2, 1,    1, 2, 1, 2, 1, 2),
              array (1, 2, 2, 1, 2, 1,    5, 2, 1, 2, 1, 2),
 	      array (1, 2, 1, 2, 1, 2,    2, 1, 2, 1, 2, 1),
              array (2, 1, 1, 2, 2, 1,    2, 1, 2, 2, 1, 2),
/* 1971 */    array (1, 2, 1, 1, 5, 2,    1, 2, 2, 2, 1, 2),
 	      array (1, 2, 1, 1, 2, 1,    2, 1, 2, 2, 2, 1),
              array (2, 1, 2, 1, 1, 2,    1, 1, 2, 2, 2, 1),
 	      array (2, 2, 1, 5, 1, 2,    1, 1, 2, 2, 1, 2),
              array (2, 2, 1, 2, 1, 1,    2, 1, 1, 2, 1, 2),
              array (2, 2, 1, 2, 1, 2,    1, 5, 2, 1, 1, 2),
              array (2, 1, 2, 2, 1, 2,    1, 2, 1, 2, 1, 1),
              array (2, 2, 1, 2, 1, 2,    2, 1, 2, 1, 2, 1),
              array (2, 1, 1, 2, 1, 6,    1, 2, 2, 1, 2, 1),
              array (2, 1, 1, 2, 1, 2,    1, 2, 2, 1, 2, 2),
/* 1981 */    array (1, 2, 1, 1, 2, 1,    1, 2, 2, 1, 2, 2),
              array (2, 1, 2, 3, 2, 1,    1, 2, 2, 1, 2, 2),
              array (2, 1, 2, 1, 1, 2,    1, 1, 2, 1, 2, 2),
 	      array (2, 1, 2, 2, 1, 1,    2, 1, 1, 5, 2, 2),
              array (1, 2, 2, 1, 2, 1,    2, 1, 1, 2, 1, 2),
 	      array (1, 2, 2, 1, 2, 2,    1, 2, 1, 2, 1, 1),
              array (2, 1, 2, 2, 1, 5,    2, 2, 1, 2, 1, 2),
              array (1, 1, 2, 1, 2, 1,    2, 2, 1, 2, 2, 1),
              array (2, 1, 1, 2, 1, 2,    1, 2, 2, 1, 2, 2),
              array (1, 2, 1, 1, 5, 1,    2, 1, 2, 2, 2, 2),
/* 1991 */    array (1, 2, 1, 1, 2, 1,    1, 2, 1, 2, 2, 2),
 	      array (1, 2, 2, 1, 1, 2,    1, 1, 2, 1, 2, 2),
              array (1, 2, 5, 2, 1, 2,    1, 1, 2, 1, 2, 1),
              array (2, 2, 2, 1, 2, 1,    2, 1, 1, 2, 1, 2),
              array (1, 2, 2, 1, 2, 2,    1, 5, 2, 1, 1, 2),
 	      array (1, 2, 1, 2, 2, 1,    2, 1, 2, 2, 1, 2),
              array (1, 1, 2, 1, 2, 1,    2, 2, 1, 2, 2, 1),
 	      array (2, 1, 1, 2, 3, 2,    2, 1, 2, 2, 2, 1),
              array (2, 1, 1, 2, 1, 1,    2, 1, 2, 2, 2, 1),
              array (2, 2, 1, 1, 2, 1,    1, 2, 1, 2, 2, 1),
/* 2001 */    array (2, 2, 2, 3, 2, 1,    1, 2, 1, 2, 1, 2),
              array (2, 2, 1, 2, 1, 2,    1, 1, 2, 1, 2, 1),
              array (2, 2, 1, 2, 2, 1,    2, 1, 1, 2, 1, 2),
 	      array (1, 5, 2, 2, 1, 2,    1, 2, 2, 1, 1, 2),
              array (1, 2, 1, 2, 1, 2,    2, 1, 2, 2, 1, 2),
              array (1, 1, 2, 1, 2, 1,    5, 2, 2, 1, 2, 2),
              array (1, 1, 2, 1, 1, 2,    1, 2, 2, 2, 1, 2),
 	      array (2, 1, 1, 2, 1, 1,    2, 1, 2, 2, 1, 2),
              array (2, 2, 1, 1, 5, 1,    2, 1, 2, 1, 2, 2),
 	      array (2, 1, 2, 1, 2, 1,    1, 2, 1, 2, 1, 2),
/* 2011 */    array (2, 1, 2, 2, 1, 2,    1, 1, 2, 1, 2, 1),
              array (2, 1, 6, 2, 1, 2,    1, 1, 2, 1, 2, 1),
              array (2, 1, 2, 2, 1, 2,    1, 2, 1, 2, 1, 2),
              array (1, 2, 1, 2, 1, 2,    1, 2, 5, 2, 1, 2),
              array (1, 2, 1, 1, 2, 1,    2, 2, 2, 1, 2, 2),
 	      array (1, 1, 2, 1, 1, 2,    1, 2, 2, 1, 2, 2),
              array (2, 1, 1, 2, 3, 2,    1, 2, 1, 2, 2, 2),
              array (1, 2, 1, 2, 1, 1,    2, 1, 2, 1, 2, 2),
              array (2, 1, 2, 1, 2, 1,    1, 2, 1, 2, 1, 2),
 	      array (2, 1, 2, 5, 2, 1,    1, 2, 1, 2, 1, 2),
/* 2021 */    array (1, 2, 2, 1, 2, 1,    2, 1, 2, 1, 2, 1),
              array (2, 1, 2, 1, 2, 2,    1, 2, 1, 2, 1, 2),
              array (1, 5, 2, 1, 2, 1,    2, 2, 1, 2, 1, 2),
              array (1, 2, 1, 1, 2, 1,    2, 2, 1, 2, 2, 1),
              array (2, 1, 2, 1, 1, 5,    2, 1, 2, 2, 2, 1),
              array (2, 1, 2, 1, 1, 2,    1, 2, 1, 2, 2, 2),
              array (1, 2, 1, 2, 1, 1,    2, 1, 1, 2, 2, 2),
 	      array (1, 2, 2, 1, 5, 1,    2, 1, 1, 2, 2, 1),
              array (2, 2, 1, 2, 2, 1,    1, 2, 1, 1, 2, 2),
              array (1, 2, 1, 2, 2, 1,    2, 1, 2, 1, 2, 1),
/* 2031 */    array (2, 1, 5, 2, 1, 2,    2, 1, 2, 1, 2, 1),
              array (2, 1, 1, 2, 1, 2,    2, 1, 2, 2, 1, 2),
 	      array (1, 2, 1, 1, 2, 1,    5, 2, 2, 2, 1, 2),
              array (1, 2, 1, 1, 2, 1,    2, 1, 2, 2, 2, 1),
              array (2, 1, 2, 1, 1, 2,    1, 1, 2, 2, 1, 2),
	      array (2, 2, 1, 2, 1, 4,    1, 1, 2, 1, 2, 2),
              array (2, 2, 1, 2, 1, 1,    2, 1, 1, 2, 1, 2),
              array (2, 2, 1, 2, 1, 2,    1, 2, 1, 1, 2, 1),
              array (2, 2, 1, 2, 5, 2,    1, 2, 1, 2, 1, 1),
 	      array (2, 1, 2, 2, 1, 2,    2, 1, 2, 1, 2, 1),
/* 2041 */    array (2, 1, 1, 2, 1, 2,    2, 1, 2, 2, 1, 2),
              array (1, 5, 1, 2, 1, 2,    1, 2, 2, 2, 1, 2),
	      array (1, 2, 1, 1, 2, 1,    1, 2, 2, 1, 2, 2)
);

$gan = array ( "갑","을","병","정","무","기","경","신","임","계" );
$jee = array ( "자","축","인","묘","진","사","오","미","신","유","술","해" );
$ddi = array ( "쥐","소","범","토끼","용","뱀","말","양","원숭이","닭","개","돼지" );
$week = array ( "일","월","화","수","목","금","토" );
$md = array ( 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 );


/*
	int lyear, lmonth, lday, leapyes;
	int syear, smonth, sday;
	int mm, y1, y2, m1;
	int i, j, k1, k2, leap, w;
	long td, y;
	lyear = get_year(1841, 2043);
	lmonth = get_month(1, 12);
*/
	$y1 = $lyear - 1841;
	$m1 = $lmonth - 1;
	$leapyes = 0;

/*
	if( $kk[$y1][$m1] > 2)
		do {
			printf("Leap? [Y=1 N=0] : ");
			scanf("%d", &leapyes);
		} while( leapyes < 0 || leapyes > 1 );
*/
	if( $leapyes == 1) {
		switch( $kk[$y1][$m1] ) {
			case 3 :
			case 5 : $mm = 29;
				break;
			case 4 :
			case 6 : $mm = 30;
				break;
		}
	} else {
		switch( $kk[$y1][$m1] ) {
			case 1 :
           		case 3 :
           		case 4 : $mm = 29;
				break;
			case 2 :
			case 5 :
			case 6 : $mm = 30;
				break;
		}
	}
//	$lday = get_day(1, $mm);
	$td = 0;
	for($i=0; $i<$y1; $i++)
		for($j=0; $j<12; $j++)
			switch( $kk[$i][$j] ) {
				case 1 : $td += 29;
					 break;
				case 2 : $td += 30;
					 break;
				case 3 : $td += 58;   /* 29+29 */
					 break;
				case 4 : $td += 59;   /* 29+30 */
				         break;
				case 5 : $td += 59;   /* 30+29 */
					 break;
				case 6 : $td += 60;   /* 30+30 */
					 break;
			}

	for ($j=0; $j<$m1; $j++)
		switch( $kk[$y1][$j] ) {
			case 1 : $td +=29;
				break;
			case 2 : $td += 30;
				break;
			case 3 : $td += 58;   /* 29+29 */
				break;
			case 4 : $td += 59;   /* 29+30 */
				break;
			case 5 : $td += 59;   /* 30+29 */
				break;
			case 6 : $td += 60;   /* 30+30 */
				break;
		}
	if( $leapyes == 1 )
		switch( $kk[$y1][$m1] ) {
			case 3 :
			case 4 : $td += 29;
				break;
			case 5 :
			case 6 : $td += 30;
				break;
		}
	$td += $lday + 22;
	/* td : 1841년 1월 1일부터 원하는 날까지의 전체 날수의 합 */

	$y1 = 1840;
	do {
		$y1++;
		$leap = ($y1 % 400 == 0) || ($y1 % 100 != 0) &&
                         ($y1 % 4 ==0);
		if($leap) $y2 = 366;
		else     $y2 = 365;
		if($td <= $y2) break;
		$td -= $y2;
	} while(1);

	$syear = $y1;
	$md[1] = $y2 - 337;
	$m1 = 0;
	do {
		$m1++;
		if( $td <= $md[$m1-1] ) break;
		$td -= $md[$m1-1];
	} while(1);
	$smonth = $m1;
	$sday = $td;
	$y = $syear - 1;
	$td = $y * 365 + $y/4 - $y/100 + $y/400;
	for($i=0; $i<$smonth-1; $i++) $td += $md[$i];
	$td += $sday;
	$w = $td % 7;

	$i = ($td + 4) % 10;
	$j = ($td + 2) % 12;
	$k1 = ($lyear + 6) % 10;
	$k2 = ($lyear + 8) % 12;

	if ($smonth < 10)	$smonth = '0' . $smonth;
	if ($sday < 10)		$sday = '0' . $sday;

	return $syear . $smonth . $sday;

}

function febdays($y)
{
	$leap = $y%400==0 || $y%100!=0 || $y%4==0;
	if($leap) $md[1] = 29;
	else     $md[1] = 28;
}


##########################################################음력 양력 변환함수 마감 부분




//데이트 운세 함수
//함수 사용예 date_unse(사용자1년,월,일,양력음력여부,남자여자여부,사용자2년,월,일,양력음력여부,보고싶은날년,월,일)
function date_unse($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex,$user2_year,$user2_month,$user2_day,$user2_hour,$user2_calendar,$user2_sex,$target_year,$target_month,$target_day)
{

	if ($user_calendar=='02')	
		{

			$result = lun2sol($user_year, $user_month, $user_day);

			$user_year  = substr($result, 0, 4);
			$user_month = substr($result, 4, 2);
			$user_day   = substr($result, 6, 2);
		}

	if ($user2_calendar=="02")	
		{

			$result = lun2sol($user2_year, $user2_month, $user2_day);

			$user2_year  = substr($result, 0, 4);
			$user2_month = substr($result, 4, 2);
			$user2_day   = substr($result, 6, 2);
		}

	if ($user_sex == 0)	
		{

			$number = substr($user_year,2,2) * 5
		+ $user_month       * 3
		+ $user_day         * 1;
		}
		else	
		{

		$number = substr($user_year,2,2) * 7
		+ $user_month       * 5
		+ $user_day         * 3;
		}

	if ($user2_sex == 0)	
		{

	$number += substr($user2_year,2,2) * 5
		+ $user2_month        * 3
		+ $user2_day          * 1;
		}
		else
		{

	$number += substr($user2_year,2,2) * 7
		+ $user2_month        * 5
		+ $user2_day          * 3;
		}

    $number += ($target_year-2000) * 1
	+ $target_month* 3
	+ $target_day  * 5

;

$number = $number % 12;

$number2 = $number;

if ($number < 10)
	{
		$number2 = '000' . $number;
	}
if ($number < 100 && $number >= 10)
{
	$number2 = '00' . $number;
}
if ($number < 1000 && $number >= 101)
{
	$number2 = '0' . $number;
}
	return $number2;
}



####이구문은 사주 부분이다.
###사주 보는 방법 saju(사용자 년,월,일,시간,양력음력구분);
function saju($user_year,$user_month,$user_day,$user_hour,$user_sex,$user_calendar)
{
if ($user_calendar=="02")
	{
		$result = lun2sol($user_year, $user_month, $user_day);
		$user_year  = substr($result, 0, 4);
		$user_month = substr($result, 4, 2);
		$user_day   = substr($result, 6, 2);
	}

		$number = substr($user_year,2,2) * 5
	+ $user_month * 3
	+ $user_day   * 1
	+ $user_hour  * 7
	+ $user_sex
;

	$number = $number % 60;

	$number2 = $number;

	if ($number < 10)
	{
		$number2 = '00' . $number;
	}
	if ($number < 100 && $number >= 10)
	{
		$number2 = '0' . $number;
	}

	return $number2;
}


###############
##속궁합
##속궁합 보는 방법 deepsex(사용자1년,월,일,시간,양력음력여부,남자여자여부,사용자2년,월,일,시간,양력음력여부)
function deepsex($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex,$user2_year,$user2_month,$user2_day,$user2_hour,$user2_calendar,$user2_sex)
{
	if ($user_calendar=="02")	{
	
		$result = lun2sol($user_year, $user_month, $user_day);	 

	$user_year  = substr($result, 0, 4);
	$user_month = substr($result, 4, 2);
	$user_day   = substr($result, 6, 2);
	}

	if ($user2_calendar=="02")	{

	$result = lun2sol($user2_year, $user2_month, $user2_day);

	$user2_year  = substr($result, 0, 4);
	$user2_month = substr($result, 4, 2);
	$user2_day   = substr($result, 6, 2);
	}


	if ($user_sex == 0)	{

	$number =  substr($user_year,2,2) * 5
		+ $user_month       * 3
		+ $user_day         * 1
		+ $user_hour        * 7;
	}
else	{

	$number =  substr($user_year,2,2) * 1
		+ $user_month       * 3
		+ $user_day         * 5
		+ $user_hour        * 7;
	}

	if ($user2_sex == 0)	{

	$number += substr($user2_year,2,2) * 5
		+ $user2_month        * 3
		+ $user2_day          * 1
		+ $user2_hour         * 7;
	}
	else	{

	$number +=  substr($user2_year,2,2) * 1
		+ $user2_month        * 3
		+ $user2_day          * 5
		+ $user2_hour         * 7;
	}


$number = $number % 12;

$number2 = $number;

if ($number < 10)
	{
	$number2 = '00' . $number;
	}

if ($number < 100 && $number >= 10)
	{
	$number2 = '0' . $number;
	}



return $number2;
}


####################

####오늘의 플라워 운세 보는 함수
####플라워 운세 보는 방법 flower(사용자1년,월,일,시간,양력음력여부,남자여자여부,보고싶은날의년,월,일)
function flower($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex,$target_year,$target_month,$target_day)
{
$calendar = $user_calendar;
	if ($calendar=="02")	{

	$result = lun2sol($user_year, $user_month, $user_day);

	$user_year  = substr($result, 0, 4);
	$user_month = substr($result, 4, 2);
	$user_day   = substr($result, 6, 2);
	}

	$number = substr($user_year,2,2) * 5
	+ $user_month * 3
	+ $user_day   * 1
	+ $user_hour  * 7
	+ $user_sex
	+ ($target_year - 2000) * 1 
	+ $target_month* 3
	+ $target_day  * 5
;

	$number = $number % 72;

	$number2 = $number;

	if ($number < 10)
	{
	$number2 = '00' . $number;
	}
	if ($number < 100 && $number >= 10)
	{
	$number2 = '0' . $number;
	}
	return $number2;
}

#########
###궁합보기 함수
##########
##궁합보기 방번 gunghab(사용자1년,월,일,시간,양력음력여부,남자여자여부,사용자2년,월,일,시간,양력음력여부)
function gunghab($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex,$user2_year,$user2_month,$user2_day,$user2_hour,$user2_calendar,$user2_sex)
{
if ($user_calendar=="02")	{
	
	$result = lun2sol($user_year, $user_month, $user_day);

	$user_year  = substr($result, 0, 4);
	$user_month = substr($result, 4, 2);
	$user_day   = substr($result, 6, 2);
}

if ($user2_calendar=="02")	{

	$result = lun2sol($user2_year, $user2_month, $user2_day);

	$user2_year  = substr($result, 0, 4);
	$user2_month = substr($result, 4, 2);
	$user2_day   = substr($result, 6, 2);
}


if ($user_sex == 0)	{

	$number = substr($user_year,2,2) * 5
		+ $user_month       * 3
		+ $user_day         * 1
		+ $user_hour        * 7;
}
else	{

	$number = substr($user_year,2,2) * 1
		+ $user_month       * 3
		+ $user_day         * 5
		+ $user_hour        * 7;
}

if ($user2_sex == 0)	{

	$number += substr($user_year,2,2) * 5
		+ $user2_month        * 3
		+ $user2_day          * 1
		+ $user2_hour         * 7;
}
else	{

	$number +=substr($user_year,2,2) * 1
		+ $user2_month        * 3
		+ $user2_day          * 5
		+ $user2_hour         * 7;
}


$number = $number % 60;

$number2 = $number;

if ($number < 10)
	{
	$number2 = '00' . $number;
	}

if ($number < 100 && $number >= 10)
	{
	$number2 = '0' . $number;
	}

	return $number2;
}
########



###사랑운세
####사랑운세 보는 방법 love(사용자1년,월,일,시간,양력음력여부,남자여자여부,보고싶은날의년,월,일)
###
function love_unse($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex,$target_year,$target_month,$target_day)
{$calendar = $user_calendar;
	if ($calendar=="02")	{

	$result = lun2sol($user_year, $user_month, $user_day);

	$user_year  = substr($result, 0, 4);
	$user_month = substr($result, 4, 2);
	$user_day   = substr($result, 6, 2);
	}

	$number = substr($user_year,2,2) * 5
	+ $user_month * 3
	+ $user_day   * 1
	+ $user_hour  * 7
	+ ($target_year - 2000) * 1
	+ $target_month* 3
	+ $target_day  * 5
;

	$number = $number % 64;

	$number2 = $number;

	if ($number < 10)
	{
		$number2 = '00' . $number;
	}

	if ($number < 100 && $number >= 10)
	{

		$number2 = '0' . $number;
	}
return $number2;
}



###여기는 일반 운세
##일반운세 보는 방법 unse_nolmal(사용자1년,월,일,시간,양력음력여부,남자여자여부,보고싶은날의년,월,일)
function unse_nolmal($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex,$target_year,$target_month,$target_day)
{$calendar = $user_calendar;
if ($calendar=="02")	{


	
	$result = lun2sol($user_year, $user_month, $user_day);

	$user_year  = substr($result, 0, 4);
	$user_month = substr($result, 4, 2);
	$user_day   = substr($result, 6, 2);
}

$number =substr($user_year,2,2)* 5
	+ $user_month * 3
	+ $user_day   * 1
	+ $user_hour  * 7
	+ $user_sex
	+ ($target_year - 2000) * 1 
	+ $target_month* 3
	+ $target_day  * 5
;

$number = $number % 120;

$number2 = $number;

if ($number < 10)
	{
	$number2 = '00' . $number;
	}

if ($number < 100 && $number >= 10)
	{
	$number2 = '0' . $number;
	}

	return $number2;

}


#####공주의 데이트 운세
###공주 운세 보는 방법  princess_date(사용자1년,월,일,양력음력여부,남자여자여부,사용자2년,월,일,양력음력여부,보고싶은날년,월,일);
function princess_date($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex,$user2_year,$user2_month,$user2_day,$user2_hour,$user2_calendar,$user2_sex,$target_year,$target_month,$target_day)
{
if ($user_calendar=="02")	{
	
	$result = lun2sol($user_year, $user_month, $user_day);

	$user_year  = substr($result, 0, 4);
	$user_month = substr($result, 4, 2);
	$user_day   = substr($result, 6, 2);
}

if ($user2_calendar=="02")	{

	$result = lun2sol($user2_year, $user2_month, $user2_day);

	$user2_year  = substr($result, 0, 4);
	$user2_month = substr($result, 4, 2);
	$user2_day   = substr($result, 6, 2);
}

if ($user_sex == 2)	{

	$number = substr($user_year,2,2) * 5
		+ $user_month       * 3
		+ $user_day         * 1;
}
else	{

	$number =substr($user_year,2,2) * 7
		+ $user_month       * 5
		+ $user_day         * 3;
}

if ($user2_sex == 2)	{

	$number += substr($user2_year,2,2) * 5
		+ $user2_month        * 3
		+ $user2_day          * 1;
}
else	{

	$number += substr($user2_year,2,2) * 7
		+ $user2_month        * 5
		+ $user2_day          * 3;
}


$number += ($target_year-2000) * 1
	+ $target_month* 3
	+ $target_day  * 5
;

$number = $number % 30;

$number2 = $number;

if ($number < 10)
	{
		$number2 = '000' . $number;
	}
if ($number < 100 && $number >= 10)
	{
	$number2 = '00' . $number;
	}
if ($number < 1000 && $number >= 101)
	{
	$number2 = '0' . $number;
	}

	return $number2;

}


###여기는공주의 사랑 운세
##일반공주의 사랑 보는 방법 princess_love사용자1년,월,일,시간,양력음력여부,남자여자여부,보고싶은날의년,월,일)
function princess_love($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex,$target_year,$target_month,$target_day)
{$calendar = $user_calendar;
if ($calendar=="02")	{


	
	$result = lun2sol($user_year, $user_month, $user_day);

	$user_year  = substr($result, 0, 4);
	$user_month = substr($result, 4, 2);
	$user_day   = substr($result, 6, 2);
}

$number =substr($user_year,2,2)* 5
	+ $user_month * 3
	+ $user_day   * 1
	+ $user_hour  * 7
	+ $user_sex
	+ ($target_year - 2000) * 1 
	+ $target_month* 3
	+ $target_day  * 5
;

$number = str_replace("-","",$number);

$number = $number % 12;

$number2 = $number;


if ($number < 10)
	{
	$number2 = '00' . (integer)$number;
	}

if ($number < 100 && $number >= 10)
	{
	$number2 = '0' . (integer)$number;
	}

	return $number2;

}
##################
###여기는공주의 일반 운세
##일반공주 보는 방법 princess_nolmal(사용자1년,월,일,시간,양력음력여부,남자여자여부,보고싶은날의년,월,일)
function princess_nolmal($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex,$target_year,$target_month,$target_day)
{
	$calendar = $user_calendar;
if ($calendar=="02")	{


	
	$result = lun2sol($user_year, $user_month, $user_day);

	$user_year  = substr($result, 0, 4);
	$user_month = substr($result, 4, 2);
	$user_day   = substr($result, 6, 2);
}

$number =substr($user_year,2,2)* 5
	+ $user_month * 3
	+ $user_day   * 1
	+ $user_hour  * 7
	+ $user_sex
	+ ($target_year - 2000) * 1 
	+ $target_month* 3
	+ $target_day  * 5
;

$number = $number % 60;

$number2 = $number;

if ($number < 10)
	{
	$number2 = '00' . $number;
	}

if ($number < 100 && $number >= 10)
	{
	$number2 = '0' . $number;
	}

	return $number2;

}
##################

##########################

###별점 운세
##별점 운세 보는 방법 star_unse(사용자1년,월,일,시간,양력음력여부);
##리턴값이.. 배열로 넘어 온다.$number3[0] = 플레이될값;$number3[1] = 별자리;
function star_unse($user_year,$user_month,$user_day,$user_hour,$user_calendar)
{
if ($user_calendar=="02")	{



	$result = lun2sol($user_year, $user_month, $user_day);

	$user_year  = substr($result, 0, 4);
	$user_month = substr($result, 4, 2);
	$user_day   = substr($result, 6, 2);
}

$number = $user_month * 100
	+ $user_day
;

if ( 321 <= $number && $number <  420)	{	$number2 = '001';	$star_name = '양';	}
if ( 420 <= $number && $number <  521)	{	$number2 = '002';	$star_name = '황소';	}
if ( 521 <= $number && $number <  622)	{	$number2 = '003';	$star_name = '쌍둥이';	}
if ( 622 <= $number && $number <  723)	{	$number2 = '004';	$star_name = '게';	}
if ( 723 <= $number && $number <  823)	{	$number2 = '005';	$star_name = '사자';	}
if ( 823 <= $number && $number <  923)	{	$number2 = '006';	$star_name = '처녀';	}
if ( 923 <= $number && $number < 1024)	{	$number2 = '007';	$star_name = '천평';	}
if (1024 <= $number && $number < 1123)	{	$number2 = '008';	$star_name = '전갈';	}
if (1123 <= $number && $number < 1222)	{	$number2 = '009';	$star_name = '궁수';	}
if (1222 <= $number || $number <  120)	{	$number2 = '010';	$star_name = '염소';	}
if ( 120 <= $number && $number <  219)	{	$number2 = '011';	$star_name = '물병';	}
if ( 219 <= $number && $number <  321)	{	$number2 = '012';	$star_name = '물고기';	}


$number3[0] = $number2;
$number3[1] = $star_name;


return $number3;
}



##별자리와 혈액형으로 보는 사랑심리
##별자리와 혈액형으로 보는 사랑심리 보는 방법 star_boold(사용자1년,월,일,시간,양력음력여부);
##리턴값이.. 배열로 넘어 온다.$number3[0] = 플레이될값;$number3[1] = 별자리;


function star_blood($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex,$user_blood)
{
	$calendar = $user_calendar;
	if ($calendar=="02")	{
		$result = lun2sol($user_year, $user_month, $user_day);
		$user_year  = substr($result, 0, 4);
		$user_month = substr($result, 4, 2);
		$user_day   = substr($result, 6, 2);
	}

	$number = $user_month * 100	+ $user_day;
	//echo $number.":첫수자<br>";

	if ( 321 <= $number && $number <  420)	{	$number2 = '01';	$star_name = '양';	}
	if ( 420 <= $number && $number <  521)	{	$number2 = '02';	$star_name = '황소';	}
	if ( 521 <= $number && $number <  622)	{	$number2 = '03';	$star_name = '쌍둥이';	}
	if ( 622 <= $number && $number <  723)	{	$number2 = '04';	$star_name = '게';	}
	if ( 723 <= $number && $number <  823)	{	$number2 = '05';	$star_name = '사자';	}
	if ( 823 <= $number && $number <  923)	{	$number2 = '06';	$star_name = '처녀';	}
	if ( 923 <= $number && $number < 1024)	{	$number2 = '07';	$star_name = '천평';	}
	if (1024 <= $number && $number < 1123)	{	$number2 = '08';	$star_name = '전갈';	}
	if (1123 <= $number && $number < 1222)	{	$number2 = '09';	$star_name = '궁수';	}
	if (1222 <= $number || $number <  120)	{	$number2 = '10';	$star_name = '염소';	}
	if ( 120 <= $number && $number <  219)	{	$number2 = '11';	$star_name = '물병';	}
	if ( 219 <= $number && $number <  321)	{	$number2 = '12';	$star_name = '물고기';	}

//echo $number2.":두번째수자<br>";
//echo $star_name.":별자리<br>";

	if ($user_sex == '2')	{			
		$number2 .= '1';	
	}else{
		$number2 .= '2';	
	}
//echo $number2.":성별을 넣은 두번째 수자<br>";
//echo $number;

$user_blood = strtolower($user_blood);

if ($user_blood == 'a')		{	$number2 .= '1';	}
if ($user_blood == 'o')		{	$number2 .= '2';	}
if ($user_blood == 'b')		{	$number2 .= '3';	}
if ($user_blood == 'ab')	{	$number2 .= '4';	}


if ($number2 == '0412')		{$number2 = '0714';}

//echo $number2.":혈액형을 넣은 두번째 수자<br>";

//echo "<br>".$number2; exit;
//echo $user_blood;

$number3[0] = (strlen($number2)==4?$number2 : "0".$number2 );
$number3[1] = $star_name;

//echo '----'.$number3[0]; exit;
return $number3;
}


###########토종비결
##토종비결 보는 방법 tojong(사용자1년,월,일,시간,양력음력여부,남자여자여부)
function tojong($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex,$target_year)
{$calendar = $user_calendar;
if ($calendar=="02")	{


	
	$result = lun2sol($user_year, $user_month, $user_day);

	$user_year  = substr($result, 0, 4);
	$user_month = substr($result, 4, 2);
	$user_day   = substr($result, 6, 2);
}

$number =substr($user_year,2,2)* 5
	+ $user_month * 3
	+ $user_day   * 1
	+ $user_hour  * 7
	+ $user_sex
	+ $target_year
	;

$number = $number % 72 ;

$number2 = $number;

if ($number < 10)
	{
	$number2 = '00' . $number;
	}

if ($number < 100 && $number >= 10)
	{
	$number2 = '0' . $number;
	}

	return $number2;

}

#######################
###엽기운세
###엽기 운세 보는 방법 yupgi_unse(사용자1년,월,일,시간,양력음력여부,남자여자여부,보고싶은날의년,월,일)
function yupgi_unse($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex,$target_year,$target_month,$target_day)
{$calendar = $user_calendar;
	if ($calendar=="02")	{

	$result = lun2sol($user_year, $user_month, $user_day);
	$user_year  = substr($result, 0, 4);
	$user_month = substr($result, 4, 2);
	$user_day   = substr($result, 6, 2);
}

$number = substr($user_year,2,2) * 5
	+ $user_month * 3
	+ $user_day   * 1
	+ $user_hour  * 7
	+ $user_sex
	+ ($target_year - 2000) * 1 
	+ $target_month* 3
	+ $target_day  * 5
;

$number = $number % 30;

$number2 = $number;

if ($number < 10)
	{
	$number2 = '00' . $number;
	}

if ($number < 100 && $number >= 10)
	{
	$number2 = '0' . $number;
	}

	return $number2;
}
###엽기도사와레저
###엽기 운세 보는 방법 yupgi_unse(사용자1년,월,일,시간,양력음력여부,남자여자여부,보고싶은날의년,월,일)
function yupgi1_unse($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex,$target_year,$target_month,$target_day)
{$calendar = $user_calendar;
	if ($calendar=="02")	{

	$result = lun2sol($user_year, $user_month, $user_day);
	$user_year  = substr($result, 0, 4);
	$user_month = substr($result, 4, 2);
	$user_day   = substr($result, 6, 2);
}

$number = substr($user_year,2,2) * 5
	+ $user_month * 3
	+ $user_day   * 1
	+ $user_hour  * 7
	+ $user_sex
	+ ($target_year - 2000) * 1 
	+ $target_month* 3
	+ $target_day  * 5
;

$number = $number % 12;

$number2 = $number;

if ($number < 10)
	{
	$number2 = '00' . $number;
	}

if ($number < 100 && $number >= 10)
	{
	$number2 = '0' . $number;
	}

	return $number2;
}


###탄생화 꽃점 보기
function birth_flower($user_year,$user_month,$user_day,$user_hour,$user_calendar)
{
	$calendar = $user_calendar;
	if ($calendar=="02")	{

	$result = lun2sol($user_year, $user_month, $user_day);
	$user_year  = substr($result, 0, 4);
	$user_month = substr($result, 4, 2);
	$user_day   = substr($result, 6, 2);
}

$iii =  strlen($user_day);
$iii1 =  strlen($user_month);


if ($iii < 2)
	{
	$user_day = '0' . $user_day;
	}
if ($iii1 < 2)
	{
	$user_month = '0' . $user_month;
	}

$number = $user_month.$user_day;



	return $number;
}




#############이부분은 주간 운세 보기 메뉴이다.
function jugan_unse($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex,$target_year,$target_month,$target_day)
{$calendar = $user_calendar;
if ($calendar=="02")	{


	
	$result = lun2sol($user_year, $user_month, $user_day);

	$user_year  = substr($result, 0, 4);
	$user_month = substr($result, 4, 2);
	$user_day   = substr($result, 6, 2);
}

$number =substr($user_year,2,2)* 5
	+ $user_month * 3
	+ $user_day   * 1
	+ $user_hour  * 7
	+ $user_sex
	+ ($target_year - 2000) * 1 
	+ $target_month* 3
	+ $target_day  * 5
;

$number = $number % 36;

$number2 = $number;

if ($number < 10)
	{
	$number2 = '00' . $number;
	}

if ($number < 100 && $number >= 10)
	{
	$number2 = '0' . $number;
	}

	return $number2;

}

###############평생사주
function life_all($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex)
{$calendar = $user_calendar;
if ($calendar=="02")	{


	
	$result = lun2sol($user_year, $user_month, $user_day);

	$user_year  = substr($result, 0, 4);
	$user_month = substr($result, 4, 2);
	$user_day   = substr($result, 6, 2);
}

$number =substr($user_year,2,2)* 5
	+ $user_month * 3
	+ $user_day   * 1
	+ $user_hour  * 7
	+ $user_sex
	;

$number = $number % 60;

$number2 = $number;

if ($number < 10)
	{
	$number2 = '00' . $number;
	}

if ($number < 100 && $number >= 10)
	{
	$number2 = '0' . $number;
	}

	return $number2;

}

###오늘 까지 데이트 날짜 구하는 함수 
function princess_date_date($user3_year,$user3_month,$user3_day,$target_year,$target_month,$target_day)
{
	$user3_hour =1;
$user3_min =1;
$user3_se =1;
$user3_month= $user3_month*1;
$user3_day= $user3_day*1;
$user3_year= $user3_year*1;

	$okik = mktime($user3_hour,$user3_min,$user3_se,$target_month,$target_day,$target_year);
	//$okik = time();

	$exp=mktime($user3_hour,$user3_min,$user3_se,$user3_month,$user3_day,$user3_year);

//	echo $user3_hour."-".$user3_min."-".$user3_se."-".$user3_month."-".$user3_day."-".$user3_year;

	$ujiuuj = $okik - $exp;

	$ujiuuj = ($ujiuuj / 86400)+1;
	$ujiuuj  = round($ujiuuj);


		if(strlen($ujiuuj) == 5)
		{
			$uo =  substr($ujiuuj,0,1);
			if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			$yuyhygi .= count_check_1(4);
			}
			
			$uo =  substr($ujiuuj,1,1);
				if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			$yuyhygi .= count_check_1(3);
			}
			
			$uo =  substr($ujiuuj,2,1);
				if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			$yuyhygi .= count_check_1(2);
			}
			
			$uo =  substr($ujiuuj,3,1);
				if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			$yuyhygi .= count_check_1(1);
			}
			
			$uo =  substr($ujiuuj,4,1);
				if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			}
			
		}

		if(strlen($ujiuuj) == 6)
		{
			$uo =  substr($ujiuuj,0,1);
				if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			$yuyhygi .= count_check_1(5);
			}
			
			$uo =  substr($ujiuuj,1,1);
				if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			$yuyhygi .= count_check_1(4);
			}
			
			$uo =  substr($ujiuuj,2,1);
				if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			$yuyhygi .= count_check_1(3);
			}
			
			$uo =  substr($ujiuuj,3,1);
				if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			$yuyhygi .= count_check_1(2);
			}
			
			$uo =  substr($ujiuuj,4,1);
				if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			$yuyhygi .= count_check_1(1);
			}

			$uo =  substr($ujiuuj,5,1);
				if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			}
		}

		if(strlen($ujiuuj) == 4)
		{
			$uo =  substr($ujiuuj,0,1);
				if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			$yuyhygi .= count_check_1(3);
			}
	
			$uo =  substr($ujiuuj,1,1);
				if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			$yuyhygi .= count_check_1(2);
			}
	
			$uo =  substr($ujiuuj,2,1);
				if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			$yuyhygi .= count_check_1(1);
			}

			$uo =  substr($ujiuuj,3,1);
				if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			}


		}

		if(strlen($ujiuuj) == 3)
		{
			
			$uo =  substr($ujiuuj,0,1);
				if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			$yuyhygi .= count_check_1(2);
			}


			
			$uo =  substr($ujiuuj,1,1);
				if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			$yuyhygi .= count_check_1(1);
			}

			$uo =  substr($ujiuuj,2,1);
				if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			}
		}
		if(strlen($ujiuuj) == 2)
		{
			$uo =  substr($ujiuuj,0,1);
				if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			$yuyhygi .= count_check_1(1);
			}
			$uo =  substr($ujiuuj,1,1);
				if($uo != 0)
			{
			$yuyhygi .= count_check($uo);
			}
		}
		if(strlen($ujiuuj) == 1)
		{
			$uo =  substr($ujiuuj,0,1);
			$yuyhygi .= count_check($uo);
		}


		

	$yuyhygi =trim($yuyhygi);
	$yuyhygi = str_replace(" ","",$yuyhygi);

	####글자를 다시 원상 복귀 해야 한다. 
	//echo $yuyhygi;
return $yuyhygi;
}
function count_check_1($coun)
	{
				switch ($coun)
				{
					case 1 :$content_ok_oi1 = "십"; 
					break;
					case 2 :$content_ok_oi1 = "백"; 
					break;
					case 3 :$content_ok_oi1 = "천"; 
					break;
						case 4 :$content_ok_oi1 = "만";
					break;
					case 5 :$content_ok_oi1 = "십만";
					break;
				}
			return $content_ok_oi1;
	}

	function count_check($uyuy)
	{
			switch($uyuy)
			{
					case 1 : $content_ok_oi = "일";
				break;
				case 2 : $content_ok_oi = "이";
				break;
					case 3 : $content_ok_oi = "삼";
				break;
				case 4 : $content_ok_oi = "사";
				break;
				case 5 : $content_ok_oi = "오";
				break;
				case 6 : $content_ok_oi = "육";
				break;
				case 7 : $content_ok_oi = "칠";
				break;
				case 8 : $content_ok_oi = "팔";
				break;
				case 9 : $content_ok_oi = "구";
				break;
							
			}

	return $content_ok_oi;
	}




###월간 운세

function month_unse($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex,$target_year,$target_month)
{$calendar = $user_calendar;
if ($calendar=="02")	{


	
	$result = lun2sol($user_year, $user_month, $user_day);

	$user_year  = substr($result, 0, 4);
	$user_month = substr($result, 4, 2);
	$user_day   = substr($result, 6, 2);
}

$number =substr($user_year,2,2)* 5
	+ $user_month * 3
	+ $user_day   * 1
	+ $user_hour  * 7
	+ $user_sex
	+ ($target_year - 2000) * 1 
	+ $target_month* 3
	
;

$number = $number % 71;

$number2 = $number;

if ($number < 10)
	{
	$number2 = '00' . $number;
	}

if ($number < 100 && $number >= 10)
	{
	$number2 = '0' . $number;
	}

	return $number2;

}

function lotto_unse($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex,$target_year,$target_month)
{
	$calendar = $user_calendar;
	$calendar = $user_calendar;
	
	if ($calendar=="02")	{

		$result = lun2sol($user_year, $user_month, $user_day);

		$user_year  = substr($result, 0, 4);
		$user_month = substr($result, 4, 2);
		$user_day   = substr($result, 6, 2);
	}

	$target1_year = $target_year;
	$target1_month = $target_month;



	for($i = $target_day;$i > 0;$i--)
	{

		$ll = getdate(mktime($user3_hour,$user3_min,$user3_se,$target_month,$i,$target_year));
		
		if($ll[wday] == 1)
		{
			$day_ok = $i;
			break;
		}
		

	}


	if(!$day_ok)
	{
		$target_day = 31;
		$target_month = $target_month -1;
		if($target_month < 1)
		{
			$target_year = $target_year -1;
			$target_month = 12;
		}
	}


	for($i = $target_day;$i > 0;$i--)
	{

		$ll = getdate(mktime($user3_hour,$user3_min,$user3_se,$target_month,$i,$target_year));
		
		if($ll[wday] == 1)
		{
			$day_ok = $i;
			break;
		}
		

	}

	$target_day = $day_ok;
	if (date(w) == 0){
			$target_day = date(W) + 1;
		} else {
			$target_day = date(W);
		}
	$number =substr($user_year,2,2)* 5
		+ $user_month * 3
		+ $user_day   * 1
		+ $user_hour  * 7
		+ $user_sex
		+ ($target1_year - 2000) * 1 
		+ $target1_month* 3
		+ $target_day  * 5
	;

	$number = $number % 12;

	$number2 = $number;

	$number = round($number);

	if ($number < 10)
	{
	$number2 = '00' . $number;
	}

	if ($number < 100 && $number >= 10)
	{
	$number2 = '0' . $number;
	}

	return $number2;

}



function game_unse($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex,$target_year,$target_month,$target_day)
{

	$calendar = $user_calendar;
	
	if ($calendar=="02")	{
		$result = lun2sol($user_year, $user_month, $user_day);
		$user_year  = substr($result, 0, 4);
		$user_month = substr($result, 4, 2);
		$user_day   = substr($result, 6, 2);
	}

//echo "데이타";
//echo $user_year."<br>".$user_month;
//echo "<br>".$user_day."<br>".$user_hour."<br>".$user_calendar."<br>".$user_sex."<br>".$target_year."<br>".$target_month."<br>".$target_day."<br>";



	$number =substr($user_year,2,2) * 5
		+ $user_month * 3
		+ $user_day   * 1
		+ $user_hour  * 7
		+ $user_sex
		+ ($target_year - 2000) * 1 
		+ $target_month* 3
		+ $target_day  * 5
	;


$number = $number % 12 ;

$number2 = $number;

if ($number < 10)
	{
	$number2 = '00' . $number;
	}

if ($number < 100 && $number >= 10)
	{
	$number2 = '0' . $number;
	}

	return $number2;

}




function horse_unse($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex,$target_year,$target_month,$target_day,$lll)
{$calendar = $user_calendar;
if ($calendar=="02")	{


	
	$result = lun2sol($user_year, $user_month, $user_day);

	$user_year  = substr($result, 0, 4);
	$user_month = substr($result, 4, 2);
	$user_day   = substr($result, 6, 2);
}

$target1_year = $target_year;
$target1_month = $target_month;



for($i = $target_day;$i > 0;$i--)
{

	$ll = getdate(mktime($user3_hour,$user3_min,$user3_se,$target_month,$i,$target_year));
	
	if($ll[wday] == 1)
	{
		$day_ok = $i;
		break;
	}
	

}


if(!$day_ok)
{
	$target_day = 31;
	$target_month = $target_month -1;
	if($target_month < 1)
	{
		$target_year = $target_year -1;
		$target_month = 12;
	}
}


for($i = $target_day;$i > 0;$i--)
{

	$ll = getdate(mktime($user3_hour,$user3_min,$user3_se,$target_month,$i,$target_year));
	
	if($ll[wday] == 1)
	{
		$day_ok = $i;
		break;
	}
	

}

$target_day = $day_ok;
$number =substr($user_year,2,2)* 5
	+ $user_month * 3
	+ $user_day   * 1
	+ $user_hour  * 7
	+ $user_sex
	+ ($target1_year - 2000) * 1 
	+ $target1_month* 3
	+ ($target_day+$lll)  * 5
;

$number = $number % 12;

$number2 = $number;

if ($number < 10)
	{
	$number2 = '00' . $number;
	}

if ($number < 100 && $number >= 10)
	{
	$number2 = '0' . $number;
	}

	return $number2;

}





##################
###여기는 성인섹스의 일반 운세
##일반섹스 보는 방법 sex_nolmal(사용자1년,월,일,시간,양력음력여부,남자여자여부,보고싶은날의년,월,일)
function sex_normal($user_year,$user_month,$user_day,$user_hour,$user_calendar,$user_sex,$target_year,$target_month,$target_day)
{
	$calendar = $user_calendar;
if ($calendar=="02")	{


	
	$result = lun2sol($user_year, $user_month, $user_day);

	$user_year  = substr($result, 0, 4);
	$user_month = substr($result, 4, 2);
	$user_day   = substr($result, 6, 2);
}

$number =substr($user_year,2,2)* 5
	+ $user_month * 3
	+ $user_day   * 1
	+ $user_hour  * 7
	+ $user_sex
	+ ($target_year - 2000) * 1 
	+ $target_month* 3
	+ $target_day  * 5
;

$number = $number % 12;

$number2 = $number;

if ($number < 10)
	{
	$number2 = '00' . $number;
	}

if ($number < 100 && $number >= 10)
	{
	$number2 = '0' . $number;
	}

	return $number2;

}
##################


function type_val($num)
{
	
switch($num)
{
	#######레저 운세########
	case 1039 : 
		$unse_infomation[ga]=0; 
		$unse_infomation[title]="레저운세"; 
		$unse_infomation[in_check] = 7;
	break;
	#############
	####### 오늘의 사랑의 명언 ########
	/*
	case 1002 : 
		$unse_infomation[ga]=0; 
		$unse_infomation[title]="오늘의 사랑의 명언"; 
	break;
	#############
	*/

	####### 정통운세 ########
	/*
	case 1003 : 
		$unse_infomation[ga]=0; 
		$unse_infomation[title]="정통운세"; 
	break;
	*/
	#############
	####### 월간 운세 ########
	case 1004 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="월간운세"; 
		$unse_infomaition[in_check] = 8;
	break;
	#############
	####### 주간 운세 ########
	case 1005 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="주간운세"; 
		$unse_infomation[in_check] = 7;
	break;
	#############
	####### 오늘의 운세########
	case 1006 : 
		$unse_infomation[ga]=0; 
		$unse_infomation[title]="오늘의 운세";
		$unse_infomation[in_check] = 7;
	break;
	#############
	####### 별자리와 혈액형 운세 ########
	case 1008 : 
		$unse_infomation[ga]=0; 
		$unse_infomation[title]="별자리와 형액형 운세"; 
		$unse_infomation[in_check] = 6;
	break;
	#############
	####### 스타의 오늘의운세 ########
	case 1009 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="스타의 오늘의 운세"; 
	break;
	#############
	####### 공주의 오늘의 운세 ########
	case 1010 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="공주의 오늘의 운세"; 
		$unse_infomation[in_check] = 7;
	break;
	#############
	####### 플라워 운세 ########
	case 1011 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="플라워 운세"; 
		$unse_infomation[in_check] = 7;
	break;
	#############
	#######	오늘의ㅡ 데이트 운세 ########
	case 1012 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="오늘의 데이트 운세"; 
		$unse_infomation[in_check] = 4;
	break;
	#############
	#######혈액형 커플링 운세 ########
	case 1014 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="형액형 커플링 운세"; 
		$unse_infomation[in_check] = 99;
	break;
	#############
	####### 경마 운세########
	case 1015 : 
		//$unse_infomation[ga]=3000;
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="경마 운세"; 
		$unse_infomation[in_check] = 11;
	break;
	#############
	####### 로또운세 ########
	case 1016 : 
		//$unse_infomation[ga]=3000;
		$unse_infomation[ga]=3000;  
		$unse_infomation[title]="로또운세"; 
		$unse_infomation[in_check] = 1;
	break;
	#############
	#######  경정 운세########
	case 1017 : 
		//$unse_infomation[ga]=3000;
		$unse_infomation[ga]=3000;  
		$unse_infomation[title]="경정 운세"; 
	break;
	#############
	####### 경륜운세 ########
	case 1018 : 
		//$unse_infomation[ga]=3000;
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="경륜 운세"; 
	break;
	#############
	####### 게임운세 ########
	case 1088 : 
		//$unse_infomation[ga]=3000;
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="게임 운세"; 
		$unse_infomation[in_check] = 7;
	break;
	#############
	####### 고스톱운세 ########
	case 1041 : 
		//$unse_infomation[ga]=3000;
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="고스톱 운세"; 
		$unse_infomation[in_check]= 7;
	break;
	#############
	#######  별자리 운세########
	case 1098 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="별자리운세"; 
		$unse_infomation[in_check] = 1;
	break;
	#############
	####### 탄생화 운세보기########
	case 1020 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="탄생화 운세보기"; 
		$unse_infomation[in_check] = 1;
	break;
	#############
	####### 토종비결########
	case 1024 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="토정비결"; 
		$unse_infomation[in_check] = 9;
	break;
	#############
	####### 사주운세########
	case 1021 : 
		$unse_infomation[ga]=500; 
		$unse_infomation[title]="사주운세"; 
		$unse_infomation[in_check] =1;
	break;
	#############
	####### 공주의 데이트 운세 ########
	case 1023 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="공주의 데이트 운세"; 
		$unse_infomation[in_check] = 4;
	break;
	#############
	####### 공주의 사랑운세########
	case 1090 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="공주의 사랑운세"; 
$unse_infomation[in_check] = 7;
	break;
	#############
	####### 오늘의 사랑 운세 ########
	case 1026 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="오늘의 사랑 운세"; 
		$unse_infomation[in_check] = 10;
	break;
	#############
	####### 궁합 ########
	case 1027 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="궁합"; 
		$unse_infomation[in_check] = 2;
	break;
	#############
	#######  속궁합########
	case 1028 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="속궁합"; 
		$unse_infomation[in_check] = 2;
	break;
	#############
	####### 스타와의 궁합 ########
	case 1029 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="스타와의 궁합"; 
	break;
	#############
	####### 스타의 토종비결########
	case 1030 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="스타의 토종비결"; 
	break;
	#############
	#######스타와의 속궁합########
	case 1031 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="스타와의 속궁합"; 
	break;
	#############
	####### 스타의 사랑운세 ########
	case 1032 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="스타의 사랑운세"; 
	break;
	#############
	####### 스타와의 데이트 운세 ########
	case 1033 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="스타와의 데이트 운세"; 
	break;
	#############
	####### 스타의 평생사주 ########
	case 1034 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="스타의 평생사주"; 
	break;
	#############
	####### 스타의 플라워 운세 ########
	case 1035 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="스타의 플라워 운세"; 
	break;
	#############
	####### 스타의 엽기 운세########
	case 1036 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="스타의 엽기 운세"; 
	break;
	#############
	####### 오늘의 엽기 운세########
	case 1037 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="오늘의 엽기 운세"; 
		$unse_infomation[in_check] = 7;

	break;
	#############
	#######엽기도사 luck's ########
	
	case 1038 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="엽기도사 luck`s"; 
		$unse_infomation[in_check] = 7;
	break;
#############

	#############
	#######꿈풀이 ########
	
	case 1040 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="꿈풀이"; 
	break;

	####### 왕자의 오늘의 운세 ########
	case 1070 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="왕자의 오늘의 운세"; 
		$unse_infomation[in_check] = 7;
	break;
	#############	

	####### 왕자의 데이트 운세 ########
	case 1071 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="왕자의 데이트 운세"; 
		$unse_infomation[in_check] = 4;
	break;
	#############
	####### 왕자의 사랑운세########
	case 1072 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="왕자의 사랑운세"; 
        $unse_infomation[in_check] = 7;
	break;
	#############



	#######오늘 여성 섹스운세########
	case 1061 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="오늘 여성  섹스운세"; 
		$unse_infomation[in_check] = 7;
	break;
	#############
	#######오늘 남성 섹스운세########
	case 1062 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="오늘 남성 섹스운세"; 
		$unse_infomation[in_check] = 7;
	break;
	#############
	#######오늘 여성 섹스만족도########
	case 1063 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="오늘 여성  섹스만족도"; 
		$unse_infomation[in_check] = 7;
	break;
	#############
	#######오늘 남성 섹스만족도########
	case 1064 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="오늘 남성 섹스만족도"; 
		$unse_infomation[in_check] = 7;
	break;
	#############

	####### 첫 데이트 운세 ########
	case 1065 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="첫 데이트 궁합"; 
		$unse_infomation[in_check] = 4;
	break;

	#######첫  섹스만족도########
	case 1066 : 
		$unse_infomation[ga]=3000; 
		$unse_infomation[title]="첫 섹스만족도"; 
		$unse_infomation[in_check] = 2;
	break;
	#############
	#############
	default :
		$unse_infomation[in_check] = 100;
	

}

return $unse_infomation;
 
}






function ramdum($file_name)
{
	
	$check = count($file_name);
	for($o =0 ;$o <$check; $o= $o+2)
		{
		

			$cont_ok_location = "../data/ment/";//여기다. 이름 부분의 위치를 적어 준다. 
			$cont_ok_location_1= ".wma";
			$file_name[$o] = trim($file_name[$o]);

			$file_name[$o+1] = trim($file_name[$o+1]);

			$file_name[$o+1] = str_replace(" ","",$file_name[$o+1]);//문자안에 들어가는 공백 제거..

			if($file_name[$o] == "(date_ok)")
				{
					
					$len_check = strlen($file_name[$o+1]);
			
					for($q=0;$q<$len_check;$q = $q+2)
						{

							$pl_count = count($play_content);

							if($pl_count == 0 or $pl_count == NULL)
								{
									
									$cont_ok = substr($file_name[$o+1],$q,2);
									#################################################


								if(($file_name[$o+1]+1) < date(ymd))
								{
									echo "<script language=javascript>alert('입력하신 년월일이 잘못 되었습니다. ');history.back();</script>";
								}
								else
								{
									if($q == 0)
									{
										
											if($cont_ok > 0 and $cont_ok < 43)
										{
											$cont_ok =$cont_ok;
										}
										else
										{
											echo "<script language=javascript>alert('입력하신 년도가 잘못 되었습니다.');history.back();</script>";
											exit;
										}
									}
									else if($q ==2)
									{
											
											if($cont_ok < 13 and $cont_ok > 0)
										{
											$cont_ok ="mon".$cont_ok;
										}
										else
										{
											echo "<script language=javascript>alert('입력하신 월이 잘못 되었습니다.');history.back();</script>";
											exit;
										}
									}
									else if($q ==4)
									{
											if($cont_ok < 32 and $cont_ok > 0)
										{
												$count_check_count = count($cont_ok);
											if($count_check_count == 1)
											{
												$cont_ok = "0".$cont_ok; 		
											}
											$cont_ok ="DAY".$cont_ok;
										}
										else
										{
											echo "<script language=javascript>alert('입력하신 일이 잘못 되었습니다.');history.back();</script>";
											exit;
										}
									}

									$play_content[0] = $cont_ok_location.$cont_ok.$cont_ok_location_1;

									$play_content[0]  = trim($play_content[0]); // 예상치 못한 결과값이 나올 우려가있어서.. 공백문자는 제가 한다. 

									$play_content[0] = str_replace(" ","",$play_content[0]); //문자안에 들어가는 공백 제거.
								}
								}
################################################################################################################

								else
								{
										$cont_ok = substr($file_name[$o+1],$q,2);
############################################################################################################
	if(($file_name[$o+1]+1) < (int)date(ymd))
								{
									echo "<script language=javascript>alert('입력하신 년월일이 잘못 되었습니다.!!!! ');history.back();</script>";
									exit;
								}
								else
								{
										if($q ==0)
									{
											if($cont_ok > 0 and $cont_ok < 43)
										{
											$cont_ok =$cont_ok;
										}
										else
										{
											echo "<script language=javascript>alert('입력하신 년도가 잘못 되었습니다.');history.back();</script>";
											exit;
										}
									}
									else if($q ==2)
									{
											if($cont_ok < 13 and $cont_ok > 0)
										{
											if(strlen($cont_ok) < 2)
											{
												$cont_ok ="mon0".$cont_ok;
											}
											else
											{
												$cont_ok ="mon".$cont_ok;
											}
											
										}
										else
										{
											echo "<script language=javascript>alert('입력하신 월이 잘못 되었습니다.');history.back();</script>";
											exit;
										}
									}
									else if($q ==4)
									{

											if($cont_ok < 32 and $cont_ok > 0)
										{
											if(strlen($cont_ok) < 2)
											{
												$cont_ok ="DAY0".$cont_ok;
											}
											else
											{
												$cont_ok ="DAY".$cont_ok;
											}
										}
										else
										{
											echo "<script language=javascript>alert('입력하신 일이 잘못 되었습니다.');history.back();</script>";
											exit;
										}
									}

										$play_content[$pl_count] = $cont_ok_location.$cont_ok.$cont_ok_location_1;

										$play_content[$pl_count] = trim($play_content[$pl_count]);// 예상치 못한 결과값이 나올 우려가있어서.. 공백문자는 제가 한다. 

										$play_content[$pl_count] = str_replace(" ","",$play_content[$pl_count]); //문자안에 들어가는 공백 제거.
								}

						}
						}
						############################################################################

				}
				else if($file_name[$o] == "(name)" )
				{
					
					
			####################################################################
			##################################################################
					$all="가각간갈감갑값갓강갖같개객갠갯갱갸갹거걱건걸검겁것겉게겨격견결겸겹경곁계고곡곤골곰곱곳공곶과곽관괄광괘괭괴괵괸굉교구국군굳굴굼굽굿궁궉권궐궤귀귁규균귤그극근글금급긍기긱긴길김깃깊까깨꺼꼬꼭꽃꾀꾐꾼꿀꿈꿩끝끼끽나낙난날남납낫낭낮낯낱낳내냇냉냑너넌널넘네녀녁년녈념녑녕녜노녹논놀놈농높놔놜뇌뇨누눅눈눌뉴뉵느늑는늘늠능늦늪니닉닐님닙다닥단달담답당대댁댕더덕덜덤덧덩데도독돈돋돌돗동돼되두둑둔둘둣둥뒤뒷드득든들듬등디딤따딴딸땅때땡떠떡떼또똑똘똥뚜뛰뜬뜰뜸띠라락란랄람랍랑랗래랭략량러런럽렁렇레려력련렬렴렵령례로록론롱뢰료룡루룻룽류륙륜률륭르륵른름릉리릭린림립마막만많맏말맑맘맛망맞매맥맨맹먀머먹먼멀멋멍메멧며멱면멸명몇몌모목몰몸못몽뫼묘묠무묵문물뭇뭉뮤미민밀밉밑바박반발밝밤밥방밭배백뱀버벅번벌범법벗벙벚베벼벽변별볏병보복볶본볼봄봉부북분불붉붓붕브비빈빙빛뻔뻥뿐뿔쁨사삭산살삶삼삽상새색샌샐샘샛생서석선설섬섭성세소속손솔솜솟송솥솰쇄쇠수숙순숟술숨숭숱쉬스슬슭슴습승시식신실심십싱싸쌀쌍쌔쓰쓸씨아악안알암압앙애액앵야약얄얌양어억언얼엄업엇엉에엔여역연열염엽엿영예오옥온올옷옹와완왈왕왜외왼욋요욕욜용우욱운울움웃웅원월위유육윤율융윷으윽은을음읍응의이익인일임입잉잎자작잔잘잠잡잣장잦재쟁저적전절점접정젖제조족존졸좀좁종좋좌죄주죽준줄줏중쥐즉즐즘즙증지직진질짐집짓징짖짚짜찌차착찬찰참창채책처척천철첨첩첫청체초촉촌촐총촬최추축춘출춤춧충췌취측츤층치칙친칠침칩칫칭카칼코콩쾌크큰키타탁탄탈탐탑탕태택탱터털토톡톤톨통퇴투퉁트특틀틈티팅파판팔팟패팽퍅퍼편폄평폐포폭폿퐁표푸푼풀품풍피픽핀필핍핑하학한할함합핫항해핵햇행향허헉헌헐험헛헝헤혁현혈혐협형혜호혹혼홀홈홍화확환활황홰회획횡효후훅훈훌훙훤훼휘휭휴휵휼흉흐흑흔흘흙흠흡흥희흰히힐힘";
	
	$ii = strlen($all);
	$yy = strlen($file_name[$o+1]);
	for ($ei = 0;$ei < $yy;$ei= $ei +2)
		{

		    $plol= substr($file_name[$o+1],$ei,2);

			$check_true_false = strpos($all,$plol);	
			if($check_true_false === false) 
			{
				echo "<script language=javascript>alert('입력하d신 이름이 올바르지 않습니다 이름을 확인하세요!!.');</script>";
				exit;
			}
		}

				####################################################################

				$len_check = strlen($file_name[$o+1]);
				###################################################################

					for($q=0;$q<$len_check;$q = $q+2)
						{

							$pl_count = count($play_content);

							if($pl_count == 0 or $pl_count == NULL)
								{
									
									$cont_ok = substr($file_name[$o+1],$q,2);

									$play_content[0] = $cont_ok_location.$cont_ok.$cont_ok_location_1;

									$play_content[0]  = trim($play_content[0]); // 예상치 못한 결과값이 나올 우려가있어서.. 공백문자는 제가 한다. 

									$play_content[0] = str_replace(" ","",$play_content[0]); //문자안에 들어가는 공백 제거.
								}
								else
								{
										$cont_ok = substr($file_name[$o+1],$q,2);

										$play_content[$pl_count] = $cont_ok_location.$cont_ok.$cont_ok_location_1;

										$play_content[$pl_count] = trim($play_content[$pl_count]);// 예상치 못한 결과값이 나올 우려가있어서.. 공백문자는 제가 한다. 

										$play_content[$pl_count] = str_replace(" ","",$play_content[$pl_count]); //문자안에 들어가는 공백 제거.
								}

						}

				}

		//이분은 멘트 이거나. 컨텐츠 부분을 체크 하는 부분이다. 

			else if($file_name[$o] == "(ment)" or $file_name[$o] == "(content)")
				{
					$pl_count = count($play_content);

					$cont_ok = $file_name[$o+1];
			
					$play_content[$pl_count] = $cont_ok;

					$play_content[$pl_count] = trim($play_content[$pl_count]);// 예상치 못한 결과값이 나올 우려가있어서.. 공백문자는 제가 한다.
					
					$play_content[$pl_count] = str_replace(" ","",$play_content[$pl_count]); //문자안에 들어가는 공백 제거.

			
				}
		}

		return $play_content;
}


//이부분은.. 정렬을 하는 부분이다. 랜덤 돌리기위해... 정렬하는 부분
function action($va_name,$speed)
{
	global $total_check_ok;
	$ca_name = chop($va_name);
	$speed = chop($speed);


	$check = count($total_check_ok);

	if($check == '0' or $check == NULL )
	{

		$total_check_ok[0] = "(".$va_name.")";

		$total_check_ok[1] = $speed;

	}
	else
	{

		$total_check_ok[$check] = "(".$va_name.")";

		$total_check_ok[$check+1] = $speed;

	}
}






?>
