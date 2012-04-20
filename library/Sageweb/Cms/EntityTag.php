<?php

/**
 * Base class for entity tag.
 *
 * @property integer $id
 * @property integer $entityId
 * @property integer $position
 * @property string $value
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */
class Sageweb_Cms_EntityTag extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('entity_tag');
        $this->option('type', 'INNODB');

        $this->hasColumn('entity_id as entityId', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('position', 'integer', 4, array(
            'primary' => true
        ));
        $this->hasColumn('value', 'string', 32, array(
            'primary' => true
        ));
    }
}