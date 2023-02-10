<?php

require("oopGenPage.php");

$title="Forgot Password Page";
$cssPath="resources/forgotPassword.css";
$bodyContent=<<<BODY

<div class="forgotPasswordForm">
<form action="/index.php" method="POST" style="...">
  <div class="container"> 
    <h1>Reset Password</h1>
    <p>An email will be sent to you with instructions on how to reset your password.</p>
    <hr>
    <label for="email"><b>Email</b></label>
    <input type="text" placeholder="Enter Email" name="email" required>
   
    <div class="clearfix">
      <button type="reset" name="resetRequest">Send Email to Reset Password</button>
    </div>
  
  </div>
</form>
</div>
BODY;

$page=new HTMLPage($title,$cssPath);
$page->setBodyContent($bodyContent);
$page->render();

?>