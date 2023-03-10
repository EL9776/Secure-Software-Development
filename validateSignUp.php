<?php

include_once('createDatabase.php');
include_once('oopGenPage.php');

session_start();
$time = time();
if (isset($_SESSION['discard']) && $time > $_SESSION['discard']) {
    session_unset(); // Destruct session after 1 hour of inactivity.
    session_destroy();
    session_start();
    header("Location: index.php?success=timeout"); // redirect with custom message.
}
$_SESSION['discard'] = $time + 3600;

$validateObject=new HTMLPage("Validate",""); // HTMLPage obj purely for validation methods.

$email = $_POST['email'];
$password = $_POST['psw'];
$repeat = $_POST['psw-repeat'];

$validateObject->validateEmail($email); // Validate email & pass are safe and match requirements.
$validateObject->validatePassword($password,$repeat);

$hash = password_hash($password, PASSWORD_DEFAULT); // hash (BCRYPT) password to store in DB securely

$_DBConnection=new DBConnection();
$_DBConnection->masterGenerate(); // Generate DB tables if not exists.
$_DBConnection->addNewUser($email,$hash,"userFiles/".substr($email,0,strpos($email,"@"))); // Add user to DB and the file directory location.
header("Location: index.php?success=created"); // Redirect with custom message.


?>