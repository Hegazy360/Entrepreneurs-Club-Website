<?php //database configuration for local database
session_start();

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
