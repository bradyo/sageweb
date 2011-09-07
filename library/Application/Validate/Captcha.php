<?php

class Application_Validate_Captcha extends Zend_Validate_Abstract
{
    const INVALID = 'invalid';

    /**
     * Validation failure message template definitions
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID => "Verification code incorrect",
    );

    public function isValid($value)
    {
        if ($value == 'sageweb') {
            return true;
        }

        $this->_error(self::INVALID);
        return false;
    }
}


