<?php

/**
 * @property integer $id
 * @property integer $userId
 * @property string $location
 * @property string $aboutBody
 * @property string $interestsBody
 * @property string $websiteUrl
 * @property string $blogUrl
 * 
 * @property Application_Model_User_User $user
 * @property array $contacts
 */
class Application_Model_Profile_Profile extends Doctrine_Record {
    
    public function setTableDefinition() {
        $this->setTableName('profile');
        $this->option('type', 'INNODB');
        
        $this->hasColumn('id', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('user_id as userId', 'integer', 4, array('notnull' => true));
        $this->hasColumn('location', 'string', 128);
        $this->hasColumn('about_body as aboutBody', 'clob');
        $this->hasColumn('interests_body as interestsBody', 'clob');
        $this->hasColumn('website_url as websiteUrl', 'string', 255);
        $this->hasColumn('blog_url as blogUrl', 'string', 255);
    }
    
    public function setUp() {
        $this->hasOne('Application_Model_User_User as user', array(
            'local' => 'user_id',
            'foreign' => 'id'
        ));
        $this->hasMany('Application_Model_Profile_Contact as contacts', array(
            'local' => 'id',
            'foreign' => 'profile_id'
        ));
    }
}