<?php

/**
 * Description of Tags
 *
 * @author brady
 */
class My_Converter_Tags
{
    public static function getArray($value)
    {
        $values = array();
        foreach (explode(',', $value) as $tag) {
            $tag = trim($tag);
            if (strlen($tag) > 0) {
                $values[] = $tag;
            }
        }
        return $values;
    }

    public static function getString($values)
    {
        if (is_array($values)) {
            return join(', ', $values);
        } else {
            return null;
        }
    }
}
