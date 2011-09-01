<?php

class ProfileController extends Zend_Controller_Action
{
    const ITEMS_PER_PAGE = 20;

    public function indexAction()
    {
        $pager = Sageweb_Table_User::getPager();

        $page = $this->_getParam('page', 1);
        $pager->setItemCountPerPage(self::ITEMS_PER_PAGE);
        $pager->setCurrentPageNumber($page);

        $this->view->pager = $pager;
    }

    private function _buildMenu($username)
    {
        $pages = array();
        $profilePage = new Zend_Navigation_Page_Mvc(array(
            'label' => 'View Profile',
            'route' => 'profile',
            'module' => 'default',
            'controller' => 'profile',
            'action' => 'show',
            'params' => array('username' => $username)
        ));
        $pages[] = $profilePage;

        $viewingUser = Application_Registry::getCurrentUser();
        if ($viewingUser->username == $username) {
            $editPage = new Zend_Navigation_Page_Mvc(array(
                'label' => 'Edit Profile',
                'route' => 'profile',
                'module' => 'default',
                'controller' => 'profile',
                'action' => 'edit',
                'params' => array('username' => $username)
            ));
            $pages[] = $editPage;
        }

        return new Zend_Navigation($pages);
    }


    public function showAction()
    {
        $username = $this->_getParam('username');
        $user = Sageweb_Table_User::getUser($username);
        if (!$user) {
            throw new Zend_Controller_Action_Exception('User does not exist', 404);
        }

        $this->view->user = $user;
        $this->view->menu = $this->_buildMenu($user->username);
    }

    public function editAction()
    {
        $username = $this->_getParam('username');
        $user = Sageweb_Table_User::getUser($username);
        if (!$user) {
            throw new Zend_Controller_Action_Exception('User does not exist', 404);
        }

        $viewingUser = Application_Registry::getCurrentUser();
        if ($viewingUser->username !== $username) {
            throw new Zend_Controller_Action_Exception('Cannot edit page', 404);
        }

        $form = new Application_Form_Profile();
        $profile = $user->profile;
        if (!$profile) {
            $profile = new Application_Model_Profile_Profile();
            $profile->userId = $viewingUser->id;
            $profile->save();
        }
        $form->populate($profile->toArray());

        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
            $profile->fromArray($values);
            $profile->save();

            $url = $this->view->url(array('action' => 'show'), 'profile');
            $this->_redirect($url);
        }

        $this->view->user = $viewingUser;
        $this->view->menu = $this->_buildMenu($viewingUser->username);
        $this->view->form = $form;
    }

    public function contentAction()
    {
        // todo
        $username = $this->_getParam('username');
        $user = Sageweb_Table_User::getUser($username);
        if (!$user) {
            throw new Zend_Controller_Action_Exception('User does not exist', 404);
        }

        $this->view->user = $user;
        $this->view->menu = $this->_buildMenu($user->username);
        $this->view->items = array();
    }

    public function discussionsAction()
    {
        // todo
        $username = $this->_getParam('username');
        $user = Sageweb_Table_User::getUser($username);
        if (!$user) {
            throw new Zend_Controller_Action_Exception('User does not exist', 404);
        }

        $this->view->user = $user;
        $this->view->menu = $this->_buildMenu($user->username);
        $this->view->items = array();
    }

    public function blockAction()
    {
        $viewingUser = Application_Registry::getCurrentUser();
        if (!$viewingUser->isModerator()) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }

        $username = $this->_getParam('username');
        $user = Sageweb_Table_User::getUser($username);
        if (!$user) {
            throw new Zend_Controller_Action_Exception('User does not exist', 404);
        }

        if ($this->_request->isPost()) {
            $user->status = Application_Model_User_User::STATUS_BLOCKED;
            $user->save();

            $this->_redirect($this->view->url(array('action' => 'show')));
        }

        $this->view->user = $user;
        $this->view->menu = $this->_buildMenu($user->username);
    }

    public function unblockAction()
    {
        $viewingUser = Application_Registry::getCurrentUser();
        if (!$viewingUser->isModerator()) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }

        $username = $this->_getParam('username');
        $user = Sageweb_Table_User::getUser($username);
        if (!$user) {
            throw new Zend_Controller_Action_Exception('User does not exist', 404);
        }

        if ($this->_request->isPost()) {
            $user->status = Application_Model_User_User::STATUS_ACTIVE;
            $user->save();

            $this->_redirect($this->view->url(array('action' => 'show')));
        }

        $this->view->user = $user;
        $this->view->menu = $this->_buildMenu($user->username);
    }

    public function roleAction()
    {
        $viewingUser = Application_Registry::getCurrentUser();
        if (!$viewingUser->isAdmin()) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }

        $username = $this->_getParam('username');
        $user = Sageweb_Table_User::getUser($username);
        if (!$user) {
            throw new Zend_Controller_Action_Exception('User does not exist', 404);
        }

        if ($this->_request->isPost()) {
            switch ($this->_getParam('role')) {
                case Application_Model_User_User::ROLE_ADMIN:
                    $user->role = Application_Model_User_User::ROLE_ADMIN;
                    break;
                case Application_Model_User_User::ROLE_MODERATOR:
                    $user->role = Application_Model_User_User::ROLE_MODERATOR;
                    break;
                case Application_Model_User_User::ROLE_MEMBER:
                    $user->role = Application_Model_User_User::ROLE_MEMBER;
                    break;
            }
            $user->save();

            $this->_redirect($this->view->url(array('action' => 'show')));
        }

        $this->view->user = $user;
        $this->view->menu = $this->_buildMenu($user->username);
    }
}


