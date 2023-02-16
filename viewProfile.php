<?php

include_once('oopGenPage.php');

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

$title="Profile";
$cssPath="/resources/profile.css";
$page=new HTMLPage($title,$cssPath);

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
<h2 class="username">Mr. {$_SESSION['user']}</h2>
</div>

</div>
BODY;

$page->setBodyContent($bodyContent);
$page->render();

?>