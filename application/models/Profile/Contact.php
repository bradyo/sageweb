<?php

/**
 * @property integer $id
 * @property integer $profileId
 * @property string $service
 * @property string $value
 */
class Application_Model_Profile_Contact {
    
    public function setTableDefinition() {
        $this->setTableName('contact');
        $this->option('type', 'INNODB');
        
        $this->hasColumn('id', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('profile_id as profileId', 'integer', 4, array(
            'notnull' => true
        ));
        $this->hasColumn('service', 'string', 64);
        $this->hasColumn('value', 'string', 255);
    }
}
