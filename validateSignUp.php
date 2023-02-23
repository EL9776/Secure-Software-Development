<?php

include_once('createDatabase.php');
include_once ('oopGenPage.php');

session_start();
$time = time();
if (isset($_SESSION['discard']) && $time > $_SESSION['discard']) {
    session_unset();
    session_destroy();
    session_start();
    header("Location: index.php");
}
$_SESSION['discard'] = $time + 3600;

$validateObject=new HTMLPage("Validate","");

$email = $_POST['email'];
$password = $_POST['psw'];
$repeat = $_POST['psw-repeat'];

$validateObject->validateEmail($email);
$validateObject->validatePassword($password,$repeat);

$hash = password_hash($password, PASSWORD_DEFAULT);

$_DBConnection=new DBConnection();
$_DBConnection->masterGenerate();
$_DBConnection->addNewUser($email,$hash,"userFiles/".substr($email,0,strpos($email,"@")));
header("Location: index.php");


?>