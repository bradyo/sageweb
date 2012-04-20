<?php

/**
 *
 */
class PageController extends Zend_Controller_Action
{
    public function showAction()
    {
        $pageName = basename($this->_getParam('name'));

        // if the view script exists, render it
        $path = APPLICATION_PATH . '/views/scripts/page/' . $pageName . '.phtml';
        if (!file_exists($path)) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }
        $this->render($pageName);
    }
}