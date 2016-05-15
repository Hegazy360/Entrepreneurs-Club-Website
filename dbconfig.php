<?php //database configuration for local database
session_start();
// $servername = "sql210.byethost17.com";
// $username   = "b17_18073034";
// $password   = "7egazy94";
// $dbname     = "b17_18073034_users";
//testing database
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "users";
try {
    $pdo = new PDO("mysql:host={$servername};dbname={$dbname}", $username, $password);
}
catch (PDOException $e) {
    echo $e->getMessage();
    die();
}
include_once 'class.user.php';
$user = new USER($pdo);
