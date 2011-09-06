<?php

/**
 * @property integer $id
 * @property string $value
 * @property string $label
 */
class Application_Model_Post_Tag extends Doctrine_Record 
{
    public function setTableDefinition() {
        $this->setTableName('tag');
        $this->option('type', 'INNODB');

        $this->hasColumn('id', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('value', 'string', 64);
        $this->hasColumn('label', 'string', 64);
    }
}
