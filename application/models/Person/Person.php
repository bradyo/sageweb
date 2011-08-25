<?php

/**
 * @property string $firstName
 * @property string $lastName
 * @property string $email
 * @property string $labName
 * @property string $location
 * @property string $personalUrl
 * @property string $labUrl
 * @property string $body
 */
class Application_Model_Person_Person extends Doctrine_Record
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();
        $this->setTableName('post_person');

        $this->hasColumn('first_name as firstName', 'string');
        $this->hasColumn('last_name as lastName', 'string');
        $this->hasColumn('email', 'string');
        $this->hasColumn('location', 'string');
        $this->hasColumn('personal_url as personalUrl', 'string');
        $this->hasColumn('lab_name as labName', 'string');
        $this->hasColumn('lab_url as labUrl', 'string');
        $this->hasColumn('body', 'clob');
    }
    
    public function setUp() {
        $this->hasOne('Sageweb_Post as post', array(
            'local' => 'post_id',
            'foreign' => 'id',
        ));
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
        $index = Zend_Registry::get('personIndex');

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

            $title = $this->firstName . ' ' . $this->lastName;
            $doc->addField(Zend_Search_Lucene_Field::text('title', $title));
            $doc->addField(Zend_Search_Lucene_Field::text('lastName', $this->lastName));
            $doc->addField(Zend_Search_Lucene_Field::text('letter', substr($this->lastName, 0, 1)));
            $doc->addField(Zend_Search_Lucene_Field::text('email', $this->email));
            $doc->addField(Zend_Search_Lucene_Field::text('labName', $this->labName));
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
            $doc->addField(Zend_Search_Lucene_Field::text('entityType', 'person'));

            $title = $this->firstName . ' ' . $this->lastName;
            $doc->addField(Zend_Search_Lucene_Field::text('title', $title));

            $body = join(' ', array(
                strip_tags($this->email),
                strip_tags($this->labName),
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