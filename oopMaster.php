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
        $this->_pages->setBodyContent($_masterPage);
    }
}

?>