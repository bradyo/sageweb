<?php

/**
 * @property integer $entityId
 * @property Application_Model_User_User $user
 * @property Zend_Date $dateBefore
 * @property Zend_Date $dateAfter
 */
class Application_Model_User_ViewSearch {
    
    private $entityId;
    private $user;
    private $ipAddress;
    private $dateBefore;
    private $dateAfter;
    
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }   
    
    /**
     * @return Doctrine_Query
     */
    public function getQuery() {
        $query = Doctrine_Query::create()->from('Application_Model_Entity_EntityView view');
        
        if ($this->entityId != null) {
            $query->andWhere('view.entityId = ?', $this->entityId);
        }
        if ($this->user != null) {
            $query->andWhere('view.userId = ?', $this->user->getId());
        }
        if ($this->ipAddress != null) {
            $query->andWhere('view.ipAddress = ?', $this->ipAddress);
        }
        if ($this->dateAfter != null) {
            $query->andWhere('view.createdAt > ?', $this->dateAfter->getTimestamp());
        }
                
        return $query;
    }
    
}
