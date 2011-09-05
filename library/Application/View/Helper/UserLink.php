<?php

class Application_View_Helper_UserLink extends Zend_View_Helper_Abstract
{
    public function userLink($userId) {
        $userRepository = Application_Registry::getUserRepostiory();
        $user = $userRepository->getOneById($userId);
        if (!$user) {
            return 'anonymous';
        } else {
            $url = $this->view->url(array('username' => $user->username), 'profile', true);
            $label = $this->view->escape($user->getDisplayName());
            $html = '<a href="' . $url . '">' . $label . '</a>';
            return $html;
        }
    }
}