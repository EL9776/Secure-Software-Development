<?php

if (isset($_POST["resetRequest"])) {

    #User authentication to prevent timing attacks
    $selector = bin2hex(random_bytes(8));
    $token = random_bytes(40);

   # $url = "www.cloud.co.uk/forgotPassword/createNewPassword.php?selector=" . $selector . "&validator=" . bin2hex($token);

    #session expires in 30 mins
    $expires = date("U") + 1800;


} else {
    header("signup.php");
}