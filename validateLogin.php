<?php

session_start();
$time = time();
if (isset($_SESSION['discard']) && $time > $_SESSION['discard']) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['discard'] = $time + 3600;

// only do if user is authed with DB and pass hash validated.
$_SESSION['user']=substr($_POST['email'],0,strpos($_POST['email'],"@"));

//echo "WIP";   // NEED interfacing with MySQL Backend here
// (Validation, data insertion, hashing of password and redirection)
$email = $_POST['email'];
$password = $_POST['psw'];

if(strlen($email)<1 || strlen($email)>100)
{
    if(strlen($email) <1)
        echo "email length too short";
    else
        echo "email length too long";
    exit();
}
elseif (!strpos($email, '@'))
{
    echo "Email syntax invalid";
    exit();
}
$dbcreds = new mysqli("localhost", "root", "Bigbrother1");
$dbcreds-> select_db('cwDB');
if($stmt = $dbcreds -> prepare("SELECT `email`, `passHash` FROM `UserDetails` WHERE BINARY `email` = ? LIMIT 1"))
{

    $stmt  -> bind_param("s", $email);
    $stmt -> execute();
    $stmt -> bind_result($uid, $uhash);
    $stmt -> store_result();

    while($stmt -> fetch())
    {
        if(password_verify($password, $uhash))
        {
            $_SESSION['uemail'] = $email;
            $_SESSION['uid'] = $uid;
            Header("Location: cloudHomepage.php");
        }
        else{
            echo "Wrong Email or password";
        }
    }
    $stmt -> close();
}
?>
