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
 * Provides API for easier access to the profile properties.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
abstract class NP_Service_Gravatar_Profiles_Profile_Abstract implements ArrayAccess
{
    /**
	 * Constructor.
	 *
	 * @param array Options for object's initialization.
	 * @return void
	 */
    public function __construct(array $options = null)
    {
        if ($options) {
            $this->setOptions($options);
        }
    }

    /**
	 * Sets object state.
	 *
	 * @param array Object members.
	 * @return NP_Service_Gravatar_Profiles_Profile_Abstract
	 */
	public function setOptions(array $options)
    {
        foreach ($options as $key=>$value) {
			$this->$key = $value;
        }

        return $this;
    }

    /**
	 * Allows access to properties of this class.
	 *
	 * @param string Name of the property.
	 * @param mixed Value that will be set.
	 * @return mixed
	 */
	public function __set($name, $value)
    {
        $method = 'set' . ucfirst($name);
        
        if (method_exists($this, $method)) { //Only if property exists.
             return $this->$method($value);
        }
    }

	/**
	 * Allows access to properties of this class.
	 *
	 * @param string Name of the property.
	 * @return mixed
	 */
	public function __get($name)
    {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) { //Only if property exists.
            return $this->$method();
        }
        else {
            return null;
        }
    }

    //ArrayAccess interface implementation

	/**
	 * Required by the ArrayAccess implementation.
	 * Gets object member for the given offset.
	 *
	 * @param string Offset.
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
        return $this->$offset;
    }

	/**
	 * Required by the ArrayAccess implementation.
	 *
	 * @param string Offset.
	 * @param mixed Value to set.
	 * @return Model_Base
	 */
	public function offsetSet($offset, $value)
	{
		$this->$offset = $value;
    }

	/**
	 * Required by the ArrayAccess implementation.
	 * Checks if offset exists.
	 *
	 * @param string Offset.
	 * @return bool
	 */
    public function offsetExists($offset)
	{
        return (null === $this->$offset);
    }

	/**
	 * Required by the ArrayAccess implementation.
	 * Does nothing.
	 *
	 * @param string Offset.
	 * @return void
	 */
    public function offsetUnset($offset)
	{
    }
}