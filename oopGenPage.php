<?php


#[AllowDynamicProperties] class HTMLPage
{
    private $_pageHead = "";
    private $_body = "";
    private $_cssFile="";
    public $fileError="";

    function __construct($_title,$cssPath)
    {
        $this->_pageHead = $_title;
        $this->setCSSFile($cssPath);
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

            $files = array_diff(scandir($path), array('.', '..'));
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
            header("Location: index.php");
        }
    }

    function userUploadFile(){
        if (isset($_POST['submit'])){
            $this->path = 'userFiles/'.$_SESSION['user'].'/';
            $this->targetFile = $this->path.basename($_FILES["fileUpload"]["name"]);
            $this->check=1;
            if (file_exists($this->targetFile)) {
                $this->check=0;
            }
            if ($_FILES["fileUpload"]["size"] > 5000000) {
                $this->check=0;
            }
            if ($this->check==0){
                $this->fileError ="File was not uploaded.";
            }
            else{
                if (move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $this->targetFile)) {
                    $this->fileError=htmlspecialchars(basename($_FILES["fileUpload"]["name"])). " has been uploaded.";
                }
                else{
                    $this->fileError="There was an error uploading your file.";
                }
            }
        }

    }
}
?>