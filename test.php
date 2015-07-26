<?php 
include 'conn.php';

$sql = $con -> exec ("UPDATE bills SET tag=0 WHERE tag=1 AND family=1 AND del=0");
print_r($sql);
?>