<?php

/**
 * Base class for event post.
 *
 * @property string $title
 * @property string $slug
 * @property string $summary
 * @property boolean $isFeatured
 * @property string $type
 * @property string $location
 * @property string $startsAt
 * @property string $endsAt
 * @property string $url
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */
class Sageweb_Cms_PostEvent extends Sageweb_Cms_Abstract_Post
{
    private static $types = array(
        'conference' => 'Conference',
        'grant-deadline' => 'Grant Deadline',
        'talk' => 'Talk',
        'training-course' => 'Training Course',
        'workshop' => 'Workshop',
        'other' => 'Other',
    );

    public function setTableDefinition()
    {
        parent::setTableDefinition();
        $this->setTableName('post_event');

        $this->hasColumn('title', 'string', 128);
        $this->hasColumn('slug', 'string', 128);
        $this->hasColumn('summary', 'clob');
        $this->hasColumn('is_featured as isFeatured', 'boolean');
        $this->hasColumn('body', 'clob');
        $this->hasColumn('type as type', 'string', 32);
        $this->hasColumn('location as location', 'string', 255);
        $this->hasColumn('starts_at as startsAt', 'date');
        $this->hasColumn('ends_at as endsAt', 'date');
        $this->hasColumn('url as url', 'string', 255);
    }

    public static function getTypeChoices()
    {
        return self::$types;
    }

    public static function getTypeLabel($type)
    {
        return self::$types[$type];
    }

    public function preSave($event)
    {
        parent::preSave($event);
        $this->slug = My_Converter_Slugify::getSlug($this->_get('title'));
    }

    public function postSave($event)
    {
        parent::postSave($event);
        $this->updateIndex(true);
        $this->updateSiteIndex(true);
    }

    /**
     * Gets an array of data with fields and relations needed in revision history.
     * @return array post data
     */
    public function getRevisionData() {
        $data = $this->getData();
        $data['tags'] = $this->getTagValues();
        return $data;
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

    public function getDaysUntilStarts()
    {
        // TODO: add localizaton support
        $startTimestamp = strtotime($this->startsAt);
        $nowTimestamp = time();
        $diff = $startTimestamp - $nowTimestamp;
        $diffInDays = floor($diff / 60 / 60 / 24);
        return $diffInDays;
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
        $index = Zend_Registry::get('eventIndex');

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
            $doc->addField(Zend_Search_Lucene_Field::unIndexed('authorId', $this->authorId));
            $doc->addField(Zend_Search_Lucene_Field::unIndexed('createdAt', $this->createdAt));
            $doc->addField(Zend_Search_Lucene_Field::text('rating', $this->entity->rating));
            $doc->addField(Zend_Search_Lucene_Field::text('upVotesCount', $this->entity->upVotesCount));
            $doc->addField(Zend_Search_Lucene_Field::text('downVotesCount', $this->entity->downVotesCount));
            $doc->addField(Zend_Search_Lucene_Field::text('viewsCount', $this->entity->viewsCount));
            $doc->addField(Zend_Search_Lucene_Field::text('commentsCount', $this->entity->commentsCount));
            $doc->addField(Zend_Search_Lucene_Field::text('title', $this->title));
            $doc->addField(Zend_Search_Lucene_Field::unStored('body', $this->body));
            $doc->addField(Zend_Search_Lucene_Field::text('tags', $this->getTagsString()));
            $index->addDocument($doc);
        }
        if ($commit) {
            $index->commit();
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
            $doc->addField(Zend_Search_Lucene_Field::text('entityType', 'event'));
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