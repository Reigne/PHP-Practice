<?php
$sname = "localhost";
$uname = "root";
$password = "";

$db_name = "db_crud";

$conn = mysqli_connect($sname, $uname, $password, $db_name);

if(!$conn) {
    echo "Connection Failed";
}

?>