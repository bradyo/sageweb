<?php

/**
 * @property string $eventType
 * @property string $location
 * @property string $startDate
 * @property string $endDate
 * @property string $url
 */
class Application_Model_Event_Event extends Doctrine_Record  {
    
    public function setTableDefinition() {
        $this->setTableName('post_event');
        $this->hasColumn('post_id as postId', 'integer');
        $this->hasColumn('type as eventType', 'string', 32);
        $this->hasColumn('location', 'string', 255);
        $this->hasColumn('start_date as startDate', 'date');
        $this->hasColumn('end_date as endDate', 'date');
        $this->hasColumn('url', 'string', 255);
    }
    
    public function getDaysUntilStart() {
        $diff = strtotime($this->startDate) - time();
        $diffInDays = floor($diff / 60 / 60 / 24);
        return $diffInDays;
    }
}
