<?php
require_once('createDatabase.php');
require_once('oopGenPage.php');

use PHPMailer\PHPMailer\PHPMailer;

require 'libs/PHPMailer/src/Exception.php';
require 'libs/PHPMailer/src/PHPMailer.php';
require 'libs/PHPMailer/src/SMTP.php';

if (isset($_POST["resetRequest"])) {
    $validateObject=new HTMLPage("Validate","");
    $userEmail = $_POST["email"];

    $validateObject->validateEmail($userEmail);


    $selector = bin2hex(random_bytes(8));
    $token = random_bytes(40);

    $absoluteHost=$_SERVER['HTTP_HOST']."/";
    $url = $absoluteHost."newPassword.php?selector=".$selector."&validator=".bin2hex($token);

    #token request expires in 30 mins
    $expires = time() + 1800;

    $_DBconnection = new DBConnection();
    $_DBconnection->resetRequest($selector,$token,$expires,$userEmail);

    #Sends the reset password email to the user
    $message = '<p>You have requested to reset your password. If you would like to continue please use the link below to create a new password.</p>';
    $message .= '<p> Password Reset (Expires in 30 minutes):</p><br>';
    $message .= '<a href="' . $url . '">' . $url . '</a>';
    try{
        $mail = new PHPMailer(true);
        $mail->IsSMTP();
        $mail->Mailer='smtp';
        $mail->SMTPAuth   = TRUE;
        $mail->SMTPSecure = "tls";
        $mail->Port       = 587;
        $mail->Host       = "smtp.gmail.com";
        $mail->Username   = "ssdcloudsender@gmail.com";
        $mail->Password   = "";
        $mail->IsHTML(true);
        $mail->AddAddress($userEmail);
        $mail->SetFrom("resetpassword@ssdcloud.com", "SSD-Cloud");
        $mail->Subject ='SSDCloud: Reset your Password';
        $mail->MsgHTML($message);
        $mail->send();
    }
    catch (Exception $e){
        echo "<h1 style='color: green; text-align:center'>Error while sending Reset Email.</h1>";
        exit();
    }
    header("Location: index.php?success=sent");
}
else {
    header("Location: index.php");
}