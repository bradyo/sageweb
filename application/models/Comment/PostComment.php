<?php

/**
 * Base class for a comment post
 *
 * @property integer $id
 * @property integer $entityId
 * @property integer $rootEntityId
 * @property integer $parentEntityId
 * @property string $name
 * @property string $email
 * @property string $url
 * @property string $body
 * 
 * @property Sageweb_Entity $entity
 */
class Application_Model_Comment_CommentPost extends Sageweb_Abstract_Post
{
    private $_depth;

    public function setTableDefinition()
    {
        parent::setTableDefinition();
        $this->setTableName('post_comment');

        $this->hasColumn('root_entity_id as rootEntityId', 'integer', 4);
        $this->hasColumn('parent_entity_id as parentEntityId', 'integer', 4);
        $this->hasColumn('name', 'string', 128);
        $this->hasColumn('email', 'string', 128);
        $this->hasColumn('url', 'string', 255);
        $this->hasColumn('body', 'string');
    }

    /**
     * Gets an array of data with fields and relations needed in revision history.
     * @return array post data
     */
    public function getRevisionData() {
        $data = $this->getData();
        return $data;
    }

    public function postSave($event) {
        parent::postSave($event);

        // recompute root entity total comments
        $db = Application_Registry::getDb();
        $stmt = $db->prepare('
            SELECT COUNT(id) as commentCount FROM post_comment p
            WHERE p.root_entity_id = ?
            GROUP BY p.root_entity_id
            ');
        $stmt->execute(array($this->rootEntityId));
        $row = $stmt->fetch();
        $commentsCount = $row['commentCount'];
        $updateStmt = $db->prepare('UPDATE entity SET comments_count = ? WHERE id = ?');
        $updateStmt->execute(array($commentsCount, $this->rootEntityId));

        // recompute total comments on parent entity also, if needed
        if ($this->rootEntityId !== $this->parentEntityId) {
            $stmt = $db->prepare('
                SELECT COUNT(id) as commentCount FROM post_comment p
                WHERE p.parent_entity_id = ?
                GROUP BY p.parent_entity_id
                ');
            $stmt->execute(array($this->parentEntityId));
            $row = $stmt->fetch();
            $commentsCount = $row['commentCount'];
            $updateStmt->execute(array($commentsCount, $this->parentEntityId));
        }

        // update forum total comments
        $discussion = Sageweb_Table_Discussion::findOneByEntityId($this->rootEntityId);
        if ($discussion) {
            $stmt = $db->prepare('
                SELECT COUNT(pc.id) as commentCount FROM post_discussion p
                LEFT JOIN post_comment pc ON pc.root_entity_id = p.entity_id
                WHERE p.forum_id = ?
                GROUP BY p.forum_id
                ');
            $stmt->execute(array($discussion->forumId));
            $row = $stmt->fetch();
            $count = $row['commentCount'];
            $updateStmt = $db->prepare('UPDATE forum SET replies_count = ? WHERE id = ?');
            $updateStmt->execute(array($count, $discussion->forumId));
        }
    }

    public function setDepth($depth)
    {
        $this->_depth = $depth;
    }

    public function getDepth()
    {
        return $this->_depth;
    }
}