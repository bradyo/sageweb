<?php

/**
 * @property string $type
 * @property string $name
 * @property string $location
 * @property string $url
 * @property string $body
 */
class Application_Model_Lab_Lab extends Sageweb_AbstractPost
{
    private static $types = array(
        'research' => 'Research',
        'company' => 'Company',
        'other' => 'Other',
    );

    public function setTableDefinition()
    {
        parent::setTableDefinition();
        $this->setTableName('post_lab');

        $this->hasColumn('type', 'string');
        $this->hasColumn('name', 'string');
        $this->hasColumn('location', 'string');
        $this->hasColumn('url', 'string');
        $this->hasColumn('body', 'clob');
    }

    public static function getTypeChoices()
    {
        return self::$types;
    }

    public static function getTypeLabel($type)
    {
        return self::$types[$type];
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
        $index = Application_Registry::getLabIndex();

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
            $doc->addField(Zend_Search_Lucene_Field::text('type', $this->type));
            $doc->addField(Zend_Search_Lucene_Field::text('name', $this->name));
            $doc->addField(Zend_Search_Lucene_Field::text('letter', substr($this->name, 0, 1)));
            $doc->addField(Zend_Search_Lucene_Field::text('location', $this->location));
            $doc->addField(Zend_Search_Lucene_Field::text('body', $this->body));
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
            $doc->addField(Zend_Search_Lucene_Field::text('entityType', 'lab'));
            $doc->addField(Zend_Search_Lucene_Field::text('title', $this->name));

            $body = join(' ', array(
                strip_tags($this->location),
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