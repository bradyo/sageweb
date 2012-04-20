<?php

class Sageweb_Cms_Table_Entity
{
    public static function createPost($entityType, array $data)
    {
        $entity = new Sageweb_Cms_Entity();
        $entity->type = $entityType;
        $entity->save();

        $object = self::_getPostObject($entityType);
        $ormData = self::_getOrmData($entity->id, $data);
        $object->fromArray($ormData);
        $object->entityId = $entity->id;
        $object->status = Sageweb_Cms_Abstract_Post::STATUS_PENDING;
        $object->createdAt = date('Y-m-d H:i:s');
        $object->save();
        return $object;
    }

    public static function updatePost(Sageweb_Cms_Abstract_Post $object, array $data)
    {
        // drop existing tags and categories
        Doctrine_Query::create()->delete('Sageweb_Cms_EntityCategory c')
            ->where('c.entityId = ?', $object->entityId)
            ->execute();
        Doctrine_Query::create()->delete('Sageweb_Cms_EntityTag t')
            ->where('t.entityId = ?', $object->entityId)
            ->execute();
        $object->clearRelated();

        // convert revision data into orm compatable data and update
        $ormData = self::_getOrmData($object->entityId, $data);
        $object->fromArray($ormData);
        $object->save();
    }

    private static function _getOrmData($entityId, $data)
    {
        // convert categories to doctrine object compatable arrays
        $ormData = $data;
        if (isset($data['categories'])) {
            $ormData['categories'] = array();
            foreach ($data['categories'] as $value) {
                $ormData['categories'][] = array(
                    'entityId' => $entityId,
                    'value' => $value
                );
            }
        }
        // convert tags to doctrine object compatable arrays
        if (isset($data['tags'])) {
            $ormData['tags'] = array();
            $position = 0;
            foreach ($data['tags'] as $value) {
                $ormData['tags'][] = array(
                    'entityId' => $entityId,
                    'position' => $position,
                    'value' => $value
                );
                $position++;
            }
        }
        return $ormData;
    }


    /**
     * @param Sageweb_Cms_Entity $entity
     * @return Sageweb_Cms_Abstract_Post
     */
    public static function findPostByEntity($entity)
    {
        switch ($entity->type) {
            case Sageweb_Cms_Entity::TYPE_ARTICLE:
                return Sageweb_Cms_Table_Article::findOneByEntityId($entity->id);
                break;
            case Sageweb_Cms_Entity::TYPE_FILE:
                return Sageweb_Cms_Table_File::findOneByEntityId($entity->id);
                break;
            case Sageweb_Cms_Entity::TYPE_LINK:
                return Sageweb_Cms_Table_Link::findOneByEntityId($entity->id);
                break;
            case Sageweb_Cms_Entity::TYPE_EVENT:
                return Sageweb_Cms_Table_Event::findOneByEntityId($entity->id);
                break;
            case Sageweb_Cms_Entity::TYPE_DISCUSSION:
                return Sageweb_Cms_Table_Discussion::findOneByEntityId($entity->id);
                break;
            case Sageweb_Cms_Entity::TYPE_COMMENT:
                return Sageweb_Cms_Table_Comment::findOneByEntityId($entity->id);
                break;
            case Sageweb_Cms_Entity::TYPE_PAPER:
                return Sageweb_Cms_Table_Paper::findOneByEntityId($entity->id);
                break;
            case Sageweb_Cms_Entity::TYPE_PERSON:
                return Sageweb_Cms_Table_Person::findOneByEntityId($entity->id);
                break;
            case Sageweb_Cms_Entity::TYPE_LAB:
                return Sageweb_Cms_Table_Lab::findOneByEntityId($entity->id);
                break;
        }
    }

    /**
     * @param integer $id
     * @return Sageweb_Cms_Entity
     */
    public static function findOneById($id)
    {
        $query = Doctrine_Query::create()->from('Sageweb_Cms_Entity e');
        $query->where ('e.id = ?', $id);
        $query->limit(1);
        return $query->fetchOne();
    }

    /**
     * @param  $entityType
     * @return Sageweb_Cms_Abstract_Post
     */
    private static function _getPostObject($entityType)
    {
        /* @var $object Doctrine_Record  */
        $object = null;
        switch ($entityType) {
            case Sageweb_Cms_Entity::TYPE_ARTICLE:
                $object = new Sageweb_Cms_PostArticle();
                break;
            case Sageweb_Cms_Entity::TYPE_LINK:
                $object = new Sageweb_Cms_PostLink();
                break;
            case Sageweb_Cms_Entity::TYPE_FILE:
                $object = new Sageweb_Cms_PostFile();
                break;
            case Sageweb_Cms_Entity::TYPE_EVENT:
                $object = new Sageweb_Cms_PostEvent();
                break;
            case Sageweb_Cms_Entity::TYPE_DISCUSSION:
                $object = new Sageweb_Cms_PostDiscussion();
                break;
            case Sageweb_Cms_Entity::TYPE_COMMENT:
                $object = new Sageweb_Cms_PostComment();
                break;
            case Sageweb_Cms_Entity::TYPE_PAPER:
                $object = new Sageweb_Cms_PostPaper();
                break;
            case Sageweb_Cms_Entity::TYPE_LAB:
                $object = new Sageweb_Cms_PostLab();
                break;
            case Sageweb_Cms_Entity::TYPE_PERSON:
                $object = new Sageweb_Cms_PostPerson();
                break;
            default:
                throw new Zend_Exception('Entity type "'. $entity->type . '" does not exist');
        }
        return $object;
    }

    public static function recomputeRating($entityId)
    {
        // recompute entity rating by votes
        $db = Sageweb_Registry::getDb();
        $stmt = $db->prepare('
            SELECT COUNT(id) as voteCount
            FROM entity_vote v
            WHERE v.entity_id = ? AND v.value = ?
            GROUP BY v.entity_id
            ');
        $stmt->execute(array($entityId, 1));
        $row = $stmt->fetch();
        $upVotesCount = $row['voteCount'];

        $db = Sageweb_Registry::getDb();
        $stmt = $db->prepare('
            SELECT COUNT(id) as voteCount
            FROM entity_vote v
            WHERE v.entity_id = ? AND v.value = ?
            GROUP BY v.entity_id
            ');
        $stmt->execute(array($entityId, -1));
        $row = $stmt->fetch();
        $downVotesCount = $row['voteCount'];

        $entity = Sageweb_Cms_Table_Entity::findOneById($entityId);
        $entity->upVotesCount = $upVotesCount;
        $entity->downVotesCount = $downVotesCount;
        $entity->rating = $upVotesCount - $downVotesCount;
        $entity->save();
    }
}
