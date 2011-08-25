<?php

class Application_Model_Post_PostFilter {
    
    protected $id;
    protected $publicId;
    protected $status;
    protected $isLatest;
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
        $this->isLatest = true;
        $this->types = array();
        $this->categories = array();
        $this->tags = array();
    }
}
