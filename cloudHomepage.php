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

$title="Signup Page";
$cssPath="/resources/cloud.css";
$bodyContent=<<<BODY
<h1>Welcome {$_SESSION['user']}</h1>
BODY;

$path = 'userFiles/'.$_SESSION['user'];

$files = array_diff(scandir($path), array('.', '..'));

$page=new HTMLPage($title,$cssPath);
$page->setBodyContent($bodyContent);
$page->render();

echo "<h2 class='filesTitle'>Files:</h2>";
foreach($files as $file){
    $totalpath=$path.'/'.$file;
    echo "<div class='fileOutput'><a href='{$totalpath}'><b>$file</b></a></div>";
}

?>