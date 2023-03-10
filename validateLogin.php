<?php

include_once('createDatabase.php');
include_once('oopGenPage.php');

session_start();
$time = time();
if (isset($_SESSION['discard']) && $time > $_SESSION['discard']) {
    session_unset();
    session_destroy();
    session_start();
    header("Location: index.php?success=timeout");
}
$_SESSION['discard'] = $time + 3600;


$email = $_POST['email'];
$password = $_POST['psw'];

$validateObject=new HTMLPage("Validate","");

$validateObject->validateEmail($email);

$_DBConnection=new DBConnection();
$_DBConnection->masterGenerate();

$_DBConnection->checkDBForAccount($email,$password);

?>
