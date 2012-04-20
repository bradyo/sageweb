<?php

class Zend_View_Helper_GetString extends Zend_View_Helper_Abstract
{
    public function getString($input)
    {
        if (is_array($input)) {
            return join(',', $input);
        } else {
            return $input;
        }
    }
}