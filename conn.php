<?php
header("Content-type: text/html; charset=utf-8"); 
if (!file_exists('sql.db')){
	if(!file_exists('sql.sample')){
		echo '数据库初始化失败，请检查数据库示例文件sql.sample是否存在';
		die();
	}else{
		if(!copy('sql.sample', 'sql.db')){
			echo '数据库初始化失败，请检查是否有写入权限';
			die();
		}
	}
}

try{
	$con = new PDO('sqlite:sql.db');
	echo 'Connected!<br />';
}catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
@mysql_query("set names utf8");

	
//$count = $con -> exec ("INSERT INTO test (text) values('中文')");
//echo $count;
//echo $a;
/*
$c = $con -> query("SELECT * FROM test");
$arr = $c -> fetchAll();
print_r($arr);
*/

?>