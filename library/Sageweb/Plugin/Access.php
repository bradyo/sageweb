<?php

class Sageweb_Plugin_Access extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        parent::dispatchLoopStartup($request);

        // if the user is logged in, update thier last seen time at each request
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $user = Sageweb_Registry::getUser();
            if (! $user->isGuest()) {
                $user->seenAt = date("Y-m-d H:i:s", time());
                $user->save();
            }
        }
    }

}
