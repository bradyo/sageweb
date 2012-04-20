<?php

class Sageweb_Cms_Table_Discussion
{
    private static function _getRootQuery()
    {
        return Doctrine_Query::create()
            ->from('Sageweb_Cms_PostDiscussion p, p.entity e, p.author a, p.tags t, p.forum f');
    }

    /**
     * @return Sageweb_Cms_PostDiscussion
     */
    public static function findOneById($id)
    {
        $query = Doctrine_Query::create()
            ->from('Sageweb_Cms_PostDiscussion p, p.entity e, p.author a, p.tags t, p.forum f');
        $query->where('p.id = ?', $id);
        $query->limit(1);
        return $query->fetchOne();
    }

    /**
     * @param int $entityId
     * @return Sageweb_Cms_PostDiscussion
     */
    public static function findOneByEntityId($entityId)
    {
        $query = Doctrine_Query::create()
            ->from('Sageweb_Cms_PostDiscussion p, p.entity e, p.author a, p.tags t, p.forum f');
        $query->where('p.entityId = ?', $entityId);
        $query->limit(1);
        return $query->fetchOne();
    }

    /**
     *
     * @param int $forumId
     * @return array
     */
    public static function getForumTags($forumId = null)
    {
        $db = Sageweb_Registry::getDb();
        if ($forumId) {
            $stmt = $db->prepare('
                SELECT t.value, COUNT(t.entity_id) as value_count
                FROM post_discussion p
                LEFT JOIN entity_tag t ON t.entity_id = p.entity_id
                WHERE p.status = ? AND p.forum_id = ?
                GROUP BY t.value
            ');
            $stmt->execute(array(Sageweb_Cms_PostDiscussion::STATUS_PUBLIC, $forumId));
        } else {
            $stmt = $db->prepare('
                SELECT t.value, COUNT(t.entity_id) as value_count
                FROM post_discussion p
                LEFT JOIN entity_tag t ON t.entity_id = p.entity_id
                WHERE p.status = ?
                GROUP BY t.value
            ');
            $stmt->execute(array(Sageweb_Cms_PostDiscussion::STATUS_PUBLIC));
        }

        $counts = array();
        $rows = $stmt->fetchAll();
        foreach ($rows as $row) {
            $value = strtolower($row['value']);
            if ($value) {
                $counts[$value] = $row['value_count'];
            }
        }
        return $counts;
    }

    public static function findNewest($forumId = null, $tag = null)
    {
        $query = Doctrine_Query::create()
            ->from('Sageweb_Cms_PostDiscussion p, p.entity e, p.author a, p.tags t, p.forum f');
        $query->where('p.status = ?', Sageweb_Cms_PostDiscussion::STATUS_PUBLIC);
        if ($forumSlug) {
            $query->andWhere('f.id = ?', $forumId);
        }
        if ($tag) {
            $query->andWhere('t.value = ?', $tag);
        }
        $query->orderBy('p.createdAt DESC');
        return $query->execute();
    }

    /**
     *
     * @param int $id discussion id
     * @return array
     */
    public static function getTags($id)
    {
        $allCounts = self::getForumTags();
        foreach ($allCounts as &$count){

        }
        
        $db = Sageweb_Registry::getDb();
        $stmt = $db->prepare('
            SELECT t.value, COUNT(t.entity_id) as value_count
            FROM post_discussion p
            LEFT JOIN entity_tag t ON t.entity_id = p.entity_id
            WHERE p.status = ? AND p.id = ?
            GROUP BY t.value
        ');
        $stmt->execute(array(Sageweb_Cms_PostDiscussion::STATUS_PUBLIC, $id));

        $counts = array();
        $rows = $stmt->fetchAll();
        foreach ($rows as $row) {
            $value = $row['value'];
            if ($value) {
                // find total count (case-insensitive)
                $count = 1;
                foreach ($allCounts as $allValue => $allCount) {
                    if (strtolower($allValue) == strtolower($value)) {
                        $count = $allCount;
                        break;
                    }
                }
                $counts[$value] = $allCount;
            }
        }
        return $counts;
    }

    
    public static function getSearchPager($queryString = null, $sort = null)
    {
        $hits = self::search($queryString, $sort);
        $adapter = new Zend_Paginator_Adapter_Array($hits);
        return new Zend_Paginator($adapter);
    }

    public static function search($queryString, $sort = null)
    {
        $index = Sageweb_Registry::getDiscussionIndex();

        try {
            if (empty($queryString)) {
                $query = Zend_Search_Lucene_Search_QueryParser::parse('entityId:[1 TO *]');
            } else {
                $query = Zend_Search_Lucene_Search_QueryParser::parse($queryString);
            }
        } catch (Zend_Search_Lucene_Exception $e) {
            return array();
        }

        switch ($sort) {
            case 'top-rated':
                $hits = $index->find($query, 'rating', SORT_NUMERIC, SORT_DESC);
                break;
            case 'most-viewed':
                $hits = $index->find($query, 'viewsCount', SORT_NUMERIC, SORT_DESC);
                break;
            case 'most-discussed':
                $hits = $index->find($query, 'commentsCount', SORT_NUMERIC, SORT_DESC);
                break;
            case 'neweset':
            default:
                $hits =  $index->find($query, 'createdAt', SORT_STRING, SORT_DESC);
                break;
        }
        return $hits;
    }

    public static function rebuildIndex()
    {
        $index = Zend_Search_Lucene::create(DATA_PATH . '/lucene/discussion-index');
        $posts = self::_getRootQuery()
            ->where('p.status = ?', Sageweb_Cms_PostPaper::STATUS_PUBLIC)
            ->execute();
        foreach ($posts as $post) {
            $post->updateIndex();
            $post->updateSiteIndex();
        }
        $index->commit();
        $index->optimize();
    }
}
