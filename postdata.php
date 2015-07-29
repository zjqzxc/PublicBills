<?php
require 'conn.php';
function FetchRepeatMemberInArray($array) { 
    // 获取去掉重复数据的数组 
    $unique_arr = array_unique ( $array ); 
    // 获取重复数据的数组 
    $repeat_arr = array_diff_assoc ( $array, $unique_arr ); 
    return $repeat_arr; 
} 
function checkstr($str){ //防止sql注入
	$str=htmlentities($str,ENT_QUOTES);
	return $str;
}

$arr=$_REQUEST;

function login($arr,$con){
	$user=$arr['user'];
	$pwd=$arr['pwd'];
	$sql = $con -> query("SELECT id,family FROM user WHERE user='$user' AND passwd='$pwd'");
	$rs1 = $sql ->fetch();
	if(empty($rs1))
		return 'error';
	else {
		$_SESSION['id']=$rs1[0];
		$_SESSION['user']=$user;
		$_SESSION['family']=$rs1[1];
		return 1;
	}
}

function logout(){
	session_destroy();
	return 1;
}

function whoami($con){
	$user=$_SESSION['user'];
	$sql = $con -> query("SELECT name FROM user WHERE user='$user'");
	$rs = $sql -> fetch();
	return $rs['name'];
}

function addnewbills($arr,$con){
	//查询family id
	$family=$_SESSION['family'];
	//参数赋值
	$title = $arr['title'];
	$money = $arr['money'];
	$participant_json = json_encode($arr['participant']);
	$datetime = strtotime($arr['date'].' '.$arr['time']);
	//数据正确性验证
	//输入检查
	if ($title=='') return 'title_null';
	if ($money=='') return 'money_null';
	if (!is_numeric($money)) return 'money_nan';
	
	//验证付款人是否有重复
	if(count(FetchRepeatMemberInArray($arr['person']))){
		return 'paidinfo_payer_repeat';
	}
	//处理RestOfAll
	if(in_array('RestOfAll', FetchRepeatMemberInArray($arr['paid']))) return 'paidinfo_toomuch_ROA';//查找是否有两个ROA
	//替换restofall为剩余的总金额
	if(in_array('RestOfAll', $arr['paid'])){
		$mm=array_search('RestOfAll', $arr['paid']);
		$total=0;
		foreach($arr['paid'] as $value){
			if(is_numeric($value))$total+=$value;
		}
		$arr['paid'][$mm]=$money-$total;
	}
	//验证支付金额与总金额是否匹配
	$total=0;
	foreach($arr['paid'] as $value){
		$total+=$value;
	}
	
	if($total>$money) return 'paidinfo_toolarge';
	elseif ($total==$money) {
		$tag=1;
	}else{
		$tag=2;
	}
	$paidinfo = array_combine($arr['person'], $arr['paid']);
	//数据库操作
	//bills表
	/*tag说明：
	 * 0：已结算
	 * 1：已完成全部付款
	 * 2：付款总额小于money
	 * */
	
	$sql = $con -> exec ("INSERT INTO bills (title,money,participant,date,tag,family) VALUES ('$title','$money','$participant_json','$datetime','$tag','$family')");
	if(!$sql) return 'sql_error';
	//从bills表中获取刚存入的记录的id
	$sql2 = $con -> query ("SELECT id FROM bills WHERE title='$title' AND date='$datetime' AND family='$family'");
	$rs = $sql2 -> fetch();
	$billid = $rs['id'];
	//付款信息存入settlement表中
	foreach ($paidinfo as $user => $paid){
		$sql = $con -> exec ("INSERT INTO settlement (user,bill,money,date,family) VALUES ('$user','$billid','$paid','$datetime','$family')");
		if(!$sql) return 'sql_error';
	}
	//更新结算刷新标志为1
	$sql = $con -> exec ("UPDATE config SET settlementchange=1 WHERE id='$family'");
	if(!$sql) return 'sql_error';
	//print_r($datetime); 
	return 1;
}

function appendrecord($arr,$con){
	$billid=$arr['title'];
	$paid=$arr['money'];
	$datetime = strtotime($arr['date'].' '.$arr['time']);
	$family=$_SESSION['family'];
	$user=$_SESSION['user'];
	
	//输入检查
	if ($billid=='') return 'title_null';
	if ($paid=='') return 'money_null';
	if (!is_numeric($paid)) return 'money_nan';
	
	$sql = $con -> query("SELECT money FROM bills WHERE id='$billid'");
	$rs = $sql -> fetch();
	$bill_total=$rs[0];
	$sql = $con -> query ("SELECT money FROM settlement WHERE bill='$billid' AND family='$family'");
	
	$paid_total=0;
	while ($rs = $sql -> fetch()){
		$paid_total += $rs['money'];
	}
	if($paid_total+$paid>$bill_total) return 'paid_toomuch';
	elseif(($paid_total+$paid)==$bill_total) {
		$sql_update = $con -> exec ("UPDATE bills SET tag=1");
	}
	
	$sql = $con -> exec ("INSERT INTO settlement (user,bill,money,date,family) VALUES ('$user','$billid','$paid','$datetime','$family')");
	if(!$sql) return 'sql_error';
	$sql = $con -> exec ("UPDATE config SET settlementchange=1 WHERE id='$family'");
	if(!$sql) return 'sql_error';
	return 1;
}


