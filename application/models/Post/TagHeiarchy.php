<?php

/**
 * @property string $namespace
 * @property integer $tagId
 * @property integer $parentTagId
 * 
 * @property Application_Model_Post_Tag $tag
 * @property Application_Model_Post_Tag $parentTag
 */
class Application_Model_Post_TagHeiarchy extends Doctrine_Record 
{
    public function setTableDefinition() {
        $this->setTableName('tag_heiarchy');
        $this->option('type', 'INNODB');

        $this->hasColumn('id', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('namespace', 'string', 64);
        $this->hasColumn('tag_id as tagId', 'integer');
        $this->hasColumn('parent_tag_id as parentTagId', 'integer');
    }
    
    public function setUp() {
        $this->hasOne('Application_Model_Post_Tag as tag', array(
            'local' => 'tag_id',
            'foreign' => 'id',
        ));
        $this->hasOne('Application_Model_Post_Tag as parentTag', array(
            'local' => 'parent_tag_id',
            'foreign' => 'id',
        ));
    }
}
