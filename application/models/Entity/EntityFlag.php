<?php

/**
 * @property integer $id
 * @property integer $entityId
 * @property integer $creatorId
 * @property string $type
 * @property string $comment
 */
class Application_Model_Entity_EntityFlag extends Doctrine_Record {
    
    const TYPE_SPAM = 'spam';
    const TYPE_INAPPROPRIATE = 'inappropriate';
    const TYPE_MIS_CATEGORIZED = 'mis-categorized';
    const TYPE_BROKEN_LINK = 'broken-link';
    const TYPE_PSEUDOSCIENCE = 'pseudoscience';
    const TYPE_OTHER = 'other';

    public function setTableDefinition() {
        $this->setTableName('entity_flag');
        $this->option('type', 'INNODB');

        $this->hasColumn('id', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('entity_id as entityId', 'integer');
        $this->hasColumn('creator_id as creatorId', 'integer');
        $this->hasColumn('created_at as createdAt', 'datetime');
        $this->hasColumn('type', 'string', 32);
        $this->hasColumn('comment', 'clob');
    }
}
