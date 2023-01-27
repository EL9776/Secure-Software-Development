<?php

require_once("oopGenPage.php");

class MasterPage
{
    private $_pages;

    function __construct($_title,$cssPath)
    {
        $this->_pages = new HTMLPage($_title);
        $this->_pages->setCSSFile($cssPath);
    }

    public function createPage()
    {
        $this->masterPageContents();
        return $this->_pages->createPage();
    }

    public function render()
    {
        $this->masterPageContents();
        $this->_pages->render();
    }

    private function masterPageContents()
    {
            $_masterPage = <<<MASTER
<h1>SSD Cloud Homepage</h1>
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
    <input type="password" placeholder="Enter Password" name="psw" required>
        
    <button type="submit">Login</button>
    <label>
      <input type="checkbox" checked="checked" name="remember"> Remember me
    </label>
  </div>

  <div class="container" style="background-color:#f1f1f1">
    <button type="button" class="cancelbtn">Cancel</button>
    <span class="psw">Forgot <a href="#">password?</a></span>
  </div>
</form>
</div>

MASTER;
        $this->_pages->setBodyContent($_masterPage);
    }
}

?>