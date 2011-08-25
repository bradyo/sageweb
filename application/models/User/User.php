<?php

/**
 * @property integer $id
 * @property string $username
 * @property string $name
 * @property string $email
 * @property string $role
 * @property string $status
 * @property string $timezone
 * @property string $locale
 * @property string $passwordAlgorithm
 * @property string $passwordHash
 * @property string $passwordSalt
 * @property string $activationKey
 * @property string $createdAt
 * @property string $seenAt
 * @property string $newsletter
 * @property integer $reputation
 * @property integer $postCount
 */
class Application_Model_User_User extends Doctrine_Record
{
    /**
     * The number of seconds since last activity that the user should be
     * considered "online".
     */
    const LAST_SEEN_LIMIT = 600;

    const ROLE_GUEST = 'guest';
    const ROLE_MEMBER = 'member';
    const ROLE_MODERATOR = 'moderator';
    const ROLE_ADMIN = 'admin';

    const STATUS_ACTIVE = 'active';
    const STATUS_BLOCKED = 'blocked';
    const STATUS_DELETED = 'deleted';

    public static function getRoleChoices()
    {
        return array(
            self::ROLE_GUEST => 'Guest',
            self::ROLE_MEMBER => 'Member',
            self::ROLE_MODERATOR => 'Moderator',
            self::ROLE_ADMIN => 'Admin',
        );
    }

