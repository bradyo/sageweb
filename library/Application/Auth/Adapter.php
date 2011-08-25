<?php

/**
 * This class authenticates against the user table and stores a user object in
 * the Zend_Auth identity if successful.
 */
class Application_Auth_Adapter implements Zend_Auth_Adapter_Interface {
    
    private $db;
    private $username;
    private $password;

    public function __construct($db) {
        $this->db = $db;
    }
    
    public function setUsername($username) {
        $this->username = $username;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function authenticate()  {
        $stmt = $db->prepare('SELECT * FROM user WHERE username = ?');
        $stmt->execute(array($this->username));

        // if we have a user, check the hashed password
        $row = $stmt->fetch();
        if ($row) {
            $userId = $row['id'];
            $algorithm = $row['password_algorithm'];
            
            $hashedPassword = $this->password;
            if ($algorithm == 'sha1') {
                $hashedPassword = sha1($this->password . $row['password_salt']);
            } elseif ($algorithm == 'md5') {
                $hashedPassword = md5($this->password . $row['password_salt']);
            }

            if ($hashedPassword == $row['password_hash']) {
                if ($row['status'] == Application_Model_User_User::STATUS_BLOCKED) {
                    return new Application_Auth_Result(Application_Auth_Result::FAILURE_BLOCKED, null);
                }
                return new Application_Auth_Result(Application_Auth_Result::SUCCESS, $this->username);
            }
        }
        return new Application_Auth_Result(Application_Auth_Result::FAILURE, null);
    }
}
