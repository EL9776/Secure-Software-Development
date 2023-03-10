<?php

require_once("oopGenPage.php");

session_start();


$title="Signup Page";
$cssPath="/resources/cloud.css";
$page=new HTMLPage($title,$cssPath); // Setting Object oriented page content to load

// Setting the body content for the cloud homepage (File upload form, logout button etc.)
$bodyContent=<<<BODY

<div class="containerButtons">
<div class="topRowUser">
{$page->userProfileIcon()}
<h1 class="nameTitle" id="propertitle">Welcome {$_SESSION['user']}</h1>
</div>
<div class="logprofbuttons">
<form method="POST" action="{$page->logoutUser()}">
<input type="submit" class="logoutbtn" name="logout" value="Logout"/>
</form>

<form method="POST" action="viewProfile.php">
<input type="submit" class="profilebtn" name="profile" value="Profile"/>
</form>
<br><br>
<form action="{$page->userUploadFile()}" method="POST" enctype="multipart/form-data">
  <b>Select file to upload</b>
  <input type="file" name="fileUpload" id="fileUpload">
  <input type="submit" value="Upload File" name="submit">
</form>

<h5>{$page->fileError}</h5>

</div>
</div>

<div class="userFiles">
<br>
<h2>Files:</h2>
{$page->viewUserFiles()}
</div>

BODY;
$page->setBodyContent($bodyContent);
$page->render(); // Echos the page via the OOP Class

?>