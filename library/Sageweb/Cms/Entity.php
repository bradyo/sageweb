<?php

/**
 * Doctrine ORM Base class for Entity model. Entities have revision history and
 * can be voted on by users.
 *
 * @property integer $id
 * @property string $type
 * @property integer $upVotesCount
 * @property integer $downVotesCount
 * @property integer $rating
 * @property integer $commentsCount
 * @property integer $viewsCount
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */

class Sageweb_Cms_Entity extends Doctrine_Record
{
    const TYPE_ARTICLE = 'article';
    const TYPE_FILE = 'file';
    const TYPE_LINK = 'link';
    const TYPE_EVENT = 'event';
    const TYPE_DISCUSSION = 'discussion';
    const TYPE_JOB = 'job';
    const TYPE_COMMENT = 'comment';
    const TYPE_PERSON = 'person';
    const TYPE_LAB = 'lab';
    const TYPE_PAPER = 'paper';

    private static $typeOptions = array(
        self::TYPE_ARTICLE => 'Article',
        self::TYPE_FILE => 'File',
        self::TYPE_LINK => 'Link',
        self::TYPE_EVENT => 'Event',
        self::TYPE_DISCUSSION => 'Discussion',
        self::TYPE_JOB => 'Job',
        self::TYPE_COMMENT => 'Comment',
        self::TYPE_PERSON => 'Person',
        self::TYPE_LAB => 'Lab',
        self::TYPE_PAPER => 'Paper',
    );

    public function setTableDefinition()
    {
        $this->setTableName('entity');
        $this->option('type', 'INNODB');
        $this->hasColumn('id', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('type', 'string', 32);
       
        $this->hasColumn('rating', 'integer');
        $this->hasColumn('up_votes_count as upVotesCount', 'integer');
        $this->hasColumn('down_votes_count as downVotesCount', 'integer');
        $this->hasColumn('comments_count as commentsCount', 'integer');
        $this->hasColumn('views_count as viewsCount', 'integer');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Sageweb_Cms_EntityRevision as revisions', array(
            'local' => 'id',
            'foreign' => 'entity_id'
        ));
        $this->hasMany('Sageweb_Cms_EntityCategory as categories', array(
            'local' => 'id',
            'foreign' => 'entity_id'
        ));
        $this->hasMany('Sageweb_Cms_EntityTag as tags', array(
            'local' => 'id',
            'foreign' => 'entity_id'
        ));
        $this->hasMany('Sageweb_Cms_EntityVote as votes', array(
            'local' => 'id',
            'foreign' => 'entity_id',
        ));
        $this->hasMany('Sageweb_Cms_EntityView as views', array(
            'local' => 'id',
            'foreign' => 'entity_id',
        ));
    }

    public function preSave($event) {
        parent::preSave($event);

        $this->rating = $this->upVotesCount - $this->downVotesCount;
    }

    public static function getTypeOptions()
    {
        return self::$typeOptions;
    }

    public static function getTypeLabel($name)
    {
        return self::$typeOptions[$name];
    }
}