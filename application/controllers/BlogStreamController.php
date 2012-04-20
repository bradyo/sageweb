<?php

class BlogStreamController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->view->users = array();
        $this->view->items = array();
    }

    public function rssAction()
    {
    }
}
