<?php

/**
 * @property string url
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */
class Sageweb_Cms_PostLink extends Sageweb_Cms_Abstract_PostContent
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();
        $this->setTableName('post_link');

        $this->hasColumn('url', 'string', 255);
    }

    /**
     * Gets an array of data with fields and relations needed in revision history.
     * @return array post data
     */
    public function getRevisionData()
    {
        $data = $this->getData();
        $data['url'] = $this->url;
        $data['categories'] = $this->getCategoryValues();
        $data['tags'] = $this->getTagValues();
        return $data;
    }

    public function postSave($event)
    {
        parent::postSave($event);

        // update indexes
        $this->updateIndex();
        $this->updateSiteIndex();
    }

    /**
     * @param boolean $commit If the index should be committed to file. Set this
     *   true if updating one record so that $index->commit() is called
     */
    public function updateIndex($commit = false)
    {
        $index = Zend_Registry::get('contentIndex');

        $term = new Zend_Search_Lucene_Index_Term($this->entityId, 'entityId');
        $docIds  = $index->termDocs($term);
        foreach ($docIds as $docId) {
            $index->delete($docId);
        }

        if ($this->status == self::STATUS_PUBLIC) {
            $doc = new Zend_Search_Lucene_Document();
            $doc->addField(Zend_Search_Lucene_Field::text('slugId', $this->getSlugId()));
            $doc->addField(Zend_Search_Lucene_Field::text('entityType', $this->entity->type));
            $doc->addField(Zend_Search_Lucene_Field::text('entityId', $this->entityId));
            $doc->addField(Zend_Search_Lucene_Field::text('authorId', $this->authorId));
            $doc->addField(Zend_Search_Lucene_Field::text('createdAt', $this->createdAt));
            $doc->addField(Zend_Search_Lucene_Field::text('rating', $this->entity->rating));
            $doc->addField(Zend_Search_Lucene_Field::text('upVotesCount', $this->entity->upVotesCount));
            $doc->addField(Zend_Search_Lucene_Field::text('downVotesCount', $this->entity->downVotesCount));
            $doc->addField(Zend_Search_Lucene_Field::text('viewsCount', $this->entity->viewsCount));
            $doc->addField(Zend_Search_Lucene_Field::text('commentsCount', $this->entity->commentsCount));
            $doc->addField(Zend_Search_Lucene_Field::text('title', $this->title));
            $doc->addField(Zend_Search_Lucene_Field::text('isFeatured', $this->isFeatured));
            $doc->addField(Zend_Search_Lucene_Field::text('summary', $this->summary));
            $doc->addField(Zend_Search_Lucene_Field::text('category', $this->getCategoryString()));
            $doc->addField(Zend_Search_Lucene_Field::text('tag', $this->getTagsString()));
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
            $doc->addField(Zend_Search_Lucene_Field::text('entityType', $this->entity->type));
            $doc->addField(Zend_Search_Lucene_Field::text('entityId', $this->entityId));
            $doc->addField(Zend_Search_Lucene_Field::text('id', $this->getSlugId()));
            $doc->addField(Zend_Search_Lucene_Field::text('title', $this->title));

            $body = join(' ', array(
                strip_tags($this->summary),
            ));
            $doc->addField(Zend_Search_Lucene_Field::unStored('body', $body));
            $index->addDocument($doc);
        }
        if ($commit) {
            $index->commit();
            $index->optimize();
        }
    }
}
