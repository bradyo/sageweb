<?php

class Application_Plugin_Loader extends Zend_Controller_Plugin_Abstract {
    
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        parent::dispatchLoopStartup($request);

        /* @var $viewRenderer Zend_View_Helper_Abstract */
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = $viewRenderer->view;
        $viewRenderer->view->addHelperPath(APPLICATION_PATH . '/views/helpers');
        
    }

}
