<?php

/**
 * Doctrine ORM Base class for a post.
 * 
 * @property integer $id
 * @property integer $entityId
 * @property integer $authorId
 * @property string $status
 * @property array $votes
 * @property array $views
 * @property array $categories
 * @property array $tags
 * @property datetime $createdAt
 * @property datetime $updatedAt
 *
 * @property Sageweb_Cms_Entity $entity
 * @property Sageweb_Cms_User $author
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */
abstract class Sageweb_Cms_Abstract_Post extends Doctrine_Record
{
    const STATUS_PENDING = 'pending';
    const STATUS_PUBLIC = 'public';
    const STATUS_HELD = 'held';
    const STATUS_DELETED = 'deleted';

    public function setTableDefinition()
    {
        parent::setTableDefinition();
        $this->option('type', 'INNODB');
        
        $this->hasColumn('id', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('entity_id as entityId', 'integer');
        $this->hasColumn('author_id as authorId', 'integer');
        $this->hasColumn('status', 'string', 32);
        $this->hasColumn('created_at as createdAt', 'datetime');
        $this->hasColumn('updated_at as updatedAt', 'datetime');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Sageweb_Cms_Entity as entity', array(
            'local' => 'entity_id',
            'foreign' => 'id',
        ));
        $this->hasOne('Sageweb_Cms_User as author', array(
            'local' => 'author_id',
            'foreign' => 'id',
        ));
        $this->hasMany('Sageweb_Cms_EntityCategory as categories', array(
            'local' => 'entity_id',
            'foreign' => 'entity_id'
        ));
        $this->hasMany('Sageweb_Cms_EntityTag as tags', array(
            'local' => 'entity_id',
            'foreign' => 'entity_id'
        ));
        $this->hasMany('Sageweb_Cms_EntityVote as votes', array(
            'local' => 'entity_id',
            'foreign' => 'entity_id',
        ));
        $this->hasMany('Sageweb_Cms_EntityView as views', array(
            'local' => 'entity_id',
            'foreign' => 'entity_id',
        ));
    }

    /**
     * Gets an array of data with fields and relations needed in revision history.
     * @return array post data
     */
    public function getRevisionData()
    {
        return $this->getData();
    }

    public function getCategoryValues()
    {
        $values = array();
        foreach ($this->categories as $obj) {
            $values[] = $obj->value;
        }
        return $values;
    }

    public function getTagValues()
    {
        $values = array();
        foreach ($this->tags as $obj) {
            $values[] = $obj->value;
        }
        return $values;
    }

    public function getCategoryString()
    {
        $categoryValues = array('all');
        foreach ($this->categories as $category) {
            $categoryValues[] = $category['value'];
        }
        return join(',', $categoryValues);
    }

    public function getTagsString()
    {
        $tagValues = array();
        foreach ($this->tags as $tag) {
            $tagValues[] = strtolower($tag['value']);
        }
        return join(',', $tagValues);
    }

    public static function getStatusOptions()
    {
        return array(
            self::STATUS_PUBLIC => 'Public',
            self::STATUS_HELD => 'Held',
            self::STATUS_DELETED => 'Deleted',
        );
    }

    public function preSave($event)
    {
        parent::preSave($event);
        if (!$this->status) {
            $this->status = Sageweb_Cms_Abstract_Post::STATUS_PENDING;
        }
    }

    public function updateIndex($commit = false)
    {
    }

    public function updateSiteIndex($commit = false)
    {
    }

    public function isPublic()
    {
        return ($this->status == self::STATUS_PUBLIC);
    }

    public function isPending()
    {
        return ($this->status == self::STATUS_PENDING);
    }

    public function isDeleted()
    {
        return ($this->status == self::STATUS_DELETED);
    }

    public function isHeld()
    {
        return ($this->status == self::STATUS_HELD);
    }

    public function isAuthor($user)
    {
        $isAuthor = false;
        if ($user !== null && !$user->isGuest()) {
            if ($user->id === $this->_get('authorId')) {
                $isAuthor = true;
            }
        }
        return $isAuthor;
    }

    public function incrementViews($user)
    {
        // check if view already exists in the last 30 min
        $date = new Zend_Date(time());
        $date->subMinute(30);
        $query = Doctrine_Query::create()->from('Sageweb_Cms_EntityView v')
            ->where('v.entityId = ?', $this->entityId)
            ->andWhere('v.ipAddress = ?', $_SERVER['REMOTE_ADDR'])
            ->andWhere('v.createdAt > ?', date('Y-m-d H:i:s', $date->getTimestamp()))
            ->limit(1);
        $recentView = $query->fetchOne();

        if (!$recentView) {
            // record the view
            $view = new Sageweb_Cms_EntityView();
            $view->entityId = $this->entityId;
            $view->ipAddress = $_SERVER['REMOTE_ADDR'];
            $view->userId = $user->id;
            $view->save();
        }

        // update Index (stores counts)
        $this->updateIndex(true);
    }

    public function updateVotesCount()
    {
        // fetch up votes
        $stmt = Sageweb_Registry::getDb()->prepare('
            SELECT COUNT(id) as up_votes_count FROM entity_vote
            WHERE entity_id = ? AND value = 1
            GROUP BY entity_id
            ');
        $stmt->execute(array($this->entityId));
        $row = $stmt->fetch();
        $this->entity->upVotesCount = $row['up_votes_count'];

        // fetch up votes
        $stmt = Sageweb_Registry::getDb()->prepare('
            SELECT COUNT(id) as down_votes_count FROM entity_vote
            WHERE entity_id = ? AND value = -1
            GROUP BY entity_id
            ');
        $stmt->execute(array($this->entityId));
        $row = $stmt->fetch();
        $this->entity->downVotesCount = $row['down_votes_count'];

        $this->entity->save();

        // update Index (stores vote counts)
        $this->updateIndex(true);
    }

    public function updateCommentsCount()
    {
        $stmt = Sageweb_Registry::getDb()->prepare('
            SELECT COUNT(id) as comments_count FROM post_comment
            WHERE root_entity_id = ?
            GROUP BY root_entity_id
            ');
        $stmt->execute(array($this->entityId));
        $row = $stmt->fetch();
        $this->entity->commentsCount = $row['comments_count'];
        $this->entity->save();

        // update Index (stores counts)
        $this->updateIndex(true);
    }
}
