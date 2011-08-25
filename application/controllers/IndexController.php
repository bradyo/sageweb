<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
    }

    public function deniedAction()
    {
    }

    public function addAction()
    {
        
    }
    
    public function testAction() {
        $this->getHelper('layout')->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();
        
        $postRepo = new Application_Model_Post_PostRepository();
        $post = $postRepo->getCurrent('event', 2);
        
        $eventRepo = new Sageweb_EventRepository();
        $event = $eventRepo->getCurrent(1);
        
        print_r($event->toArray());
    }
}

