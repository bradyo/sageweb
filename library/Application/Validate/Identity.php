<?php

/**
 *
 * @author brady
 */
class Application_Validate_Identity extends Zend_Validate_Abstract
{
    const INVALID = 'identityInvalid';

    /**
     * Validation failure message template definitions
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID => "Username does not exist",
    );

    public function isValid($value)
    {
        $db = Sageweb_Registry::getDb();
        $stmt = $db->prepare('
            SELECT id FROM user
            WHERE username = ?
            ');
        $stmt->execute(array($value));

        $valid = true;
        $row = $stmt->fetch();
        if (!$row) {
            $this->_error(self::INVALID);
            $valid = false;
        }
        return $valid;
    }
}


