<?php

class Application_View_Helper_FormatDate extends Zend_View_Helper_Abstract
{
    public function formatDate($mysqlDatetime, $showTime = false)
    {
        if (empty($mysqlDatetime)) {
            return null;
        }

        // TODO: add localizaton support
        $timestamp = strtotime($mysqlDatetime);

        if ($showTime) {
            return date('j F Y, g:ia', $timestamp);
        } else {
            return date('j F Y', $timestamp);
        }
    }
}