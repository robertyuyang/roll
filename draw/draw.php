﻿<html>
<head>
<title>draw</title>
<script>
function onClick(user_name){
	document.roll.user_name.value = user_name;
	document.roll.submit();

}
function onReset(){
	document.roll.reset.value = 1;
	document.roll.submit();
}
function changeBG(){
	document.body.style.backgroundImage = "url(images/winner_bg.jpg)"
}
</script>
</head>
<body text="#000000" background="">


<?php 

function seed()

{

list($msec, $sec) = explode(' ', microtime());

return (float) $sec;

}


srand(seed());

/*$draws = array("A1","B1","B2","B3","B4");
array_splice($draws, 0,1);
var_dump($draws);
echo ('<br>');
array_splice($draws, 3,1);
var_dump($draws);
#$draws = array("A1","A2","A3","A4","B1","B2","B3","B4","C1","C2","C3","D1","D2","D3");
$draws_count = count($draws);
 */
$user_name_list = array("a"=>0, "b"=>0,  "c"=>0);
$his_num_list =  array("a"=>0, "b"=>0,  "c"=>0);
$leading_user_name = '';
$leading_number = 0;
$have_a_winner = 0;
$have_number_count = 0;

$doc = new DOMDocument(); 
$doc->load("data.xml");
$root = $doc->getElementsByTagName('root');
$flag = $root->item(0)->getElementsByTagName('flag');
$ele_draws = $root->item(0)->getElementsByTagName('draws');
$ele_draws = $ele_draws->item(0)->getElementsByTagName('draw');
$left_draws = array();
#echo $ele_draws->item(3)->nodeValue;
foreach( $ele_draws as $ele_draw)
{
	if($ele_draw->getAttribute('taken') == '0')
	{
		array_push($left_draws, $ele_draw->nodeValue);
	}
}
$draws_count = count($left_draws);
$ele_have_a_winner = $flag->item(0)->getElementsByTagName('have_a_winner');
$root = $root->item(0)->getElementsByTagName('data');
$nodes = $root->item(0)->getElementsByTagName( "item" );
$leading_ele_his_num = null; 
foreach( $nodes as $node ) 
{ 
	$ele_user_name = $node->getElementsByTagName( "user_name" );  
	$user_name = $ele_user_name->item(0)->nodeValue;

	$ele_number = $node->getElementsByTagName('number');
	$ele_his_num = $node->getElementsByTagName('his_num');
	$number = 0;	

	if(isset($_POST['reset']) && ($_POST['reset'] == 1)){
		$ele_number->item(0)->nodeValue = ''; 
		$ele_have_a_winner->item(0)->nodeValue = 0;
	}
	else if(isset($_POST['user_name'])){
		$post_user_name =  $_POST['user_name'];
		if($user_name == $post_user_name){
			if($ele_number->item(0)->nodeValue == ''){
				$index = rand(1, $draws_count) - 1; 
				$draw_name = $left_draws[$index];
				$ele_number->item(0)->nodeValue = $draw_name;

				array_splice($left_draws, $index,1);
				#var_dump($left_draws);
				$draws_count--;
				foreach( $ele_draws as $ele_draw)
				{
					if($ele_draw->nodeValue == $draw_name)
					{
						$ele_draw->setAttribute('taken', '1');
					}
				}
			}		
		}
	}

	$number = $ele_number->item(0)->nodeValue;
	if(!empty($number)){
		$have_number_count++;
	}
/*
	if($number > $leading_number){
		$leading_user_name = $user_name;
		$leading_number = $number;
		$leading_ele_his_num = $ele_his_num;
	}*/

	$user_name_list[$user_name] = $number;
	$his_num_list[$user_name] = $ele_his_num->item(0)->nodeValue;
	$ele_real_name = $node->getElementsByTagName( "real_name" );  
	$real_name = $ele_real_name->item(0)->nodeValue;
	$his_num_list[$user_name] = $real_name;
	#echo mb_convert_encoding($real_name, "gb2312", "utf-8"); 
	#
	#echo '<br />';
	#echo $number;
	#echo '<br />';
	

}
/*
if($have_number_count == count($user_name_list)){
	$have_a_winner = 1;
	if($ele_have_a_winner->item(0)->nodeValue == 0){
		$ele_have_a_winner->item(0)->nodeValue = 1;
		$leading_ele_his_num->item(0)->nodeValue = $leading_ele_his_num->item(0)->nodeValue + 1;
		$his_num_list[$leading_user_name] = $his_num_list[$leading_user_name] + 1;
	}

}*/

$doc->save('data.xml');




?>




<center>
<form action="draw.php" method="post" name="roll"> 
<input type="text" name="user_name" style="display:none" />
<input type="number" name="reset" value="0" style="display:none" >
<center><h1><b>抽签系统1.0</b></h1></center>
<table width="1024px"  bgcolor0="#ffffff" border="1px">

<tr height="100px" align="center" valign="center">
<?php


foreach($user_name_list as $key => $value ){
	$big_num_list = array('一','二','三','四','五','六','七','八','九','十');
	echo '<td>';
	if(isset($user_name_list[$key]) && !empty($user_name_list[$key])){
		if($key == $leading_user_name){
			if($have_a_winner == 0){
				echo '<img src="images\leading.jpg" width = "100px" height = "100px"/><br/>领先者';
			}else{
				echo '<font face="黑体" size="8px" color="#ffffff"> <img src="images\winner.gif" width = "150px" height = "150px"/><b>';
				echo $big_num_list[$his_num_list[$key] - 1];
				echo '冠王';
				echo '<img src="images\winner.gif" width = "150px" height = "150px"/><b></font>';
				echo '<br><input  type="button" value="reset" onclick=onReset()  style="width:50px;height:20px;"/>';
				echo '<script>changeBG();</script>';
			}
			
		}
	
	}

	echo '</td>';
}
?>


</tr>


<tr height="300px">
<?php
if($have_a_winner == 0){
	foreach($user_name_list as $key => $value ){
		echo '<td align="center">';
		echo '<img src="images\\'.$key.'.jpg" width="230px"/>';
		echo '<br>';
		echo $his_num_list[$key];
		echo '</td>';
	}
}
else{
	/*
	foreach($user_name_list as $key => $value ){
		if($key != $leading_user_name){
			echo '<td><img src="images\\'.$key.'.jpg" width="50px" height="50px"/></td>';
		}
		else
		{
			echo '<td><img src="images\\'.$key.'.jpg" height="400px"/></td>';
		}
	
	}*/

}



	
?>


</tr>
<tr bgcolor="#ffffff" height="20px" align="center" valign="center">
<?php
foreach($user_name_list as $key => $value ){

	echo '<td>';
	if(isset($user_name_list[$key]) && !empty($user_name_list[$key])){
		echo $user_name_list[$key];
	
	}
	else{
		echo '<input  type="button" value="抽签" onclick=onClick("'.$key.'")  style="width:150px;height:50px;"/>';
	}

	//echo '<br>';
	//echo '历史战绩:' ; 
	//echo $his_num_list[$key];
	//echo '次登顶';
	echo '</td>';
	
}
?>


</tr>
</table>
</form>
</center>
</body>

</html>
