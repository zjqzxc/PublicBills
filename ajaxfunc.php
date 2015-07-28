<?php
require_once 'conn.php';
$family=isset($_SESSION['family'])?$_SESSION['family']:null;
$user=isset($_SESSION['user'])?$_SESSION['user']:null;

if($user!='') $func=$_REQUEST['func'];
else die('登陆不合法');

switch ($func)
{
case "gethtmluserlist":
	$txt1='// 示例模板
        <div class="ui-block-a">
          <label for="red">赵宏</label>
          <input type="checkbox" name="participant[]" id="red" value="red"  checked="checked" >
        </div>
        <div class="ui-block-b">
          <label for="green">郭鑫鑫</label>
          <input type="checkbox" name="participant[]" id="green" value="green">
        </div>
        <div class="ui-block-c">
          <label for="blue">张嘉琦</label>
          <input type="checkbox" name="participant[]" id="blue" value="blue">
        </div>
        <div class="ui-block-a">
          <label for="red1">赵宏2</label>
          <input type="checkbox" name="participant[]" id="red1" value="red1">
        </div>
	';
	$txt='';
	$sql=$con -> query("SELECT * FROM user WHERE family='$family'");
	$rs = $sql -> fetchAll();
	//print_r($rs);
	$n = count ($rs); //记录数
	$r = ceil($n/3); //分行，每行三个，向上取整得行数
	for ($i=0;$i<$r;$i++){
		for ($k=0;$k<3;$k++){
			if(($i*3+$k)>=$n) break;
			if($k==0)$tmp="ui-block-a";
			elseif ($k==1) $tmp="ui-block-b";
			else $tmp="ui-block-c";
			$user=$rs[$i*3+$k]['user'];
			$name=$rs[$i*3+$k]['name'];
			$txt=$txt."
	<div class=\"$tmp\">
          <label for=\"$user\">$name</label>
          <input type=\"checkbox\" name=\"participant[]\" id=\"$user\" value=\"$user\"  checked=\"checked\" >
        </div>
			";
		}
	}
	break;
case  'addoneperson':
	$txt='
	  <div class="ui-block-a">
          <select name="person[]" id="person">';          
	$sql=$con -> query("SELECT * FROM user WHERE family='$family'");
	$rs = $sql -> fetchAll();
	$n = count ($rs); //记录数
	for($i=0;$i<$n;$i++){
		$user=$rs[$i]['user'];
		$name=$rs[$i]['name'];
		if($_SESSION['id']==$rs[$i]['id'])$select='selected';
		else $select='';
		$txt=$txt."<option value=\"$user\" $select>$name</option>";
	}
          $txt=$txt.'</select>
        </div>
        <div class="ui-block-b">
          <input type="text" name="paid[]" value="RestOfAll" onfocus="if (value ==\'RestOfAll\'){value =\'\'}" onblur="if (value ==\'\' || isNaN(value)){value=\'RestOfAll\'}">
        </div>
        ';
	break;
case 'appendrecord':
  $txt='
<form id="callAjaxForm">
	  <input type="hidden" name="funcname" value=2>
      <select name="title" id="title" data-native-menu="false">
      ';
  $sql=$con -> query("SELECT * FROM bills WHERE tag=2 AND del=0 AND family='$family'");
  while ($rs = $sql -> fetch())
  {
  	$participantlist=json_decode($rs['participant']);
  	if (!in_array($user, $participantlist)) continue;
  	$title=$rs['title'];
	$id=$rs['id'];
	$txt=$txt."<option value=\"$id\" >$title</option>";
  }
  $txt=$txt.'</select>
      <label for="money" class="ui-hidden-accessible">Money</label>
      <input type="text" name="money" id="money" placeholder="你支付了多少钱">      
      <label for="date">
        <input id="btn_dayminus" type="button" data-inline="true" value="昨天的事" data-mini="true" onclick="dayminus()">
        <input id="btn_hoursminus" type="button" data-inline="true" value="一小时前" data-mini="true" onclick="hoursminus()">
      </label>
      <div class="ui-grid-a">
        <div class="ui-block-a">
            <input type="date" name="date" id="date">
        </div>
        <div class="ui-block-b">
            <input type="time" name="time" id="time">
        </div>
      </div>
      <input type="button" onclick="submit0()" id="submit" data-inline="false" value="提交">

    </form>
  '  ;
  break;
  
case 'history':
//家庭成员列表
	$userlist = Array();
	$sql = $con -> query("SELECT user,name FROM user WHERE family='$family'");
	while ($rs = $sql -> fetch()){
		$userlist[$rs['user']]=$rs['name'];
	}
	
	//正在进行
	$txt= '<button onclick="javascript:$(\'.except-me\').toggle();">和我有关</button>
    <h2>正在进行</h2>';
	$sql = $con -> query("SELECT * FROM bills WHERE tag=2 AND family='$family' AND del=0");
	while($rs = $sql -> fetch()){
		//print_r($rs);
		//计算剩余金额
		$paid='';
		$paidinfo='';
		$bill=$rs['id'];
		$sql2 = $con -> query("SELECT user,money FROM settlement WHERE bill='$bill' AND family='$family'");
		while ($rs2= $sql2 -> fetch()){
			$paid+=$rs2['money'];
			$paidinfo.=$userlist[$rs2['user']].' 支付了 '.$rs2['money'].' 元<br />'
			;
		}
		$rest=$rs['money']-$paid;
		//参与人列表
		$participantlist=json_decode($rs['participant']);
		$participantname='';
		foreach ($participantlist as $value){
			$participantname.=$userlist[$value].' ';
		}
		if (in_array($user, $participantlist)) $txt.='<div data-role="collapsible">
		';
		else $txt.='<div class="except-me" data-role="collapsible">
		';
		$txt.='<h3>'.$rs['title'].'<h3>
		';
		$txt.='<div>
        <b>总金额：</b>'.$rs['money'].'<br />
        <b>参与人：</b>'.$participantname.'<br />
        <b>时间：</b>'.date("Y-m-d H:i", $rs['date']).'<br />
        <b>剩余金额：</b>'.$rest.'<br />
        <b>支付详情：</b><br />'.$paidinfo.'
      </div>
      </div>
      ';
	}
	//echo '<br />还未结算<br />';
	$txt.='<h2>还未结算</h2>';
	$sql1 = $con -> query ("SELECT * FROM bills WHERE tag=1 AND family='$family' AND del=0");
	while($rs = $sql1 -> fetch()){
		$paidinfo='';
		$bill=$rs['id'];
		$sql2 = $con -> query("SELECT user,money FROM settlement WHERE bill='$bill' AND family='$family'");
		while ($rs2= $sql2 -> fetch()){
			$paidinfo.=$userlist[$rs2['user']].' 支付了 '.$rs2['money'].' 元<br />'
			;
		}
		//参与人列表
		$participantlist=json_decode($rs['participant']);
		$participantname='';
		foreach ($participantlist as $value){
			$participantname.=$userlist[$value].' ';
		}
		if (in_array($user, $participantlist)) $txt.='<div data-role="collapsible">
		';
		else $txt.='<div class="except-me" data-role="collapsible">
		';
		$txt.='<h3>'.$rs['title'].'<h3>
		';
		$txt.='<div>
        <b>总金额：</b>'.$rs['money'].'<br />
        <b>参与人：</b>'.$participantname.'<br />
        <b>时间：</b>'.date("Y-m-d H:i", $rs['date']).'<br />
        <b>支付详情：</b><br />'.$paidinfo.'
      </div>
      </div>
      ';
	}
	
	//历史记录
	$txt.='<h2>历史记录</h2>
	<div data-filter="true">';
	$sql1 = $con -> query ("SELECT * FROM bills WHERE tag=0 AND family='$family' AND del=0");
	while($rs = $sql1 -> fetch()){
		$paidinfo='';
		$bill=$rs['id'];
		$sql2 = $con -> query("SELECT user,money FROM settlement WHERE bill='$bill' AND family='$family'");
		while ($rs2= $sql2 -> fetch()){
			$paidinfo.=$userlist[$rs2['user']].' 支付了 '.$rs2['money'].' 元<br />'
			;
		}
		//参与人列表
		$participantlist=json_decode($rs['participant']);
		$participantname='';
		foreach ($participantlist as $value){
			$participantname.=$userlist[$value].' ';
		}
		if (in_array($user, $participantlist)) $txt.='<div data-role="collapsible">
		';
		else $txt.='<div class="except-me" data-role="collapsible">
		';
		$txt.='<h3>'.$rs['title'].'<h3>
		';
		$txt.='<div>
        <b>总金额：</b>'.$rs['money'].'<br />
        <b>参与人：</b>'.$participantname.'<br />
        <b>时间：</b>'.date("Y-m-d H:i", $rs['date']).'<br />
        <b>支付详情：</b><br />'.$paidinfo.'
      </div>
      </div>
      ';
	}
	$txt.='</div>
	<h2> </h2>
    <input href="#" type="button" id="more" value="更多..." disabled="reue"></input>
    <h2> </h2>';
    
	break;
	
case 'settlement':
	$deviation=0;//偏差值，记录钱数四舍五入后与实际值的差
	//查询config的settlementchange字段，若为0则实付应付无改变，若为1则更新
	$sql = $con -> query("SELECT settlementchange FROM config WHERE id='$family'");
	$rs = $sql ->fetch();
	if($rs['settlementchange']){
		$sql_family = $con -> query ("SELECT * FROM user WHERE family='$family'");
		while ($rs_family=$sql_family -> fetch()){
			$usertmp = $rs_family['user'];
			//echo '<br />'.$usertmp;
			//echo '<br />应付：';
			$sql = $con -> query ("SELECT * FROM bills WHERE tag=1 AND family='$family' AND del=0");
			$AccountsPayable=0;
			$RealPayment=0;
			$bills=Array();
			while($rs = $sql -> fetch()){
				$bills[]=$rs['id'];
				$participantlist=json_decode($rs['participant']);
				if (!in_array($usertmp, $participantlist)) continue;
				$num=count($participantlist);
				$AccountsPayable+=$rs['money']/$num;	
			}
			//echo $AccountsPayable;

			$realmoney=$AccountsPayable;
			$recordmoney=round($realmoney+$deviation);
			$deviation=$realmoney-$recordmoney;
			//echo '<br />'.$recordmoney;
			//echo '<br />'.$deviation;

			//echo '<br />实付：';
			foreach ($bills as $value){
				$sql = $con -> query("SELECT money FROM settlement WHERE user='$usertmp' AND bill='$value'");
				$rs = $sql -> fetch();
				$RealPayment+=$rs['money'];
			}
			//echo $RealPayment;
			$sql = $con -> query("UPDATE user SET accountspayable='$recordmoney',realpayment='$RealPayment' WHERE user='$usertmp'");
		}
		$sql = $con -> query("UPDATE config SET settlementchange=0 WHERE id='$family'");
	}
	
	
	//处理给钱方式
	$sql = $con -> query("SELECT * FROM user WHERE family='$family'");
	$i=0;
	while($rs = $sql -> fetch()){
		$arr[$i]['user']=$rs['user'];
		$arr[$i]['money']=$rs['realpayment']-$rs['accountspayable'];
		$arr[$i]['name']=$rs['name'];
		$i++;
	}
	//print_r($arr);

	/*//测试数组
	$arr=array(
		array("money"=>"10","name"=>"name1"),
		array("money"=>"-15","name"=>"name2"),
		array("money"=>"-15","name"=>"name3"),
		array("money"=>"1","name"=>"name4")
	);*/
	$num=count($arr);
	$arr_record=$arr;
	$show = Array();
	for($i=0;$i<$num;$i++){//第一层：从第一个人开始，若为正则收钱，为负则给钱
		if($arr[$i]['money']<0) {
			while (-$arr[$i]['money']){
				for($j=0;$j<$num;$j++){
					if($arr[$j]['money']>0){//第二层循环：找到值为正的人，即应该收其他人付钱的人
						if($arr[$j]['money']>=(-$arr[$i]['money'])){
							//echo $arr[$i]['name'].'>>'.$arr[$j]['name'].'  '.-$arr[$i]['money'].'<br />';
							$show[]=array($arr[$i]['name'],$arr[$j]['name'],-$arr[$i]['money']);
							$arr[$j]['money']+=$arr[$i]['money'];
							$arr[$i]['money']=0;
							break;
							//print_r($arr);
						}else{
							//echo $arr[$i]['name'].'>>'.$arr[$j]['name'].'  '.$arr[$j]['money'].'<br />';
							$show[]=array($arr[$j]['name'],$arr[$i]['name'],$arr[$j]['money']);
							$arr[$i]['money']+=$arr[$j]['money'];
							$arr[$j]['money']=0;
						}
					}else{
						echo '数据有误，计算无法进行！<br />';
						break(2);
					}
				}
				//数据完整性保护，防止while进入死循环。
				//获取money组中不同元素的数量。若为2（除0以外，还有一个其他数），则表示出现错误。
				$m =array();
				foreach($arr as $value){
					$m[]=$value['money'];
				}
				//print_r($m);
				//echo count(array_unique($m));
				if (count(array_unique($m))==2){
					echo '数据完整性有误，但问题不大';
					break;
				}
			}
		}
	}
	$txt='<h3>回顾(从上次结算至今)</h3>
	';
	foreach ($arr_record as $value){
		$txt.="<h5>". $value['name'].' 需要收入（负数为支出） ：'.$value['money']." 元。</h5>
		";
	}
	$txt.='<h4>详情请参阅历史记录</h4>';
	
	$txt.='<h3>支付详情</h3>';
	foreach ($show as $value){
		$txt.="<h5> $value[0] 支付 $value[1] 共 $value[2] 元。</h5>
		";
	}
	$txt2='<input id="btn_settlement" type="button" data-inline="false" value="发起一次结算" onclick="settlement()">';
	$txt3='<input id="btn_settlement" type="button" data-inline="false" value="结算完成" onclick="newconfirm(\'你确定已经收到钱了（或者都给出去了）？\',dosettlement)">';
	$txt4='<input id="btn_settlement" type="button" data-inline="false" value="等待他人完成结算"  disabled="disabled">';
	
	$sql = $con -> query("SELECT status FROM config WHERE id='$family'");
	$rs = $sql -> fetch();
	$sql2 = $con -> query("SELECT status FROM user WHERE user='$user'");
	$rs2 = $sql2 -> fetch();
	if($rs['status']==1) 
		if ($rs2['status']==2) $txt.=$txt4;
		else $txt.=$txt3;
	else $txt.=$txt2;
	break;
case 'help':
	$txt='使用帮助';
	break;
default:
	$txt='Unknown Function！';
}
echo $txt;

?>