<?php

/**
 * Base class for forum topic post.
 *
 * @property integer $forumId
 * @property string $title
 * @property string $slug
 * @property string $body
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */
class Sageweb_Cms_ForumTopic extends Sageweb_Cms_PostAbstract
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();
        $this->setTableName('post_topic');
        $this->hasColumn('forum_id as forumId', 'integer');
        $this->hasColumn('title', 'string', 128);
        $this->hasColumn('slug', 'string', 128);
        $this->hasColumn('body', 'clob');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Sageweb_Cms_Forum as forum', array(
            'local' => 'forum_id',
            'foreign' => 'id',
        ));
    }
}