<?php

/**
 * @property integer $id
 * @property integer $publicId
 * @property integer $version
 * @property string $status
 * @property boolean $isCurrent
 * @property string $search
 * @property string $orderBy
 * @property array $types
 * @property array $categories
 * @property array $tags
 */
class Application_Model_Post_PostFilter {
    
    protected $id;
    protected $publicId;
    protected $version;
    protected $status;
    protected $isCurrent;
    protected $search;
    protected $orderBy;
    protected $types;
    protected $categories;
    protected $tags;
    
    public function __get($property) {
        return $this->$property;
    }
    public function __set($property, $value) {
        $this->$property = $value;
    }
    
    public function __construct() {
        $this->orderBy = 'post.whenAt desc';
        $this->isCurrent = true;
        $this->types = array();
        $this->categories = array();
        $this->tags = array();
    }
}
