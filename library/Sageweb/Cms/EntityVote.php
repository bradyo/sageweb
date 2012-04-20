<?php

/**
 * Base class for Entity vote.
 *
 * @property integer $entityId
 * @property integer $userId
 * @property integer $value
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */

class Sageweb_Cms_EntityVote extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('entity_vote');
        $this->option('type', 'INNODB');
        
        $this->hasColumn('entity_id as entityId', 'integer');
        $this->hasColumn('user_id as userId', 'integer');
        $this->hasColumn('value as value', 'integer');
    }
}