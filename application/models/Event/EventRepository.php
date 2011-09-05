<?php

class Application_Model_Event_EventRepository 
{
    /**
     * @var Zend_Search_Lucene_Interface
     */
    private $searchIndex;
    
    public function __construct(Zend_Search_Lucene_Interface $searchIndex) {
        $this->searchIndex = $searchIndex;
    }

    public function save(Application_Model_Event_Event $event) {
        $event->save();
        $this->updateSearchIndex($event);
    }
    
    private function updateSearchIndex(Application_Model_Post_Post $post) {
        // remove existing document from index if it exists
        $term = new Zend_Search_Lucene_Index_Term($post->entityId, 'entityId');
        $docIds  = $this->searchIndex->termDocs($term);
        foreach ($docIds as $docId) {
            $this->searchIndex->delete($docId);
        }
        // add document
        $doc = new Zend_Search_Lucene_Document();
        $doc->addField(Zend_Search_Lucene_Field::text('entityId', $post->entityId));
        $doc->addField(Zend_Search_Lucene_field::unStored('type', $post->type));
        $doc->addField(Zend_Search_Lucene_Field::unStored('title', $post->title));
        $doc->addField(Zend_Search_Lucene_Field::unStored('summary', $post->summary));
        $doc->addField(Zend_Search_Lucene_Field::unStored('body', $post->body));
        $doc->addField(Zend_Search_Lucene_Field::unStored('location', $post->event->location));
        $this->searchIndex->addDocument($doc);
        $this->searchIndex->commit();
    }
           
    public function getAll(Application_Model_Event_EventFilter $filter, $offset = null, $limit = null) {
        $query = $this->getQuery($filter);
        if ($offset !== null) {
            $query->offset($offset);
        }
        if ($limit !== null) {
            $query->limit($limit);
        }
        return $query->execute();
    }
    
    public function getOne(Application_Model_Event_EventFilter $filter) {
        $query = $this->getQuery($filter);
        $query->limit(1);
        return $query->fetchOne();
    }
    
    /**
     * @param integer $id
     * @return Application_Model_Post_Post
     */
    public function getLatestById($id) {
        $filter = new Application_Model_Event_EventFilter();
        $filter->publicId = $id;
        $filter->isLatest = true;
        
        $query = $this->getQuery($filter);
        $query->limit(1);
        return $query->fetchOne();
    }
        
    public function getCount(Application_Model_Event_EventFilter $filter) {
        $query = $this->getQuery($filter);
        return $query->count();
    }
    
    /**
     * @return Doctrine_Query
     */
    public function getQuery(Application_Model_Event_EventFilter $filter = null) {
        $query = Doctrine_Query::create()->from('
            Application_Model_Post_Post post, post.event event, 
            post.stats stats, post.creator creator, post.reviewer reviewer, 
            post.author author, post.categories category, post.tags tags 
            ');
        if ($filter != null) {
            if ($filter->search) {
                $entityIds = array();
                $hits = $this->searchIndex->find($filter->search);
                foreach ($hits as $hit) {
                    $doc = $hit->getDocument();
                    $entityIds[] = $doc->getFieldValue('entityId');
                }
                // work around for doctrine for wherein bug with empty array: 
                // http://www.doctrine-project.org/jira/browse/DC-727
                if (count($entityIds) == 0) {
                    $query->andWhere('1 = 2');
                } else {
                    $query->andWhereIn('post.entityId', $entityIds);
                }
            }
            if ($filter->publicId != null) {
                $query->andWhere('post.publicId = ?', $filter->publicId);
            }
            if ($filter->status) {
                $query->andWhere('post.status = ?', $filter->status);
            }
            if ($filter->isLatest) {
                $query->andWhere('post.isLatest = ?', true);
            }
            if (count($filter->categories) > 0) {
                $query->andWhereIn('post.categories.category.value', $filter->categories);
            }
            if (count($filter->tags) > 0) {
                $normalizedTags = $this->getNormalizedTags($filter->tags);
                $query->andWhereIn('post.tags.normalValue', $normalizedTags);
            }
            
            // event options
            if (count($filter->eventTypes) > 0) {
                $query->andWhereIn('event.eventType', $filter->eventTypes);
            }
            if ($filter->startDateAfter != null) {
                $query->andWhere('event.startDate > ?', $filter->startDateAfter);
            }
            if ($filter->startDateBefore != null) {
                $query->andWhere('event.startDate < ?', $filter->startDateBefore);
            }
            if ($filter->orderBy) {
                $query->orderBy($filter->orderBy);
            }
        }
        return $query;
    }
    
    private function getNormalizedTags() {
        $normalizedValues = array();
        foreach ($this->tags as $value) {
            $normalizedValues[] = Application_Converter_Slugify::getSlug($value);
        }
        return $normalizedValues;
    }
    
    public function rebuildIndex() {
        $query = Doctrine_Query::create()->from('
            Application_Model_Post_Post post, post.event event, 
            post.stats stats, post.creator creator, post.reviewer reviewer, 
            post.author author, post.categories category, post.tags tags 
            ');
        $posts = $query->execute();
        foreach ($posts as $post) {
            try {
                $this->updateSearchIndex($post);
            } catch (Exception $e) {
                echo "failed to index event : ";
                var_dump($post->toArray());
            }
        }
    }
}
