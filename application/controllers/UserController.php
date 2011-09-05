<?php

class UserController extends Zend_Controller_Action
{
    public function accountAction()
    {
        $user = Application_Registry::getCurrentUser();
        if ($user->role == Sageweb_Cms_User::ROLE_GUEST) {
            throw new Zend_Controller_Action_Exception('Not logged in', 404);
        }

        $form = new Application_Form_Account();
        $formValues = $user->toArray();
        $formValues['displayName'] = $user->getDisplayName();
        $form->populate($formValues);
        if ($this->_request->isPost() && $form->isValid($this->_getAllParams())) {
            // update the data
            $user->name = $form->getValue('displayName');
            if (empty($user->name)) {
                $user->name = $user->username;
            }
            $user->email = $form->getValue('email');
            $user->timezone = $form->getValue('timezone');
            $user->save();

            // add a flash message and redirect
            Application_Registry::getFlashMessenger()->addMessage(array(
                'type' => 'info',
                'value' => 'Your account has been updated.'
            ));
            $this->_redirect($this->view->url());
        }

        $this->view->form = $form;
        $this->view->menu = $this->_getMenu();
    }

    public function settingsAction()
    {
        $this->view->menu = $this->_getMenu();
    }

    public function passwordAction()
    {
        // if activation key is given, authenticate the user
        $auth = Zend_Auth::getInstance();
        $activationKey = $this->_request->getParam('activationKey');
        if ($activationKey) {
            $user = Sageweb_Cms_Table_User::findByActivationKey($activationKey);
            if ($user) {
                // clear the activation key and re-save
                $user->activationKey = null;
                $user->save();

                // set a flag to show the user a message that they should
                // reset thier password
                $this->view->resettingPassword = true;
            }
        }

        $user = Application_Registry::getCurrentUser();
        if ($user->role == Sageweb_Cms_User::ROLE_GUEST) {
            $url = $this->view->url(array(), 'login', true) . '?dest=' . $this->view->url();
            $this->_redirect($url);
        }

        $form = new Application_Form_Password();
        $form->populate($user->toArray());
        if ($this->_request->isPost() && $form->isValid($this->_getAllParams())) {
            // update the data
            $user->setPassword($form->getValue('password'));
            $user->save();

            // add a flash message and redirect
            Application_Registry::getFlashMessenger()->addMessage(array(
                'type' => 'info',
                'value' => 'Your password has been updated.'
            ));
            $this->_redirect($this->view->url());
        }

        $this->view->form = $form;
        $this->view->menu = $this->_getMenu();
    }

    public function notificationsAction()
    {
        $user = Application_Registry::getCurrentUser();
        if ($user->role == Sageweb_Cms_User::ROLE_GUEST) {
            throw new Zend_Controller_Action_Exception('Not logged in', 404);
        }

        $form = new Application_Form_Notifications();
        $form->populate($user->toArray());
        if ($this->_request->isPost() && $form->isValid($this->_getAllParams())) {
            // update the data
            $user->newsletter = $form->getValue('newsletter');
            $user->save();

            // add a flash message and redirect
            Application_Registry::getFlashMessenger()->addMessage(array(
                'type' => 'info',
                'value' => 'Your e-mail notification options have been updated.'
            ));
            $this->_redirect($this->view->url());
        }

        $this->view->form = $form;
        $this->view->menu = $this->_getMenu();
    }

    private function _getMenu()
    {
        $pages = array();
        $accountPage = new Zend_Navigation_Page_Mvc(array(
            'label' => 'Account',
            'route' => 'account',
            'module' => 'default',
            'controller' => 'user',
            'action' => 'account'
        ));
        $pages[] = $accountPage;

        $passwordPage = new Zend_Navigation_Page_Mvc(array(
            'label' => 'Password',
            'route' => 'account',
            'module' => 'default',
            'controller' => 'user',
            'action' => 'password'
        ));
        $pages[] = $passwordPage;

        $notificationsPage = new Zend_Navigation_Page_Mvc(array(
            'label' => 'Notifications',
            'route' => 'account',
            'module' => 'default',
            'controller' => 'user',
            'action' => 'notifications'
        ));
        $pages[] = $notificationsPage;

        return new Zend_Navigation($pages);
    }

