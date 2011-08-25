<?php

/**
 * Controller for changing user account settings.
 */
class AccountController extends Zend_Controller_Action
{
    public function indexAction()
    {
        // if activation key is given, authenticate the user
        $auth = Zend_Auth::getInstance();
        $activationKey = $this->_request->getParam('activationKey');
        if ($activationKey) {
            $userTable = Sageweb_Model__UserTable::getInstance();
            $user = $userTable->findByActivationKey($activationKey);
            if ($user) {
                // clear the activation key and re-save
                $user->activationKey = null;
                $user->save();

                // load profile before saving to auth
                $user->loadReference('profile');

                $auth->clearIdentity();
                $auth->getStorage()->write($user);

                // set a flag to show the user a message that they should
                // reset thier password
                $this->view->resettingPassword = true;
            }
        }

        // if username not given, use the currently logged in user
        if (!$auth->hasIdentity()) {
            $redirector = new Zend_Controller_Action_Helper_Redirector();
            $redirector->gotoRoute(array(
                'controller' => 'User',
                'action' => 'login',
                'dest' => urlencode($this->getRequest()->getRequestUri())
                ),
                'default', true
            );
        }

        $user = Zend_Auth::getInstance()->getIdentity();
        $this->view->user = $user;

        $form = new Sageweb_Model_Form_AccountForm();
        $form->populate(array(
           'email' => $user->email,
           'timezone' => $user->timezone,
        ));

        $message = null;
        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getParams())) {
                $values = $form->getValues();

                $user->email = $values['email'];
                if (!empty($values['password'])) {
                    $user->passwordAlgorithm = 'sha1';
                    $user->passwordSalt = sha1(time());
                    $user->passwordHash = sha1($values['password'] . $user->passwordSalt);
                }
                $user->timezone = $values['timezone'];
                $user->save();

                $message = "Account updated successfully!";
            }
        }
        $this->view->form = $form;
        $this->view->message = $message;
    }

    public function executePassword()
    {
    }
   
    public function executePreferences()
    {
    }

    public function executePrivacy()
    {
    }

    public function executeSubscriptions()
    {
    }
}
