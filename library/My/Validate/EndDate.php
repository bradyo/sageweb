<?php

class My_Validate_EndDate extends Zend_Validate_Abstract
{
    const NOT_GREATER_THAN = 'notGreaterThan';

    protected $_messageTemplates = array(
        self::NOT_GREATER_THAN => 'End date must be greater than start date'
    );

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);

        if (is_array($context)) {
            if (isset($context['startsAt']) && ($value > $context['startsAt'])) {
                return true;
            }
        } elseif (is_string($context) && ($value > $context)) {
            return true;
        }

        $this->_error(self::NOT_GREATER_THAN);
        return false;
    }

}