<?php

class My_Navigation_Page_MvcCount extends Zend_Navigation_Page_Mvc
{
    private $_count;

    public function __construct($options = null) 
    {
        if (isset($options['count'])) {
            $this->_count = $options['count'];
            unset($options['count']);
        }
        parent::__construct($options);
    }

    public function getCount()
    {
        return $this->_count;
    }
}
