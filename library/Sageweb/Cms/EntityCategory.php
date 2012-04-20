<?php

/**
 * Doctrine ORM Base class for entity categories.
 *
 * @property integer $id
 * @property integer $entityId
 * @property string $value
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */
class Sageweb_Cms_EntityCategory extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('entity_category');
        $this->option('type', 'INNODB');

        $this->hasColumn('entity_id as entityId', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('value', 'string', 32, array(
            'primary' => true
        ));
    }
}
