<?php

/**
 * Converts strings to uri slugs.
 */
class Application_Converter_Slugify
{
    public static function getSlug($value) {
        // remove redundant whitespace
        $value = preg_replace('/(\s+?)/', ' ', $value);
        $value = trim($value);
        if (empty($value)) {
            return null;
        }

        // remove articles
        $value = preg_replace('/(^|\s+)(a|an|the)(\s+|$)/i', '$1$3', $value);
        $value = trim($value);

        // replace punctuations
        $replace = array("'");
        $value = str_replace($replace, '-', $value);

        //$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        $value = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $value);
        $value = strtolower(trim($value, '-'));
        $value = preg_replace("/[\/_|+ -]+/", '-', $value);
        return $value;
    }
}
