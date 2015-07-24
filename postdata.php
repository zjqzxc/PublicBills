<?php
require 'conn.php';

$arr=$_REQUEST;

function login($arr,$con){
	$user=$arr['user'];
	$pwd=$arr['pwd'];
	$sql = $con -> query("SELECT id FROM user WHERE user='$user' AND passwd='$pwd'");
	$rs1 = $sql ->fetch();
	if(empty($rs1))
		return 'error';
	else {
		$_SESSION['id']=$rs1[0];
		return 1;
	}
		
}
function addnewbills(){
	;
}

switch ($arr['funcname'])
{
	case 'login':
		$txt=login($arr,$con);
		break;
	case '1':
		$txt=11;
		break;
	default:
		$txt='Unknown Function';	
}
echo $txt;
?>