    public function setTableDefinition()
    {
        parent::setTableDefinition();
        $this->setTableName('user');
        $this->option('type', 'INNODB');
        
        $this->hasColumn('id', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('username', 'string', 32, array('notnull' => true));
        $this->hasColumn('name', 'string', '64');
        $this->hasColumn('email', 'string', 128, array('notnull' => true));
        $this->hasColumn('role', 'string', 32, array('notnull' => true));
        $this->hasColumn('status', 'string', 32);
        $this->hasColumn('timezone', 'string', 32);
        $this->hasColumn('locale', 'string', 32);
        $this->hasColumn('language', 'string', 2);
        $this->hasColumn('created_at as createdAt', 'timestamp');
        $this->hasColumn('seen_at as seenAt', 'timestamp');
        $this->hasColumn('password_algorithm as passwordAlgorithm', 'string', 32);
        $this->hasColumn('password_hash as passwordHash', 'string', 40);
        $this->hasColumn('password_salt as passwordSalt', 'string', 40);
        $this->hasColumn('activation_key as activationKey', 'string', 40);
        $this->hasColumn('newsletter', 'string', 32);
        $this->hasColumn('reputation', 'integer');
        $this->hasColumn('post_count as postCount', 'integer');
    }

    public function preInsert($event) {
        // disable save for guests
        if ($this->_get('role') == self::ROLE_GUEST) {
            return;
        }
        parent::preInsert($event);
    }

    public function setPassword($password)
    {
        $salt = sha1(time());
        $this->passwordAlgorithm = 'sha1';
        $this->passwordSalt = $salt;
        $this->passwordHash = sha1($password . $salt);
    }

    public function getDisplayName()
    {
        $displayName = $this->_get('name');
        if (!empty($displayName)) {
            return $displayName;
        } else {
            return $this->username;
        }
    }

    public function getRole()
    {
        if ($this->id == null) {
            return self::ROLE_GUEST;
        } else {
            return $this->_get('role');
        }
    }

    public function getRoleLabel()
    {
        $roles = self::getRoleChoices();
        return $roles[$this->getRole()];
    }

    public function isGuest()
    {
        return ($this->role == self::ROLE_GUEST);
    }

    public function isModerator()
    {
        // true if either admin or moderator
        $isModerator = ($this->role == Application_Model_User_User::ROLE_MODERATOR);
        $isAdmin = ($this->role == Application_Model_User_User::ROLE_ADMIN);
        return ($isModerator || $isAdmin);
    }

    public function isAdmin()
    {
        return ($this->role == Application_Model_User_User::ROLE_ADMIN);
    }

    public function isOnline()
    {
        $seenAtTimestamp = strtotime($this->seenAt);
        if (time() - $seenAtTimestamp < self::LAST_SEEN_LIMIT) {
            return true;
        }
        return false;
    }

    public function isBlocked()
    {
        return ($this->status == self::STATUS_BLOCKED);
    }

    public function isDeleted()
    {
        return ($this->status == self::STATUS_DELETED);
    }

    public function canEdit(Sageweb_Abstract_Post $post)
    {
        switch ($post->entity->type) {
            case Sageweb_Entity::TYPE_PAPER:
            case Sageweb_Entity::TYPE_PERSON:
            case Sageweb_Entity::TYPE_LAB:
                // allow anybody to edit posts
                return true;
                break;
            case Sageweb_Entity::TYPE_ARTICLE:
            case Sageweb_Entity::TYPE_LINK:
            case Sageweb_Entity::TYPE_FILE:
            case Sageweb_Entity::TYPE_EVENT:
            case Sageweb_Entity::TYPE_JOB:
            case Sageweb_Entity::TYPE_COMMENT:
            case Sageweb_Entity::TYPE_DISCUSSION:
//            case Sageweb_Entity::TYPE_QUESTION:
//            case Sageweb_Entity::TYPE_ANSWER:
                // allow authors and moderators to edit posts
                if ($this->isModerator() || $post->isAuthor($this)) {
                    return true;
                }
                break;
        }
        return false;
    }

    /**
     * Vote up or down an entity.
     * @param Sageweb_Entity $entity
     * @param integer $value vote value (-1 for down vote, +1 for up vote)
     */
    public function vote($entity, $value)
    {
        $value = ($value < 0) ? -1 : 1;
        $existingVote = Doctrine_Query::create()
            ->from('Application_Model_Entity_EntityVote v')
            ->where('v.userId = ? AND v.entityId = ?', array($this->id, $entity->id))
            ->limit(1)
            ->fetchOne();

        if (!$existingVote) {
            // creating a new vote
            $vote = new Application_Model_Entity_EntityVote();
            $vote->entityId = $entity->id;
            $vote->userId = $this->id;
            $vote->value = $value;
            $vote->save();
        }
        else {
            // chaning a vote
            if ($existingVote->value == -1 && $value == 1) {
                // changing down vote to up vote
                $existingVote->value = $value;
                $existingVote->save();
            } 
            else if ($existingVote->value == 1 && $value == -1) {
                // change up vote to a down vote
                $existingVote->value = $value;
                $existingVote->save();
            }
            else {
                // same value, toggle vote by removing
                $existingVote->delete();
            }
        }
        Sageweb_Table_Entity::recomputeRating($entity->id);

        // update indexes
        $post = Sageweb_Table_Entity::findPostByEntity($entity);
        $post->updateVotesCount();
    }

    public function getVote($entity)
    {
        $existingVote = Doctrine_Query::create()
            ->from('Application_Model_Entity_EntityVote v')
            ->where('v.userId = ? AND v.entityId = ?', array($this->id, $entity->id))
            ->limit(1)
            ->fetchOne();
        return $existingVote;
    }

    public function createRevision($entity, $data, $comment = null)
    {
        $revision = new Sageweb_EntityRevision();
        $revision->entityId = $entity->id;
        $revision->status = Sageweb_EntityRevision::STATUS_PENDING;
        $revision->creatorId = $this->id;
        $revision->createdAt = date('Y-m-d H:i:s');
        $revision->creatorComment = $comment;
        $revision->jsonData = Zend_Json::encode($data);
        $revision->save();
        return $revision;
    }

    public function acceptRevision($revision, $comment = null)
    {
        if (!$this->isModerator()) {
            return;
        }
        $revision->status = Sageweb_EntityRevision::STATUS_ACCEPTED;
        $revision->reviewerId = $this->id;
        $revision->reviewedAt = date('Y-m-d H:i:s');
        $revision->reviewerComment = $comment;
        $revision->save();
        $revision->apply();
        return $revision;
    }

    public function rejectRevision($revision, $comment = null)
    {
        if (!$this->isModerator()) {
            return;
        }
        $revision->status = Sageweb_EntityRevision::STATUS_REJECTED;
        $revision->reviewerId = $this->id;
        $revision->reviewedAt = date('Y-m-d H:i:s');
        $revision->reviewerComment = $comment;
        $revision->save();
        return $revision;
    }

    public function updateRevision($revision, $comment = null)
    {
        if (!$this->isModerator()) {
            return;
        }
        $revision->reviewerId = $this->id;
        $revision->reviewedAt = date('Y-m-d H:i:s');
        $revision->reviewerComment = $comment;
        $revision->save();
        if ($revision->status == Sageweb_EntityRevision::STATUS_ACCEPTED) {
            $revision->apply();
        }
        return $revision;
    }
}
