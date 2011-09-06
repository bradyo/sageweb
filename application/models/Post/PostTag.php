<?php

/**
 * @property integer $id
 * @property integer $postId
 * @property integer $tagId
 * @property integer $creatorId
 * @property string $reviewStatus
 * 
 * @property Application_Model_Post_Post $post
 * @property Application_Model_Post_Tag $tag
 * @property Application_Model_User_User $creator
 */
class Application_Model_Post_PostTag extends Doctrine_Record
{
    const REVIEW_STATUS_PENDING = 'pending';
    const REVIEW_STATUS_ACCEPTED = 'accepted';
    
    public function setTableDefinition() {
        $this->setTableName('post_tag');
        $this->option('type', 'INNODB');

        $this->hasColumn('id', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('post_id as postId', 'integer');
        $this->hasColumn('tag_id as tagId', 'integer');
        $this->hasColumn('creator_id as creatorId', 'integer');
        $this->hasColumn('review_status as reviewStatus', 'string', 32);
    }
    
    public function setUp() {
        $this->hasOne('Application_Model_Post_Post as post', array(
            'local' => 'post_id',
            'foreign' => 'id',
        ));
        $this->hasOne('Application_Model_Post_Tag as tag', array(
            'local' => 'tag_id',
            'foreign' => 'id',
        ));
        $this->hasOne('Application_Model_User_User as creator', array(
            'local' => 'creator_id',
            'foreign' => 'id',
        ));
    }
}
