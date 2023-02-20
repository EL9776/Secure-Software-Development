<?php

require_once("oopGenPage.php");

session_start();
$time = time();
if (isset($_SESSION['discard']) && $time > $_SESSION['discard']) {
    session_unset();
    session_destroy();
    session_start();
    header("Location: index.php");
}
$_SESSION['discard'] = $time + 3600;

if (!isset($_SESSION['user'])){
    header("Location: index.php");
}

$title="Signup Page";
$cssPath="/resources/cloud.css";
$page=new HTMLPage($title,$cssPath);

$bodyContent=<<<BODY

<div class="containerButtons">
<h1 class="nameTitle" id="propertitle">Welcome {$_SESSION['user']}</h1>
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
$page->render();

?>