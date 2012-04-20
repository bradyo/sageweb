<?php

/**
 * Base class for Entity view.
 *
 * @property integer $entityId
 * @property integer $userId
 * @property string $ipAddress
 * @property string $createdAt
 * @property Sageweb_Model_Orm_User $user
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */

class Sageweb_Cms_EntityView extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('entity_view');
        $this->option('type', 'INNODB');
        
        $this->hasColumn('entity_id as entityId', 'integer');
        $this->hasColumn('user_id as userId', 'integer');
        $this->hasColumn('ip_address as ipAddress', 'string', 40);
        $this->hasColumn('created_at as createdAt', 'datetime');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Sageweb_Cms_User as user', array(
            'local' => 'user_id',
            'foreign' => 'id',
        ));
    }

    public function preSave($event)
    {
        parent::preSave($event);
        $this->createdAt = date('Y-m-d H:i:s');
    }

    public function postInsert($event)
    {
        parent::postInsert($event);

        // update entity statistics
        $entity = Doctrine_Query::create()->from('Sageweb_Cms_Entity e')
            ->where('e.id = ?', $this->entityId)
            ->limit(1)
            ->fetchOne();
        $entity->viewsCount += 1;
        $entity->save();
    }
}