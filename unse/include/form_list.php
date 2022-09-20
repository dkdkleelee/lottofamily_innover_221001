<?
$user_member[year] = 1970;
function user_year($num,$user_check)
{
	echo "<select name=\"user".$num."_year\">";

		   
		   for($i=1900;$i< date(Y) + 20 ;$i++)
			{if($i < 10)
					{
						$i = "0".$i;
					}
				if($i != $user_check)
					{
						 echo  "<option value=\"$i\">$i</option>";
					}
					else
					{
						echo  "<option value=\"$i\" selected>$i</option>";
					}

			}
			

	echo "</select>";
}


function user_month($num,$user_check)
{
	echo "<select name=\"user".$num."_month\">";
  
			for($i=1;$i< 13;$i++)
			{
					if($i < 10)
					{
						$i = "0".$i;
					}
				if($i != $user_check)
					{
						 echo  "<option value=\"$i\">$i</option>";
					}
					else
					{
						echo  "<option value=\"$i\" selected>$i</option>";
					}
			}

     echo "</select>";
}




function user_day($num,$user_check)
	{
		echo '<select name="user'.$num.'_day">';
		
             for($i=1;$i< 32;$i++)
			{
				 
					if($i < 10)
					{
						$i = "0".$i;
					}
				if($i != $user_check)
					{
						 echo  "<option value=\"$i\">$i</option>";
					}
					else
					{
						echo  "<option value=\"$i\" selected>$i</option>";
					}

			}

		echo '</select>';
	}


function user_sex($num,$user_check)
	{
		   echo "<select name=\"user".$num."_sex\">";
			
          	if( 0 == $user_check)
			{
			 echo  "<option value=\"1\" selected>남자</option>";
			 echo  "<option value=\"2\" >여자</option>";
			}
			else
			{
				echo  "<option value=\"2\" selected>여자</option>";
			    echo  "<option value=\"1\"'>남자</option>";
			}
			
			echo "</select>";
	}



function user_cal($num,$check)
	{
		if($check == "1")
		{
			echo "<input type=\"radio\" name=\"user".$num."_sol\" value=\"01\" checked>";
		}
		else
		{
			echo "<input type=\"radio\" name=\"user".$num."_sol\" value=\"02\">";
		}
	}


function target_year($num,$first,$last,$date)
	{
		echo "<select name=target".$num."_year>";

		for($i=$first;$i< $last;$i++)
		{
			if($i != $date)
			{
				 echo  "<option value=\"$i\">$i</option>";
			}
			else
			{
				echo  "<option value=\"$i\" selected>$i</option>";
			}
	
		}
	  

		echo "</select>";
	}
function target_month($num,$date)
	{
	echo "<select name=target".$num."_month>";

		for($i=1;$i< 13;$i++)
		{
				if($i < 10)
					{
						$i = "0".$i;
					}
				if($i != $date)
					{
						 echo  "<option value=\"$i\">$i</option>";
					}
					else
					{
						echo  "<option value=\"$i\" selected>$i</option>";
					}
	
		}
	  

			echo "</select>";
	}
function target_day($num,$date)
	{
	echo "<select name=target".$num."_day>";

		for($i=1;$i<32;$i++)
		{
				if($i < 10)
				{
					$i = "0".$i;
				}
				if($i != $date)
				{
					 echo  "<option value=\"$i\">$i</option>";
				}
				else
				{
					echo  "<option value=\"$i\" selected>$i</option>";
				}
	
		}
	  

	echo "</select>";
	}

function user_hour($num,$user_check)
	{

		echo "<select name=\"user".$num."_hour\">";

		echo  "<option value=\"0\">00시:00분 - 01시:30분 자시</option>";
		echo  "<option value=\"2\">01시:31분 - 03시:30분 축시</option>";
		echo  "<option value=\"4\">03시:31분 - 05시:30분 인시</option>";
		echo  "<option value=\"6\">05시:31분 - 07시:30분 묘시</option>";
		echo  "<option value=\"8\">07시:31분 - 09시:30분 진시</option>";
		echo  "<option value=\"10\">09시:31분 - 11시:30분 사시</option>";
		echo  "<option value=\"12\">11시:31분 - 13시:30분 오시</option>";
		echo  "<option value=\"14\">13시:31분 - 15시:30분 미시</option>";
		echo  "<option value=\"16\">15시:31분 - 17시:30분 신시</option>";
		echo  "<option value=\"18\">17시:31분 - 19시:30분 유시</option>";
		echo  "<option value=\"20\">19시:31분 - 21시:30분 술시</option>";
		echo  "<option value=\"22\">21시:31분 - 23시:30분 해시</option>";
		echo  "<option value=\"0\">23시:31분 - 24시:00분 야자시</option>";
		echo  "<option value=\"0\">생시 모름</option>";


		 echo '</select>';
	}
function user_cal2($num,$check)
	{
		if($check == "1")
		{
			echo "<input type=\"radio\" name=\"user_col".$num."_sol\" value=\"01\" checked>";
		}
		else
		{
			echo "<input type=\"radio\" name=\"user_col".$num."_sol\" value=\"02\">";
		}
	}

?>