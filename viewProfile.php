<?php

include_once('oopGenPage.php');

session_start();
$time = time();
if (isset($_SESSION['discard']) && $time > $_SESSION['discard']) {
    session_unset(); // Destruct after 1h of inactivity.
    session_destroy();
    session_start();
    header("Location: index.php?success=timeout"); // Redirect with custom message.
}
$_SESSION['discard'] = $time + 3600;

if (!isset($_SESSION['user'])){ // if not authenticated then dont allow access to this page.
    header("Location: index.php");
}

$title="Profile"; // set content for the page obj
$cssPath="/resources/profile.css";
$page=new HTMLPage($title,$cssPath);
// body content contains standard layout alongside logout button, dynamic profile icon, dynamic stats, profile icon upload and custom error.
$bodyContent=<<<BODY
<div class="profileTitle">
<h1>Your Profile</h1>

<div class="loghomebuttons">
<form method="POST" action="{$page->logoutUser()}">
<input type="submit" class="logoutbtn" name="logout" value="Logout"/>
</form>
<form method="POST" action="cloudHomepage.php">
<input type="submit" class="homepagebtn" name="homepage" value="Homepage"/>
</form>
<br><br>
<h2 class="username">{$_SESSION['user']}'s Profile</h2>
</div>
</div>
{$page->userProfileIcon()}
<div class="dynamicContent">
<h4>You Have Uploaded {$page->dynamicUserStats()} Files</h4>
</div>
<div class="changeProfile">
<form action="{$page->userUploadFile(1)}" method="POST" enctype="multipart/form-data">
  <b>Change Profile Picture</b><br>
  <input type="file" name="fileUpload" id="fileUpload">
  <br>
  <input type="submit" value="Upload Photo" name="submit">
</form>
<h5>{$page->fileError}</h5>
</div>
BODY;

$page->setBodyContent($bodyContent);
$page->render(); // Echo page obj content to the browser.

?>