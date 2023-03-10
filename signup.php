<?php

require_once("oopGenPage.php");

session_start();
$time = time();
if (isset($_SESSION['discard']) && $time > $_SESSION['discard']) {
    session_unset(); // Destuct session if been inactive for 1 hour or more.
    session_destroy();
    session_start();
    header("Location: index.php?success=timeout"); // Redirect with custom message.
}
$_SESSION['discard'] = $time + 3600;


$title="Signup Page"; // Set OOP Page content
$cssPath="/resources/signup.css";
$bodyContent=<<<BODY
<div class="signupForm">
<form action="/validateSignUp.php" method="POST" style="border:1px solid #ccc">
  <div class="container">
    <h1>Sign Up</h1>
    <p>Please fill in this form to create an account.</p>
    <hr>
    <label for="email"><b>Email</b></label>
    <input type="text" placeholder="Enter Email" name="email" required>

    <label for="psw"><b>Password</b></label>
    <input type="password" placeholder="Enter Password" name="psw" required>

    <label for="psw-repeat"><b>Repeat Password</b></label>
    <input type="password" placeholder="Repeat Password" name="psw-repeat" required>

    <div class="clearfix">
      <button type="submit" class="signupbtn">Sign Up</button>
    </div>
  </div>
</form>
</div>
BODY;


$page=new HTMLPage($title,$cssPath);
$page->setBodyContent($bodyContent);
$page->render(); // Echo page obj to the browser.

?>