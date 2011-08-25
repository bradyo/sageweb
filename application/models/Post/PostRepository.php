<?php

class Application_Model_Post_PostRepository {
    
    /**
     * @var Zend_Search_Lucene_Interface
     */
    private $searchIndex;
    
    public function __construct(Zend_Search_Lucene $searchIndex) {
        $this->searchIndex = $searchIndex;
    }

    public function save(Sageweb_Post $post) {
        $post->save();

        // remove existing document from index if it exists
        $term = new Zend_Search_Lucene_Index_Term($post->entityId, 'entityId');
        $docIds  = $this->searchIndex->termDocs($term);
        foreach ($docIds as $docId) {
            $this->searchIndex->delete($docId);
        }
        // add document
        $doc = new Zend_Search_Lucene_Document();
        $doc->addField(Zend_Search_Lucene_Field::text('entityId', $post->entityId));
        $doc->addField(Zend_Search_Lucene_Field::unStored('title', $post->title));
        $doc->addField(Zend_Search_Lucene_Field::unStored('summary', $post->summary));
        $doc->addField(Zend_Search_Lucene_Field::unStored('body', $post->body));
        $this->searchIndex->addDocument($doc);
        $this->searchIndex->commit();
    }
              
    public function getAll(Sageweb_PostSearch $search, $offset = null, $limit = null) {
        $query = $this->getQuery($search);
        if ($offset !== null) {
            $query->offset($offset);
        }
        if ($limit !== null) {
            $query->limit($limit);
        }
        return $query->execute();
    }
    
    public function getOne(Sageweb_PostSearch $search) {
        $query = $this->getQuery($search);
        $query->limit(1);
        return $query->fetchOne();
    }
        
    public function getCount(Sageweb_PostSearch $search) {
        $query = $this->getQuery($search);
        return $query->count();
    }
    
    /**
     * @return Doctrine_Query
     */
    public function getQuery(Sageweb_PostSearch $search = null) {
        $query = Doctrine_Query::create()->from('
            Sageweb_Post post, 
            post.stats stats, post.creator creator, post.reviewer reviewer, 
            post.author author, post.categories category, post.tags tags 
            ');
        if ($search != null) {
            if (!empty($search->search)) {
                $entityIds = array();
                $hits = $this->searchIndex->find($search->search);
                foreach ($hits as $hit) {
                    $doc = $hit->getDocument();
                    $entityIds[] = $doc->getFieldValue('entityId');
                }
                $query->andWhereIn('post.entityId', $entityIds);
            }
            if ($search->publicId != null) {
                $query->andWhere('post.publicId = ?', $search->publicId);
            }
            if ($search->status) {
                $query->andWhere('post.status = ?', $search->status);
            }
            if ($search->isLatest) {
                $query->andWhere('post.isLatest = ?', true);
            }
            if (count($search->postTypes) > 0) {
                $query->whereIn('post.type', $search->postTypes);
            }
            if (count($search->categories) > 0) {
                $query->andWhereIn('post.categories.category.value', $search->categories);
            }
            if (count($search->tags) > 0) {
                $normalizedTags = $this->getNormalizedTags($search->tags);
                $query->andWhereIn('post.tags.normalValue', $normalizedTags);
            }
            $query->orderBy($search->orderBy);
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
}
