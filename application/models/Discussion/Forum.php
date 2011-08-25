<?php

/**
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property integer $discussionsCount
 * @property integer $repliesCount
 * @property integer $lastDiscussionId
 *
 * @property Application_Model_Discussion_DiscussionPost $lastDiscussion
 * @property array $topics
 */
class Application_Model_Discussion_Forum extends Doctrine_Record
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
        $this->hasMany('Application_Model_Discussion_DiscussionPost as discussions', array(
            'local' => 'id',
            'foreign' => 'forum_id',
        ));
        $this->hasOne('Application_Model_Discussion_DiscussionPost as lastDiscussion', array(
            'local' => 'last_discussion_id',
            'foreign' => 'id'
        ));
    }
}