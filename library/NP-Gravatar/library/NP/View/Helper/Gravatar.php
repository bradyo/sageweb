<?php
/**
 * NP-Gravatar
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is
 * bundled with this package in the file LICENSE.txt.
 */

/**
 * View helper for rendering Gravatar image URLs.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_View_Helper_Gravatar
{
    const AVATAR_SERVER = 'http://www.gravatar.com/avatar';

    /**
     * Options that are recongized by the Gravatar images service.
     * 
     * @var array
     */
    protected $_validOptions = array(
        's'=>'size',
        'd'=>'default',
        'r'=>'rating'
    );

    /**
     * Generates and returns Gravatar image URL.
     * 
     * @param string $email Email address.
     * @param array $options Options for rendering image. (optional)
     * @param string $ext File-type extension of the image. (optional)
     * @return string
     */
    public function gravatar($email, $options = null, $ext = null)
    {
        $hash = md5(strtolower(trim((string)$email)));
        $options = $this->_filterOptions((array)$options);
        $optionsQueryString = (!empty($options)) ? '?' . http_build_query($options) : '';
        $ext = ($ext !== null) ? '.' . trim(ltrim((string)$ext, '.')) : '';

        return self::AVATAR_SERVER . '/' . $hash . $ext . $optionsQueryString;
    }

    /**
     * Filters supplied options, so that there are no duplicates
     * in it.
     *
     * @param array $options
     * @return array
     */
    protected function _filterOptions($options)
    {
        foreach ($options as $key=>$value) {
            if (
                (
                    in_array($key, $this->_validOptions) //Key is in valid options?
                    //Its alias is not in supplied $options array?
                    && !isset($options[array_search($key, $this->_validOptions)])
                )
                || //Or...
                (
                    array_key_exists($key, $this->_validOptions) //Key is alias of some valid option?
                    //Its appropriate valid option in supplied $options array?
                    && !isset($options[$this->_validOptions[$key]])
                )
            ) {
                continue;
            }
            else {
                unset($options[$key]);
            }
        }

        return $options;
    }
}