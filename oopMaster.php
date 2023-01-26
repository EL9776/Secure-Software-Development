<?php

require_once("oopGenPage.php");

class MasterPage
{
    private $_pages;

    function __construct($_title,$cssPath)
    {
        $this->_pages = new HTMLPage($_title);
        $this->_pages->setCSSFile($cssPath);
    }

    public function createPage()
    {
        $this->masterPageContents();
        return $this->_pages->createPage();
    }

    public function render()
    {
        $this->masterPageContents();
        $this->_pages->render();
    }

    private function masterPageContents()
    {
            $_masterPage = <<<MASTER
<h1>SSD Cloud Homepage</h1>
MASTER;
        $this->_pages->setBodyContent($_masterPage);
    }
}

?>