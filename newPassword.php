<?php

require_once("oopGenPage.php"); // Class imports
require_once('createDatabase.php');

if (isset($_GET['selector'])&&isset($_GET['validator'])){ // Code only executes if variables exist in URL
    session_start();
    $time = time();
    if (isset($_SESSION['discard']) && $time > $_SESSION['discard']) {
        session_unset(); // Destructs session afer 1 hour (3600s).
        session_destroy();
        session_start();
        header("Location: index.php?success=timeout"); // Redirect with custom message.
    }
    $_SESSION['discard'] = $time + 3600;

    $_DBconnection=new DBConnection(); // Instantiation of DB Class Obj.
    // Sets body content and form, where the action executes the pass replacement logic in the DB Obj.
    $bodyContent=<<<BODY
    <div class="newPasswordForm">
      
      <form action="{$_DBconnection->retreivePassRequest($_GET['selector'],$_GET['validator'])}" method="POST" style="border:1px solid #ccc">
      <div class="container">
      <h1>Password Reset</h1>
    <p>Please fill in this form to Reset your password.</p>
    <hr>
                    <input type="password" name="newPassword" placeholder="Enter New Password">
                    <input type="password" name="repeatNewPassword" placeholder="Confirm New Password">
                    <button type="submit" name="submitNewPassword">Reset Password</button>
                </form>
      </div>
    </div>
BODY;

    $title="Reset Password";
    $cssPath="/resources/resetPass.css";
    $page=new HTMLPage($title,$cssPath);
    $page->setBodyContent($bodyContent);
    $page->render(); // Echos Page obj to browser.
}
else{
    header("Location: index.php"); // if token and selector arent set in URL then redirect to index.php.
}
?>