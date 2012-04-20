<?php

/**
 * @property integer $forumId
 * @property string $title
 * @property string $slug
 * @property string $body
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */
class Sageweb_Cms_PostDiscussion extends Sageweb_Cms_Abstract_Post
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();
        $this->setTableName('post_discussion');
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

    public function getRevisionData()
    {
        $data = $this->getData();
        $data['tags'] = $this->getTagValues();
        return $data;
    }


    public function preSave($event)
    {
        parent::preSave($event);
        $this->slug = My_Converter_Slugify::getSlug($this->_get('title'));
    }

    public function postSave($event)
    {
        parent::postSave($event);

        // recompute forum discussions count
        $db = Sageweb_Registry::getDb();
        $stmt = $db->prepare('
            SELECT COUNT(id) as discussionsCount FROM post_discussion p
            WHERE p.forum_id = ?
            GROUP BY p.forum_id
            ');
        $stmt->execute(array($this->forumId));
        $row = $stmt->fetch();
        $count = $row['discussionsCount'];
        $updateStmt = $db->prepare('UPDATE forum SET discussions_count = ? WHERE id = ?');
        $updateStmt->execute(array($count, $this->forumId));

        // update index
        $this->updateIndex(true);
        $this->updateSiteIndex(true);
    }

    /**
     * Updates the search document for this post. Removes the document and
     * recreates it as required by Zend_Search_Lucene.
     *
     * @param boolean $commit If the index should be committed to file. Set this
     *   true if updating one record so that $index->commit() is called
     */
    public function updateIndex($commit = false)
    {
        $index = Zend_Registry::get('discussionIndex');

        $term = new Zend_Search_Lucene_Index_Term($this->entityId, 'entityId');
        $docIds  = $index->termDocs($term);
        foreach ($docIds as $docId) {
            $index->delete($docId);
        }

        if ($this->status == self::STATUS_PUBLIC) {
            $doc = new Zend_Search_Lucene_Document();
            $doc->addField(Zend_Search_Lucene_Field::text('id', $this->id));
            $doc->addField(Zend_Search_Lucene_Field::text('entityId', $this->entityId));
            $doc->addField(Zend_Search_Lucene_Field::text('slugId', $this->getSlugId()));
            $doc->addField(Zend_Search_Lucene_Field::text('authorId', $this->authorId));
            $doc->addField(Zend_Search_Lucene_Field::text('createdAt', $this->createdAt));
            $doc->addField(Zend_Search_Lucene_Field::text('rating', $this->entity->rating));
            $doc->addField(Zend_Search_Lucene_Field::text('upVotesCount', $this->entity->upVotesCount));
            $doc->addField(Zend_Search_Lucene_Field::text('downVotesCount', $this->entity->downVotesCount));
            $doc->addField(Zend_Search_Lucene_Field::text('viewsCount', $this->entity->viewsCount));
            $doc->addField(Zend_Search_Lucene_Field::text('commentsCount', $this->entity->commentsCount));
            $doc->addField(Zend_Search_Lucene_Field::text('forumId', $this->forumId));
            $doc->addField(Zend_Search_Lucene_Field::text('title', $this->title));
            $doc->addField(Zend_Search_Lucene_Field::unStored('body', $this->body));
            $doc->addField(Zend_Search_Lucene_Field::text('tags', $this->getTagsString()));
            $index->addDocument($doc);
        }
        if ($commit) {
            $index->commit();
        }
    }

    public function getSlugId()
    {
        $slug = $this->_get('slug');
        if (!empty($slug)) {
            return $this->_get('id') . '-' . $this->_get('slug');
        } else {
            return $this->_get('id');
        }
    }

    public function updateSiteIndex($commit = false)
    {
        $index = Zend_Registry::get('siteIndex');
        $term = new Zend_Search_Lucene_Index_Term($this->entityId, 'entityId');
        $docIds  = $index->termDocs($term);
        foreach ($docIds as $docId) {
            $index->delete($docId);
        }
        if ($this->status == self::STATUS_PUBLIC) {
            $doc = new Zend_Search_Lucene_Document();
            $doc->addField(Zend_Search_Lucene_Field::text('id', $this->id));
            $doc->addField(Zend_Search_Lucene_Field::text('entityId', $this->entityId));
            $doc->addField(Zend_Search_Lucene_Field::text('entityType', 'discussion'));
            $doc->addField(Zend_Search_Lucene_Field::text('title', $this->title));

            $body = join(' ', array(
                strip_tags($this->body),
            ));
            $doc->addField(Zend_Search_Lucene_Field::unStored('body', $body));
            $index->addDocument($doc);
        }
        if ($commit) {
            $index->commit();
        }
    }
}
