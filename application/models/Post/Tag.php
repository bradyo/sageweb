<?php

/**
 * @property integer $id
 * @property integer $postId
 * @property string $value
 * @property string $normalValue
 * @property integer $position
 */
class Application_Model_Post_Tag extends Doctrine_Record {
    
    public function setTableDefinition() {
        $this->setTableName('post_tag');
        $this->option('type', 'INNODB');

        $this->hasColumn('id', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('post_id as postId', 'integer');
        $this->hasColumn('value', 'string', 64);
        $this->hasColumn('normal_value as normalValue', 'string', 64);
        $this->hasColumn('position', 'integer', 4);
    }
}
