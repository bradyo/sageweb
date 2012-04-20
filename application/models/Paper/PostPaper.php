<?php

/**
 * @
 * @property string $publishDate
 * @property string $url
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */
class Application_Model_Paper_Paper extends Sageweb_Abstract_Post
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();
        $this->setTableName('post_paper');

        $this->hasColumn('pubmed_id as pubmedId', 'integer');
        $this->hasColumn('title', 'clob');
        $this->hasColumn('authors', 'clob');
        $this->hasColumn('source', 'clob');
        $this->hasColumn('publish_date as publishDate', 'date');
        $this->hasColumn('url as url', 'string', 255);
        $this->hasColumn('type', 'string', 32);
        $this->hasColumn('abstract', 'clob');
        $this->hasColumn('summary', 'clob');
    }

    public function postSave($event)
    {
        parent::postSave($event);
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
        $index = Zend_Registry::get('paperIndex');
        $term = new Zend_Search_Lucene_Index_Term($this->entityId, 'entityId');
        $docIds  = $index->termDocs($term);
        foreach ($docIds as $docId) {
            $index->delete($docId);
        }
        if ($this->status == self::STATUS_PUBLIC) {
            $doc = new Zend_Search_Lucene_Document();
            $doc->addField(Zend_Search_Lucene_Field::text('id', $this->id));
            $doc->addField(Zend_Search_Lucene_Field::text('entityId', $this->entityId));
            $doc->addField(Zend_Search_Lucene_Field::unIndexed('authorId', $this->authorId));
            $doc->addField(Zend_Search_Lucene_Field::UnIndexed('createdAt', $this->createdAt));
            $doc->addField(Zend_Search_Lucene_Field::text('rating', $this->entity->rating));
            $doc->addField(Zend_Search_Lucene_Field::text('upVotesCount', $this->entity->upVotesCount));
            $doc->addField(Zend_Search_Lucene_Field::text('downVotesCount', $this->entity->downVotesCount));
            $doc->addField(Zend_Search_Lucene_Field::text('viewsCount', $this->entity->viewsCount));
            $doc->addField(Zend_Search_Lucene_Field::text('commentsCount', $this->entity->commentsCount));
            $doc->addField(Zend_Search_Lucene_Field::text('title', $this->title));
            $doc->addField(Zend_Search_Lucene_Field::text('publishDate', $this->publishDate));
           $doc->addField(Zend_Search_Lucene_Field::text('type', $this->type));
            $doc->addField(Zend_Search_Lucene_Field::text('authors', $this->authors));
            $doc->addField(Zend_Search_Lucene_Field::text('source', $this->source));
            $doc->addField(Zend_Search_Lucene_Field::text('abstract', $this->abstract));
            $doc->addField(Zend_Search_Lucene_Field::text('summary', $this->abstract));
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
            $doc->addField(Zend_Search_Lucene_Field::text('entityType', 'paper'));
            $doc->addField(Zend_Search_Lucene_Field::text('title', $this->title));

            $body = join(' ', array(
                strip_tags($this->authors),
                strip_tags($this->source),
                strip_tags($this->abstract),
                strip_tags($this->summary),
            ));
            $doc->addField(Zend_Search_Lucene_Field::unStored('body', $body));
            $index->addDocument($doc);
        }
        if ($commit) {
            $index->commit();
        }
    }
}
