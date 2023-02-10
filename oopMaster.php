<?php

require_once("oopGenPage.php");

class MasterPage
{
    private $_pages;

    function __construct($_title,$cssPath)
    {
        $this->_pages = new HTMLPage($_title,$cssPath);
    }

    public function render()
    {
        $this->masterPageContents();
        $this->_pages->render();
    }

    private function masterPageContents()
    {
            $_masterPage = <<<MASTER

<h1>Cloud </h1>
<div class="masterLoginForm">
<h2>Login Form</h2>
<form action="/validateLogin.php" method="POST">
  <div class="imgcontainer">
    <img src="resources/avatar.png" alt="Avatar" class="avatar">
  </div>

  <div class="container">
    <label for="email"><b>Email</b></label>
    <input type="text" placeholder="Enter Email Address" name="email" required>

    <label for="psw"><b>Password</b></label>
    <input type="password" placeholder="Enter Password background-colour = green"  name="psw" required>
        
    <button type="submit">Login</button>
  </div>

  <div class="container" style="background-color:darkorchid">
    <button type="button" class="cancelbtn">Cancel</button>
    <span class="psw"><a href="forgotPassword.php">Forgot password?</a></span>
    
  </div>
  <div class="container" style="background-color:yellow">
  <span class="signupLink"><a href="/signup.php">Sign Up</a></span>
  </div>
</form>
</div>
</body>
MASTER;
        $this->_pages->setBodyContent($_masterPage);
    }
}

?>