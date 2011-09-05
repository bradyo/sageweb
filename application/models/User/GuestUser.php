<?php

class Application_Model_User_GuestUser extends Application_Model_User_User {
    
    public function save(Doctrine_Connection $conn = null) {
        throw new Exception("Cannot save guest users");
    }

    public function construct() {
        parent::construct();
        $this->role = self::ROLE_GUEST;
    }
}
