<?php

/**
 * Base class for forum.
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property integer $discussionsCount
 * @property integer $repliesCount
 * @property integer $lastDiscussionId
 * 
 * @property Sageweb_Cms_PostDiscussion $lastDiscussion
 * @property array $topics
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */
class Sageweb_Cms_Forum extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('forum');
        $this->option('type', 'INNODB');
        $this->hasColumn('id', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('title', 'string', 128);
        $this->hasColumn('slug', 'string' , 128);
        $this->hasColumn('description', 'string', 255);
        $this->hasColumn('discussions_count as discussionsCount', 'integer');
        $this->hasColumn('replies_count as repliesCount', 'integer');
        $this->hasColumn('last_discussion_id as lastDiscussionId', 'integer');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Sageweb_Cms_PostDiscussion as discussions', array(
            'local' => 'id',
            'foreign' => 'forum_id',
        ));
        $this->hasOne('Sageweb_Cms_PostDiscussion as lastDiscussion', array(
            'local' => 'last_discussion_id',
            'foreign' => 'id'
        ));
    }
}