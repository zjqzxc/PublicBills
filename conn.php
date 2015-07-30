<?php
session_start();
header("Content-type: text/html; charset=utf-8"); 
if (!file_exists('sql.db')){
	if(!file_exists('sql.sample.db')){
		echo '数据库初始化失败，请检查数据库示例文件sql.sample.db是否存在';
		die();
	}else{
		if(!copy('sql.sample.db', 'sql.db')){
			echo '数据库初始化失败，请检查是否有写入权限';
			die();
		}
	}
}

try{
	$con = new PDO('sqlite:sql.db');
	$con ->query("set names utf8");
	#echo 'Connected!<br />';
}catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
	
//$count = $con -> exec ("INSERT INTO test (text) values('中文')");
//echo $count;
//echo $a;
/*
$c = $con -> query("SELECT * FROM user");
$arr = $c -> fetchAll();
print_r($arr);
*/

?>