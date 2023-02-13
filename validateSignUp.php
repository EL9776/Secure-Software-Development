<?php

include_once('createDatabase.php');

session_start();
$time = time();
if (isset($_SESSION['discard']) && $time > $_SESSION['discard']) {
    session_unset();
    session_destroy();
    session_start();
    header("Location: index.php");
}
$_SESSION['discard'] = $time + 3600;

$email = $_POST['email'];
$password = $_POST['psw'];
$repeat = $_POST['psw-repeat'];


// Validate password strength
$uppercase = preg_match('@[A-Z]@', $password);
$lowercase = preg_match('@[a-z]@', $password);
$number    = preg_match('@[0-9]@', $password);
$specialChars = preg_match('@[^\w]@', $password);
if (strlen($email)<1 )
{
    echo "Email length too short";

}

elseif (!strpos($email, '@'))
{
    echo "email invalid";
}

else if(!$uppercase || !$lowercase ||!$specialChars  || !$number  || strlen($password) < 9)
{
    echo 'Password should be at least 9 characters in length and should include at least one upper case letter, one number, and one special character.';

}
elseif ($password !== $repeat)
{
    echo "passwords do not match";
}
$hash = password_hash($password, PASSWORD_DEFAULT);

$_DBConnection=new DBConnection();
$_DBConnection->masterGenerate();


?>