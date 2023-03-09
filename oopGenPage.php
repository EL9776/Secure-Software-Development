<?php

require_once('createDatabase.php');

class HTMLPage
{
    private $_pageHead = "";
    private $_body = "";
    private $_cssFile="";
    public $fileError="";
    private $dynamicStats=0;

    function __construct($_title,$cssPath)
    {
        $this->_pageHead = $_title;
        $this->setCSSFile($cssPath);
        $this->dBObj=new DBConnection();
    }

    public function setCSSFile($_cssFile)
    {
        $this->_cssFile = $_cssFile;
    }

    public function setBodyContent($_bodyContent)
    {
        $this->_body = $_bodyContent;
    }

    public function render()
    {
        echo $this->createPage();
    }

    public function createPage()
    {
        $pageContents = <<<HTML
<!DOCTYPE html>
<html lang="en">
{$this->gen_Head()}
{$this->gen_Body()}
</html>
HTML;
        return $pageContents;
    }

    private function gen_Head()
    {
        $headContent = <<<HEAD
<head>
    <link rel="stylesheet" href={$this->_cssFile}>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$this->_pageHead}</title>
    <link rel="icon" type="image/x-icon" href="/resources/lock.png">
</head>
HEAD;
        return $headContent;
    }

    private function gen_Body()
    {
        $htmlData = <<<BODY
<body>
    {$this->_body}
</body>
BODY;
        return $htmlData;
    }

    function viewUserFiles(){
        $finalOutput="";
        if (isset($_SESSION['user'])){
            $path = 'userFiles/'.$_SESSION['user'];

            $files = array_diff(scandir($path), array('.', '..','profile.png'));
            $finalOutput=<<<FILES

        FILES;
            foreach($files as $file){
                $totalpath=$path.'/'.$file;
                $finalOutput=$finalOutput."<div class='fileOutput'><a href='{$totalpath}' download=''>$file</a></div><br>";
            }
        }
        return $finalOutput;
    }

    function logoutUser(){
        if (isset($_POST['logout'])){
            session_unset();
            session_destroy();
            session_start();
            header("Location: index.php?success=logout");
        }
    }

    function userUploadFile($profileUploadCheck=0){
        $this->profileUploadCheck=$profileUploadCheck;
        if (isset($_POST['submit'])){
            $this->path = 'userFiles/'.$_SESSION['user'].'/';
            $this->targetFile = $this->path.basename($_FILES["fileUpload"]["name"]);
            $this->check=1;
            if (file_exists($this->targetFile) && $profileUploadCheck==0) {
                $this->check=0;
            }
            if ($_FILES["fileUpload"]["size"] > 5000000) {
                $this->check=0;
            }
            if (strlen($_FILES["fileUpload"]["name"])>35){
                $this->check=0;
            }
            if ($this->profileUploadCheck==0 && $_FILES["fileUpload"]["name"]=="profile.png"){
                $this->check=0;
            }
            if ($this->check==0){
                $this->fileError ="File was not uploaded.";
            }
            else{
                if ($this->profileUploadCheck==0) {
                    if (move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $this->targetFile)){
                        $this->fileError=htmlspecialchars(basename($_FILES["fileUpload"]["name"])). " has been uploaded.";
                        $this->dBObj->uploadedFile($_FILES["fileUpload"]["name"]);
                    }
                }
                else if ($this->profileUploadCheck==1) {
                    if (move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $this->path.basename('profile.png'))){
                        $this->fileError=htmlspecialchars(basename('profile.png')). " has been uploaded.";
                        header("Location: viewProfile.php");
                    }
                }
                else {
                    $this->fileError="There was an error uploading your file.";
                }
            }
        }
    }

    function validateEmail($email){

        if (strlen($email)<1 )
        {
            echo "<h1 style='color: green; text-align:center'>Email length too short</h1>";
            exit();
        }
        else if (strlen($email)>100){
            echo "<h1 style='color: green; text-align:center'>Email length too long</h1>";
            exit();
        }
        else if (!strpos($email, '@'))
        {
            echo "<h1 style='color: green; text-align:center'>Email Invalid</h1>";
            exit();
        }
        else if (preg_match('@[^\w]@',substr($email,0,strpos($email,"@")))
            || (substr_count($email,"@")!=1)){
            echo "<h1 style='color: green; text-align:center'>E-mail contains illegal characters</h1>";
            exit();
        }
    }

    function dynamicUserStats(){
        $this->dynamicStats=$this->dBObj->getDynamicUserStats();
        return $this->dynamicStats;
    }

    function validatePassword($password,$repeat){
        // Validate password strength
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        if(!$uppercase || !$lowercase ||!$specialChars  || !$number  || strlen($password) < 9)
        {
            echo "<h1 style='color: green; text-align:center'>Password should be at least 9 characters in length and should include at least one upper case letter, one number, and one special character.</h1>";
            exit();
        }
        elseif ($password !== $repeat)
        {
            echo "<h1 style='color: green; text-align:center'>Passwords do not match</h1>";
            exit();
        }
    }

    function userProfileIcon(){
        $this->checkPath=$_SERVER['DOCUMENT_ROOT']."/userFiles/".$_SESSION['user']."/profile.png";
        if (file_exists($this->checkPath)){
            $this->avatarFile="/userFiles/".$_SESSION['user']."/profile.png";
            $profileOutput=<<<PROFILE
<div class="imgcontainer">
    <img src="{$this->avatarFile}" alt="Avatar" class="avatar">
  </div>
PROFILE;
        }
        else{
            $profileOutput=<<<PROFILE
<div class="imgcontainer">
    <img src="resources/avatar.png" alt="Avatar" class="avatar">
  </div>
PROFILE;
        }
        return $profileOutput;
    }
}
?>