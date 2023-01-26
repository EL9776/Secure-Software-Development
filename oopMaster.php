<?php

require_once("oopGenPage.php");

class MasterPage
{
    private $_pages;

    function __construct($_title)
    {
        $this->_pages = new HTMLPage($_title);
    }

    public function createPage()
    {
        $this->setMasterContent();
        return $this->_pages->createPage();
    }

    public function render()
    {
        $this->setMasterContent();
        $this->_pages->render();
    }

    private function setMasterContent()
    {
            $_masterPage = <<<MASTER
<h1>SSD Cloud Homepage</h1>
MASTER;
        $this->_pages->setBodyContent($_masterPage);
    }
}

?>