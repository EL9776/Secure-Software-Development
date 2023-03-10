<?php

require_once("oopGenPage.php");

session_start();
$time = time();
if (isset($_SESSION['discard']) && $time > $_SESSION['discard']) {
    session_unset(); // Logic to ensure session is destroyed after an hour
    session_destroy();
    session_start();
    header("Location: index.php?success=timeout");
}
$_SESSION['discard'] = $time + 3600;

$title="Forgot Password Page"; // Setting OOP Page Obj content.
$cssPath="resources/forgotPassword.css";
$bodyContent=<<<BODY

<div class="forgotPasswordForm">
<form action="/resetRequest.php" method="POST">
  <div class="container">
    <h1>Reset Password</h1>
    <p>An email will be sent to you with instructions on how to reset your password.</p>
    <hr>
    <label for="email"><b>Email</b></label>
    <input type="text" placeholder="Enter Email" name="email">
    <div class="clearfix">
      <button type="submit" name="resetRequest">Send Email to Reset Password</button>
    </div>
  </div>
</form>
</div>
BODY;

$page=new HTMLPage($title,$cssPath);
$page->setBodyContent($bodyContent);
$page->render(); // Echos HTMLpage to browser.

?>