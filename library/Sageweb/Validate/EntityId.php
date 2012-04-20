<?php

/**
 *
 * @author brady
 */
class Sageweb_Validate_EntityId extends Zend_Validate_Abstract
{
    const INVALID = 'idInvalid';

    /**
     * Validation failure message template definitions
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID => "entity does not exist",
    );

    public function isValid($value)
    {
        $db = Sageweb_Registry::getDb();
        $stmt = $db->prepare('SELECT 1 FROM entity WHERE id = ? LIMIT 1');
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


