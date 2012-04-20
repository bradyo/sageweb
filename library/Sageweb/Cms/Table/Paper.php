<?php

class Sageweb_Cms_Table_Paper
{
    const TYPE_CURRENT = "current";
    const TYPE_CLASSICAL = "classical";

    const SORT_NEWEST = 'newest';
    const SORT_POPULAR = 'popular';
    const SORT_TOP = 'top';

    private static function _getRootQuery()
    {
        return Doctrine_Query::create()
            ->from('Sageweb_Cms_PostPaper p, p.entity e, p.author a, p.tags t');
    }

    /**
     * @return Sageweb_Cms_PostPaper
     */
    public static function findOneByEntityId($entityId)
    {
        $query = self::_getRootQuery();
        $query->where('p.entityId = ?', $entityId);
        $query->limit(1);
        return $query->fetchOne();
    }

    /**
     * @return Sageweb_Cms_PostPaper
     */
    public static function findOneById($id)
    {
        $query = self::_getRootQuery();
        $query->where('p.id = ?', $id);
        $query->limit(1);
        return $query->fetchOne();
    }

    public static function findNewest($count = null)
    {
        $query = self::_getRootQuery();
        $query->where('p.status = ?', Sageweb_Cms_Abstract_Post::STATUS_PUBLIC);
        $query->orderBy('p.createdAt DESC');
        if ($count) {
            $query->limit($count);
        }
        return $query->execute();
    }
    
    public static function findSince($sinceDate, $count = null)
    {
        $query = Doctrine_Query::create()->from('Sageweb_Cms_PostPaper p')
            ->where('p.createdAt > ?', $sinceDate)
            ->andWhere('p.status = ?', Sageweb_Cms_Abstract_Content::STATUS_PUBLIC)
            ->orderBy('p.createdAt DESC');
        if ($count) {
            $query->limit($count);
        }
        return $query->execute();
    }

    public static function findPublic()
    {
        return self::_getRootQuery()
            ->where('p.status = ?', Sageweb_Cms_PostPaper::STATUS_PUBLIC)
            ->execute();
    }


    public static function getSearchPager($queryString = null, $sort = null, $type = null)
    {
        $hits = self::search($queryString, $sort, $type);
        $adapter = new Zend_Paginator_Adapter_Array($hits);
        return new Zend_Paginator($adapter);
    }
    
    public static function search($queryString, $sort = null, $type = null)
    {
        $index = Sageweb_Registry::getPaperIndex();

        switch ($type) {
            case 'current':
                $queryString .= ' type:current';
                break;
            case 'classical':
                $queryString .= ' type:classical';
                break;
            default:
                // select all types
                break;
        }

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
            case 'publishDate':
                $hits =  $index->find($query, 'publishDate', SORT_STRING, SORT_DESC);
                break;
            default:
                $hits =  $index->find($query, 'createdAt', SORT_STRING, SORT_DESC);
                break;
        }
        return $hits;
    }

    public static function rebuildIndex()
    {
        $index = Zend_Search_Lucene::create(DATA_PATH . '/lucene/paper-index');
        $posts = Sageweb_Cms_Table_Paper::findPublic();
        foreach ($posts as $post) {
            $post->updateIndex();
            $post->updateSiteIndex();
        }
        $index->commit();
        $index->optimize();
    }
}
