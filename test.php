<?php
include 'conn.php';


$rs = $con -> query("SELECT * FROM user");
$arr = $rs -> fetchAll();
print_r($arr);