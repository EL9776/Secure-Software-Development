<?php

require_once('oopMaster.php');

$title="Test";
$cssPath="/resources/master.css";

$page=new MasterPage($title,$cssPath);
$page->render();

?>