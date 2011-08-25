<?php

/**
 * @property string $eventType
 * @property string $location
 * @property string $startDate
 * @property string $endDate
 * @property string $url
 *
 * @property Application_Model_Post_Post $post
 */
class Application_Model_Event_EventPost extends Doctrine_Record  {
    
    public function setTableDefinition() {
        $this->setTableName('post_event');
        $this->hasColumn('post_id as postId', 'integer');
        $this->hasColumn('type as eventType', 'string', 32);
        $this->hasColumn('location', 'string', 255);
        $this->hasColumn('start_date as startDate', 'date');
        $this->hasColumn('end_date as endDate', 'date');
        $this->hasColumn('url', 'string', 255);
    }
    
    public function setUp() {
        $this->hasOne('Application_Model_Post_Post as post', array(
            'local' => 'post_id',
            'foreign' => 'id',
        ));
    }
    
    public function getDaysUntilStart() {
        $diff = strtotime($this->startDate) - time();
        $diffInDays = floor($diff / 60 / 60 / 24);
        return $diffInDays;
    }
}
