<?php

/**
 * Base class for entity revision.
 *
 * @property integer $id
 * @property integer $entityId
 * @property string $entityType
 * @property string $status
 * @property integer $creatorId
 * @property datetime $createdAt
 * @property string $creatorComment
 * @property integer $reviewerId
 * @property datetime $reviewedAt
 * @property string $reviewerComment
 * @property string $jsonData
 * 
 * @property Sageweb_Cms_Entity $entity
 * @property Sageweb_Cms_User $creator
 * @property Sageweb_Cms_User $reviewer
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */
class Sageweb_Cms_EntityRevision extends Doctrine_Record
{
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_DELETED = 'deleted';

    public function setTableDefinition()
    {
        $this->setTableName('entity_revision');
        $this->option('type', 'INNODB');
        
        $this->hasColumn('id', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('entity_id as entityId', 'integer', 4);
        $this->hasColumn('status', 'string', 32, array(
            'default' => self::STATUS_PENDING
        ));
        $this->hasColumn('creator_id as creatorId', 'integer', 4);
        $this->hasColumn('created_at as createdAt', 'datetime');
        $this->hasColumn('creator_comment as creatorComment', 'clob');
        $this->hasColumn('reviewer_id as reviewerId', 'integer', 4);
        $this->hasColumn('reviewed_at as reviewedAt', 'datetime');
        $this->hasColumn('reviewer_comment as reviewerComment', 'clob');
        $this->hasColumn('json_data as jsonData', 'clob');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Sageweb_Cms_Entity as entity', array(
            'local' => 'entity_id',
            'foreign' => 'id',
        ));
        $this->hasOne('Sageweb_Cms_User as creator', array(
            'local' => 'creator_id',
            'foreign' => 'id',
        ));
        $this->hasOne('Sageweb_Cms_User as reviewer', array(
            'local' => 'reviewer_id',
            'foreign' => 'id',
        ));
    }

    public static function getStatusChoices()
    {
        return array(
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_DELETED => 'Deleted'
        );
    }

    public function getStatusLabel($status)
    {
        $choices = self::getStatusChoices();
        return $choices[$status];
    }

    public function apply()
    {
        $entity = Sageweb_Cms_Table_Entity::findOneById($this->entityId);
        $post = Sageweb_Cms_Table_Entity::findPostByEntity($entity);
        $data = Zend_Json::decode($this->jsonData);
        Sageweb_Cms_Table_Entity::updatePost($post, $data);
    }
}