    /**
     * Register a user account. The new user fills out the registration form
     * and receives an email with the new password and tokenized login link.
     */
    public function registerAction()
    {
        $form = new Application_Model_User_RegisterForm();
        if ($this->getRequest()->isPost()) {
            $isValid = $form->isValid($this->getRequest()->getParams());
            if ($isValid) {
                $values = $form->getValues();

                // create a random password and mail to user
                $user = new Sageweb_Cms_User();
                $user->username = $values['username'];
                $user->email = $values['email'];
                $user->role = Sageweb_Cms_User::ROLE_MEMBER;
                $user->activationKey = md5(time());

                // create a random password and stored salted hash in field
                $password = substr(md5(time()+rand(1, 10000)), 0, 8);
                $user->passwordAlgorithm = 'sha1';
                $user->passwordSalt = sha1(time());
                $user->passwordHash = sha1($password . $user->passwordSalt);
                $user->save();

                // email user login credentials
                $emailBody = $this->view->partial('emails/register.phtml', array(
                    'username' => $user->username,
                    'password' => $password,
                    'activationKey' => $user->activationKey,
                ));
                $mail = new Zend_Mail('UTF-8');
                $mail->setFrom('support@sageweb.org', 'Sageweb Team');
                $mail->addTo($user->email);
                $mail->setSubject('Sageweb.org account details');
                $mail->setBodyHtml($emailBody);
                $mail->send();

                $this->renderScript('user/register-success.phtml');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Creates a reset token an sends its via e-mail. The token allows the user
     * to login once and reset thier password.
     */
    public function resetPasswordAction()
    {
        $form = new Application_Form_PasswordReset();
        if ($this->getRequest()->isPost()) {
            $isValid = $form->isValid($this->getRequest()->getParams());
            if ($isValid) {
                $values = $form->getValues();

                // save a new activation key to the user
                $user = Sageweb_Cms_Table_User::findOneByUsername($values['username']);
                $user->activationKey = md5(time());
                $user->save();

                // email user login credentials
                $emailBody = $this->view->partial('emails/reset.phtml', array(
                    'username' => $user->username,
                    'activationKey' => $user->activationKey,
                ));
                $mailer = Application_Registry::getMailer();
                $mailer->setFrom('support@sageweb.org', 'Sageweb Support');
                $mailer->addTo($user->email);
                $mailer->setSubject('reset password request for '. $user->username);
                $mailer->setBodyHtml($emailBody);
                $mailer->send();

                $this->renderScript('user/reset-success.phtml');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Shows a login form and saves user object to Zend_Auth if authenticated
     * successfully.
     */
    public function loginAction()
    {
        // if passing a one time login key, bypass login form
        if ($this->_getParam('key')) {
            $key = $this->_getParam('key');
            $user = Sageweb_Cms_Table_User::findByActivationKey($key);
            if ($user) {
                $user->activationKey = null;
                $user->save();

                // save user to session
                Zend_Auth::getInstance()->getStorage()->write($user);
                $namespace = new Zend_Session_Namespace('user');
                $namespace->user = $user;
                Zend_Session::rememberMe();
            }

            Application_Registry::getFlashMessenger()->addMessage(array(
                'type' => 'info',
                'value' => 'Please set your new password below.'
            ));
            $this->_redirect('/account/password');
        }

        $form = new Application_Model_User_LoginForm();
        if ($this->_request->isPost()) {
            $form->populate($this->_getAllParams());

            // authenticate the user with auth adapter
            $adapter = new Application_Auth_Adapter();
            $adapter->setUsername($form->getValue('username'));
            $adapter->setPassword($form->getValue('password'));
            $result = Zend_Auth::getInstance()->authenticate($adapter);
            if ($result->isValid()) {
                // get the user object
                $username = Zend_Auth::getInstance()->getIdentity();
                $user = Sageweb_Cms_Table_User::getUser($username);

                // save user to session
                $namespace = new Zend_Session_Namespace('user');
                $namespace->user = $user;
                if ($form->getValue('remember')) {
                    Zend_Session::rememberMe();
                }

                // redirect to given destination, or to profile page
                $dest = $this->_getParam('dest');
                if (!$dest) {
                    $dest = $this->view->url(
                        array('username' => $user->username), 'profile', true
                    );
                }
                $this->_redirect(urldecode($dest));
            } elseif ($result->getCode() == Application_Auth_Result::FAILURE_BLOCKED) {
                $this->view->message = 'User has been blocked. Please contact support@sageweb.org to get re-activated.';
            } else {
                $this->view->message = 'Username and password incorrect.';
            }
        }
        $this->view->form = $form;

        $form = new Application_Model_User_RegisterForm();
        if ($this->getRequest()->isPost()) {
            $isValid = $form->isValid($this->getRequest()->getParams());
            if ($isValid) {
                $values = $form->getValues();

                // create a random password and mail to user
                $user = new Sageweb_Cms_User();
                $user->username = $values['username'];
                $user->email = $values['email'];
                $user->role = Sageweb_Cms_User::ROLE_MEMBER;
                $user->activationKey = md5(time());

                // create a random password and stored salted hash in field
                $password = substr(md5(time()+rand(1, 10000)), 0, 8);
                $user->passwordAlgorithm = 'sha1';
                $user->passwordSalt = sha1(time());
                $user->passwordHash = sha1($password . $user->passwordSalt);
                $user->save();

                // email user login credentials
                $emailBody = $this->view->partial('emails/register.phtml', array(
                    'username' => $user->username,
                    'password' => $password,
                    'activationKey' => $user->activationKey,
                ));
                $mail = new Zend_Mail('UTF-8');
                $mail->setFrom('support@sageweb.org', 'Sageweb Team');
                $mail->addTo($user->email);
                $mail->setSubject('Sageweb.org account details');
                $mail->setBodyHtml($emailBody);
                $mail->send();

                $this->renderScript('user/register-success.phtml');
            }
        }
        $this->view->registerForm = $form;
    }

    /**
     * Logs the user out and clears session data.
     */
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy();
        Zend_Session::ForgetMe();
        $this->_redirect('/');
    }
}
