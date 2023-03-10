<?php

require_once('createDatabase.php');

class HTMLPage
{
    private $_pageHead = "";
    private $_body = "";
    private $_cssFile="";
    public $fileError="";
    private $dynamicStats=0;

    function __construct($_title,$cssPath) // Generates title for page, Css path and an instance of the DB obj.
    {
        $this->_pageHead = $_title;
        $this->setCSSFile($cssPath);
        $this->dBObj=new DBConnection();
    }

    public function setCSSFile($_cssFile) // Setter Method.
    {
        $this->_cssFile = $_cssFile;
    }

    public function setBodyContent($_bodyContent) // Setter Method.
    {
        $this->_body = $_bodyContent;
    }

    public function render() // Getter method for page contents.
    {
        echo $this->createPage();
    }

    public function createPage() // Setter Method for page head and body.
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

    private function gen_Head() // Adds external stylesheet to head and page title.
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

    private function gen_Body() // Setter Method for the page body
    {
        $htmlData = <<<BODY
<body>
    {$this->_body}
</body>
BODY;
        return $htmlData;
    }

    function viewUserFiles(){ // Outputs every file in the respective user directory hiding .,.. and profile.png.
        $finalOutput="";
        if (isset($_SESSION['user'])){ // Only executes if user is authenticated, to avoid data leaks.
            $path = 'userFiles/'.$_SESSION['user'];

            $files = array_diff(scandir($path), array('.', '..','profile.png'));
            $finalOutput=<<<FILES

        FILES;
            foreach($files as $file){ // Adds to output var for each file in directory.
                $totalpath=$path.'/'.$file;
                $finalOutput=$finalOutput."<div class='fileOutput'><a href='{$totalpath}' download=''>$file</a></div><br>";
            }
        }
        return $finalOutput; // Output of all files string.
    }

    function logoutUser(){ // If logout button is pressed then the session is destoryed and user is redirected.
        if (isset($_POST['logout'])){
            session_unset();
            session_destroy();
            session_start();
            header("Location: index.php?success=logout");
        }
    }

    function userUploadFile($profileUploadCheck=0){ // Uploads user files to the respective directory.
        $this->profileUploadCheck=$profileUploadCheck; // Check for if profile icon is being uploaded.
        if (isset($_POST['submit'])){ // if file is submitted
            $this->path = 'userFiles/'.$_SESSION['user'].'/';
            $this->targetFile = $this->path.basename($_FILES["fileUpload"]["name"]); // Set the total file path of the file uploaded.
            $this->check=1;
            if (file_exists($this->targetFile) && $profileUploadCheck==0) { // Ensures no duplicates
                $this->check=0;
            }
            if ($_FILES["fileUpload"]["size"] > 5000000) { // ensures filesize is not too big.
                $this->check=0;
            }
            if (strlen($_FILES["fileUpload"]["name"])>35){ // ensures filename is no longer than 35 characters
                $this->check=0;
            }
            if ($this->profileUploadCheck==0 && $_FILES["fileUpload"]["name"]=="profile.png"){ // if uploaded file is profile.png then fail since its only to be done from the profile page.
                $this->check=0;
            }
            if ($this->check==0){ // sets file error if any of the checks fail.
                $this->fileError ="File was not uploaded.";
            }
            else{
                if ($this->profileUploadCheck==0) { // Logic for if the file being uploaded is from the main cloud page.
                    if (move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $this->targetFile)){
                        $this->fileError=htmlspecialchars(basename($_FILES["fileUpload"]["name"])). " has been uploaded.";
                        $this->dBObj->uploadedFile($_FILES["fileUpload"]["name"]); // executes audit log of file into DB Table.
                    }
                }
                else if ($this->profileUploadCheck==1) { // If file is from the profile page, then force name to profile.png and permit upload.
                    if (move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $this->path.basename('profile.png'))){
                        $this->fileError=htmlspecialchars(basename('profile.png')). " has been uploaded.";
                        header("Location: viewProfile.php"); // Redirect
                    }
                }
                else {
                    $this->fileError="There was an error uploading your file.";
                }
            }
        }
    }

    function validateEmail($email){ // Validates the email is sanitised and valid.

        if (strlen($email)<1 ) // email logic checks for length, containing @, and making sure no illegal characters e.g ! or £
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

    function dynamicUserStats(){ // Getter for the Dynamic user stats (Num of files uploaded)
        $this->dynamicStats=$this->dBObj->getDynamicUserStats();
        return $this->dynamicStats;
    }

    function validatePassword($password,$repeat){ // Ensures password is sanitised and matches strength requirements.
        // Validate password strength
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);
        // password strength logic for 9 chars, 1 upper, 1 num and 1 special char.
        if(!$uppercase || !$lowercase ||!$specialChars  || !$number  || strlen($password) < 9)
        {
            echo "<h1 style='color: green; text-align:center'>Password should be at least 9 characters in length and should include at least one upper case letter, one number, and one special character.</h1>";
            exit();
        }
        elseif ($password !== $repeat) // ensures passwords match.
        {
            echo "<h1 style='color: green; text-align:center'>Passwords do not match</h1>";
            exit();
        }
    }

    function userProfileIcon(){ // Dynamic user profile icon is set to default if there is not an existing profile.png in user folder.
        $this->checkPath=$_SERVER['DOCUMENT_ROOT']."/userFiles/".$_SESSION['user']."/profile.png";
        if (file_exists($this->checkPath)){
            $this->avatarFile="/userFiles/".$_SESSION['user']."/profile.png"; // path to user profile.
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
        return $profileOutput; // outputs dynamic profile.png.
    }
}
?>