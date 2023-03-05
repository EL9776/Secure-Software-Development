<?php
require_once('createDatabase.php');
require_once('oopGenPage.php');
if (isset($_POST["resetRequest"])) {
    $validateObject=new HTMLPage("Validate","");
    $userEmail = $_POST["email"];

    $validateObject->validateEmail($userEmail);


    $selector = bin2hex(random_bytes(8));
    $token = random_bytes(40);


    $url = "newPassword.php?selector=".$selector."&validator=".bin2hex($token);

    #token request expires in 30 mins
    $expires = time() + 1800;

    $_DBconnection = new DBConnection();
    $_DBconnection->resetRequest($selector,$token,$expires,$userEmail);

    #Sends the reset password email to the user
    $to = $userEmail;
    $subject = 'Reset your Password';
    $message = '<p>You have requested to reset your password. If you would like to continue please use the link below to create a new password.</p>';
    $message .= '<p> Password Reset (Expires in 30 minutes):</br>';
    $message .= '<a href="' . $url . '">' . $url . '</a></p>';

    $headers = "From: SSD Cloud <resetpassword@ssdcloud.com>\r\n";
    $headers .= "Reply-To: resetpassword@ssdcloud.com\r\n";
    $headers .= "Content-type: text/html\r\n";

//    mail($to, $subject, $message,$headers);
//    header("Location: forgotPassword.php?reset=success");
//    header("Location: index.php?reset=success");
    echo $message;
}
else {
    header("Location: index.php");
}