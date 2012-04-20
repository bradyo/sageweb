<?php

/**
 * Doctrine ORM Base class for forum group model.
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * 
 * @property array $forums
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */
class Sageweb_Cms_ForumGroup extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('forum_group');
        $this->option('type', 'INNODB');
        
        $this->hasColumn('id', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('name', 'string', 128);
        $this->hasColumn('slug', 'string', 128);
        $this->hasColumn('description', 'string', 255);
    }

    public function setUp()
    {
        $this->hasMany('Sageweb_Model_Orm_Forum as forums', array(
            'local' => 'id',
            'foreign' => 'forum_group_id',
        ));
    }
}