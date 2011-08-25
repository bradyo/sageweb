<?php

/**
 * @property integer $entityId
 * @property integer $userId
 * @property integer $value
 * 
 * @property Application_Model_User_User $user
 */
class Application_Model_Entity_EntityVote extends Doctrine_Record {
    
    public function setTableDefinition() {
        $this->setTableName('entity_vote');
        $this->option('type', 'INNODB');
        
        $this->hasColumn('entity_id as entityId', 'integer');
        $this->hasColumn('user_id as userId', 'integer');
        $this->hasColumn('value as value', 'integer');
    }
    
    public function setUp() {
        $this->hasOne('Application_Model_User_User as user', array(
            'local' => 'user_id',
            'foreign' => 'id',
        ));
    }
}