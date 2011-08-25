<?php

class Application_Auth_Result extends Zend_Auth_Result {
    
    const FAILURE =  0;
    const FAILURE_IDENTITY_NOT_FOUND = -1;
    const FAILURE_CREDENTIAL_INVALID = -2;
    const FAILURE_BLOCKED  = -3;
    const SUCCESS =  1;

    protected $_code;
    protected $_identity;
    protected $_messages;

    /**
     * Sets the result code, identity, and failure messages
     *
     * @param int $code
     * @param mixed $identity
     * @param array $messages
     * @return void
     */
    public function __construct($code, $identity, array $messages = array()) {
        $this->_code     = (int) $code;
        $this->_identity = $identity;
        $this->_messages = $messages;
    }

    /**
     * Returns whether the result represents a successful authentication attempt
     *
     * @return boolean
     */
    public function isValid() {
        return ($this->_code > 0) ? true : false;
    }

    /**
     * getCode() - Get the result code for this authentication attempt
     *
     * @return int
     */
    public function getCode() {
        return $this->_code;
    }

    /**
     * Returns the identity used in the authentication attempt
     *
     * @return mixed
     */
    public function getIdentity() {
        return $this->_identity;
    }

    /**
     * Returns an array of string reasons why the authentication attempt was unsuccessful
     *
     * If authentication was successful, this method returns an empty array.
     *
     * @return array
     */
    public function getMessages() {
        return $this->_messages;
    }
}
