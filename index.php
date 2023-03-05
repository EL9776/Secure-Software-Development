<?php

require_once('oopGenPage.php');
require_once('createDatabase.php');

session_start();
$time = time();
if (isset($_SESSION['discard']) && $time > $_SESSION['discard']) {
    session_unset();
    session_destroy();
    session_start();
    header("Location: index.php?success=timeout");
}
$_SESSION['discard'] = $time + 3600;

$title="SSD Cloud";
$cssPath="/resources/master.css";
$bodyContent=<<<MASTER
<h1>SSD Cloud Login</h1>
<div class="masterLoginForm">
<form action="/validateLogin.php" method="POST">
  <div class="imgcontainer">
    <img src="resources/avatar.png" alt="Avatar" class="avatar">
  </div>

  <div class="container">
    <label for="email"><b>Email</b></label>
    <input type="text" placeholder="Enter Email Address" name="email" required>

    <label for="psw"><b>Password</b></label>
    <input type="password" placeholder="Enter Password" name="psw" required>
        
    <button type="submit">Login</button>
  </div>

  <div class="container">
  <span class="psw"><a href="forgotPassword.php">Forgot password?</a></span>
  <span class="signupLink"><a href="/signup.php">Sign Up</a></span>
  </div>

</form>
</div>
</body>
MASTER;

$page=new HTMLPage($title,$cssPath);
$page->setBodyContent($bodyContent);

$_DBConnection=new DBConnection();
$_DBConnection->masterGenerate();

if (isset($_GET['success'])){
    if ($_GET['success']=="true"){
        echo "<h3 style='color: green; text-align:center'>Password Successfully Reset!</h3>";
    }
    else if ($_GET['success']=="created"){
        echo "<h3 style='color: green; text-align:center'>Account Successfully Created!</h3>";
    }
    else if ($_GET['success']=="logout"){
        echo "<h3 style='color: green; text-align:center'>Successfully Logged Out!</h3>";
    }
    else if ($_GET['success']=="timeout"){
        echo "<h3 style='color: green; text-align:center'>Session Timed Out!</h3>";
    }
}

$page->render();

?>