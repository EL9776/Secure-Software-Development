<?php


class HTMLPage
{
    private $_pageHead = "";
    private $_body = "";

    function __construct($_title)
    {
        $this->_pageHead = $_title;
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
}
?>