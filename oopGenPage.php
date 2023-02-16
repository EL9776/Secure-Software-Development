<?php


class HTMLPage
{
    private $_pageHead = "";
    private $_body = "";
    private $_cssFile="";

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
        $path = 'userFiles/'.$_SESSION['user'];

        $files = array_diff(scandir($path), array('.', '..'));
        $finalOutput=<<<FILES

        FILES;
        foreach($files as $file){
            $totalpath=$path.'/'.$file;
            $finalOutput=$finalOutput."<div class='fileOutput'><a href='{$totalpath}' download=''>$file</a></div><br>";
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
}
?>