function settlement($con){
	//将config表中status值置为1，同时将user表中当前用户的status置为1
	$family=$_SESSION['family'];
	$user=$_SESSION['user'];
	$sql = $con -> exec("UPDATE config SET status=1 WHERE id='$family'");
	$sql2= $con -> exec("UPDATE user SET status=1 WHERE user='$user'");
	if($sql&&$sql2) return 1;
	else return 'sql_error';
}

function checksettlement($con){
	if (!isset($_SESSION['user'])) return 'no_login';
	$family=$_SESSION['family'];
	$user=$_SESSION['user'];
	$sql = $con -> query("SELECT status FROM config WHERE id='$family'");
	$rs = $sql ->fetch();
	$sql = $con -> query("select status FROM user WHERE user='$user'");
	$rs2= $sql ->fetch();
	if($rs['status']==1 AND $rs2['status']!=1 AND $rs2['status']!=2) return 1;
	else return 0;
}

function dosettlement($con){
	$family=$_SESSION['family'];
	$user=$_SESSION['user'];
	$sql = $con -> exec("UPDATE user SET status=2 WHERE user='$user'");
	if(!$sql) return 'sql_error';
	$sql2 = $con -> query("SELECT status FROM user WHERE family='$family'");
	while($rs = $sql2 -> fetch()){
		if($rs['status']==2) continue;
		else return 1;
	}
	//清楚结算临时状态
	$sql = $con -> exec("UPDATE config SET status=0 WHERE id='$family'");
	$sql2 = $con -> exec ("UPDATE user SET status=0 WHERE family='$family'");
	//标记已完成结算的bill
	$sql3 = $con -> exec ("UPDATE bills SET tag=0 WHERE tag=1 AND family='$family' AND del=0");
	//修改config表中settlementchange字段，置为1
	$sql4 = $con -> exec ("UPDATE config SET settlementchange=1 WHERE id='$family'");
	if($sql AND $sql2) return 2;
	else return 'status_reset_error';
}

function register($arr,$con){
	/* 返回值说明：
	 * 1：成功创建一个家庭并成功创建账户
	 * 2：成功创建账户并加入一个家庭
	 * 'family_not_exist':家庭不存在
	 */
	$user=$arr['user_reg'];
	$pwd=$arr['passwd'];
	$name=$arr['name'];
	date_default_timezone_set('PRC'); 
	$time=time();
	if(isset($arr['familyname'])){//新建一个家庭
		$familyname=checkstr($arr['familyname']);
		$sql = $con -> exec("INSERT INTO config (familyname,admin,datetime) VALUES ('$familyname','$user','$time')");
		if(!$sql) return 'sql_error1';
		$sql2 = $con -> query ("SELECT id FROM config WHERE familyname='$familyname' AND admin='$user'");
		$rs2 = $sql2 -> fetch();
		$familyid=$rs2['id'];
		// add a record to user
		$sql = $con -> exec("INSERT INTO user (user,passwd,name,family) VALUES ('$user','$pwd','$name','$familyid')");
		if(!$sql) return 'sql_error2';
		return 1;
	}
	else {
		$familyid=$arr['familyid'];
		$admin=$arr['familyuser'];
		$sql = $con -> query("SELECT id FROM config WHERE id='$familyid' AND admin='$admin'");
		$rs = $sql -> fetch();
		if($rs){
			$familyid=$rs['id'];
			$sql = $con -> exec("INSERT INTO user (user,passwd,name,family) VALUES ('$user','$pwd','$name','$familyid')");
			if(!$sql) return 'sql_error2';
		}else{
			return 'family_not_exist';
		}
		return 2;
	}
	
	
}

function checkuser($arr,$con){
	$user = trim($arr['user']);
	$sql = $con -> query("SELECT id FROM user WHERE user='$user'");
	$rs = $sql -> fetch();
	if ($rs) return 0;
	else return 1;
}

switch ($arr['funcname'])
{
	case 'login':
		$txt=login($arr,$con);
		break;
	case 'logout':
		$txt=logout();
		break;
	case 'whoami':
		$txt=whoami($con);
		break;
	case '1':
		$txt=addnewbills($arr,$con);
		break;
	case '2':
		$txt=appendrecord($arr,$con);
		break;
	case "settlement":
		$txt = settlement($con);
		break;
	case "checksettlement":
		$txt = checksettlement($con);
		break;
	case "dosettlement":
		$txt = dosettlement($con);
		break;
	case "register":
		$txt = register($arr,$con);
		break;
	case "checkuser":
		$txt = checkuser($arr,$con);
		break;
	default:
		$txt='Unknown Function';	
}
echo $txt;
?>