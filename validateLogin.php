<?php

session_start();
$time = time();
if (isset($_SESSION['discard']) && $time > $_SESSION['discard']) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['discard'] = $time + 3600;

$_SESSION['user']=substr($_POST['email'],0,strpos($_POST['email'],"@"));
header("Location: cloudHomepage.php");
echo "WIP"; // Need to generate UNIQUE SessionID here and go to
                // appropriate interface DONT use rand()
                // NEED interfacing with MySQL Backend here
                // (Validation, data insertion, hashing of password and redirection)

?>