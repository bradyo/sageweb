<?php

class Zend_View_Helper_UserLink extends Zend_View_Helper_Abstract
{
    public function userLink($id)
    {
        $user = Sageweb_Cms_Table_User::findOneById($id);
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