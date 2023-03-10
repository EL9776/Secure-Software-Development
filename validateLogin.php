<?php

include_once('createDatabase.php');
include_once('oopGenPage.php');

session_start();
$time = time();
if (isset($_SESSION['discard']) && $time > $_SESSION['discard']) {
    session_unset(); // Destory session if inactive for longer than 1 hour.
    session_destroy();
    session_start();
    header("Location: index.php?success=timeout"); // redirect with custom message.
}
$_SESSION['discard'] = $time + 3600;


$email = $_POST['email'];
$password = $_POST['psw'];

$validateObject=new HTMLPage("Validate",""); // instantiate the HTMLPage class for validation methods only.

$validateObject->validateEmail($email); // Ensure email is sanitised and matches strength requirements.

$_DBConnection=new DBConnection();
$_DBConnection->masterGenerate(); // Generate all DB tables if not exists.

$_DBConnection->checkDBForAccount($email,$password); // Check that the account exists in the DB.

?>
