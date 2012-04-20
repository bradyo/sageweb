<?php

/**
 * @param string $body
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */
class Sageweb_Cms_PostArticle extends Sageweb_Cms_Abstract_PostContent
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();
        $this->setTableName('post_article');
    }

    public function postSave($event)
    {
        parent::postSave($event);

        // find all uploads referenced in html body. Set upload "temporary" to false.
        preg_match_all('/"\/upload\/download\/(\d+)-.+?"/', $this->body, $matches);
        foreach ($matches[1] as $id) {
            $upload = Sageweb_Cms_Table_Upload::findOneById($id);
            $upload->isTemporary = false;
            $upload->save();
        }

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
            $doc->addField(Zend_Search_Lucene_Field::text('rating',
                $this->entity->rating));
            $doc->addField(Zend_Search_Lucene_Field::text('upVotesCount',
                $this->entity->upVotesCount));
            $doc->addField(Zend_Search_Lucene_Field::text('downVotesCount',
                $this->entity->downVotesCount));
            $doc->addField(Zend_Search_Lucene_Field::text('viewsCount',
                $this->entity->viewsCount));
            $doc->addField(Zend_Search_Lucene_Field::text('commentsCount',
                $this->entity->commentsCount));
            $doc->addField(Zend_Search_Lucene_Field::text('title', $this->title));
            $doc->addField(Zend_Search_Lucene_Field::text('isFeatured', $this->isFeatured));
            $doc->addField(Zend_Search_Lucene_Field::text('summary', $this->summary));
            $doc->addField(Zend_Search_Lucene_Field::unStored('body', $this->body));
            $doc->addField(Zend_Search_Lucene_Field::text('category', $this->getCategoryString()));
            $doc->addField(Zend_Search_Lucene_Field::text('tag', $this->getTagsString()));
            $index->addDocument($doc);
        }
        if ($commit) {
            $index->commit();
            $index->optimize();
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
                strip_tags($this->body)
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
